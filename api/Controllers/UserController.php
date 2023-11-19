<?php
namespace Warehouse\Controllers;
use Warehouse\Models\Token;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Warehouse\Models\User;

class UserController
{
    public function index(Request $request, Response $response, array $args){
        $results = User::getUsers($request);
        $code = array_key_exists('status', $results) ? 500 : 200;
        return $response->withJson($results, $code, JSON_PRETTY_PRINT);
    }

    public function view(Request $request, Response $response, array $args){
        $id = $args['id'];
        $results = User::getUserById($id);
        $code = array_key_exists('status', $results) ? 500 : 200;
        return $response->withJson($results, $code, JSON_PRETTY_PRINT);
    }

    public function create(Request $request, Response $response, array $args){
        $user = User::createUser($request);
        $results = [
            'status' => 'user created',
            'data' => $user
        ];
        return $response->withJson($results, 201, JSON_PRETTY_PRINT);
    }

    public function update(Request $request, Response $response, array $args){
        $user = User::updateUser($request);
        $results = [
            'status' => 'user updated',
            'data' => $user
        ];
        return $response->withJson($results, 200, JSON_PRETTY_PRINT);
    }

    public function delete(Request $request, Response $response, array $args)
    {
        $id = $args['id'];
        User::deleteUser($id);
        $results = [
            'status' => 'User deleted',
        ];
        $code = array_key_exists('status', $results) ? 200 : 500;
        return $response->withJson($results, 200, JSON_PRETTY_PRINT);
    }

    public function authJWT(Request $request, Response $response)
    {
        $params = $request->getParsedBody();
        $username = $params['username'];
        $password = $params['password'];
        $user = User::authenticateUser($username, $password);
        if ($user) {
            $status_code = 200;
            $jwt = User::generateJWT($user->id);
            $results = [
                'status' => 'login successful',
                'jwt' => $jwt,
                'name' => $user->username
            ];
        } else {
            $status_code = 401;
            $results = [
                'status' => 'login failed',
            ];
        }
        //return $results;
        return $response->withJson($results, $status_code,
            JSON_PRETTY_PRINT);
    }

    // Validate a user with username and password. It returns a Bearer token on success
    public function authBearer(Request $request, Response $response)
    {
        $params = $request->getParsedBody();
        $username = $params['username'];
        $password = $params['password'];
        $user = User::authenticateUser($username, $password);
        if ($user) {
            $status_code = 200;
            $token = Token::generateBearer($user->id);
            $results = [
                'status' => 'login successful',
                'token' => $token
            ];
        } else {
            $status_code = 401;
            $results = [
                'status' => 'login failed'
            ];
        }
        return $response->withJson($results, $status_code,
            JSON_PRETTY_PRINT);
    }

}
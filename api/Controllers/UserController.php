<?php

namespace Warehouse\Controllers;

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
}
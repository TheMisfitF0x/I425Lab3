<?php

namespace Warehouse\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Warehouse\Models\Employee;

class EmployeeController
{
    public function index(Request $request, Response $response, array $args){
        $results = Employee::getEmployees($request);
        $code = array_key_exists("status", $results) ? 500 : 200;

        return $response->withJson($results, $code, JSON_PRETTY_PRINT);
    }

    public function view(Request $request, Response $response, array $args)
    {
        $id = $args['id'];
        $results = Employee:: getEmployeeById($id);
        $code = array_key_exists("status", $results) ? 500 : 200;

        return $response->withJson($results, $code, JSON_PRETTY_PRINT);
    }

    public function create(Request $request, Response $response, array $args)
    {
        $employee = Employee::createEmployee($request);
        if ($employee->id) {
            $results = [
                'status' => 'Employee created',
                'employee_uri' => '/employees/' . $employee->id,
                'data' => $employee
            ];
            $code = 201;
        } else {
            $code = 500;
        }

        return $response->withJson($results, $code, JSON_PRETTY_PRINT);
    }

    public function update(Request $request, Response $response, array $args)
    {
        $employee = Employee::updateEmployee($request);
        if ($employee->id) {
            $results = [
                'status' => 'Employee updated',
                'message_uri' => '/employee/' . $employee->id,
                'data' => $employee
            ];
            $code = 200;
        } else {
            $code = 500;
        }

        return $response->withJson($results, $code, JSON_PRETTY_PRINT);
    }

    public function delete(Request $request, Response $response, array $args)
    {
        $id = $request->getAttribute('id');
        Employee::deleteEmployee($request);
        if (Employee::find($id)->exists) {
            return $response->withStatus(500);

        } else {
            $results = [
                'status' => "Employee '/employees/$id' has been deleted."
            ];
            return $response->withJson($results, 200, JSON_PRETTY_PRINT);
        }
    }
}
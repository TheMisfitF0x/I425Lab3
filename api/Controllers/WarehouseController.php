<?php

namespace Warehouse\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Warehouse\Models\Warehouse;

class WarehouseController
{
    public function index(Request $request, Response $response, array $args){
        $results = Warehouse::getWarehouses($request);
        $code = array_key_exists("status", $results) ? 500 : 200;

        return $response->withJson($results, $code, JSON_PRETTY_PRINT);
    }

    public function view(Request $request, Response $response, array $args)
    {
        $id = $args['id'];
        $results = Warehouse:: getWarehouseById($id);
        $code = array_key_exists("status", $results) ? 500 : 200;

        return $response->withJson($results, $code, JSON_PRETTY_PRINT);
    }

    public function viewOrders(Request $request, Response $response, array $args){
        $id = $args['id'];
        $results = Warehouse::getOrdersByWarehouse($id);
        $code = array_key_exists('status', $results) ? 500 : 200;
        return $response->withJson($results, $code, JSON_PRETTY_PRINT);
    }

    public function viewProducts(Request $request, Response $response, array $args){
        $id = $args['id'];
        $results = Warehouse::getProductsByWarehouse($id);
        $code = array_key_exists('status', $results) ? 500 : 200;
        return $response->withJson($results, $code, JSON_PRETTY_PRINT);
    }

    public function create(Request $request, Response $response, array $args)
    {
        $warehouse = Warehouse::createWarehouse($request);
        if ($warehouse->id) {
            $results = [
                'status' => 'Employee created',
                'employee_uri' => '/employees/' . $warehouse->id,
                'data' => $warehouse
            ];
            $code = 201;
        } else {
            $code = 500;
        }

        return $response->withJson($results, $code, JSON_PRETTY_PRINT);
    }

    public function update(Request $request, Response $response, array $args)
    {
        // Insert a new student
        $warehosue = Warehouse::updateWarehouse($request);
        if ($warehosue->id) {
            $results = [
                'status' => 'Employee updated',
                'message_uri' => '/employee/' . $warehosue->id,
                'data' => $warehosue
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
        Warehouse::deleteWarehouse($request);
        if (Warehouse::find($id)->exists) {
            return $response->withStatus(500);

        } else {
            $results = [
                'status' => "Warehouse '/warehouses/$id' has been deleted."
            ];
            return $response->withJson($results, 200, JSON_PRETTY_PRINT);
        }
    }
}
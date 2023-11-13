<?php

namespace Warehouse\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Warehouse\Models\Order;

class OrderController
{
    public function index(Request $request, Response $response, array $args){
        $results = Order::getOrders($request);
        $code = array_key_exists('status', $results) ? 500 : 200;
        return $response->withJson($results, $code, JSON_PRETTY_PRINT);
    }

    public function view(Request $request, Response $response, array $args){
        $id = $args['id'];
        $results = Order::getOrderById($id);
        $code = array_key_exists('status', $results) ? 500 : 200;
        return $response->withJson($results, $code, JSON_PRETTY_PRINT);
    }

    public function create(Request $request, Response $response, array $args){
        $order = Order::createOrder($request);
        if ($order->id) {
            $results = [
                'status' => 'Order created',
                'order_uri' => '/orders/' . $order->id,
                'data' => $order
            ];
            $code = 201;
        } else {
            $code = 500;
        }

        return $response->withJson($results, $code, JSON_PRETTY_PRINT);
    }

    public function update(Request $request, Response $response, array $args){
        $order = Order::updateOrder($request);
        if ($order->id) {
            $results = [
                'status' => 'Order updated',
                'order_uri' => '/orders/' . $order->id,
                'data' => $order
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
        Order::deleteOrder($request);
        if (Order::find($id)->exists) {
            return $response->withStatus(500);

        } else {
            $results = [
                'status' => "Order '/orders/$id' has been deleted."
            ];
            return $response->withJson($results, 200, JSON_PRETTY_PRINT);
        }
    }
}
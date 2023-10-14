<?php
/**
 * Author: Isaac Lowe
 * Date: 10/11/2023
 * File: Index.php
 * Description:
 */


require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/boostrap.php';

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Management\Models\Warehouse;
use Management\Models\Order;
use Management\Models\Product;
use Management\Models\Employee;
use Management\Models\User;

$app->get('/', function ($request,$response, $args){
    return $response->write("Hello, this is Warehouse Management API.");
});
//START OF WAREHOUSES~~~~~~~~~~~~~!

//GET all warehouses
$app -> get('/warehouses', function(Request $request, Response $response, array $args){

    $warehouses = Warehouse::all();
    $payload = [];

    foreach ($warehouses as $whouse){
        $payload[$whouse->id] = [
            'Location' => $whouse -> Location,
            'Lease_Num' => $whouse -> Lease_Num,
            'Sqft' => $whouse -> Sqft,
            'Monthly_Cost' => $whouse -> Monthly_Cost
        ];
    }
    return $response->withStatus(200) -> withJson($payload);
});

//GET single warehouse
$app -> get('/warehouses/{id}', function($request, $response,$args) {
    $id = $args['id'];
    $warehouse = new Warehouse();
    $whouse = $warehouse->find($id);

    $payload[$warehouse->id] = [
        'Location' => $whouse -> Location,
        'Lease_Num' => $whouse -> Lease_Num,
        'Sqft' => $whouse -> Sqft,
        'Monthly_Cost' => $whouse -> Monthly_Cost

    ];
    return $response->withStatus(200)->withJson($payload);
});

//POST Warehouse
$app->post('/warehouses', function ($request, $response, $args) {
    $warehouse = new Warehouse();
    $_Location = $request->getParsedBodyParam('warehouse','');
    $_Lease_Num = $request->getParsedBodyParam('Lease_Num');
    $_Sqft = $request->getParsedBodyParam('Sqft');
    $_Monthly_Cost = $request->getParsedBodyParam('Monthly_Cost');

    $warehouse->Location = $_Location;
    $warehouse->Lease_Num = $_Lease_Num;
    $warehouse->Sqft = $_Sqft;
    $warehouse->Monthly_Cost = $_Monthly_Cost;
    $warehouse->save();

    if ($warehouse->id) {
        $payload = ['warehouse_id' => $warehouse->id,
        'warehouse_uri' => '/warehouses/' . $warehouse->id
        ];
        return $response->withStatus(201)->withJson($payload);
    } else {
        return $response->withStatus(500);
    }
});

//PATCH Warehouse
$app->patch('/warehouses/{id}', function ($request, $response, $args) {
    $id = $args['id'];
    $warehouse = Warehouse::findOrFail($id);
    $params = $request->getParsedBody();
    foreach ($params as $field => $value) {
        $warehouse->$field = $value;
    }
    $warehouse->save();
    if ($warehouse->id) {
        $payload = ['Warehouse_id' => $warehouse->id,
            'Location' => $warehouse -> Location,
            'Lease_Num' => $warehouse -> Lease_Num,
            'Sqft' => $warehouse -> Sqft,
            'Monthly_Cost' => $warehouse -> Monthly_Cost,
            'warehouse_uri' => '/warehouse/' . $warehouse->id
        ];
        return $response->withStatus(200)->withJson($payload);
    } else {
        return $response->withStatus(500);
    }
});

//DELETE Warehouse
$app->delete('/warehouses/{id}', function ($request, $response, $args) {
    $id = $args['id'];
    $warehouse = Warehouse::find($id);
    $warehouse->delete();
    if ($warehouse->exists) {
        return $response->withStatus(500);
    } else {
        return $response->withStatus(204)->getBody()->write("Warehouse
'/warehouses/$id' has been deleted.");
    }
});

//END OF WAREHOUSES~~~~~~~~~~~~~~~!

//START OF ORDERS~~~~~~~~~~~~~~!

//GET orders from single warehouse
$app->get('/warehouses/{id}/orders', function(Request $request, Response $response, array $args){
    $id = $args['id'];
    $warehouse = new Warehouse();
    $orders = $warehouse->find($id)->orders;

    foreach($orders as $order){
        $payload[$order->id] = ['Warehouse_Id'=>$order->Warehouse_Id,
            'Cost'=>$order->Cost,
            'User_id'=>$order->User_id,
            'Product_Id'=>$order->Product_Id,
            'Date_Created'=>$order->Date_Created
        ];
    }

    return $response->withStatus(200)->withJson($payload);

});

//GET all orders
$app->get('/orders', function(Request $request, Response $response, array $args){
    $orders = Order::all();

    $payload = [];

    foreach ($orders as $order){
        $payload[$order->id] = ['Warehouse_Id'=>$order->Warehouse_Id,
            'Cost'=>$order->Cost,
            'User_id'=>$order->User_id,
            'Product_Id'=>$order->Product_Id,
            'Date_Created'=>$order->Date_Created
        ];
    }
    return $response->withStatus(200)->withJson($payload);
});

//GET single order
$app->get('/order/{id}', function(Request $request, Response $response, array $args){
    $id = $args['id'];
    $order = new Order();
    $_order = $order->find($id);

    $payload[$_order->id] = ['Warehouse_Id'=>$_order->Warehouse_Id,
        'Cost'=>$_order->Cost,
        'User_id'=>$_order->User_id,
        'Product_Id'=>$_order->Product_Id,
        'Date_Created'=>$_order->Date_Created
    ];

    return $response->withStatus(200)->withJson($payload);
});

//POST Order
$app->post('/orders', function ($request, $response, $args) {
    $order = new Order();
    $_warehouse_id = $request->getParsedBodyParam('Warehouse_Id', '');
    $_cost = $request->getParsedBodyParam('Cost');
    $_user_id = $request->getParsedBodyParam('User_id');
    $_product_id = $request->getParsedBodyParam('Product_Id');
    $_date_created = $request->getParsedBodyParam('Date_Created');
    $order->Warehouse_Id = $_warehouse_id;
    $order->Cost = $_cost;
    $order->User_id = $_user_id;
    $order->Product_Id = $_product_id;
    $order->Date_Created = $_date_created;
    $order->save();
    if ($order->id) {
        $payload = ['order_id' => $order->id,
            'order_uri' => '/order/' . $order->id];
        return $response->withStatus(201)->withJson($payload);
    } else {
        return $response->withStatus(500);
    }
});

//PATCH Order
$app->patch('/order/{id}', function ($request, $response, $args) {
    $id = $args['id'];
    $order = Order::findOrFail($id);
    $params = $request->getParsedBody();
    foreach ($params as $field => $value) {
        $order->$field = $value;
    }
    $order->save();
    if ($order->id) {
        $payload = ['Order_Id' => $order->id,
            'Warehouse_Id'=>$order->Warehouse_Id,
            'Cost'=>$order->Cost,
            'User_id'=>$order->User_id,
            'Product_Id'=>$order->Product_Id,
            'Date_Created'=>$order->Date_Created
        ];
        return $response->withStatus(200)->withJson($payload);
    } else {
        return $response->withStatus(500);
    }
});

$app->run();



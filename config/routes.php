<?php

//use Warehouse\Authentication\MyAuthenticator;
use Warehouse\Authentication\BasicAuthenticator;
//use Warehouse\Authentication\BearerAuthenticator;
use Warehouse\Authentication\JWTAuthenticator;

$app->get('/', function ($request, $response, $args) {
    return $response->write('Hello, this is Warehouse Management API.');
});

$app->get('/hello/{name}', function ($request, $response, $args) {
    return $response->write("Hello " . $args['name']);
});

// User routes
$app->group('/users', function () {
    $this->get('', 'UserController:index');
    $this->get('/{id}', 'UserController:view');
    $this->post('', 'UserController:create');
    $this->put('/{id}', 'UserController:update');
    $this->patch('/{id}', 'UserController:update');
    $this->delete('/{id}', 'UserController:delete');
    $this->post('/authBearer', 'UserController:authBearer');
    $this->post('/authJWT', 'UserController:authJWT');
});

// Route groups
$app->group('', function () {
    $this->group('/warehouses', function () {
        // The Warehouse group
        $this->get('', 'WarehouseController:index'); // "Class" is registered in DIC
        $this->get('/{id}', 'WarehouseController:view');
        $this->get('/{id}/orders', 'WarehouseController:viewOrders');
        $this->get('/{id}/products', 'WarehouseController:viewProducts');
        $this->post('', 'WarehouseController:create');
        $this->put('/{id}', 'WarehouseController:update');//Postman PUT Boyd with x-www-form-urlencoded to send new information.
        $this->patch('/{id}', 'WarehouseController:update');//Postman PATCH Boyd with x-www-form-urlencoded to send new information.
        $this->delete('/{id}', 'WarehouseController:delete');
    });
    // The Order group
    $this->group('/orders', function () {
        $this->get('', 'OrderController:index');
        $this->get('/{id}', 'OrderController:view');
        $this->post('', 'OrderController:create');
        $this->put('/{id}', 'OrderController:update');//Postman PUT Boyd with x-www-form-urlencoded to send new information.
        $this->patch('/{id}', 'OrderController:update');//Postman PATCH Boyd with x-www-form-urlencoded to send new information.
        $this->delete('/{id}', 'OrderController:delete');
    });
    // The Employee group
    $this->group('/employees', function () {
        $this->get('', 'EmployeeController:index');
        $this->get('/{id}', 'EmployeeController:view');
        $this->post('', 'EmployeeController:create');
        $this->put('/{id}', 'EmployeeController:update');//Postman PUT Boyd with x-www-form-urlencoded to send new information.
        $this->patch('/{id}', 'EmployeeController:update');//Postman PATCH Boyd with x-www-form-urlencoded to send new information.
        $this->delete('/{id}', 'EmployeeController:delete');
    });
    // The Product group
    $this->group('/products', function () {
        $this->get('', 'ProductController:index');
        $this->get('/{id}', 'ProductController:view');
        $this->post('', 'ProductController:create');
        $this->put('/{id}', 'ProductController:update');//Postman PUT Boyd with x-www-form-urlencoded to send new information.
        $this->patch('/{id}', 'ProductController:update');//Postman PATCH Boyd with x-www-form-urlencoded to send new information.
        $this->delete('/{id}', 'ProductController:delete');
    });
//->add(new MyAuthenticator());
})->add(new BasicAuthenticator());
//})->add(new BearerAuthenticator());
//})->add(new JWTAuthenticator());
//$app->add(new ChatterLogging());
$app->run();
<?php

//use Chatter\Middleware\Logging as ChatterLogging;
//use Chatter\Authentication\MyAuthenticator;
//use Chatter\Authentication\BasicAuthenticator;
//use Chatter\Authentication\BearerAuthenticator;
//use Chatter\Authentication\JWTAuthenticator;

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
        $this->post('', 'WarehouseController:create');
        $this->put('/{id}', 'WarehouseController:update');//Postman PUT Boyd with x-www-form-urlencoded to send new information.
        $this->patch('/{id}', 'WarehouseController:update');//Postman PATCH Boyd with x-www-form-urlencoded to send new information.
        $this->delete('/{id}', 'WarehouseController:delete');
    });
    // The Comment group
    $this->group('/orders', function () {
        $this->get('', 'OrderController:index');
        $this->get('/{id}', 'OrderController:view');
        $this->post('', 'OrderController:create');
        $this->put('/{id}', 'OrderController:update');//Postman PUT Boyd with x-www-form-urlencoded to send new information.
        $this->patch('/{id}', 'OrderController:update');//Postman PATCH Boyd with x-www-form-urlencoded to send new information.
        $this->delete('/{id}', 'OrderController:delete');
    });
    //})->add(new MyAuthenticator());
    //})->add(new BasicAuthenticator());
//})->add(new BearerAuthenticator());
//$app->add(new MyAuthenticator());
//$app->add(new BasicAuthenticator());
//})->add(new JWTAuthenticator());
});
//$app->add(new ChatterLogging());
$app->run();
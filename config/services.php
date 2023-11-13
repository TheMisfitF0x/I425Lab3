<?php


// Alias to the controllers
use Warehouse\Controllers\EmployeeController as EmployeeController;
use Warehouse\Controllers\OrderController as OrderController;
use Warehouse\Controllers\ProductController as ProductController;
use Warehouse\Controllers\UserController as UserController;
use Warehouse\Controllers\WarehouseController as WarehouseController;

/*
 * The following is the controller and middleware factory. It
 * registers controllers and middleware with the DI container so
 * they can be accessed in other classes. Injecting instances into
 * the DI container so you don't need to pass the entire container or app,
 * rather only the services needed.
 * https://akrabat.com/accessing-services-in-slim-3/#comment-35429
 */
// Register controllers with the DIC. $c is the container itself.
$container['EmployeeController'] = function ($c) {
    return new EmployeeController();
};

$container['OrderController'] = function ($c) {
    return new OrderController();
};

$container['ProductController'] = function ($c) {
    return new ProductController();
};

$container['UserController'] = function ($c) {
    return new UserController();
};

$container['WarehouseController'] = function ($c) {
    return new WarehouseController();
};

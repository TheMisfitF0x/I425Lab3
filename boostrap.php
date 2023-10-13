<?php
/**
 * Author: Isaac Lowe
 * Date: 10/11/2023
 * File: boostrap.php
 * Description:
 */

include 'config/credentials.php';
include 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

$config['displayErrorDetails'] = true;
$config['addContentLengthHandler'] = false;

$app = new \Slim\App(["settings" => $config]);

$capsule = new Capsule();
$capsule->addConnection([
   "driver"=>"mysql",
    "host" => $db_host,
    "port" => 3306,
    "database" => $db_name,
    "username" => $db_user,
    "password" => $db_pass,
    "chazrset" => "utf8",
    "collation" => "utf8_general_ci",
    "prefix" => "" //optional

]);

$capsule -> setAsGlobal();
$capsule -> bootEloquent();

$container = $app->getContainer();
$container['db'] = function ($container)use($capsule){
    return $capsule;
};
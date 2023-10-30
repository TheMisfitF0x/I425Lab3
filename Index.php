<?php
/**
 * Authors: Isaac Lowe, Logan Douglass, Logan Orender, Samuel Sibhatu
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
    $_Location = $request->getParsedBodyParam('Location','');
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

    $payload = [];

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

    $count = Order::count();

    $params = $request->getQueryParams();

    //Do limit and offset exist?
    $limit = array_key_exists('limit', $params) ? (int)$params['limit'] : 10; //items per page
    $offset = array_key_exists('offset', $params) ? (int)$params['offset'] : 0; // offset of the first item

    //get search terms
    $term = array_key_exists('q', $params) ? $params['q'] : null;

    if(!is_null($term)){
        $orders = Order::searchOrders($term);
        $payload_final = [];
        foreach ($orders as $_order) {
                $payload_final[$_order->id] = ['Order_Id'=>$_order->id,
                    'Warehouse_Id'=>$_order->Warehouse_Id,
                    'Cost'=>$_order->Cost,
                    'User_id'=>$_order->User_id,
                    'Product_Id'=>$_order->Product_Id,
                    'Date_Created'=>$_order->Date_Created
            ];
        }
    }else {
        //Pagination
        $links = Order::getLinks($request, $limit, $offset);

        //Sorting
        $sort_key_array = Order::getSortKeys($request);

        $query = Order::skip($offset)->take($limit); // limit the rows

        //Sort output by one or more keys and directions
        foreach ($sort_key_array as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        $orders = $query->get();

        $payload = [];

        foreach ($orders as $_order) {
            $payload[$_order->id] = ['Order_Id'=>$_order->id,
                'Warehouse_Id'=>$_order->Warehouse_Id,
                'Cost'=>$_order->Cost,
                'User_id'=>$_order->User_id,
                'Product_Id'=>$_order->Product_Id,
                'Date_Created'=>$_order->Date_Created
            ];
        }

        $payload_final = [
            'totalCount' => $count,
            'limit' => $limit,
            'offset' => $offset,
            'links' => $links,
            'sort' => $sort_key_array,
            'data' => $payload
        ];
    }
    return $response->withStatus(200)->withJson($payload_final);
    
});

//GET single order
$app->get('/orders/{id}', function(Request $request, Response $response, array $args){
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
            'order_uri' => '/orders/' . $order->id];
        return $response->withStatus(201)->withJson($payload);
    } else {
        return $response->withStatus(500);
    }
});

//PATCH Order
$app->patch('/orders/{id}', function ($request, $response, $args) {
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
            'Date_Created'=>$order->Date_Created,
            'order_uri' => '/order/' . $order->id
        ];
        return $response->withStatus(200)->withJson($payload);
    } else {
        return $response->withStatus(500);
    }
});

//DELETE Order
$app->delete('/orders/{id}', function ($request, $response, $args) {
    $id = $args['id'];
    $order = Order::find($id);
    $order->delete();
    if ($order->exists) {
        return $response->withStatus(500);
    } else {
        return $response->withStatus(204)->getBody()->write("Order
'/orders/$id' has been deleted.");
    }
});

//END OF ORDERS~~~~~~~~~~~~~~~!

//START OF USERS~~~~~~~~~~~~~~!

//GET all users
$app->get('/users', function(Request $request, Response $response, array $args){
    $count = User::count();

    $params = $request->getQueryParams();

    //Do limit and offset exist?
    $limit = array_key_exists('limit', $params) ? (int)$params['limit'] : 10; //items per page
    $offset = array_key_exists('offset', $params) ? (int)$params['offset'] : 0; // offset of the first item

    //get search terms
    $term = array_key_exists('q', $params) ? $params['q'] : null;

    if(!is_null($term)){
        $users = User::searchUsers($term);
        $payload_final = [];
        foreach ($users as $_user) {
            $payload_final[$_user->id] = ['Username'=>$_user->Username,
                'User_Id'=>$_user->id,
                'Dob'=>$_user->Dob,
                'Date_Created'=>$_user->Date_Created
            ];
        }
    }else {
        //Pagination
        $links = User::getLinks($request, $limit, $offset);

        //Sorting
        $sort_key_array = User::getSortKeys($request);

        $query = User::skip($offset)->take($limit); // limit the rows

        //Sort output by one or more keys and directions
        foreach ($sort_key_array as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        $users = $query->get();

        $payload = [];

        foreach ($users as $_user) {
            $payload[$_user->id] = ['Username' => $_user->Username,
                'User_Id' => $_user->id,
                'Dob' => $_user->Dob,
                'Date_Created' => $_user->Date_Created
            ];
        }

        $payload_final = [
            'totalCount' => $count,
            'limit' => $limit,
            'offset' => $offset,
            'links' => $links,
            'sort' => $sort_key_array,
            'data' => $payload
        ];
    }
    return $response->withStatus(200)->withJson($payload_final);
});

//GET single user
$app->get('/users/{id}', function(Request $request, Response $response, array $args){
    $id = $args['id'];
    $user = new User();
    $_user = $user->find($id);

    $payload[$_user->id] = ['Username'=>$_user->Username,
        'Dob'=>$_user->Dob,
        'Date_Created'=>$_user->Date_Created
    ];

    return $response->withStatus(200)->withJson($payload);
});

//POST user
$app->post('/users', function ($request, $response, $args) {
    $user = new User();
    $_username = $request->getParsedBodyParam('Username');
    $_pass = $request->getParsedBodyParam('Pass');
    $_dob = $request->getParsedBodyParam('Dob');
    $_date_created = $request->getParsedBodyParam('Date_Created');
    $user->Username = $_username;
    $user->Pass = $_pass;
    $user->Dob = $_dob;
    $user->Date_Created = $_date_created;
    $user->save();
    if ($user->id) {
        $payload = ['User_Id' => $user->id,
            'user_uri' => '/users/' . $user->id];
        return $response->withStatus(201)->withJson($payload);
    } else {
        return $response->withStatus(500);
    }
});

//PATCH User
$app->patch('/users/{id}', function ($request, $response, $args) {
    $id = $args['id'];
    $user = User::findOrFail($id);
    $params = $request->getParsedBody();
    foreach ($params as $field => $value) {
        $user->$field = $value;
    }
    $user->save();
    if ($user->id) {
        $payload = ['Username'=>$user->Username,
            'Dob'=>$user->Dob,
            'Date_Created'=>$user->Date_Created,
            'user_uri' => '/users/' . $user->id

        ];
        return $response->withStatus(200)->withJson($payload);
    } else {
        return $response->withStatus(500);
    }
});

//DELETE User
$app->delete('/users/{id}', function ($request, $response, $args) {
    $id = $args['id'];
    $user = User::find($id);
    $user->delete();
    if ($user->exists) {
        return $response->withStatus(500);
    } else {
        return $response->withStatus(204)->getBody()->write("User
'/users/$id' has been deleted.");
    }
});

//END OF USERS



//START OF Employees~~~~~~~~~~~~~~!
// Employee Endpoints
$app->group('/employees', function ($app) {
    // GET all employees
    $app->get('', function (Request $request, Response $response) {
        $count = Employee::count();

        $params = $request->getQueryParams();

        //Do limit and offset exist?
        $limit = array_key_exists('limit', $params) ? (int)$params['limit'] : 10; //items per page
        $offset = array_key_exists('offset', $params) ? (int)$params['offset'] : 0; // offset of the first item

        //get search terms
        $term = array_key_exists('q', $params) ? $params['q'] : null;

        if(!is_null($term)){
            $employees = Employee::searchEmployees($term);
            $payload_final = [];
            foreach ($employees as $employee) {
                $payload_final[$employee->id] = [
                    'Name' => $employee->Name,
                    'Dob' => $employee->Dob,
                    'Date_Hired' => $employee->Date_Hired
                ];
            }
        }else {
            //Pagination
            $links = Employee::getLinks($request, $limit, $offset);

            //Sorting
            $sort_key_array = Employee::getSortKeys($request);

            $query = Employee::skip($offset)->take($limit); // limit the rows

            //Sort output by one or more keys and directions
            foreach ($sort_key_array as $column => $direction) {
                $query->orderBy($column, $direction);
            }

            $employees = $query->get();

            $payload = [];
            foreach ($employees as $employee) {
                $payload[$employee->id] = [
                    'Name' => $employee->Name,
                    'Dob' => $employee->Dob,
                    'Date_Hired' => $employee->Date_Hired
                ];
            }

            $payload_final = [
                'totalCount' => $count,
                'limit' => $limit,
                'offset' => $offset,
                'links' => $links,
                'sort' => $sort_key_array,
                'data' => $payload
            ];
        }
        return $response->withStatus(200)->withJson($payload_final);
    });

    // GET single employee
    $app->get('/{id}', function ($request, $response, $args) {
        $id = $args['id'];
        $employee = Employee::find($id);

        if (!$employee) {
            return $response->withStatus(404)->withJson(['error' => 'Employee not found']);
        }

        $payload[$employee->id] = [
            'Name' => $employee->Name,
            'Dob' => $employee->Dob,
            'Date_Hired' => $employee->Date_Hired
        ];

        return $response->withStatus(200)->withJson($payload);
    });

    // POST Employee
    $app->post('', function ($request, $response, $args) {
        $employee = new Employee();
        $employee->Name = $request->getParsedBodyParam('Name');
        $employee->Dob = $request->getParsedBodyParam('Dob');
        $employee->Date_Hired = $request->getParsedBodyParam('Date_Hired');
        $employee->save();

        if ($employee->id) {
            $payload = ['employee_id' => $employee->id, 'employee_uri' => '/employees/' . $employee->id];
            return $response->withStatus(201)->withJson($payload);
        } else {
            return $response->withStatus(500);
        }
    });

    // PATCH Employee
    $app->patch('/{id}', function ($request, $response, $args) {
        $id = $args['id'];
        $employee = Employee::findOrFail($id);
        $params = $request->getParsedBody();
        foreach ($params as $field => $value) {
            $employee->$field = $value;
        }
        $employee->save();

        if ($employee->id) {
            $payload = [
                'employee_id' => $employee->id,
                'Name' => $employee->Name,
                'Dob' => $employee->Dob,
                'Date_Hired' => $employee->Date_Hired,
                'employee_uri' => '/employees/' . $employee->id
            ];
            return $response->withStatus(200)->withJson($payload);
        } else {
            return $response->withStatus(500);
        }
    });

    // DELETE Employee
    $app->delete('/{id}', function ($request, $response, $args) {
        $id = $args['id'];
        $employee = Employee::find($id);

        if (!$employee) {
            return $response->withStatus(404)->withJson(['error' => 'Employee not found']);
        }

        $employee->delete();

        if ($employee->exists) {
            return $response->withStatus(500)->withJson(['error' => 'Failed to delete employee']);
        } else {
            return $response->withStatus(204)->getBody()->write("Employee '/employees/$id' has been deleted.");
        }
    });
});


//END OF Employee~~~~~~~~~~~~~~!

//START OF Product~~~~~~~~~~~~~~!
// Product Endpoints
//GET products from single warehouse
$app->get('/warehouses/{id}/products', function(Request $request, Response $response, array $args){
    $id = $args['id'];
    $warehouse = new Warehouse();
    $products = $warehouse->find($id)->products;

    $payload = [];

    foreach ($products as $product) {
        $payload[$product->id] = [
            'Warehouse_Id' => $product->Warehouse_Id,
            'Product_Name' => $product->Product_Name,
            'Product_Desc' => $product->Product_Desc,
            'Product_Weight' => $product->Product_Weight,
            'Product_Count' => $product->Product_Count
        ];
    }

    return $response->withStatus(200)->withJson($payload);

});

$app->group('/products', function ($app) {
    // GET all products
    $app->get('', function (Request $request, Response $response) {
        $count = Product::count();

        $params = $request->getQueryParams();

        //Do limit and offset exist?
        $limit = array_key_exists('limit', $params) ? (int)$params['limit'] : 10; //items per page
        $offset = array_key_exists('offset', $params) ? (int)$params['offset'] : 0; // offset of the first item

        //get search terms
        $term = array_key_exists('q', $params) ? $params['q'] : null;

        if(!is_null($term)){
            $products = Product::searchProducts($term);
            $payload_final = [];
            foreach ($products as $_product) {
                $payload_final[$_product->id] = ['Product_Id' => $_product->id,
                'Warehouse_Id' => $_product->Warehouse_Id,
                'Product_Name' => $_product->Product_Name,
                'Product_Desc' => $_product->Product_Desc,
                'Product_Weight' => $_product->Product_Weight,
                'Product_Count' => $_product->Product_Count
            ];
            }
        }else {
            //Pagination
            $links = Product::getLinks($request, $limit, $offset);

            //Sorting
            $sort_key_array = Product::getSortKeys($request);

            $query = Product::skip($offset)->take($limit); // limit the rows

            //Sort output by one or more keys and directions
            foreach ($sort_key_array as $column => $direction) {
                $query->orderBy($column, $direction);
            }

            $products = $query->get();

            $payload = [];

            foreach ($products as $_product) {
                $payload[$_product->id] = [
                    'Product_Id' => $_product->id,
                    'Warehouse_Id' => $_product->Warehouse_Id,
                    'Product_Name' => $_product->Product_Name,
                    'Product_Desc' => $_product->Product_Desc,
                    'Product_Weight' => $_product->Product_Weight,
                    'Product_Count' => $_product->Product_Count
                ];
            }

            $payload_final = [
                'totalCount' => $count,
                'limit' => $limit,
                'offset' => $offset,
                'links' => $links,
                'sort' => $sort_key_array,
                'data' => $payload
            ];
        }
        return $response->withStatus(200)->withJson($payload_final);
    });

    // GET single product
    $app->get('/{id}', function ($request, $response, $args) {
        $id = $args['id'];
        $product = Product::find($id);

        if (!$product) {
            return $response->withStatus(404)->withJson(['error' => 'Product not found']);
        }

        $payload[$product->id] = [
            'Warehouse_Id' => $product->Warehouse_Id,
            'Product_Name' => $product->Product_Name,
            'Product_Desc' => $product->Product_Desc,
            'Product_Weight' => $product->Product_Weight,
            'Product_Count' => $product->Product_Count
        ];

        return $response->withStatus(200)->withJson($payload);
    });

    // POST Product
    $app->post('', function ($request, $response, $args) {
        $product = new Product();
        $product->Warehouse_Id = $request->getParsedBodyParam('Warehouse_Id');
        $product->Product_Name = $request->getParsedBodyParam('Product_Name');
        $product->Product_Desc = $request->getParsedBodyParam('Product_Desc');
        $product->Product_Weight = $request->getParsedBodyParam('Product_Weight');
        $product->Product_Count = $request->getParsedBodyParam('Product_Count');
        $product->save();

        if ($product->id) {
            $payload = ['product_id' => $product->id, 'product_uri' => '/products/' . $product->id];
            return $response->withStatus(201)->withJson($payload);
        } else {
            return $response->withStatus(500);
        }
    });

    // PATCH Product
    $app->patch('/{id}', function ($request, $response, $args) {
        $id = $args['id'];
        $product = Product::findOrFail($id);
        $params = $request->getParsedBody();
        foreach ($params as $field => $value) {
            $product->$field = $value;
        }
        $product->save();

        if ($product->id) {
            $payload = [
                'product_id' => $product->id,
                'Warehouse_Id' => $product->Warehouse_Id,
                'Product_Name' => $product->Product_Name,
                'Product_Desc' => $product->Product_Desc,
                'Product_Weight' => $product->Product_Weight,
                'Product_Count' => $product->Product_Count,
                'product_uri' => '/products/' . $product->id
            ];
            return $response->withStatus(200)->withJson($payload);
        } else {
            return $response->withStatus(500);
        }
    });

    // DELETE Product
    $app->delete('/{id}', function ($request, $response, $args) {
        $id = $args['id'];
        $product = Product::find($id);

        if (!$product) {
            return $response->withStatus(404)->withJson(['error' => 'Product not found']);
        }

        $product->delete();

        if ($product->exists) {
            return $response->withStatus(500)->withJson(['error' => 'Failed to delete product']);
        } else {
            return $response->withStatus(204)->getBody()->write("Product '/products/$id' has been deleted.");
        }
    });
});
//END OF Product~~~~~~~~~~~~~~!

$app->run();
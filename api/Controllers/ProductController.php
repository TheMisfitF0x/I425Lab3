<?php

namespace Warehouse\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Warehouse\Models\Product;

class ProductController
{
    public function index(Request $request, Response $response, array $args){
        $results = Product::getProducts($request);
        $code = array_key_exists('status', $results) ? 500 : 200;
        return $response->withJson($results, $code, JSON_PRETTY_PRINT);
    }

    public function view(Request $request, Response $response, array $args){
        $id = $args['id'];
        $results = Product::getProductById($id);
        $code = array_key_exists('status', $results) ? 500 : 200;
        return $response->withJson($results, $code, JSON_PRETTY_PRINT);
    }

    public function create(Request $request, Response $response, array $args){
        $product = Product::createProduct($request);
        if ($product->id) {
            $results = [
                'status' => 'Product created',
                'product_uri' => '/products/' . $product->id,
                'data' => $product
            ];
            $code = 201;
        } else {
            $code = 500;
        }

        return $response->withJson($results, $code, JSON_PRETTY_PRINT);
    }

    public function update(Request $request, Response $response, array $args){
        $product = Product::updateProduct($request);
        if ($product->id) {
            $results = [
                'status' => 'Product updated',
                'product_uri' => '/products/' . $product->id,
                'data' => $product
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
        Product::deleteProduct($request);
        if (Product::find($id)->exists) {
            return $response->withStatus(500);

        } else {
            $results = [
                'status' => "Product '/products/$id' has been deleted."
            ];
            return $response->withJson($results, 200, JSON_PRETTY_PRINT);
        }
    }
}
<?php

namespace Warehouse\Models;

use \Illuminate\Database\Eloquent\Model;
class Product extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class,"warehouse_id");
    }

    public static function getProducts($request){
        $count = self::count();

        $params = $request->getQueryParams();

        //Do limit and offset exist?
        $limit = array_key_exists('limit', $params) ? (int)$params['limit'] : 10; //items per page
        $offset = array_key_exists('offset', $params) ? (int)$params['offset'] : 0; // offset of the first item

        //get search terms
        $term = array_key_exists('q', $params) ? $params['q'] : null;

        if(!is_null($term)){
            $products = self::searchProducts($term);
            $payload_final = [];
            foreach ($products as $_product) {
                $payload_final[$_product->id] = [
                    'product_id' => $_product->id,
                    'warehouse_id' => $_product->warehouse_id,
                    'product_name' => $_product->product_name,
                    'product_desc' => $_product->product_desc,
                    'product_weight' => $_product->product_weight,
                    'product_count' => $_product->product_count
                ];
            }
        }else {
            //Pagination
            $links = self::getLinks($request, $limit, $offset);

            //Sorting
            $sort_key_array = self::getSortKeys($request);

            $query = self::skip($offset)->take($limit); // limit the rows

            //Sort output by one or more keys and directions
            foreach ($sort_key_array as $column => $direction) {
                $query->orderBy($column, $direction);
            }

            $products = $query->get();

            $payload = [];
            foreach ($products as $_product) {
                $payload[$_product->id] = [
                    'product_id' => $_product->id,
                    'warehouse_id' => $_product->warehouse_id,
                    'product_name' => $_product->product_name,
                    'product_desc' => $_product->product_desc,
                    'product_weight' => $_product->product_weight,
                    'product_count' => $_product->product_count
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
            return $payload_final;
        }
    }

    public static function getProductById($id)
    {
        $product = self::findOrFail($id);
        return $product;
    }

    public static function createProduct($request)
    {
        // Retrieve parameters from request body
        $params = $request->getParsedBody();

        // Create a new User instance
        $product = new Product();

        // Set the user's attributes
        foreach ($params as $field => $value) {
            $product->$field = $value;
        }

        // Insert the user into the database
        $product->save();
        return $product;
    }

    public static function updateProduct($request)
    {
        // Retrieve parameters from request body
        $params = $request->getParsedBody();

        //Retrieve the user's id from url and then the user from the database
        $id = $request->getAttribute('id');
        $product = self::findOrFail($id);

        foreach ($params as $field => $value) {
            $product->$field = $value;
        }

        // Update the professor
        $product->save();
        return $product;
    }

    public static function deleteProduct($request)
    {
        $id = $request->getAttribute('id');
        $product = self::findOrFail($id);
        return ($product->delete());
    }

    public static function searchProducts($terms)
    {
        if (is_numeric($terms)) {
            $query = self::where('id', "like", "%$terms%");
        } else {
            $query = self::where('product_name', 'like', "%$terms%")
                ->orWhere('product_desc', 'like', "%$terms%");
        }
        $results = $query->get();
        return $results;
    }

    // This function returns an array of links for pagination.
    public static function getLinks($request, $limit, $offset)
    {
        $count = self::count();
        $uri = $request->getUri();
        $base_url = $uri->getBaseUrl();
        $path = $uri->getPath();
        $links = array();
        $links[] = ['rel' => 'self', 'href' => $base_url . "/$path" . "?limit=$limit&offset=$offset"];
        $links[] = ['rel' => 'first', 'href' => $base_url . "/$path" . "?limit=$limit&offset=0"];
        if ($offset - $limit >= 0) {
            $links[] = ['rel' => 'prev', 'href' => $base_url . "/$path" . "?limit=$limit&offset=" . ($offset - $limit)];
        }
        if ($offset + $limit < $count) {
            $links[] = ['rel' => 'next', 'href' => $base_url . "/$path" . "?limit=$limit&offset=" . ($offset + $limit)];
        }
        $links[] = ['rel' => 'last', 'href' => $base_url . "/$path" . "?limit=$limit&offset=" . $limit * (ceil($count / $limit) - 1)];
        return $links;
    }

    //getSortKeys return an array for sorting features
    public static function getSortKeys($request){
        $sort_key_array = [];

        // Get querystring variables from url
        $params = $request->getQueryParams();
        if (array_key_exists('sort', $params)) {
            $sort = preg_replace('/^\[|\]$|\s+/', '', $params['sort']); //remove white spaces, [, and ]
            $sort_keys = explode(',', $sort); //get all the key:direction pairs
            foreach ($sort_keys as $sort_key) {
                $direction = 'asc';
                $column = $sort_key;
                if (strpos($sort_key, ':')) {
                    list($column, $direction) = explode(':', $sort_key);
                }
                $sort_key_array[$column] = $direction;
            }
        }
        return $sort_key_array;
    }
}
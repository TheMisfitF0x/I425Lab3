<?php
/**
 * Author: Isaac Lowe
 * Date: 10/12/2023
 * File: Product.php
 * Description:
 */

namespace Management\Models;
class Product extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, "Warehouse_Id");
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

    // Get sort keys
    public static function getSortKeys($request)
    {
        $sort_key_array = [];

        $params = $request->getQueryParams();
        if (array_key_exists('sort', $params)) {
            $sort = preg_replace('/^\[|\]$|\s+/', '', $params['sort']);
            $sort_keys = explode(',', $sort);
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

    // Search products
    public static function searchProducts($terms)
    {
        $query = self::query();
        foreach ($terms as $term) {
            $query->orWhere('Product_Name', 'like', "%$term%")
                ->orWhere('Product_Desc', 'like', "%$term%");
        }
        $results = $query->get();
        return $results;
    }
}
<?php
/**
 * Author: Isaac Lowe
 * Date: 10/12/2023
 * File: Employee.php
 * Description:
 */

namespace Management\Models;

class Employee extends \Illuminate\Database\Eloquent\Model
{
    protected $table =  'employee';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public static function searchEmployees($terms)
    {
        if (is_numeric($terms)) {
            $query = self::where('id', "like", "%$terms%");
        } else {
            $query = self::where('Name', 'like', "%$terms%")
                ->orWhere('Date_Hired', 'like', "%$terms%");
        }
        $results = $query->get();
        return $results;
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

<?php

namespace Warehouse\Models;

use \Illuminate\Database\Eloquent\Model;
class Employee extends Model
{
    // The table associated with this model
    protected $table = 'employee';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public static function getEmployees($request){
        $count = self::count();

        $params = $request->getQueryParams();

        //Do limit and offset exist?
        $limit = array_key_exists('limit', $params) ? (int)$params['limit'] : 10; //items per page
        $offset = array_key_exists('offset', $params) ? (int)$params['offset'] : 0; // offset of the first item

        //get search terms
        $term = array_key_exists('q', $params) ? $params['q'] : null;

        if(!is_null($term)){
            $employees = self::searchEmployees($term);
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
            $links = self::getLinks($request, $limit, $offset);

            //Sorting
            $sort_key_array = self::getSortKeys($request);

            $query = self::skip($offset)->take($limit); // limit the rows

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
            return $payload_final;
        }
    }

    public static function getEmployeeById($id){
        $employee = self::findOrFail($id);
        return $employee;
    }

    public static function createEmployee($request){
        $params = $request->getParsedBody();

        //Create a new employee object
        $employee = new Employee();

        foreach ($params as $field => $value) {
            $employee->$field = $value;
        }

        $employee->save();
        return $employee;
    }

    public static function updateEmployee($request){
        $params = $request->getParsedBody();
        $id = $request->getAttribute('id');
        $employee = self::findOrFail($id);

        foreach ($params as $field => $value) {
            $employee->$field = $value;
        }
        $employee->save();
        return $employee;
    }

    public static function deleteEmployee($request){
        $id = $request->getAttribute('id');
        $employee = self::findOrFail($id);
        return($employee->delete());
    }

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
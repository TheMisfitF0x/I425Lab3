<?php

namespace Warehouse\Models;

use \Illuminate\Database\Eloquent\Model;
class User extends Model
{
    // The table associated with this model
    protected $table = 'users';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public static function getUsers($request){
        $count = self::count();

        $params = $request->getQueryParams();

        //Do limit and offset exist?
        $limit = array_key_exists('limit', $params) ? (int)$params['limit'] : 10; //items per page
        $offset = array_key_exists('offset', $params) ? (int)$params['offset'] : 0; // offset of the first item

        //get search terms
        $term = array_key_exists('q', $params) ? $params['q'] : null;

        if(!is_null($term)){
            $users = self::searchUsers($term);
            $payload_final = [];
            foreach ($users as $_user) {
                $payload_final[$_user->id] = [
                    'Username'=>$_user->Username,
                    'User_Id'=>$_user->id,
                    'Dob'=>$_user->Dob,
                    'Date_Created'=>$_user->Date_Created
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

            $users = $query->get();

            $payload = [];
            foreach ($users as $_user) {
                $payload[$_user->id] = [
                    'Username' => $_user->Username,
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
            return $payload_final;
        }
    }

    public static function getUserById($id)
    {
        $user = self::findOrFail($id);
        return $user;
    }

    public static function createUser($request)
    {
        // Retrieve parameters from request body
        $params = $request->getParsedBody();

        // Create a new User instance
        $user = new User();

        // Set the user's attributes
        foreach ($params as $field => $value) {

            // Need to hash password
            if ($field == 'Pass') {
                $value = password_hash($value, PASSWORD_DEFAULT);
            }

            $user->$field = $value;
        }

        // Insert the user into the database
        $user->save();
        return $user;
    }

    public static function updateUser($request)
    {
        // Retrieve parameters from request body
        $params = $request->getParsedBody();

        //Retrieve the user's id from url and then the user from the database
        $id = $request->getAttribute('id');
        $user = self::findOrFail($id);

        // Update attributes of the professor
        $user->Username = $params['Username'];
        $user->Pass = password_hash($params['Pass'], PASSWORD_DEFAULT);
        $user->Dob = $params['Dob'];

        // Update the professor
        $user->save();
        return $user;
    }

    public static function deleteUser($id)
    {
        $user = self::findOrFail($id);
        return ($user->delete());
    }

    public static function searchUsers($terms)
    {
        if (is_numeric($terms)) {
            $query = self::where('id', "like", "%$terms%");
        } else {
            $query = self::where('Username', 'like', "%$terms%")
                ->orWhere('Date_Created', 'like', "%$terms%");
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
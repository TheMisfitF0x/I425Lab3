<?php

namespace Warehouse\Models;

use \Illuminate\Database\Eloquent\Model;
class Warehouse extends Model
{
    // The table associated with this model
    protected $table = 'warehouse';
    protected $primaryKey = 'id';
    public $timestamps = false;

    //one to many relationship
    public function orders(){
        return $this->hasMany(Order::class, 'warehouse_id');
    }

    //one to many relationship
    public function products(){
        return $this->hasMany(Product::class, 'warehouse_id');
    }

    public static function getWarehouses()
    {
        $warehouses = self::all();
        return $warehouses;
    }

    public static function getWarehouseById($id)
    {
        $warehouse = self::findOrFail($id);
        return $warehouse;
    }

    public static function getOrdersByWarehouse($id)
    {
        $orders = self::findOrFail($id)->orders;
        return $orders;
    }

    public static function getProductsByWarehouse($id)
    {
        $products = self::findOrFail($id)->orders;
        return $products;
    }

    public static function createWarehouse($request)
    {
        // Retrieve parameters from request body
        $params = $request->getParsedBody();

        // Create a new User instance
        $warehouse = new Warehouse();

        // Set the user's attributes
        foreach ($params as $field => $value) {
            $warehouse->$field = $value;
        }

        // Insert the user into the database
        $warehouse->save();
        return $warehouse;
    }

    public static function updateWarehouse($request)
    {
        // Retrieve parameters from request body
        $params = $request->getParsedBody();

        //Retrieve the user's id from url and then the user from the database
        $id = $request->getAttribute('id');
        $warehouse = self::findOrFail($id);

        foreach ($params as $field => $value) {
            $warehouse->$field = $value;
        }

        // Update the professor
        $warehouse->save();
        return $warehouse;
    }

    public static function deleteWarehouse($id)
    {
        $warehouse = self::findOrFail($id);
        return ($warehouse->delete());
    }
}
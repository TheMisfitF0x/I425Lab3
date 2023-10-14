<?php
/**
 * Author: Isaac Lowe
 * Date: 10/11/2023
 * File: Warehouse.php
 * Description:
 */

namespace Management\Models;


class Warehouse extends \Illuminate\Database\Eloquent\Model
{
    protected $table =  'warehouse';
    protected $primaryKey = 'id';
    public $timestamps = false;

    //one to many relationship
    public function orders(){
        return $this->hasMany(Order::class, 'Warehouse_Id');
    }


}
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
    protected $table =  'products';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function Warehouse()
    {
        $this->belongsTo(Warehouse::class, "Warehouse_Id");
    }
}
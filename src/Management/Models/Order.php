<?php
/**
 * Author: Isaac Lowe
 * Date: 10/12/2023
 * File: Order.php
 * Description:
 */

namespace Management\Models;

class Order extends \Illuminate\Database\Eloquent\Model
{
    protected $table =  'orders';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
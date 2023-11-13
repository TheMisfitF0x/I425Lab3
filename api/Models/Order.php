<?php

namespace Warehouse\Models;

use \Illuminate\Database\Eloquent\Model;
class Order extends Model
{
// The table associated with this model
    protected $table = 'orders';
    protected $primaryKey = 'id';
}
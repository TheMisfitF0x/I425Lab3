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


}
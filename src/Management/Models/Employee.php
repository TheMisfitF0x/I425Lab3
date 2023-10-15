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
}
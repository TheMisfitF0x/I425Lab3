<?php
/**
 * Author: Isaac Lowe
 * Date: 10/12/2023
 * File: User.php
 * Description:
 */

namespace Management\Models;

class User extends \Illuminate\Database\Eloquent\Model
{
    protected $table =  'users';
    protected $primaryKey = 'id';
    public $timestamps = false;
}

<?php


namespace mywishlist\models;

use Illuminate\Database\Eloquent\Model;

class Compte extends Model{

    protected $table = 'compte';
    protected $primaryKey = 'username';
    public $timestamps = false;
    public $incrementing = false; //Primordial sinon Eloquent pense que c'est un int la primary key
}
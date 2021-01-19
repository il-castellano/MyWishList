<?php

namespace mywishlist\models;

use Illuminate\Database\Eloquent\Model;

class Liste extends Model {

    protected $table = 'liste';
    protected $primaryKey = 'token';
    public $timestamps = false;
    public $incrementing = false;

    public function getItems() {
        return $this->hasMany('mywishlist\models\Item', 'tokenListe');
    }
}
<?php


namespace mywishlist\models;


use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'message';
    protected $primaryKey = 'id_message';
    public $timestamps = false;

    public function getListe() {
        return $this->belongsTo('mywishlist\models\Liste', 'tokenListe');
    }

}
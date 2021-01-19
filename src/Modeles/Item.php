<?php



namespace wishlist\Modeles;

class Item extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'item';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function liste() {
        return $this->belongsTo('wishlist\modele\Liste' , 'liste_id');
    }

    public function cagnottes() {
        return $this->hasMany('wishlist\modele\Cagnotte' , 'item_id');
    }


}
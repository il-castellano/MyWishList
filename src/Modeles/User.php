<?php



namespace wishlist\modele;

class User extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function listes() {
        return $this->hasMany('wishlist\modele\Liste', 'user_id');
    }
}
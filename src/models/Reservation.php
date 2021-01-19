<?php


namespace mywishlist\models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Reservation extends Model
{
    protected $table = 'reservation';
    protected $primaryKey = ['idItem', 'tokenListe'];
    public $timestamps = false;
    public $incrementing = false;

    public function getItem() {
        return $this->belongsTo('mywishlist\models\Item', 'idItem');
    }

    public function getListe() {
        return $this->belongsTo('mywishlist\models\Liste', 'token');
    }

     // /!\ cette méthode permet d'utiliser la table qui a une clé primaire composée
     // ce n'est pas gérer de base par Eloquent /!\

    /**
     * Set the keys for a save update query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function setKeysForSaveQuery(Builder $query)
    {
        $keys = $this->getKeyName();
        if(!is_array($keys)){
            return parent::setKeysForSaveQuery($query);
        }

        foreach($keys as $keyName){
            $query->where($keyName, '=', $this->getKeyForSaveQuery($keyName));
        }

        return $query;
    }

    /**
     * Get the primary key value for a save query.
     *
     * @param mixed $keyName
     * @return mixed
     */
    protected function getKeyForSaveQuery($keyName = null)
    {
        if(is_null($keyName)){
            $keyName = $this->getKeyName();
        }

        if (isset($this->original[$keyName])) {
            return $this->original[$keyName];
        }

        return $this->getAttribute($keyName);
    }


}
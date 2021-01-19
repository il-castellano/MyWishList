<?php

namespace mywishlist\conf;

use Illuminate\Database\Capsule\Manager as DB;

class Database {

    public static function connect(){
        $bddConfig = parse_ini_file('conf.ini');

        $db = new DB();
        $db->addConnection( [
            'driver'    => 'mysql',
            'host'      => $bddConfig['host'],
            'database'  => $bddConfig['database'],
            'username'  => $bddConfig['username'],
            'password'  => $bddConfig['password'],
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => ''
        ] );
        $db->setAsGlobal();
        $db->bootEloquent();
    }
}
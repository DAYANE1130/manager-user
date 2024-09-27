<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        $db = db_connect();
        $sql = 'select * from users';
        $result = $db->query($sql);
        //var_dump($result->getResultObject());
        return view('welcome_message');
    }
}

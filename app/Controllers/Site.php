<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Site extends BaseController
{
    public function index()
    {
        $logged = session()->get('loggedUser');

        if (!$logged) {
            return   redirect()->to('login'); // faltava o return
        }

        return view('site', ['loggedUser' => $logged]);
    }
}

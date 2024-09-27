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
            redirect()->to('login');
        }

        return view('site', ['loggedUser' => $logged]);
    }
}

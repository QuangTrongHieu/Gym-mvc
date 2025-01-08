<?php

namespace App\Controllers;

class ContactController extends BaseController
{
    public function index()
    {
        $this->view('contact/contact', [
            'title' => 'Liên Hệ - PowerGym'
        ]);
    }
}

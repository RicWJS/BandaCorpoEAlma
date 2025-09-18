<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Display the contact page.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('contact');
    }
}

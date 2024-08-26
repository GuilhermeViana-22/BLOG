<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {
        return view('blog.index'); // Certifique-se de que 'blog.index' corresponde ao caminho da view
    }
}

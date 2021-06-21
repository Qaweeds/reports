<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{

    public function __invoke()
    {
        if(Auth::user()->id !== 3) return redirect()->route('report');
        return view('admin.index');
    }
}

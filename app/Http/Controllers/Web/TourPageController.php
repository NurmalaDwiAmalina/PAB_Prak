<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Illuminate\Http\Request;

class TourPageController extends Controller
{
    public function index()
    {
        return Inertia::render('Tours/Index');
    }
    public function attend($id)
    {
        return Inertia::render('Tours/Attend', ['id' => $id]);
    }
}

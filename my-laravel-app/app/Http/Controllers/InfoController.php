<?php

namespace App\Http\Controllers;

use App\Models\Info;
use Illuminate\Http\Request;

class InfoController extends Controller
{
    public function getInfo()
    {
        return response()->json(Info::all());
    }
}

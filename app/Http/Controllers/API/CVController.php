<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CVController extends Controller
{
    public function suggest(Request $request, $term)
    {
        return response()->json([]);
    }
}

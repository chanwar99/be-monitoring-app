<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Program;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Program::latest('updated_at')->get();

        return response()->json([
            'message' => 'Berhasil Tampil semua program',
            'data' => $categories
        ]);
    }

}

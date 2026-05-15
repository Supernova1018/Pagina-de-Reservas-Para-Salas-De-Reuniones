<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    // GET /api/users
    public function index()
    {
        return response()->json([
            'users' => User::query()
                ->select(['id', 'name', 'email', 'created_at'])
                ->orderBy('id')
                ->get(),
        ]);
    }
}
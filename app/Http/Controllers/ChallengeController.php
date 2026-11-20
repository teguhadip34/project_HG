<?php

namespace App\Http\Controllers;

use App\Services\GamificationService;
use Illuminate\Http\Request;

class ChallengeController extends Controller
{
    public function index(Request $request, GamificationService $gamification)
    {
        return view('tantangan', [
            'board' => $gamification->challenges($request->user()->id),
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\CategoryModel;
use App\Models\QuizModel;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller {
    function getDashboard( Request $request ) {
        $totalQuiz = QuizModel::count();
        $totalUsers = User::count();
        $totalCategory = CategoryModel::count();
        $recentUsers = User::query()->limit( 5 )->latest()->get();
        $data = [
            'totalQuiz' => $totalQuiz,
            'totalUsers' => $totalUsers,
            'totalCategory' => $totalCategory,
            'recentUsers' => $recentUsers
        ];
        return $this->actionSuccess( 'Dashboard data fetch successfully', $data );
    }
}

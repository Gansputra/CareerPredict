<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SalaryInsightsController extends Controller
{
    /**
     * Display a list of salary insights for various job roles.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // In a real‑world scenario this would be fetched from a database or an external API.
        // For the purpose of this demo we provide a static collection.
        $salaryData = [
            ['role' => 'Data Scientist', 'average' => 'IDR 15,000,000'],
            ['role' => 'Software Engineer', 'average' => 'IDR 12,000,000'],
            ['role' => 'Product Manager', 'average' => 'IDR 13,500,000'],
            ['role' => 'UX Designer', 'average' => 'IDR 10,500,000'],
            ['role' => 'DevOps Engineer', 'average' => 'IDR 14,000,000'],
        ];

        return view('salary.index', compact('salaryData'));
    }
}

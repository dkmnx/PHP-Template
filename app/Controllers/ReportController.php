<?php

namespace App\Controllers;

class ReportController
{
    public function index()
    {
        view('reports/index', [
            'title' => 'Reports'
        ]);
    }
}

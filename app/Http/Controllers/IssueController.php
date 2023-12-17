<?php

namespace App\Http\Controllers;

use App\Actions\Issue\CreateNewIssue;
use Illuminate\Http\Request;

class IssueController extends Controller
{
    public function store(Request $request)
    {
        return CreateNewIssue::create($request->all());
    }
}
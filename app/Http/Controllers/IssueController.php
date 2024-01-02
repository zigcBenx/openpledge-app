<?php

namespace App\Http\Controllers;

use App\Actions\Issue\CreateNewIssue;
use App\Actions\Issue\GetIssueById;
use App\Actions\Issue\GetIssues;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IssueController extends Controller
{
    public function store(Request $request)
    {
        return CreateNewIssue::create($request->all());
    }

    public function index()
    {
        $issues = GetIssues::get();
        return Inertia::render('Issues/Index', [
            'issues' => $issues
        ]);
    }

    public function show($id)
    {
        $issue = GetIssueById::get($id, true);
        return Inertia::render('Issues/Show', [
            'issue' => $issue
        ]);
    }
}
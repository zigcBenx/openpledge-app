<?php

namespace App\Http\Controllers;

use App\Actions\Repository\CreateNewRepository;
use App\Actions\Repository\GetRepositories;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RepositoryController extends Controller
{
    public function getRequestNew(Request $request)
    {                
        return Inertia::render('RequestRepository');
    }

    public function create(Request $request)
    {
        return CreateNewRepository::create($request->all());
    }

    public function index()
    {
        $repositories = GetRepositories::get();
        return Inertia::render('Repositories', [
            'repositories' => $repositories
        ]);
    }
}

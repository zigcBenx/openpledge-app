<?php

namespace App\Http\Controllers;

use App\Actions\Repository\CreateNewRepository;
use App\Actions\Repository\GetRepositories;
use App\Actions\Repository\GetRepositoryById;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RepositoryController extends Controller
{
    public function getRequestNew(Request $request)
    {                
        return Inertia::render('Repositories/Create');
    }

    public function create(Request $request)
    {
        return CreateNewRepository::create($request->all());
    }

    public function index()
    {
        $repositories = GetRepositories::get();
        return Inertia::render('Repositories/Index', [
            'repositories' => $repositories
        ]);
    }

    public function show($id)
    {
        $repository = GetRepositoryById::get($id);
        return Inertia::render('Repositories/Show', [
            'repository' => $repository
        ]);
    }
}
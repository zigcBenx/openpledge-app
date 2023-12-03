<?php

namespace App\Http\Controllers;

use App\Actions\Repository\CreateNewRepository;
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
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class RepositoryController extends Controller
{
    public function getRequestNew(Request $request)
    {
        // TODO get value from request -> validate it
        // check if repository exists on github
        // if exists: check if it already exists in our database
            // if so: response with already existing message and link to that repository on open pledge
            // if not: create new repository and open it on open pledge
                
        return Inertia::render('RequestRepository');
    }
}

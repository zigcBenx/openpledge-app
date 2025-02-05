<?php

namespace App\Traits;

use Exception;

trait LoadsGraphQLQueries
{
    public static function loadGraphQLQuery(string $name): string
    {
        $path = resource_path("graphql/{$name}.graphql");
        
        if (!file_exists($path)) {
            throw new Exception("GraphQL query file not found: {$name}");
        }
        
        return file_get_contents($path);
    }
} 
<?php

namespace App\Actions\Github;

use Exception;
use Github\Exception\ValidationFailedException;

class GetGithubRepositoryByName
{
    /**
     * Delete the given user.
     */
    public static function run($githubUser, $repositoryName)
    {
        try {
            $githubResult = GetGithubRepositories::run('repo:' . $githubUser . '/' . $repositoryName);
            if (!$githubResult['total_count']) {
                throw new Exception('This repository is private or it doesn\'t exist on Github!');
            }
            return $githubResult['items'][0];
        } catch (ValidationFailedException $e) {
            throw new Exception('The specified user or repository does not exist on GitHub!', $e->getCode(), $e);
        } catch (Exception $e) {
            throw $e;
        }
    }
}

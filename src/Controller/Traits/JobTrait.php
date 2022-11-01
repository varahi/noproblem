<?php

declare(strict_types=1);

namespace App\Controller\Traits;

trait JobTrait
{
    /**
     * @param $user
     * @return array|null
     */
    public function getFeaturedProfiles($user)
    {
        if ($user != null && count($user->getFeaturedProfiles()) > 0) {
            foreach ($user->getFeaturedProfiles() as $featuredProfile) {
                $featuredProfiles[] = $featuredProfile->getId();
            }
        } else {
            $featuredProfiles = null;
        }

        return $featuredProfiles;
    }

    /**
     * @param $user
     * @return array|null
     */
    public function getFeaturedJobs($user)
    {
        // Get liked vacancies
        if ($user != null && count($user->getFeaturedJobs()) > 0) {
            foreach ($user->getFeaturedJobs() as $featuredJob) {
                $featuredJobs[] = $featuredJob->getId();
            }
        } else {
            $featuredJobs = null;
        }

        return $featuredJobs;
    }
}

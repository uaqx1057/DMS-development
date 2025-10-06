<?php

namespace App\Services;

use App\Repositories\LanguageRepository;

class LanguageService
{
   /**
     * Create a new class instance.
     */
    public function __construct(protected LanguageRepository $languageRepository)
    {
        //
    }

    public function all()
    {
        return $this->languageRepository->all();
    }

}

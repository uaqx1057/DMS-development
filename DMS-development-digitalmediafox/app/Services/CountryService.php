<?php

namespace App\Services;

use App\Repositories\CountryRepository;

class CountryService
{
   /**
     * Create a new class instance.
     */
    public function __construct(protected CountryRepository $countryRepository)
    {
        //
    }

    public function all()
    {
        return $this->countryRepository->all();
    }

}

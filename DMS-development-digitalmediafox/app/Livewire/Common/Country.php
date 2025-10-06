<?php

namespace App\Livewire\Common;

use App\Services\CountryService;
use Livewire\Attributes\Modelable;
use Livewire\Component;

class Country extends Component
{
    #[Modelable]
    public $country_id = '';

    protected CountryService $countryService;

    public function mount(CountryService $countryService): void
    {
        $this->countryService = $countryService;
    }

    public function render()
    {
        $countries = cache()->rememberForever('countries', function () {
            return $this->countryService->all();
        });

        return view('livewire.common.country', compact('countries'));
    }
}

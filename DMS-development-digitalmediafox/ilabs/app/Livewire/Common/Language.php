<?php

namespace App\Livewire\Common;

use App\Services\LanguageService;
use Livewire\Attributes\Modelable;
use Livewire\Component;

class Language extends Component
{

    #[Modelable]
    public $language_id = '';

    protected LanguageService $languageService;

    public function mount(LanguageService $languageService): void
    {
        $this->languageService = $languageService;
    }

    public function render()
    {
        $languages = cache()->rememberForever('languages', function () {
            return $this->languageService->all();
        });

        return view('livewire.common.language', compact('languages'));
    }
}

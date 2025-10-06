<?php

namespace App\Repositories;

use App\Interfaces\LanguageInterface;
use App\Models\Language;

class LanguageRepository implements LanguageInterface
{
    public function all(){
        return Language::all();
    }
}

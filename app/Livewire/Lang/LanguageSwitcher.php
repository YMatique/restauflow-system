<?php

namespace App\Livewire\Lang;

use Livewire\Component;

class LanguageSwitcher extends Component
{

    public function setLanguage($lang)
    {
        app()->setLocale($lang);
        session(['locale' => $lang]);
        $this->dispatch('$refresh');
    }

    public function render()
    {
        return view('livewire.lang.language-switcher');
    }
}

<?php

namespace App\Livewire\POS;

use App\Models\Category;
use Livewire\Component;

class CategoriesBar extends Component
{
   public $selectedCategory = null;

    public function mount($selectedCategory = null)
    {
        $this->selectedCategory = $selectedCategory;
    }

    public function selectCategory($categoryId)
    {
        $this->selectedCategory = $categoryId;
        
        // Disparar evento para atualizar produtos
        $this->dispatch('categorySelected', categoryId: $categoryId);
    }

    public function getCategoriesProperty()
    {
        return Category::where('company_id', auth()->user()->company_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function render()
    {
        return view('livewire.p-o-s.categories-bar', [
            'categories' => $this->categories
        ]);
    }
}

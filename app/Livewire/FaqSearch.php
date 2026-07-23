<?php

namespace App\Livewire;

use App\Models\Faq;
use Livewire\Component;

class FaqSearch extends Component
{
    public string $search = '';

    public function render()
    {
        $query = Faq::latest();

        if (! empty(trim($this->search))) {
            $search = trim($this->search);
            $query->where(function ($q) use ($search) {
                $q->where('pertanyaan', 'like', "%{$search}%")
                    ->orWhere('jawaban', 'like', "%{$search}%");
            });
        }

        $faqs = $query->get();

        return view('livewire.faq-search', [
            'faqs' => $faqs,
        ]);
    }
}

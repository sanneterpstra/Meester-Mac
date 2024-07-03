<?php

namespace App\Http\Livewire;

use App\Models\Review;
use Livewire\Component;

class HomePage extends Component
{
    public $reviews;

    public function render()
    {
        $this->reviews = Review::all()->filter(function ($review) {
            return $review->comment !== '';
        })->map(function ($review) {
            if (str_contains($review->comment, '(Translated by Google)')) {
                $translation_pos = strpos($review->comment, '(Translated by Google)');
                $original_pos = strpos($review->comment, '(Original)');

                $originaltext = substr($review->comment, $original_pos + 11);
                $translatedtext = substr($review->comment, $translation_pos, (strlen($review->comment) - $original_pos) * -1);

                $review->translatedComment = $translatedtext;
                $review->comment = $originaltext;
            }

            return $review;
        });

        return view('livewire.site.home-page');
    }
}

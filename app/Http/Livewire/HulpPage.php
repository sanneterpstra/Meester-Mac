<?php

namespace App\Http\Livewire;

use App\Models\Review;
use Livewire\Component;

class HulpPage extends Component
{
    public $reviews;

    public function render()
    {
        $reviews = Review::all();
        $collection1 = collect();
        $collection2 = collect();
        $collection3 = collect();

        foreach ($reviews as $index => $review) {
            $wordToFind = '(Original)';
            $string = $review->comment;
            $foundPosition = strstr($string, $wordToFind);

            if ($foundPosition !== false) {
                $result = substr($string, strpos($string, $foundPosition) + 10);
                $review->comment = mb_convert_encoding($result, 'ISO-8859-1', 'UTF-8');
            }

            switch ($index % 3) {
                case 0:
                    $collection1->push($review);
                    break;
                case 1:
                    $collection2->push($review);
                    break;
                case 2:
                    $collection3->push($review);
                    break;
            }
        }

        $this->reviews = collect([$collection1, $collection2, $collection3]);

        return view('livewire.hulp-page');
    }
}

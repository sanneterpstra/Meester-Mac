<?php

namespace App\Hub\Components\Slots;

use Livewire\Component;
use Lunar\Hub\Slots\AbstractSlot;
use Lunar\Hub\Slots\Traits\HubSlot;

class TrackingNumberSlot extends Component implements AbstractSlot
{
    use HubSlot;

    public bool $showTrackingInfoEdit = false;

    public $meta;

    public function mount()
    {
        $this->meta = (array) $this->slotModel->meta;
    }

    public function rules()
    {
        return [
            'meta.tracking_url' => 'url',
        ];
    }

    public static function getName()
    {
        return 'hub.components.slots.tracking-number-slot';
    }

    public function getSlotHandle()
    {
        return 'tracking-number-slot';
    }

    public function getSlotInitialValue()
    {
        return [];
    }

    public function getSlotPosition()
    {
        return 'top';
    }

    public function getSlotTitle()
    {
        return 'Tracking info';
    }

    public function updateSlotModel()
    {
    }

    public function saveTrackingInfo()
    {
        $this->validate($this->rules());

        $this->slotModel->meta = $this->meta;
        $this->slotModel->save();

        $this->slotModel->refresh();

        // $this->notify("Tracking info updated");

        $this->showTrackingInfoEdit = false;
    }

    public function render()
    {
        return view('hub.livewire.components.tracking-number-slot');
    }
}

@props([
    'unavailableDates' => [],
    'error' => false
    ])
    <div {!! $attributes->merge(['class' => 'border'])
        ->class([
        'border-red-400' => !!$error,
        ]) !!}>
        <div wire:ignore x-data="{
    datepicker: null,
    selectedDate: @entangle($attributes->wire('date')),

    updateDate() {
        this.selectedDate = this.datepicker.getDate('dd/mm/yyyy');
    },

    init(){
        const today = new Date();
        const inThirtyDays = new Date(today);
        inThirtyDays.setDate(today.getDate() + 30);

        this.datepicker = new Datepicker(this.$refs.picker, {
            language: 'nl',
            showDaysOfWeek: true,
            maxDate: inThirtyDays,
            minDate: today,
            datesDisabled: {{collect($unavailableDates)}},
            language: 'nl',
            daysOfWeekDisabled: [0,6],
            maxView: 0,
        });

        if(this.selectedDate) {
            this.datepicker.setDate(this.selectedDate);
        }
    }
}">
            <div x-ref="picker" x-on:click="updateDate"></div>
        </div>
    </div>
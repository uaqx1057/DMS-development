@props([
    'checked' => false,
    'name' => '',
    'label' => ''
])
<div class="form-check form-radio-secondary mb-3">
    <input class="form-check-input" type="radio" name="{{$name}}" id="{{$name}}" @if($checked) checked @endif wire:model='is_receive_email_notification'>
    <label class="form-check-label" for="formradioRight6">
        @translate($label)
    </label>
</div>

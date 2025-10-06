@props([
    'required' => false,
    'name' => ''
])
<label {{ $attributes->merge(['class' => 'form-label']) }}>{{ $name }}
@if ($required)
<span class="text-danger">*</span>
@endif
</label>

@props(['type' => 'text', 'value' => null])

<input 
    type="{{ $type }}" 
    {{ $attributes->merge(['class' => 'form-control']) }}
    @if($value) value="{{ $value }}" @endif
>


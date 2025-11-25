@props(['messages', 'class' => ''])

@if ($messages)
    <div {{ $attributes->merge(['class' => 'text-danger ' . $class]) }}>
        @foreach ((array) $messages as $message)
            <p class="text-danger">{{ $message }}</p>
        @endforeach
    </div>
@endif


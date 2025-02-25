@props(['title' => null, 'disabled' => false])

@php
    $headline = Str::headline($title);
    $slug = Str::slug($title);
@endphp

<div class="form-group">
    <label for="{{ $slug }}">{{ $headline }} <span class="text-danger">*</span></label>
    <textarea name="{{ $slug }}" {!! $attributes->merge(['class' => 'form-control', 'id' => $slug]) !!}  rows="4" placeholder="Enter {{ $headline }}"  {{ $disabled ? 'disabled' : '' }}>{{ old($slug) }}</textarea>

    @if ($errors->has($slug))
        <div class="error text-danger mt-3">{{ $errors->first($slug) }}</div>
    @endif
</div>
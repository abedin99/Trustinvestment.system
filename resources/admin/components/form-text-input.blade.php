
@props(['title' => null,'data' => null, 'disabled' => false, 'required' => false])

@php
    $headline = Str::headline($title);
    $slug = Str::slug($title);
@endphp

<div class="form-group">
    <label for="{{ $slug }}">{{ $headline }} @if($required)<span class="text-danger">*</span>@endif</label>
    <input name="{{ $slug }}" {!! $attributes->merge(['class' => 'form-control', 'id' => $slug]) !!} value="{{ old($slug, $data) }}" placeholder="Enter {{ $headline }}"  {{ $disabled ? 'disabled' : '' }} />

    @if ($errors->has($slug))
        <div class="error text-danger mt-3">{{ $errors->first($slug) }}</div>
    @endif
</div>
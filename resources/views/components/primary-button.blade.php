<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn cur-p btn-primary']) }}>
    {{ $slot }}
</button>

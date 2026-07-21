@props(['class' => 'h-8 w-8'])

<img src="{{ company_logo_url() }}"
     alt="{{ company_name() }}"
     {{ $attributes->merge(['class' => $class . ' object-contain']) }} />

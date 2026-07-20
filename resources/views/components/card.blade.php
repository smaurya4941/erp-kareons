<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden']) }}>
    @if(isset($header))
        <div class="px-6 py-5 border-b border-gray-50 bg-gray-50/30">
            {{ $header }}
        </div>
    @endif
    
    <div class="p-6">
        {{ $slot }}
    </div>

    @if(isset($footer))
        <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-50">
            {{ $footer }}
        </div>
    @endif
</div>

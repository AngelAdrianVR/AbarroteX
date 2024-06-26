@component('mail::message')
# {{ $greeting }}

{!! $description !!}

@component('mail::button', ['url' => $url])
@if (isset($button_label))
{{ $button_label }}
@else
Ver en sistema    
@endif   
@endcomponent

{{ $salutation }}

@isset($subcopy)
@component('mail::subcopy')
{!! $subcopy !!}
@endcomponent
@endisset

@endcomponent
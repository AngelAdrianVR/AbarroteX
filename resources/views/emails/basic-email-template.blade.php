@component('mail::message')
# {{ $greeting }}

{!! $description !!}

{{ $salutation }}

@component('mail::button', ['url' => $url])
Ver en sistema
@endcomponent
@endcomponent
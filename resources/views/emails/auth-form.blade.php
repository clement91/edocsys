@component('mail::message')
# Hey there!

{{ $data['name'] }} has requested you to review the document regarding to {{ $data['message'] }} <br/>
Please click the button below to proceed.

@component('mail::button', ['url' => $url ])
Review Document
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent

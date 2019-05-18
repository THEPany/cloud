@component('mail::message')
#Hola {{ $user->name }},

La organizacion **{{ $organization->name }}** te a invitado a ser parte de su equipo.
@component('mail::button', ['url' => $url])
Aceptar invitación
@endcomponent

Gracias, <br>
{{ config('app.name') }}

@slot('subcopy')
If you’re having trouble clicking the "Aceptar invitación" button, copy and paste the URL below into your web browser: [{{$url}}]({{$url }})
@endslot
@endcomponent
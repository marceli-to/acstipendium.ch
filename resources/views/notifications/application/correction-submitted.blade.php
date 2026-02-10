<x-mail::message>
<div class="text-base">
<strong>Korrektur eingegangen</strong>
</div>
<br>
<div class="text-base">
Folgende Bewerbung wurde korrigiert:
</div>
<br>
@if ($data['firstname'] || $data['name'])
<div class="text-base">
<strong>Name</strong><br>
{{ $data['firstname'] }} {{ $data['name'] }}
</div>
<br>
@endif
@if ($data['email'])
<div class="text-base">
<strong>E-Mail</strong><br>
{{ $data['email'] }}
</div>
<br>
@endif
</x-mail::message>

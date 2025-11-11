<x-mail::message>
<div class="text-base">
Die Bestätigungs-E-Mail für folgende Bewerbung konnte nicht zugestellt werden:
</div>
<br>
@if ($data['user_name'])
<div class="text-base">
<strong>Bewerber</strong><br>
{{ $data['user_name'] }}
</div>
<br>
@endif
@if ($data['recipient_email'])
<div class="text-base">
<strong>E-Mail</strong><br>
{{ $data['recipient_email'] }}
</div>
<br>
@endif
@if ($data['error_message'])
<div class="text-base">
<strong>Fehler</strong><br>
{{ $data['error_message'] }}
</div>
<br>
@endif
<div class="text-base">
Die Bewerbung wurde trotzdem erfolgreich gespeichert. Bitte kontaktieren Sie den Bewerber auf einem anderen Weg, um die Bewerbung zu bestätigen.
</div>
</x-mail::message>

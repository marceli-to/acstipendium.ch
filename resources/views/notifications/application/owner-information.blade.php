<x-mail::message>
<div class="text-base">
Neue Stipendium-Bewerbung
</div>
<br>
@if ($data['firstname'])
<div class="text-base">
<strong>Vorname</strong><br>
{{ $data['firstname'] }}
</div>
<br>
@endif
@if ($data['name'])
<div class="text-base">
<strong>Name</strong><br>
{{ $data['name'] }}
</div>
<br>
@endif
@if ($data['name_artist_group'])
<div class="text-base">
<strong>Name Künstlergruppe</strong><br>
{{ $data['name_artist_group'] }}
</div>
<br>
@endif
@if ($data['dob'])
<div class="text-base">
<strong>Geburtsdatum</strong><br>
{{ $data['dob'] }}
</div>
<br>
@endif
@if ($data['street'])
<div class="text-base">
<strong>Strasse/Nr.</strong><br>
{{ $data['street'] }}
</div>
<br>
@endif
@if ($data['zip'])
<div class="text-base">
<strong>PLZ</strong><br>
{{ $data['zip'] }}
</div>
<br>
@endif
@if ($data['location'])
<div class="text-base">
<strong>Ort</strong><br>
{{ $data['location'] }}
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
@if ($data['phone'])
<div class="text-base">
<strong>Telefon</strong><br>
{{ $data['phone'] }}
</div>
<br>
@endif
@if ($data['website'])
<div class="text-base">
<strong>Website</strong><br>
{{ $data['website'] }}
</div>
<br>
@endif
@if (isset($data['zip_file']) && $data['zip_file'] && isset($data['entry_id']))
<div class="text-base">
  <strong>Dokumente</strong><br>
  <a
    href="{{ route('applications.download-zip', $data['entry_id']) }}"
    class="btn">
    Herunterladen (ZIP)
  </a>
</div>
<br>
@endif
</x-mail::message>

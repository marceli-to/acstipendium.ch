<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'firstname' => 'required',
            'name_artist_group' => 'nullable',
            'dob' => 'required|date',
            'street' => 'required',
            'zip' => 'required',
            'location' => 'required',
            'phone' => 'required',
            'website' => 'nullable|url',
            'email' => 'required|email|regex:/^[^\s@]+@[^\s@]+\.[^\s@]+$/',
            'geographic_relation_text' => 'required|max:500',
            'age_verification_files' => 'required|array|min:1',
            'age_verification_files.*' => 'file|mimes:png,jpg,jpeg,pdf,zip|max:10240',
            'geographic_relation_proofs' => 'required|array|min:1',
            'geographic_relation_proofs.*' => 'file|mimes:png,jpg,jpeg,pdf,zip|max:10240',
            'privacy' => 'accepted',
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Name ist erforderlich',
            'firstname.required' => 'Vorname ist erforderlich',
            'dob.required' => 'Geburtsdatum ist erforderlich',
            'dob.date' => 'Geburtsdatum muss ein gültiges Datum sein',
            'street.required' => 'Adresse ist erforderlich',
            'zip.required' => 'PLZ ist erforderlich',
            'location.required' => 'PLZ/Ort ist erforderlich',
            'phone.required' => 'Telefon ist erforderlich',
            'website.url' => 'Website muss eine gültige URL sein',
            'email.required' => 'E-Mail ist erforderlich',
            'email.email' => 'E-Mail muss gültig sein',
            'email.regex' => 'E-Mail muss gültig sein',
            'geographic_relation_text.required' => 'Bernbezug ist erforderlich',
            'geographic_relation_text.max' => 'Bernbezug darf maximal 500 Zeichen enthalten',
            'age_verification_files.required' => 'Altersnachweis ist erforderlich',
            'age_verification_files.min' => 'Mindestens ein Altersnachweis muss hochgeladen werden',
            'age_verification_files.*.file' => 'Altersnachweis muss eine Datei sein',
            'age_verification_files.*.mimes' => 'Altersnachweis muss eine PNG, JPG, JPEG, PDF oder ZIP Datei sein',
            'age_verification_files.*.max' => 'Altersnachweis darf maximal 10 MB groß sein',
            'geographic_relation_proofs.required' => 'Mindestens ein Beleg ist erforderlich',
            'geographic_relation_proofs.min' => 'Mindestens ein Beleg muss hochgeladen werden',
            'geographic_relation_proofs.*.file' => 'Beleg muss eine Datei sein',
            'geographic_relation_proofs.*.mimes' => 'Beleg muss eine PNG, JPG, JPEG, PDF oder ZIP Datei sein',
            'geographic_relation_proofs.*.max' => 'Beleg darf maximal 10 MB groß sein',
            'privacy.accepted' => 'Die Datenschutzbestimmungen müssen akzeptiert werden',
        ];
    }
}

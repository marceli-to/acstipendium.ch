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
            'resume_files' => 'required|array|min:1',
            'resume_files.*' => 'file|mimes:pdf|max:20480',
            'works' => 'required|array|min:1',
            'works.*.title' => 'required|string|max:255',
            'works.*.year' => 'required|string|max:255',
            'works.*.dimensions' => 'nullable|string|max:255',
            'works.*.duration' => 'nullable|string|max:255',
            'works.*.technology' => 'required|string|max:500',
            'works.*.remarks' => 'nullable|string|max:500',
            'privacy_truthful' => 'accepted',
            'privacy_original_work' => 'accepted',
            'privacy_ai' => 'accepted',
            'privacy_data' => 'accepted',
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
            'resume_files.required' => 'Dossier ist erforderlich',
            'resume_files.min' => 'Dossier muss hochgeladen werden',
            'resume_files.*.file' => 'Dossier muss eine Datei sein',
            'resume_files.*.mimes' => 'Dossier muss eine PDF Datei sein',
            'resume_files.*.max' => 'Dossier darf maximal 20 MB groß sein',
            'works.required' => 'Mindestens ein Werk ist erforderlich',
            'works.min' => 'Mindestens ein Werk muss angegeben werden',
            'works.*.title.required' => 'Werktitel ist erforderlich',
            'works.*.title.max' => 'Werktitel darf maximal 255 Zeichen enthalten',
            'works.*.year.required' => 'Jahr ist erforderlich',
            'works.*.year.max' => 'Jahr darf maximal 255 Zeichen enthalten',
            'works.*.dimensions.max' => 'Grösse darf maximal 255 Zeichen enthalten',
            'works.*.duration.max' => 'Dauer darf maximal 255 Zeichen enthalten',
            'works.*.technology.required' => 'Technik ist erforderlich',
            'works.*.technology.max' => 'Technik darf maximal 500 Zeichen enthalten',
            'works.*.remarks.max' => 'Kommentar darf maximal 500 Zeichen enthalten',
            'privacy_truthful.accepted' => 'Sie müssen bestätigen, dass Ihre Angaben wahrheitsgemäss sind',
            'privacy_original_work.accepted' => 'Sie müssen bestätigen, dass die Arbeiten eigenständig entstanden sind',
            'privacy_ai.accepted' => 'Sie müssen bestätigen, dass der Einsatz von KI gekennzeichnet ist',
            'privacy_data.accepted' => 'Sie müssen die Teilnahmebedingungen und Datenschutzerklärung akzeptieren',
        ];
    }
}

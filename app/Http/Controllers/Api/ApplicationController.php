<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use Statamic\Facades\Entry;
use App\Notifications\Course\UserConfirmation;
use App\Notifications\Course\OwnerInformation;

class ApplicationController extends Controller
{
  public function store(Request $request)
  {
    $validationResult = $this->validateRequest($request);

    if ($validationResult !== TRUE)
    {
      return $validationResult;
    }

    $title = $request->input('firstname') . ' ' . $request->input('name') . ' ' . $request->input('email');

    // build data
    $data = [
      'title' => $title,
      'name' => $request->input('name'),
      'firstname' => $request->input('firstname'),
      'dob' => $request->input('dob'),
      'street' => $request->input('street'),
      'location' => $request->input('location'),
      'phone' => $request->input('phone'),
      'email' => $request->input('email'),
      'remarks' => $request->input('remarks') ?? null,
    ];

    $entry = Entry::make()
      ->collection('applications')
      ->slug($title)
      ->data($data)
      ->save();


    Notification::route('mail', $request->input('email'))
      ->notify(new UserConfirmation($data));

    Notification::route('mail', env('MAIL_TO'))
      ->notify(new OwnerInformation($data));

    return response()->json(['message' => 'Store successful']);
  }

  protected function validateRequest(Request $request)
  {
    $validationRules = $this->getValidationRules();

    $validator = Validator::make(
      $request->all(),
      $validationRules['rules'],
      $validationRules['messages']
    );

    if ($validator->fails())
    {
      $errors = $validator->errors();
      $formattedErrors = [];

      foreach ($errors->messages() as $field => $messages)
      {
        $formattedErrors[$field] = $messages[0];
      }

      return response()->json(['errors' => $formattedErrors], 422);
    }

    return TRUE;
  }

  protected function getValidationRules()
  {
    $validationRules = [
      'name' => 'required',
      'firstname' => 'required',
      'dob' => 'required|date',
      'street' => 'required',
      'location' => 'required',
      'phone' => 'required',
      'email' => 'required|email|regex:/^[^\s@]+@[^\s@]+\.[^\s@]+$/',
      'privacy' => 'accepted',
    ];

    // Set validation messages
    $validationMessages = [
      'name.required' => 'Name ist erforderlich',
      'firstname.required' => 'Vorname ist erforderlich',
      'dob.required' => 'Geburtsdatum ist erforderlich',
      'dob.date' => 'Geburtsdatum muss ein gültiges Datum sein',
      'street.required' => 'Adresse ist erforderlich',
      'location.required' => 'PLZ/Ort ist erforderlich',
      'phone.required' => 'Telefon ist erforderlich',
      'email.required' => 'E-Mail ist erforderlich',
      'email.email' => 'E-Mail muss gültig sein',
      'email.regex' => 'E-Mail muss gültig sein',
      'privacy.accepted' => 'Die Datenschutzbestimmungen müssen akzeptiert werden',
    ];

    return [
      'rules' => $validationRules,
      'messages' => $validationMessages,
    ];
  }
}
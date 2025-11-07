<template>
  <template v-if="formSuccess">
    <success-alert>
      {{ trans('Vielen Dank für Ihre Anmeldung!') }}
    </success-alert>
  </template>
  <template v-if="formError">
    <error-alert>
      {{ trans('Bitte überprüfen Sie die eingegebenen Daten.') }}
    </error-alert>
  </template>
  <heading-1>
    {{ trans('Formular') }}
  </heading-1>
  <form
    @submit.prevent="submitForm"
    class="md:grid md:grid-cols-12 gap-x-8 md:gap-x-16">

    <div class="md:col-span-6">
      <card>
        <heading-2>
          {{ trans('Persönliche Angaben') }}
        </heading-2>
        <form-group>
          <form-text-field
            v-model="form.firstname"
            :error="errors.firstname"
            @update:error="errors.firstname = $event"
            :placeholder="errors.firstname ? errors.firstname : trans('Vorname')"
            :label="trans('Vorname')"
            :aria-label="trans('Vorname')"
            required
          />
        </form-group>
        <form-group>
          <form-text-field
            v-model="form.name"
            :error="errors.name"
            @update:error="errors.name = $event"
            :placeholder="errors.name ? errors.name : trans('Name')"
            :label="trans('Name')"
            :aria-label="trans('Name')"
            required
          />
        </form-group>
        <form-group>
          <form-text-field
            v-model="form.name_artist_group"
            :error="errors.name_artist_group"
            @update:error="errors.name_artist_group = $event"
            :placeholder="errors.name_artist_group ? errors.name_artist_group : trans('Name Künstlergruppe')"
            :label="trans('Name Künstlergruppe')"
            :aria-label="trans('Name Künstlergruppe')"
          />
        </form-group>
        <form-group>
          <form-text-field
            type="text"
            v-model="form.street"
            :error="errors.street"
            @update:error="errors.street = $event"
            :placeholder="errors.street ? errors.street : trans('Adresse')"
            :label="trans('Adresse')"
            :aria-label="trans('Adresse')"
            required
          />
        </form-group>
        <div class="md:grid md:grid-cols-2 md:gap-x-16">
          <form-group>
            <form-text-field
              type="text"
              v-model="form.zip"
              :error="errors.zip"
              @update:error="errors.zip = $event"
              :placeholder="errors.zip ? errors.zip : trans('PLZ')"
              :label="trans('PLZ')"
              :aria-label="trans('PLZ')"
              required
            />
          </form-group>
          <form-group>
            <form-text-field
              type="text"
              v-model="form.location"
              :error="errors.location"
              @update:error="errors.location = $event"
              :placeholder="errors.location ? errors.location : trans('Ort')"
              :label="trans('Ort')"
              :aria-label="trans('Ort')"
              required
            />
          </form-group>
        </div>

        <form-group>
          <form-text-field
            type="text"
            v-model="form.phone"
            :error="errors.phone"
            @update:error="errors.phone = $event"
            :placeholder="errors.phone ? errors.phone : trans('Telefon')"
            :label="trans('Telefon')"
            :aria-label="trans('Telefon')"
            required
          />
        </form-group>
        <form-group>
          <form-text-field
            type="text"
            v-model="form.website"
            :error="errors.website"
            @update:error="errors.website = $event"
            :placeholder="errors.website ? errors.website : trans('Website')"
            :label="trans('Website')"
            :aria-label="trans('Website')"
          />
        </form-group>
        <form-group>
          <form-text-field
            type="email"
            v-model="form.email"
            :error="errors.email"
            @update:error="errors.email = $event"
            :placeholder="errors.email ? errors.email : trans('E-Mail')"
            :label="trans('E-Mail')"
            :aria-label="trans('E-Mail')"
            required
          />
        </form-group>
      </card>
    </div>

    <div class="md:col-span-6">

      <card>
        <heading-2>
          {{ trans('Bernbezug') }}
        </heading-2>
        <p>
          {{  trans('Ein Bezug zum Kanton Bern ist Voraussetzung. Bitte lesen Sie die Teilnahmebedingungen.')  }}
        </p>
        <form-group>
          <form-textarea-field
            v-model="form.geographic_relation_text"
            :error="errors.geographic_relation_text"
            @update:error="errors.geographic_relation_text = $event"
            :placeholder="errors.geographic_relation_text ? errors.geographic_relation_text : trans('Maxine')"
            :label="trans('Dein Bernbezug')"
            :aria-label="trans('Dein Bernbezug')"
            :maxlength="500"
            required
          />
        </form-group>
        <form-group
          v-for="(proof, index) in geographicRelationProofFields"
          :key="index"
          class="">
          <file-upload
            v-model="form.geographic_relation_proofs[index]"
            :name="`geographic_relation_proof_${index}`"
            :label="index === 0 ? trans('Belege') : ''"
            :required="index === 0"
            :error="index === 0 ? errors.geographic_relation_proofs : ''"
            @update:error="errors.geographic_relation_proofs = $event"
          />
        </form-group>
        <form-button
          type="button"
          @click="addGeographicRelationProofField"
          :label="trans('Weiteren Beleg hinzufügen')"
          class="pill pill-sm pill-solid-primary pill-icon-sm">
          <svg class="w-10 h-auto" width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M15.4403 12.5547V0H12.5497V12.5547H0V15.4453H12.5497V28H15.4403V15.4453H28V12.5547H15.4403Z" fill="white"/>
          </svg>
        </form-button>
      </card>

      <card>
        <heading-2>
          {{ trans('Altersgrenze') }}
        </heading-2>
        <p>
          {{ trans('Das Höchstalter für die Teilnahme beträgt 40 Jahre (der 41. Geburtstag darf im Jahr der Jurierung noch nicht erreicht sein).') }}
        </p>
        <form-group>
          <form-dob-field
            v-model="form.dob"
            :error="errors.dob"
            @update:error="errors.dob = $event"
            :placeholder="errors.dob ? errors.dob : '01.01.2000'"
            :label="trans('Geburtsdatum')"
            :aria-label="trans('Geburtsdatum')"
            :eligibility-year="eligibilityYear"
            required
          />
        </form-group>
        <form-group>
          <file-upload
            v-model="form.age_verification_files"
            name="age_verification"
            :label="trans('ID / Ausweis')"
            :error="errors.age_verification_files"
            @update:error="errors.age_verification_files = $event"
            required
          />
        </form-group>
      </card>

    </div>

    <div class="col-span-full grid grid-cols-6 md:grid-cols-12 gap-8 md:gap-16 mt-8 md:mt-16">
      <card
        v-for="(work, index) in works"
        :key="index"
        :class="getWorkCardClass(index)"
        class="relative col-span-full">
        <heading-2>
          {{ trans('Werk') }} {{ index + 1 }}
        </heading-2>

        <div class="md:grid md:grid-cols-2 md:gap-16">
          <form-group>
            <form-text-field
              v-model="work.title"
              :error="errors[`works.${index}.title`]"
              @update:error="errors[`works.${index}.title`] = $event"
              :placeholder="errors[`works.${index}.title`] ? errors[`works.${index}.title`] : trans('Werktitel')"
              :label="trans('Titel')"
              :aria-label="trans('Titel')"
              required
            />
          </form-group>
          <form-group>
            <form-text-field
              v-model="work.year"
              :error="errors[`works.${index}.year`]"
              @update:error="errors[`works.${index}.year`] = $event"
              :placeholder="errors[`works.${index}.year`] ? errors[`works.${index}.year`] : '2025'"
              :label="trans('Jahr')"
              :aria-label="trans('Jahr')"
              required
            />
          </form-group>
        </div>

        <div class="md:grid md:grid-cols-2 md:gap-16">
          <form-group>
            <form-text-field
              v-model="work.dimensions"
              :error="errors[`works.${index}.dimensions`]"
              @update:error="errors[`works.${index}.dimensions`] = $event"
              :placeholder="errors[`works.${index}.dimensions`] ? errors[`works.${index}.dimensions`] : 'L x B x T'"
              :label="trans('Grösse')"
              :aria-label="trans('Grösse')"
            />
          </form-group>
          <form-group>
            <form-text-field
              v-model="work.duration"
              :error="errors[`works.${index}.duration`]"
              @update:error="errors[`works.${index}.duration`] = $event"
              :placeholder="errors[`works.${index}.duration`] ? errors[`works.${index}.duration`] : 'hh:mm:ss'"
              :label="trans('Dauer')"
              :aria-label="trans('Dauer')"
            />
          </form-group>
        </div>

        <form-group>
          <form-textarea-field
            v-model="work.technology"
            :error="errors[`works.${index}.technology`]"
            @update:error="errors[`works.${index}.technology`] = $event"
            :placeholder="errors[`works.${index}.technology`] ? errors[`works.${index}.technology`] : trans('Technik')"
            :label="trans('Technik')"
            :aria-label="trans('Technik')"
            :maxlength="500"
            required
          />
        </form-group>

        <form-group class="!mb-10 md:!mb-12">
          <form-textarea-field
            v-model="work.remarks"
            :error="errors[`works.${index}.remarks`]"
            @update:error="errors[`works.${index}.remarks`] = $event"
            :placeholder="errors[`works.${index}.remarks`] ? errors[`works.${index}.remarks`] : trans('Kommentar')"
            :label="trans('Kommentar')"
            :aria-label="trans('Kommentar')"
            :maxlength="500"
          />
        </form-group>

        <div class="flex justify-center">
          <form-button
            v-if="index > 0"
            type="button"
            @click="removeWork(index)"
            :label="trans('löschen')"
            class="pill pill-sm pill-solid-primary md:!h-24 !text-sm md:!text-md md:!px-12" />
        </div>

      </card>
    </div>

    <div class="col-span-full flex justify-center mt-8 md:mt-16">
      <form-button
        type="button"
        @click="addWork"
        :label="trans('Weiteres Werk hinzufügen')"
        class="pill pill-lg pill-solid-primary pill-icon-sm !mb-0">
        <svg class="w-16 md:w-20" width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M15.4403 12.5547V0H12.5497V12.5547H0V15.4453H12.5497V28H15.4403V15.4453H28V12.5547H15.4403Z" fill="white"/>
        </svg>
      </form-button>
    </div>

    <card class="col-span-full md:col-span-8 md:col-start-3 lg:col-span-6 lg:col-start-4 mt-8 md:mt-16">
      <heading-2>
        {{ trans('Dossier') }}
      </heading-2>
      <p>
        {{ trans('Maximale Dateigrösse: 20 MB') }}<br>
        {{ trans('Datei-Format: PDF') }}
      </p>
      <form-group>
        <file-upload
          v-model="form.resume_files"
          name="resume"
          :label="trans('Dossier')"
          :error="errors.resume_files"
          @update:error="errors.resume_files = $event"
          required
        />
      </form-group>
    </card>

    <card class="col-span-full md:col-span-10 md:col-start-2 lg:col-span-8 lg:col-start-3">
      <heading-2>
        {{ trans('Abschluss') }}
      </heading-2>
      <div>
        {{ trans('Mit dem Absenden dieser Anmeldung erkläre ich mich mit den Teilnahmebedingungen des AC-Stipendiums einverstanden. Der Upload kann einige Minuten dauern. Wenn der Upload geklappt hat, erhalten Sie im Anschluss ein Bestätigungs-E-Mail. Falls Sie dieses nicht bekommen, bitte nochmals versuchen.') }}
      </div>

      <div class="md:grid md:grid-cols-12 md:gap-x-16 mt-14 md:mt-32">
        <form-group class="md:col-span-6 text-sm md:text-md">
          <form-checkbox
            v-model="form.privacy_truthful"
            :error="errors.privacy_truthful"
            @update:error="errors.privacy_truthful = $event"
            id="privacy-truthful"
            name="privacy_truthful"
            :label="trans('Ich bestätige, dass meine Angaben wahrheitsgemäss und vollständig sind. Mir ist bewusst, dass falsche oder unvollständige Angaben zum Ausschluss führen können.')" />
        </form-group>

        <form-group class="md:col-span-6 text-sm md:text-md">
          <form-checkbox
            v-model="form.privacy_original_work"
            :error="errors.privacy_original_work"
            @update:error="errors.privacy_original_work = $event"
            id="privacy-original-work"
            name="privacy_original_work"
            :label="trans('Ich bestätige, dass die eingereichten Arbeiten eigenständig und unabhängig entstanden sind, weder im Rahmen einer Ausbildung noch unter Anleitung Dritter.')" />
        </form-group>

        <form-group class="md:col-span-6 text-sm md:text-md">
          <form-checkbox
            v-model="form.privacy_ai"
            :error="errors.privacy_ai"
            @update:error="errors.privacy_ai = $event"
            id="privacy-ai"
            name="privacy_ai"
            :label="trans('Ich bestätige, dass der Einsatz von Künstlicher Intelligenz (KI) oder ähnlichen Systemen in meinen Kunstwerken entsprechend gekennzeichnet ist.')" />
        </form-group>

        <form-group class="md:col-span-6 text-sm md:text-md">
          <form-checkbox
            v-model="form.privacy_data"
            :error="errors.privacy_data"
            @update:error="errors.privacy_data = $event"
            id="privacy-data"
            name="privacy_data"
            :label="trans('Mit meiner Bewerbung bestätige ich, die Teilnahmebedingungen und Datenschutzerklärung akzeptiert zu haben.')" />
        </form-group>
      </div>

      <form-group class="flex justify-center w-full mt-14 md:mt-32">
        <form-button
          type="submit"
          :label="trans('Absenden')"
          :disabled="isSubmitting"
          class="pill pill-lg pill-solid-primary pill-icon-lg">
          <template v-if="isSubmitting">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="!w-16 !h-16 md:!w-20 md:!h-20 relative">
              <path fill="currentColor" d="M12,4a8,8,0,0,1,7.89,6.7A1.53,1.53,0,0,0,21.38,12h0a1.5,1.5,0,0,0,1.48-1.75,11,11,0,0,0-21.72,0A1.5,1.5,0,0,0,2.62,12h0a1.53,1.53,0,0,0,1.49-1.3A8,8,0,0,1,12,4Z"><animateTransform attributeName="transform" dur="0.75s" repeatCount="indefinite" type="rotate" values="0 12 12;360 12 12"/></path>
            </svg>
          </template>
        </form-button>
      </form-group>

    </card>
    
  </form>
</template>
<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import FormGroup from '@/forms/components/fields/group.vue';
import FormTextField from '@/forms/components/fields/text.vue';
import FormDobField from '@/forms/components/fields/dob.vue';
import FormTextareaField from '@/forms/components/fields/textarea.vue';
import FormButton from '@/forms/components/fields/button.vue';
import FormCheckbox from '@/forms/components/fields/checkbox.vue';
import FileUpload from '@/forms/components/fields/file-upload.vue';
import Card from '@/forms/components/card.vue';
import SuccessAlert from '@/forms/components/alerts/success.vue';
import ErrorAlert from '@/forms/components/alerts/error.vue';
import Heading1 from '@/forms/components/headings/h1.vue';
import Heading2 from '@/forms/components/headings/h2.vue';
import { useFormScroll } from '@/composables/useFormScroll';
import { useTranslations } from '@/composables/useTranslations';

const props = defineProps({
  eligibilityYear: {
    type: Number,
    default: () => new Date().getFullYear() + 1
  }
});

const { scrollToForm } = useFormScroll();
const { trans, getLocale } = useTranslations();

const isSubmitting = ref(false);
const formSuccess = ref(false);
const formError = ref(false);

// Track number of geographic relation proof fields
const geographicRelationProofFields = ref([0]);

// Track works
const works = ref([
  {
    title: '',
    year: '',
    dimensions: '',
    duration: '',
    technology: '',
    remarks: ''
  }
]);

const form = ref({
  name: '',
  firstname: '',
  name_artist_group: '',
  dob: '',
  street: '',
  zip: '',
  location: '',
  phone: '',
  website: '',
  email: '',
  geographic_relation_text: '',
  geographic_relation_proofs: [[]],
  age_verification_files: [],
  resume_files: [],
  privacy_truthful: false,
  privacy_original_work: false,
  privacy_ai: false,
  privacy_data: false
});

const errors = ref({
  name: '',
  firstname: '',
  dob: '',
  street: '',
  zip: '',
  location: '',
  phone: '',
  email: '',
  age_verification_files: '',
  geographic_relation_proofs: '',
  resume_files: '',
  privacy_truthful: '',
  privacy_original_work: '',
  privacy_ai: '',
  privacy_data: '',
});

function addGeographicRelationProofField() {
  const nextIndex = geographicRelationProofFields.value.length;
  geographicRelationProofFields.value.push(nextIndex);
  form.value.geographic_relation_proofs.push([]);
}

function addWork() {
  works.value.push({
    title: '',
    year: '',
    dimensions: '',
    duration: '',
    technology: '',
    remarks: ''
  });
}

function removeWork(index) {
  if (index > 0) {
    works.value.splice(index, 1);
  }
}

function getWorkCardClass(index) {
  const totalWorks = works.value.length;
  const isLastItem = index === totalWorks - 1;
  const isOddTotal = totalWorks % 2 === 1;

  // If odd number of works and this is the last one: centered
  if (isOddTotal && isLastItem) {
    return 'md:col-span-8 md:col-start-3 lg:col-span-6 lg:col-start-4';
  }

  // All other cases: just col-span-6
  return 'md:col-span-6';
}

async function submitForm() {
  isSubmitting.value = true;
  formSuccess.value = false;
  formError.value = false;

  try {
    const formData = new FormData();

    // Add all form fields
    formData.append('name', form.value.name || '');
    formData.append('firstname', form.value.firstname || '');
    formData.append('name_artist_group', form.value.name_artist_group || '');
    formData.append('dob', form.value.dob || '');
    formData.append('street', form.value.street || '');
    formData.append('zip', form.value.zip || '');
    formData.append('location', form.value.location || '');
    formData.append('phone', form.value.phone || '');
    formData.append('website', form.value.website || '');
    formData.append('email', form.value.email || '');
    formData.append('geographic_relation_text', form.value.geographic_relation_text || '');
    formData.append('privacy_truthful', form.value.privacy_truthful ? '1' : '0');
    formData.append('privacy_original_work', form.value.privacy_original_work ? '1' : '0');
    formData.append('privacy_ai', form.value.privacy_ai ? '1' : '0');
    formData.append('privacy_data', form.value.privacy_data ? '1' : '0');

    // Add age verification files
    if (form.value.age_verification_files && form.value.age_verification_files.length > 0) {
      form.value.age_verification_files.forEach((file, index) => {
        formData.append(`age_verification_files[${index}]`, file);
      });
    }

    // Add resume files
    if (form.value.resume_files && form.value.resume_files.length > 0) {
      form.value.resume_files.forEach((file, index) => {
        formData.append(`resume_files[${index}]`, file);
      });
    }

    // Add geographic relation proofs (flatten the nested arrays)
    if (form.value.geographic_relation_proofs && form.value.geographic_relation_proofs.length > 0) {
      let fileIndex = 0;
      form.value.geographic_relation_proofs.forEach((filesArray) => {
        if (Array.isArray(filesArray) && filesArray.length > 0) {
          filesArray.forEach((file) => {
            formData.append(`geographic_relation_proofs[${fileIndex}]`, file);
            fileIndex++;
          });
        }
      });
    }

    // Add works
    works.value.forEach((work, index) => {
      formData.append(`works[${index}][title]`, work.title || '');
      formData.append(`works[${index}][year]`, work.year || '');
      formData.append(`works[${index}][dimensions]`, work.dimensions || '');
      formData.append(`works[${index}][duration]`, work.duration || '');
      formData.append(`works[${index}][technology]`, work.technology || '');
      formData.append(`works[${index}][remarks]`, work.remarks || '');
    });

    const response = await axios.post('/api/application', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    });
    handleSuccess();
  } catch (error) {
    handleError(error);
  }
}

function handleSuccess() {
  // Reset form
  form.value = {
    name: '',
    firstname: '',
    name_artist_group: '',
    dob: '',
    street: '',
    zip: '',
    location: '',
    phone: '',
    website: '',
    email: '',
    geographic_relation_text: '',
    geographic_relation_proofs: [[]],
    age_verification_files: [],
    resume_files: [],
    privacy_truthful: false,
    privacy_original_work: false,
    privacy_ai: false,
    privacy_data: false
  };

  // Reset geographic relation proof fields to just one
  geographicRelationProofFields.value = [0];

  // Reset works to just one empty work
  works.value = [
    {
      title: '',
      year: '',
      dimensions: '',
      duration: '',
      technology: '',
      remarks: ''
    }
  ];

  errors.value = {
    name: '',
    firstname: '',
    dob: '',
    street: '',
    zip: '',
    location: '',
    phone: '',
    email: '',
    age_verification_files: '',
    geographic_relation_proofs: '',
    resume_files: '',
    privacy_truthful: '',
    privacy_original_work: '',
    privacy_ai: '',
    privacy_data: '',
  };

  isSubmitting.value = false;
  formSuccess.value = true;

  // Redirect to success page based on locale
  const locale = getLocale();
  const successUrl = locale === 'fr'
    ? '/fr/bourse/inscription-reussie'
    : '/stipendium/anmeldung-erfolgreich';

  window.location.href = successUrl;
}

function handleError(error) {
  isSubmitting.value = false;
  formError.value = true;
  if (error.response && error.response.data && typeof error.response.data.errors === 'object') {
    Object.keys(error.response.data.errors).forEach(key => {
      const errorValue = error.response.data.errors[key];
      errors.value[key] = Array.isArray(errorValue) ? errorValue[0] : errorValue;
    });
  }
  scrollToForm();
}
</script>
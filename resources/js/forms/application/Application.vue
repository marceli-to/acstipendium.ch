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
    class="lg:grid lg:grid-cols-12 gap-8 lg:gap-16">

    <div class="lg:col-span-6">
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
        <div class="lg:grid lg:grid-cols-2 lg:gap-x-16">
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
    
    <div class="lg:col-span-6">

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
            :label="trans('Dein Bernbezug (max. 500 Zeichen)')"
            :aria-label="trans('Dein Bernbezug (max. 500 Zeichen)')"
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
            required
          />
        </form-group>
      </card>

    </div>
   

    <!-- <form-group>
      <form-checkbox
        v-model="form.privacy"
        :error="errors.privacy"
        @update:error="errors.privacy = $event"
        id="privacy-contact"
        name="privacy"
        label="Ich habe die <a href='/datenschutz' class='decoration-1'>Datenschutzerklärung</a> gelesen und stimme dieser zu.*"
      />
    </form-group> -->
    <div class="col-span-full">
      <form-group class="flex justify-center w-full">
        <form-button
          type="submit"
          :label="trans('Absenden')"
          :disabled="isSubmitting"
          class="pill pill-lg pill-solid-primary"
        >
          <template v-if="isSubmitting">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-12 h-12 relative">
              <path fill="currentColor" d="M12,4a8,8,0,0,1,7.89,6.7A1.53,1.53,0,0,0,21.38,12h0a1.5,1.5,0,0,0,1.48-1.75,11,11,0,0,0-21.72,0A1.5,1.5,0,0,0,2.62,12h0a1.53,1.53,0,0,0,1.49-1.3A8,8,0,0,1,12,4Z"><animateTransform attributeName="transform" dur="0.75s" repeatCount="indefinite" type="rotate" values="0 12 12;360 12 12"/></path>
            </svg>
          </template>
        </form-button>
      </form-group>
    </div>
    
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
const { trans } = useTranslations();

const isSubmitting = ref(false);
const formSuccess = ref(false);
const formError = ref(false);

// Track number of geographic relation proof fields
const geographicRelationProofFields = ref([0]);

const form = ref({
  name: 'Stadelmann',
  firstname: 'Marcel',
  name_artist_group: 'MTO KG',
  dob: '07.04.1977',
  street: 'Lindenhofstrasse 1',
  zip: '4052',
  location: 'Basel',
  phone: '078 749 74 09',
  website: 'https://marceli.to',
  email: 'm@marceli.to',
  geographic_relation_proofs: [[]],
  age_verification_files: [],
  privacy: true
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
  privacy: '',
});

function addGeographicRelationProofField() {
  const nextIndex = geographicRelationProofFields.value.length;
  geographicRelationProofFields.value.push(nextIndex);
  form.value.geographic_relation_proofs.push([]);
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
    formData.append('privacy', form.value.privacy ? '1' : '0');

    // Add age verification files
    if (form.value.age_verification_files && form.value.age_verification_files.length > 0) {
      form.value.age_verification_files.forEach((file, index) => {
        formData.append(`age_verification_files[${index}]`, file);
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
    name: null,
    firstname: null,
    name_artist_group: null,
    dob: null,
    street: null,
    zip: null,
    location: null,
    phone: null,
    website: null,
    email: null,
    geographic_relation_text: null,
    geographic_relation_proofs: [[]],
    age_verification_files: [],
    privacy: false
  };

  // Reset geographic relation proof fields to just one
  geographicRelationProofFields.value = [0];

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
    privacy: '',
  };

  isSubmitting.value = false;
  formSuccess.value = true;
  scrollToForm();
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
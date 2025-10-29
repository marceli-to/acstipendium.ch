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

    <card class="lg:col-span-6">
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
        <form-dob-field
          v-model="form.dob"
          :error="errors.dob"
          @update:error="errors.dob = $event"
          :placeholder="errors.dob ? errors.dob : trans('Geburtsdatum')"
          :label="trans('Geburtsdatum')"
          :aria-label="trans('Geburtsdatum')"
          :eligibility-year="eligibilityYear"
          required
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
      <form-group>
        <form-text-field
          type="text"
          v-model="form.location"
          :error="errors.location"
          @update:error="errors.location = $event"
          :placeholder="errors.location ? errors.location : trans('PLZ/Ort')"
          :label="trans('PLZ/Ort')"
          :aria-label="trans('PLZ/Ort')"
          required
        />
      </form-group>
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
          />
        </form-group>
      </card>

      <card>
        <heading-2>
          {{ trans('Altersgrenze') }}
        </heading-2>
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
    </form-group>
    <form-group>
      <form-button 
        type="submit" 
        :label="'Anmelden'"
        :disabled="isSubmitting"
        :submitting="isSubmitting"
      />
    </form-group> -->
    
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

const form = ref({
  name: null,
  firstname: null,
  name_artist_group: null,
  dob: null,
  street: null,
  location: null,
  phone: null,
  website: null,
  email: null,
  privacy: false
});

const errors = ref({
  name: '',
  firstname: '',
  dob: '',
  street: '',
  location: '',
  phone: '',
  email: '',
  privacy: '',
});


onMounted(async () => {

});

async function submitForm() {
  isSubmitting.value = true;
  formSuccess.value = false;
  formError.value = false;

  try {
    const response = await axios.post('/api/application', {
      ...form.value
    });
    handleSuccess();
  } catch (error) {
    handleError(error);
  }
}

function handleSuccess() {
  form.value = {
    name: null,
    firstname: null,
    name_artist_group: null,
    dob: null,
    street: null,
    location: null,
    phone: null,
    website: null,
    email: null,
    privacy: false
  };

  errors.value = {
    name: '',
    firstname: '',
    dob: '',
    street: '',
    location: '',
    phone: '',
    email: '',
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
      errors.value[key] = error.response.data.errors[key];
    });
  }
  scrollToForm();
}
</script>
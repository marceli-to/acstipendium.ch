<template>
  <div id="correction-request-alerts" class="scroll-mt-100 md:scroll-mt-150 lg:scroll-mt-200">
    <template v-if="success">
      <success-alert>
        {{ trans('Falls eine Bewerbung mit dieser E-Mail-Adresse existiert, erhalten Sie einen Korrektur-Link per E-Mail.') }}
      </success-alert>
    </template>
    <template v-if="formError">
      <error-alert>
        {{ trans('Bitte überprüfen Sie die eingegebenen Daten.') }}
      </error-alert>
    </template>
  </div>
  <heading-1>
    {{ trans('Bewerbung korrigieren') }}
  </heading-1>
  <p class="mb-8">
    {{ trans('Geben Sie Ihre E-Mail-Adresse ein, mit der Sie sich beworben haben. Sie erhalten einen Link, um Ihre Bewerbung zu korrigieren.') }}
  </p>
  <form @submit.prevent="submitForm" v-if="!success" class="max-w-2xl">
    <card>
      <form-group>
        <form-text-field
          type="email"
          v-model="email"
          :error="errors.email"
          @update:error="errors.email = $event"
          :placeholder="errors.email ? errors.email : trans('E-Mail')"
          :label="trans('E-Mail')"
          :aria-label="trans('E-Mail')"
          required
        />
      </form-group>
      <form-group class="flex justify-center w-full mt-8">
        <form-button
          type="submit"
          :label="trans('Korrektur-Link anfordern')"
          :disabled="isSubmitting"
          class="pill pill-lg pill-solid-primary">
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
import { ref } from 'vue';
import axios from 'axios';
import FormGroup from '@/forms/components/fields/group.vue';
import FormTextField from '@/forms/components/fields/text.vue';
import FormButton from '@/forms/components/fields/button.vue';
import Card from '@/forms/components/card.vue';
import SuccessAlert from '@/forms/components/alerts/success.vue';
import ErrorAlert from '@/forms/components/alerts/error.vue';
import Heading1 from '@/forms/components/headings/h1.vue';
import { useTranslations } from '@/composables/useTranslations';

const { trans } = useTranslations();

const email = ref('');
const isSubmitting = ref(false);
const success = ref(false);
const formError = ref(false);
const errors = ref({ email: '' });

async function submitForm() {
  isSubmitting.value = true;
  formError.value = false;
  errors.value = { email: '' };

  try {
    await axios.post('/api/correction/request', { email: email.value });
    success.value = true;
  } catch (error) {
    formError.value = true;
    if (error.response?.data?.errors) {
      Object.keys(error.response.data.errors).forEach(key => {
        const val = error.response.data.errors[key];
        errors.value[key] = Array.isArray(val) ? val[0] : val;
      });
    }
  } finally {
    isSubmitting.value = false;
  }
}
</script>

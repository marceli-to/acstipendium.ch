<template>
  <div>
    <template v-if="loading">
      <heading-1>
        {{ trans('Bewerbung wird geladen...') }}
      </heading-1>
    </template>
    <template v-else-if="expired">
      <error-alert>
        {{ trans('Dieser Korrektur-Link ist ungültig oder abgelaufen. Bitte fordern Sie einen neuen Link an.') }}
      </error-alert>
    </template>
    <template v-else-if="prefillData">
      <application-form
        :prefill-data="prefillData"
        mode="correction"
        :endpoint="`/api/correction/${token}`"
        :eligibility-year="eligibilityYear"
      />
    </template>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import ApplicationForm from '@/forms/application/Application.vue';
import ErrorAlert from '@/forms/components/alerts/error.vue';
import Heading1 from '@/forms/components/headings/h1.vue';
import { useTranslations } from '@/composables/useTranslations';

const { trans } = useTranslations();

const props = defineProps({
  token: {
    type: String,
    required: true
  },
  eligibilityYear: {
    type: Number,
    default: () => new Date().getFullYear() + 1
  }
});

const loading = ref(true);
const expired = ref(false);
const prefillData = ref(null);

onMounted(async () => {
  try {
    const response = await axios.get(`/api/correction/${props.token}`);
    prefillData.value = response.data;
  } catch (error) {
    expired.value = true;
  } finally {
    loading.value = false;
  }
});
</script>

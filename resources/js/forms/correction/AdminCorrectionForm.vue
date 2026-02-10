<template>
  <div>
    <heading-1>
      {{ trans('Bewerbung korrigieren') }}
    </heading-1>

    <template v-if="loadingList">
      <p class="text-white/60">{{ trans('Bewerbungen werden geladen...') }}</p>
    </template>
    <template v-else>
      <div class="mb-32 md:mb-64 max-w-xl mx-auto">
        <select
          v-model="selectedId"
          @change="loadApplication"
          class="w-full bg-transparent border-2 border-white rounded-full px-8 py-4 md:py-6 text-white focus:outline-none focus:!ring-0 appearance-none cursor-pointer"
        >
          <option value="" disabled>{{ trans('Bewerbung auswählen') }}</option>
          <option
            v-for="app in applications"
            :key="app.id"
            :value="app.id"
          >
            {{ app.firstname }} {{ app.name }} — {{ app.email }}
          </option>
        </select>
      </div>

      <template v-if="loadingApplication">
        <p class="text-white/60">{{ trans('Bewerbung wird geladen...') }}</p>
      </template>
      <template v-else-if="prefillData">
        <application-form
          :prefill-data="prefillData"
          mode="correction"
          :endpoint="`/api/correction/admin/${selectedId}`"
          :eligibility-year="eligibilityYear"
        />
      </template>
    </template>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import ApplicationForm from '@/forms/application/Application.vue';
import Heading1 from '@/forms/components/headings/h1.vue';
import { useTranslations } from '@/composables/useTranslations';

const { trans } = useTranslations();

const props = defineProps({
  eligibilityYear: {
    type: Number,
    default: () => new Date().getFullYear() + 1
  }
});

const loadingList = ref(true);
const loadingApplication = ref(false);
const applications = ref([]);
const selectedId = ref('');
const prefillData = ref(null);

onMounted(async () => {
  try {
    const response = await axios.get('/api/correction/admin/applications');
    applications.value = response.data;
  } catch (error) {
    console.error('Failed to load applications', error);
  } finally {
    loadingList.value = false;
  }
});

async function loadApplication() {
  if (!selectedId.value) return;

  loadingApplication.value = true;
  prefillData.value = null;

  try {
    const response = await axios.get(`/api/correction/admin/${selectedId.value}`);
    prefillData.value = response.data;
  } catch (error) {
    console.error('Failed to load application', error);
  } finally {
    loadingApplication.value = false;
  }
}
</script>

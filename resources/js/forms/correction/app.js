import { createApp } from 'vue';
import CorrectionRequest from './CorrectionRequest.vue';
import CorrectionForm from './CorrectionForm.vue';
import ApplicationForm from '../application/Application.vue';

if (document.getElementById('correction-request')) {
  const app = createApp({});
  app.component('correction-request', CorrectionRequest);
  app.mount('#correction-request');
}

if (document.getElementById('correction-form')) {
  const app = createApp({});
  app.component('correction-form', CorrectionForm);
  app.component('application-form', ApplicationForm);
  app.mount('#correction-form');
}

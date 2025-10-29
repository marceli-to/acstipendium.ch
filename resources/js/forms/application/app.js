import { createApp } from 'vue';
import ApplicationForm from './Application.vue';
const app = createApp({});
app.component('application-form', ApplicationForm);
if (document.getElementById('application-form')) {
  app.mount('#application-form');
}
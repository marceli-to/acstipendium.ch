<template>
  <div class="relative">
    <form-label
      :label="label"
      :required="required" />
    <file-pond
      ref="pond"
      :name="name"
      :label-idle="translatedLabelIdle"
      :allow-multiple="allowMultiple"
      :accepted-file-types="acceptedFileTypes"
      :max-file-size="maxFileSize"
      :server="serverConfig"
      :files="modelValue"
      @updatefiles="handleUpdateFiles"
      class-name="filepond-custom"
    />
  </div>
</template>

<script setup>
import { computed } from 'vue';
import vueFilePond from 'vue-filepond';
import 'filepond/dist/filepond.min.css';
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';
import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size';
import FormLabel from './label.vue';
import { useTranslations } from '@/composables/useTranslations';

const FilePond = vueFilePond(
  FilePondPluginFileValidateType,
  FilePondPluginFileValidateSize
);

const { trans } = useTranslations();

const translatedLabelIdle = computed(() => {
  return `<span class="filepond--label-action">${trans('Datei auswählen')}</span> <span>${trans('Keine Datei ausgewählt')}</span>`;
});

const props = defineProps({
  modelValue: {
    type: Array,
    default: () => []
  },
  name: {
    type: String,
    required: true
  },
  label: {
    type: String,
    default: ''
  },
  required: {
    type: Boolean,
    default: false
  },
  prefix: {
    type: String,
    required: true
  },
  allowMultiple: {
    type: Boolean,
    default: false
  },
  maxFileSize: {
    type: String,
    default: '10MB'
  }
});

const emit = defineEmits(['update:modelValue']);

const acceptedFileTypes = ['image/png', 'image/jpeg', 'image/jpg', 'application/pdf', 'application/zip'];

const serverConfig = {
  process: {
    url: '/api/upload',
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
    },
    ondata: (formData) => {
      formData.append('prefix', props.prefix);
      return formData;
    }
  },
  revert: '/api/upload/revert',
  restore: null,
  load: null,
  fetch: null
};

const handleUpdateFiles = (fileItems) => {
  const files = fileItems.map(fileItem => ({
    source: fileItem.source,
    serverId: fileItem.serverId,
    filename: fileItem.filename
  }));
  emit('update:modelValue', files);
};
</script>

<style>
.filepond-custom .filepond--root {
  font-family: inherit;
}

.filepond-custom .filepond--drop-label {
  height: auto;
  min-height: 48px;
  display: flex;
  align-items: center;
  padding: 0 3rem;
}

.filepond-custom .filepond--drop-label label {
  display: flex;
  align-items: center;
  gap: 1rem;
  width: 100%;
  cursor: pointer;
}

.filepond-custom .filepond--label-action {
  background-color: var(--color-primary);
  color: white;
  padding: 0.5rem 2rem;
  border-radius: 9999px;
  text-decoration: none;
  font-size: 0.875rem;
  font-weight: 500;
  white-space: nowrap;
  cursor: pointer;
  transition: background-color 0.2s;
}

.filepond-custom .filepond--label-action:hover {
  background-color: var(--color-primary-dark, var(--color-primary));
}

.filepond-custom .filepond--panel-root {
  background-color: white;
  border: 2px solid white;
  border-radius: 9999px;
}

.filepond-custom .filepond--list-scroller {
  margin-top: 1rem;
}

.filepond-custom .filepond--item {
  background-color: white;
  border: 2px solid white;
  border-radius: 9999px;
  margin-bottom: 0.5rem;
}

.filepond-custom .filepond--file {
  padding: 0.5rem 2rem;
}

.filepond-custom .filepond--credits {
  display: none;
}
</style>

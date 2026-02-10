<template>
  <div class="relative">
    <form-label
      :label="label"
      :required="required"
      :error="error" />

    <div
      :class="[
        'w-full pl-6 pr-8 md:px-10 py-2 md:py-6 bg-white text-primary rounded-full border-2 flex items-center gap-4',
        error ? 'border-danger' : 'border-white'
      ]">
      <label
        :for="name"
        :class="[
          'pill pill-sm md:!h-24 cursor-pointer whitespace-nowrap !mb-0 !text-sm md:!text-md md:!px-12 !leading-none',
          error ? 'bg-danger text-white border-danger' : 'pill-solid-primary'
        ]">
        {{ trans('Datei auswählen') }}
      </label>
      <input
        :id="name"
        type="file"
        :name="name"
        :multiple="allowMultiple"
        :accept="accept"
        @change="handleFileChange"
        class="hidden"
      />
      <span class="text-sm md:text-md truncate flex-1 text-primary/50">
        <template v-if="hasNewFiles">{{ fileLabel }}</template>
        <template v-else-if="existingFile">
          <a v-if="existingFile.download_url" :href="existingFile.download_url" target="_blank" class="underline">{{ existingFile.name }}</a>
          <span v-else>{{ existingFile.name }}</span>
        </template>
        <template v-else>{{ fileLabel }}</template>
      </span>
      <button
        v-if="deletable"
        type="button"
        @click="handleDelete"
        :class="[
          'pill pill-sm md:!h-24 whitespace-nowrap !mb-0 !text-sm md:!text-md md:!px-12 !leading-none',
          error ? 'bg-danger text-white border-danger' : 'pill-solid-primary'
        ]">
        {{ trans('löschen') }}
      </button>
    </div>

    <div
      v-if="error"
      class="text-danger text-sm md:text-md ml-8 md:ml-12 mt-2 lg:mt-4">
      {{ error }}
    </div>
    <div
      v-if="hint && !error"
      class="text-danger text-sm md:text-md ml-8 md:ml-12 mt-2 lg:mt-4">
      {{ hint }}
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import FormLabel from './label.vue';
import { useTranslations } from '@/composables/useTranslations';

const { trans } = useTranslations();

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
  error: {
    type: String,
    default: ''
  },
  required: {
    type: Boolean,
    default: false
  },
  allowMultiple: {
    type: Boolean,
    default: false
  },
  deletable: {
    type: Boolean,
    default: false
  },
  accept: {
    type: String,
    default: 'image/png,image/jpeg,image/jpg,application/pdf'
  },
  existingFile: {
    type: Object,
    default: null
  },
  hint: {
    type: String,
    default: ''
  }
});

const emit = defineEmits(['update:modelValue', 'update:error', 'delete']);

const hasNewFiles = computed(() => {
  const files = props.modelValue || [];
  return files.length > 0;
});

const fileLabel = computed(() => {
  const files = props.modelValue || [];
  if (files.length === 0) {
    return trans('Keine Datei ausgewählt');
  }
  if (files.length === 1) {
    return files[0].name;
  }
  return `${files.length} ${trans('Dateien ausgewählt')}`;
});

const handleFileChange = (event) => {
  const files = Array.from(event.target.files || []);
  emit('update:modelValue', files);
  // Clear error when files are selected
  if (files.length > 0) {
    emit('update:error', '');
  }
};

const handleDelete = () => {
  emit('delete');
};
</script>

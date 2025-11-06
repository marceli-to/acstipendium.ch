<template>
  <div class="relative">
    <form-label
      :label="label"
      :required="required"
      :error="error" />

    <div
      :class="[
        'w-full pl-6 pr-8 lg:px-10 py-2 lg:py-6 bg-white text-primary rounded-full border-2 flex items-center gap-4',
        error ? 'border-danger' : 'border-white'
      ]">
      <label
        :for="name"
        :class="[
          'pill pill-sm lg:!h-24 cursor-pointer whitespace-nowrap !mb-0 !text-sm lg:!text-md lg:!px-12 !leading-none',
          error ? 'bg-danger text-white border-danger' : 'pill-solid-primary'
        ]">
        {{ trans('Datei auswählen') }}
      </label>
      <input
        :id="name"
        type="file"
        :name="name"
        :multiple="allowMultiple"
        :accept="acceptedFileTypes"
        @change="handleFileChange"
        class="hidden"
      />
      <span class="text-sm lg:text-md truncate"
        :class="error ? 'text-danger/50' : 'text-primary/50'">
        {{ error || fileLabel }}
      </span>
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
  }
});

const emit = defineEmits(['update:modelValue', 'update:error']);

const acceptedFileTypes = 'image/png,image/jpeg,image/jpg,application/pdf,application/zip';

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
</script>

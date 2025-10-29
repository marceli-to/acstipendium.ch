<template>
  <div class="relative">
    <form-label
      :label="label"
      :required="required" />
    <input
      type="text"
      v-maska="'##.##.####'"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      @blur="validateAge"
      @focus="$emit('update:error', '')"
      :placeholder="placeholder"
      :aria-label="ariaLabel"
      :required="required"
      :class="[
        'w-full px-12 lg:px-16 py-8 bg-white text-primary text-sm lg:text-md rounded-full !border-none !ring-0 focus:!ring-0 focus:!outline-none placeholder:text-sm placeholder:lg:text-md placeholder:text-primary/50 leading-none',
        { '!border-red-700 placeholder:!text-red-700': error },
      ]"
    >
    <form-error :error="error" />
  </div>
</template>

<script setup>
import { vMaska } from "maska/vue"
import FormLabel from './label.vue';
import FormError from './error.vue';
import { useTranslations } from '@/composables/useTranslations';

const { trans } = useTranslations();

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  placeholder: {
    type: String,
    default: ''
  },
  label: {
    type: String,
    default: ''
  },
  ariaLabel: {
    type: String,
    default: ''
  },
  required: {
    type: Boolean,
    default: false
  },
  error: {
    type: String,
    default: ''
  },
  eligibilityYear: {
    type: Number,
    default: () => new Date().getFullYear() + 1
  },
  maxAge: {
    type: Number,
    default: 40
  }
});

const emit = defineEmits(['update:modelValue', 'update:error']);

const validateAge = () => {
  if (!props.modelValue || props.modelValue.length < 10) {
    return;
  }

  // Parse the date from DD.MM.YYYY format
  const parts = props.modelValue.split('.');
  if (parts.length !== 3) {
    return;
  }

  const day = parseInt(parts[0], 10);
  const month = parseInt(parts[1], 10);
  const year = parseInt(parts[2], 10);

  // Validate the date parts
  if (isNaN(day) || isNaN(month) || isNaN(year)) {
    emit('update:error', trans('Ungültiges Datum'));
    return;
  }

  // Create date object (month is 0-indexed in JS)
  const birthDate = new Date(year, month - 1, day);

  // Check if the date is valid
  if (birthDate.getDate() !== day || birthDate.getMonth() !== month - 1 || birthDate.getFullYear() !== year) {
    emit('update:error', trans('Ungültiges Datum'));
    return;
  }

  // Check if the birth date is in the future
  if (birthDate > new Date()) {
    emit('update:error', trans('Geburtsdatum kann nicht in der Zukunft liegen'));
    return;
  }

  // Calculate age at the end of the eligibility year
  // The (maxAge + 1)th birthday must not have been reached in the eligibility year
  const referenceDate = new Date(props.eligibilityYear, 11, 31); // December 31st of eligibility year
  const maxAgeBirthday = new Date(year + props.maxAge + 1, month - 1, day);

  // If (maxAge + 1)th birthday is on or before Dec 31st of eligibility year, they're too old
  if (maxAgeBirthday <= referenceDate) {
    emit('update:error', trans(`Das Höchstalter für die Teilnahme beträgt 40 Jahre (der 41. Geburtstag darf im Jahr der Jurierung ${props.eligibilityYear} noch nicht erreicht sein)`));
    return;
  }

  // Clear any previous errors
  emit('update:error', '');
};
</script>

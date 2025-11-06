<template>
  <div class="relative">
    <form-label
      :label="label"
      :required="required"
      :error="error" />
    <input
      :type="type"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      @focus="$emit('update:error', '')"
      :placeholder="placeholder"
      :aria-label="ariaLabel"
      :class="[
        'w-full px-12 lg:px-16 py-6 bg-white text-primary text-sm lg:text-md rounded-full border-2 border-white !ring-0 focus:!ring-0 focus:!outline-none placeholder:text-sm placeholder:lg:text-md placeholder:text-primary/50 leading-none',
        { '!border-2 !border-danger placeholder:!text-danger/50': error },
      ]"
    >
  </div>
</template>

<script setup>
import FormLabel from './label.vue';

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  type: {
    type: String,
    default: 'text'
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
});

defineEmits(['update:modelValue', 'update:error']);
</script>

<template>
  <div class="relative">
    <form-label
      :label="label"
      :required="required" />
    <input
      :type="type"
      v-maska="mask"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      @focus="$emit('update:error', '')"
      :placeholder="placeholder"
      :aria-label="ariaLabel"
      :required="required"
      :class="[
        'w-full px-12 lg:px-16 py-8 bg-white text-primary text-sm lg:text-md rounded-full !border-none !ring-0 focus:!ring-0 focus:!outline-none placeholder:text-sm placeholder:lg:text-md placeholder:text-primary/50 leading-none',
        { '!border-red-700 placeholder:!text-red-700': error },
      ]"
    >
  </div>
</template>

<script setup>
import { vMaska } from "maska/vue"
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
  mask: {
    type: String,
    required: true
  },
});

defineEmits(['update:modelValue', 'update:error']);
</script>

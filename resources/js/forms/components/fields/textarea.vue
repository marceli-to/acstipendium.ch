<template>
  <div class="relative">
    <div class="flex items-center justify-between mb-4">
      <form-label
        :label="label"
        :required="required" />
      <div
        v-if="maxlength"
        class="text-xs lg:text-sm text-right mr-8 lg:mr-12">
        {{ characterCount }}/{{ maxlength }}
      </div>
    </div>
    <textarea
      :value="modelValue"
      :placeholder="placeholder"
      :maxlength="maxlength"
      @input="$emit('update:modelValue', $event.target.value)"
      @focus="$emit('update:error', '')"
      :aria-label="ariaLabel"
      :required="required"
      :class="[
        'w-full min-h-80 lg:min-h-120 px-12 lg:px-16 py-8 lg:py-12 bg-white text-primary text-sm lg:text-md rounded-16 lg:rounded-24 !border-none !ring-0 focus:!ring-0 focus:!outline-none placeholder:text-sm placeholder:lg:text-md placeholder:text-primary/50 [field-sizing:content]',
        { '!border-red-700 placeholder:!text-red-700': error },
      ]">
    </textarea>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import FormLabel from './label.vue';

const props = defineProps({
  modelValue: {
    type: String,
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
  maxlength: {
    type: Number,
    default: null
  }
});

const characterCount = computed(() => {
  return props.modelValue ? props.modelValue.length : 0;
});

defineEmits(['update:modelValue', 'update:error']);
</script>

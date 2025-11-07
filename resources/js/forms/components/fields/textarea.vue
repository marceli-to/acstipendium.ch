<template>
  <div class="relative">
    <div class="flex items-center justify-between mb-4">
      <form-label
        :label="label"
        :required="required"
        :error="error" />
      <div
        v-if="maxlength"
        :class="[
          'text-xs md:text-sm text-right mr-8 md:mr-12',
          { 'text-danger': error }
        ]">
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
      :class="[
        'w-full min-h-80 md:min-h-120 px-12 md:px-16 py-6 md:py-10 bg-white text-primary text-sm md:text-md rounded-16 md:rounded-24 border-2 border-white !ring-0 focus:!ring-0 focus:!outline-none placeholder:text-sm placeholder:md:text-md placeholder:text-primary/50 [field-sizing:content]',
        { '!border-2 !border-danger placeholder:!text-danger/50': error },
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

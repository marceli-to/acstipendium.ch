<template>
  <button :type="type" :class="classes" :disabled="disabled">
    <slot></slot>
    {{ label }}
  </button>
</template>
<script setup>
const props = defineProps({
  type: {
    type: String,
    default: 'submit'
  },
  label: {
    type: String,
    required: true
  },
  classes: {
    type: String,
    default: ''
  },
  disabled: {
    type: Boolean,
    default: false
  }
});
</script>
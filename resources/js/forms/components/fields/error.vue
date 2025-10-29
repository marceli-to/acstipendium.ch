<template>
  <div v-if="error" :class="classes" class="text-red-700 text-xs lg:text-sm py-6 lg:py-8 px-8 lg:px-12">{{ error }}</div>
</template>
<script setup>

const props = defineProps({
  error: {
    type: String,
    default: ''
  },
  classes: {
    type: String,
    default: ''
  }
});
</script>
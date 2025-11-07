<template>
  <div v-if="error" :class="classes" class="text-danger text-xs md:text-sm py-6 md:py-8 px-8 md:px-12">{{ error }}</div>
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
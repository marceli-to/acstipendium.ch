<template>
  <div :class="classes">
    <slot></slot>
  </div>
</template>

<script setup>
const props = defineProps({
  classes: {
    type: String,
    default: 'mb-14 lg:mb-16'
  }
});
</script>
<template>
  <label
    v-if="label"
    :for="id"
    class="block py-2 text-sm lg:text-md ml-8 lg:ml-12">
    {{ label }}<span v-if="required">*</span>
  </label>
</template>

<script setup>
const props = defineProps({
  id: {
    type: String,
    default: ''
  },
  label: {
    type: String,
    default: ''
  },
  required: {
    type: Boolean,
    default: false
  }
});
</script>
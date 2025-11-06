<template>
  <div :class="['checkboxes relative', classes]">
    <div class="flex items-start gap-x-16">
      <input 
        :id="id" 
        :name="name" 
        :value="modelValue" 
        :checked="checked" 
        :disabled="disabled" 
        :required="required" 
        type="checkbox" 
        @change="handleChange"
        class="mt-2 shrink-0"
        :class="[
          { '!border-danger': error },
        ]"
      />
      <label :for="id" v-html="label"></label>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import Error from './error.vue';

const props = defineProps({
  id: {
    type: String,
    required: true,
  },
  name: {
    type: String,
    required: true,
  },
  modelValue: {
    type: [String, Boolean],
    default: false,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  required: {
    type: Boolean,
    default: false,
  },
  label: {
    type: String,
    required: true,
  },
  classes: {
    type: [String, Array, Object],
    default: '',
  },
  error: {
    type: String,
    default: '',
  },
});

const emit = defineEmits(['update:modelValue', 'update:error']);

const checked = computed(() => props.modelValue);

function handleChange(event) {
  emit('update:modelValue', event.target.checked);
  if (event.target.checked) {
    emit('update:error', '');
  }
}
</script>

<style scoped>
input[type="checkbox"] {
  @apply w-24 h-24 border-2 border-white rounded-[8px] cursor-pointer appearance-none bg-transparent;
}

input[type="checkbox"]:checked {
  @apply bg-white shadow-glow-sm-white;
}
</style>
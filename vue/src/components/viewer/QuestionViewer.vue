<template lang="">
   <fieldset class="mb-4">
     <div>
        <legend class="text-base font-medium text-gray-900">
            {{ index + 1 }}. {{ question.question }}
        </legend>
        <p class="text-gray-500 text-sm">
            {{ question.description }}
        </p>
     </div>

     <div class="mt-3">
        <div v-if="question.type === 'select'">
        
        </div>
        <div v-else-if="question.type === 'radio'">

        </div>
        <div v-else-if="question.type === 'checkbox'">

        </div>
        <div v-else-if="question.type === 'text'">

        </div>
        <div v-else-if="question.type === 'textarea'">
        
        </div>
     </div>
   </fieldset>
   <hr class="mb-4"/>
</template>
<script setup>
  import { ref } from 'vue';
   const { question, index, modelValue } = defineProps({
       question: Object,
       index: Number,
       modelValue: [String, Array],
   });

  const emits = defineEmits(['update:modelValue']);

  let model;

  if(question.type === 'checkbox'){
    model = ref({});
  }

</script>
<style scoped>

</style>

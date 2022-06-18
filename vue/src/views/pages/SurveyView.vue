<template lang="">
  <PageContent>
    <template v-slot:header>
      <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-900">
          {{ model.id ? model.title : "Create new survey" }}
        </h1>
      </div>
    </template>
    <form @submit.prevent="saveSurvey">
      <div class="shadow sm:rounded-md sm:overflow-hidden">
        <!-- survey fields -->
        <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
          <!-- image -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Image</label>
            <div class="mt-1 flex items-center">
              <img
                v-if="model.image_url"
                :src="model.image_url"
                :alt="model.title"
                class="w-64 h-48 object-cover"
              />
              <label
                v-else
                class="flex items-center justify-center h-12 w-12 rounded-full overflow-hidden bg-gray-100"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-[80%] w-[80%] text-gray-800"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                  />
                </svg>
              </label>

              <button
                type="button"
                class="relative overflow-hidden ml-5 bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                <input
                  type="file"
                  @change="onImageChoose"
                  class="absolute left-0 top-0 right-0 bottom-0 opacity-0 cursor-pointer"
                />
                Change
              </button>
            </div>
            <!-- / image -->
            <!-- title -->
            <div>
              <label for="title" class="block text-sm font-medium text-gray-700"
                >Title</label
              >
              <input
                type="text"
                name="title"
                id="title"
                v-model="model.title"
                autocomplete="survey_title"
                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
              />
            </div>
            <!-- / title -->
            <!-- description -->
            <div class="mt-3">
              <label
                for="description"
                class="block text-sm font-medium text-gray-700"
                >Description</label
              >
              <div class="mt-1">
                <textarea
                  type="textarea"
                  name="description"
                  id="description"
                  v-model="model.description"
                  autocomplete="survey_description"
                  class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                />
              </div>
            </div>
            <!-- / description -->
            <!-- expire date -->
            <div class="mt-3">
              <label
                for="expire_date"
                class="block text-sm font-medium text-gray-700"
                >Description</label
              >
              <input
                type="date"
                name="expire_date"
                id="expire_date"
                v-model="model.expire_date"
                autocomplete="survey_expire_date"
                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
              />
            </div>
            <!-- / expire date -->
            <!-- status -->
            <div class="mt-3 flex items-start">
              <div class="flex items-center h-5">
                <input
                  type="checkbox"
                  name="status"
                  id="status"
                  v-model="model.status"
                  class="mt-1 h-5 w-5 focus:ring-indigo-500 text-indigo-600 border-gray-300 rounded"
                />
              </div>

              <div class="ml-3 text-sm"></div>
              <label for="status" class="font-medium text-gray-700"
                >Active</label
              >
            </div>
            <!-- / status -->
              <!-- / survey fields -->
              <!-- questions -->
              <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                <h3 class="text-2xl font-semibold flex items-center justify-between">
                 Question
                 <!-- add questions -->
                  <button 
                  type="button"
                  @click="addQuestion()"
                  class="flex items-center text-sm py-1 px-4 rounded-sm text-white bg-gray-600 hover:bg-gray-700"
                  >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
  <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
</svg>
                  Add Question</button>
                  <!-- / add questions -->
                </h3>
                <div v-if="!model.questions?.length" class="text-center text-gray-600">
                You don't have any questions created!
                </div>
                <div v-for="(question, index) in model.questions" :key="question.id">
                <QuestionEditor
                :question="question"
                :index="index"
                @addQuestion="addQuestion"
                @deleteQuestion="deleteQuestion"
                />
                </div>
              </div>
              <!-- / questions -->
            <!-- submit button -->
            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
              <button type="submit"
               class="
               inline-flex justify-center py-2 px-4 border border-transparent
               shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none
                focus:right-2 focus:ring-offset-2 focus:ring-indigo-500
               ">Save</button>
            </div>
            <!-- / submit button -->
          </div>
        </div>
      </div>
    </form>
  </PageContent>
</template>
<script setup>
import { v4 as uuidv4 } from "uuid";
import { ref, watch } from "vue";
import { useRoute, useRouter } from "vue-router";
import store from "../../store";

import PageContent from "../../components/PageContent.vue";
import QuestionEditor from "../../components/editor/QuestionEditor.vue";

const router = useRouter();

const route = useRoute();

// create empty survey
let model = ref({
  title: "",
  status: false,
  description: null,
  image_url: null,
  expire_date: null,
  expire_date: null,
  questions: [],
});
// watch to current survey data change and when this happens we update local model 
watch(
   () => store.state.currentSurvey.data,
   (newVal, oldVal) => {
     model.value = {
      ...JSON.parse(JSON.stringify(newVal)),
      status: newVal.status !== 'draft', 
     }
   }
);

if (route.params.id) {
   store.dispatch('getSurvey', route.params.id);
}

// survey image preview
function onImageChoose(ev){
         const file = ev.target.files[0];
         const reader = new FileReader();
         reader.onload = () => {
          // the field to send on backend and apply validations
          model.value.image = reader.result;

          // the field to display here
          model.value.image_url = reader.result;
         };
        reader.readAsDataURL(file);
}

function addQuestion(index) {
   const newQuestion = {
     id: uuidv4(),
     type: "text",
     question: "", 
     description: null,
     data: {},
   };
   model.value.questions.splice(index, 0, newQuestion);
}

function deleteQuestion(question){
  model.value.questions = model.value.questions.filter((q) => q !== question);
}

function questionChange(question){
    model.value.questions = model.value.questions.map((q) => {
      if(q.id === question.id){
          return JSON.parse(JSON.stringify(question));
      }
      return q;
    })
}
/**
 * create or update survey
 */
function saveSurvey(){
   store.dispatch("saveSurvey", model.value).then(({data}) => {
      router.push({
         name: "SurveyView",
         params: {id: data.data.id},
      });
   });
}
</script>
<style lang=""></style>

<template>
   <PageContent>
      <template v-slot:header>
         <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
      </template>
      <div v-if="loading" class="flex justify-center">Loading...</div>
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 text-gray-700">
         <div class="bg-white shadow-md p-3 animate-fade-in-down text-center flex flex-col order-1 lg:order-2"
            style="animation-delay: 0.1s">
            <h3 class="text-2xl font-semibold">Total Surveys</h3>
            <div class="text-8xl font-semibold flex-1 flex items-center justify-center">{{ data.total_surveys }}</div>
         </div>

         <div class="bg-white shadow-md p-3 animate-fade-in-down text-center flex flex-col order-2 lg:order-4"
            style="animation-delay: 0.2s">
            <h3 class="text-2xl font-semibold">Total Answers</h3>
            <div class="text-8xl font-semibold flex-1 flex items-center justify-center">{{ data.total_answers }}</div>
         </div>

         <div class="row-span-2 order-3 animate-fade-in-down bg-white shadow-md p-4 lg:order-1">
            <h3 class="text-2xl font-semibold mb-2">Latest Survey</h3>
            <img :src="data.latest_survey.image_url" class="w-[240px] mx-auto" :alt="data.latest_survey.title" />
            <h3 class="text-xl font-bold mt-2 mb-3">{{ data.latest_survey.title }}</h3>

            <div class="flex justify-between text-sm mb-1">
               <div>Create Date:</div>
               <div>{{ data.latest_survey.created_at }}</div>
            </div>

            <div class="flex justify-between text-sm mb-1">
               <div>Expire Date:</div>
               <div>{{ data.latest_survey.expire_date }}</div>
            </div>

            <div class="flex justify-between text-sm mb-1">
               <div>Status:</div>
               <div>{{ data.latest_survey.status == 1 ? 'Active' : 'Draft' }}</div>
            </div>

            <div class="flex justify-between text-sm mb-1">
               <div>Questions:</div>
               <div>{{ data.latest_survey.questions }}</div>
            </div>

            <div class="flex justify-between text-sm mb-1">
               <div>Answers:</div>
               <div>{{ data.latest_survey.answers }}</div>
            </div>

            <div class="flex justify-between">
               <router-link :to="{ name: 'SurveyView', params: { id: data.latest_survey.id } }" class="flex py-2 px-4 border border-transparent text-sm rounded-md text-indigo-500 hover:bg-indigo-700
              hover:text-white transition-colors focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                     <path stroke-linecap="round" stroke-linejoin="round"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  </svg> Edit Survey
               </router-link>
               <button class="flex py-2 px-4 border border-transparent text-sm rounded-md text-indigo-500 hover:bg-indigo-700
              hover:text-white transition-colors focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                     <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                     <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                  View Answers
               </button>
            </div>
         </div>

         <div class="row-span-2 p-3 animate-fade-in-down order-4 bg-white shadow-md lg:order-3"
            style="animation-delay: 0.3s">
            <div
             class="flex justify-between items-center mb-3 px-2"
            >
             <h3 class="text-2xl font-semibold">Latest Answers</h3>
            <a
             href="javascript:void(0)"
             class="text-sm text-blue-500 hover:decoration-blue-500"
            >
               View All
            </a>
            </div>
            <a
            href="#"
            v-for="answer of data.latest_answers"
            :key="answer.id"
            class="block p-2 hover:bg-gray-100/90"
            >
            <div class="font-semibold">{{ answer.survey.title }}</div>
           <small>
            Answer Made at:
            <i class="font-semibold">{{ answer.end_date }}</i>
            </small>
           </a>
         </div>
      </div>
   </PageContent>
</template>

<script setup>
import PageContent from '../components/PageContent.vue';
import { computed } from 'vue';
import { useStore } from 'vuex';

const store = useStore();

const loading = computed(() => store.state.dashboard.loading);
const data = computed(() => store.state.dashboard.data);

store.dispatch('getDashboardData');

</script>

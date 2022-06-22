<template>
<PageContent>
   <template v-slot:header>
    <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
   </template>
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

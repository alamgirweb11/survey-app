import { createApp } from 'vue'
import store from './store'
import router from './router'
import './main.css'
import App from './App.vue'

createApp(App).use(router).use(store).mount('#app')

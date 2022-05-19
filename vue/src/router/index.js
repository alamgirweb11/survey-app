import { createRouter, createWebHistory } from "vue-router";
import DefaultLayout from '../components/DefaultLayout.vue'
import Dashboard from '../views/Dashboard.vue'
import Login from '../views/auth/Login.vue'
import Register from '../views/auth/Register.vue'
import Surveys from '../views/pages/Surveys.vue'

const routes = [
        {
               path: '/',
               redirect: '/dashboard',
               name: 'Dashboard',
               component: DefaultLayout,
               children:[
                       {path: '/dashboard', name: 'Dashboard', component: Dashboard},
                       {path: '/surveys', name: 'Surveys', component: Surveys}
               ]
        },
        {
               path: '/login',
               name: 'Login',
               component: Login,
        },
        {
               path: '/register',
               name: 'Register',
               component: Register
        },
]

const router = createRouter({
       history: createWebHistory(),
       routes
})

export default router
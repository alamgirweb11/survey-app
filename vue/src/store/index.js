import {createStore} from 'vuex'
import axiosClient from '../axios'
const store = createStore({
       state: {
          user: {
                  data: {},
                  token: sessionStorage.getItem("TOKEN")
          }
       },
       getters: {},

       mutations: {
        logout: (state) => {
                 state.user.data = {},
                 state.user.token = null
        },
         setUser: (state, userData) => {
                state.user.token = userData.token  
                state.user.data = userData.user
                sessionStorage.setItem('TOKEN', userData.token)  
         }
       },

       actions: {
               register({commit}, user){
                   return axiosClient.post('/register', user)
                   .then(({data}) => {
                            commit('setUser', data)
                            return data;
                   }) 
               }
       },
       
       modules: {},
})

export default store
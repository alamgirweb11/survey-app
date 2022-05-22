import {createStore} from 'vuex'
const baseUrl = `http://localhost:8000/api`;
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
                     return fetch(baseUrl+`/register`,{
                            headers:{
                                    "Content-Type":"application/json",
                                    Accept: "application/json"
                            },
                            method: "POST",
                            body: JSON.stringify(user),
                     }).then((res) => res.json())
                     .then((res) => {
                              commit("setUser", res)
                              return res
                     })     
               }
       },
       
       modules: {},
})

export default store
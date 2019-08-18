import Vue from 'vue'
import Vuex from 'vuex'

import data from './module-data'
import { axios } from 'boot/axios'
import { LocalStorage } from 'quasar'

Vue.use(Vuex)

/*
 * If not building with SSR mode, you can
 * directly export the Store instantiation
 */

export default function (/* { ssrContext } */) {
  const Store = new Vuex.Store({
    modules: {
      data
    },

    state: {
      auth: {
        token: LocalStorage.getItem('token'),
        role: '',
        name: '',
      },
      loading: false
    },

    getters: {
      getToken(state) {
        return state.auth.token
      },
      getName(state) {
        return state.auth.name
      },
      getRole(state) {
        return state.auth.role
      }      
    },

    mutations: {
      rename (state, {name}) {
        state.auth.name = name
      },
      applyAuth (state, payload) {
        console.log(payload)
        state.auth = {
          token: payload.token,
          role: payload.role,
          name: payload.name
        }
        LocalStorage.set('token', payload.token)
        this.$router.push({
          path: '/'
        })
      },      
      removeAuth (state) {
        state.auth = {
          token: '',
          role: '',
          name: ''
        }
        LocalStorage.remove('token')
        this.$router.push({
          path: '/login'
        })
      },      
      loadingStart (state) {
        state.loading = true
      },      
      loadingEnd (state) {
        state.loading = false
      },

    },

    actions: {
      // requestAuth ({commit}, payload) {
      //   commit('loadingStart')
      //   return new Promise((resolve, reject) => {
      //     setTimeout(() => {
      //       let data = {
      //         token: payload.username,
      //         role: payload.username,
      //         name: payload.username
      //       }
      //       commit('applyAuth', data)
      //       commit('loadingEnd')
      //       resolve(data)
      //     }, 2000)
      //   })
      // },
      // validateToken ({getters,commit}, relog) {
      //   console.log(relog, getters.getToken)
      //   commit('loadingStart')
      //   return new Promise((resolve, reject) => {
      //     setTimeout(() => {
      //       console.log(getters)
      //       if (getters.getToken != '') {
      //         let data = {
      //           token: getters.getToken,
      //           role: getters.getToken,
      //           name: getters.getToken,
      //         }
      //         if (relog) {
      //           commit('applyAuth', data)
      //         }
      //         commit('loadingEnd')
      //         resolve(data)
      //       } else {
      //         reject()
      //       }
      //     }, 2000)
      //   })
      // },
      requestAuth ({dispatch,commit}, payload) {
        // console.log(payload)
        commit('loadingStart')
        return new Promise((resolve, reject) => {
          axios.post('/auth/login', {
            username: payload.username,
            password: payload.password
          }).then((response) => {
            const data = response.data
            if (data.status == "success") {
              commit('applyAuth', {token: data.data.access_token})  
              dispatch('validateToken',true)
              resolve(data)
            } else {
              reject()
            }
          }).catch((error) => {
            console.log(error)
            reject()
          }).finally(() => {
            commit('loadingEnd')
          })
        })
      },
      validateToken ({getters,commit}, relog) {
        // console.log(relog, getters.getToken)
        commit('loadingStart')
        return new Promise((resolve, reject) => {
          axios.post('/auth/me', {
            token: getters.getToken,
          }).then((response) => {
            console.log(response)
            const data = response.data
            if (data.status == "success") {
              if (relog) {
                commit('applyAuth', {token: getters.getToken, role: data.data.role, name: data.data.name})
              }
              resolve(data)
            } else {
              reject()
            }
          }).catch((error) => {
            console.log(error)
            if (!relog) {
              commit('removeAuth')
            }
            reject()
          }).finally(() => {
            commit('loadingEnd')
          })
        })
      },
      revokeAuth ({getters,commit}) {
        commit('loadingStart')
        return new Promise((resolve, reject) => {
          axios.post('/api/auth/logout', {
            token: getters.getToken,
          }).finally(() => {
            commit('removeAuth')
            commit('loadingEnd')
            resolve()
          })
        })
      },
    },


    // enable strict mode (adds overhead!)
    // for dev mode only
    strict: process.env.DEV
  })

  return Store
}

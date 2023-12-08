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
      printout: {
        margin: {
          left: LocalStorage.getItem('pml') || '5px',
          top: LocalStorage.getItem('pmt') || '16px',
        },
        font: {
          one: LocalStorage.getItem('pf1') || "'Century Gothic', serif",
          two: LocalStorage.getItem('pf2') || "'Century Gothic', monospace",
          three: LocalStorage.getItem('pf3') || "'Century Gothic', serif",
        },
        store: {
          name: LocalStorage.getItem('psn') || 'UD. Ladang Roti',
          address: LocalStorage.getItem('psa') || 'Jl. Pulau Morotai 45, Denpasar, Bali',
          phone: LocalStorage.getItem('psp') || '+6282 237 810 111',
          email: LocalStorage.getItem('pse') || 'braud.artisanbakery@gmail.com',
          city: LocalStorage.getItem('psc') || 'Denpasar'
        },
        width: LocalStorage.getItem('pw') || "80%",
        rows: LocalStorage.getItem('pr') || 20,
        styles: LocalStorage.getItem('ps') || {
          alp: {
            font: "'Century Gothic', monospace",
            size: "12px",
            bold: "100",
          },
          num: {
            font: "'B612 Mono', monospace",
            size: "11px",
            bold: "100",
          },
        },
        notabene: LocalStorage.getItem('pn') || '',
        notabenei: LocalStorage.getItem('pn2') || '',
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
      },
      getPrintout(state) {
        return state.printout
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
      setPrintoutSettings(state, payload) {
        state.printout.margin.left = payload.margin.left
        LocalStorage.set('pml', payload.margin.left)
        state.printout.margin.top = payload.margin.top
        LocalStorage.set('pmt', payload.margin.top)
        state.printout.font.one = payload.font.one
        LocalStorage.set('pf1', payload.font.one)
        state.printout.font.two = payload.font.two
        LocalStorage.set('pf2', payload.font.two)
        state.printout.font.three = payload.font.three
        LocalStorage.set('pf3', payload.font.three)
        state.printout.width = payload.width
        LocalStorage.set('pw', payload.width)
        state.printout.rows = payload.rows
        LocalStorage.set('pr', payload.rows)

        state.printout.store.name = payload.store.name
        LocalStorage.set('psn', payload.store.name)
        state.printout.store.address = payload.store.address
        LocalStorage.set('psa', payload.store.address)
        state.printout.store.phone = payload.store.phone
        LocalStorage.set('psp', payload.store.phone)
        state.printout.store.email = payload.store.email
        LocalStorage.set('pse', payload.store.email)
        state.printout.store.city = payload.store.city
        LocalStorage.set('psc', payload.store.city)

        state.printout.styles = payload.styles
        LocalStorage.set('ps', payload.styles)

        state.printout.notabene = payload.notabene
        LocalStorage.set('pn', payload.notabene)
        state.printout.notabenei = payload.notabenei
        LocalStorage.set('pn2', payload.notabene)
      }

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

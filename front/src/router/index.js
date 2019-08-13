import Vue from 'vue'
import VueRouter from 'vue-router'

import routes from './routes'
// import store from '../store'

Vue.use(VueRouter)

/*
 * If not building with SSR mode, you can
 * directly export the Router instantiation
 */

export default function ({store}/* { store, ssrContext } */) {
  const Router = new VueRouter({
    scrollBehavior: () => ({ x: 0, y: 0 }),
    routes,

    // Leave these as is and change from quasar.conf.js instead!
    // quasar.conf.js -> build -> vueRouterMode
    // quasar.conf.js -> build -> publicPath
    mode: process.env.VUE_ROUTER_MODE,
    base: process.env.VUE_ROUTER_BASE
  })

  Router.beforeEach((to, from, next) => {
    let curRole = store.getters.getRole
    let token = store.getters.getToken
    let reqRole = to.meta.role
    if (curRole && curRole !== '') {
      store.dispatch("validateToken",false)
      if (curRole === 'superadmin') {
        next()
      } else if (Array.isArray(reqRole) && reqRole.includes(curRole)) {
        next()
      } else if (reqRole === true) {
        next()
      } else {
        next({
          path: '/',
        })
      }
    } else {  
      if (to.path === '/login') {
        store.dispatch("validateToken",true)
        next()
      } else {
        next({
          path: '/login',
        })
      }
    }
  })

  return Router
}

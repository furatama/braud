import axios from 'axios'

export default async ({ Vue }) => {
  Vue.prototype.$axios = axios
  // Vue.prototype.$axiosFetch 
}

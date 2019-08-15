import axios from 'axios'

const axiosInstance = axios.create({
  // baseURL: 'http://localhost/bdsm-laravel-boilerplate/public'
})

export default ({ Vue }) => {
  Vue.prototype.$axios = axiosInstance
}

export { axiosInstance as axios }
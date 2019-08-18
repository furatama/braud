import axios from 'axios'

const axiosInstance = axios.create({
  baseURL: '/api/'
})

export default ({ Vue }) => {
  Vue.prototype.$axios = axiosInstance
}

export { axiosInstance as axios }
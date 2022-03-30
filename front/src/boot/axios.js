import axios from 'axios'

const isLocalHost = window.location.hostname === 'localhost'

const axiosInstance = axios.create({
  baseURL: isLocalHost ? '/braud/public/api/' : '/braud/public/api/'
})

export default ({ Vue }) => {
  Vue.prototype.$axios = axiosInstance
}

export { axiosInstance as axios }

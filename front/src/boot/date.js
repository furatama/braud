import {date} from 'quasar'

// "async" is optional
export default async ({ Vue }) => {
  Vue.prototype.$date = date
}

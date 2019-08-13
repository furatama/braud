import { Notify } from 'quasar'

// "async" is optional
export default async ({ Vue }) => {
  let notify = (msg, color, icon) => {
    Notify.create({
      position: "top",
      color: color,
      icon: icon,
      message: msg,
      actions: [
        { label: 'OK', color: "white" }
      ]
    })
  }

  Vue.prototype.$notifyBase = notify
  Vue.prototype.$notify = (msg, color='negative') => {notify(msg,color,color == "negative" ? "close" : "check")}
  Vue.prototype.$notifyNegative = (msg) => {notify(msg,"negative","close")}
  Vue.prototype.$notifyPositive = (msg) => {notify(msg,"positive","check")}
}

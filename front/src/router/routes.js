
const routes = [
  {
    path: '/',
    component: () => import('layouts/MyLayout.vue'),
    children: [
      { path: '', component: () => import('pages/Index.vue'), meta: {role: true} },
      { path: 'home', component: () => import('pages/Index.vue'), meta: {role: true} },
      { path: 'settings', component: () => import('pages/Settings.vue'), meta: {role: true} },
      { path: 'kasir', component: () => import('pages/Kasir.vue'), meta: {role: ['admin','kasir']} },
      { path: 'order', component: () => import('pages/Order.vue'), meta: {role: ['admin','kasir']} },
      { path: 'invoice', component: () => import('pages/Invoice.vue'), meta: {role: ['admin','kasir']} },
      { path: 'resep', component: () => import('pages/Error404.vue'), meta: {role: ['admin','bakery']} },
      { path: 'report/customer', component: () => import('pages/ReportCustomer.vue'), meta: {role: ['admin','kasir']} },
      { path: 'report/produk', component: () => import('pages/ReportProduk.vue'), meta: {role: ['admin','kasir']} },
      { path: 'report/order', component: () => import('pages/ReportOrder.vue'), meta: {role: ['admin','kasir']} },
      { path: 'master/customer', component: () => import('pages/MasterCustomer.vue'), meta: {role: ['admin','kasir']} },
      { path: 'master/produk', component: () => import('pages/MasterProduk.vue'), meta: {role: ['admin','kasir','bakery']} },
      { path: 'master/bahan', component: () => import('pages/Error404.vue'), meta: {role: ['admin','bakery']} },
      { path: 'master/komposisi', component: () => import('pages/Error404.vue'), meta: {role: ['admin','bakery']} },
      { path: 'master/staff', component: () => import('pages/MasterStaff.vue'), meta: {role: ['admin']} },
    ]
  },
  {
    path: '/login',
    component: () => import('layouts/Login.vue')
  }
]

// Always leave this as last one
if (process.env.MODE !== 'ssr') {
  routes.push({
    path: '*',
    component: () => import('pages/Error404.vue')
  })
}

export default routes

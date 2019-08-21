<template>
  <q-page padding>
    <smart-table
      title="List Order"
      :columns="columns"
      :data="data"
      :doRequest="requestMethod"
    >

    </smart-table>
  </q-page>
</template>

<script>
import SmartTable from '../components/plugins/SmartTable'
const apiURL = '/order/data'

export default {
  // name: 'PageName',
  components: {
    'smart-table' : SmartTable
  },
  data() {
    return {
      columns: [
        { name: 'tanggal', label: 'Tanggal Order', type: 'date'},
        { name: 'no', label: 'No Order', type: 'string'},
        { name: 'customer', label: 'Customer', type: 'string'},
        { name: 'metode', label: 'Metode', type: 'enum', options: [
          {label: 'Tunai', value: 'cash'},{label: 'Kredit', value: 'credit'},
        ]},
        { name: 'total', label: 'Total', type: 'decimal'},
        { name: 'tunai', label: 'Terbayar', type: 'decimal'},
        { name: 'lunas', label: 'Lunas?', type: 'enum', options: [
          {label: 'Lunas', value: 1},{label: 'Belum', value: 0},
        ]},
        { name: 'edit', label: 'Detail Order', type: 'dialog', component: () => import('../components/OrderEdit.vue') },
      ],
      data: []
    }
  },
  methods: {
    requestMethod({pagination,colFilter}) {
      let { page, rowsPerPage, rowsNumber, sortBy, descending } = pagination
      return new Promise((resolve, reject) => {
        this.$store.dispatch("fetchAll",
          {url: apiURL, rowsPerPage, page, sortBy, descending, colFilter}
        ).then((response) => {
          let data = response.data
          this.data = data.data
          resolve(data)
        }).catch((error) => {
          this.$notifyNegative('Ada Sebuah Kesalahan')
          console.log(error)
          reject(error)
        })
      })
    },
    // onRequest (props) {
    //   let { page, rowsPerPage, rowsNumber, sortBy, descending } = props.pagination
    //   let colFilter = {} 
    //   let cols = props.cols || this.cols)
    //   cols.forEach((el) => {
    //     if (el.type && el.type != 'dialog')
    //       colFilter[el.name] = el.filter
    //   })

    //   this.$store.dispatch("fetchAll",{url: apiURL, rowsPerPage, page, sortBy, descending, colFilter})
    //     .then((data) => {
    //       let tabledata = data.data
    //       this.data = tabledata.data
    //       this.nomor = tabledata.from
    //       props.pagination.page = tabledata.current_page
    //       props.pagination.rowsPerPage = tabledata.per_page
    //       props.pagination.rowsNumber = tabledata.total          
    //     }).catch((error) => {
    //       console.log(error)
    //       this.$notifyNegative('Ada Sebuah Kesalahan')
    //     })
    // },
  },
}
</script>

<style>
</style>

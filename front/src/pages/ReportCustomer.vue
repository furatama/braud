<template>
  <q-page padding>
    <report-table 
      title="Report Customer"
      :columns="columns"
      :resourceURL="link"
      :addFilters="filters"
    />
  </q-page>
</template>

<script>
import ReportTable from '../components/plugins/ReportTable'

export default {
  components: {
    'report-table' : ReportTable
  },
  data() {
    return {
      link: 'report/customer',
      columns: [
        { name: 'date', label: 'Tanggal', type: 'date'},
        { name: 'nama', label: 'Customer', type: 'string'},
        { name: 'qty', label: 'Jumlah Produk', type: 'integer'},
        { name: 'nilai', label: 'Nilai Order', type: 'decimal'},
        { name: 'terbayar', label: 'Terbayar', type: 'decimal'}
      ],
      filters: [
        { name: 'customer', label: 'Customer', type: 'select', options: []},
      ]
    }
  },
  mounted() {
    // setTimeout(() => { 
      this.$store.dispatch("fetchOptions",{url: '/customer/aktif'})
        .then((response) => {
          let data = response.data
          this.filters[0].options = data.map((v) => {
            return {
              label: v.nama,
              value: v.id
            }
          })
        }).catch((error) => {
          console.log(error)
        })
    // }, 100);
  }
}
</script>

<style>
</style>

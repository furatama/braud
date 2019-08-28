<template>
  <q-page padding>
    <report-table 
      title="Report Produk"
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
      link: 'report/produk',
      columns: [
        { name: 'date', label: 'Tanggal', type: 'date'},
        { name: 'nama', label: 'Produk', type: 'string'},
        { name: 'qty', label: 'Qty', type: 'integer'},
        { name: 'total', label: 'Nominal', type: 'decimal'},
        { name: 'net', label: 'Netto (-disc)', type: 'decimal'},
      ],
      filters: [
        { name: 'produk', label: 'Produk', type: 'select', options: []},
      ]
    }
  },
  mounted() {
    // setTimeout(() => { 
      this.$store.dispatch("fetchOptions",{url: '/produk/aktif'})
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

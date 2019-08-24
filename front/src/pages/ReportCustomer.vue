<template>
  <q-page padding>
    <report-table 
      title="Report Customer"
      :columns="columns"
      :resourceURL="link"
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
    }
  },
}
</script>

<style>
</style>

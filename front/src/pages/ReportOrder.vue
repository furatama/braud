<template>
  <q-page padding>
    <report-table 
      title="Report Order"
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
      link: 'report/order',
      columns: [
        { name: 'date', label: 'Tanggal', type: 'date'},
        { name: 'norder', label: 'Jumlah Order', type: 'integer'},
        { name: 'nproduk', label: 'Jumlah Produk', type: 'integer'},
        { name: 'total', label: 'Nominal', type: 'decimal'},
        { name: 'torder', label: 'N Cash', type: 'integer'},
        { name: 'korder', label: 'N Credit', type: 'integer'},
      ],
    }
  },
}
</script>

<style>
</style>

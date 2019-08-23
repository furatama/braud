<template>
  <q-page padding>
    <report-table 
      title="Report Produk"
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
      link: 'report/produk',
      columns: [
        { name: 'date', label: 'Tanggal', type: 'date'},
        { name: 'nama', label: 'Produk', type: 'string'},
        { name: 'qty', label: 'Qty', type: 'integer'},
        { name: 'total', label: 'Nominal', type: 'decimal'},
        { name: 'net', label: 'Netto (-disc)', type: 'decimal'},
      ],
    }
  },
}
</script>

<style>
</style>

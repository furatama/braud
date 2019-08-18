<template>
  <!-- Master Customer -->
  <m-table  
    title="Master Satuan"
    :columns="columns"
    :resourceURL="resourceURL"
    :inputs="inputs"
  >
  </m-table>
</template>

<script>
import MasterTable from './plugins/MasterTable'

export default {
  name: 'MasterSatuan',
  components: {
    'm-table' : MasterTable
  },
  data() {
    return {
      resourceURL: "/api/satuan/data",
      columns: [
        { name: 'nama', label: 'Nama', type: 'string'},
        { name: 'keterangan', label: 'Keterangan', type: 'string' },
      ],
      inputs: [
        { name: 'nama', label: 'Nama', type: 'text', required: true},
        { name: 'keterangan', label: 'Keterangan', type: 'text'},
      ]
    }
  }
}
</script>

<style>
</style>

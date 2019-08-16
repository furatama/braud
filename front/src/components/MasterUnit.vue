<template>
  <!-- Master Customer -->
  <m-table  
    title="Master Unit"
    :columns="columns"
    :resourceURL="resourceURL"
    :inputs="inputs"
  >
  </m-table>
</template>

<script>
import MasterTable from './plugins/MasterTable'

export default {
  name: 'MasterCustomer',
  components: {
    'm-table' : MasterTable
  },
  data() {
    return {
      resourceURL: "/api/unit",
      columns: [
        { name: 'nama', type: 'string'},
        { name: 'keterangan', type: 'string' },
      ],
      inputs: [
        { name: 'nama', type: 'text', required: true},
        { name: 'keterangan', type: 'text', required: true},
      ]
    }
  }
}
</script>

<style>
</style>

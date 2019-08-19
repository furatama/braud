<template>
  <!-- Master Customer -->
  <m-table  
    title="Master Kategori"
    :columns="columns"
    :resourceURL="resourceURL"
    :inputs="inputs"
  >
  </m-table>
</template>

<script>
import MasterTable from './plugins/MasterTable'

export default {
  name: 'MasterCategory',
  components: {
    'm-table' : MasterTable
  },
  data() {
    return {
      resourceURL: "/kategori/data",
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

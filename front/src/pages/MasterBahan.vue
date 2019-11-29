<template>
  <q-page padding>
    <m-table  
      title="Master Bahan"
      :columns="columns"
      :resourceURL="resourceURL"
      :inputs="inputs"
    >
    </m-table>
  </q-page>
</template>

<script>
import MasterTable from '../components/plugins/MasterTable'

export default {
  name: 'MasterBahan',
  components: {
    'm-table' : MasterTable
  },
  data() {
    return {
      resourceURL: "/bahan/data",
      columns: [
        { name: 'nama', label: 'Nama', type: 'string'},
        { name: 'harga', label: 'Harga/Kg', type: 'decimal'},
        { name: 'jenis', label: 'Jenis', type: 'string'},
      ],
      inputs: [
        { name: 'nama', label: 'Nama', type: 'text'},
        { name: 'harga', label: 'Harga/Kg', type: 'decimal'},
        { name: 'jenis', label: 'Jenis', type: 'text'},
      ]
    }
  }
}
</script>

<style>
</style>

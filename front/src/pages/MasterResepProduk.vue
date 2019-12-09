<template>
  <!-- Master Customer -->
  <q-page padding>
    <m-table  
      title="Master Resep"
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
  name: 'MasterResepProduk',
  components: {
    'm-table' : MasterTable
  },
  data() {
    return {
      resourceURL: "/resep-produk/data",
      columns: [
        { name: 'nama', label: 'Nama', type: 'string'},
        { name: 'jenis', label: 'Jenis', type: 'string' },
        { name: 'berat', label: 'Berat 1 Resep (gram)', type: 'integer' },
      ],
      inputs: [
        { name: 'nama', label: 'Nama*', type: 'text', required: true},
        { name: 'berat', label: 'Berat 1 Resep (gram)*', type: 'number'},
        { name: 'jenis', label: 'Jenis', type: 'text'},
      ]
    }
  }
}
</script>

<style>
</style>

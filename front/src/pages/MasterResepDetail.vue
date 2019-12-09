<template>
  <q-page padding>
  <!-- Master Customer -->
    <m-table  
      title="Master Resep Detail"
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
  name: 'MasterResepDetail',
  components: {
    'm-table' : MasterTable
  },
  data() {
    return {
      resourceURL: "/resep-detail/data",
      columns: [
        { name: 'resep', label: 'Resep', type: 'string' },
        { name: 'berat', label: 'Berat', type: 'string'},
        { name: 'ratio', label: 'Ratio', type: 'decimal' },
        { name: 'produk', label: 'Produk', type: 'string' },
      ],
      inputs: [
        { name: 'id_resep', label: 'Resep*', type: 'resource2', resource: {
          url: '/resep-produk/data', component: () => import('./MasterResepProduk.vue'), label: 'nama',
        }},
        { name: 'berat', label: 'Berat*', type: 'text', required: true},
        { name: 'ratio', label: 'Ratio', type: 'decimal'},
        { name: 'id_produk', label: 'Produk', type: 'resource2', resource: {
          url: '/produk/aktif', component: () => import('./MasterProduk.vue'), label: 'nama',
        }},
      ]
    }
  }
}
</script>

<style>
</style>

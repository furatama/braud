<template>
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
  name: 'MasterResep',
  components: {
    'm-table' : MasterTable
  },
  data() {
    return {
      resourceURL: "/resep/data",
      bahan: [],
      columns: [
        { name: 'produk', label: 'Produk', type: 'string'},
        { name: 'ratio', label: 'Ratio', type: 'decimal'},
        { name: 'komposisi', label: 'Komposisi', type: 'string'},
      ],
      inputs: [
        { name: 'produk', label: 'Produk', type: 'text'},
        { name: 'ratio', label: 'Ratio', type: 'number'},
        { name: 'komposisi', label: 'Komposisi', type: 'detail', default: [{}], detail: []},
      ]
    }
  },
  async mounted() {
    let response = await this.$store.dispatch("fetchOptions",{url: '/bahan/data'})
    const data = response.data
    this.bahan = data.map((v) => {
      return {
        label: v.nama,
        value: v.id
      }
    })

    
    this.inputs = [
        { name: 'id_produk', label: 'Produk', type: 'resource2', resource: {
          url: '/produk/data', component: () => import('./MasterProduk.vue'), label: 'nama',
        }},
        { name: 'ratio', label: 'Ratio', type: 'decimal'},
        { name: 'komposisi', label: 'Komposisi', type: 'detail', default: [{}], detail: [
          {name: 'id_bahan', label: 'Bahan', type: 'select', options: this.bahan},
          {name: 'batch', label: 'Kg-Batch/Ratio', type: 'decimal'}
        ]},
    ]
  }
}
</script>

<style>
</style>

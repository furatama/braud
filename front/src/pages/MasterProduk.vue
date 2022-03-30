<template>
  <q-page padding>
    <m-table
      title="Master Produk"
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
  name: 'MasterProduk',
  components: {
    'm-table' : MasterTable
  },
  data() {
    return {
      resourceURL: "/produk/data",
      columns: [
        { name: 'kode', label: 'Kode', type: 'string'},
        { name: 'nama', label: 'Nama', type: 'string'},
        { name: 'harga_global', label: 'Harga Global', type: 'decimal'},
        { name: 'kategori', label: 'Kategori', type: 'string'},
        { name: 'satuan', label: 'Satuan', type: 'string' },
        { name: 'aktif', label: 'Aktif?', type: 'boolean' },
      ],
      inputs: [
        { name: 'kode', label: 'Kode', type: 'text', required: true},
        { name: 'nama', label: 'Nama', type: 'text', required: true},
        { name: 'id_kategori', label: 'Kategori', type: 'resource', resource: {
          url: '/kategori/data', component: () => import('../components/MasterCategory.vue'), label: 'nama',
        }},
        { name: 'id_satuan', label: 'Satuan', type: 'resource', resource: {
          url: '/satuan/data', component: () => import('../components/MasterUnit.vue'), label: 'nama',
        }},
        { name: 'keterangan', label: 'Keterangan', type: 'textarea'},
        { name: 'aktif', label: 'Aktif?', type: 'toggle', default: 1 },
        { name: 'harga_global', label: 'Harga Global', type: 'decimal'},
      ]
    }
  }
}
</script>

<style>
</style>

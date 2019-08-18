<template>
  <q-page padding>
    <m-table  
      title="Master Customer"
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
  name: 'MasterCustomer',
  components: {
    'm-table' : MasterTable
  },
  data() {
    return {
      resourceURL: "/customer/data",
      columns: [
        { name: 'nama', label: 'Nama', type: 'string'},
        { name: 'alamat', label: 'Alamat', type: 'string'},
        { name: 'telepon', label: 'Telepon', type: 'string'},
        { name: 'email', label: 'Email', type: 'string'},
        { name: 'aktif', label: 'Aktif?', type: 'boolean' },
        { name: 'harga', label: 'Harga', type: 'dialog', component: () => import('../components/MasterHarga.vue') },
      ],
      inputs: [
        { name: 'nama', label: 'Nama', type: 'text'},
        { name: 'alamat', label: 'Alamat', type: 'text'},
        { name: 'telepon', label: 'Telepon', type: 'text'},
        { name: 'email', label: 'Email', type: 'text'},
        { name: 'keterangan', label: 'Keterangan', type: 'textarea'},
        { name: 'aktif', label: 'Aktif?', type: 'toggle', default: 1 },
      ]
    }
  }
}
</script>

<style>
</style>

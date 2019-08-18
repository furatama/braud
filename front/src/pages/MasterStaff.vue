<template>
  <q-page padding>
    <m-table  
      title="Master Staff"
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
  name: 'MasterStaff',
  components: {
    'm-table' : MasterTable
  },
  data() {
    return {
      resourceURL: "/user/data",
      columns: [
        { name: 'username', label: 'Username', type: 'string'},
        { name: 'name', label: 'Nama', type: 'string'},
        { name: 'role', label: 'Sebagai', type: 'string'},
        { name: 'isactive', label: 'Aktif?', type: 'boolean' },
        { name: 'isconfirmed', label: 'Terkonfirmasi?', type: 'boolean' },
      ],
      inputs: [
        { name: 'username', label: 'Username', type: 'text', required: true},
        { name: 'password', label: 'Password', type: 'password', required: true},
        { name: 'name', label: 'Nama', type: 'text', required: true},
        { name: 'role', label: 'Hak Akses', type: 'select', options: [
          {label: 'Admin', value: 'admin'},
          {label: 'Kasir', value: 'kasir'},
          {label: 'Bakery', value: 'bakery'},
        ]},
        { name: 'isactive', label: 'Aktif?', type: 'toggle', default: 1 },
        { name: 'isconfirmed', label: 'Terkonfirmasi?', type: 'toggle', default: 1 },
      ]
    }
  }
}
</script>

<style>
</style>

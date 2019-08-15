<template>
  <q-page padding>
    <!-- Master Customer -->
    <m-table  
      title="Master Test"
      :columns="columns"
      :fetchURL="fetchURL"
      :inputs="inputs"
    >
    </m-table>
  </q-page>
</template>

<script>
import MasterTable from '../components/MasterTable'

export default {
  name: 'MasterCustomer',
  components: {
    'm-table' : MasterTable
  },
  data() {
    return {
      fetchURL: "/api/test",
      columns: [
        { name: 'int', type: 'integer'},
        { name: 'str', type: 'string'},
        { name: 'bool', type: 'boolean' },
        { name: 'date', type: 'date'},
      ],
      inputs: [
        { name: 'int', type: 'number', required: true},
        { name: 'str', type: 'text', required: true},
        { name: 'bool', type: 'toggle', required: true, default: 1 },
        { name: 'date', type: 'date', required: true},
      ]
    }
  }
}
</script>

<style>
</style>

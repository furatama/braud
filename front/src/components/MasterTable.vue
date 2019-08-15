<template>
  <div>
    <q-table
      :title="title"
      :data="data"
      :columns="cols"
      row-key="id"
      :pagination.sync="pagination"
      :loading="loading"
      :filter="filter"
      @request="onRequest"
      binary-state-sort
      :rows-per-page-options="rpp"
      class="bg-secondary text-anti-primary"
      table-class="no-scroll bg-accent text-primary"
    >
      <template v-slot:top-right>
        <div class="row justify-between">
          <slot name="header"></slot>
          <q-input dark dense debounce="300" v-model="filter" placeholder="Search">
            <template v-slot:append>
              <q-icon name="search" />
            </template>
          </q-input>
        </div>
      </template>
      <template v-slot:body="props">
        <q-tr :props="props">
          <q-td v-for="col in props.cols" :key="col.name" :props="props">
            <template v-if="col.name == 'action'">
              <q-btn icon="edit" color="warning" flat dense @click="onEdit(props.row)"/>
              <q-btn icon="delete" color="negative" flat dense>
                <q-popup-proxy :breakpoint="600">
                  <q-banner inline-actions class="bg-negative text-white">
                    Yakin?
                    <template v-slot:action>
                      <q-btn flat label="Ya" v-close-popup @click="onDelete(props.row)"/>
                    </template>
                  </q-banner>
                </q-popup-proxy>
              </q-btn>
            </template>
            <template v-else-if="col.name == 'no'">
              <q-avatar size="20px" class="q-ma-none q-pa-none text-anti-primary" color="primary">{{props.row.__index+nomor}}</q-avatar>
            </template>
            <template v-else-if="col.type && col.type == 'boolean'">
              <q-avatar v-if="col.value" size="24px" class="text-white" color="positive" icon="check"></q-avatar>
              <q-avatar v-else size="24px" class="text-white" color="negative" icon="close"></q-avatar>
            </template>
            <template v-else>
              {{col.value}}
            </template>
          </q-td>
        </q-tr>
      </template>
    </q-table>

    <!--  -->

    <q-page-sticky position="top" :offset="[0, 5]">
      <q-btn dense class="q-px-sm" rounded icon="add" label="data" color="positive" @click="formDialog = true" />
    </q-page-sticky>
    
    <q-dialog v-model="formDialog">
      <q-card>
        <q-bar class="bg-primary text-white">
          <div class="text-h6">{{editId === null ? 'Tambah' : 'Ubah'}} {{title}}</div>
          <q-space />
          <q-btn dense flat icon="close" v-close-popup>
            <q-tooltip>Close</q-tooltip>
          </q-btn>
        </q-bar>
        <q-card-section>
          <div v-for="(input,index) in inps" :key="index">
            <template v-if="input.type == 'text' || !input.type">
              <q-input :label="input.label" v-model="input.value" />
            </template>
            <template v-else-if="input.type == 'number'">
              <q-input type="number" :label="input.label" v-model.number="input.value" />
            </template>
            <template v-else-if="input.type == 'toggle'">
              <q-toggle checked-icon="check" unchecked-icon="clear" :true-value="1" :false-value="0" v-model="input.value" color="primary" :label="input.label"/>
            </template>
            <template v-else-if="input.type == 'date'">
              <q-input :label="input.label" v-model="input.value" mask="####/##/##" placeholder="YYYY/MM/DD">
                <template v-slot:append>
                  <q-icon name="event" class="cursor-pointer">
                    <q-popup-proxy>
                      <q-date v-model="input.value" />
                    </q-popup-proxy>
                  </q-icon>
                </template>
              </q-input>
            </template>
          </div>
        </q-card-section>
        <q-card-actions>
          <div class="row justify-end q-my-xs">
            <q-btn :loading="loading" :label="!editId ? 'Tambah Data' : 'Ubah Data'" color="positive" @click="onSubmit"/>
          </div>
        </q-card-actions>
      </q-card>
    </q-dialog>

  </div>
</template>

<script>
export default {
  name: 'MasterTable',
  props: {
    title: String,
    columns: Array,
    visibleColumns: Array,
    resource: String,
    fetchURL: String,
    inputs: Array
  },
  data () {
    return {
      filter: '',
      rpp: [3,7,15,50],
      pagination: {
        sortBy: 'name',
        descending: false,
        page: 1,
        rowsPerPage: 7,
        rowsNumber: 10,
      },
      data: [],
      cols: [],
      inps: [],
      nomor: 1,
      editId: null,
      formDialog: false,
    }
  },
  computed: {
    loading: {
      get() { 
        console.log('loading:',this.$store.state.loading )
        return this.$store.state.loading 
      },
      set(value) {
        if (value) { 
          this.$store.commit("loadingStart") 
        } else { 
          this.$store.commit("loadingEnd") 
        }
      }
    },
  },
  methods: {
    fetchFromServer(filter,paginate,page,sortBy,descending) {
      this.loading = true
      return new Promise((resolve, reject) => {
        this.$axios.get(this.fetchURL, {
          headers: {
            'Authorization' : 'Bearer ' + this.$store.getters.getToken,
          },
          params: {
            filter: filter,
            paginate: paginate, 
            page: page,
            sortBy: sortBy,
            descending: descending
          }
        }).then((response) => {
          const body = response.data
          console.log('body:',body)
          if (body.status === "success") {
            resolve(body.data)
          } else {
            reject(response)
          }
        }).catch((error) => {
          reject(error)
        }).finally(() => {
          this.loading = false
        })
      })
    },
    deleteFromServer(id) {
      this.loading = true
      return new Promise((resolve, reject) => {
        this.$axios.delete(this.fetchURL + `/${id}`, {
          headers: {
            'Authorization' : 'Bearer ' + this.$store.getters.getToken,
          }
        }).then((response) => {
          const body = response.data
          if (body.status === "success") {
            resolve(body.data)
          } else {
            reject(response)
          }
        }).catch((error) => {
          reject(error)
        }).finally(() => {
          this.loading = false
        })
      })
    },
    postToServer(url,inputs) {
      this.loading = true
      return new Promise((resolve, reject) => {
        this.$axios.post(url, {
          ...inputs,
          token: this.$store.getters.getToken
        }).then((response) => {
          const body = response.data
          if (body.status === "success") {
            resolve(body.data)
          } else {
            reject(response)
          }
        }).catch((error) => {
          reject(error)
        }).finally(() => {
          this.loading = false
        })
      })
    },
    onRequest (props) {
      let { page, rowsPerPage, rowsNumber, sortBy, descending } = props.pagination
      let filter = props.filter

      this.fetchFromServer(filter, rowsPerPage, page, sortBy, descending)
        .then((data) => {
          let tabledata = data.data
          this.data = tabledata.data
          this.nomor = tabledata.from
          this.pagination.page = tabledata.current_page
          this.pagination.rowsPerPage = tabledata.per_page
          this.pagination.rowsNumber = tabledata.total          
        }).catch((error) => {
          console.log(error)
          this.$notifyNegative('Ada Sebuah Kesalahan')
        })
    },
    onDelete(row) {
      this.deleteFromServer(row.id).then((data) => {
        this.$notifyPositive('Data Berhasil Dihapus')
      }).catch((error) => {
        console.log(error)
        this.$notifyNegative('Ada Sebuah Kesalahan')
      }).finally(() => {
        this.onRequest({
          pagination: this.pagination,
          filter: this.filter
        })
      })
    },
    onSubmit() {
      let url = this.editId ? this.fetchURL + `/${this.id}` : this.fetchURL
      let inputs = {}
      this.inps.forEach((input) => {
        inputs[input.name] = input.value
      })
      console.log(inputs)
      this.postToServer(url,inputs).then((data) => {
        if (!this.editId)
          this.$notifyPositive('Data Berhasil Dimasukkan')
        else
          this.$notifyPositive('Data Berhasil Diubah')
      }).catch((error) => {
        console.log(error)
        this.$notifyNegative('Ada Sebuah Kesalahan')
      })
    }
  },
  created() {
    this.cols = this.columns.map((col) => {
      return {
        ...col,
        align: col.align ? col.align : (col.type == 'integer' || col.type == 'decimal' || col.type == 'date' ? 'right' : 'left'),
        label: col.label ? col.label : col.name.toUpperCase(),
        field: col.field ? col.field : col.name,
        format: (val, row) => {
          if (col.type === 'integer' || col.type === 'decimal') {
            return this.$numeralCurrency(val)
          } else if (col.type === 'date') {
            return this.$date.formatDate(val, 'DD/MMMM/YYYY')
          }
          return val
        }
      }
    })
    this.cols = [
      {name: 'no', label: '', style: 'width:25px', sortable: false},
      ...this.cols,
      {name: 'action', label: '', sortable: false}
    ]

    this.inps = this.inputs.map((inp) => {
      return {
        ...inp,
        label: inp.label ? inp.label : inp.name.toUpperCase(),
        value: inp.default ? inp.default : ''
      }
    })
  },
  mounted () {
    // get initial data from server (1st page)
    this.onRequest({
      pagination: this.pagination,
      filter: undefined
    })
  },
}
</script>

<style lang="stylus">
.text-grey-8 .material-icons
  color: white !important
</style>

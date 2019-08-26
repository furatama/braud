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
      :hide-header="!loading"
    >
      <template v-slot:top-right>
        <div class="row q-col-gutter-md">
          <slot name="header"></slot>
          <q-input dark outlined dense debounce="300" v-model="filter" placeholder="Search">
            <template v-slot:append>
              <q-icon name="search" />
            </template>
          </q-input>
        </div>
      </template>
      <template v-slot:top-row="props">
        <q-tr v-if="!loading" :props="props">
          <q-td v-for="(col,index) in props.cols" :key="index">
            <template v-if="col.type == 'string'">
              <q-input debounce="300" v-model="col.filter" style="height:12px" input-style="padding:0px;font-size:12px" dense :placeholder="`${col.label}`"/>
            </template>
            <template v-else-if="col.type == 'boolean'">
              <q-toggle checked-icon="check" unchecked-icon="clear" :true-value="1" :false-value="0" v-model="col.filter" color="primary" dense style="margin-left:-15px"/>
            </template>
          </q-td>
        </q-tr>
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
            <template v-else-if="col.type == 'boolean'">
              <q-avatar v-if="col.value" size="24px" class="text-white" color="positive" icon="check"></q-avatar>
              <q-avatar v-else size="24px" class="text-white" color="negative" icon="close"></q-avatar>
            </template>
            <template v-else-if="col.type == 'dialog'">
              <q-btn color="primary" :label="col.label" dense @click="showComponent(col,props.row)"/>
            </template>
            <template v-else>
              {{col.value}}
            </template>
          </q-td>
        </q-tr>
      </template>
    </q-table>

    <q-btn dense class="q-px-sm top-btn" rounded icon="add" label="data" color="positive" @click="onNew()" />

    <!--  -->    
    
    <q-dialog v-model="formDialog">
      <q-card style="min-width:40vw">
        <q-bar class="bg-primary text-white">
          <div class="text-h6">{{editID === null ? 'Tambah' : 'Ubah'}} {{title}}</div>
          <q-space />
          <q-btn dense flat icon="close" v-close-popup>
            <q-tooltip>Close</q-tooltip>
          </q-btn>
        </q-bar>
        <form @submit.prevent="onSubmit">
        <q-card-section>
          <div v-for="(input,index) in inps" :key="index">
            <template v-if="input.type == 'text' || !input.type">
              <q-input :label="input.label" v-model="input.value" />
            </template>
            <template v-else-if="input.type == 'textarea'">
              <q-input autogrow :label="input.label" v-model="input.value" />
            </template>
            <template v-else-if="input.type == 'password'">
              <q-input type="password" :label="input.label" v-model="input.value" />
            </template>
            <template v-else-if="input.type == 'number'">
              <q-input type="number" :label="input.label" v-model.number="input.value" />
            </template>
            <template v-else-if="input.type == 'toggle'">
              <q-toggle class="q-my-sm" checked-icon="check" unchecked-icon="clear" :true-value="1" :false-value="0" v-model="input.value" color="primary" :label="input.label"/>
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
            <template v-else-if="input.type == 'select'">
              <q-select v-model="input.value" :options="input.options" map-options :label="input.label">
              </q-select>
            </template>
            <template v-else-if="input.type == 'resource'">
              <q-select v-model="input.value" :options="input.options" map-options :label="input.label">
                <template v-if="input.resource.component" v-slot:append>
                  <q-btn round dense flat icon="add" @click="showResource(input,index)" />
                </template>
              </q-select>
            </template>
          </div>
        </q-card-section>
        <q-card-actions>
          <div class="row justify-end q-my-xs full-width">
            <q-btn type="submit" :loading="loading" :label="!editID ? 'Tambah Data' : 'Ubah Data'" color="positive"/>
          </div>
        </q-card-actions>
        </form>
      </q-card>
    </q-dialog>

    <!--  -->

    <q-dialog v-model="resourceDialog" @hide="refreshResource">
      <q-card style="min-width:75vw">
        <q-bar class="bg-primary text-white">
          <div class="text-h6">{{resource.title}}</div>

          <q-space />

          <q-btn dense flat icon="close" v-close-popup>
            <q-tooltip>Close</q-tooltip>
          </q-btn>
        </q-bar>
        
        <q-card-section class="q-pa-none q-ma-none">
          <div class="scroll q-pa-md" style="height:85vh">
            <component :is="resource.component"></component>
          </div>
        </q-card-section>
      </q-card>
    </q-dialog>

    <!--  -->

    <q-dialog v-model="componentDialog">
      <q-card style="width:80vw;max-width:100vw">
        <q-bar class="bg-primary text-white">
          <div class="text-h6">{{component.title}}</div>

          <q-space />

          <q-btn dense flat icon="close" v-close-popup>
            <q-tooltip>Close</q-tooltip>
          </q-btn>
        </q-bar>
        
        <q-card-section class="q-pa-none q-ma-none">
          <q-scroll-area style="height:85vh" class="q-px-md">
            <component class="q-pa-md" :data="component.data" :is="component.component"></component>
          </q-scroll-area>
        </q-card-section>
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
    resourceURL: String,
    inputs: Array
  },
  data () {
    return {
      filter: '',
      rpp: [5,7,9,15,50],
      pagination: {
        page: 1,
        rowsPerPage: 9,
        rowsNumber: 10,
      },
      data: [],
      cols: [],
      inps: [],
      nomor: 1,
      editID: null,
      formDialog: false,
      defInps: [],
      resourceDialog: false,
      resource: {},
      componentDialog: false,
      component: {},
      singleFilter: {}
    }
  },
  computed: {
    loading: {
      get() { 
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
    colFilters() {
      return this.cols
    }
  },
  watch: {
    colFilters: {
      handler(val){
        this.rerequest()
      },
      deep: true
    }
  },
  methods: {
    onRequest (props) {
      let { page, rowsPerPage, rowsNumber, sortBy, descending } = props.pagination
      let filter = props.filter
      let colFilter = {} 
      let cols = props.cols || this.cols
      cols.forEach((el) => {
        if (el.type && el.type != 'dialog')
          colFilter[el.name] = el.filter
      })

      console.log(colFilter)

      this.$store.dispatch("fetchAll",{url: this.resourceURL, filter, rowsPerPage, page, sortBy, descending, colFilter})
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
    rerequest() {
      this.onRequest({
        pagination: this.pagination,
        filter: this.filter,
        cols: this.cols
      })
    },
    onDelete(row) {
      this.$store.dispatch("deleteSingle",{url: this.resourceURL, id: row.id})
      .then((data) => {
        this.$notifyPositive('Data Berhasil Dihapus')
      }).catch((error) => {
        console.log(error)
        this.$notifyNegative('Ada Sebuah Kesalahan')
      }).finally(() => {
        this.rerequest()
      })
    },
    onEdit(row) {
      this.editID = row.id
      this.$store.dispatch("fetchSingle",{url: this.resourceURL, id: row.id}).then((data) => {
        this.inps = this.inps.map((input) => {
          return {
            ...input,
            value: data.data[input.name]
          }
        })
        this.formDialog = true
      }).catch((error) => {
        console.log(error)
        this.$notifyNegative('Ada Sebuah Kesalahan')
      })
    },
    onNew() {
      this.editID = null
      this.resetForm()
      this.formDialog = true
    },
    onSubmit() {
      let inputs = {}
      this.inps.forEach((input) => {
        if (input.value != null && input.value != undefined) {
          inputs[input.name] = input.value
          if (input.value.value && input.type == 'resource' || input.type == 'select')
            inputs[input.name] = input.value.value
        }
      })
      let afterSubmit = () => {
        this.resetForm()
        this.formDialog = false
        setTimeout(() => {
          this.rerequest()
        }, 100)
      }
      if (this.editID) {
        this.$store.dispatch("updateSingle",{url: this.resourceURL, id: this.editID, inputs})
        .then((data) => {
          this.$notifyPositive('Data Berhasil Diubah')
        }).catch((error) => {
          console.log(error)
          this.$notifyNegative('Ada Sebuah Kesalahan')
        }).finally(afterSubmit())
      } else {
        this.$store.dispatch("postSingle",{url: this.resourceURL, inputs})
        .then((data) => {
          this.$notifyPositive('Data Berhasil Dimasukkan')
        }).catch((error) => {
          console.log(error)
          this.$notifyNegative('Ada Sebuah Kesalahan')
        }).finally(afterSubmit())
      }
    },
    showResource(input,index) {
      this.resource = {
        title: input.label,
        component: input.resource.component,
        index: index
      }
      this.resourceDialog = true
    },
    showComponent(col,row) {
      this.component = {
        title: col.label,
        component: col.component,
        data: row
      }
      console.log(this.component)
      this.componentDialog = true
    },
    fillResource(resource,index) {
      this.$store.dispatch("fetchOptions",{url: resource.url})
        .then((response) => {
          let data = response.data
          let options = data.map((v) => {
            return {
              label: v[resource.label],
              value: v.id
            }
          })
          this.$set(this.defInps[index], "options", options);
          this.$set(this.inps[index], "options", options);
        }).catch((error) => {
          console.log(error)
        })
    },
    refreshResource() {
      let input = this.defInps[this.resource.index]
      this.fillResource(input.resource,this.resource.index)
    },
    resetForm() {
      this.inps = this.defInps.map((input) => {
        return {
          ...input,
          value: input.default
        }
      })
    },
  },
  created() {
    this.cols = this.columns.map((col) => {
      return {
        ...col,
        align: col.align || (col.type == 'integer' || col.type == 'decimal' || col.type == 'date' ? 'right' : 'left'),
        label: col.label || col.name.toUpperCase(),
        field: col.field || col.name,
        filter: col.filter || (col.type == 'boolean' ? 1 : ''),
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
  },
  mounted () {    
    // this.rerequest()

    this.defInps = this.inputs.map((input,index) => {
      if (input.type == 'resource') {
        this.fillResource(input.resource,index)
      }
      return {
        ...input,
        label: input.label ? input.label : input.name.toUpperCase(),
        value: input.default ? input.default : null
      }
    })

    this.resetForm()
  },
}
</script>

<style lang="stylus">
.text-grey-8 .material-icons
  color: white !important

.top-btn
  position: absolute
  top: 2px
  left: 40%
</style>

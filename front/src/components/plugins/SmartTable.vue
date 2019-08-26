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
        </div>
      </template>
      <template v-slot:top-row="props">
        <q-tr v-if="!loading" :props="props">
          <q-td v-for="(col,index) in props.cols" :key="index">
            <template v-if="col.type == 'string' || col.type == 'decimal' || col.type == 'integer'">
              <q-input debounce="300" v-model="col.filter" style="height:12px" input-style="padding:0px;font-size:12px" dense :placeholder="`${col.label}`"/>
            </template>
            <template v-else-if="col.type == 'boolean'">
              <q-toggle checked-icon="check" unchecked-icon="clear" :true-value="1" :false-value="0" v-model="col.filter" color="primary" dense style="margin-left:-15px"/>
            </template>
            <template v-else-if="col.type == 'enum'">
              <q-select clearable dense v-model="col.filter" :options="col.options" map-options style="height:12px;min-width:50px;font-size:12px" input-style="padding:0px;font-size:12px" :label="`${col.label}`" />
            </template>
            <template v-else-if="col.type == 'date'">
              <q-input clearable v-model="col.filter" mask="####-##-##" style="height:12px" input-style="padding:0px;font-size:12px" dense :placeholder="`${col.label}`">
                <template v-slot:after>
                  <q-icon name="event" class="cursor-pointer" style="margin-top:-25px">
                    <q-popup-proxy>
                      <q-date mask="YYYY-MM-DD" v-model="col.filter"/>
                    </q-popup-proxy>
                  </q-icon>
                </template>
              </q-input>
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
            <template v-else-if="col.name == 'n'">
              <q-avatar size="20px" class="q-ma-none q-pa-none text-anti-primary" color="primary">{{props.row.__index+nomor}}</q-avatar>
            </template>
            <template v-else-if="col.type == 'boolean'">
              <q-avatar v-if="col.value" size="24px" class="text-white" color="positive" icon="check"></q-avatar>
              <q-avatar v-else size="24px" class="text-white" color="negative" icon="close"></q-avatar>
            </template>
            <template v-else-if="col.type == 'enum'">
              {{enumValue(col,props.row)}}
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

    <q-dialog v-model="componentDialog" @hide="onRequest()">
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
            <component @closeDialog="componentDialog = false" class="q-pa-md" :data="component.data" :is="component.component"></component>
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
    resourceURL: String,
    inputs: Array,
    data: Array,
    doRequest: Function,
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
      cols: [],
      nomor: 1,
      editID: null,
      formDialog: false,
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
        let props = {
          pagination: this.pagination,
          cols: val
        }
        this.onRequest(props)
      },
      deep: true
    }
  },
  methods: {
    onRequest(props = this) {
      let pagination = props.pagination || this.pagination
      let cols = props.cols || this.cols
      let colFilter = {} 
      cols.forEach((el) => {
        if (el.type && el.type != 'dialog') {
          colFilter[el.name] = el.filter
          if (el.type == 'enum') {
            if (el.filter !== '' && el.filter !== null && el.filter.hasOwnProperty('value'))
              colFilter[el.name] = el.filter.value
            else
              colFilter[el.name] = ''
          }
        }
      })
      this.loading = true
      this.doRequest({pagination,colFilter}).then((data) => {
        this.nomor = data.from
        this.pagination.page = data.current_page
        this.pagination.rowsPerPage = data.per_page
        this.pagination.rowsNumber = data.total          
      }).catch((error) => {
        console.log(error)
      }).finally(() => {
        this.loading = false
      })
    },
    showComponent(col,row) {
      this.component = {
        title: col.label,
        component: col.component,
        data: row
      }
      this.componentDialog = true
    },
    enumValue(col,row) {
      // if (col.value == undefined && col.value == null)
      //   return '-'
      let val = col.options.find((v) => v.value == row[col.name])
      if (val == undefined)
        return '-'
      return val.label
    }
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
      {name: 'n', label: '', style: 'width:25px', sortable: false},
      ...this.cols,
    ]
  },
  mounted () {    
    // this.rerequest()

    // this.defInps = this.inputs.map((input,index) => {
    //   if (input.type == 'resource') {
    //     this.fillResource(input.resource,index)
    //   }
    //   return {
    //     ...input,
    //     label: input.label ? input.label : input.name.toUpperCase(),
    //     value: input.default ? input.default : null
    //   }
    // })

    // this.resetForm()
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

<template>
  <div>
    <q-table
      :title="titleComputed"
      :data="data"
      :columns="cols"
      row-key="id"
      :pagination.sync="pagination"
      :loading="loading"
      @request="onRequest"
      binary-state-sort
      :rows-per-page-options="rpp"
      class="bg-secondary text-anti-primary"
      table-class="no-scroll bg-accent text-primary"
    >
      <template v-slot:top-right>
        <div class="row reverse q-col-gutter-md">
          <div>
            <q-btn label="Export XLS" color="green"/>
          </div>
          <div>
            <q-btn-dropdown
              color="primary"
              label="Filter"
              icon="sort"
              @hide="onRequest"
            >
              <div class="q-pa-sm">
                <div class="">
                  <q-option-group
                    v-model="filter"
                    :options="filterOpts"
                    color="primary"
                    inline
                  />
                </div>

                <q-separator inset class="q-my-sm" />

                <div class="row q-col-gutter-sm">
                  <q-input clearable label="Dari" v-model="filterDari" mask="####/##/##" placeholder="YYYY/MM/DD">
                    <template v-slot:append>
                      <q-icon name="event" class="cursor-pointer">
                        <q-popup-proxy>
                          <q-date v-model="filterDari" />
                        </q-popup-proxy>
                      </q-icon>
                    </template>
                  </q-input>
                  <q-input clearable label="Sampai" v-model="filterSampai" mask="####/##/##" placeholder="YYYY/MM/DD">
                    <template v-slot:append>
                      <q-icon name="event" class="cursor-pointer">
                        <q-popup-proxy>
                          <q-date v-model="filterSampai" />
                        </q-popup-proxy>
                      </q-icon>
                    </template>
                  </q-input>
                </div>
              </div>
            </q-btn-dropdown>
          </div>
        </div>
      </template>
    </q-table>
  </div>
</template>

<script>
export default {
  props: {
    title: String,
    columns: Array,
    resourceURL: String,
    inputs: Array,
    doRequest: Function,
  },
  data () {
    return {
      filter: 'd',
      filterDari: '',
      filterSampai: '',
      rpp: [5,7,9,15,50,100,500],
      pagination: {
        page: 1,
        rowsPerPage: 9,
        rowsNumber: 10,
      },
      data: [],
      cols: [],
      dateIndex: 0,
      titleComputed: this.title
    }
  },
  computed: {
    filterOpts() {
      return [
        { label: 'Harian', value: 'd' },
        { label: 'Bulanan', value: 'm' },
        { label: 'Tahunan', value: 'y' },
        { label: 'Keseluruhan', value: 'a' },
      ]
    },
    loading() {
      return this.$store.state.loading
    },
  },
  methods: {
    onRequest (props = null) {
      let pagination = props && props.hasOwnProperty('pagination') ? props.pagination : this.pagination
      let params = {
        method: this.filter,
        from: this.filterDari || null,
        to: this.filterSampai || null,
      }

      this.$store.dispatch("fetchPaginate",{url: this.resourceURL, pagination, params})
        .then((data) => {
          let tabledata = data.data
          this.data = tabledata.data
          this.pagination.page = tabledata.current_page
          this.pagination.rowsPerPage = tabledata.per_page
          this.pagination.rowsNumber = tabledata.total          
          this.reformat()
        }).catch((error) => {
          console.log(error)
          this.$notifyNegative('Ada Sebuah Kesalahan')
        })
    },
    reformat() {
      let index = this.dateIndex
      if (this.filter === 'd') {
        this.cols[index].label = 'Tanggal'
        this.cols[index].format = (val, row) => { return this.$date.formatDate(val, 'DD/MMMM/YYYY') }
      } else if (this.filter === 'm') {
        this.cols[index].label = 'Bulan'
        this.cols[index].format = (val, row) => { return this.$date.formatDate(val, 'MMMM/YYYY') }
      } else if (this.filter === 'y') {
        this.cols[index].label = 'Tahun'
        this.cols[index].format = (val, row) => { return this.$date.formatDate(val, 'YYYY') }
      } else if (this.filter === 'a') {
        this.cols[index].label = '-'
        this.cols[index].format = (val, row) => { return '-' }
      }

      
      if (this.filterDari && this.filterSampai)
        this.titleComputed = `${this.title} [${this.$date.formatDate(this.filterDari,'DD/MM/YYYY')} ~ ${this.$date.formatDate(this.filterSampai,'DD/MM/YYYY')}]`
      else if (this.filterDari)
        this.titleComputed = `${this.title} [${this.$date.formatDate(this.filterDari,'DD/MM/YYYY')} ~ ...]`
      else if (this.filterSampai)
        this.titleComputed = `${this.title} [... ~ ${this.$date.formatDate(this.filterSampai,'DD/MM/YYYY')}]`
      else
        this.titleComputed = this.title
    }
  },
  created() {
    this.cols = this.columns.map((col,index) => {
      if (col.type == 'date') this.dateIndex = index
      return {
        ...col,
        align: col.align || (col.type == 'integer' || col.type == 'decimal' || col.type == 'date' ? 'right' : 'left'),
        label: col.label || col.name.toUpperCase(),
        field: col.field || col.name,
        filter: col.filter || (col.type == 'boolean' ? 1 : ''),
        format: (val, row) => {
          if (col.type === 'decimal') {
            return 'Rp ' + this.$numeralCurrency(val)
          } else if (col.type === 'date') {
            return this.$date.formatDate(val, 'DD/MMMM/YYYY')
          }
          return val
        }
      }
    })
  },
  mounted() {
    this.onRequest()
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

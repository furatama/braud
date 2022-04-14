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
            <q-btn label="Export Ke Excel" color="green" @click="exportXLS"/>
          </div>
          <div>
            <q-btn-dropdown
              color="primary"
              label="Filter"
              icon="sort"
              @show="applyFilter"
              @hide="onRequest"
            >
              <q-card class="q-pa-sm">
                <div class="text-h6 q-mb-sm">Jenis Report</div>
                <div class="">
                  <q-option-group
                    v-model="filter"
                    :options="filterOpts"
                    color="primary"
                    inline
                  />
                </div>

                <q-separator inset class="q-my-sm" />

                <div class="text-h6 q-mb-sm">Scope Report</div>

                <div class="row q-col-gutter-sm">
                  <div><q-btn @click="setDate('d')" color="green-5" label="Hari Ini"/></div>
                  <div><q-btn @click="setDate('w')" color="green-6" label="Minggu Ini"/></div>
                  <div><q-btn @click="setDate('m')" color="green-7" label="Bulan Ini"/></div>
                  <div><q-btn @click="setDate('y')" color="green-8" label="Tahun Ini"/></div>
                </div>

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

                <q-separator v-if="aFilters.length > 0" inset class="q-my-sm" />

                <div v-if="aFilters.length > 0" class="text-h6 q-mb-sm">Key Filter</div>

                <div v-for="(filter,ifilter) in aFilters" :key="ifilter">
                  <div v-if="filter.type == 'text' || !filter.type">
                    <q-input clearable :label="filter.label" v-model="filter.value" />
                  </div>
                  <div v-else-if="filter.type == 'select'">
                    <select-filter clearable v-model="filter.value" :options="filter.options" map-options :label="filter.label">
                    </select-filter>
                  </div>
                </div>

              </q-card>
            </q-btn-dropdown>
          </div>
        </div>
      </template>
    </q-table>
  </div>
</template>

<script>
import SelectFilter from './SelectFilter'
import { LocalStorage } from 'quasar'

export default {
  components: {
    'select-filter' : SelectFilter
  },
  props: {
    title: String,
    columns: Array,
    resourceURL: String,
    inputs: Array,
    doRequest: Function,
    addFilters: Array,
  },
  data () {
    return {
      filter: 'd',
      filterDari: '',
      filterSampai: '',
      rpp: [5,7,9,15,50,100,500],
      pagination: {
        sortBy: null,
        descending: false,
        page: 1,
        rowsPerPage: 50,
        rowsNumber: 10,
      },
      data: [],
      cols: [],
      dateIndex: 0,
      titleComputed: this.title,
      aFilters: []
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
    pFilters() {
      return this.aFilters.reduce((acc,v) => {
        acc[v.name] = v.value && v.value.hasOwnProperty('label') ? v.value.value : v.value
        return acc
      }, {})
    }
  },
  watch: {
    addFilters: {
      handler(val){
        let vals = this.aFilters.map(v => v.value)
        this.aFilters = [...val]
      },
      deep: true
    }
  },
  methods: {
    applyFilter() {
      // setTimeout(() => {
        this.aFilters.forEach((v,i) => {
          if (this.pFilters[v.name])
            this.aFilters[i].value = this.pFilters[v.name]
        })
        console.log(this.aFilters)
      // }, 500);
    },
    onRequest (props = null) {
      console.log(props, this.pFilters)
      let pagination = props && props.hasOwnProperty('pagination') ? props.pagination : this.pagination
      let params = {
        method: this.filter,
        from: this.filterDari || null,
        to: this.filterSampai || null,
        ...this.pFilters
      }

      let cache = LocalStorage.getItem('REPORT_' + this.resourceURL + '.' + JSON.stringify(params))
      if (!cache)
        cache = LocalStorage.getItem('REPORT_' + this.resourceURL)
      if (cache) {
        let tabledata = JSON.parse(cache)
        this.data = tabledata.data
        this.pagination.page = tabledata.current_page
        this.pagination.rowsPerPage = tabledata.per_page
        this.pagination.rowsNumber = tabledata.total
        this.pagination.sortBy = pagination.sortBy
        this.pagination.descending = pagination.descending
        this.reformat()
      }
      this.$store.dispatch("fetchPaginate",{url: this.resourceURL, pagination, params})
        .then((data) => {
          let tabledata = data.data
          this.data = tabledata.data
          this.pagination.page = tabledata.current_page
          this.pagination.rowsPerPage = tabledata.per_page
          this.pagination.rowsNumber = tabledata.total
          this.pagination.sortBy = pagination.sortBy
          this.pagination.descending = pagination.descending
          this.reformat()
          LocalStorage.set('REPORT_' + this.resourceURL, JSON.stringify(tabledata))
          LocalStorage.set('REPORT_' + this.resourceURL + '.' + JSON.stringify(params), JSON.stringify(tabledata))
        }).catch((error) => {
          console.log(error)
          this.$notifyNegative('Ada Sebuah Kesalahan')
        })
    },
    reformat() {
      let index = this.dateIndex
      let jenis = ""
      if (this.filter === 'd') {
        this.cols[index].label = 'Tanggal'
        this.cols[index].format = (val, row) => { return this.$date.formatDate(val, 'DD/MMMM/YYYY') }
        jenis = "[Harian]"
      } else if (this.filter === 'm') {
        this.cols[index].label = 'Bulan'
        this.cols[index].format = (val, row) => { return this.$date.formatDate(val, 'MMMM/YYYY') }
        jenis = "[Bulanan]"
      } else if (this.filter === 'y') {
        this.cols[index].label = 'Tahun'
        this.cols[index].format = (val, row) => { return this.$date.formatDate(val, 'YYYY') }
        jenis = "[Tahunan]"
      } else if (this.filter === 'a') {
        this.cols[index].label = '-'
        this.cols[index].format = (val, row) => { return '-' }
        jenis = "[Keseluruhan]"
      }


      if (this.filterDari && this.filterSampai)
        this.titleComputed = `${this.title} ${jenis} [${this.$date.formatDate(this.filterDari,'DD/MM/YYYY')} ~ ${this.$date.formatDate(this.filterSampai,'DD/MM/YYYY')}]`
      else if (this.filterDari)
        this.titleComputed = `${this.title} ${jenis} [${this.$date.formatDate(this.filterDari,'DD/MM/YYYY')} ~ ...]`
      else if (this.filterSampai)
        this.titleComputed = `${this.title} ${jenis} [... ~ ${this.$date.formatDate(this.filterSampai,'DD/MM/YYYY')}]`
      else
        this.titleComputed = `${this.title} ${jenis}`
    },
    exportXLS() {
      let params = {
        method: this.filter,
        from: this.filterDari || null,
        to: this.filterSampai || null,
        ...this.pFilters
      }
      this.$store.dispatch("getXLS",{url: this.resourceURL + '/excel', params, filename: this.titleComputed})
    },
    setDate(where) {
      let today = new Date()
      let date = this.$date
      if (where == 'd') {
        this.filterDari = date.formatDate(today,'YYYY/MM/DD')
        this.filterSampai = date.formatDate(today,'YYYY/MM/DD')
      } else if (where == 'w') {
        let wk = date.getDayOfWeek(today)
        let start = date.subtractFromDate(today, {days: wk-1})
        let end = date.addToDate(today, {days: 7-wk})
        this.filterDari = date.formatDate(start,'YYYY/MM/DD')
        this.filterSampai = date.formatDate(end,'YYYY/MM/DD')
      } else if (where == 'm') {
        let mon = date.daysInMonth(today)
        let start = date.adjustDate(today, {date: 1})
        let end = date.adjustDate(today, {date: mon})
        this.filterDari = date.formatDate(start,'YYYY/MM/DD')
        this.filterSampai = date.formatDate(end,'YYYY/MM/DD')
      } else if (where == 'y') {
        let start = date.adjustDate(today, {date: 1, month: 1})
        let end = date.adjustDate(today, {date: 31, month: 12})
        this.filterDari = date.formatDate(start,'YYYY/MM/DD')
        this.filterSampai = date.formatDate(end,'YYYY/MM/DD')
      }
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
        sortable: col.sortable || true,
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

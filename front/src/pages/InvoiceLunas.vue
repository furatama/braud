<template>
  <q-page padding>

    <x-table
      title="List Invoice Credit"
      :data="table.data"
      :columns="table.columns"
      :pagination.sync="table.pagination"
      :loading="loading"
      :rows-per-page-options="[0]"
      class="bg-secondary text-anti-primary"
      table-class="no-scroll bg-accent text-primary"
      :selected.sync="table.selected"
    >
      <template v-slot:top-right>
        <div class="row reverse q-col-gutter-md">
          <div class="row q-col-gutter-sm">
            <q-input dense dark outlined label="Dari" v-model="filterDari" mask="####/##/##" placeholder="YYYY/MM/DD">
              <template v-slot:append>
                <q-icon name="event" class="cursor-pointer">
                  <q-popup-proxy>
                    <q-date v-model="filterDari" />
                  </q-popup-proxy>
                </q-icon>
              </template>
            </q-input>
            <q-input dense dark outlined label="Sampai" v-model="filterSampai" mask="####/##/##" placeholder="YYYY/MM/DD">
              <template v-slot:append>
                <q-icon name="event" class="cursor-pointer">
                  <q-popup-proxy>
                    <q-date v-model="filterSampai" />
                  </q-popup-proxy>
                </q-icon>
              </template>
            </q-input>
          </div>
          <div style="width:300px">
            <select-filter dense class="bg-white" outlined label="Customer" v-model="table.customer" :options="table.customerOpts" :loading="loading"/>
          </div>
        </div>
      </template>
      <template v-slot:bottom>
      </template>
    </x-table>

    <q-dialog v-if="curData" v-model="showEdit">
      <q-card style="min-width:40vw">
        <q-bar class="bg-primary text-white">
          <div class="text-h6">Edit Invoice</div>
          <q-space />
          <q-btn dense flat icon="close" v-close-popup>
            <q-tooltip>Close</q-tooltip>
          </q-btn>
        </q-bar>
        <q-card-section>
          <div class="column">
            <q-input autogrow label="Catatan" v-model="curData.catatan" />
          </div>
        </q-card-section>
        <q-card-actions>
          <div class="row justify-end q-gutter-sm q-my-xs full-width">
            <q-btn type="button" icon="delete" color="negative">
              <q-popup-proxy :breakpoint="600">
                <q-banner inline-actions class="bg-negative text-white">
                  Yakin?
                  <template v-slot:action>
                    <q-btn flat label="Ya" v-close-popup @click="onDelete"/>
                  </template>
                </q-banner>
              </q-popup-proxy>
            </q-btn>
            <q-btn type="button" label="SIMPAN INV" @click="onSubmit(false)" color="positive"/>
            <q-btn type="button" label="LUNASKAN INV" @click="onSubmit(true)" color="positive"/>
          </div>
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import SelectFilter from '../components/plugins/SelectFilter'
import TableEx from '../components/plugins/TableEx'
import PrintOut from '../components/InvoicePrintOut'

export default {
  // name: 'PageName',
  components: {
    'select-filter' : SelectFilter,
    'print-out' : PrintOut,
    'x-table' : TableEx
  },
  data() {
    return {
      table: {
        tanggal: this.$date.formatDate(Date.now(),'YYYY/MM/DD'),
        due: this.$date.formatDate(this.$date.adjustDate(this.$date.addToDate(new Date(), {month: 1}), {date: 20}),'YYYY/MM/DD'),
        customer: '',
        customerOpts: [],
        columns: [
          { name: 'tanggal', field: 'tanggal', label: 'Tanggal Invoice', type: 'date'},
          { name: 'customer', field: 'customer', label: 'Customer', type: 'string'},
          { name: 'no', field: 'no', label: 'No Order (3 digit belakang)', type: 'string'},
          { name: 'order', field: 'order', label: 'Total', type: 'decimal'},
          { name: 'lunas', field: 'lunas', label: 'Status', type: 'enum', options: [
            {label: 'Lunas', value: 1},{label: 'Belum', value: 0},{label: 'J.Tmpo', value: 2},
          ]},
          { name: 'catatan', field: 'catatan', label: 'Catatan', type: 'string'},
          { name: 'edit', field: 'edit', label: 'Edit', type: 'callback', attr: {color: 'positive'}, callback: (row) => {
            this.showEdit = true
            this.curData = {...row}
          }},
        ],
        pagination: {
          rowsPerPage: 0
        },
        data: [],
        grandTotal: 0,

        selected: [],
        getSelectedString: '',
      },
      filterDari: '',
      filterSampai: '',
      editCatatan: '',
      showEdit: false,
      curData: null
    }
  },
  computed: {
    loading() {
      return this.$store.state.loading 
    },
    customerData() {
      return this.table.customer
    },
    totalSelected() {
      return this.table.selected.reduce((sum,v) => {
        return (sum * 1) + (1 * v.total)
      },0)
    },
    printData() {
      return {
        data: this.table.selected,
        total: this.totalSelected,
        from: this.filterDari,
        to: this.filterSampai,
        customer: this.table.customer.label

      }
    }
  },
  watch: {
    customerData() {
      this.requestData()
    },
    filterDari() {
      this.requestData()
    },
    filterSampai() {
      this.requestData()
    },
    orderDate() {
      this.loadNoOrder()
    },
    payMethod() {
      this.loadNoOrder()
    },
    filter() {
      this.produkData = this.$array_filter(this.defProdukData,this.filter,'nama')
    }
  },
  methods: {
    requestData() {
      this.table.selected = []
      const idCust = this.table.customer.value
      this.$store.dispatch("fetch",
        {url: '/invoice/data', params: {
          id_customer: idCust,
          dari: this.filterDari,
          sampai: this.filterSampai
        }}
      ).then((response) => {
        let data = response.data
        this.table.data = data
        console.log(this.table.data, 'tdata')
        // resolve(data)
      }).catch((error) => {
        this.$notifyNegative('Ada Sebuah Kesalahan')
        console.log(error)
        // reject(error)
      })
      // })
    },
    selectAll(evt) {
      evt.target.select()
      // console.log(evt)
    },
    loadCustomer() {
      this.$store.dispatch("fetchOptions",{url: '/customer/aktif'})
        .then((response) => {
          let data = response.data
          this.table.customerOpts = data.map((v) => {
            return {
              label: v.nama,
              value: v.id,
              alamat: v.alamat || '',
              telepon: v.telepon || '',
              email: v.email || '',
            }
          })
        }).catch((error) => {
          console.log(error)
          this.$notifyNegative("Gagal Mengambil Data Customer")
        })
    },
    onSubmit(lunas) {
      let inputs = {
        ...this.curData,
        lunaskan: lunas
      }
      const ed = lunas ? "dilunaskan" : "diedit"
      this.$store.dispatch("updateSingle",{url: '/invoice/data', id: this.curData.id, inputs})
        .then((response) => {
          this.requestData()
          this.$notifyPositive("Berhasil " + ed)
          this.showEdit = false
        }).catch((error) => {
          // console.log(error)
          this.$notifyNegative("Gagal " + ed)
        })
    },
    onDelete() {
      const row = curData 
      this.$store.dispatch("deleteSingle",{url: '/invoice/data', id: row.id})
      .then((data) => {
        this.$notifyPositive('Invoice Berhasil Dihapus')
      }).catch((error) => {
        console.log(error)
        this.$notifyNegative('Ada Sebuah Kesalahan')
      }).finally(() => {
        this.requestData()
      })
    },
  },
  mounted() {
    this.loadCustomer()
    const date = this.$date
    const today = new Date()
    const mon = date.daysInMonth(today)
    const start = date.adjustDate(today, {date: 1})
    const end = date.adjustDate(today, {date: mon})
    this.filterDari = date.formatDate(start,'YYYY/MM/DD')
    this.filterSampai = date.formatDate(end,'YYYY/MM/DD')
  }
}
</script>

<style lang="stylus">
  .q-table th
    opacity: 1 !important

  .q-item__section--main ~ .q-item__section--side 
    align-items: flex-end
    padding-right: 0
    padding-left: 1px
  
  input[type=number]::-webkit-inner-spin-button, 
  input[type=number]::-webkit-outer-spin-button { 
    -webkit-appearance: none; 
    margin-right: 2px; 
  }

</style>

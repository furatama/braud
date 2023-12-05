<template>
  <q-page padding>

    <x-table
      title="List Order"
      :data="table.data"
      :columns="table.columns"
      row-key="id"
      :pagination.sync="table.pagination"
      :loading="loading"
      selection="multiple"
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
        <div class="row full-width justify-between">
          <span class="text-h5">Total Credit : {{$numeralCurrency(totalSelected)}}</span>
          <div class="row q-gutter-sm">
            <q-btn :disabled="table.selected.length == 0" @click="onSubmit(false)" label="CETAK INVOICE" color="positive"/>
            <q-btn :disabled="table.selected.length == 0" @click="onSubmit(true)" label="CETAK & SIMPAN" color="positive"/>
          </div>
        </div>
      </template>
    </x-table>
    <print-out
      ref="printer"
      :data="printData"
    />
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
          { name: 'tanggal', field: 'tanggal', label: 'Tanggal Order', type: 'date'},
          { name: 'no', field: 'no', label: 'No Order', type: 'string'},
          { name: 'total', field: 'total', label: 'Total', type: 'decimal'},
          { name: 'lunas', field: 'lunas', label: 'Status', type: 'enum', options: [
            {label: 'Lunas', value: 1},{label: 'Belum Lunas', value: 0},{label: 'Jatuh Tempo', value: 2},
          ]},
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
      produkData: [],
      defProdukData: [],
      filter: '',
      checkout: {
        isShow: false,
      },
      payment: {
        paid: 0,
        method: 'cash',
        options: [
          {label: 'Cash', value: 'cash'},
          {label: 'Credit', value: 'credit'},
        ],
      },
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
        customer: this.table.customer.label,
        customerAlamat: this.table.customer.alamat,
      }
    }
  },
  watch: {
    customerData(val) {
      this.requestData(val.value)
    },
    filterDari(val) {
      this.requestData(this.table.customer.value)
    },
    filterSampai(val) {
      this.requestData(this.table.customer.value)
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
    requestData(idCust) {
      this.table.selected = []
      // let { page, rowsPerPage, rowsNumber, sortBy, descending } = pagination
      // return new Promise((resolve, reject) => {
      this.$store.dispatch("fetch",
        {url: 'order/customer/' +  idCust, params: {
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
    onSubmit(simpan) {
      if (!simpan) {
        this.printOrder()
        return
      }
      let inputs = {
        id_customer: this.table.customer.hasOwnProperty('value') ? this.table.customer.value : this.table.customer,
        tanggal: this.$date.formatDate(Date(),'YYYY/MM/DD'),
        detail: this.table.selected.map((v) => {
          return {
            id_order: v.id,
          }
        }),
      }
      console.log(inputs)
      this.$store.dispatch("postSingle",{url: `/invoice/data`,inputs})
        .then((response) => {
          this.requestData(this.table.customer.value)
          this.printOrder()
          this.$notifyPositive("Invoice berhasil disimpan")
        }).catch((error) => {
          // console.log(error)
          this.$notifyNegative("Invoice gagal disimpan")
        })
    },
    printOrder() {
      this.$refs.printer.print()
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

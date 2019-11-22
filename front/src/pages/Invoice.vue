<template>
  <q-page padding>

    <x-table
      title="List Invoice"
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
          <div style="width:400px">
            <select-filter dense class="bg-white" outlined label="Customer" v-model="table.customer" :options="table.customerOpts" :loading="loading"/>
          </div>
        </div>
      </template>
      <template v-slot:bottom>
        <div class="row full-width justify-end">
          <q-btn label="LUNASKAN DAN CETAK INVOICE" color="positive"/>
        </div>
      </template>
    </x-table>

    <!-- <q-dialog persistent v-model="checkout.isShow" @hide="() => {checkout.isShow = false}">
      <q-card style="width:50vw">
        <q-bar class="bg-primary text-white">
          <div class="text-h6">CHECKOUT</div>

          <q-space />

          <q-btn dense flat icon="close" v-close-popup>
            <q-tooltip>Close</q-tooltip>
          </q-btn>
        </q-bar>
        
        <q-card-section>
          <q-input label="Total Harga" :value="$numeralCurrency(table.grandTotal)" readonly input-class="text-right text-h5"/>
          <div class="row q-my-sm">
            <span class="q-mt-sm">Metode Pembayaran : </span><q-option-group v-model="payment.method" :options="payment.options" color="primary" inline/>
          </div>
          <q-input label="Jumlah Bayar" v-model="payment.paid" input-class="text-right text-h5"/>
          <div class="row q-my-sm q-col-gutter-xs">
            <div class="col"><q-btn class="full-width" dense color="negative" label="CLEAR" @click="addMoney('clear')"/></div>
            <div class="col"><q-btn class="full-width" dense color="primary" label="1.000" @click="addMoney(1000)"/></div>
            <div class="col"><q-btn class="full-width" dense color="primary" label="2.000" @click="addMoney(2000)"/></div>
            <div class="col"><q-btn class="full-width" dense color="primary" label="5.000" @click="addMoney(5000)"/></div>
            <div class="col"><q-btn class="full-width" dense color="primary" label="10.000" @click="addMoney(10000)"/></div>
            <div class="col"><q-btn class="full-width" dense color="primary" label="20.000" @click="addMoney(20000)"/></div>
            <div class="col"><q-btn class="full-width" dense color="primary" label="50.000" @click="addMoney(50000)"/></div>
            <div class="col"><q-btn class="full-width" dense color="primary" label="100.000" @click="addMoney(100000)"/></div>
            <div class="col"><q-btn class="full-width" dense color="positive" label="PAS" @click="addMoney('pas')"/></div>
          </div>
          <q-input :label="exchangeLabel" :value="displayExchange" readonly input-class="text-right text-h5"/>
        </q-card-section>

        <q-card-actions align="right" class="bg-primary text-white">
          <q-btn
            label="CETAK ORDER" 
            color="positive" 
            bordered 
            icon="print"
            :disable="(exchange < 0 && payment.method == 'cash') || (exchange > 0 && payment.method == 'credit')" 
            @click="() => {submitOrder(); printOrder();}"
          >
          </q-btn>
          
        </q-card-actions>
      </q-card>
    </q-dialog> -->
    <!-- <div class="absolute-bottom full-width bg-accent shadow-3">
      asdf
    </div> -->
    <print-out 
      ref="printer" 
      :data="printData"
    />
      <!-- :data="table.data" 
      :total="table.grandTotal" 
      :cash="paidAmount" 
      :exchange="exchange" 
      :date="$date.formatDate(Date.now(), 'DD.MM.YY-HH:mm')" -->
  </q-page>
</template>

<script>
import SelectFilter from '../components/plugins/SelectFilter'
import TableEx from '../components/plugins/TableEx'
import PrintOut from '../components/OrderPrintOut'

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
          // { name: 'customer', field: 'customer', label: 'Customer', type: 'string'},
          // { name: 'metode', field: 'metode', label: 'Metode', type: 'enum', options: [
          //   {label: 'Cash', value: 'cash'},{label: 'Credit', value: 'credit'},
          // ]},
          { name: 'total', field: 'total', label: 'Total', type: 'decimal'},
          // { name: 'tunai', field: 'tunai', label: 'Terbayar', type: 'decimal'},
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
      }
    }
  },
  computed: {
    loading() {
      return this.$store.state.loading 
    },
    // displayExchange() {
    //   return this.payment.method === 'cash' ? this.$numeralCurrency(this.exchange) : this.$numeralCurrency(-this.exchange)
    // },
    // exchangeLabel() {
    //   return this.payment.method === 'cash' ? 'Kembalian' : 'Sisa Hutang'
    // },
    customerData() {
      return this.table.customer
    },
    // orderDate() {
    //   return this.order.tanggal
    // },
    // payMethod() {
    //   return this.payment.method
    // },
    // exchange() {
    //   const total = this.table.grandTotal
    //   const paid = this.payment.paid
    //   return paid - total
    // },
    // printData() {
    //   return {
    //     // no: this.order.no,
    //     tanggal: this.$date.formatDate(this.order.tanggal, 'DD/MM/YYYY'),
    //     due: this.$date.formatDate(this.order.due, 'DD/MM/YYYY'),
    //     kasir: this.$store.getters.getName,
    //     method: this.payment.method == 'cash' ? "CASH" : "CREDIT",
    //     cust: {
    //       nama: this.customerData.label,
    //       email: this.customerData.email,
    //       alamat: this.customerData.alamat,
    //       telepon: this.customerData.telepon,
    //     },
    //     total: this.$numeralCurrency(this.table.grandTotal),
    //     tunaiLabel: this.payment.method == 'cash' ? "Terbayar" : "Kredit",
    //     tunai: this.$numeralCurrency(this.payment.paid),
    //     sisaLabel: this.payment.method == 'cash' ? "Kembalian" : "Hutang",
    //     sisa: this.payment.method == 'cash' ? this.$numeralCurrency(this.exchange) : this.$numeralCurrency(-this.exchange),
    //     data: this.table.data
    //   }
    // }
  },
  watch: {
    customerData(val) {
      console.log(val)
      this.requestData(val.value)
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
      // let { page, rowsPerPage, rowsNumber, sortBy, descending } = pagination
      // return new Promise((resolve, reject) => {
      this.$store.dispatch("fetchAll",
        {url: 'order/customer/' +  idCust}
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
    submitOrder() {
      let inputs = {
        no: this.order.no,
        id_customer: this.customerData.value,
        metode: this.payment.method,
        tanggal: this.orderDate,
        due: this.order.due,
        faktur: '',
        tunai: this.payment.paid,
        total: this.table.grandTotal,
        keterangan: '',
        detail: this.table.data.map((v) => {
          return {
            id_produk: v.id,
            harga: v.hargaN,
            qty: v.qty,
            diskon: v.diskon
          }
        }),
      }
      this.$store.dispatch("postSingle",{url: `/order/data`,inputs})
        .then((response) => {
          this.resetForm()
          this.$notifyPositive("Berhasil Memasukkan Order")
        }).catch((error) => {
          // console.log(error)
          this.$notifyNegative("Gagal Memasukkan Order")
        })
    },
    printOrder() {
      this.$refs.printer.print()
    },
  },
  mounted() {
    this.loadCustomer()
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

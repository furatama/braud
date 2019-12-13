<template>
  <q-page padding>
    <div class="row q-col-gutter-sm">
      <div class="col-9">
        <div class="row q-gutter-sm">
          <q-input v-show="false" v-model="order.no" readonly outlined label="No Order" :loading="loading" />
          <q-input v-model="order.tanggal" outlined label="Tanggal Order" mask="####/##/##" placeholder="YYYY/MM/DD">
            <template v-slot:append>
              <q-icon name="event" class="cursor-pointer">
                <q-popup-proxy>
                  <q-date v-model="order.tanggal"/>
                </q-popup-proxy>
              </q-icon>
            </template>
          </q-input>
          <q-input v-model="order.due" outlined label="Jatuh Tempo (Credit Only)" mask="####/##/##" placeholder="YYYY/MM/DD">
            <template v-slot:append>
              <q-icon name="event" class="cursor-pointer">
                <q-popup-proxy>
                  <q-date v-model="order.due"/>
                </q-popup-proxy>
              </q-icon>
            </template>
          </q-input>
          <select-filter class="col-5" outlined label="Customer" v-model="order.customer" :options="order.customerOpts" :loading="loading"/>
        </div>
        <q-separator class="q-my-sm"/>

        <q-table
          :data="table.data"
          table-header-class="bg-secondary text-white"
          :columns="table.columns"
          dense
          class="my-sticky-header-table"
          :pagination.sync="table.pagination"
          no-data-label="Belum Ada Produk"            
        >
          <template v-slot:body="props">
            <q-tr :props="props" :style="`background-color:rgba(255,255,0,${props.row.bg})`">
              <q-td v-for="col in props.cols" :key="col.name" :props="props">
                <template v-if="col.name == 'action'">
                  <q-btn icon="clear" color="negative" flat dense @click="() => { table.data.splice(props.row.__index,1); refreshGrandTotal(); }">
                  </q-btn>
                </template>
                <template v-else-if="col.name == 'no'">
                  <q-avatar size="24px" class="text-white" color="primary">{{props.row.__index+1}}</q-avatar>
                </template>
                <template v-else-if="col.name == 'qty'">
                  <q-input ref="qty" @focus="selectAll" @input="refreshSubtotal(table.data[props.row.__index])" type="number" dense v-model="table.data[props.row.__index][col.name]" input-class="text-right" style="width:40px;float:right"/>
                </template>
                <template v-else-if="col.name == 'diskon'">
                  <q-input @focus="selectAll" min="0" max="100" @input="refreshSubtotal(table.data[props.row.__index])" type="number" dense v-model="table.data[props.row.__index][col.name]" input-class="text-right" style="width:50px;float:right">
                    <template v-slot:after>
                      <span class="text-body2">%</span>
                    </template>
                  </q-input>
                </template>
                <template v-else>
                  {{col.value}}
                </template>
              </q-td>
            </q-tr>
          </template>
          <template v-slot:bottom>
            <div class="row full-width justify-end">
              <div>
                <div style="margin-right:48px" class="text-right">
                  <span class="">Rp</span>
                  <span class="text-h5">{{$numeralCurrency(table.grandTotal)}}</span>
                </div>
              </div>
            </div>
          </template>
        </q-table>

        <div class="row q-mt-sm justify-between">
          <q-btn :disabled="table.data.length <= 0" label="RESET" color="info" icon="refresh" @click="() => {resetForm()}"/>
          <div class="row q-col-gutter-md">
            <div class="q-mr-md"><q-btn :disabled="table.data.length <= 0" label="CHECKOUT CREDIT" color="teal" icon="credit_card" @click="addOrder('credit')"/></div>
            <div><q-btn :disabled="table.data.length <= 0" label="CHECKOUT CASH" color="green" icon="monetization_on" @click="addOrder('cash')"/></div>
          </div>
        </div>

      </div>
    <!-- <div class="flex q-gutter-xs">
      <q-btn size="sm" dense v-for="n in 30" :label="`bread asdf croissant ${n*10*n}`" icon="add"/>
    </div> -->
      <div class="fixed-right" style="top:6vh;right:10px">
        <div class="q-pt-sm">
          <q-list bordered separator class="bg-accent" >
            <q-item dark class="bg-primary">
              <q-item-section>      
                <q-input dark outlined dense debounce="300" v-model="filter" placeholder="Filter Produk">
                  <template v-slot:append>
                    <q-icon name="search" />
                  </template>
                </q-input>
              </q-item-section>
            </q-item>

            <q-scroll-area style="height:80vh">
              <q-item  v-for="(data,index) in produkData" clickable v-ripple :key="index" class="q-pa-none">
                <q-item-section @click="selectSearchedItem(data)" class="q-pl-sm q-py-sm">
                  <q-item-label>{{data.nama}}</q-item-label>
                  <q-item-label caption>{{`Rp ${$numeral(data.harga).format('0,0')}`}}</q-item-label>
                </q-item-section>
                <q-item-section side>
                  <q-btn color="secondary" label="+5" @click="selectSearchedItem(data,5)"/>
                </q-item-section>
              </q-item>

            <q-inner-loading :showing="loading">
              <q-spinner-dots size="50px" color="primary" />
            </q-inner-loading>
            </q-scroll-area>
          </q-list>
        </div>
      </div>
    </div>

    <q-dialog persistent v-model="checkout.isShow" @hide="() => {checkout.isShow = false}">
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
    </q-dialog>
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
import PrintOut from '../components/OrderPrintOut'

export default {
  // name: 'PageName',
  components: {
    'select-filter' : SelectFilter,
    'print-out' : PrintOut,
  },
  data() {
    return {
      order: {
        no: '',
        tanggal: this.$date.formatDate(this.$date.addToDate(new Date(), { days: 1 }),'YYYY/MM/DD'),
        due: this.$date.formatDate(this.$date.adjustDate(this.$date.addToDate(new Date(), {month: 1}), {date: 20}),'YYYY/MM/DD'),
        customer: '',
        customerOpts: [],
      },
      table: {
        columns: [
          { name: 'produk', align: 'left', label: 'PRODUK', field: 'produk', sortable: false },
          { name: 'qty', align: 'right', label: 'QTY', field: 'qty', sortable: false },
          { name: 'harga', align: 'right', label: 'HARGA', field: 'harga', sortable: false },
          { name: 'diskon', align: 'right', label: 'DISKON', field: 'diskon', sortable: false },
          { name: 'subtotal', align: 'right', label: 'SUBTOTAL', field: 'subtotal', sortable: false },
          { name: 'action', field: 'action', label: '', sortable: false, style: 'width:10px;padding:0'}
        ],
        pagination: {
          rowsPerPage: 0
        },
        data: [{},{}],
        grandTotal: 0
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
    displayExchange() {
      return this.payment.method === 'cash' ? this.$numeralCurrency(this.exchange) : this.$numeralCurrency(-this.exchange)
    },
    exchangeLabel() {
      return this.payment.method === 'cash' ? 'Kembalian' : 'Sisa Hutang'
    },
    customerData() {
      this.table.data = []
      return this.order.customer
    },
    orderDate() {
      return this.order.tanggal
    },
    payMethod() {
      return this.payment.method
    },
    exchange() {
      const total = this.table.grandTotal
      const paid = this.payment.paid
      return paid - total
    },
    printData() {
      return {
        no: this.order.no,
        tanggal: this.$date.formatDate(this.order.tanggal, 'DD/MM/YYYY'),
        due: this.$date.formatDate(this.order.due, 'DD/MM/YYYY'),
        kasir: this.$store.getters.getName,
        method: this.payment.method == 'cash' ? "CASH" : "CREDIT",
        cust: {
          nama: this.customerData.label,
          email: this.customerData.email,
          alamat: this.customerData.alamat,
          telepon: this.customerData.telepon,
        },
        total: this.$numeralCurrency(this.table.grandTotal),
        tunaiLabel: this.payment.method == 'cash' ? "Terbayar" : "Kredit",
        tunai: this.$numeralCurrency(this.payment.paid),
        sisaLabel: this.payment.method == 'cash' ? "Kembalian" : "Hutang",
        sisa: this.payment.method == 'cash' ? this.$numeralCurrency(this.exchange) : this.$numeralCurrency(-this.exchange),
        data: this.table.data
      }
    }
  },
  watch: {
    customerData() {
      this.loadProduk()
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
    selectAll(evt) {
      evt.target.select()
      // console.log(evt)
    },
    loadCustomer() {
      this.$store.dispatch("fetchOptions",{url: '/customer/aktif'})
        .then((response) => {
          let data = response.data
          this.order.customerOpts = data.map((v) => {
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
    loadNoOrder() {
      return new Promise((resolve,reject) => {
        this.$store.dispatch("fetch",{url: `/order/num`, params: {tanggal: this.order.tanggal, metode: this.payment.method}})
          .then((data) => {
            let n = (Number(data.count) + 1)
            n = n > 999 ? n.toString(36) : n.toString()
            this.order.no = "BA" + this.$date.formatDate(this.order.tanggal,"YYMMDD") + n.padStart(3,'0')
            resolve()
          }).catch((error) => {
            console.log(error)
            this.$notifyNegative("Gagal Mengambil Data No Order")
            reject()
          })
      })
    },
    loadProduk() {
      this.$store.dispatch("fetchOptions",{url: `/produk/customer/${this.customerData.value}`})
        .then((response) => {
          let data = response.data
          this.defProdukData = data
          if (this.filter !== '')
            this.filter = ''
          this.produkData = this.$array_filter(this.defProdukData,this.filter,'nama')
        }).catch((error) => {
          console.log(error)
          this.$notifyNegative("Gagal Mengambil Data Produk")
        })
    },
    selectSearchedItem(item,add = 1) {
      let refs = this.$refs
      let row = 0
      let alreadyRow = this.table.data.find((v,i) => {
        row = i
        return v.id == item.id
      })
      if (alreadyRow) {
        alreadyRow.qty = Number(alreadyRow.qty) + Number(add)
        this.refreshSubtotal(alreadyRow)
      } else {
        alreadyRow = {
          id: item.id,
          kode: item.kode,
          produk: item.nama,
          qty: add,
          diskon: 0,
          harga: this.$numeralCurrency(item.harga),
          hargaN: item.harga,
          subtotal: this.$numeralCurrency(item.harga),
          subtotalN: item.harga,
          bg: 0
        }
        row = this.table.data.push(alreadyRow) - 1
        this.refreshGrandTotal()
      }
      alreadyRow.bg = 0.15
      setTimeout(function() { 
        refs['qty'][row].focus()
        alreadyRow.bg = 0
      }, 100);
    },
    refreshSubtotal(row) {
      let subtotal = ((100 - row.diskon) / 100) * row.hargaN * row.qty
      row.subtotal = this.$numeralCurrency(subtotal)
      row.subtotalN = subtotal
      this.refreshGrandTotal()
    },
    refreshGrandTotal() {
      let total = 0
      this.table.data.forEach(v => {
        total += Number(v.subtotalN)
      });
      this.table.grandTotal = total
      console.log(this.table.grandTotal)
    },
    addMoney(amount) {
      if (amount == 'clear')
        this.payment.paid = 0
      else if (amount == 'pas')
        this.payment.paid = this.table.grandTotal
      else
        this.payment.paid += Number(amount)
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
    resetForm() {
      this.loadNoOrder()
      this.order.tanggal = this.$date.formatDate(Date.now(),'YYYY/MM/DD')
      this.order.customer = ''
      this.table.data = []
      this.refreshGrandTotal()
      this.defProdukData = []
      this.produkData = []
      this.filter = ''
      this.payment.paid = 0
      this.payment.method = 'cash'
      this.checkout.isShow = false
    },
    printOrder() {
      this.$refs.printer.print()
    },
    addOrder(metode) {
      if (metode === 'cash') {
        this.payment.paid = this.table.grandTotal
      } else {
        this.payment.paid = 0
      }
      this.payment.method = metode
      this.loadNoOrder().then(() => {
        this.submitOrder()
        this.printOrder()
      })
    }
  },
  mounted() {
    this.loadCustomer()
    this.loadNoOrder()
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

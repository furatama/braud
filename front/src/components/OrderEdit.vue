<template>
  <div>
    <div class="row q-col-gutter-sm">
      <div class="col-12">
        <div class="row justify-between">
          <div>No Order: <br/>{{data.no}}</div>
          <div>Customer: <br/>{{data.customer}}</div>
          <div>Tanggal Order: <br/>{{$date.formatDate(data.tanggal,'DD/MMMM/YYYY')}}</div>
          <div>Tanggal Kirim: <br/>{{$date.formatDate(data.tgl_kirim,'DD/MMMM/YYYY')}}</div>
          <div v-show="data.metode == 'credit'">Jatuh Tempo: <br/>{{$date.formatDate(data.due,'DD/MMMM/YYYY')}}</div>
          <div>Metode: <br/>{{data.metode == 'cash' ? 'Cash' : (data.metode == 'credit' ? 'Credit' : 'Transfer')}}</div>
          <div v-show="data.metode == 'credit'">Terbayar: <br/>{{$numeralCurrency(data.tunai)}}</div>
          <div>Status: <br/>{{getStatus}}</div>
        </div>
        <q-separator class="q-my-sm"/>

        <div class="row q-my-sm justify-between">
          <q-banner v-show="editMode" dense class="bg-yellow text-negative full-width">
            <template v-slot:avatar>
              <q-icon name="warning" color="negative" />
            </template>
            Mengubah/Menghapus order akan menghapus semua kredit yang telah dilakukan (jika metode pembayaran kredit)
          </q-banner>
          <q-btn v-show="!editMode && data.metode == 'transfer'" :disabled="nilaiTunai >= table.grandTotal" :label="nilaiTunai >= table.grandTotal ? 'SUDAH TERTRANSFER' : 'TERIMA TRANSFER'" :color="nilaiTunai >= table.grandTotal ? 'grey' : 'blue'" icon="send" @click="transfer.dialog = true"/>
          <q-btn v-show="!editMode && data.metode == 'credit'" :disabled="nilaiTunai >= table.grandTotal" label="BAYAR KREDIT" :color="nilaiTunai >= table.grandTotal ? 'grey' : 'lime'" icon="attach_money" @click="kredit.dialog = true"/>
          <q-btn v-show="!editMode" label="EDIT / HAPUS ORDER" color="warning" icon="edit" @click="editMode = true"/>
        </div>

        <div v-if="editMode" class="row q-gutter-md q-mb-sm">
          <div class="col-2">
            <q-input dense v-model="orderData.tanggal" outlined label="Tanggal Order" mask="####/##/##" placeholder="YYYY/MM/DD">
              <template v-slot:append>
                <q-icon name="event" class="cursor-pointer">
                  <q-popup-proxy>
                    <q-date v-model="orderData.tanggal"/>
                  </q-popup-proxy>
                </q-icon>
              </template>
            </q-input>
          </div>
          <div class="col-2">
            <q-input dense v-model="orderData.tgl_kirim" outlined label="Tanggal Kirim" mask="####/##/##" placeholder="YYYY/MM/DD">
              <template v-slot:append>
                <q-icon name="event" class="cursor-pointer">
                  <q-popup-proxy>
                    <q-date v-model="orderData.tgl_kirim"/>
                  </q-popup-proxy>
                </q-icon>
              </template>
            </q-input>
          </div>
          <div class="col-2">
            <q-select map-options dense outlined label="Metode Pembayaran" v-model="orderData.metode" :options="metodeOpts"/>
          </div>
          <div class="col-3" v-show="orderData.metode == 'credit'">
            <q-input dense v-model="orderData.due" outlined label="Jatuh Tempo (Credit Only)" mask="####/##/##" placeholder="YYYY/MM/DD">
              <template v-slot:append>
                <q-icon name="event" class="cursor-pointer">
                  <q-popup-proxy>
                    <q-date v-model="orderData.due"/>
                  </q-popup-proxy>
                </q-icon>
              </template>
            </q-input>
          </div>
        </div>

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
            <q-tr :props="props">
              <q-td v-for="(col) in props.cols" :key="col.name" :props="props">
                <template v-if="col.name == 'action'">
                  <q-btn :disabled="!editMode" icon="clear" :color="editMode? 'negative' : 'white'" flat dense @click="() => { table.data.splice(props.row.__index,1); refreshGrandTotal(); }">
                  </q-btn>
                </template>
                <template v-else-if="col.name == 'no'">
                  <q-avatar size="24px" class="text-white" color="primary">{{props.row.__index+1}}</q-avatar>
                </template>
                <template v-else-if="col.name == 'qty' && editMode">
                  <q-input @input="refreshSubtotal(table.data[props.row.__index])" type="number" dense v-model="table.data[props.row.__index][col.name]" input-class="text-right" style="width:40px;float:right"/>
                </template>
                <template v-else-if="col.name == 'diskon' && editMode">
                  <q-input @input="refreshSubtotal(table.data[props.row.__index])" type="number" dense v-model="table.data[props.row.__index][col.name]" input-class="text-right" style="width:50px;float:right">
                    <template v-slot:after>
                      <span class="text-body2">%</span>
                    </template>
                  </q-input>
                </template>
                <template v-else-if="col.name == 'produk' && editMode">
                  <select-filter @input="refreshSubtotal(table.data[props.row.__index])" dense outlined v-model="props.row.id_produk" :options="produkData"/>
                </template>
                <template v-else>
                  {{col.value}}
                  {{col.name == 'diskon' ? '%' : ''}}
                </template>
              </q-td>
            </q-tr>
          </template>
          <template v-slot:bottom>
            <div class="row full-width justify-between">
              <div>
                <q-btn color="primary" label="PRODUK" icon="add" v-show="editMode" @click="table.data.push({})"/>
              </div>
              <br/>
              <div class="row q-gutter-sm">
                <div style="margin-right:48px" class="text-right">
                  <q-input min="0" max="100" type="number" dense v-model="table.tax" input-class="text-right" style="width:50px;float:right" label="Tax" :readonly="!editMode">
                    <template v-slot:after>
                      <span class="text-body2">%</span>
                    </template>
                  </q-input>
                </div>
                <div style="margin-right:48px;margin-top:20px" class="text-right">
                  <span class="">Rp</span>
                  <span class="text-h5">{{$numeralCurrency(table.grandTotal)}}</span>
                </div>
              </div>
            </div>
          </template>
        </q-table>

        <div class="row q-mt-sm justify-between">
          <div v-show="!editMode"/>
          <q-btn v-show="editMode" label="HAPUS ORDER" color="negative" icon="delete">
            <q-popup-proxy :breakpoint="600">
              <q-banner inline-actions class="bg-negative text-white">
                Yakin?
                <template v-slot:action>
                  <q-btn flat label="Ya" @click="onDelete()"/>
                </template>
              </q-banner>
            </q-popup-proxy>
          </q-btn>

          <div class="row">
            <q-btn v-show="editMode" class="q-mr-md" label="SIMPAN" color="positive" icon="save" @click="onSubmit(false)"/>
            <q-btn :disabled="table.data.length <= 0" :label="editMode ? 'SIMPAN & CETAK NOTA' : 'CETAK NOTA'" color="positive" icon="print" @click="onSubmit"/>
          </div>
        </div>

      </div>
    </div>

    <q-dialog persistent v-model="kredit.dialog" @hide="() => {kredit.dialog = false}">
      <q-card style="width:50vw">
        <q-bar class="bg-primary text-white">
          <div class="text-h6">PEMBAYARAN KREDIT</div>

          <q-space />

          <q-btn dense flat icon="close" v-close-popup>
            <q-tooltip>Close</q-tooltip>
          </q-btn>
        </q-bar>

        <q-card-section>
          <q-input label="Sisa Kredit" :value="$numeralCurrency(hutang)" readonly input-class="text-right text-h5"/>
          <q-input label="Jumlah Bayar" v-model="kredit.paid" input-class="text-right text-h5"/>
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
          <q-input label="Sisa" :value="$numeralCurrency(sisaHutang)" readonly input-class="text-right text-h5"/>
        </q-card-section>

        <q-card-actions align="right" class="bg-primary text-white">
          <q-btn
            label="BAYAR HUTANG"
            color="positive"
            bordered
            icon="attach_money"
            :disable="kredit.paid <= 0"
            @click="submitKredit"
          >
          </q-btn>

        </q-card-actions>
      </q-card>
    </q-dialog>

    <q-dialog persistent v-model="transfer.dialog" @hide="() => {transfer.dialog = false}">
      <q-card style="width:50vw">
        <q-bar class="bg-primary text-white">
          <div class="text-h6">KONFIRMASI TRANSFER</div>

          <q-space />

          <q-btn dense flat icon="close" v-close-popup>
            <q-tooltip>Close</q-tooltip>
          </q-btn>
        </q-bar>

        <q-card-section>
          Apakah customer sudah mentransfer sejumlah Rp<span class="text-h5">{{$numeralCurrency(sisaHutang)}}</span>?
        </q-card-section>

        <q-card-actions align="right" class="bg-primary text-white">
          <q-btn
            label="SUDAH TRANSFER"
            color="positive"
            bordered
            icon="send"
            @click="submitTransfer"
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
  </div>
</template>

<script>
import SelectFilter from '../components/plugins/SelectFilter'
import PrintOut from '../components/OrderPrintOut'

export default {
  // name: 'PageName',
  props: {
    data: Object
  },
  components: {
    'select-filter' : SelectFilter,
    'print-out' : PrintOut,
  },
  data() {
    return {
      editMode: false,
      transfer: false,
      kredit: {
        detail: [],
        total: 0,
        paid: 0,
        dialog: false
      },
      transfer: {
        dialog: false
      },
      table: {
        columns: [
          { name: 'produk', align: 'left', label: 'PRODUK', field: 'produk', sortable: false },
          { name: 'harga', align: 'right', label: 'HARGA', field: 'harga', sortable: false },
          { name: 'qty', align: 'right', label: 'QTY', field: 'qty', sortable: false },
          { name: 'diskon', align: 'right', label: 'DISKON', field: 'diskon', sortable: false },
          { name: 'subtotal', align: 'right', label: 'SUBTOTAL', field: 'subtotal', sortable: false },
          { name: 'action', field: 'action', label: '', sortable: false, style: 'width:10px;padding:0'}
        ],
        pagination: {
          rowsPerPage: 0
        },
        data: [],
        grandTotal: 0,
        tax: 10,
      },
      produkData: [],
      defProdukData: [],
      customerData: {},
      orderData: [],
      filter: '',
      checkout: {
        isShow: false,
      },
      payment: {
        paid: 0,
        method: 'cash',
        options: [
          {label: 'Cash', value: 'cash'},
          {label: 'Kredit', value: 'credit'},
          {label: 'Transfer', value: 'transfer'},
        ],
      },
      metodeOpts: [
        {label: "Cash", value: "cash"},
        {label: "Credit", value: "credit"},
        {label: "Transfer", value: "transfer"},
      ],
      metodeMap: {
        cash: 'Cash',
        credit: 'Credit',
        transfer: 'Transfer',
      },
    }
  },
  computed: {
    getStatus() {
      if (this.data.metode == 'cash' || this.data.metode == 'transfer') {
        return (this.$numeralVal(this.data.tunai) >= this.$numeralVal(this.data.total) ? 'Lunas' : 'Belum Lunas')
      } else {
        return (this.$numeralVal(this.data.tunai) >= this.$numeralVal(this.data.total) ? 'Lunas' :
        (this.$date.getDateDiff(new Date(), this.data.due) > 0 ? 'Jatuh Tempo' : 'Belum Lunas'))
      }
    },
    tanggalOrder() {
      return this.orderData.tanggal
    },
    nilaiTunai() {
      return this.data.metode == 'cash' ? this.table.grandTotal : this.data.tunai
    },
    id() {
      return this.data.id
    },
    payMethod() {
      return this.payment.method
    },
    hutang() {
      return this.table.grandTotal - this.kredit.total
    },
    sisaHutang() {
      return this.hutang - this.kredit.paid
    },
    kreditPaid() {
      return this.kredit.paid
    },
    exchange() {
      const total = this.table.grandTotal
      const paid = this.data.tunai
      return paid - total
    },
    printData() {
      this.orderData.metode = this.orderData.hasOwnProperty('metode') ? (this.orderData.metode.hasOwnProperty('value') ? this.orderData.metode.value : this.orderData.metode) : 'cash'
      return {
        id_order: this.orderData.id,
        no: this.orderData.no,
        tanggal: this.$date.formatDate(this.orderData.tanggal, 'DD/MM/YYYY'),
        due: this.$date.formatDate(this.orderData.due, 'DD/MM/YYYY'),
        tgl_kirim: this.$date.formatDate(this.orderData.tgl_kirim, 'DD/MM/YYYY'),
        kasir: this.$store.getters.getName,
        method: this.metodeMap[this.orderData.metode],
        cust: {
          nama: this.customerData.nama,
          email: this.customerData.email,
          alamat: this.customerData.alamat,
          telepon: this.customerData.telepon,
        },
        tax: this.table.tax,
        total: this.$numeralCurrency(this.table.grandTotal),
        tunaiLabel: this.orderData.metode != 'credit' ? "Terbayar" : "Kredit",
        tunai: this.$numeralCurrency(this.orderData.tunai),
        sisaLabel: this.orderData.metode == 'cash' ? "Kembalian" : "Hutang",
        sisa: this.orderData.metode == 'cash' ? this.$numeralCurrency(this.exchange) : this.$numeralCurrency(-this.exchange),
        data: this.table.data.map((v) => {
          return {
            ...v, produk: v.id_produk.label || v.produk
          }
        })
      }
    },
  },
  watch: {
    kreditPaid(newV,oldV) {
      if (this.sisaHutang < 0) {
        this.kredit.paid = this.hutang
      }
      if (isNaN(newV)) {
        this.kredit.paid = oldV
      }
    },
    tanggalOrder(newV,oldV) {
      if (!this.$date.isSameDate(newV, this.$date.formatDate(this.data.tanggal,"YYYY/MM/DD"))) {
      //   this.$store.dispatch("fetch",{url: `/order/num`, params: {tanggal: newV, metode: this.payment.method}})
      //     .then((data) => {
      //       let n = (Number(data.count) + 1)
      //       n = n > 999 ? n.toString(36) : n.toString()
      //       this.orderData.no = "BA" + this.$date.formatDate(newV,"YYMMDD") + n.padStart(3,'0') + 'e'
      //     }).catch((error) => {
      //       console.log(error)
      //       this.$notifyNegative("Gagal Mengambil Data No Order")
      //     })
      // } else {
        this.orderData.no = this.data.no + 'e'
      }
    },
    "table.tax"() {
      this.refreshGrandTotal()
    }
  },
  methods: {
    onDelete() {
      this.$store.dispatch("deleteSingle",{url: 'order/data', id: this.data.id})
      .then((data) => {
        this.$emit('closeDialog')
        this.$notifyPositive('Order Berhasil Dihapus')
      }).catch((error) => {
        console.log(error)
        this.$notifyNegative('Ada Sebuah Kesalahan')
      })
    },
    loadCustomer() {
      this.$store.dispatch("fetchSingle",{url: '/customer/data', id:this.data.id_customer})
        .then((response) => {
          let data = response.data
          this.customerData = {
              nama: data.nama,
              id: data.id,
              alamat: data.alamat || '',
              telepon: data.email || '',
              email: data.email || '',
          }
        }).catch((error) => {
          console.log(error)
          this.$notifyNegative("Gagal Mengambil Data Customer")
        })
    },
    loadKredit() {
      this.$store.dispatch("fetch",{url: `/kredit/order/${this.data.id}`, params: {}})
        .then((response) => {
          this.kredit.detail = response.detail
          this.kredit.total = response.total
        }).catch((error) => {
          console.log(error)
          this.$notifyNegative("Gagal Mengambil Data Kredit")
        })
    },
    loadDetail() {
      this.$store.dispatch("fetch",{url: `/order-detail/data/${this.id}`, params: {}})
        .then((response) => {
          this.table.data = response.data.map((v) => {
            let subtotal = ((100 - v.diskon) / 100) * v.harga * v.qty
            return {
              ...v,
              harga: this.$numeralCurrency(v.harga),
              diskon: this.$numeralVal(v.diskon),
              hargaN: v.harga,
              subtotal: this.$numeralCurrency(subtotal),
              subtotalN: subtotal,
            }
          })
          this.refreshGrandTotal()
        }).catch((error) => {
          console.log(error)
          this.$notifyNegative("Gagal Mengambil Data Detail")
        })
    },
    loadProduk() {
      this.$store.dispatch("fetchOptions",{url: `/produk/customer/${this.data.id_customer}`})
        .then((response) => {
          let data = response.data
          this.produkData = data.map((v) => {
            return {
              ...v,
              label: v.nama,
              value: v.id
            }
          })
        }).catch((error) => {
          console.log(error)
          this.$notifyNegative("Gagal Mengambil Data Produk")
        })
    },
    refreshSubtotal(row) {
      row.hargaN = row.id_produk.harga || row.hargaN
      row.harga = this.$numeralCurrency(row.hargaN)
      row.qty = isNaN(row.qty) ? 1 : row.qty
      row.diskon = isNaN(row.diskon) ? 0 : row.diskon

      let subtotal = ((100 - row.diskon) / 100) * row.hargaN * row.qty
      row.subtotal = this.$numeralCurrency(subtotal)
      row.subtotalN = subtotal
      this.refreshGrandTotal()
    },
    refreshGrandTotal() {
      let total = 0
      const tax = this.table.tax
      this.table.data.forEach(v => {
        total += Number(v.subtotalN)
      });
      this.table.grandTotal = total * ((100 + Number(tax)) / 100)
      console.log(tax, this.table.grandTotal)
    },
    addMoney(amount) {
      if (amount == 'clear')
        this.kredit.paid = 0
      else if (amount == 'pas')
        this.kredit.paid = this.hutang
      else
        this.kredit.paid += Number(amount)
    },
    onSubmit(doPrint = true) {
      if (this.editMode) {
        this.orderData.metode = this.orderData.metode.hasOwnProperty('value') ? this.orderData.metode.value : this.orderData.metode
        let inputs = {
          no: this.orderData.no,
          id_customer: this.orderData.id_customer,
          metode: this.orderData.metode,
          tanggal: this.orderData.tanggal,
          due: this.orderData.metode == 'credit' ? this.orderData.due : null,
          faktur: '',
          tunai: this.orderData.metode == 'cash' ? this.table.grandTotal : 0,
          keterangan: '',
          detail: this.table.data.map((v) => {
            return {
              id_produk: v.id_produk.value || v.id_produk,
              harga: v.hargaN,
              qty: v.qty,
              diskon: v.diskon
            }
          })
        }
        this.$store.dispatch("updateSingle",{url: `/order/data`,id:this.data.id,inputs})
          .then((response) => {
            this.$emit('closeDialog')
            this.$notifyPositive("Berhasil Mengupdate Order")
          }).catch((error) => {
            console.log(error)
            this.$notifyNegative("Gagal Mengupdate Order")
          })
      }
      if (doPrint)
        this.printOrder()
    },
    submitKredit() {
      let inputs = {
        id_order: this.data.id,
        tunai: this.kredit.paid,
        keterangan: this.kredit.keterangan || '',
      }
      this.$store.dispatch("postSingle",{url: `/kredit/data`,inputs})
        .then((response) => {
          this.$emit('closeDialog')
          this.$notifyPositive("Berhasil Memasukkan Kredit")
        }).catch((error) => {
          console.log(error)
          this.$notifyNegative("Gagal Memasukkan Kredit")
        })
    },
    submitTransfer() {
      let inputs = {
        id_order: this.data.id,
        tunai: this.sisaHutang,
        keterangan: this.transfer.keterangan || '',
      }
      this.$store.dispatch("postSingle",{url: `/kredit/data`,inputs})
        .then((response) => {
          this.$emit('closeDialog')
          this.$notifyPositive("Berhasil Terima Transfer")
        }).catch((error) => {
          console.log(error)
          this.$notifyNegative("Gagal Terima Transfer")
        })
    },
    printOrder() {
      this.$refs.printer.print()
    }
  },
  mounted() {
    // this.loadNoOrder()
    setTimeout(() => {
      this.orderData = {
        ...this.data,
        tanggal: this.$date.formatDate(this.data.tanggal,"YYYY/MM/DD"),
        due: this.$date.formatDate(this.data.due,"YYYY/MM/DD"),
        tgl_kirim: this.$date.formatDate(this.data.tgl_kirim,"YYYY/MM/DD"),
      }
      this.table.tax = parseInt(this.data.tax || 0)
      this.loadCustomer()
      this.loadDetail()
      this.loadProduk()
      this.loadKredit()
    },1)
  }
}
</script>

<style lang="stylus">
  .q-table th
    opacity: 1 !important
</style>

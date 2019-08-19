<template>
  <q-page padding>
    <div class="row q-col-gutter-sm">
      <div class="col-9">
        <div class="row q-gutter-sm">
          <q-input outlined label="No Order"/>
          <q-input outlined label="Tanggal Order" mask="####/##/##" placeholder="YYYY/MM/DD">
            <template v-slot:append>
              <q-icon name="event" class="cursor-pointer">
                <q-popup-proxy>
                  <q-date />
                </q-popup-proxy>
              </q-icon>
            </template>
          </q-input>
          <q-select outlined label="Customer" value="Customer" :options="['Customer']"/>
        </div>
        <q-separator class="q-my-sm"/>

        <q-table
          :data="table.data"
          table-header-class="bg-secondary text-white"
          :columns="table.columns"
          dense
          class="my-sticky-header-table"
          :pagination.sync="table.pagination"
          no-data-label="Belum Ada Barang"            
        >
          <template v-slot:body="props">
            <q-tr :props="props">
              <q-td v-for="(col,index) in props.cols" :key="col.name" :props="props">
                <template v-if="col.name == 'action'">
                  <q-btn icon="clear" color="negative" flat dense @click="() => { table.data.splice(props.row.__index,1); refreshGrandTotal(); }">
                  </q-btn>
                </template>
                <template v-else-if="col.name == 'no'">
                  <q-avatar size="24px" class="text-white" color="primary">{{props.row.__index+1}}</q-avatar>
                </template>
                <template v-else-if="col.name == 'qty' ||col.name == 'diskon' || col.name == 'harga'">
                  <q-input @input="refreshSubtotal(table.data[props.row.__index])" type="number" dense v-model="table.data[props.row.__index].qty" input-class="text-right" style="width:80px;float:right"/>
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
                <span class="">Rp</span>
                <span class="text-h5">{{table.grandTotal}}</span>
              </div>
            </div>
          </template>
        </q-table>

        <div class="row q-mt-sm justify-end">
          <q-btn label="CHECKOUT" color="positive" icon="local_grocery_store" @click="() => {checkout.isShow = true}"/>
        </div>

      </div>
    <!-- <div class="flex q-gutter-xs">
      <q-btn size="sm" dense v-for="n in 30" :label="`bread asdf croissant ${n*10*n}`" icon="add"/>
    </div> -->
      <div class="col-3">
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

            <q-scroll-area style="height:75vh">
              <q-item  v-for="(value,index) in searchData" clickable v-ripple :key="index">
                <q-item-section @click="selectSearchedItem(value)">
                  <q-item-label>{{value.name}}</q-item-label>
                  <q-item-label caption>{{value.code}}</q-item-label>
                </q-item-section>
                <q-item-section side>{{`Rp ${$numeral(value.price).format('0,0')}`}}</q-item-section>
              </q-item>
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
          <q-input label="Total Harga" :value="table.grandTotal" readonly input-class="text-right text-h5"/>
          <div class="row q-my-sm">
            <span class="q-mt-sm">Metode Pembayaran : </span><q-option-group v-model="payment.method" :options="payment.options" color="primary" inline/>
          </div>
          <q-input label="Jumlah Bayar" v-model="payment.paidAmount" input-class="text-right text-h5"/>
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
          <q-input label="Kembalian" :value="exchange" readonly input-class="text-right text-h5"/>
        </q-card-section>

        <q-card-actions align="right" class="bg-primary text-white">
          <q-btn
            label="CETAK ORDER" 
            color="positive" 
            bordered 
            icon="print"
            :disable="$numeralVal(exchange) < 0" 
            @click="() => {submitSales(); printSales();}"
          >
          </q-btn>
          
        </q-card-actions>
      </q-card>
    </q-dialog>
    <!-- <div class="absolute-bottom full-width bg-accent shadow-3">
      asdf
    </div> -->
  </q-page>
</template>

<script>
export default {
  // name: 'PageName',
  data() {
    return {
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
      searchData: [{},{},{},{},{},{},{},{},{},{},{},{},{},{},{},{},{}],
      checkout: {
        isShow: false,
      },
      payment: {
        paid: 0,
        method: 'cash',
        options: [
          {label: 'Tunai', value: 'cash'},
          {label: 'Kredit', value: 'credit'},
        ]
      }
    }
  }
}
</script>

<style lang="stylus">
  .q-table th
    opacity: 1 !important
</style>

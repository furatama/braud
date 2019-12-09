<template>
  <q-page padding>
    <div class="row q-gutter-sm">
      <q-input dense outlined label="Orderan Tanggal" v-model="tglOrder" mask="####/##/##" placeholder="YYYY/MM/DD">
        <template v-slot:append>
          <q-icon name="event" class="cursor-pointer">
            <q-popup-proxy>
              <q-date v-model="tglOrder" />
            </q-popup-proxy>
          </q-icon>
        </template>
      </q-input>
      <q-btn label="PROSES" @click="procCalc" color="positive"/>
      <q-btn label="RESET" @click="resetCalc" color="info"/>
    </div>
    <q-separator class="q-my-sm"/>

    <div class="row q-gutter-sm">
      <div v-for="(resep,index) in reseps" :key="index">
      <q-card >
        <q-card-section class="bg-secondary text-white">
          <div style="min-width: 200px">
            <q-select dark
              outlined
              :label="`Resep #${index+1}`" @input="onInputResep"
              v-model="reseps[index]" :options="resepOpts"/>
          </div>
        </q-card-section>

        <template v-if="resep.hasOwnProperty('detail')">
          <q-card-section v-for="(detail,idx) in resep.detail" :key="idx" class="bg-accent q-py-xs" style="border-bottom: 1px grey solid">
            <div class="q-px-xs q-py-xs">
              <q-input mask="####" input-class="text-right" outlined type="number" :label="`@${detail.berat}`" v-model="reseps[index].detail[idx].loaf" dense/>
            </div>
          </q-card-section>
          <q-card-section class="q-mt-md bg-primary text-white">
            <div class="column q-px-xs q-gutter-md">
              <q-input dark label="Total Berat" readonly dense input-class="text-right" outlined :value="totalBerat(resep)"/>
              <q-input dark label="Total Resep" readonly dense input-class="text-right" outlined :value="totalResep(resep)"/>
            </div>
          </q-card-section>
        </template>
      </q-card>
      </div>
    </div>
    <div class="absolute-bottom-right q-pa-sm">
      <q-btn label="PRINT" icon="print" @click="printOrder" color="positive"/>
    </div>
    <print-out 
      ref="printer" 
      :data="printData"
    />
  </q-page>
</template>

<script>

import {Decimal} from 'decimal.js';
export default {
  // name: 'Resep',
  components: {
    'form-table' : () => import('../components/plugins/FormTable.vue'),
    'select-filter' : () => import('../components/plugins/SelectFilter.vue'),
    'print-out' : () => import('../components/ResepPrintOut.vue'),
  },
  data() {
    return {
      dataBahan: [],
      
      reseps: [{}],
      resepOpts: [],
      tglOrder: '',

      watchOnce: false,
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
    printData() {
      let data = this.reseps
      .filter(v => Object.keys(v).length > 0)
      .map(v => {
        return {
          nama: v.nama + ' @' + v.berat + 'g ',
          berat: this.totalBerat(v),
          resep: this.totalResepCeil(v),
        }
      })
      return {
        data
      }
    }
  },
  watch: {
    // reseps : {
    //   handler(val){
    //     if (this.watchOnce == false) {
    //       this.watchOnce = true
    //       this.calculateFromBahan()
    //       setTimeout(() => {
    //         this.watchOnce = false
    //       }, 100);
    //     }
    //   },
    //   deep: true
    // }
  },
  methods: {
    onInputResep(val) {
      let hasEmpty = false
      this.reseps.forEach(v => {
        if (!v.hasOwnProperty('detail')) {
          hasEmpty = true
        }
      })
      if (!hasEmpty) {
        this.reseps.push({})
      }
    },
    totalBerat(resep) {
      // return 0
      const berat = resep.berat
      return resep.detail.reduce((total,v) => {
        const ratio = v.ratio
        const loaf = v.hasOwnProperty('loaf') ? (v.loaf ? v.loaf : 0) : 0
        // total += (berat / Number(ratio)) * Number(loaf)
        total = (new Decimal(berat)).dividedBy(ratio).times(loaf).add(total).toFixed(2)
        return total
      }, 0)
    },
    totalResep(resep) {
      let n = (new Decimal(this.totalBerat(resep))).dividedBy(resep.berat)
      return `${n.toFixed(2)} (${n.ceil()})`
    },
    totalResepCeil(resep) {
      let n = (new Decimal(this.totalBerat(resep))).dividedBy(resep.berat)
      return n.ceil()
    },
    async resetCalc() {
      const response = await this.$store.dispatch("fetchOptions",{url: `/resep-produk/options`})
      const data = response.data
      this.resepOpts = data.map((v) => {
        return {
          ...v,
          label: v.nama + ' (' + v.berat + 'g) ',
          value: v.id
        }
      })
      this.reseps = [{}]
    },
    async procCalc() {
      const response = await this.$store.dispatch("fetch",{
        url: `/resep-produk/proses`, params: {
          tanggal: this.tglOrder
       }
      })
      const data = response.data
      this.resepOpts = data.map((v) => {
        return {
          ...v,
          label: v.nama + ' (' + v.berat + 'g) ',
          value: v.id
        }
      })
      this.reseps = this.resepOpts.filter(v => {
        let loaf = v.detail.reduce((total,d) => total += Number(d.loaf), 0)
        return loaf > 0
      }).concat({})
    },
    printOrder() {
      this.$refs.printer.print()
    },
    calculateFromN() {
      this.dataBahan = this.dataBahan.map(v => {
        let batch = (new Decimal(v.diperlukan)).times(this.nResep)
        // let batch = v.diperlukan * this.nResep
        return {
          ...v,
          batch: batch,
          terpakai: batch,
          sisa: 0,
          budget: (new Decimal(v.harga)).times(batch),
          efektif: (new Decimal(v.harga)).times(batch),
        }
      })
    },
    calculateFromBahan() {
      let n = 9999
      this.dataBahan.forEach(v => {
        const c = (new Decimal(v.batch)).dividedBy(v.diperlukan)
        n = Math.min(n,c)
      });
      n = Math.floor(n)
      this.dataBahan = this.dataBahan.map(v => {
        const terpakai = v.diperlukan * n
        return {
          ...v,
          terpakai: terpakai,
          sisa: (new Decimal(v.batch)).minus(terpakai),
          budget: (new Decimal(v.harga)).times(v.batch),
          efektif: (new Decimal(v.harga)).times(terpakai),
        }
      })
      this.nResep = n
    },
    loadBahan() {
      this.$store.dispatch("fetch",{url: `/bahan/resep/${this.produk.value}`})
        .then((response) => {
          let data = response.data
          this.nResep = 1
          this.dataBahan = data.map(v => {
            return {
              ...v,
              diperlukan: v.batch,
              terpakai: v.batch,
              sisa: 0,
              budget: (new Decimal(v.harga)).times(v.batch),
              efektif: (new Decimal(v.harga)).times(v.batch),
            }
          })
        }).catch((error) => {
          console.log(error)
          this.$notifyNegative("Gagal Mengambil Data Bahan")
        })
    },
  },
  async mounted() {
    const response = await this.$store.dispatch("fetchOptions",{url: `/resep-produk/options`})
    const data = response.data
    this.resepOpts = data.map((v) => {
      return {
        ...v,
        label: v.nama + ' (' + v.berat + 'g) ',
        value: v.id
      }
    })
  },

}
</script>

<style>
</style>

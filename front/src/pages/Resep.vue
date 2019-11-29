<template>
  <q-page padding>
    <div class="column q-gutter-md">
      <div>
        <select-filter outlined label="Produk" v-model="produk" :options="produkOpts" :loading="loading"/>
      </div>
      <div>
        <q-input outlined label="Jumlah Resep" type="number" v-model="nResep"/>
      </div>
      <div>
        <form-table
          :columns="cols"
          :data.sync="dataBahan"
          class="bg-secondary text-white q-mb-sm"
          table-header-class="bg-secondary text-white"
          table-class="bg-white text-black"
        >
          <template v-slot:bottom-row>
            <q-tr class="bg-grey-4">
              <q-td>
                <b>Jumlah</b>
              </q-td>
              <q-td align="right">
                <b>{{jmlPerResep}}</b>
              </q-td>
              <q-td align="right">
                <b>{{jmlBatch}}</b>
              </q-td>
              <q-td align="right">
                <b>{{jmlTerpakai}}</b>
              </q-td>
              <q-td align="right">
                <b>{{jmlSisa}}</b>
              </q-td>
              <q-td align="right">
                <b>{{jmlBudget}}</b>
              </q-td>
              <q-td align="right">
                <b>{{jmlEfektif}}</b>
              </q-td>
            </q-tr>
          </template>
        </form-table>
      </div>
    </div>
  </q-page>
</template>

<script>


export default {
  // name: 'Resep',
  components: {
    'form-table' : () => import('../components/plugins/FormTable.vue'),
    'select-filter' : () => import('../components/plugins/SelectFilter.vue'),
  },
  data() {
    return {
      dataBahan: [],
      cols: [
        { name: 'bahan', label: 'Bahan', attr: {style: 'font-weight:bold'}, align: 'left' },
        { name: 'diperlukan', label: 'Batch/Rasio/1-Resep', align: 'right' },
        { name: 'batch', type: 'number', label: 'Batch/Rasio', attr: {step: '0.001', dense: true}, align: 'right' },
        { name: 'terpakai', label: 'Jumlah Terpakai', align: 'right' },
        { name: 'sisa', label: 'Jumlah Sisa', align: 'right' },
        { name: 'budget', label: 'Budget Batch', align: 'right' },
        { name: 'efektif', label: 'Budget Efektif', align: 'right' },
      ],
      produkOpts: [],
      produk: '',
      nResep: 0,
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
    jmlPerResep() {
     return  this.dataBahan.reduce((total,element) => element.diperlukan * 1.0 + total * 1.0, 0.00)
    },
    jmlBatch() {
     return  this.dataBahan.reduce((total,element) => element.batch * 1.0 + total * 1.0, 0.00)
    },
    jmlTerpakai() {
     return  this.dataBahan.reduce((total,element) => element.terpakai * 1.0 + total * 1.0, 0.00)
    },
    jmlSisa() {
     return this.jmlBatch - this.jmlTerpakai
    },
    jmlBudget() {
     return  this.dataBahan.reduce((total,element) => element.harga * element.batch, 0.00)
    },
    jmlEfektif() {
     return  this.dataBahan.reduce((total,element) => element.harga * element.terpakai, 0.00)
    }
  },
  watch: {
    produk() {
      this.loadBahan()
    },
    nResep() {
      if (this.watchOnce == false) {
        this.watchOnce = true
        this.calculateFromN()
        setTimeout(() => {
          this.watchOnce = false
        }, 100);
      }
    },
    dataBahan : {
      handler(val){
        if (this.watchOnce == false) {
          this.watchOnce = true
          this.calculateFromBahan()
          setTimeout(() => {
            this.watchOnce = false
          }, 100);
        }
      },
      deep: true
    }
  },
  methods: {
    calculateFromN() {
      this.dataBahan = this.dataBahan.map(v => {
        let batch = v.diperlukan * this.nResep
        return {
          ...v,
          batch: batch,
          terpakai: batch,
          sisa: 0,
          budget: v.harga * batch,
          efektif: v.harga * batch,
        }
      })
    },
    calculateFromBahan() {
      let n = 9999
      this.dataBahan.forEach(v => {
        const c = v.batch / v.diperlukan
        n = Math.min(n,c)
      });
      n = Math.floor(n)
      this.dataBahan = this.dataBahan.map(v => {
        const terpakai = v.diperlukan * n
        return {
          ...v,
          terpakai: terpakai,
          sisa: v.batch - terpakai,
          budget: v.harga * v.batch,
          efektif: v.harga * terpakai,
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
              budget: v.harga * v.batch,
              efektif: v.harga * v.batch,
            }
          })
        }).catch((error) => {
          console.log(error)
          this.$notifyNegative("Gagal Mengambil Data Bahan")
        })
    },
  },
  async mounted() {
    const response = await this.$store.dispatch("fetchOptions",{url: `/resep/options`})
    const data = response.data
    this.produkOpts = data.map((v) => {
      return {
        label: v.produk,
        value: v.id
      }
    })
  },

}
</script>

<style>
</style>

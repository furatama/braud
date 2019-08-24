<template>
  <form @submit.prevent="onSubmit">
    <q-field filled stack-label label="Customer">
      <template v-slot:control>
        <div class="self-center full-width no-outline" tabindex="0">{{data.nama}}</div>
      </template>
    </q-field>
    <div class="row" style="height:auto">
      <div class="col-6 row q-col-gutter-md" v-for="(row,index) in listHarga" :key="index">
        <div class="q-mt-md"><q-avatar size="20px" class="q-ma-none q-pa-none text-anti-primary" color="primary">{{index+1}}</q-avatar></div>
        <div><select-filter label="Nama Produk" v-model="row.produk" :options="produkOpts" style="width:225px"/></div>
        <div><q-input type="number" label="Harga" v-model="row.harga" style="width:80px" input-class="text-right"/></div>
        <div class="q-mt-md">
          <q-btn flat dense icon="delete" color="negative" @click="removeHarga(index)"/>
        </div>
      </div>
      <div class="col-6 row q-col-gutter-md" v-if="listHarga.length < produkOpts.length">
        <div class="q-mt-md">
          <q-avatar size="20px" class="q-ma-none q-pa-none text-anti-primary" color="primary">{{listHarga.length+1}}</q-avatar>
        </div>
        <div class="q-mt-md">
          <q-btn class="full-width" icon="add" label="produk" @click="newHarga()"/>
        </div>
      </div>
    </div>
    <q-separator class="q-my-md" />
    <div class="row justify-end">
      <q-btn type="submit" color="positive" :label="`Submit Harga ${data.nama}`"/>
    </div>
    <q-inner-loading :showing="loading">
      <q-spinner-dots size="50px" color="primary" />
    </q-inner-loading>
  </form>
</template>

<script>
import SelectFilter from './plugins/SelectFilter'

export default {
  // name: 'ComponentName',
  props: {
    data: [Object,Array]
  },
  components: {
    'select-filter' : SelectFilter
  },
  data () {
    return {
      listHarga: [],
      produkOpts: []
    }
  },
  computed: {
    loading: {
      get() {
        return this.$store.state.loading 
      }
    }
  },
  methods: {
    newHarga() {
      if (this.listHarga.length < this.produkOpts.length) {
        this.listHarga.push({
          produk: '',
          harga: '',
        })
      }
    },
    removeHarga(index) {
      this.listHarga.splice(index,1)
    },
    fillOptions(index) {
      this.$store.dispatch("fetchOptions",{url: '/produk/all'})
        .then((response) => {
          let data = response.data
          this.produkOpts = data.map((v) => {
            return {
              label: v.aktif == 1 ? v.nama : v.nama + ' (<b>NONAKTIF</b>)',
              value: v.id,
              aktif: v.aktif
            }
          })
        }).catch((error) => {
          console.log(error)
          this.$notifyNegative("Gagal Mengambil Data Produk")
        })
    },
    onSubmit() {
      let dups = this.$array_duplicate(this.listHarga,(item) => item.produk.value ? item.produk.value : item.produk)
      
      if (dups.length > 0) {
        this.$notifyNegative("Ada item yang sama")
        return
      }

      let id = this.data.id
      let submitData = this.listHarga.map((input) => {
        return {
          id_customer: id,
          id_produk: input.produk.value ? input.produk.value : input.produk,
          harga: input.harga
        }
      })

      this.$store.dispatch("postSingle",{url: `/customer/${this.data.id}/harga`, inputs: {harga: submitData} })
        .then((response) => {
          this.$notifyPositive("Harga Berhasil Diupdate")
        }).catch((error) => {
          console.log(error)
          this.$notifyNegative("Gagal Mengirim Data Harga")
        })
    },
    loadHarga() {
      this.$store.dispatch("fetchAll",{url: `/customer/${this.data.id}/harga`})
        .then((response) => {
          let data = response.data
          this.listHarga = data.map((v) => {
            return {
              produk: v.id_produk,
              harga: this.$numeralVal(v.harga),
            }
          })
        }).catch((error) => {
          console.log(error)
          this.$notifyNegative("Gagal Mengambil Data Harga")
        })
    }
  },
  mounted() {
    setTimeout(() => {
      this.fillOptions()
      this.loadHarga()
    }, 1)
  }

}
</script>

<style>
</style>

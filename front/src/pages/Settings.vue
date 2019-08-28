<template>
  <q-page padding>
    <span class="text-h4">Pengaturan Akun</span>
    <q-separator class="q-mt-sm q-mb-lg"/>
    <form @submit.prevent="updateInfo" class="column q-col-gutter-md q-px-xl">
      <q-input outlined v-model="username" :maxlength="10" label="Username"/>
      <q-input outlined v-model="nama" :maxlength="15" label="Nama"/>
      <div class="row justify-end">
        <q-btn type="submit" label="Update Akun" color="positive"/>
      </div>
    </form>
    <q-separator class="q-my-lg"/>
    <form @submit.prevent="updatePassword" class="column q-col-gutter-md q-px-xl">
      <span class="text-h5">Ganti Password</span>
      <q-input outlined v-model="oldPassword" type="password" label="Password Lama"/>
      <q-input outlined v-model="password" type="password" label="Password Baru"/>
      <div class="row justify-end">
        <q-btn type="submit" label="Update Password" color="positive"/>
      </div>
    </form>
    <q-separator class="q-my-lg"/>
    <form @submit.prevent="updatePrintout" class="column q-col-gutter-md q-px-xl">
      <span class="text-h5">Printout</span>
      <div class="row q-col-gutter-md">
        <q-input outlined v-model="printout.margin.left" label="Margin Left"/>
        <q-input outlined v-model="printout.margin.top" label="Margin Top"/>
      </div>
      <q-input outlined v-model="printout.font.one" label="Font Family 1"/>
      <q-input outlined v-model="printout.font.two" label="Font Family 2"/>
      <q-input outlined v-model="printout.font.three" label="Font Family 3"/>
      <q-input outlined v-model="printout.width" label="Print Width"/>
      <q-input outlined v-model="printout.rows" label="Limit Item Per Nota"/>
      <q-input outlined v-model="printout.store.name" label="Nama Usaha"/>
      <q-input outlined v-model="printout.store.address" label="Alamat Usaha"/>
      <q-input outlined v-model="printout.store.phone" label="Telpon Usaha"/>
      <q-input outlined v-model="printout.store.email" label="Email Usaha"/>
      <div class="row justify-end">
        <q-btn type="submit" label="Update Printout" color="positive"/>
      </div>
    </form>
    <q-inner-loading :showing="loading">
      <q-spinner-dots size="50px" color="primary" />
    </q-inner-loading>
  </q-page>
</template>

<script>
export default {
  name: 'Settings',
  data() {
    return {
      username: '',
      nama: '',
      oldPassword: '',
      password: '',
      printout: {}
    }
  },
  computed: {
    loading: {
      get() {
        return this.$store.state.loading 
      }
    },
    // printout() {
    //   return this.$store.getters.getPrintout
    // }
  },
  methods: {
    loadInfo() {
      this.$store.dispatch("postSingle",{url: `/auth/me`})
        .then((response) => {
          let data = response
          this.username = data.username
          this.nama = data.name
        }).catch((error) => {
          console.log(error)
          this.$notifyNegative("Gagal Mengambil Informasi")
        })
    },
    updatePassword() {
      let inputs = {
        oldPassword: this.oldPassword,
        password: this.password
      }
      this.$store.dispatch("postSingle",{url: `/auth/update-password`, inputs})
        .then((response) => {
          let data = response.data
          this.$notifyPositive("Berhasil Mengupdate Password")
          this.oldPassword = ''
          this.password = ''
        }).catch((error) => {
          console.log(error)
          this.$notifyNegative("Gagal Mengupdate Password")
        })
    },
    updateInfo() {
      let inputs = {
        username: this.username,
        name: this.nama
      }
      this.$store.dispatch("postSingle",{url: `/auth/update-info`, inputs})
        .then((response) => {
          let data = response.data
          this.$store.commit("rename",{name: data.name})
          this.$notifyPositive("Berhasil Mengupdate Informasi")
        }).catch((error) => {
          console.log(error)
          this.$notifyNegative("Gagal Mengupdate Informasi")
        })
    },
    updatePrintout() {
      this.$store.commit('setPrintoutSettings', {
        margin : {
          left: this.printout.margin.left,
          top: this.printout.margin.top,
        },
        font : {
          one: this.printout.font.one,
          two: this.printout.font.two,
          three: this.printout.font.three,
        },
        width: this.printout.width,
        rows: this.printout.rows,
        store: {
          name: this.printout.store.name,
          address: this.printout.store.address,
          phone: this.printout.store.phone,
          email: this.printout.store.email,
        },
      })
    }
  },
  created() {
    this.loadInfo()
    this.printout = {
      margin : {
        left: this.$store.state.printout.margin.left,
        top: this.$store.state.printout.margin.top,
      },
      font : {
        one: this.$store.state.printout.font.one,
        two: this.$store.state.printout.font.two,
        three: this.$store.state.printout.font.three,
      },
      width: this.$store.state.printout.width,
      rows: this.$store.state.printout.rows,
      store: {
        name: this.$store.state.printout.store.name,
        address: this.$store.state.printout.store.address,
        phone: this.$store.state.printout.store.phone,
        email: this.$store.state.printout.store.email,
      },
    }
  },
}
</script>

<style>
</style>

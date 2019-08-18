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
      password: ''
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
    }
  },
  created() {
    this.loadInfo()
  },
}
</script>

<style>
</style>

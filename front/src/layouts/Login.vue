<template>
  <div class="q-py-lg row justify-center">
    <div class="shadow-1 col-md-5 col-sm-11">
      <form @submit.prevent="auth">
        <h4 class="q-pa-lg q-ma-none text-center text-anti-primary bg-primary">Login Form</h4>
        <div class="q-pa-lg">
          <q-input v-model="username" name="username" type="text" label="Username"/>
          <q-input v-model="password" name="password" :type="isPwd ? 'password' : 'text'" label="Password">
            <template v-slot:append>
              <q-icon
                :name="isPwd ? 'visibility_off' : 'visibility'"
                class="cursor-pointer"
                @click="isPwd = !isPwd"
              />
            </template>
          </q-input>
          <div class="row reverse justify-between q-mt-lg">
            <q-btn class="text-anti-primary bg-primary" label="login" type="submit" style="width:175px" />
          </div>
        </div>
        <slot></slot>
      </form>
    </div>
    
  </div>
</template>

<script>
export default {
  name: "LoginForm",
  data() {
    return {
      username: '',
      password: '',
      isPwd: true,
    }
  },
  methods: {
    error(msg) {
      console.log(msg)
      this.$notifyNegative(`Login Gagal [Username/Password Salah]`)
    },
    success(data) {
      this.$notifyPositive(`Login Berhasil`)
    },
    auth() {
      this.$store.dispatch('requestAuth', {
        username: this.username,
        password: this.password
      }).then((data) => {
        this.success(data)
      }).catch((msg) => {
        this.error(msg)
      })
    },
  },
  created() {
    console.log(this.$store.state.auth)
  }
}
</script>
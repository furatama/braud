<template>
  <q-layout view="hHh Lpr lFf">
    <q-header elevated>
      <q-toolbar class="title-brand">
        <q-btn
          outline
          dense
          round
          @click="leftDrawerOpen = !leftDrawerOpen"
          aria-label="Menu"
          style="background-color:grey !important"
        >
          <q-icon name="img:statics/app-logo-128x128.png" />
        </q-btn>

        <q-toolbar-title>
          Braud Artisan Bakery
        </q-toolbar-title>

        <div>v1.7.0322</div>
      </q-toolbar>
    </q-header>

    <q-drawer
        v-model="leftDrawerOpen"
        :width="250"
        :breakpoint="400"
        show-if-above
        content-class="bg-secondary"
      >
      <q-scroll-area style="height: calc(100% - 85px); margin-top: 85px; border-right: 1px solid #ddd">
        <q-list bordered>
          <template v-for="link in links" >
            <q-item v-if="!link.child && (roleChecker(link.role))" :key="link.label"
              clickable v-ripple active-class="text-grey" class="link-brand" :to="link.to"
            >
              <q-item-section avatar>
                <q-icon :name="link.icon" />
              </q-item-section>
              <q-item-section>{{link.label}}</q-item-section>
            </q-item>
            <q-expansion-item
              v-if="link.child && (roleChecker(link.role))"
              :icon="link.icon"
              :label="link.label"
              :key="link.label"
              header-class="link-brand"
              :content-inset-level="0.25"
            >
              <template v-for="child in link.child">
                <q-expansion-item v-if="child.menu && (roleChecker(child.role))" group="inner" :label="child.label" header-class="text-black" :key="child.label" :content-inset-level="0.5" :default-opened="child.active">
                  <q-item v-for="mlink in child.menu"
                    clickable
                    v-ripple
                    header-class="link-brand"
                    :key="mlink.label"
                    dense
                  >
                    <q-item-section class="link-brand">{{mlink.label}}</q-item-section>
                  </q-item>
                </q-expansion-item>

                <q-item v-if="!child.menu && (roleChecker(child.role))"
                  clickable
                  v-ripple
                  header-class="link-brand"
                  :to="child.to"
                  :key="child.label"
                >
                  <q-item-section class="link-brand-2">{{child.label}}</q-item-section>
                </q-item>
              </template>

            </q-expansion-item>
            <q-separator v-if="roleChecker(link.role)" :key="'sep' + link.label" />
          </template>


        </q-list>
      </q-scroll-area>

      <div class="absolute-top bg-accent" style="height: 85px">
        <div class="q-pa-sm bg-transparent title-brand-2 row justify-between">
          <q-avatar size="56px" icon="face">
          </q-avatar>
          <div class="text-weight-bold column flex q-pt-md">
            <div class="col ">Halo, {{user}}</div>
            <div class="col text-caption">
              <div class="row justify-between">
                <q-btn dense color="indigo"  size="sm" flat icon="settings" label="PENGATURAN" to="/settings"/>
                <q-btn dense color="negative" size="sm" flat icon="meeting_room" label="LOGOUT" @click="$store.dispatch('revokeAuth')"/>
              </div>
            </div>
          </div>

        </div>
      </div>
    </q-drawer>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script>

export default {
  name: 'MyLayout',
  data () {
    return {
      leftDrawerOpen: this.$q.platform.is.desktop,
      links: [],
    }
  },
  computed: {
    user: {
      get() { return this.$store.getters.getName }
    },
    role: {
      get() { return this.$store.getters.getRole }
    }
  },
  methods: {
    roleChecker(role) {
      if (this.role === 'superadmin')
        return true
      return (role && role.includes(this.role)) || !role
    }
  },
  created() {
    this.links = [
      {
        label: 'Index',
        to: '/home',
        icon: 'home',
      },
      {
        label: 'Kasir',
        to: '/kasir',
        icon: 'store',
        role: ['admin','kasir']
      },
      {
        label: 'Order',
        to: '/order',
        icon: 'list_alt',
        role: ['admin','kasir']
      },
      {
        label: 'Invoice',
        icon: 'insert_drive_file',
        child: [
          {label: 'Pembuatan', to: '/invoice'},
          {label: 'Pelunasan', to: '/invoice-lunas'},
        ],
        role: ['admin','kasir']
      },
      {
        label: 'Report',
        icon: 'import_contacts',
        child: [
          {label: 'Customer', to: '/report/customer'},
          {label: 'Order', to: '/report/order'},
          {label: 'Produk', to: '/report/produk'},
          {label: 'Produk Per Cust', to: '/report/customerproduk'},
        ],
        role: ['admin','kasir']
      },
      {
        label: 'Hitung Resep',
        to: '/resep',
        icon: 'cake',
        role: ['admin','bakery']
      },
      {
        label: 'Master',
        icon: 'storage',
        child: [
          {label: 'Customer', to: '/master/customer', role: ['admin','kasir']},
          {label: 'Produk', to: '/master/produk', role: ['admin','kasir','bakery']},
          {label: 'Resep', to: '/master/resepproduk', role: ['admin','bakery']},
          {label: 'Resep Detail', to: '/master/resepdetail', role: ['admin','bakery']},
          // {label: 'Bahan', to: '/master/bahan', role: ['admin','bakery']},
          {label: 'Staff', to: '/master/staff', role: ['admin']},
				]
      },
    ]
  }
}
</script>

<style>
</style>

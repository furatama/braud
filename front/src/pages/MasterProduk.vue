<template>
  <q-page padding>
    <q-dialog v-model="formDialog">
      <q-card style="min-width:40vw">
        <q-bar class="bg-primary text-white">
          <div class="text-h6">test</div>
          <q-space />
          <q-btn dense flat icon="close" v-close-popup>
            <q-tooltip>Close</q-tooltip>
          </q-btn>
        </q-bar>
        <q-card-section>
          <div>
            <q-input label="asdf"/>
            <q-select v-model="input.value" :options="input.options" map-options :label="input.label">
              <template v-slot:append>
                <q-btn round dense flat icon="add"  />
              </template>
            </q-select>
          </div>
        </q-card-section>
        <q-card-actions>
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
export default {
  // name: 'PageName',
  data() {
    return {
      formDialog: false,
      input: {
        label: 'sarang',
        value: 'gg',
        options: [
          {label: 'a',
          value: 1}
        ]
      }
    }
  },
  mounted() {
    this.formDialog = true
  }
}
</script>

<style>
</style>

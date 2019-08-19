<template>
  <q-select 
    v-model="model" 
    :dense="dense" 
    :outlined="outlined"
    :options="filteredOptions"
    :label="label"
    map-options
    use-input
    hide-selected
    fill-input
    @filter="(val, update) => {urlAPI ? filterDS(val,update) : filterNoDS(val,update)}"
    @input="input"
  >
    <template v-slot:no-option>
      <q-item>
        <q-item-section class="text-grey">
          Tidak Ada Hasil
        </q-item-section>
      </q-item>
    </template>
    <!-- <template v-if="row.component" v-slot:append>
      <q-btn round dense flat icon="add" @click="showDialog(row)" />
    </template> -->
  </q-select>
</template>

<script>
export default {
  name: 'SelectFilter',
  props: {
    dense: Boolean,
    label: String,
    urlAPI: String,
    outlined: Boolean,
    options: Array,
    value: [Number,String,Object]
  },
  data () {
    return {
      model: null,
      filteredOptions: [],
      defaultOptions: null
    }
  },
  methods: {
    filterNoDS(val, update) {
      if (val === '') {
        update(() => {
          this.filteredOptions = this.defaultOptions
        })
        return
      }

      update(() => {
        const needle = val.toLowerCase()
        this.filteredOptions = this.defaultOptions.filter(v => v.label.toLowerCase().indexOf(needle) > -1)
      })
    },

    filterDS(val, update) {
      if (val === '' && this.defaultOptions !== null) {
        update(() => {
          this.filteredOptions = this.defaultOptions
        })
        return
      }

      this.$store.dispatch("getOptions",{url: this.urlAPI})
        .then((result) => {
          this.defaultOptions = result.data
      }).finally(() => {

        update(() => {
          const needle = val.toLowerCase()
          this.filteredOptions = this.defaultOptions.filter(v => v.label.toLowerCase().indexOf(needle) > -1)
        })

      })

    },

    input(val) {
      this.$emit('input',val)
    },
  },
  watch: {
    options(val) {
      this.defaultOptions = [...this.options]
      this.model = this.options.find((el) => {
        // if (el.value == this.value) {
        //   console.log(el.value,this.value)
        // }
        return el.value == this.value
      })
    },
    value(val) {
      console.log('value',val)
      this.model = val
    }
  },
  mounted() {
    if (this.options) {
      this.defaultOptions = [...this.options]
      // console.log('options',this.options)
      this.model = this.options.find((el) => {
        return el.value == this.value
      })
    }
    // this.model = this.value
  },
}
</script>

<style>
</style>

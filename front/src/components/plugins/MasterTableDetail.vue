<template>
  <div>
    <div class="flex q-gutter-sm" v-for="(val,index) in value" :key="index">
      <div v-for="(input,inpIndex) in detail" :key="inpIndex">
        <template v-if="input.type == 'text' || !input.type">
          <q-input :label="input.label + ' ' + (index+1)" :value="gVal(val,input.name)" @input="(v) => onInput(input.name,index,v)" />
        </template>
        <template v-else-if="input.type == 'number'">
          <q-input type="number" :label="input.label + ' ' + (index+1)" :value="gVal(val,input.name)" @input="(v) => onInput(input.name,index,v)" />
        </template>
        <template v-else-if="input.type == 'select'">
          <q-select style="width:200px" :options="input.options" map-options emit-value :label="input.label + ' ' + (index+1)" :value="gVal(val,input.name)" @input="(v) => onInput(input.name,index,v)">
          </q-select>
        </template>
        <template v-else-if="input.type == 'decimal'">
          <q-input type="number" :label="input.label + ' ' + (index+1)" :value="gVal(val,input.name)" @input="(v) => onInput(input.name,index,v)" step="0.01"  />
        </template>
      </div>
    </div>
    <q-btn class="q-pa-xs q-my-xs" dense icon="add" color="info" @click="addRow"/>
  </div>
</template>

<script>
export default {
  // name: 'ComponentName',
  props: {
    value: Array,
    detail: Array
  },
  methods: {
    onInput(name,index,value) {
      // console.log(name,index,value)
      let item = [...this.value]
      item[index][name] = value.hasOwnProperty('value') ? value.value : value
      // console.log(item, 'item')
      this.$emit('input', item)
    },
    gVal(val,name) {
      // console.log(val, name, val[name], 'input')
      return val.hasOwnProperty(name) ? val[name] : ''
    },
    addRow() {
      this.$emit('input',this.value.concat({}))
    },
  },
  data () {
    return {
      
    }
  },
  mounted() {
    // console.log(this.value, 'val')
    // console.log(this.detail, 'det')
  }
}
</script>

<style>
</style>

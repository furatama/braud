<template>
  <x-table
    :hide-bottom="!$scopedSlots.hasOwnProperty('bottom')"
    dense
    v-bind="$attrs"
    v-on="$listeners"
    :data="data"
    :pagination.sync="noPagination"
  >
    <template v-slot:body="props">
      <q-tr :props="props">
        <q-td v-for="col in props.cols" :key="col.name" :props="props">
          <template v-if="col.type === 'selection'">
            <q-checkbox v-model="props.row[col.name]"/>
          </template>
          <template v-else-if="col.type === 'no'">
            <q-avatar size="20px" class="q-ma-none q-pa-none text-anti-primary" color="primary">{{props.row.__index+1}}</q-avatar>
          </template>
          <template v-else-if="col.type === 'delete'">
            <q-btn icon="clear" color="negative" flat dense @click="deleteCol(props.row.__index)"/>
          </template>
          <template v-else-if="col.type === 'boolean'">
            <q-toggle checked-icon="check" unchecked-icon="close" :true-value="1" :false-value="0" v-model="props.row[col.name]" color="primary"/>
          </template>
          <template v-else-if="col.type === 'button'">
            <q-btn v-bind="col.attr" @click="col.callback(col,props.row)"/>
          </template>
          <template v-else-if="col.type === 'select'">
            <select-filter :placeholder="col.label" :options="col.options" v-model="props.row[col.name]" v-bind="col.attr"/>
          </template>
          <template v-else-if="col.type === 'text'">
            <q-input :placeholder="col.label" v-model="props.row[col.name]" v-bind="col.attr" @input="$emit('changeData',col.value,props.row,col)"/>
          </template>
          <template v-else-if="col.type === 'number'">
            <q-input type="number" input-class="text-right" :placeholder="col.label" v-model="props.row[col.name]" v-bind="col.attr" v-on="col.listeners" @input="$emit('changeData',col.value,props.row,col)" @focus="(evt) => {evt.target.select()}" />
          </template>
          <template v-else>
            <span v-bind="col.attr">{{col.value}}</span>
          </template>
        </q-td>
      </q-tr>
    </template>
    <template v-slot:bottom-row="props">     
      <slot name="bottom-row" v-bind="props"/>
    </template>
    <template v-slot:bottom="props">
      <div class="full-width">      
        <slot name="bottom" v-bind="props"/>
      </div>
    </template>
  </x-table>
</template>

<script>
export default {
  name: 'FormTable',
  components: {
    'select-filter' : () => import('./SelectFilter'),
    'x-table' : () => import('./TableEx.vue')
  },
  props: {
    data: Array 
  },
  data () {
    return {
      noPagination: {
        rowsPerPage: 0
      },
    }
  },
  methods: {
    deleteCol(index) {
      this.$emit("update:data",this.data.filter(v => v.__index != index));
    }
  },
  mounted() {
  }
}
</script>

<style>
</style>

<template>
  <q-table
    v-bind="attrs" v-on="listeners"
    :filter.sync="filter"
  >
    <template v-for="(_, slot) of scopedSlots" v-slot:[slot]="props">
      <template v-if="slot == 'body-cell'">
        <template v-if="props.col.name == '__no'">        
          <q-td :props="props">
            <q-avatar size="20px" class="q-ma-none q-pa-none text-anti-primary" color="primary">{{getNo(props.row.__index)}}</q-avatar>
          </q-td>
        </template>
        <template v-else-if="props.col.type == 'callback' && props.col.callback"> 
          <q-td :props="props">
            <q-btn :label="props.col.label" @click="props.col.callback(props.row,props.col,props)" v-bind="props.col.attr"/>
          </q-td>
        </template>
        <template v-else-if="_ === null">
          <q-td :props="props">
            {{props.value}}
          </q-td>
        </template>
        <slot v-else :name="slot" v-bind="props"/>
      </template>
      <template v-else>
        <slot :name="slot" v-bind="props"/>
      </template>
    </template>
    <template v-slot:top-right>
      <div v-if="filter != undefined && !scopedSlots.hasOwnProperty('top-right')" class="row">
        <q-input dark outlined dense debounce="300" :value="filter" @input="(val) => {$emit('update:filter',val)}" placeholder="Search">
          <template v-slot:append>
            <q-icon name="search" />
          </template>
        </q-input>
      </div>
      <slot v-else name="top-right"/>
    </template>
  </q-table>
</template>

<script>
export default {
  name: 'TableEx',
  data () {
    return {
      columns: [],
      visColumns: [],
    }
  },
  props: {
    filter: String
  },
  inheritAttrs: false,
  computed: {
    attrs() {
      let cols = this.initColumns(this.$attrs.columns)
      return {
        ...this.$attrs,
        columns: cols,
        visibleColumns: this.$attrs.visibleColumns || this.visibleColumns(cols),
      }
    },
    listeners() {
      let {...listens} = this.$listeners
      return listens
    },
    scopedSlots() {
      let {...sslots} = {'body-cell' : null, ...this.$scopedSlots}
      return sslots
    },
    pagination() {
      return this.attrs.pagination
    },
  },
  watch: {
    // attrs: {
    //   handle(val) {
    //     console.log('watching' , val)
    //   },
    //   // deep: true
    // }
    // pagination(val) {
    //   console.log(val)
    // }
  },
  methods: {
    getNo(index) {
      if (!this.pagination)
        return index + 1
      let rpp = Number(this.pagination.rowsPerPage)
      if (rpp <= 0 || isNaN(rpp))
        return index + 1
      return (Number(this.pagination.page) - 1) * rpp + index + 1
    },
    updateTable() {
      // this.columns = initColumns(this.attrs.columns)
      // this.visColumns = initColumns(this.attrs.columns)
    },
    initColumns(cols) {
      return cols.map((col) => {
        if (!col.name) {
          return {
            ...col,
            name: '__' + col.type,
            label: '',
            field: '__' + col.type,
            style: col.style || {
              width: '20px'
            }
          }
        } else {
          return {
            ...col,
            align: col.align || (col.type == 'integer' || col.type == 'decimal' || col.type == 'date' ? 'right' : 'left'),
            label: col.label || col.name.toUpperCase(),
            field: col.field || col.name,
            filter: col.filter || (col.type == 'boolean' ? 1 : ''),
            format: (val, row) => {
              if (col.format) {
                return col.format(val,row)
              } else if (col.type === 'integer' || col.type === 'decimal') {
                return this.$numeralCurrency(val)
              } else if (col.type === 'date') {
                return this.$date.formatDate(val, 'DD/MMMM/YYYY')
              } else if (col.type === 'datetime') {
                return this.$date.formatDate(val, 'DD/MMMM/YYYY HH:mm')
              } else if (col.type === 'enum' && col.options) {
                return col.options.find(v => v.value == val)['label']
              }
              return val
            }
          }
        }
      })
    },
    visibleColumns(cols) {
      let colus = cols.filter((col) => !col.type || col.type !== 'hidden')
      return colus.map((col) => col.name)
    }
  },
  mounted() {
    console.log('attrs',this.$attrs)
    console.log('listeners',this.$listeners)
    console.log('scopedSlots',this.$scopedSlots)
    console.log('newattrs',this.attrs)
    // setTimeout(() => {
    //   console.log('newattrs',this.attrs)
    // },1000)
    // setTimeout(() => {
    //   console.log(this)
    //   this.scopedSlots = this.$scopedSlots
    // },5000)
  },
  updated() {
  },
}
</script>

<style>
</style>

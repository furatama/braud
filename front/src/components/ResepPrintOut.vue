<template>
  <div id="printMe" v-show="doPrint" >
    <table 
      border="0" cellpadding="0px" cellspacing="0px"
      :style="`
        font-family: ${po.font.two};
        font-size: 14px;
        margin-left: ${po.margin.left + 10};
        margin-top: ${po.margin.top};
        width: ${po.width};
        border: 0.5px dotted;
    `">
      <tr style="font-size: 12px; font-weight:bold; height: 20px">
        <td style="border-bottom: 1px dotted;border-left: 1px dotted;border-right: 1px dotted;width:5%;">
          No
        </td>
        <td style="border-bottom: 1px dotted;border-left: 1px dotted;border-right: 1px dotted" >
          Resep
        </td>
        <td style="border-bottom: 1px dotted;border-left: 1px dotted;border-right: 1px dotted;width:20%; text-align:right">
          Total Berat
        </td>
        <td style="border-bottom: 1px dotted;border-left: 1px dotted;border-right: 1px dotted;width:15%; text-align:right">
          Total Resep
        </td>
      </tr>
      <tr v-for="(item,n) in data.data">
        <td style="padding:0 3px;margin:0;height:12px;border-left: 1px dotted;border-right: 1px dotted;vertical-align:top">
          <span :style="getStyle(po.styles.num)">{{n+1}}</span>
        </td>
        <td style="padding:0 3px;margin:0;height:12px;border-left: 1px dotted;border-right: 1px dotted;vertical-align:top">
          <span :style="getStyle(po.styles.alp)">{{item.nama}}</span>
        </td>
        <td style="text-align:right;padding:0 3px;margin:0;height:12px;border-left: 1px dotted;border-right: 1px dotted;vertical-align:top">
          <span :style="getStyle(po.styles.alp)">{{item.berat}}</span>
        </td>
        <td style="text-align:right;padding:0 3px;margin:0;height:12px;border-left: 1px dotted;border-right: 1px dotted;vertical-align:top">
          <span :style="getStyle(po.styles.alp)">{{item.resep}}</span>
        </td>
      </tr>
    </table>
  </div>

</template>

<script>
export default {
  // name: 'ComponentName',
  props: {
    data: Object
  },
  computed: {
    po() {
      return this.$store.getters.getPrintout
    }
  },
  data() {
    return {
      dats: [],
      doPrint: false
    };
  },
  watch: {
    // data() {
    //   let iter = Math.ceil(this.data.data.length / this.po.rows)
    //   this.dats = []
    //   for (let i = 0; i < iter; i++) {
    //     let as = this.data.data.slice(i * this.po.rows,(i+1) * this.po.rows)
    //     this.dats.push({
    //       ...this.data,
    //       data: as,
    //       subPart: this.$numeralCurrency(
    //           as.reduce((acc,v) => {
    //             return acc + Number(this.$numeralVal(v.subtotal))
    //           },0
    //         )
    //       )
    //     })        
    //   }
    // }
  },
  methods: {
    getStyle(obj) {
      let s = ''
      if (obj.font) {
        s = s + "font-family:" + obj.font + ";"
      }
      if (obj.size) {
        s = s + "font-size:" + obj.size + ";"
      }
      if (obj.bold) {
        s = s + "font-weight:" + obj.bold + ";"
      }
      return s
    },
    print() {
      // Pass the element id here
      this.doPrint = true;
      this.$htmlToPaper("printMe", () => {
        console.log("Printing done or got cancelled!");
        this.doPrint = false;
      });
    }
  }
};
</script>
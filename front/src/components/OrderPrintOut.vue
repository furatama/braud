<template>
  <div id="printMe" v-show="doPrint" >
    <div v-for="(dat,index) in dats">
      <table 
        border="0" cellpadding="5px" cellspacing="0"
        :style="`
          font-family: ${po.font.one};
          font-size: 12px;
          margin-left: ${po.margin.left};
          margin-top: ${po.margin.top};
          width: ${po.width};
      `">
        <tr style="font-size: 14px;">
          <td style="vertical-align: top; width:35%; font-weight:bold; font-size: 20px;">
            <img
              src="~assets/print-logo.jpg"
              style="width:250px;"
            >
            <div style="height:2px"/>
          </td>
          <td style="vertical-align: bottom; text-align:center"  >
            <span v-if="dats.length > 1">{{index+1}}/{{dats.length}}</span>
            <div style="height:2px"/>
          </td>
          <td style="vertical-align: bottom; width:20%; text-align:right">
            <span style="border-bottom: 1px dotted; font-weight: bold;">#{{dat.no}}</span>
            <div style="height:2px"/>
          </td>
        </tr>
        <tr>
          <td style="vertical-align: top">
            <div style="
              font-weight:bold;
              font-size: 14px;
            ">{{po.store.name}}</div>
            <div>{{po.store.address}}</div>
            <div style="
              font-size: 10px;
            ">{{po.store.phone}} / {{po.store.email}}</div>
          </td>
          <td style="vertical-align: top">
            <div style="
              font-size: 14px;
            ">Cust: <b>{{dat.cust.nama}}</b></div>
            <div v-if="dat.cust.alamat">{{dat.cust.alamat || '-'}}</div>
            <div v-if="dat.cust.telepon || dat.cust.email" style="
              font-size: 10px;
            ">{{dat.cust.telepon || '-'}} / {{dat.cust.email || '-'}}</div>
          </td>
          <td style="vertical-align: top; text-align:right">
            <div>{{dat.tanggal}}</div>
            <div>Staff: {{dat.kasir}}</div>
          </td>
        </tr>
      </table>
      <div style=""/>
      <table 
        border="0" cellpadding="0px" cellspacing="0px"
        :style="`
          font-family: ${po.font.two};
          font-size: 12px;
          margin-left: ${po.margin.left};
          margin-top: ${po.margin.top};
          width: ${po.width};
          border: 0.5px dotted;
      `">
        <tr style="font-size: 12px; font-weight:bold; height: 20px">
          <td style="border-bottom: 1px dotted;border-left: 1px dotted;border-right: 1px dotted;width:5%;">
            No
          </td>
          <td style="border-bottom: 1px dotted;border-left: 1px dotted;border-right: 1px dotted" >
            Product
          </td>
          <td style="border-bottom: 1px dotted;border-left: 1px dotted;border-right: 1px dotted;width:10%; text-align:right">
            Qty
          </td>
          <td style="border-bottom: 1px dotted;border-left: 1px dotted;border-right: 1px dotted;width:15%; text-align:right">
            Price
          </td>
          <td style="border-bottom: 1px dotted;border-left: 1px dotted;border-right: 1px dotted;width:10%; text-align:right">
            Disc
          </td>
          <td style="border-bottom: 1px dotted;border-left: 1px dotted;border-right: 1px dotted;width:15%; text-align:right">
            Subtotal
          </td>
        </tr>
        <tr v-for="(item,n) in dat.data">
          <td style="padding:0 3px;margin:0;height:12px;border-left: 1px dotted;border-right: 1px dotted;vertical-align:top">{{n+1+(index*po.rows)}}</td>
          <td style="padding:0 3px;margin:0;height:12px;border-left: 1px dotted;border-right: 1px dotted;vertical-align:top">{{item.produk}}</td>
          <td style="text-align:right;padding:0 3px;margin:0;height:12px;border-left: 1px dotted;border-right: 1px dotted;vertical-align:top">{{item.qty}}</td>
          <td style="text-align:right;padding:0 3px;margin:0;height:12px;border-left: 1px dotted;border-right: 1px dotted;vertical-align:top">{{item.harga}}</td>
          <td style="text-align:right;padding:0 3px;margin:0;height:12px;border-left: 1px dotted;border-right: 1px dotted;vertical-align:top">{{item.diskon}}%</td>
          <td style="text-align:right;padding:0 3px;margin:0;height:12px;border-left: 1px dotted;border-right: 1px dotted;vertical-align:top">{{item.subtotal}}</td>
        </tr>
      </table>
      <div  align="right" 
        :style="`
          font-family: ${po.font.two};
          font-size: 12px;
          margin-left: ${po.margin.left};
          margin-top: ${po.margin.top};
          width: ${po.width};
        `">
        <div style="display:flex;justify-content: flex-end;">
          <div style="width:15%;margin-right:5px;font-weight:bold">Total : </div>
          <div style="width:15%;margin-right:5px;">{{dat.subPart}}</div>
        </div>
        <div v-if="dats.length > 1" style="display:flex;justify-content: flex-end;">
          <div style="width:15%;margin-right:5px;font-weight:bold">Total All : </div>
          <div style="width:15%;margin-right:5px">{{dat.total}}</div>
        </div>
        <div style="display:flex;justify-content: flex-end;">
          <div style="width:15%;margin-right:5px;font-weight:bold">Method : </div>
          <div style="width:15%;margin-right:5px">{{dat.method}}</div>
        </div>
      </div>
      <div 
      :style="`
          font-family: ${po.font.three};
          font-size: 14px;
          margin-left: ${po.margin.left};
          margin-top: ${dats.length > 1 ? '-42px' : '-27px'};
          width: ${po.width};
      `">
        <div style="display:flex;">
          <div style="width:150px; margin-left: 10%">
            <div align="center">Recipient
            <br/><br/>
            (<span style="margin-left:120px"/>)</div>
          </div>
          <div  style="width:150px; margin-left: 10%">
            <div align="center">Cashier
            <br/><br/>
            (<span style="margin-left:120px"/>)</div>
          </div>
        </div>
      </div>
      <br style="margin-bottom:10px" v-if="index + 1 < dats.length"/>
    </div>
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
    data() {
      let iter = Math.ceil(this.data.data.length / this.po.rows)
      this.dats = []
      for (let i = 0; i < iter; i++) {
        let as = this.data.data.slice(i * this.po.rows,(i+1) * this.po.rows)
        this.dats.push({
          ...this.data,
          data: as,
          subPart: this.$numeralCurrency(
              as.reduce((acc,v) => {
                console.log(v)
                return acc + Number(this.$numeralVal(v.subtotal))
              },0
            )
          )
        })        
      }
    }
  },
  methods: {
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
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
          <td style="vertical-align: top; font-weight:bold; font-size: 20px;" colspan="2">
            <img
              src="~assets/print-logo.jpg"
              style="width:100px;"
            >
            <!-- ARTISAN BAKERY -->
            <div style="height:2px"/>
            <span v-if="dats.length > 1">{{index+1}}/{{dats.length}}</span>
            <div style="height:2px"/>
          </td>
          <td style="vertical-align: middle; width:105px; text-align:right" rowspan="2">
            <div style="font-size: 12px;">Order No:</div>
            <div style="font-size: 15px;font-weight: bold;">{{dat.no}}</div>
            <div style="margin:3px 0"/>
            <div style="font-size: 12px;">Order Date:</div>
            <div style="font-size: 12px;font-weight: bold;">{{dat.tanggal}}</div>
            <div style="margin:3px 0"/>
            <div style="font-size: 12px;">Delivery Date:</div>
            <div style="font-size: 12px;font-weight: bold;">{{dat.tgl_kirim}}</div>
          </td>
        </tr>
        <tr>
          <td style="vertical-align: top; width:220px;">
            <div style="
              font-weight:bold;
              font-size: 14px;
            ">{{po.store.name}}</div>
            <div>{{po.store.address}}</div>
            <div style="
              font-size: 10px;
            ">{{po.store.phone}}</div>
            <div style="
              font-size: 10px;
            ">{{po.store.email}}</div>
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
        </tr>
      </table>
      <div style=""/>
      <table
        border="0" cellpadding="0px" cellspacing="0px"
        :style="`
          font-family: ${po.font.two};
          font-size: 12px;
          margin-left: ${po.margin.left};
          margin-top: 2px;
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
            Price (Rp)
          </td>
          <td v-if="dat.hasDiscount" style="border-bottom: 1px dotted;border-left: 1px dotted;border-right: 1px dotted;width:10%; text-align:right">
            Disc
          </td>
          <td style="border-bottom: 1px dotted;border-left: 1px dotted;border-right: 1px dotted;width:15%; text-align:right">
            Sub (Rp)
          </td>
        </tr>
        <tr v-for="(item,n) in dat.data">
          <td style="padding:0 3px;margin:0;height:12px;border-left: 1px dotted;border-right: 1px dotted;vertical-align:top">
            <span :style="getStyle(po.styles.num)">{{n+1+(index*po.rows)}}</span>
          </td>
          <td style="padding:0 3px;margin:0;height:12px;border-left: 1px dotted;border-right: 1px dotted;vertical-align:top">
            <span :style="getStyle(po.styles.alp)">{{item.produk}}</span>
          </td>
          <td style="text-align:right;padding:0 3px;margin:0;height:12px;border-left: 1px dotted;border-right: 1px dotted;vertical-align:top">
            <span :style="getStyle(po.styles.num)">{{item.qty}}</span>
          </td>
          <td style="text-align:right;padding:0 3px;margin:0;height:12px;border-left: 1px dotted;border-right: 1px dotted;vertical-align:top">
            <span :style="getStyle(po.styles.num)">{{item.harga}}</span>
          </td>
          <td v-if="dat.hasDiscount" style="text-align:right;padding:0 3px;margin:0;height:12px;border-left: 1px dotted;border-right: 1px dotted;vertical-align:top">
            <span :style="getStyle(po.styles.num)">{{item.diskon}}%</span>
          </td>
          <td style="text-align:right;padding:0 3px;margin:0;height:12px;border-left: 1px dotted;border-right: 1px dotted;vertical-align:top">
            <span :style="getStyle(po.styles.num)">{{item.subtotal}}</span>
          </td>
        </tr>
      </table>
      <div
        :style="`
          font-family: ${po.font.two};
          font-size: 12px;
          margin-left: ${po.margin.left};
          margin-top: 2px;
          width: ${po.width};
          display:flex;
          justify-content: space-between;
        `">
        <div align="right" :style="`width: 200px;`">
          <div style="display:flex;justify-content: flex-start;">
            <div style="margin-right:1px;font-weight:bold">Payment : </div>
            <div style="margin-right:4px">{{dat.method}}</div>
          </div>
          <div v-if="dat.method.toLowerCase() == 'credit'" style="display:flex;justify-content: flex-start;">
            <div style="margin-right:5px;font-weight:bold">Due : </div>
            <div style="margin-right:5px;font-weight:bold">{{dat.due}}</div>
          </div>
        </div>
        <div align="right" :style="`width: 300px;`">
          <div style="display:flex;justify-content: flex-end;">
            <div style="width:auto;margin-right:1px;font-weight:bold">Total (Rp) : </div>
            <div :style="`${getStyle(po.styles.num)}width:24%;margin-right:4px;font-weight:bold`">{{dat.subPartPure}}</div>
          </div>
          <div style="display:flex;justify-content: flex-end;">
            <div style="width:auto;margin-right:1px;font-weight:bold">{{data.tax}}% tax (Rp) : </div>
            <div :style="`${getStyle(po.styles.num)}width:24%;margin-right:4px;font-weight:bold`">{{dat.subPartTax}}</div>
          </div>
          <div style="display:flex;justify-content: flex-end;">
            <div style="width:auto;margin-right:1px;font-weight:bold">Total +{{data.tax}}% tax (Rp) : </div>
            <div :style="`${getStyle(po.styles.num)}width:24%;margin-right:4px;font-weight:bold`">{{dat.subPart}}</div>
          </div>
          <div v-if="dats.length > 1" style="display:flex;justify-content: flex-end;">
            <div style="width:auto;margin-right:1px;font-weight:bold">Total All (Rp) : </div>
            <div :style="`${getStyle(po.styles.num)}width:24%;margin-right:4px;font-weight:bold`">{{dat.total}}</div>
          </div>
        </div>
      </div>
      <div
      :style="`
          font-family: ${po.font.three};
          font-size: 14px;
          margin-left: ${po.margin.left};
          margin-top: -20px;
          width: ${po.width};
      `">
        <div v-if="po.notabene" :style="`
          font-family: ${po.font.three};
          font-size: 10.5px;
          width: 75%;
        `">
          <pre>{{po.notabene}} </pre>
        </div>
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
        <div align="right" :style="`
          font-family: ${po.font.one};
          font-weight:bold;
          font-size: 9px;
          margin-top: -10px;
          padding-right: 10px;`
        ">
          Issued on {{today}}
        </div>
      </div>
      <!-- <br style="margin-bottom:50px" v-if="index + 1 < dats.length"/> komen masalah printout -->
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
    },
    today() {
      return this.$date.formatDate(new Date(), 'DD/MM/YYYY')
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
          hasDiscount: as.reduce((acc,v) => {
            return acc || (Number(v.diskon) > 0)
          },false),
          subPartPure: this.$numeralCurrency(
            Number(as.reduce((acc,v) => {
              return acc + Number(this.$numeralVal(v.subtotal))
            },0
          ))),
          subPartTax: this.$numeralCurrency(
              Number(as.reduce((acc,v) => {
                return acc + Number(this.$numeralVal(v.subtotal))
              },0
            )) * ((this.data.tax || 0) / 100)
          ),
          subPart: this.$numeralCurrency(
              Number(as.reduce((acc,v) => {
                return acc + Number(this.$numeralVal(v.subtotal))
              },0
            )) * ((100 + (this.data.tax || 0)) / 100)
          )
        })
      }
    }
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
      console.log(this.data)
      this.doPrint = true;
      this.$htmlToPaper("printMe", () => {
        console.log("Printing done or got cancelled!");
        this.doPrint = false;
      });
    }
  }
};
</script>

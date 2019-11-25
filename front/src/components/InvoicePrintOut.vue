<template>
  <div id="printMe" v-show="doPrint" >
    <div>
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
            ARTISAN BAKERY
            <div style="height:2px"/>
          </td>
          <td style="vertical-align: middle; width:105px; text-align:right" rowspan="2">
            
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
        </tr>
        <tr>
          <td style="vertical-align: top; width:20px;">
          </td>
          <td style="vertical-align: bottom">
            <div style="
              font-size: 18px;
              text-decoration:underline;
            "><b>RECEIPT INVOICE</b></div>

          </td>
        </tr>
      </table>
      <div style=""/>

      <table 
        border="0" cellpadding="0px" cellspacing="0px"
        :style="`
          padding: 15px 0;
          font-family: ${po.font.two};
          font-size: 15px;
          margin-left: ${po.margin.left};
          margin-top: 2px;
          width: ${po.width};
          border: 0.5px dotted;
      `">
        <tr style="font-size: 14px; height: 25px">
          <th align="right">RECEIVED FROM</th>
          <td style="padding: 0 5px"> : </td>
          <td>{{customer}}</td>
        </tr>
        <tr style="font-size: 14px; height: 25px">
          <th align="right">DESCRIPTION</th>
          <td style="padding: 0 5px"> : </td>
          <td>Payment of <b>{{nData}}</b> Invoice(s) from <b>{{dateFrom}}</b> to <b>{{dateTo}}</b></td>
        </tr>
        <tr style="font-size: 14px; height: 25px">
          <th align="right">AMOUNT</th>
          <td style="padding: 0 5px"> : </td>
          <td><small>Rp.</small> {{amountTotal}}</td>
        </tr>
      </table>
      <div  align="right" 
        :style="`
          font-family: ${po.font.two};
          font-size: 14px;
          margin-left: ${po.margin.left};
          margin-top: 15px;
          width: ${po.width};
        `">
        
        <div style="width:300px; margin-left: 10%">
          <div align="center">
            Denpasar, {{today}}
          <br/>
            Recipient
          <br/><br/><br/><br/>
          (<span style="margin-left:120px"/>)</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  // name: 'ComponentName',
  props: {
    data: Object,
  },
  computed: {
    po() {
      return this.$store.getters.getPrintout
    },
    today() {
      return this.$date.formatDate(Date(), 'DD MMMM YYYY')
    }
  },
  data() {
    return {
      dats: [],
      doPrint: false,
      nData: 0,
      dateFrom: '',
      dateTo: '',
      amountTotal: 0,
      customer: ''
    };
  },
  watch: {
    data() {
      this.nData = this.data.data.length
      this.amountTotal = this.$numeralCurrency(this.data.total)
      this.dateFrom = this.$date.formatDate(this.data.from, 'DD/MM/YYYY')
      this.dateTo = this.$date.formatDate(this.data.to, 'DD/MM/YYYY')
      this.customer = this.data.customer
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
      this.doPrint = true;
      this.$htmlToPaper("printMe", () => {
        console.log("Printing done or got cancelled!");
        this.doPrint = false;
      });
    }
  }
};
</script>
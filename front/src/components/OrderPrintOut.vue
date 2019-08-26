<template>
  <div id="printMe" v-show="doPrint" >
    <table 
      border="0" cellpadding="5px" cellspacing="0"
      style="
        font-family: 'Draft 17cpi', 'Consolas', Courier, monospace;
        font-size: 12px;
        margin: 0 2% 0 0;
        width: 90%;
    ">
      <tr style="font-size: 14px;">
        <td style="vertical-align: top; width:40%; font-weight:bold; font-size: 20px;">
          BRAUD ARTISAN BAKERY
          <div style="height:2px"/>
        </td>
        <td style="vertical-align: bottom;"  >
          
          <div style="height:2px"/>
        </td>
        <td style="vertical-align: bottom; width:25%; text-align:right">
          <span style="border-bottom: 1px solid;">NO: {{data.no}}</span>
          <div style="height:2px"/>
        </td>
      </tr>
      <tr>
        <td style="vertical-align: top">
          <div style="
            font-weight:bold;
            font-size: 14px;
          ">UD. Ladang Roti</div>
          <div>Jl. Pulau Morotai 45, Denpasar, Bali</div>
          <div style="
            font-size: 10px;
          ">082 237 810 111 / braud.artisanbakery@gmail.com</div>
        </td>
        <td style="vertical-align: top">
          <div style="
            font-weight:bold;
            font-size: 14px;
          ">Cust: {{data.cust.nama}}</div>
          <div>{{data.cust.alamat || '-'}}</div>
          <div style="
            font-size: 10px;
          ">{{data.cust.telepon || '-'}} / {{data.cust.email || '-'}}</div>
        </td>
        <td style="vertical-align: top; text-align:right">
          <div>{{data.tanggal}}</div>
          <div>Kasir: {{data.kasir}}</div>
        </td>
      </tr>
    </table>
    <br/>
    <table 
      border="0" cellpadding="0px" cellspacing="0px"
      style="
        font-family: 'Draft 17cpi', 'Consolas', Courier, monospace;
        font-size: 14px;
        margin: 0 2% 0 0;
        width: 90%;
        
        border: 0.5px solid;
    ">
      <tr style="font-size: 14px; font-weight:bold; height: 20px">
        <td style="border-bottom: 1px solid;border-left: 1px solid;border-right: 1px solid;width:5%;">
          No
        </td>
        <td style="border-bottom: 1px solid;border-left: 1px solid;border-right: 1px solid" >
          Produk
        </td>
        <td style="border-bottom: 1px solid;border-left: 1px solid;border-right: 1px solid;width:15%; text-align:right">
          Harga
        </td>
        <td style="border-bottom: 1px solid;border-left: 1px solid;border-right: 1px solid;width:10%; text-align:right">
          Qty
        </td>
        <td style="border-bottom: 1px solid;border-left: 1px solid;border-right: 1px solid;width:10%; text-align:right">
          Diskon
        </td>
        <td style="border-bottom: 1px solid;border-left: 1px solid;border-right: 1px solid;width:15%; text-align:right">
          Subtotal
        </td>
      </tr>
      <tr v-for="(item,n) in data.data">
        <td style="padding:0 3px;margin:0;height:12px;border-left: 1px solid;border-right: 1px solid;vertical-align:top">{{n+1}}</td>
        <td style="padding:0 3px;margin:0;height:12px;border-left: 1px solid;border-right: 1px solid;vertical-align:top">{{item.produk}}</td>
        <td style="text-align:right;padding:0 3px;margin:0;height:12px;border-left: 1px solid;border-right: 1px solid;vertical-align:top">{{item.harga}}</td>
        <td style="text-align:right;padding:0 3px;margin:0;height:12px;border-left: 1px solid;border-right: 1px solid;vertical-align:top">{{item.qty}}</td>
        <td style="text-align:right;padding:0 3px;margin:0;height:12px;border-left: 1px solid;border-right: 1px solid;vertical-align:top">{{item.diskon}}%</td>
        <td style="text-align:right;padding:0 3px;margin:0;height:12px;border-left: 1px solid;border-right: 1px solid;vertical-align:top">{{item.subtotal}}</td>
      </tr>
    </table>
    <div  align="right" style="
      width: 90%;
      margin: 1% 2% 0 0;
      font-family: 'Draft 17cpi', 'Consolas', Courier, monospace;
      font-size: 14px;
    ">
      <div style="display:flex;justify-content: flex-end;">
        <div style="width:15%;margin-right:5px">Total : </div>
        <div style="width:15%;margin-right:5px">{{data.total}}</div>
      </div>
      <div style="display:flex;justify-content: flex-end;">
        <div style="width:15%;margin-right:5px">Metode : </div>
        <div style="width:15%;margin-right:5px">{{data.method}}</div>
      </div>
      <!-- <div style="display:flex;justify-content: flex-end;">
        <div style="width:15%;margin-right:5px">{{data.sisaLabel}} : </div>
        <div style="width:15%;margin-right:5px">{{data.sisa}}</div>
      </div> -->
    </div>
    <div style="
      width: 90%;
      margin: -25px 5% 0 0;
      font-family: 'Draft 17cpi', 'Consolas', Courier, monospace;
      font-size: 14px;
    ">
      <div style="display:flex;">
        <div style="width:150px; margin-left: 10%">
          <div align="center">Penerima</div>
          <br/>
          <div>(________________)</div>
        </div>
        <div  style="width:150px; margin-left: 10%">
          <div align="center">Kasir</div>
          <br/>
          <div>(________________)</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  // name: 'ComponentName',
  props: {
    data: Object
  },
  data() {
    return {
      doPrint: false
    };
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
<template>
  <div id="printMe" v-show="doPrint" >
    <table 
      border="0" cellpadding="5px" cellspacing="0"
      style="
        font-family: 'Draft 17cpi', 'Consolas', Courier, monospace;
        font-size: 12px;
        margin: 5% 2% 0 2%;
        width: 96%;
    ">
      <tr style="font-size: 14px;">
        <td style="vertical-align: top; width:40%; font-weight:bold; font-size: 20px;">
          BRAUD ARTISAN BAKERY
          <div style="height:2px"/>
        </td>
        <td style="vertical-align: bottom;"  >
          <span style="border-bottom: 1px solid;">NO: xxxxxxx</span>
          <div style="height:2px"/>
        </td>
        <td style="vertical-align: bottom; width:15%; text-align:right">
          TUNAI
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
          ">Cust: Customer</div>
          <div>Alamat: </div>
        </td>
        <td style="vertical-align: top; text-align:right">
          <div>Tgl: 19-10-2012</div>
          <div>Kasir: kasir</div>
        </td>
      </tr>
    </table>
    <hr/>
    <table 
      border="0" cellpadding="0px" cellspacing="0px"
      style="
        font-family: 'Draft 17cpi', 'Consolas', Courier, monospace;
        font-size: 14px;
        margin: 0 2% 0 2%;
        width: 96%;
        
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
      <tr v-for="n in 5">
        <td style="padding:0 3px;margin:0;height:12px;border-left: 1px solid;border-right: 1px solid;vertical-align:top">{{n}}</td>
        <td style="padding:0 3px;margin:0;height:12px;border-left: 1px solid;border-right: 1px solid;vertical-align:top">Bun Chocolate 200g</td>
        <td style="text-align:right;padding:0 3px;margin:0;height:12px;border-left: 1px solid;border-right: 1px solid;vertical-align:top">2000</td>
        <td style="text-align:right;padding:0 3px;margin:0;height:12px;border-left: 1px solid;border-right: 1px solid;vertical-align:top">1</td>
        <td style="text-align:right;padding:0 3px;margin:0;height:12px;border-left: 1px solid;border-right: 1px solid;vertical-align:top">20%</td>
        <td style="text-align:right;padding:0 3px;margin:0;height:12px;border-left: 1px solid;border-right: 1px solid;vertical-align:top">1800</td>
      </tr>
    </table>
    <div align="right" style="
      width: 96%;
      margin: 1% 2% 0 2%;
      font-family: 'Draft 17cpi', 'Consolas', Courier, monospace;
      font-size: 14px;
    ">
      <div style="display:flex;justify-content: flex-end;">
        <div style="width:15%;margin-right:5px">Total : </div>
        <div style="width:15%;margin-right:5px">80000</div>
      </div><div style="display:flex;justify-content: flex-end;">
        <div style="width:15%;margin-right:5px">Terbayar : </div>
        <div style="width:15%;margin-right:5px">80000</div>
      </div><div style="display:flex;justify-content: flex-end;">
        <div style="width:15%;margin-right:5px">Kembali : </div>
        <div style="width:15%;margin-right:5px">80000</div>
      </div>
    </div>
    <div style="
      width: 96%;
      margin: -50px 2% 0 2%;
      font-family: 'Draft 17cpi', 'Consolas', Courier, monospace;
      font-size: 14px;
    ">
      <div style="display:flex;">
        <div style="width:150px">
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

    <!-- <div style="
      font-family: 'Draft 17cpi', 'Consolas', Courier, monospace
      font-size:12px;
    ">
      <div style="display:flex;justify-content:space-between">
        <span>TOKO XXX</span>
        <span>{{date}}</span>
      </div>
      <hr style="padding:0;margin:0">
      <hr style="padding:0;margin:0">
      <div v-for="(value,index) in data" :key="index" style="display:flex">
        <div style="width:35%">{{value.item}}</div>
        <div style="width:15%;text-align:right">{{value.qty}}</div>
        <div style="width:25%;text-align:right">{{value.price}}</div>
        <div style="width:25%;text-align:right">{{value.subtotal}}</div>
      </div>
      <div style="display:flex;justify-content:flex-end">
        <div style="width:50%">
          <hr style="padding:0;margin:0">
          <hr style="padding:0;margin:0">
          <div style="display:flex;">
            <div style="width:50%">Total</div>
            <div style="width:50%;text-align:right">{{total}}</div>          
          </div>
          <div style="display:flex;">
            <div style="width:50%">Tunai</div>
            <div style="width:50%;text-align:right">{{cash}}</div>
          </div>
          <div style="display:flex;">
            <div style="width:50%">Kembali</div>
            <div style="width:50%;text-align:right">{{exchange}}</div>
          </div>
        </div>
      </div>
    </div> -->
  </div>
</template>

<script>
export default {
  // name: 'ComponentName',
  props: {
    data: Array,
    cash: [String,Number],
    total: [String,Number],
    exchange: [String,Number],
    date: [String,Number]
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

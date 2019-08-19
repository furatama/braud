import numeral from 'numeral'

export default async ({ Vue }) => {
  numeral.register('locale', 'id', {
    delimiters: {
        thousands: '.',
        decimal: ','
    },
    abbreviations: {
        thousand: 'k',
        million: 'm',
        billion: 'b',
        trillion: 't'
    },
    currency: {
        symbol: 'Rp'
    }
  });

  // switch between locales
  // numeral.locale('id');

  Vue.prototype.$numeral = numeral
  Vue.prototype.$numeralVal = (val) => {return numeral(val).value()}
  Vue.prototype.$numeralCurrency = (val) => {return numeral(val).format('0,0')}
  Vue.prototype.$numeralCurrencyAdd = (val1,val2) => {return numeral(numeral(val1).value() + numeral(val2).value()).format('0,0')}
  Vue.prototype.$numeralCurrencySub = (val1,val2) => {return numeral(numeral(val1).value() - numeral(val2).value()).format('0,0')}
  Vue.prototype.$numeralCurrencyMul = (val1,val2) => {return numeral(numeral(val1).value() * numeral(val2).value()).format('0,0')}
  Vue.prototype.$numeralCurrencyDiv = (val1,val2) => {return numeral(numeral(val1).value() / numeral(val2).value()).format('0,0')}
  // Vue.prototype.$axiosFetch 
}

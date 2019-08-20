// import something here

// "async" is optional
export default async ({Vue}) => {
  Vue.prototype.$array_duplicate = (arr,valComp = null) => {
    let object = {};
    let result = [];
    
    let vc = valComp

    if (valComp == null) {
      vc = (item) => item
    }

    arr.forEach((item) => {
      let val = vc(item)
      if(!object[val])
        object[val] = 0;
      object[val] += 1;
    })

    for (var prop in object) {
      if(object[prop] >= 2) {
        result.push(prop);
      }
    }

    return result;
  }

  Vue.prototype.$array_filter = (arr, val, label = "label") => {
    const needle = val.toLowerCase()
    return arr.filter(v => v[label].toLowerCase().indexOf(needle) > -1)
  }
}

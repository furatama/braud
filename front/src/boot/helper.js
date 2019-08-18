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
}

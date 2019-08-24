import { axios } from 'boot/axios'

export function fetch(context,{url,params}) {
  return new Promise((resolve, reject) => {
    context.commit("loadingStart")
    axios.get(url, {
      headers: {
        'Authorization' : 'Bearer ' + context.getters.getToken,
      },
      params
    }).then((response) => {
      const body = response.data
      if (body.status === "success") {
        resolve(body.data)
      } else if (body.status === "fail") {
        resolve(body.data)
      } else {
        reject(response)
      }
    }).catch((error) => {
      reject(error)
    }).finally(() => {
      context.commit("loadingEnd")
    })
  })
}

export function fetchPaginate(context,{url,pagination,params}) {
  return new Promise((resolve, reject) => {
    context.commit("loadingStart")
    axios.get(url, {
      headers: {
        'Authorization' : 'Bearer ' + context.getters.getToken,
      },
      params : {
        paginate: pagination.rowsPerPage, 
        page: pagination.page,
        sortBy: pagination.sortBy,
        descending: pagination.descending,
        ...params
      }
    }).then((response) => {
      const body = response.data
      if (body.status === "success") {
        resolve(body.data)
      } else if (body.status === "fail") {
        resolve(body.data)
      } else {
        reject(response)
      }
    }).catch((error) => {
      reject(error)
    }).finally(() => {
      context.commit("loadingEnd")
    })
  })
}

export function fetchAll(context,{url,filter,rowsPerPage,page,sortBy,descending,colFilter}) {
  return new Promise((resolve, reject) => {
    context.commit("loadingStart")
    axios.get(url, {
      headers: {
        'Authorization' : 'Bearer ' + context.getters.getToken,
      },
      params: {
        filter: filter,
        paginate: rowsPerPage, 
        page: page,
        sortBy: sortBy,
        descending: descending,
        colFilter: colFilter
      }
    }).then((response) => {
      const body = response.data
      if (body.status === "success") {
        resolve(body.data)
      } else {
        reject(response)
      }
    }).catch((error) => {
      reject(error)
    }).finally(() => {
      context.commit("loadingEnd")
    })
  })
}

export function fetchOptions(context,{url}) {
  return new Promise((resolve, reject) => {
    context.commit("loadingStart")
    axios.get(url, {
      headers: {
        'Authorization' : 'Bearer ' + context.getters.getToken,
      }
    }).then((response) => {
      const body = response.data
      if (body.status === "success") {
        resolve(body.data)
      } else {
        reject(response)
      }
    }).catch((error) => {
      reject(error)
    }).finally(() => {
      context.commit("loadingEnd")
    })
  })
}

export function fetchSingle(context, {url,id}) {
  return new Promise((resolve, reject) => {
    context.commit("loadingStart")
    axios.get(url + `/${id}`, {
      headers: {
        'Authorization' : 'Bearer ' + context.getters.getToken,
      }
    }).then((response) => {
      const body = response.data
      if (body.status === "success") {
        resolve(body.data)
      } else {
        reject(response)
      }
    }).catch((error) => {
      reject(error)
    }).finally(() => {
      context.commit("loadingEnd")
    })
  })
}

export function deleteSingle(context, {url, id}) {
  return new Promise((resolve, reject) => {
    context.commit("loadingStart")
    axios.delete(url + `/${id}`, {
      headers: {
        'Authorization' : 'Bearer ' + context.getters.getToken,
      }
    }).then((response) => {
      const body = response.data
      if (body.status === "success") {
        resolve(body.data)
      } else {
        reject(response)
      }
    }).catch((error) => {
      reject(error)
    }).finally(() => {
      context.commit("loadingEnd")
    })
  })
}

export function postSingle(context, {url, inputs}) {
  return new Promise((resolve, reject) => {
    context.commit("loadingStart")
    axios.post(url, {
      ...inputs,
      token: context.getters.getToken
    }).then((response) => {
      const body = response.data
      if (body.status === "success") {
        resolve(body.data)
      } else {
        reject(response)
      }
    }).catch((error) => {
      reject(error)
    }).finally(() => {
      context.commit("loadingEnd")
    })
  })
}

export function updateSingle(context, {url,id,inputs}) {
  return new Promise((resolve, reject) => {
    context.commit("loadingStart")
    axios.put(url + `/${id}`, {
      ...inputs,
      token: context.getters.getToken
    }).then((response) => {
      const body = response.data
      if (body.status === "success") {
        resolve(body.data)
      } else {
        reject(response)
      }
    }).catch((error) => {
      reject(error)
    }).finally(() => {
      context.commit("loadingEnd")
    })
  })
}

export function getXLS(context, {url,params,filename}) {
  context.commit("loadingStart")
  axios({
    url,
    params: params,
    method: 'GET',
    responseType: 'blob',
    headers: {
      'Authorization' : 'Bearer ' + context.getters.getToken,
    }
  }).then((response) => {
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', filename + '.xlsx');
    document.body.appendChild(link);
    link.click();
  }).catch((error) => {
    console.log(error)
  }).finally(() => {
    context.commit("loadingEnd")
  })
}
import { axios } from 'boot/axios'

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
const { createApp } = Vue;
const { createVuetify } = Vuetify;
const vuetify = createVuetify();

const token = sessionStorage.getItem('eu_token')

const ajaxAction = async (url, action,token, queryStringData = null) => {
    const response = await fetch(url, {
        method:'post',
        headers:{
            'Content-Type':'application/x-www-form-urlencoded'
        },
        body:`action=${action}&token=${token}${queryStringData?'&':''}${queryStringData}`
    })
    return await response.json()
}

const newQueryString = ( data ) => {
    return Object.keys( data ).map( key => `${key}=${data[key]}`).join('&')
}
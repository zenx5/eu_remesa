const { createApp } = Vue;
const { createVuetify } = Vuetify;
const vuetify = createVuetify();

const token = sessionStorage.getItem('eu_token')

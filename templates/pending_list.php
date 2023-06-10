<!-- <link href="https://cdn.jsdelivr.net/npm/vuetify@3.3.2/dist/vuetify.min.css" rel="stylesheet"></link>
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vuetify@3.3.2/dist/vuetify.min.js"></script> -->
<h2>Pending Operation</h2>

<!-- <div id="pending-admin-app" style="width:30%">
    <v-list>
        <v-list-item
            v-for="item in pendings"
            :key="item.id"
            :title="item.mount + ' ' + item.currency"
        ></v-list-item>
    </v-list>
</div> -->



<script>
    // const { createApp } = Vue;
    // const { createVuetify } = Vuetify;
    // const vuetify = createVuetify();

    createApp({
        data(){
            return {
                pendings:[
                    { id:0, mount:100, currency:'USD' },
                    { id:1, mount:130, currency:'USD' },
                    { id:2, mount:150, currency:'USD' },
                    { id:3, mount:90, currency:'USD' },
                ],
                message:'hola mundo'
            }
        },
        methods:{

        }

    }).use(vuetify).mount('#pending-admin-app')
</script>
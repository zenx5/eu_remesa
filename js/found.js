const appTemplate = (entity) => ({
    data(){
        return {
            loading: false,
            entities:[]
        }
    },
    async beforeMount(){
        const response = await fetch('/wp-admin/admin-ajax.php', {
            method:'post',
            headers:{
                'Content-Type':'application/x-www-form-urlencoded'
            },
            body:`action=get_${entity}&token=${token}`
        })
        const result = await response.json()
        console.log( result )
        this.entities = result
    },
    methods:{
        async saveEntityValue(){
            this.loading = true;
            const currencyString = Array.from( 
                Object.keys(this.entities),
                ( key ) => `${key}=${this.entities[key]}`
            ).join('&')
            const response = await fetch('/wp-admin/admin-ajax.php',{
                method:'post',
                headers:{
                    'Content-Type':'application/x-www-form-urlencoded'
                },
                body:`action=save_${entity}&${currencyString}&token=${token}`
            })
            const result = await response.json()
            this.loading = false;
        }
    }
})

const appRoot = createApp({
    data(){
        return {
            tab:null
        }
    },
    methods:{
        changeTab(r){
            console.log('log 2', r, this.tab )
        }
        
    }
}).use(vuetify)

createApp(appTemplate('found')).use(vuetify).mount('#found-admin-app')
const appConfirms = createApp({
    data(){
        return {
            confirms:[],
        }
    },
    async beforeMount(){
        const response = await fetch('/wp-admin/admin-ajax.php', {
            method:'post',
            headers:{
                'Content-Type':'application/x-www-form-urlencoded'
            },
            body:`action=get_confirms&token=${token}`
        })
        const result = await response.json()
        console.log( result )
        this.confirms = result
    }
}).use(vuetify).mount('#history-admin-app')
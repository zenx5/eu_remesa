createApp({
    methods:{
        async resetall(){
            const response = await fetch('/wp-admin/admin-ajax.php', {
                method:'post',
                headers:{
                    'Content-Type':'application/x-www-form-urlencoded'
                },
                body:`action=reset_all&token=${token}`
            })
            if( await response.json() ){
                document.location.reload();
            }
            
        }
    }
}).use(vuetify).mount('#control-admin-app')

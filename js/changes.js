createApp({
    data(){
        return {
            loading:false,
            currencyFrom:'',
            currencyTo:'',
            relation:'>',
            deposit:0,
            value_format:true,
            value:0,
            changes:[],
            optionsTypeChange: [
                { label:'%', value:true },
                { label:'unid', value:false },
            ],
            optionsRelationChange:['<','<=','>','>=','=']
        }
    },
    async beforeMount(){
        const result = await ajaxAction(
            '/wp-admin/admin-ajax.php',
            'get_changes',
            token
        )
        console.log( result )
        this.changes = result.map( item => ({...item, rules:[...item.rules, {
            relation:'>',
            deposit:0,
            value_format:false,
            value:0
        }]}))
    },
    methods:{
        async addChange(){
            this.loading = true;
            const result = await ajaxAction(
                '/wp-admin/admin-ajax.php',
                'set_change',
                token,
                newQueryString({
                    currency_from: this.currencyFrom,
                    currency_to: this.currencyTo
                })
            )
            this.loading = false;
            console.log( result )
            this.changes = result.map( item => ({...item, rules:[...item.rules, {
                relation:'>',
                deposit:0,
                value_format:true,
                value:0
            }]}))
        },
        async removeRule(idChange, idRule){
            console.log('remove rule')
            this.loading = true;
            const result = await ajaxAction(
                '/wp-admin/admin-ajax.php',
                'remove_rule',
                token,
                newQueryString({
                    id: idChange,
                    index: idRule
                })
            )
            this.loading = false;
            console.log( result )
            this.changes = result.map( item => ({...item, rules:[...item.rules, {
                relation:'>',
                deposit:0,
                value_format:true,
                value:0
            }]}))
        },
        async addRule(idChange){
            console.log('new rule 1')
            const newRule = this.changes.find( change => change.id===idChange ).rules.slice(-1)[0]
            this.loading = true;
            const result = await ajaxAction(
                '/wp-admin/admin-ajax.php',
                'add_rule',
                token,
                newQueryString({
                    ...newRule,
                    id:idChange
                })
            )
            this.loading = false;
            this.relation = '>'
            this.deposit = 0
            this.value_format = true
            this.value = 0
            console.log( result )
            this.changes = result.map( item => ({...item, rules:[...item.rules, {
                relation:'>',
                deposit:0,
                value_format:true,
                value:0
            }]}))
        }
    }

}).use(vuetify).mount('#changes')
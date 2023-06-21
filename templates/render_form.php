    <link href="https://cdn.jsdelivr.net/npm/vuetify@3.3.2/dist/vuetify.min.css" rel="stylesheet"></link>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vuetify@3.3.2/dist/vuetify.min.js"></script>
    <div id="form-app" style="display:flex; flex-direction:column; gap:20px;">
        <div style="display:flex; align-items:center; flex-direction:row; justify-content:space-between; gap:15px;">
            <v-btn @click="actionOne" :disabled="disableOne">{{labelBtnOne}}</v-btn>
            <v-btn @click="actionTwo" :disabled="disableTwo">{{labelBtnTwo}}</v-btn>
        </div>
        <v-progress-linear :model-value="progress" height="5"></v-progress-linear>
        <div v-if="step===1" style="margin-top:20px">
            <v-select
                variant="outlined"
                v-model="countryFrom"
                label="Pais de Origen"
                item-title="name"
                :items="filteredCountriesFrom"
            ></v-select>
            <v-select
                variant="outlined"
                v-model="countryTo"
                label="Pais de Destino"
                item-title="name"
                :items="filteredCountriesTo"
            ></v-select>
            <span v-if="countryTo!==''&&countryFrom!==''">{{ rate }} {{getFromCurrency}}/{{getToCurrency}}</span>
        </div>
        <div v-if="step===2" style="margin-top:20px">
            <v-text-field label="Monto Enviado" variant="outlined" type="number" v-model="mount">
                <template v-slot:prepend-inner>
                    <span style="padding-right:10px;">{{ getFromCurrency }}</span>
                </template>
            </v-text-field>
            <v-text-field label="Monto Recibido" variant="outlined" type="number" v-model="mountReceived" disabled>
                <template v-slot:prepend-inner>
                    <span style="padding-right:10px;">{{ getToCurrency }}</span>
                </template>
            </v-text-field>
            <v-btn @click="checkAvalaibility" :disabled="mount===0||avalaible">Chequear Disponibilidad</v-btn>
            <span v-if="avalaible">
                <p>El monto ha sido apartado, usted tiene 15 minutos para realizar la trasferencia antes de que sea ofrecido a otro usuario.</p>
                <p>Los datos para la operacion son los siguientes.</p>
                <v-text-field v-model="reference" label="Referencia"></v-text-field>
            </span>
        </div>
        <div v-if="step===3" style="margin-top:20px">
            <p>{{message}}</p>
        </div>
        
    </div>
    <script>
        const { createApp } = Vue;
        const { createVuetify } = Vuetify;
        const vuetify = createVuetify();

        createApp({
            data(){
                return {
                    progress: 33.3,
                    step:1,
                    mount:0,
                    reference:'',
                    avalaible:false,
                    countryFrom:'',
                    countryTo:'',
                    labelBtnOne:' - ',
                    labelBtnTwo:'Siguiente',
                    message: "Hi, running...",
                    countries: [
                        { name:'Argentina', currency:'ARS', symbol:'($)', currencyValue:<?=$rates->ars?> },
                        { name:'Brasil', currency:'BRL', symbol:'(R$)', currencyValue:<?=$rates->brl?>  },
                        { name:'Colombia', currency:'COP', symbol:'($)', currencyValue:<?=$rates->cop?>  },
                        { name:'Estados Unidos', currency:'USD', symbol:'($)', currencyValue:1 },
                        { name:'Venezuela', currency:'VEF', symbol:'(Bs)', currencyValue:<?=$rates->vef?>  },
                    ]
                }
            },
            computed:{
                rate() {
                    const { 
                        // currency:currencyFrom,
                        currencyValue:valueFrom
                    } = this.countries.find( country => country.name===this.countryFrom )
                    const {
                        // currency:currencyTo,
                        currencyValue:valueTo
                    } = this.countries.find( country => country.name===this.countryTo )
                    return valueTo / valueFrom
                },
                filteredCountriesFrom() {
                    return this.countries.filter( country => this.countryFrom !== country.name )
                },
                filteredCountriesTo() {
                    return this.countries.filter( country => this.countryTo !== country.name )
                },
                getFromCurrency() {
                    return this.countries.find( country => this.countryFrom === country.name ).currency
                },
                getToCurrency() {
                    return this.countries.find( country => this.countryTo === country.name ).currency
                },
                mountReceived() {
                    return this.mount*this.rate
                },
                disableOne() {
                    return this.step===1;
                },
                disableTwo() {
                    return this.countryFrom===this.countryTo ||
                    this.countryFrom==='' ||
                    this.countryTo==='' //|| this.reference===''
                }
            },
            methods:{
                async checkAvalaibility(){
                    const currency = this.getToCurrency.toLowerCase()
                    const mount = this.mountReceived
                    const response = await fetch('/wp-admin/admin-ajax.php',{
                        method:'post',
                        headers:{
                            'Content-Type':'application/x-www-form-urlencoded'
                        },
                        body:`action=get_found_of&currency=${currency}&mount=${mount}`
                    })
                    const result = await response.json()
                    console.log( result, this.mountReceived )
                    this.avalaible = JSON.parse(result.avalaible)
                },
                actionOne (){
                    this.step--;
                    this.progress = this.step*100/3;
                    this.changeLabels(this.step)
                },
                actionTwo(){
                    this.step++;
                    this.progress = this.step*100/3;
                    this.changeLabels(this.step)
                },
                async changeLabels(step){
                    if( step === 1 ) {
                        this.labelBtnOne=' - '
                        this.labelBtnTwo='Siguiente'
                    }
                    else if( step === 2 ) {
                        this.labelBtnOne='Atras'
                        this.labelBtnTwo='Enviar'
                    }
                    else if( step === 3) {
                        const response = await fetch('/wp-admin/admin-ajax.php',{
                            method:'post',
                            headers:{
                                'Content-Type':'application/x-www-form-urlencoded'
                            },
                            body:`action=send_confirm&currency_from=${this.getFromCurrency}&mount_from=${this.mount}&currency_to=${this.getToCurrency}&mount_to=${this.mountReceived}&reference=${this.reference}`
                        })
                        const result = await response.json()
                        this.message = result.message
                    }
                }
            }
        }).use(vuetify).mount('#form-app')
    </script>
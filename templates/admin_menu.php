<div style="display:flex; justify-content:space-between; align-items:center;">
    <h1>Configuracion de Remesas</h1>
    <span id="control-admin-app">
        <v-btn color="red-darken-4" @click="resetall">Reset All</v-btn>
    </span>
</div>
<div id="root-admin-app">
    <v-card>
        <v-tabs v-model="tab" bg-color="primary" @update="changeTab">
            <v-tab value="rate">Tasas</v-tab>
            <v-tab value="found">Fondos</v-tab>
            <v-tab value="operations">Operaciones</v-tab>
        </v-tabs>
        <v-card-text>
            <v-window v-model="tab">
                <v-window-item value="rate">
                    <iframe width="100%" height="500px" src="<?='/wp-content/plugins/eu-remesa/app.php?template=rate&type=html&script=base,rate&token=113'?>"></iframe>
                </v-window-item>
                <v-window-item value="found">
                    <iframe width="100%" height="500px" src="<?='/wp-content/plugins/eu-remesa/app.php?template=found&type=html&script=base,found&token=113'?>"></iframe>
                </v-window-item>
                <v-window-item value="operations">
                    <iframe width="100%" height="500px" src="<?='/wp-content/plugins/eu-remesa/app.php?template=operations&type=html&script=base,confirm&token=113'?>"></iframe>
                </v-window-item>
            </v-window>
        </v-card-text>
    </v-card>
</div>

<!-- <div style="display:flex; flex-direction:row; justify-content:space-around">
    <div id="rate-admin-app" style="width:30%">
        <h2>Tasas</h2>
        <v-text-field
            v-for="(entity, tag) in entities"
            type="number"
            variant="outlined"
            min="1"
            v-model="entities[tag]">
            <template v-slot:prepend>
                <span>Valor del {{tag.toUpperCase()}}</span>
            </template>
        </v-text-field>
        <v-btn variant="outlined" color="primary" style="width:100%" :loading="loading" @click="saveEntityValue">Guardar</v-btn>
    </div>
    <div id="found-admin-app" style="width:30%">
        <h2>Fondos</h2>
        <v-text-field
            v-for="(entity, tag) in entities"
            type="number"
            variant="outlined"
            min="0"
            v-model="entities[tag]">
            <template v-slot:prepend>
                <span>Fondos para {{tag.toUpperCase()}}</span>
            </template>
        </v-text-field>
        <v-btn variant="outlined" color="primary" style="width:100%" :loading="loading" @click="saveEntityValue">Guardar</v-btn>
    </div>
</div>
<div id="history-admin-app" style="width:100%">
    <h2>Operaciones</h2>
    <v-list>
        <v-list-item
            v-for="item in confirms"
            :key="item.id"
            variant="plain"
        >
            <template v-slot:title>
                <span>Numero de referencia: {{item.reference}}</span>
            </template>
            <template v-slot:subtitle>
                <span style="display:flex; flex-direction:column; padding-left:20px;">
                    <span>Monto recibido: {{item.mount_from}} ({{item.currency_from}})</span>
                    <span>Monto entregado: {{item.mount_to}} ({{item.currency_to}})</span>
                </span>
            </template>
        </v-list-item>
    </v-list>
</div> -->

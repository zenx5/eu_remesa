<div style="display:flex; justify-content:space-between; align-items:center;">
    <h1>Configuracion de Remesas</h1>
    <span id="control-admin-app">
        <v-btn color="red-darken-4" @click="resetall">Reset All</v-btn>
    </span>
</div>
<div id="root-admin-app">
    <v-card>
        <v-tabs v-model="tab" bg-color="primary" @update="changeTab">
            <v-tab value="changes">Cambios</v-tab>
            <v-tab value="rate">Tasas</v-tab>
            <v-tab value="found">Fondos</v-tab>
            <v-tab value="operations">Operaciones</v-tab>
        </v-tabs>
        <v-card-text>
            <v-window v-model="tab">
                <v-window-item value="changes">
                    <iframe width="100%" height="500px" src="<?='/wp-content/plugins/eu-remesa/app.php?template=changes&type=html&script=base,changes&token='.$token?>"></iframe>
                </v-window-item>
                <v-window-item value="rate">
                    <iframe width="100%" height="500px" src="<?='/wp-content/plugins/eu-remesa/app.php?template=rate&type=html&script=base,rate&token='.$token?>"></iframe>
                </v-window-item>
                <v-window-item value="found">
                    <iframe width="100%" height="500px" src="<?='/wp-content/plugins/eu-remesa/app.php?template=found&type=html&script=base,found&token='.$token?>"></iframe>
                </v-window-item>
                <v-window-item value="operations">
                    <iframe width="100%" height="500px" src="<?='/wp-content/plugins/eu-remesa/app.php?template=operations&type=html&script=base,confirm&token='.$token?>"></iframe>
                </v-window-item>
            </v-window>
        </v-card-text>
    </v-card>
</div>
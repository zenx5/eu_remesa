<h1>Configuracion de Remesas</h1>
<div style="display:flex; flex-direction:row; justify-content:space-around">
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
        <v-btn variant="outlined" style="width:100%" :loading="loading" @click="saveEntityValue">Guardar</v-btn>
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
        <v-btn variant="outlined" style="width:100%" :loading="loading" @click="saveEntityValue">Guardar</v-btn>
    </div>
</div>
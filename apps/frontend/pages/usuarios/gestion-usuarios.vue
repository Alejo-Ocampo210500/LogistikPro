<template>
    <v-card flat class="mb-4">
        <v-card-title>
            <v-toolbar flat>
                <v-toolbar-title>
                    <div class="d-flex align-center">
                        <v-icon color="warning" class="mr-3" size="30">
                            mdi-account-group
                        </v-icon>
                        <span>Usuarios</span>
                    </div>
                </v-toolbar-title>
                <v-divider class="mx-4" inset vertical></v-divider>
            </v-toolbar>
        </v-card-title>

        <v-card-subtitle class="mb-4 mt-5" align="center">
            <h3>
                <b class="justify-center">
                    <v-icon>mdi-information-slab-circle-outline</v-icon>
                    aca podras administrar los usuarios del sistema, asignarles roles y permisos, y gestionar su acceso
                    a las diferentes funcionalidades de la aplicación.
                </b>
            </h3>
        </v-card-subtitle>

        <v-card-text>
            
        </v-card-text>


    </v-card>
</template>
<script>
export default {
    name: 'gestion-usuarios',
    layout: 'admin',




}
</script>
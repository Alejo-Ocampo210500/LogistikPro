<template>
  <v-card flat class="mb-4">
    <v-card-title class="py-2">
      <v-toolbar flat color="transparent">

        <!-- ENCABEZADO DEL MODULO -->
        <v-toolbar-title>
          <div class="d-flex align-center">
            <v-icon color="accent" class="mr-3" size="30">
              mdi-tag-multiple-outline
            </v-icon>
            <span>Marcas</span>
          </div>
        </v-toolbar-title>
        <v-divider class="mx-4" inset vertical></v-divider>
        <v-spacer></v-spacer>

        <!-- BOTON CREAR MARCA -->
        <v-btn color="accent" large rounded elevation="3" class="text-none font-weight-bold px-5" @click="crearMarca()">
          <v-icon left>mdi-plus-circle</v-icon>
          Crear marca
        </v-btn>
      </v-toolbar>
    </v-card-title>

    <!-- CAMPO DE BUSQUEDA -->
    <v-text-field class="mx-4" v-model="search" append-icon="mdi-magnify" label="Buscar" single-line hide-details>
    </v-text-field>

    <!-- TABLA DE MARCAS -->
    <v-data-table class="mx-4" :headers="headers" :items="marcas" :search="search"
      no-data-text="No se encontraron registros"></v-data-table>
    <v-card-text> </v-card-text>

    <v-row justify="center">
      <v-dialog v-model="dialogoCrearMarca" persistent max-width="600px">
        <v-card rounded="lg" elevation="10">
          <v-toolbar flat color="secondary" dark>
            <v-icon color="accent" class="mr-3">mdi-tag-plus-outline</v-icon>
            <v-toolbar-title class="font-weight-bold">
              Crear nueva marca
            </v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn icon @click="cerrarDialogoCrearMarca()">
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-toolbar>

          <v-card-text class="pt-5 pb-2">
            <v-card rounded="lg" elevation="0" class="mb-4 info-hint-card">
              <v-card-text class="py-3">
                <div class="d-flex align-start">
                  <v-icon color="info" class="mr-3 mt-1">mdi-information-outline</v-icon>
                  <div class="text-body-2">
                    Ingresa la información de la marca que deseas crear. Asegúrate de completar todos los campos
                    requeridos antes de guardar.
                  </div>
                </div>
              </v-card-text>
            </v-card>

            <v-form ref="formCrearMarcaRef" v-model="formCrearMarcaValido" lazy-validation>
              <v-row dense>
                <v-col cols="12" sm="6">
                  <v-text-field v-model="formCrearMarca.nombre" label="Nombre" outlined dense clearable
                    :rules="reglasFormCrearMarca.nombre"></v-text-field>
                </v-col>

                <v-col cols="12" sm="6">
                  <v-text-field v-model="formCrearMarca.codigo" label="Codigo" outlined dense clearable></v-text-field>
                </v-col>

                <v-col cols="12">
                  <v-textarea v-model="formCrearMarca.description" label="Descripcion" outlined dense rows="3" auto-grow
                    no-resize :rules="reglasFormCrearMarca.description"></v-textarea>
                </v-col>
              </v-row>
            </v-form>
          </v-card-text>

          <v-card-actions class="px-6 pb-5">
            <v-spacer></v-spacer>
            <v-btn text color="secondary" @click="cerrarDialogoCrearMarca()">
              Cancelar
            </v-btn>
            <v-btn color="accent" class="text-none font-weight-bold" @click="guardarMarca()">
              Guardar
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-row>
  </v-card>
</template>

<script>
export default {
  name: 'marcas',
  layout: 'admin',

  data() {
    return {
      search: '',
      headers: [
        { text: 'Nombre', value: 'nombre' },
        { text: 'Codigo', value: 'codigo' },
        { text: 'Descripcion', value: 'description' },
        { text: 'Acciones', value: 'actions', sortable: false }
      ],
      marcas: [],
      dialogoCrearMarca: false,
      formCrearMarcaValido: false,
      formCrearMarca: {
        nombre: '',
        codigo: '',
        description: ''
      },
      reglasFormCrearMarca: {
        nombre: [
          (valor) => !!valor || 'El nombre es requerido',
          (valor) => (valor && valor.trim().length >= 2) || 'El nombre debe tener al menos 2 caracteres'
        ],
        description: [
          (valor) => !!valor || 'La descripcion es requerida',
          (valor) => (valor && valor.trim().length >= 5) || 'La descripcion debe tener al menos 5 caracteres'
        ]
      }
    }
  },

  methods: {
    crearMarca() {
      this.dialogoCrearMarca = true
    },

    cerrarDialogoCrearMarca() {
      this.dialogoCrearMarca = false
      this.resetFormCrearMarca()
    },

    guardarMarca() {
      const esValido = this.$refs.formCrearMarcaRef.validate()

      if (!esValido) {
        return
      }

      // Aqui va la peticion al backend cuando se conecte el endpoint.
      this.cerrarDialogoCrearMarca()
    },

    resetFormCrearMarca() {
      this.formCrearMarca = {
        nombre: '',
        codigo: '',
        description: ''
      }

      if (this.$refs.formCrearMarcaRef) {
        this.$refs.formCrearMarcaRef.resetValidation()
      }
    }
  }
}
</script>

<style scoped>
.info-hint-card {
  background: rgba(74, 163, 255, 0.12);
  border: 1px solid rgba(74, 163, 255, 0.3);
}
</style>

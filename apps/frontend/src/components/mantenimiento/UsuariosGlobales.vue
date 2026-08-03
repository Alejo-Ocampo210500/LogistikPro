<template>
    <section class="table-card usuarios-card">

        <div class="table-head">
            <div>
                <span class="table-kicker">Administración global</span>
                <h2>Usuarios del Sistema</h2>
            </div>

            <div class="table-actions">
                <span class="table-count">
                    {{ users.length }} registros
                </span>
            </div>
        </div>

        <!-- TABLA PARA LISTAR LOS USUARIOS GLOBALES-->
        <div class="table-wrap">
            <v-data-table dense :items="users" :headers="headers" hide-default-footer
                class="elevation-1 usuarios-table">

                <template slot="item.nombre" slot-scope="{ item }">
                    <span class="user-name-pill">
                        {{ item.nombre }}
                    </span>
                </template>
                <template slot="item.empresa.nombre_comercial" slot-scope="{ item }">
                    <span class="company-chip">
                        {{ item.empresa ? item.empresa.nombre_comercial : '-' }}
                    </span>
                </template>
                <template slot="item.rol.nombre" slot-scope="{ item }">
                    <span class="role-pill">
                        {{ item.rol ? item.rol.nombre : '-' }}
                    </span>
                </template>
                <template slot="item.estado.nombre" slot-scope="{ item }">
                    <span :class="[
                        'status-pill',
                        item.estado && item.estado.nombre.toLowerCase() === 'activo'
                            ? 'status-ok'
                            : 'status-off'
                    ]">
                        {{ item.estado ? item.estado.nombre : '-' }}
                    </span>
                </template>
                <template slot="item.actions" slot-scope="{ item }">
                    <button type="button" class="action-button-edit" @click="editUsuarioGlobales(item)">
                        <i class="mdi mdi-pencil"></i>
                    </button>
                </template>
            </v-data-table>

            <!-- MODAL EDITAR USUARIO -->
            <v-dialog v-model="dialog" max-width="720px" persistent>
                <v-card class="dialog-card">
                    <v-card-title class="dialog-card-title">
                        <v-avatar size="46" class="dialog-avatar">
                            <v-icon large>
                                mdi-account-group
                            </v-icon>
                        </v-avatar>
                        <div>
                            <span class="dialog-kicker">
                                {{ isEditMode ? 'Editar usuario' : 'Nuevo usuario' }}
                            </span>

                            <h3 class="dialog-title">
                                {{ isEditMode ? 'Actualizar usuario global' : 'Crear usuario global' }}
                            </h3>

                            <p class="dialog-description">
                                Gestiona los datos del usuario y mantén actualizada su información en el sistema.
                            </p>
                        </div>
                    </v-card-title>
                    <v-divider />

                    <v-card-text>
                        <v-form ref="usuarioForm" lazy-validation>
                            <div class="dialog-grid">
                                <v-text-field v-model="form.nombre" label="Nombre" outlined dense required />
                                <v-text-field v-model="form.apellido" label="Apellido" outlined dense required />
                                <v-text-field v-model="form.telefono" label="Teléfono" outlined dense />
                                <v-text-field v-model="form.email" label="Correo electrónico" outlined dense required />
                                <v-select v-model="form.estado_id" :items="estados" item-text="nombre" item-value="id"
                                    label="Estado" outlined dense />
                            </div>

                        </v-form>

                    </v-card-text>
                    <v-divider />
                    <v-card-actions class="dialog-actions">
                        <button type="button" class="secondary-button" @click="dialog = false">
                            Cancelar
                        </button>
                        <button type="button" class="submit-button" @click="actualizarUsuario">
                            Guardar cambios
                        </button>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </div>

    </section>
</template>


<script>
import api from '@/services/api'

export default {

    name: 'UsuariosGlobales',

    data() {
        return {

            users: [],
            estados: [],
            dialog: false,
            isEditMode: false,
            selectedUserId: null,

            form: {
                nombre: '',
                apellido: '',
                telefono: '',
                email: '',
                estado_id: null,
            },

            headers: [
                {
                    text: 'Nombre',
                    value: 'nombre'
                },
                {
                    text: 'Apellido',
                    value: 'apellido'
                },
                {
                    text: 'Teléfono',
                    value: 'telefono'
                },
                {
                    text: 'Correo',
                    value: 'email'
                },
                {
                    text: 'Empresa',
                    value: 'empresa.nombre_comercial'
                },
                {
                    text: 'Rol',
                    value: 'rol.nombre'
                },
                {
                    text: 'Estado',
                    value: 'estado.nombre'
                },
                {
                    text: 'Acciones',
                    value: 'actions',
                    sortable: false
                }
            ]
        }
    },


    mounted() {
        this.obtenerUsuarios()
        this.obtenerEstados()
    },


    methods: {

        async actualizarUsuario() {

            this.$emit('start-action', 'Actualizando usuario global...');

            try {

                await api.put(
                    `/mantenimiento/editar-usuarios-globales/${this.selectedUserId}`,
                    this.form
                );

                this.dialog = false;

                await this.obtenerUsuarios();

                this.$emit('success', 'Usuario actualizado correctamente.');

            } catch (error) {

                this.$emit(
                    'error',
                    error.response?.data?.mensaje || 'No se pudo actualizar el usuario.'
                );

            } finally {

                this.$emit('stop-action');

            }
        },

        async obtenerEstados() {

            const response = await api.get('/estados')

            this.estados = response.data.map(estado => ({
                id: estado.id,
                nombre: estado.nombre
            }))

        },

        editUsuarioGlobales(usuario) {

            this.isEditMode = true;

            this.selectedUserId = usuario.id;

            this.form = {
                nombre: usuario.nombre,
                apellido: usuario.apellido,
                telefono: usuario.telefono,
                email: usuario.email,
                estado_id: usuario.estado_id,
            };

            this.dialog = true;
        },

        async obtenerUsuarios() {

            try {

                const { data } = await api.get('/superadmin/usuarios')

                this.users = data

            } catch (error) {

                console.error(error)

            }

        },


        editUser(user) {
            console.log('Editar usuario', user)
        },


        deleteUser(user) {
            console.log('Eliminar usuario', user)
        }

    }

}
</script>



<style scoped>
.flash {
    margin-bottom: 20px;
    padding: 14px 18px;
    border-radius: 14px;
    font-weight: 700;
}

.flash.success {
    background: #d1fae5;
    color: #065f46;
}

.flash.error {
    background: #fee2e2;
    color: #991b1b;
}

.flash.info {
    background: #dbeafe;
    color: #1e40af;
}

.submit-button {
    border: none;
    border-radius: 14px;
    padding: 11px 18px;
    background: linear-gradient(135deg, #f4b740 0%, #ffd277 45%, #d99210 100%);
    color: #0b1530;
    font-weight: 800;
    cursor: pointer;
    transition: transform 0.2s ease, filter 0.2s ease;
}

.secondary-button {
    border: 1px solid rgba(23, 48, 79, 0.12);
    background: rgba(255, 255, 255, 0.95);
    color: #17304f;
    padding: 11px 18px;
    border-radius: 14px;
    font-weight: 800;
    cursor: pointer;
}

.table-card {
    padding: 24px;
    border-radius: 28px;
    background: rgba(255, 255, 255, .92);
    border: 1px solid rgba(23, 48, 79, .08);
    box-shadow: 0 20px 48px rgba(14, 28, 54, .08);
}


.table-head {

    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 20px;

}


.table-kicker {

    display: block;
    margin-bottom: 6px;
    color: rgba(23, 48, 79, .58);
    text-transform: uppercase;
    letter-spacing: .14em;
    font-size: .72rem;

}


h2 {

    margin: 0;
    font-size: 1.5rem;
    color: #17304f;

}



.table-count {

    padding: 9px 12px;
    border-radius: 999px;
    background: rgba(250, 175, 1, .12);
    color: #996600;
    font-weight: 800;

}



.table-wrap {
    overflow-x: auto;
    overflow-y: visible;
    -webkit-overflow-scrolling: touch;
    overscroll-behavior-x: contain;
}

.usuarios-table .v-data-table__wrapper {
    overflow-x: auto;
    overflow-y: visible;
    -webkit-overflow-scrolling: touch;
}

.usuarios-table .v-data-table__wrapper table {
    width: max-content;
    min-width: 980px;
}



.usuarios-table th,
.usuarios-table td {

    padding: 14px 12px;
    border-bottom: 1px solid rgba(23, 48, 79, .08);

}



.usuarios-table th {

    color: rgba(23, 48, 79, .64);
    font-size: .84rem;
    text-transform: uppercase;
    letter-spacing: .08em;

}



.usuarios-table td {

    color: #17304f;

}



.usuarios-table tbody tr:hover {

    background: rgba(244, 247, 255, .78);

}



.user-name-pill {

    display: inline-flex;
    padding: 8px 12px;
    border-radius: 999px;
    background: rgba(244, 171, 58, .12);
    color: #ad6f00;
    font-weight: 800;

}



.company-chip {

    display: inline-flex;
    padding: 6px 10px;
    border-radius: 999px;
    background: rgba(23, 48, 79, .08);
    color: #17304f;
    font-weight: 700;

}



.role-pill {

    display: inline-flex;
    padding: 6px 10px;
    border-radius: 999px;
    background: rgba(96, 165, 250, .15);
    color: #1d4ed8;
    font-weight: 700;

}



.status-pill {

    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 5px 12px;
    border-radius: 999px;
    font-size: .8rem;
    font-weight: 700;

}



.status-ok {

    background: rgba(109, 211, 160, .18);
    color: #186843;

}



.status-off {

    background: rgba(255, 123, 123, .16);
    color: #9b2f2f;

}




.action-button-edit,
.action-button-delete {

    border: 0;
    border-radius: 999px;
    width: 36px;
    height: 36px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: white;
    cursor: pointer;
    margin-right: 6px;

}



.action-button-edit {

    background: linear-gradient(135deg,
            #3b82f6 0%,
            #60a5fa 45%,
            #1d4ed8 100%);

}

.dialog-card {
    border-radius: 28px;
    overflow: hidden;
}


.dialog-card-title {

    display: flex;
    gap: 18px;
    align-items: center;
    padding: 24px;

}


.dialog-avatar {

    background: linear-gradient(135deg,
            #f4b740 0%,
            #ffd277 45%,
            #d99210 100%);

    color: #0b1530;

}


.dialog-kicker {

    display: block;
    text-transform: uppercase;
    letter-spacing: .14em;
    color: rgba(23, 48, 79, .58);
    font-size: .72rem;
    margin-bottom: 8px;

}


.dialog-title {

    margin: 0;
    font-size: 1.6rem;
    color: #17304f;

}


.dialog-description {

    margin-top: 8px;
    color: rgba(23, 48, 79, .68);
    line-height: 1.6;
    max-width: 520px;

}


.dialog-grid {

    display: grid;
    gap: 18px;

}


.dialog-actions {

    padding: 18px 24px 22px;
    justify-content: flex-end;
    gap: 12px;

}



.action-button-delete {

    background: linear-gradient(135deg,
            #ef4444 0%,
            #f87171 45%,
            #b91c1c 100%);

}



.action-button-edit i,
.action-button-delete i {

    font-size: 16px;

}

@media (max-width: 900px) {
    .table-card {
        padding: 16px;
    }

    .table-head {
        flex-direction: column;
    }

    .table-actions {
        width: 100%;
        justify-content: flex-start;
    }
}
</style>
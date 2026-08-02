<template>
    <section class="categories-shell">
        <article class="table-card">
            <div class="table-head">
                <div>
                    <span class="table-kicker">Inventario</span>
                    <h2>Listado de categorias</h2>
                </div>

                <button type="button" class="submit-button" @click="abrirModalCrear">
                    <i class="mdi mdi-plus"></i>
                    <span>Crear categoria</span>
                </button>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Categoria</th>
                            <th>Descripcion</th>
                            <th>Estado</th>
                            <th>Productos</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in categorias" :key="item.id">
                            <td>
                                <strong>{{ item.nombre }}</strong>
                                <!-- <small>ID: {{ item.id || 'N/A' }}</small> -->
                            </td>
                            <td>{{ item.descripcion || 'Sin descripcion' }}</td>
                            <td>
                                <span :class="['status-pill', estaActiva(item) ? 'status-ok' : 'status-off']">
                                    {{ estaActiva(item) ? 'Activa' : 'Inactiva' }}
                                </span>
                            </td>
                            <td>{{ item.productos_count || 0 }}</td>
                            <td>
                                <div class="row-actions">
                                    <button
                                        type="button"
                                        class="action-button"
                                        @click="abrirModalEditar(item)"
                                        aria-label="Editar categoria"
                                    >
                                        <i class="mdi mdi-pencil"></i>
                                        <span class="button-tooltip">Editar</span>
                                    </button>

                                    <button
                                        type="button"
                                        :class="['action-button', estaActiva(item) ? 'action-disable' : 'action-enable']"
                                        @click="solicitarCambioEstado(item)"
                                        :aria-label="estaActiva(item) ? 'Inactivar categoria' : 'Activar categoria'"
                                    >
                                        <i :class="estaActiva(item) ? 'mdi mdi-close-circle-outline' : 'mdi mdi-check-circle-outline'"></i>
                                        <span class="button-tooltip">{{ estaActiva(item) ? 'Inactivar' : 'Activar' }}</span>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="!categorias.length">
                            <td colspan="5" class="empty-row">No hay categorias registradas.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </article>

        <v-dialog v-model="categoryDialog" max-width="640px" persistent>
            <v-card class="dialog-card">
                <v-card-title class="dialog-card-title">
                    <v-avatar size="46" class="dialog-avatar">
                        <v-icon large>mdi-shape-outline</v-icon>
                    </v-avatar>
                    <div>
                        <span class="dialog-kicker">{{ editMode ? 'Editar categoria' : 'Nueva categoria' }}</span>
                        <h3 class="dialog-title">{{ editMode ? 'Actualizar categoria de inventario' : 'Crear categoria de inventario' }}</h3>
                        <p class="dialog-description">Configura nombre, descripcion y estado. La categoria quedara lista para clasificar productos.</p>
                    </div>
                </v-card-title>

                <v-divider />

                <v-card-text class="dialog-card-body">
                    <div v-if="validationMessage" class="flash error">
                        {{ validationMessage }}
                    </div>

                    <div class="dialog-grid">
                        <label class="field field-full">
                            <span>Nombre de la categoria</span>
                            <input v-model.trim="form.nombre" type="text" placeholder="Ej: Bebidas" />
                        </label>

                        <label class="field field-full">
                            <span>Descripcion</span>
                            <textarea v-model.trim="form.descripcion" rows="3" placeholder="Descripcion corta de la categoria"></textarea>
                        </label>

                        <label class="field field-half">
                            <span>Estado</span>
                            <select v-model.number="form.estado_id">
                                <option v-for="state in estados" :key="state.id" :value="state.id">
                                    {{ state.nombre }}
                                </option>
                            </select>
                        </label>
                    </div>
                </v-card-text>

                <v-divider />

                <v-card-actions class="dialog-actions">
                    <button type="button" class="secondary-button" @click="cerrarModal">
                        Cancelar
                    </button>
                    <button type="button" class="submit-button" data-action-loader="true" @click="guardarCategoria">
                        {{ editMode ? 'Guardar cambios' : 'Crear categoria' }}
                    </button>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <v-dialog v-model="confirmDialog" max-width="520px" persistent>
            <v-card class="dialog-card">
                <v-card-title class="dialog-card-title">
                    <v-avatar size="46" class="dialog-avatar dialog-avatar-alert">
                        <v-icon large>mdi-alert-circle-outline</v-icon>
                    </v-avatar>
                    <div>
                        <span class="dialog-kicker">Confirmar accion</span>
                        <h3 class="dialog-title">{{ pendingAction === 'activar' ? 'Activar categoria' : 'Inactivar categoria' }}</h3>
                    </div>
                </v-card-title>

                <v-divider />

                <v-card-text class="dialog-card-body">
                    <p>
                        {{ pendingAction === 'activar' ? 'Deseas activar la categoria' : 'Deseas inactivar la categoria' }}
                        <strong>"{{ pendingCategory ? pendingCategory.nombre : '' }}"</strong>?
                    </p>
                </v-card-text>

                <v-divider />

                <v-card-actions class="dialog-actions">
                    <button type="button" class="secondary-button" @click="cerrarDialogoConfirmacion">Cancelar</button>
                    <button type="button" class="submit-button" data-action-loader="true" @click="confirmarCambioEstado">Aceptar</button>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </section>
</template>

<script>
import api from '@/services/api';

export default {
    name: 'InventarioCategroiasView',

    props: {
        session: {
            type: Object,
            default: null,
        },
    },

    data() {
        return {
            categoryDialog: false,
            editMode: false,
            editingId: null,
            confirmDialog: false,
            pendingCategory: null,
            pendingAction: '',
            validationMessage: '',
            categorias: [],
            estados: [
                { id: 1, nombre: 'Activo' },
                { id: 2, nombre: 'Inactivo' },
            ],
            form: {
                empresa_id: null,
                nombre: '',
                descripcion: '',
                estado_id: 1,
                creado_por: null,
                actualizado_por: null,
            },
        };
    },

    mounted() {
        this.listarCategorias();
    },

    methods: {
        esperarTresSegundos() {
            return new Promise(resolve => {
                window.setTimeout(resolve, 3000);
            });
        },

        resolverError(error) {
            if (error?.response?.data?.errors) {
                const errores = Object.values(error.response.data.errors).flat();
                return errores[0] || 'No se pudo completar la operacion.';
            }

            if (error?.response?.data?.mensaje) {
                return error.response.data.mensaje;
            }

            return 'No se pudo completar la operacion.';
        },

        async listarCategorias() {
            try {
                const { data } = await api.get('/categorias');
                this.categorias = Array.isArray(data?.categorias) ? data.categorias : [];
            } catch (error) {
                this.categorias = [];
            }
        },

        reiniciarFormulario() {
            this.form = {
                empresa_id: this.session?.empresa_id || this.session?.empresa?.id || null,
                nombre: '',
                descripcion: '',
                estado_id: 1,
                creado_por: this.session?.user?.id || null,
                actualizado_por: this.session?.user?.id || null,
            };
            this.validationMessage = '';
            this.editMode = false;
            this.editingId = null;
        },

        estaActiva(item) {
            return Number(item.estado_id) === 1;
        },

        abrirModalCrear() {
            this.reiniciarFormulario();
            this.categoryDialog = true;
        },

        abrirModalEditar(item) {
            this.validationMessage = '';
            this.editMode = true;
            this.editingId = item.id;
            this.form = {
                empresa_id: item.empresa_id || this.session?.empresa_id || this.session?.empresa?.id || null,
                nombre: item.nombre,
                descripcion: item.descripcion,
                estado_id: Number(item.estado_id) || 1,
                creado_por: item.creado_por || this.session?.user?.id || null,
                actualizado_por: this.session?.user?.id || null,
            };
            this.categoryDialog = true;
        },

        cerrarModal() {
            this.categoryDialog = false;
            this.reiniciarFormulario();
        },

        async guardarCategoria() {
            if (!this.form.nombre) {
                this.validationMessage = 'El nombre de la categoria es obligatorio.';
                return;
            }

            const actionLabel = this.editMode ? 'Guardando cambios de categoria...' : 'Creando categoria...';

            this.$emit('start-action', actionLabel, null, null);

            try {
                await this.esperarTresSegundos();

                if (this.editMode) {
                    const payload = {
                        nombre: this.form.nombre,
                        descripcion: this.form.descripcion,
                        estado_id: this.form.estado_id,
                    };

                    const { data } = await api.put(`/categorias/${this.editingId}/actualizar`, payload);
                    const categoriaActualizada = data?.categoria || data?.data || null;

                    if (categoriaActualizada) {
                        this.categorias = this.categorias.map(item => (item.id === this.editingId ? categoriaActualizada : item));
                    }
                } else {
                    const payload = {
                        nombre: this.form.nombre,
                        descripcion: this.form.descripcion,
                        estado_id: this.form.estado_id,
                    };

                    const { data } = await api.post('/categorias/crear', payload);
                    const categoriaCreada = data?.categoria || data?.data || null;

                    if (categoriaCreada) {
                        this.categorias.unshift(categoriaCreada);
                    } else {
                        const nextId = this.categorias.length
                            ? Math.max(...this.categorias.map(item => Number(item.id) || 0)) + 1
                            : 1;

                        this.categorias.unshift({
                            id: nextId,
                            empresa_id: this.form.empresa_id,
                            nombre: this.form.nombre,
                            descripcion: this.form.descripcion,
                            estado_id: this.form.estado_id,
                            creado_por: this.form.creado_por,
                            actualizado_por: this.form.actualizado_por,
                            productos_count: 0,
                        });
                    }
                }

                this.cerrarModal();
                await this.listarCategorias();
            } catch (error) {
                this.validationMessage = this.resolverError(error);
            } finally {
                this.$emit('stop-action');
            }
        },

        solicitarCambioEstado(item) {
            this.pendingCategory = item;
            this.pendingAction = this.estaActiva(item) ? 'inactivar' : 'activar';
            this.confirmDialog = true;
        },

        cerrarDialogoConfirmacion() {
            this.confirmDialog = false;
            this.pendingCategory = null;
            this.pendingAction = '';
        },

        async confirmarCambioEstado() {
            if (!this.pendingCategory) {
                this.cerrarDialogoConfirmacion();
                return;
            }

            const categoryId = this.pendingCategory.id;
            const actionLabel = `${this.pendingAction === 'activar' ? 'Activando' : 'Inactivando'} categoria...`;

            this.confirmDialog = false;
            this.$emit('start-action', actionLabel, null, null);

            try {
                await this.esperarTresSegundos();

                const { data } = await api.put(`/categorias/${categoryId}/cambiarEstado`);
                const categoriaActualizada = data?.categoria || data?.data || null;

                if (categoriaActualizada) {
                    this.categorias = this.categorias.map(entry => (entry.id === categoryId ? categoriaActualizada : entry));
                }

                await this.listarCategorias();
                this.cerrarDialogoConfirmacion();
            } catch (error) {
                this.validationMessage = this.resolverError(error);
            } finally {
                this.$emit('stop-action');
            }
        },
    },
};
</script>

<style scoped>
.categories-shell {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.table-card {
    padding: 24px;
    border-radius: 24px;
    background: rgba(255, 255, 255, 0.92);
    border: 1px solid rgba(23, 48, 79, 0.08);
    box-shadow: 0 20px 48px rgba(14, 28, 54, 0.08);
}

.table-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 18px;
}

.table-kicker {
    display: block;
    margin-bottom: 6px;
    color: rgba(23, 48, 79, 0.58);
    text-transform: uppercase;
    letter-spacing: 0.14em;
    font-size: 0.72rem;
}

h2 {
    margin: 0;
    font-size: 1.38rem;
    color: #17304f;
}

.table-wrap {
    overflow: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th,
td {
    padding: 13px 12px;
    text-align: left;
    border-bottom: 1px solid rgba(23, 48, 79, 0.08);
}

th {
    color: rgba(23, 48, 79, 0.64);
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
}

td strong,
td small {
    display: block;
}

td small {
    margin-top: 4px;
    color: rgba(23, 48, 79, 0.58);
}

.status-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 84px;
    padding: 7px 11px;
    border-radius: 999px;
    font-weight: 800;
    font-size: 0.78rem;
}

.status-ok {
    background: rgba(109, 211, 160, 0.18);
    color: #186843;
}

.status-off {
    background: rgba(255, 123, 123, 0.16);
    color: #9b2f2f;
}

.row-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.action-button {
    border: 0;
    border-radius: 999px;
    width: 40px;
    height: 40px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    position: relative;
    background: linear-gradient(135deg, #f4b740 0%, #ffd277 45%, #d99210 100%);
    color: #0b1530;
    cursor: pointer;
}

.action-button i {
    font-size: 18px;
}

.action-button.action-enable {
    background: #16a34a;
    color: #ffffff;
}

.action-button.action-disable {
    background: #dc2626;
    color: #ffffff;
}

.button-tooltip {
    position: absolute;
    top: -30px;
    left: 50%;
    transform: translateX(-50%);
    white-space: nowrap;
    opacity: 0;
    visibility: hidden;
    background: rgba(23, 48, 79, 0.96);
    color: #ffffff;
    border-radius: 999px;
    padding: 6px 10px;
    font-size: 0.72rem;
    font-weight: 700;
    pointer-events: none;
    transition: opacity 0.15s ease, visibility 0.15s ease, transform 0.15s ease;
}

.action-button:hover .button-tooltip {
    opacity: 1;
    visibility: visible;
    transform: translateX(-50%) translateY(-4px);
}

.empty-row {
    text-align: center;
    color: rgba(23, 48, 79, 0.58);
    padding: 22px 14px;
}

.dialog-card {
    border-radius: 28px;
    overflow: hidden;
    background: #ffffff;
    box-shadow: 0 26px 62px rgba(15, 34, 65, 0.18);
}

.dialog-card-title {
    display: flex;
    gap: 18px;
    align-items: center;
    padding: 28px 24px 0;
}

.dialog-avatar {
    background: linear-gradient(135deg, #f4b740 0%, #ffd277 45%, #d99210 100%);
    color: #0b1530;
}

.dialog-avatar-alert {
    background: linear-gradient(135deg, #f97316 0%, #fb7185 45%, #ef4444 100%);
    color: #ffffff;
}

.dialog-kicker {
    display: block;
    text-transform: uppercase;
    letter-spacing: 0.14em;
    color: rgba(23, 48, 79, 0.58);
    font-size: 0.72rem;
    margin-bottom: 8px;
}

.dialog-title {
    margin: 0;
    font-size: 1.52rem;
    color: #17304f;
}

.dialog-description {
    margin-top: 8px;
    color: rgba(23, 48, 79, 0.68);
    line-height: 1.55;
    max-width: 520px;
}

.dialog-card-body {
    padding: 20px 24px;
}

.dialog-grid {
    display: grid;
    gap: 16px;
}

.field-full {
    grid-column: 1 / -1;
}

.field-half {
    max-width: 260px;
}

.field {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.field span {
    color: #17304f;
    font-size: 0.84rem;
    font-weight: 700;
}

.field input,
.field textarea,
.field select {
    width: 100%;
    border-radius: 8px;
    border: 1px solid rgba(23, 48, 79, 0.22);
    background: #ffffff;
    color: #17304f;
    padding: 11px 12px;
    outline: none;
}

.field textarea {
    resize: vertical;
    min-height: 82px;
}

.dialog-actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    padding: 14px 24px 20px;
}

.secondary-button {
    border: 1px solid rgba(23, 48, 79, 0.14);
    border-radius: 14px;
    background: rgba(255, 255, 255, 0.95);
    color: #17304f;
    font-weight: 800;
    height: 44px;
    min-width: 122px;
    padding: 0 18px;
}

.submit-button {
    border: 0;
    border-radius: 14px;
    background: linear-gradient(135deg, #f4b740 0%, #ffd277 45%, #d99210 100%);
    color: #0b1530;
    font-weight: 800;
    height: 44px;
    min-width: 122px;
    padding: 0 18px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
}

.flash {
    border-radius: 12px;
    padding: 10px 12px;
    font-size: 0.88rem;
    font-weight: 700;
}

.flash.error {
    background: rgba(255, 123, 123, 0.16);
    color: #9b2f2f;
}

@media (max-width: 900px) {
    .table-head {
        flex-direction: column;
    }
}
</style>
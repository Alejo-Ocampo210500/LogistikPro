<template>
    <section class="marcas-shell">
        <article class="table-card">
            <div class="table-head">
                <div>
                    <span class="table-kicker">Inventario</span>
                    <h2>Listado de marcas</h2>
                </div>

                <button type="button" class="submit-button" @click="abrirModalCrear">
                    <i class="mdi mdi-plus"></i>
                    <span>Crear marca</span>
                </button>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Marca</th>
                            <th>Descripcion</th>
                            <th>Logo</th>
                            <th>Sitio web</th>
                            <th>Estado</th>
                            <th>Productos</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in marcas" :key="item.id">
                            <td>
                                <strong>{{ item.nombre }}</strong>
                            </td>
                            <td>{{ item.descripcion || 'Sin descripcion' }}</td>
                            <td>
                                <img
                                    v-if="item.logo"
                                    :src="item.logo"
                                    :alt="`Logo de ${item.nombre}`"
                                    class="brand-logo"
                                />
                                <span v-else class="site-empty">Sin logo</span>
                            </td>
                            <td>
                                <a
                                    v-if="item.sitio_web"
                                    :href="normalizarUrl(item.sitio_web)"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="site-link"
                                >
                                    {{ item.sitio_web }}
                                </a>
                                <span v-else class="site-empty">Sin sitio web</span>
                            </td>
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
                                        aria-label="Editar marca"
                                    >
                                        <i class="mdi mdi-pencil"></i>
                                        <span class="button-tooltip">Editar</span>
                                    </button>

                                    <button
                                        type="button"
                                        :class="['action-button', estaActiva(item) ? 'action-disable' : 'action-enable']"
                                        @click="solicitarCambioEstado(item)"
                                        :aria-label="estaActiva(item) ? 'Inactivar marca' : 'Activar marca'"
                                    >
                                        <i :class="estaActiva(item) ? 'mdi mdi-close-circle-outline' : 'mdi mdi-check-circle-outline'"></i>
                                        <span class="button-tooltip">{{ estaActiva(item) ? 'Inactivar' : 'Activar' }}</span>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="!marcas.length">
                            <td colspan="7" class="empty-row">No hay marcas registradas.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </article>

        <v-dialog v-model="marcaDialog" max-width="680px" persistent>
            <v-card class="dialog-card">
                <v-card-title class="dialog-card-title">
                    <v-avatar size="46" class="dialog-avatar">
                        <v-icon large>mdi-rocket-launch</v-icon>
                    </v-avatar>
                    <div>
                        <span class="dialog-kicker">{{ editMode ? 'Editar marca' : 'Nueva marca' }}</span>
                        <h3 class="dialog-title">{{ editMode ? 'Actualizar marca ' : 'Crear marca ' }}</h3>
                        <p class="dialog-description">Configura la marca con descripcion, logo, sitio web y estado para mantener tu catalogo ordenado.</p>
                    </div>
                </v-card-title>

                <v-divider />

                <v-card-text class="dialog-card-body">
                    <div v-if="validationMessage" class="flash error">
                        {{ validationMessage }}
                    </div>

                    <div class="dialog-grid">
                        <label class="field field-full">
                            <span>Nombre de la marca</span>
                            <input v-model.trim="form.nombre" type="text" placeholder="Ej: Coca Cola" />
                        </label>

                        <label class="field field-full">
                            <span>Descripcion</span>
                            <textarea v-model.trim="form.descripcion" rows="3" placeholder="Descripcion corta de la marca"></textarea>
                        </label>

                        <label class="field field-full">
                            <span>Logo</span>
                            <input v-model.trim="form.logo" type="text" placeholder="Ej: https://cdn.tuempresa.com/logo.png" />
                            <div class="logo-preview-frame">
                                <img v-if="form.logo" :src="form.logo" alt="Vista previa de logo" class="logo-preview" />
                                <span v-else class="site-empty">Sin logo cargado</span>
                            </div>
                        </label>

                        <label class="field field-full">
                            <span>Sitio web</span>
                            <input v-model.trim="form.sitio_web" type="text" placeholder="Ej: coca-cola.com" />
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
                    <button type="button" class="submit-button" data-action-loader="true" @click="guardarMarca">
                        {{ editMode ? 'Guardar cambios' : 'Crear marca' }}
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
                        <h3 class="dialog-title">{{ pendingAction === 'activar' ? 'Activar marca' : 'Inactivar marca' }}</h3>
                    </div>
                </v-card-title>

                <v-divider />

                <v-card-text class="dialog-card-body">
                    <p>
                        {{ pendingAction === 'activar' ? 'Deseas activar la marca' : 'Deseas inactivar la marca' }}
                        <strong>"{{ pendingMarca ? pendingMarca.nombre : '' }}"</strong>?
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
    name: 'InventarioMarcasView',

    props: {
        session: {
            type: Object,
            default: null,
        },
    },

    data() {
        return {
            marcaDialog: false,
            editMode: false,
            editingId: null,
            confirmDialog: false,
            pendingMarca: null,
            pendingAction: '',
            validationMessage: '',
            marcas: [],
            estados: [
                { id: 1, nombre: 'Activo' },
                { id: 2, nombre: 'Inactivo' },
            ],
            form: {
                empresa_id: null,
                nombre: '',
                descripcion: '',
                logo: '',
                sitio_web: '',
                estado_id: 1,
                creado_por: null,
                actualizado_por: null,
            },
        };
    },

    mounted() {
        this.listarMarcas();
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

        normalizarUrl(url) {
            if (!url) {
                return '';
            }

            return /^https?:\/\//i.test(url) ? url : `https://${url}`;
        },

        async listarMarcas() {
            try {
                const { data } = await api.get('/marcas');
                this.marcas = Array.isArray(data?.marcas) ? data.marcas : Array.isArray(data?.data) ? data.data : [];
            } catch (error) {
                this.marcas = [];
            }
        },

        reiniciarFormulario() {
            this.form = {
                empresa_id: this.session?.empresa_id || this.session?.empresa?.id || null,
                nombre: '',
                descripcion: '',
                logo: '',
                sitio_web: '',
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
            this.marcaDialog = true;
        },

        abrirModalEditar(item) {
            this.validationMessage = '';
            this.editMode = true;
            this.editingId = item.id;
            this.form = {
                empresa_id: item.empresa_id || this.session?.empresa_id || this.session?.empresa?.id || null,
                nombre: item.nombre || '',
                descripcion: item.descripcion || '',
                logo: item.logo || '',
                sitio_web: item.sitio_web || '',
                estado_id: Number(item.estado_id) || 1,
                creado_por: item.creado_por || this.session?.user?.id || null,
                actualizado_por: this.session?.user?.id || null,
            };
            this.marcaDialog = true;
        },

        cerrarModal() {
            this.marcaDialog = false;
            this.reiniciarFormulario();
        },

        async guardarMarca() {
            if (!this.form.nombre) {
                this.validationMessage = 'El nombre de la marca es obligatorio.';
                return;
            }

            if (this.form.logo && this.form.logo.length > 255) {
                this.validationMessage = 'El logo debe ser una URL o ruta corta de maximo 255 caracteres.';
                return;
            }

            if (this.form.logo && this.form.logo.startsWith('data:')) {
                this.validationMessage = 'El logo no puede enviarse en base64. Usa una URL o ruta del archivo.';
                return;
            }

            const actionLabel = this.editMode ? 'Guardando cambios de marca...' : 'Creando marca...';
            this.$emit('start-action', actionLabel, null, null);

            try {
                await this.esperarTresSegundos();

                if (this.editMode) {
                    const payload = {
                        nombre: this.form.nombre,
                        descripcion: this.form.descripcion,
                        logo: this.form.logo,
                        sitio_web: this.form.sitio_web,
                        estado_id: this.form.estado_id,
                    };

                    const { data } = await api.put(`/marcas/${this.editingId}/actualizar`, payload);
                    const marcaActualizada = data?.marca || data?.marcas || data?.data || null;

                    if (marcaActualizada) {
                        this.marcas = this.marcas.map(item => (item.id === this.editingId ? marcaActualizada : item));
                    }
                } else {
                    const payload = {
                        nombre: this.form.nombre,
                        descripcion: this.form.descripcion,
                        logo: this.form.logo,
                        sitio_web: this.form.sitio_web,
                        estado_id: this.form.estado_id,
                    };

                    const { data } = await api.post('/marcas/crearMarcasCliente', payload);
                    const marcaCreada = data?.marcas || data?.marca || data?.data || null;

                    if (marcaCreada) {
                        this.marcas.unshift(marcaCreada);
                    } else {
                        const nextId = this.marcas.length
                            ? Math.max(...this.marcas.map(item => Number(item.id) || 0)) + 1
                            : 1;

                        this.marcas.unshift({
                            id: nextId,
                            empresa_id: this.form.empresa_id,
                            nombre: this.form.nombre,
                            descripcion: this.form.descripcion,
                            logo: this.form.logo,
                            sitio_web: this.form.sitio_web,
                            estado_id: this.form.estado_id,
                            creado_por: this.form.creado_por,
                            actualizado_por: this.form.actualizado_por,
                            productos_count: 0,
                        });
                    }
                }

                this.cerrarModal();
                await this.listarMarcas();
            } catch (error) {
                this.validationMessage = this.resolverError(error);
            } finally {
                this.$emit('stop-action');
            }
        },

        solicitarCambioEstado(item) {
            this.pendingMarca = item;
            this.pendingAction = this.estaActiva(item) ? 'inactivar' : 'activar';
            this.confirmDialog = true;
        },

        cerrarDialogoConfirmacion() {
            this.confirmDialog = false;
            this.pendingMarca = null;
            this.pendingAction = '';
        },

        async confirmarCambioEstado() {
            if (!this.pendingMarca) {
                this.cerrarDialogoConfirmacion();
                return;
            }

            const marcaId = this.pendingMarca.id;
            const actionLabel = `${this.pendingAction === 'activar' ? 'Activando' : 'Inactivando'} marca...`;

            this.confirmDialog = false;
            this.$emit('start-action', actionLabel, null, null);

            try {
                await this.esperarTresSegundos();

                const { data } = await api.post(`/marcas/${marcaId}/cambiarEstado`);
                const marcaActualizada = data?.marca || data?.marcas || data?.data || null;

                if (marcaActualizada) {
                    this.marcas = this.marcas.map(entry => (entry.id === marcaId ? marcaActualizada : entry));
                }

                await this.listarMarcas();
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
.marcas-shell {
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

.site-link {
    color: #17304f;
    font-weight: 700;
    text-decoration: none;
    border-bottom: 1px dashed rgba(23, 48, 79, 0.35);
}

.site-empty {
    color: rgba(23, 48, 79, 0.58);
}

.brand-logo {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    object-fit: cover;
    border: 1px solid rgba(23, 48, 79, 0.12);
    background: #ffffff;
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

.logo-preview-frame {
    border-radius: 14px;
    border: 1px solid rgba(23, 48, 79, 0.12);
    background: linear-gradient(180deg, rgba(248, 250, 253, 0.95) 0%, rgba(244, 248, 252, 0.9) 100%);
    padding: 12px;
    min-height: 92px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.logo-preview {
    width: 88px;
    height: 88px;
    border-radius: 12px;
    object-fit: cover;
    border: 1px solid rgba(23, 48, 79, 0.16);
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

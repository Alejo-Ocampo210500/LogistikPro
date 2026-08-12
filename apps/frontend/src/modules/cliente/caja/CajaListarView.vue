<template>
    <section class="cajas-shell">
        <article class="hero-card">
            <div>
                <span class="hero-kicker">Caja</span>
                <h2>Centro de cajas operativas</h2>
                <p>
                    Controla cada punto de cobro con precisión: crea, edita y consulta el detalle técnico
                    de tus cajas en un solo módulo.
                </p>
            </div>
        </article>

        <article class="toolbar-card">
            <label class="search-field" for="search-cajas">
                <i class="mdi mdi-magnify"></i>
                <input
                    id="search-cajas"
                    v-model.trim="search"
                    type="text"
                    placeholder="Buscar por código, nombre, sucursal o impresora"
                />
            </label>

            <button type="button" class="submit-button" @click="abrirModalCrear">
                <i class="mdi mdi-plus"></i>
                <span>Nueva caja</span>
            </button>
        </article>

        <section class="kpi-grid" aria-label="Metricas de cajas">
            <article class="kpi-card kpi-card--total">
                <div class="kpi-head">
                    <span>Total cajas</span>
                    <i class="mdi mdi-cash-register"></i>
                </div>
                <strong class="kpi-value">{{ totalCajas }}</strong>
                <small class="kpi-note">Puntos de caja registrados</small>
            </article>

            <article class="kpi-card kpi-card--branches">
                <div class="kpi-head">
                    <span>Sucursales con caja</span>
                    <i class="mdi mdi-office-building-marker-outline"></i>
                </div>
                <strong class="kpi-value">{{ totalSucursalesConCaja }}</strong>
                <small class="kpi-note">Cobertura de operación</small>
            </article>

            <article class="kpi-card kpi-card--printers">
                <div class="kpi-head">
                    <span>Con impresora</span>
                    <i class="mdi mdi-printer-pos"></i>
                </div>
                <strong class="kpi-value">{{ totalConImpresora }}</strong>
                <small class="kpi-note">Listas para facturación</small>
            </article>

            <article class="kpi-card kpi-card--states">
                <div class="kpi-head">
                    <span>Cajas activas</span>
                    <i class="mdi mdi-shield-check-outline"></i>
                </div>
                <strong class="kpi-value">{{ totalActivas }}</strong>
                <small class="kpi-note">Listas para operación</small>
            </article>
        </section>

        <section v-if="loading" class="cards-grid cards-grid--loading" aria-label="Cargando cajas">
            <article v-for="n in 6" :key="`skeleton-${n}`" class="caja-card skeleton-card">
                <div class="skeleton-line skeleton-line--lg"></div>
                <div class="skeleton-line"></div>
                <div class="skeleton-line"></div>
                <div class="skeleton-line skeleton-line--sm"></div>
            </article>
        </section>

        <section v-else class="cards-grid" aria-label="Tarjetas de cajas">
            <article
                v-for="item in filteredCajas"
                :key="item.id"
                class="caja-card"
            >
                <header class="card-head">
                    <div>
                        <small class="card-code">{{ item.codigo || 'SIN-CODIGO' }}</small>
                        <h3>{{ item.nombre || 'Caja sin nombre' }}</h3>
                    </div>
                    <span :class="['status-pill', esCajaActiva(item) ? 'status-ok' : 'status-off']">
                        {{ item.estado_nombre || (esCajaActiva(item) ? 'Activo' : 'Inactivo') }}
                    </span>
                </header>

                <div class="cash-strip">
                    <span class="cash-dot"></span>
                    <strong>{{ item.sucursal_nombre || 'Sucursal sin asignar' }}</strong>
                    <small>{{ item.impresora || 'Sin impresora' }}</small>
                </div>

                <div class="card-body">
                    <article class="stat-chip">
                        <i class="mdi mdi-office-building-outline"></i>
                        <div>
                            <span>Sucursal</span>
                            <strong>{{ item.sucursal_nombre || 'Sin asignar' }}</strong>
                        </div>
                    </article>

                    <article class="stat-chip">
                        <i class="mdi mdi-printer-pos"></i>
                        <div>
                            <span>Impresora</span>
                            <strong>{{ item.impresora ? 'Configurada' : 'Pendiente' }}</strong>
                        </div>
                    </article>

                    <article class="stat-chip stat-chip--wide">
                        <i class="mdi mdi-text-box-outline"></i>
                        <div>
                            <span>Descripción operativa</span>
                            <strong>{{ item.descripcion || 'Sin descripción' }}</strong>
                        </div>
                    </article>
                </div>

                <footer class="card-actions">
                    <button
                        type="button"
                        class="action-button action-edit"
                        @click="abrirModalEditar(item)"
                        aria-label="Editar caja"
                    >
                        <i class="mdi mdi-pencil"></i>
                        <span class="button-tooltip">Editar</span>
                    </button>

                    <button
                        type="button"
                        class="action-button action-view"
                        @click="abrirModalDetalle(item)"
                        aria-label="Ver detalle caja"
                    >
                        <i class="mdi mdi-eye-outline"></i>
                        <span class="button-tooltip">Ver detalle</span>
                    </button>

                    <button
                        type="button"
                        :class="['action-button', esCajaActiva(item) ? 'action-disable' : 'action-enable']"
                        @click="solicitarCambioEstado(item)"
                        :aria-label="esCajaActiva(item) ? 'Inactivar caja' : 'Activar caja'"
                    >
                        <i :class="esCajaActiva(item) ? 'mdi mdi-close-circle-outline' : 'mdi mdi-check-circle-outline'"></i>
                        <span class="button-tooltip">{{ esCajaActiva(item) ? 'Inactivar' : 'Activar' }}</span>
                    </button>
                </footer>
            </article>

            <article v-if="!filteredCajas.length" class="empty-card">
                <i class="mdi mdi-cash-register"></i>
                <h4>No hay cajas para mostrar</h4>
                <p>Crea una nueva caja o ajusta el filtro de búsqueda.</p>
            </article>
        </section>

        <v-dialog v-model="cajaDialog" max-width="860px" persistent>
            <v-card class="dialog-card">
                <v-card-title class="dialog-card-title">
                    <v-avatar size="46" class="dialog-avatar">
                        <v-icon large>{{ editMode ? 'mdi-cash-register' : 'mdi-cash-register-plus' }}</v-icon>
                    </v-avatar>
                    <div>
                        <span class="dialog-kicker">{{ editMode ? 'Editar caja' : 'Nueva caja' }}</span>
                        <h3 class="dialog-title">{{ editMode ? 'Actualizar caja operativa' : 'Crear caja operativa' }}</h3>
                        <p class="dialog-description">
                            Configura identificación, sucursal, estado e impresora de tu punto de caja.
                        </p>
                    </div>
                </v-card-title>

                <v-divider />

                <v-card-text class="dialog-card-body">
                    <div v-if="validationMessage" class="flash error">
                        {{ validationMessage }}
                    </div>

                    <div class="dialog-grid dialog-grid--cols-2">
                        <label class="field field-half">
                            <span>Código</span>
                            <input v-model.trim="form.codigo" type="text" placeholder="Ej: POS-001" />
                        </label>

                        <label class="field field-half">
                            <span>Nombre</span>
                            <input v-model.trim="form.nombre" type="text" placeholder="Ej: Caja principal" />
                        </label>

                        <label class="field field-half">
                            <span>Sucursal</span>
                            <select v-model.number="form.sucursal_id">
                                <option :value="null">Selecciona una sucursal</option>
                                <option v-for="sucursal in sucursales" :key="sucursal.id" :value="sucursal.id">
                                    {{ sucursal.nombre || 'Sucursal sin nombre' }}
                                </option>
                            </select>
                        </label>

                        <label class="field field-half">
                            <span>Estado</span>
                            <select v-model.number="form.estado_id">
                                <option :value="null">Selecciona estado</option>
                                <option v-for="estado in estados" :key="estado.id" :value="estado.id">
                                    {{ estado.nombre }}
                                </option>
                            </select>
                        </label>

                        <label class="field field-half">
                            <span>Impresora</span>
                            <input v-model.trim="form.impresora" type="text" placeholder="Ej: EPSON-TM-T20III" />
                        </label>

                        <label class="field field-full">
                            <span>Descripción</span>
                            <textarea v-model.trim="form.descripcion" rows="3" placeholder="Descripción operativa de la caja"></textarea>
                        </label>
                    </div>
                </v-card-text>

                <v-divider />

                <v-card-actions class="dialog-actions">
                    <button type="button" class="secondary-button" @click="cerrarModal">Cancelar</button>
                    <button type="button" class="submit-button" data-action-loader="true" @click="guardarCaja">
                        {{ editMode ? 'Guardar cambios' : 'Crear caja' }}
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
                        <span class="dialog-kicker">Confirmar acción</span>
                        <h3 class="dialog-title">{{ pendingAction === 'activar' ? 'Activar caja' : 'Inactivar caja' }}</h3>
                    </div>
                </v-card-title>

                <v-divider />

                <v-card-text class="dialog-card-body">
                    <p>
                        {{ pendingAction === 'activar' ? 'Vas a activar la caja' : 'Vas a inactivar la caja' }}
                        <strong>"{{ pendingCaja ? pendingCaja.nombre : '' }}"</strong>.
                    </p>
                </v-card-text>

                <v-divider />

                <v-card-actions class="dialog-actions">
                    <button type="button" class="secondary-button" @click="cerrarDialogoConfirmacion">Cancelar</button>
                    <button type="button" class="submit-button" data-action-loader="true" @click="confirmarCambioEstadoCaja">Aceptar</button>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <v-dialog v-model="detalleDialog" max-width="920px" persistent>
            <v-card class="dialog-card detail-dialog-card">
                <v-card-title class="dialog-card-title detail-header">
                    <v-avatar size="48" class="dialog-avatar detail-avatar">
                        <v-icon large>mdi-cash-register</v-icon>
                    </v-avatar>

                    <div class="detail-header-copy">
                        <span class="dialog-kicker">Ficha técnica de caja</span>
                        <h3 class="dialog-title">{{ detalleCaja.nombre || 'Caja sin nombre' }}</h3>
                        <p class="detail-subtitle">Código {{ detalleCaja.codigo || 'N/A' }} · Sucursal {{ detalleCaja.sucursal_nombre || 'Sin asignar' }}</p>
                    </div>
                </v-card-title>

                <v-divider />

                <v-card-text class="dialog-card-body detail-body">
                    <section class="detail-kpis" aria-label="Resumen de caja">
                        <article class="detail-kpi">
                            <span>Sucursal</span>
                            <strong>{{ detalleCaja.sucursal_nombre || 'Sin nombre' }}</strong>
                            <small>Punto asignado</small>
                        </article>

                        <article class="detail-kpi">
                            <span>Impresora</span>
                            <strong>{{ detalleCaja.impresora || 'No configurada' }}</strong>
                            <small>Dispositivo asociado</small>
                        </article>

                        <article class="detail-kpi">
                            <span>Código</span>
                            <strong>{{ detalleCaja.codigo || 'N/A' }}</strong>
                            <small>Identificador comercial</small>
                        </article>

                        <article :class="['detail-state-card', esCajaActiva(detalleCaja) ? 'detail-state-card--active' : 'detail-state-card--inactive']">
                            <span>Estado actual</span>
                            <strong>{{ esCajaActiva(detalleCaja) ? 'ACTIVO' : 'INACTIVO' }}</strong>
                            <small>
                                {{ esCajaActiva(detalleCaja) ? 'Disponible para operación' : 'No disponible para operación' }}
                            </small>
                        </article>
                    </section>

                    <section class="detail-grid" aria-label="Detalle completo de caja">
                        <article class="detail-panel">
                            <h4>Identificación</h4>
                            <ul>
                                <li><span>Código</span><strong>{{ detalleCaja.codigo || 'N/A' }}</strong></li>
                                <li><span>Nombre</span><strong>{{ detalleCaja.nombre || 'N/A' }}</strong></li>
                                <li><span>Tipo</span><strong>Caja operativa</strong></li>
                                <li><span>Módulo</span><strong>Gestión de cajas</strong></li>
                            </ul>
                        </article>

                        <article class="detail-panel">
                            <h4>Operación</h4>
                            <ul>
                                <li><span>Sucursal</span><strong>{{ detalleCaja.sucursal_nombre || 'N/A' }}</strong></li>
                                <li><span>Impresora</span><strong>{{ detalleCaja.impresora || 'Sin dato' }}</strong></li>
                                <li><span>Estado</span><strong>{{ esCajaActiva(detalleCaja) ? 'Activo' : 'Inactivo' }}</strong></li>
                                <li><span>Descripción</span><strong>{{ detalleCaja.descripcion || 'Sin dato' }}</strong></li>
                            </ul>
                        </article>

                        <article class="detail-panel detail-panel--wide">
                            <h4>Trazabilidad</h4>
                            <ul>
                                <li><span>Creado</span><strong>{{ formatearFecha(detalleCaja.created_at) }}</strong></li>
                                <li><span>Actualizado</span><strong>{{ formatearFecha(detalleCaja.updated_at) }}</strong></li>
                                <li><span>Último estado</span><strong>{{ esCajaActiva(detalleCaja) ? 'Activo' : 'Inactivo' }}</strong></li>
                            </ul>
                        </article>
                    </section>
                </v-card-text>

                <v-divider />

                <v-card-actions class="dialog-actions">
                    <button type="button" class="secondary-button" @click="cerrarModalDetalle">Cerrar</button>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </section>
</template>

<script>
import api from '@/services/api';

export default {
    name: 'CajaListarView',

    props: {
        session: {
            type: Object,
            default: null,
        },
    },

    data() {
        return {
            loading: false,
            cajaDialog: false,
            detalleDialog: false,
            confirmDialog: false,
            editMode: false,
            editingId: null,
            validationMessage: '',
            search: '',
            cajas: [],
            sucursales: [],
            estados: [],
            pendingCaja: null,
            pendingAction: '',
            detalleCaja: {},
            form: this.formularioBase(),
        };
    },

    computed: {
        filteredCajas() {
            const term = this.search.toLowerCase();

            if (!term) {
                return this.cajas;
            }

            return this.cajas.filter((item) =>
                [
                    item.codigo,
                    item.nombre,
                    item.descripcion,
                    item.impresora,
                    item.sucursal_nombre,
                    item.estado_nombre,
                ]
                    .join(' ')
                    .toLowerCase()
                    .includes(term)
            );
        },

        totalCajas() {
            return this.cajas.length;
        },

        totalSucursalesConCaja() {
            const unique = new Set(this.cajas.map((item) => Number(item.sucursal_id || 0)).filter(Boolean));
            return unique.size;
        },

        totalConImpresora() {
            return this.cajas.filter((item) => String(item.impresora || '').trim() !== '').length;
        },

        totalActivas() {
            return this.cajas.filter((item) => this.esCajaActiva(item)).length;
        },
    },

    mounted() {
        this.inicializarVista();
    },

    methods: {
        formularioBase() {
            return {
                sucursal_id: null,
                codigo: '',
                nombre: '',
                descripcion: '',
                impresora: '',
                estado_id: null,
            };
        },

        async inicializarVista() {
            this.loading = true;

            try {
                await this.cargarCatalogos();
                await this.listarCajas();
                this.establecerEstadoActivoPorDefecto();
            } finally {
                this.loading = false;
            }
        },

        esperarTresSegundos() {
            return new Promise((resolve) => {
                window.setTimeout(resolve, 3000);
            });
        },

        resolverError(error) {
            if (error?.response?.data?.errors) {
                const errores = Object.values(error.response.data.errors).flat();
                return errores[0] || 'No se pudo completar la operación.';
            }

            if (error?.response?.data?.mensaje) {
                return error.response.data.mensaje;
            }

            return 'No se pudo completar la operación.';
        },

        extraerLista(payload, keys = []) {
            if (Array.isArray(payload)) {
                return payload;
            }

            if (payload && typeof payload === 'object') {
                for (const key of keys) {
                    if (Array.isArray(payload[key])) {
                        return payload[key];
                    }
                }

                const firstArray = Object.values(payload).find((value) => Array.isArray(value));
                return Array.isArray(firstArray) ? firstArray : [];
            }

            return [];
        },

        normalizarTexto(value) {
            return String(value || '')
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .trim()
                .toLowerCase();
        },

        obtenerEstadoIdPorNombre(nombreObjetivo) {
            const objetivo = this.normalizarTexto(nombreObjetivo);

            if (!objetivo) {
                return null;
            }

            const estado = this.estados.find((item) => this.normalizarTexto(item?.nombre) === objetivo);
            return estado?.id ? Number(estado.id) : null;
        },

        obtenerEstadoActivoId() {
            return this.obtenerEstadoIdPorNombre('activo') || 1;
        },

        obtenerEstadoInactivoId() {
            return this.obtenerEstadoIdPorNombre('inactivo') || 2;
        },

        esCajaActiva(item) {
            const estadoId = Number(item?.estado_id || 0);
            if (estadoId > 0) {
                return estadoId === this.obtenerEstadoActivoId();
            }

            const estadoNombre = item?.estado_nombre || item?.estado?.nombre || '';
            return this.normalizarTexto(estadoNombre) === 'activo';
        },

        establecerEstadoActivoPorDefecto() {
            if (this.form.estado_id) {
                return;
            }

            const activo = this.estados.find((estado) => this.normalizarTexto(estado?.nombre) === 'activo');
            this.form.estado_id = activo?.id ? Number(activo.id) : 1;
        },

        async cargarCatalogos() {
            const responses = await Promise.allSettled([
                api.get('/sucursales'),
                api.get('/estados'),
            ]);

            const [sucursalesRes, estadosRes] = responses;

            this.sucursales = sucursalesRes.status === 'fulfilled'
                ? this.extraerLista(sucursalesRes.value?.data, ['sucursales'])
                : [];

            this.estados = estadosRes.status === 'fulfilled'
                ? this.extraerLista(estadosRes.value?.data, ['estados'])
                : [];
        },

        normalizarCaja(item) {
            const sucursal = this.sucursales.find((it) => Number(it.id) === Number(item.sucursal_id));
            const estado = this.estados.find((it) => Number(it.id) === Number(item.estado_id));

            return {
                ...item,
                sucursal_nombre: item.sucursal?.nombre || item.sucursal_nombre || sucursal?.nombre || '',
                estado_nombre: item.estado?.nombre || item.estado_nombre || estado?.nombre || '',
            };
        },

        async listarCajas() {
            try {
                const { data } = await api.get('/cajas');
                const listado = this.extraerLista(data, ['cajas']);
                this.cajas = listado.map((item) => this.normalizarCaja(item));
            } catch (error) {
                this.cajas = [];
            }
        },

        abrirModalCrear() {
            this.validationMessage = '';
            this.editMode = false;
            this.editingId = null;
            this.form = this.formularioBase();
            this.establecerEstadoActivoPorDefecto();
            this.cajaDialog = true;
        },

        abrirModalEditar(item) {
            this.validationMessage = '';
            this.editMode = true;
            this.editingId = Number(item.id);

            this.form = {
                sucursal_id: Number(item.sucursal_id) || null,
                codigo: item.codigo || '',
                nombre: item.nombre || '',
                descripcion: item.descripcion || '',
                impresora: item.impresora || '',
                estado_id: Number(item.estado_id) || null,
            };

            this.cajaDialog = true;
        },

        cerrarModal() {
            this.cajaDialog = false;
            this.validationMessage = '';
            this.editMode = false;
            this.editingId = null;
            this.form = this.formularioBase();
        },

        abrirModalDetalle(item) {
            this.detalleCaja = { ...item };
            this.detalleDialog = true;
        },

        cerrarModalDetalle() {
            this.detalleDialog = false;
            this.detalleCaja = {};
        },

        solicitarCambioEstado(item) {
            this.pendingCaja = item;
            this.pendingAction = this.esCajaActiva(item) ? 'inactivar' : 'activar';
            this.confirmDialog = true;
        },

        cerrarDialogoConfirmacion() {
            this.confirmDialog = false;
            this.pendingCaja = null;
            this.pendingAction = '';
        },

        async confirmarCambioEstadoCaja() {
            if (!this.pendingCaja?.id) {
                this.cerrarDialogoConfirmacion();
                return;
            }

            const activar = this.pendingAction === 'activar';
            const estadoId = activar ? this.obtenerEstadoActivoId() : this.obtenerEstadoInactivoId();

            this.$emit('start-action', activar ? 'Activando caja...' : 'Inactivando caja...', null, null);

            try {
                await this.esperarTresSegundos();

                await api.post(`/cajas/${this.pendingCaja.id}/cambiarEstado`, {
                    estado_id: estadoId,
                });

                await this.listarCajas();
                this.cerrarDialogoConfirmacion();
            } catch (error) {
                this.validationMessage = this.resolverError(error);
            } finally {
                this.$emit('stop-action');
            }
        },

        validarFormulario() {
            if (!this.form.sucursal_id) {
                return 'Debes seleccionar una sucursal.';
            }

            if (!this.form.nombre) {
                return 'El nombre de la caja es obligatorio.';
            }

            if (!this.form.descripcion) {
                return 'La descripción de la caja es obligatoria.';
            }

            return '';
        },

        async guardarCaja() {
            this.validationMessage = this.validarFormulario();

            if (this.validationMessage) {
                return;
            }

            const payload = {
                sucursal_id: this.form.sucursal_id,
                codigo: this.form.codigo || null,
                nombre: this.form.nombre,
                descripcion: this.form.descripcion,
                impresora: this.form.impresora || null,
                estado_id: this.form.estado_id || null,
            };

            const actionLabel = this.editMode ? 'Actualizando caja...' : 'Creando caja...';
            this.$emit('start-action', actionLabel, null, null);

            try {
                await this.esperarTresSegundos();

                if (this.editMode && this.editingId) {
                    await api.put(`/cajas/${this.editingId}/actualizar`, payload);
                } else {
                    await api.post('/cajas/crear', payload);
                }

                this.cerrarModal();
                await this.listarCajas();
            } catch (error) {
                this.validationMessage = this.resolverError(error);
            } finally {
                this.$emit('stop-action');
            }
        },

        formatearFecha(value) {
            if (!value) {
                return 'Sin dato';
            }

            const fecha = new Date(value);

            if (Number.isNaN(fecha.getTime())) {
                return String(value);
            }

            return new Intl.DateTimeFormat('es-CO', {
                dateStyle: 'medium',
                timeStyle: 'short',
            }).format(fecha);
        },
    },
};
</script>

<style scoped>
.cajas-shell {
    display: flex;
    flex-direction: column;
    gap: 18px;
}

.hero-card {
    border-radius: 24px;
    padding: 22px;
    border: 1px solid rgba(23, 48, 79, 0.12);
    background:
        radial-gradient(circle at 100% 0, rgba(244, 183, 64, 0.18), transparent 40%),
        linear-gradient(140deg, #ffffff 0%, #f4f9ff 100%);
    box-shadow: 0 18px 38px rgba(14, 28, 54, 0.08);
}

.hero-kicker {
    display: inline-block;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    font-size: 0.69rem;
    color: rgba(23, 48, 79, 0.62);
}

.hero-card h2 {
    margin: 8px 0 10px;
    color: #17304f;
    font-size: clamp(1.2rem, 2.1vw, 1.58rem);
}

.hero-card p {
    margin: 0;
    color: rgba(23, 48, 79, 0.74);
    line-height: 1.55;
    max-width: 780px;
}

.toolbar-card {
    border-radius: 14px;
    border: 1px solid rgba(23, 48, 79, 0.12);
    background: #ffffff;
    padding: 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    flex-wrap: wrap;
}

.search-field {
    width: min(560px, 100%);
    min-width: 260px;
    display: flex;
    align-items: center;
    gap: 8px;
    border-radius: 10px;
    border: 1px solid rgba(23, 48, 79, 0.14);
    background: #f8fbff;
    padding: 0 10px;
}

.search-field i {
    color: rgba(23, 48, 79, 0.65);
    font-size: 19px;
}

.search-field input {
    border: none;
    outline: none;
    width: 100%;
    height: 40px;
    color: #17304f;
    background: transparent;
}

.kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 12px;
}

.kpi-card {
    position: relative;
    overflow: hidden;
    border-radius: 14px;
    border: 1px solid rgba(23, 48, 79, 0.14);
    background:
        radial-gradient(circle at 100% 0, rgba(244, 183, 64, 0.2), transparent 45%),
        linear-gradient(145deg, #ffffff 0%, #f4f9ff 100%);
    box-shadow: 0 10px 20px rgba(23, 48, 79, 0.08);
    padding: 12px;
}

.kpi-card::before {
    content: '';
    position: absolute;
    inset: 0 auto 0 0;
    width: 4px;
    border-radius: 14px 0 0 14px;
}

.kpi-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: rgba(23, 48, 79, 0.78);
    font-size: 0.78rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 8px;
}

.kpi-head i {
    width: 30px;
    height: 30px;
    border-radius: 9px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    color: rgba(23, 48, 79, 0.86);
    background: rgba(23, 48, 79, 0.12);
}

.kpi-value {
    display: block;
    color: #17304f;
    font-size: clamp(1.25rem, 2vw, 1.62rem);
    line-height: 1.1;
}

.kpi-note {
    display: block;
    margin-top: 6px;
    color: rgba(23, 48, 79, 0.62);
    font-size: 0.75rem;
    font-weight: 600;
}

.kpi-card--total::before {
    background: linear-gradient(180deg, #2d6a9f 0%, #17304f 100%);
}

.kpi-card--branches::before {
    background: linear-gradient(180deg, #0ea6a6 0%, #0c7676 100%);
}

.kpi-card--printers::before {
    background: linear-gradient(180deg, #8b5cf6 0%, #6d28d9 100%);
}

.kpi-card--states::before {
    background: linear-gradient(180deg, #f4b740 0%, #d99210 100%);
}

.cards-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
}

.caja-card {
    position: relative;
    overflow: hidden;
    border-radius: 20px;
    border: 1px solid rgba(23, 48, 79, 0.12);
    background:
        radial-gradient(circle at 100% 0, rgba(79, 140, 255, 0.14), transparent 44%),
        linear-gradient(140deg, #ffffff 0%, #f7fbff 100%);
    box-shadow: 0 14px 28px rgba(17, 36, 65, 0.1);
    padding: 14px;
    display: flex;
    flex-direction: column;
    gap: 11px;
    transition: transform 180ms ease, box-shadow 180ms ease;
}

.caja-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 18px 34px rgba(17, 36, 65, 0.16);
}

.caja-card::before {
    content: '';
    position: absolute;
    inset: 0 auto 0 0;
    width: 5px;
    background: linear-gradient(180deg, #4f8cff 0%, #2563eb 100%);
}

.card-head {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    align-items: flex-start;
}

.card-code {
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
    border: 1px solid rgba(23, 48, 79, 0.15);
    background: rgba(23, 48, 79, 0.05);
    color: rgba(23, 48, 79, 0.74);
    padding: 5px 10px;
    font-size: 0.72rem;
    font-weight: 800;
    letter-spacing: 0.03em;
}

.card-head h3 {
    margin: 6px 0 0;
    color: #17304f;
    font-size: 1.03rem;
}

.card-body {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 8px;
}

.cash-strip {
    border-radius: 12px;
    border: 1px solid rgba(23, 48, 79, 0.1);
    background: rgba(23, 48, 79, 0.04);
    padding: 9px 10px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.cash-dot {
    width: 8px;
    height: 8px;
    border-radius: 999px;
    background: #16a34a;
}

.cash-strip strong {
    color: #17304f;
    font-size: 0.84rem;
}

.cash-strip small {
    margin-left: auto;
    color: rgba(23, 48, 79, 0.68);
    font-weight: 700;
    font-size: 0.74rem;
}

.stat-chip {
    border-radius: 12px;
    border: 1px solid rgba(23, 48, 79, 0.1);
    background: #ffffff;
    padding: 9px 10px;
    display: flex;
    gap: 8px;
}

.stat-chip i {
    margin-top: 2px;
    color: rgba(23, 48, 79, 0.58);
}

.stat-chip span {
    display: block;
    color: rgba(23, 48, 79, 0.62);
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    font-weight: 800;
}

.stat-chip strong {
    display: block;
    margin-top: 2px;
    color: #17304f;
    font-size: 0.82rem;
}

.stat-chip--wide {
    grid-column: 1 / -1;
}

.card-actions {
    margin-top: auto;
    display: flex;
    gap: 8px;
    padding-top: 4px;
}

.empty-card {
    grid-column: 1 / -1;
    border-radius: 20px;
    border: 1px dashed rgba(23, 48, 79, 0.2);
    background: rgba(255, 255, 255, 0.85);
    padding: 28px 16px;
    text-align: center;
    color: rgba(23, 48, 79, 0.74);
}

.empty-card i {
    font-size: 2.2rem;
    color: rgba(23, 48, 79, 0.44);
}

.empty-card h4 {
    margin: 10px 0 6px;
    color: #17304f;
}

.empty-card p {
    margin: 0;
}

.skeleton-card {
    pointer-events: none;
}

.skeleton-line {
    height: 12px;
    border-radius: 999px;
    background: linear-gradient(90deg, rgba(23, 48, 79, 0.08), rgba(23, 48, 79, 0.16), rgba(23, 48, 79, 0.08));
    background-size: 260% 100%;
    animation: shimmer 1.5s ease-in-out infinite;
}

.skeleton-line--lg {
    width: 70%;
    height: 15px;
}

.skeleton-line--sm {
    width: 45%;
}

@keyframes shimmer {
    0% {
        background-position: 100% 0;
    }

    100% {
        background-position: -100% 0;
    }
}

.status-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 84px;
    padding: 7px 11px;
    border-radius: 999px;
    font-size: 0.78rem;
    font-weight: 800;
}

.status-ok {
    background: rgba(109, 211, 160, 0.18);
    color: #186843;
}

.status-off {
    background: rgba(255, 123, 123, 0.16);
    color: #9b2f2f;
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
    max-width: 620px;
}

.dialog-card-body {
    padding: 20px 24px;
}

.dialog-grid {
    display: grid;
    gap: 14px;
}

.dialog-grid--cols-2 {
    grid-template-columns: repeat(6, minmax(0, 1fr));
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
.field select,
.field textarea {
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
    min-height: 88px;
}

.field input:focus,
.field select:focus,
.field textarea:focus {
    border-color: rgba(23, 48, 79, 0.4);
    box-shadow: 0 0 0 2px rgba(23, 48, 79, 0.08);
}

.field-full {
    grid-column: span 6;
}

.field-half {
    grid-column: span 3;
}

.dialog-actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    padding: 14px 24px 20px;
}

.flash.error {
    border-radius: 12px;
    padding: 10px 12px;
    font-size: 0.88rem;
    font-weight: 700;
    margin-bottom: 14px;
    background: rgba(255, 123, 123, 0.16);
    color: #9b2f2f;
    border: 0;
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
    justify-content: center;
    gap: 8px;
    cursor: pointer;
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

.action-button {
    border: 0;
    border-radius: 999px;
    width: 40px;
    height: 40px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    position: relative;
    cursor: pointer;
}

.action-button i {
    font-size: 18px;
}

.action-edit {
    background: linear-gradient(135deg, #f4b740 0%, #ffd277 45%, #d99210 100%);
    color: #0b1530;
}

.action-view {
    background: linear-gradient(135deg, #4f8cff 0%, #2563eb 100%);
    color: #ffffff;
}

.action-enable {
    background: #16a34a;
    color: #ffffff;
}

.action-disable {
    background: #dc2626;
    color: #ffffff;
}

.dialog-avatar-alert {
    background: linear-gradient(135deg, #f97316 0%, #fb7185 45%, #ef4444 100%);
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

.detail-dialog-card {
    border-radius: 30px;
}

.detail-header {
    padding-right: 26px;
}

.detail-header-copy {
    flex: 1;
}

.detail-subtitle {
    margin: 8px 0 0;
    color: rgba(23, 48, 79, 0.66);
    font-size: 0.9rem;
}

.detail-body {
    display: grid;
    gap: 16px;
}

.detail-kpis {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 10px;
}

.detail-kpi {
    border-radius: 14px;
    border: 1px solid rgba(23, 48, 79, 0.12);
    background: linear-gradient(145deg, #ffffff 0%, #f7fbff 100%);
    padding: 12px;
}

.detail-kpi span {
    display: block;
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: rgba(23, 48, 79, 0.62);
    font-weight: 800;
}

.detail-kpi strong {
    display: block;
    margin-top: 8px;
    color: #17304f;
    font-size: 1.05rem;
}

.detail-kpi small {
    display: block;
    margin-top: 4px;
    color: rgba(23, 48, 79, 0.6);
}

.detail-state-card {
    border-radius: 14px;
    padding: 12px;
    border: 1px solid transparent;
}

.detail-state-card span {
    display: block;
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    font-weight: 800;
    opacity: 0.86;
}

.detail-state-card strong {
    display: block;
    margin-top: 8px;
    font-size: 1.05rem;
}

.detail-state-card small {
    display: block;
    margin-top: 4px;
    opacity: 0.78;
}

.detail-state-card--active {
    background: linear-gradient(145deg, rgba(22, 163, 74, 0.2) 0%, rgba(34, 197, 94, 0.16) 100%);
    border-color: rgba(22, 163, 74, 0.3);
    color: #166534;
}

.detail-state-card--inactive {
    background: linear-gradient(145deg, rgba(220, 38, 38, 0.2) 0%, rgba(248, 113, 113, 0.16) 100%);
    border-color: rgba(220, 38, 38, 0.3);
    color: #991b1b;
}

.detail-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
}

.detail-panel {
    border-radius: 14px;
    border: 1px solid rgba(23, 48, 79, 0.1);
    background: #ffffff;
    padding: 12px;
}

.detail-panel--wide {
    grid-column: span 2;
}

.detail-panel h4 {
    margin: 0 0 10px;
    color: #17304f;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.06em;
}

.detail-panel ul {
    list-style: none;
    padding: 0;
    margin: 0;
    display: grid;
    gap: 8px;
}

.detail-panel li {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    border-bottom: 1px dashed rgba(23, 48, 79, 0.12);
    padding-bottom: 6px;
}

.detail-panel li:last-child {
    border-bottom: 0;
    padding-bottom: 0;
}

.detail-panel li span {
    color: rgba(23, 48, 79, 0.66);
    font-size: 0.78rem;
}

.detail-panel li strong {
    color: #17304f;
    font-size: 0.82rem;
    text-align: right;
}

@media (max-width: 1120px) {
    .kpi-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .cards-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .detail-kpis {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .detail-grid {
        grid-template-columns: 1fr;
    }

    .detail-panel--wide {
        grid-column: span 1;
    }
}

@media (max-width: 760px) {
    .cards-grid,
    .kpi-grid {
        grid-template-columns: 1fr;
    }

    .toolbar-card {
        flex-direction: column;
        align-items: stretch;
    }

    .search-field {
        width: 100%;
        min-width: 100%;
    }

    .dialog-grid--cols-2 {
        grid-template-columns: 1fr;
    }

    .card-body {
        grid-template-columns: 1fr;
    }

    .field-full,
    .field-half {
        grid-column: span 1;
    }

    .dialog-title {
        font-size: 1.22rem;
    }

    .dialog-actions {
        flex-direction: column-reverse;
    }

    .dialog-actions .secondary-button,
    .dialog-actions .submit-button,
    .toolbar-card .submit-button {
        width: 100%;
        justify-content: center;
    }

    .detail-kpis {
        grid-template-columns: 1fr;
    }
}
</style>

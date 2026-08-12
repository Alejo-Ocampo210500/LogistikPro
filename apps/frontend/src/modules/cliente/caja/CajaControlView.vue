<template>
    <section class="cajas-shell">
        <article class="hero-card">
            <div>
                <span class="hero-kicker">Caja</span>
                <h2>Control de Caja</h2>
                <p>
                    Administra aperturas, cierres y anulaciones en un solo módulo con trazabilidad completa
                    y hora oficial de Colombia.
                </p>
            </div>
        </article>

        <article class="toolbar-card">
            <label class="select-field" for="estado-control-caja">
                <span>Estado</span>
                <select id="estado-control-caja" v-model="filters.estado">
                    <option value="todos">Todos</option>
                    <option value="abierta">Abiertas</option>
                    <option value="cerrada">Cerradas</option>
                    <option value="anulada">Anuladas</option>
                </select>
            </label>

            <label class="select-field" for="sucursal-control-caja">
                <span>Sucursal</span>
                <select id="sucursal-control-caja" v-model="filters.sucursal">
                    <option value="todas">Todas</option>
                    <option v-for="sucursal in sucursalesFiltro" :key="sucursal" :value="sucursal">
                        {{ sucursal }}
                    </option>
                </select>
            </label>

            <label class="select-field" for="fecha-desde-control-caja">
                <span>Desde</span>
                <input id="fecha-desde-control-caja" v-model="filters.desde" type="date" />
            </label>

            <label class="select-field" for="fecha-hasta-control-caja">
                <span>Hasta</span>
                <input id="fecha-hasta-control-caja" v-model="filters.hasta" type="date" />
            </label>

            <button type="button" class="secondary-button" :disabled="loading || isSubmitting" @click="recargarControles">
                <i class="mdi mdi-refresh"></i>
                <span>Actualizar</span>
            </button>

            <button type="button" class="danger-button" :disabled="loading || isSubmitting" @click="limpiarFiltros">
                <i class="mdi mdi-filter-remove-outline"></i>
                <span>Limpiar</span>
            </button>

            <button
                type="button"
                class="submit-button"
                :disabled="loading || isSubmitting || !puedeAbrirNuevaCaja"
                @click="abrirDialogoApertura"
            >
                <i class="mdi mdi-lock-open-variant-outline"></i>
                <span>Abrir caja</span>
            </button>
        </article>

        <section class="kpi-grid" aria-label="Metricas de control de caja">
            <article class="kpi-card kpi-card--total">
                <div class="kpi-head">
                    <span>Abiertas</span>
                    <i class="mdi mdi-lock-open-check-outline"></i>
                </div>
                <strong class="kpi-value">{{ cajasAbiertas.length }}</strong>
                <small class="kpi-note">Cajas operando en turno</small>
            </article>

            <article class="kpi-card kpi-card--branches">
                <div class="kpi-head">
                    <span>Cerradas</span>
                    <i class="mdi mdi-lock-check-outline"></i>
                </div>
                <strong class="kpi-value">{{ cajasCerradas.length }}</strong>
                <small class="kpi-note">Turnos finalizados</small>
            </article>

            <article class="kpi-card kpi-card--printers">
                <div class="kpi-head">
                    <span>Anuladas</span>
                    <i class="mdi mdi-close-octagon-outline"></i>
                </div>
                <strong class="kpi-value">{{ cajasAnuladas.length }}</strong>
                <small class="kpi-note">Con observación registrada</small>
            </article>

            <article class="kpi-card kpi-card--states">
                <div class="kpi-head">
                    <span>Perfil</span>
                    <i class="mdi mdi-account-badge-outline"></i>
                </div>
                <strong class="kpi-value kpi-value--profile">{{ isAdministrador ? 'Administrador' : 'Operador' }}</strong>
                <small class="kpi-note">{{ isAdministrador ? 'Puede abrir varias cajas' : 'Solo una caja abierta a la vez' }}</small>
            </article>
        </section>

        <section class="lanes-grid" aria-label="Vista dividida control caja">
            <article class="lane-card">
                <header class="lane-head">
                    <span class="lane-kicker">Operación Activa</span>
                    <h3>Cajas Abiertas</h3>
                    <small>{{ cajasAbiertasFiltradas.length }} registros</small>
                </header>

                <section v-if="showListSkeleton" class="cards-grid cards-grid--loading" aria-label="Cargando abiertas">
                    <article v-for="n in 3" :key="`open-skeleton-${n}`" class="caja-card skeleton-card">
                        <div class="skeleton-line skeleton-line--lg"></div>
                        <div class="skeleton-line"></div>
                        <div class="skeleton-line"></div>
                        <div class="skeleton-line skeleton-line--sm"></div>
                    </article>
                </section>

                <section v-else class="cards-grid" aria-label="Cajas abiertas">
                    <article v-for="item in cajasAbiertasFiltradas" :key="item.id" class="caja-card">
                        <header class="card-head">
                            <div>
                                <small class="card-code">{{ item.caja_codigo || 'SIN-CODIGO' }}</small>
                                <h3>{{ item.caja_nombre || 'Caja sin nombre' }}</h3>
                            </div>
                            <span class="status-pill status-ok">Abierta</span>
                        </header>

                        <div class="cash-strip">
                            <span class="cash-dot"></span>
                            <strong>{{ item.sucursal_nombre || 'Sucursal sin asignar' }}</strong>
                            <small>{{ fullUserName(item.usuario_apertura) || 'Sin usuario' }}</small>
                        </div>

                        <div class="card-body">
                            <article class="stat-chip">
                                <i class="mdi mdi-cash"></i>
                                <div>
                                    <span>Monto apertura</span>
                                    <strong>{{ formatearMoneda(item.monto_apertura) }}</strong>
                                </div>
                            </article>

                            <article class="stat-chip">
                                <i class="mdi mdi-clock-outline"></i>
                                <div>
                                    <span>Hora Bogotá</span>
                                    <strong>{{ formatearFecha(item.fecha_apertura || item.created_at) }}</strong>
                                </div>
                            </article>

                            <article class="stat-chip stat-chip--wide">
                                <i class="mdi mdi-text-box-outline"></i>
                                <div>
                                    <span>Observación apertura</span>
                                    <strong>{{ item.observaciones_apertura || 'Sin observación' }}</strong>
                                </div>
                            </article>
                        </div>

                        <footer class="card-actions">
                            <button type="button" class="action-button action-view" :disabled="isSubmitting" @click="abrirDialogoDetalle(item)">
                                <i class="mdi mdi-eye-outline"></i>
                                <span class="button-tooltip">Ver detalle</span>
                            </button>

                            <button type="button" class="action-button action-enable" :disabled="isSubmitting" @click="abrirDialogoCierre(item)">
                                <i class="mdi mdi-lock-check-outline"></i>
                                <span class="button-tooltip">Cerrar caja</span>
                            </button>

                            <button type="button" class="action-button action-disable" :disabled="isSubmitting" @click="abrirDialogoAnulacion(item)">
                                <i class="mdi mdi-close-circle-outline"></i>
                                <span class="button-tooltip">Anular caja</span>
                            </button>
                        </footer>
                    </article>

                    <article v-if="!cajasAbiertasFiltradas.length" class="empty-card">
                        <i class="mdi mdi-lock-open-variant-outline"></i>
                        <h4>No hay cajas abiertas</h4>
                        <p>No hay resultados para los filtros actuales.</p>
                    </article>
                </section>
            </article>

            <article class="lane-card">
                <header class="lane-head">
                    <span class="lane-kicker">Historial Operativo</span>
                    <h3>Cajas Cerradas y Anuladas</h3>
                    <small>{{ cajasCerradasYAnuladasFiltradas.length }} registros</small>
                </header>

                <section v-if="showListSkeleton" class="cards-grid cards-grid--loading" aria-label="Cargando cerradas y anuladas">
                    <article v-for="n in 3" :key="`closed-skeleton-${n}`" class="caja-card skeleton-card">
                        <div class="skeleton-line skeleton-line--lg"></div>
                        <div class="skeleton-line"></div>
                        <div class="skeleton-line"></div>
                        <div class="skeleton-line skeleton-line--sm"></div>
                    </article>
                </section>

                <section v-else class="cards-grid" aria-label="Cajas cerradas y anuladas">
                    <article v-for="item in cajasCerradasYAnuladasFiltradas" :key="item.id" class="caja-card">
                        <header class="card-head">
                            <div>
                                <small class="card-code">{{ item.caja_codigo || 'SIN-CODIGO' }}</small>
                                <h3>{{ item.caja_nombre || 'Caja sin nombre' }}</h3>
                            </div>
                            <span :class="['status-pill', item.estado_normalizado === 'anulada' ? 'status-off' : 'status-ok-soft']">
                                {{ item.estado_normalizado === 'anulada' ? 'Anulada' : 'Cerrada' }}
                            </span>
                        </header>

                        <div class="cash-strip">
                            <span class="cash-dot" :class="item.estado_normalizado === 'anulada' ? 'cash-dot--danger' : ''"></span>
                            <strong>{{ item.sucursal_nombre || 'Sucursal sin asignar' }}</strong>
                            <small>{{ fullUserName(item.usuario_cierre) || 'Sin usuario' }}</small>
                        </div>

                        <div class="card-body">
                            <article class="stat-chip">
                                <i class="mdi mdi-cash-check"></i>
                                <div>
                                    <span>Monto cierre</span>
                                    <strong>{{ formatearMoneda(item.monto_cierre) }}</strong>
                                </div>
                            </article>

                            <article class="stat-chip">
                                <i class="mdi mdi-swap-horizontal"></i>
                                <div>
                                    <span>Diferencia</span>
                                    <strong>{{ formatearMoneda(item.diferencia) }}</strong>
                                </div>
                            </article>

                            <article class="stat-chip stat-chip--wide">
                                <i class="mdi mdi-text-box-outline"></i>
                                <div>
                                    <span>Observación cierre</span>
                                    <strong>{{ item.observaciones_cierre || 'Sin observación' }}</strong>
                                </div>
                            </article>
                        </div>

                        <footer class="card-actions">
                            <button type="button" class="action-button action-view" :disabled="isSubmitting" @click="abrirDialogoDetalle(item)">
                                <i class="mdi mdi-eye-outline"></i>
                                <span class="button-tooltip">Ver detalle</span>
                            </button>
                        </footer>
                    </article>

                    <article v-if="!cajasCerradasYAnuladasFiltradas.length" class="empty-card">
                        <i class="mdi mdi-lock-check-outline"></i>
                        <h4>Sin cierres ni anulaciones</h4>
                        <p>No hay resultados para los filtros actuales.</p>
                    </article>
                </section>
            </article>
        </section>

        <v-dialog v-model="aperturaDialog" max-width="860px" persistent>
            <v-card class="dialog-card">
                <v-card-title class="dialog-card-title">
                    <v-avatar size="46" class="dialog-avatar">
                        <v-icon large>mdi-cash-register-plus</v-icon>
                    </v-avatar>
                    <div>
                        <span class="dialog-kicker">Control de caja</span>
                        <h3 class="dialog-title">Abrir caja</h3>
                        <p class="dialog-description">Configura la apertura del turno para iniciar operación.</p>
                    </div>
                </v-card-title>

                <v-divider />

                <v-card-text class="dialog-card-body">
                    <div v-if="validationMessage" class="flash error">
                        {{ validationMessage }}
                    </div>

                    <div class="dialog-grid dialog-grid--cols-2">
                        <label class="field field-full">
                            <span>Caja</span>
                            <select v-model.number="aperturaForm.caja_id">
                                <option :value="null">Selecciona una caja</option>
                                <option v-for="caja in cajasDisponiblesParaApertura" :key="caja.id" :value="caja.id">
                                    {{ caja.codigo || 'SIN-CODIGO' }} - {{ caja.nombre || 'Caja sin nombre' }}
                                </option>
                            </select>
                        </label>

                        <label class="field field-half">
                            <span>Monto apertura</span>
                            <input v-model.number="aperturaForm.monto_apertura" type="number" min="0" step="1" placeholder="Ej: 100000" />
                        </label>

                        <label class="field field-full">
                            <span>Observación de apertura</span>
                            <textarea v-model.trim="aperturaForm.observaciones_apertura" rows="3" placeholder="Detalle del inicio de turno"></textarea>
                        </label>
                    </div>
                </v-card-text>

                <v-divider />

                <v-card-actions class="dialog-actions">
                    <button type="button" class="secondary-button" :disabled="isSubmitting" @click="cerrarDialogos">Cancelar</button>
                    <button type="button" class="submit-button" :disabled="isSubmitting" @click="confirmarAperturaCaja">
                        <i v-if="submittingAction === 'abrir'" class="mdi mdi-loading mdi-spin"></i>
                        <span>{{ submittingAction === 'abrir' ? 'Abriendo...' : 'Abrir caja' }}</span>
                    </button>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <v-dialog v-model="cierreDialog" max-width="860px" persistent>
            <v-card class="dialog-card">
                <v-card-title class="dialog-card-title">
                    <v-avatar size="46" class="dialog-avatar">
                        <v-icon large>mdi-cash-register</v-icon>
                    </v-avatar>
                    <div>
                        <span class="dialog-kicker">Control de caja</span>
                        <h3 class="dialog-title">Cerrar caja</h3>
                        <p class="dialog-description">Confirma montos finales y cierre del turno.</p>
                    </div>
                </v-card-title>

                <v-divider />

                <v-card-text class="dialog-card-body">
                    <div v-if="validationMessage" class="flash error">
                        {{ validationMessage }}
                    </div>

                    <div class="dialog-grid dialog-grid--cols-2">
                        <label class="field field-half">
                            <span>Monto cierre</span>
                            <input v-model.number="cierreForm.monto_cierre" type="number" min="0" step="1" placeholder="Ej: 150000" />
                        </label>

                        <label class="field field-half">
                            <span>Efectivo sistema</span>
                            <input v-model.number="cierreForm.efectivo_sistema" type="number" min="0" step="1" placeholder="Ej: 148000" />
                        </label>

                        <label class="field field-half">
                            <span>Efectivo contado</span>
                            <input v-model.number="cierreForm.efectivo_contado" type="number" min="0" step="1" placeholder="Ej: 150000" />
                        </label>

                        <label class="field field-half">
                            <span>Diferencia calculada</span>
                            <input :value="formatearMoneda(diferenciaCierre)" type="text" disabled />
                        </label>

                        <label class="field field-full">
                            <span>Observación de cierre</span>
                            <textarea v-model.trim="cierreForm.observaciones_cierre" rows="3" placeholder="Detalle de cierre y novedades"></textarea>
                        </label>
                    </div>
                </v-card-text>

                <v-divider />

                <v-card-actions class="dialog-actions">
                    <button type="button" class="secondary-button" :disabled="isSubmitting" @click="cerrarDialogos">Cancelar</button>
                    <button type="button" class="submit-button" :disabled="isSubmitting" @click="confirmarCierreCaja">
                        <i v-if="submittingAction === 'cerrar'" class="mdi mdi-loading mdi-spin"></i>
                        <span>{{ submittingAction === 'cerrar' ? 'Cerrando...' : 'Cerrar caja' }}</span>
                    </button>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <v-dialog v-model="anulacionDialog" max-width="760px" persistent>
            <v-card class="dialog-card">
                <v-card-title class="dialog-card-title">
                    <v-avatar size="46" class="dialog-avatar dialog-avatar-alert">
                        <v-icon large>mdi-alert-circle-outline</v-icon>
                    </v-avatar>
                    <div>
                        <span class="dialog-kicker">Control de caja</span>
                        <h3 class="dialog-title">Anular caja</h3>
                        <p class="dialog-description">La observación de anulación es obligatoria.</p>
                    </div>
                </v-card-title>

                <v-divider />

                <v-card-text class="dialog-card-body">
                    <div v-if="validationMessage" class="flash error">
                        {{ validationMessage }}
                    </div>

                    <div class="dialog-grid dialog-grid--cols-2">
                        <label class="field field-full">
                            <span>Observación de anulación</span>
                            <textarea v-model.trim="anulacionForm.observacion" rows="4" placeholder="Describe motivo de anulación"></textarea>
                        </label>
                    </div>
                </v-card-text>

                <v-divider />

                <v-card-actions class="dialog-actions">
                    <button type="button" class="secondary-button" :disabled="isSubmitting" @click="cerrarDialogos">Cancelar</button>
                    <button type="button" class="submit-button" :disabled="isSubmitting" @click="confirmarAnulacionCaja">
                        <i v-if="submittingAction === 'anular'" class="mdi mdi-loading mdi-spin"></i>
                        <span>{{ submittingAction === 'anular' ? 'Anulando...' : 'Anular caja' }}</span>
                    </button>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <v-dialog v-model="detalleDialog" max-width="980px" persistent>
            <v-card class="dialog-card detail-dialog-card">
                <v-card-title class="dialog-card-title detail-header">
                    <v-avatar size="48" class="dialog-avatar detail-avatar">
                        <v-icon large>mdi-cash-register</v-icon>
                    </v-avatar>

                    <div class="detail-header-copy">
                        <span class="dialog-kicker">Control de caja</span>
                        <h3 class="dialog-title">{{ detalleItem ? (detalleItem.caja_nombre || 'Caja sin nombre') : 'Detalle de caja' }}</h3>
                        <p class="detail-subtitle">
                            Código {{ detalleItem ? (detalleItem.caja_codigo || 'N/A') : 'N/A' }} ·
                            Sucursal {{ detalleItem ? (detalleItem.sucursal_nombre || 'Sin asignar') : 'Sin asignar' }}
                        </p>
                    </div>

                    <span :class="['status-pill', detalleEstadoNormalizado === 'abierta' ? 'status-ok' : (detalleEstadoNormalizado === 'anulada' ? 'status-off' : 'status-ok-soft')]">
                        {{ detalleEstadoEtiqueta }}
                    </span>
                </v-card-title>

                <v-divider />

                <v-card-text class="dialog-card-body detail-body">
                    <section class="detail-kpis" aria-label="Resumen de control de caja">
                        <article class="detail-kpi detail-kpi-apertura">
                            <span>Monto apertura</span>
                            <strong>{{ detalleResumen.montoApertura }}</strong>
                            <small>Base inicial de turno</small>
                        </article>

                        <article class="detail-kpi detail-kpi-cierre">
                            <span>Monto cierre</span>
                            <strong>{{ detalleResumen.montoCierre }}</strong>
                            <small>Valor final reportado</small>
                        </article>

                        <article class="detail-kpi detail-kpi-diferencia">
                            <span>Diferencia</span>
                            <strong>{{ detalleResumen.diferencia }}</strong>
                            <small>Contado vs sistema</small>
                        </article>

                        <article class="detail-kpi detail-kpi-estado">
                            <span>Estado actual</span>
                            <strong>{{ detalleEstadoEtiqueta }}</strong>
                            <small>{{ detalleResumen.fechaEstado }}</small>
                        </article>
                    </section>

                    <section class="detail-grid" aria-label="Detalle completo de control de caja">
                        <article class="detail-panel detail-panel--id">
                            <h4><span class="detail-section-tag detail-section-tag--id">Identificación</span></h4>
                            <ul>
                                <li><span>Caja</span><strong>{{ detalleFicha.cajaNombre }}</strong></li>
                                <li><span>Código caja</span><strong>{{ detalleFicha.cajaCodigo }}</strong></li>
                                <li><span>Sucursal</span><strong>{{ detalleFicha.sucursalNombre }}</strong></li>
                                <li><span>Estado</span><strong>{{ detalleEstadoEtiqueta }}</strong></li>
                            </ul>
                        </article>

                        <article class="detail-panel detail-panel--inventory">
                            <h4><span class="detail-section-tag detail-section-tag--inventory">Operación</span></h4>
                            <ul>
                                <li><span>Usuario apertura</span><strong>{{ detalleFicha.usuarioApertura }}</strong></li>
                                <li><span>Fecha apertura</span><strong>{{ detalleFicha.fechaApertura }}</strong></li>
                                <li><span>Usuario cierre</span><strong>{{ detalleFicha.usuarioCierre }}</strong></li>
                                <li><span>Fecha cierre</span><strong>{{ detalleFicha.fechaCierre }}</strong></li>
                            </ul>
                        </article>

                        <article class="detail-panel detail-panel--commercial">
                            <h4><span class="detail-section-tag detail-section-tag--commercial">Cuadre</span></h4>
                            <ul>
                                <li><span>Efectivo sistema</span><strong>{{ detalleFicha.efectivoSistema }}</strong></li>
                                <li><span>Efectivo contado</span><strong>{{ detalleFicha.efectivoContado }}</strong></li>
                                <li><span>Observación apertura</span><strong>{{ detalleFicha.observacionApertura }}</strong></li>
                                <li><span>Observación cierre</span><strong>{{ detalleFicha.observacionCierre }}</strong></li>
                            </ul>
                        </article>
                    </section>
                </v-card-text>

                <v-divider />

                <v-card-actions class="dialog-actions">
                    <button type="button" class="secondary-button" @click="detalleDialog = false">Cerrar</button>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </section>
</template>

<script>
import api from '@/services/api';

const CONTROL_CAJA_API = {
    listar: '/control-cajas',
    abrir: '/control-cajas/abrir',
    cerrar: (controlCajaId) => `/control-cajas/${controlCajaId}/cerrar`,
    anular: (controlCajaId) => `/control-cajas/${controlCajaId}/anular`,
};

const BOGOTA_TZ = 'America/Bogota';

export default {
    name: 'CajaControlView',

    props: {
        session: {
            type: Object,
            default: null,
        },
    },

    data() {
        return {
            loading: false,
            reloadingList: false,
            submittingAction: '',
            validationMessage: '',
            cajas: [],
            controles: [],
            filters: {
                estado: 'todos',
                sucursal: 'todas',
                desde: '',
                hasta: '',
            },
            aperturaDialog: false,
            cierreDialog: false,
            anulacionDialog: false,
            detalleDialog: false,
            detalleItem: null,
            cajaSeleccionada: {},
            aperturaForm: this.getAperturaBase(),
            cierreForm: this.getCierreBase(),
            anulacionForm: this.getAnulacionBase(),
        };
    },

    computed: {
        roleName() {
            return String(this.session?.rol?.nombre || '').trim().toLowerCase();
        },

        userId() {
            return Number(this.session?.user?.id || 0);
        },

        isAdministrador() {
            return this.roleName.includes('admin') || this.roleName === 'superadmin' || this.roleName === 'superadministrador';
        },

        isSubmitting() {
            return this.submittingAction !== '';
        },

        showListSkeleton() {
            return this.loading || this.reloadingList;
        },

        controlesVisibles() {
            if (this.isAdministrador) {
                return this.controles;
            }

            return this.controles.filter((item) => Number(item.usuario_apertura_id || 0) === this.userId);
        },

        cajasAbiertas() {
            return this.controlesVisibles.filter((item) => item.estado_normalizado === 'abierta');
        },

        cajasCerradas() {
            return this.controlesVisibles.filter((item) => item.estado_normalizado === 'cerrada');
        },

        cajasAnuladas() {
            return this.controlesVisibles.filter((item) => item.estado_normalizado === 'anulada');
        },

        tieneCajaAbiertaUsuario() {
            return this.cajasAbiertas.some((item) => Number(item.usuario_apertura_id || 0) === this.userId);
        },

        puedeAbrirNuevaCaja() {
            if (this.isAdministrador) {
                return true;
            }

            return !this.tieneCajaAbiertaUsuario;
        },

        sucursalesFiltro() {
            const uniques = new Set(this.controlesVisibles.map((item) => item.sucursal_nombre || '').filter(Boolean));
            return Array.from(uniques).sort((a, b) => a.localeCompare(b));
        },

        controlesFiltrados() {
            const estado = this.filters.estado;
            const sucursal = this.filters.sucursal;
            const desde = this.filters.desde || '';
            const hasta = this.filters.hasta || '';

            return this.controlesVisibles.filter((item) => {
                const estadoOk = estado === 'todos' || item.estado_normalizado === estado;
                const sucursalOk = sucursal === 'todas' || (item.sucursal_nombre || '') === sucursal;

                const fechaControl = this.obtenerFechaReferenciaControl(item);
                const fechaDesdeOk = !desde || (fechaControl && fechaControl >= desde);
                const fechaHastaOk = !hasta || (fechaControl && fechaControl <= hasta);
                const fechaOk = fechaDesdeOk && fechaHastaOk;

                if (!estadoOk || !sucursalOk || !fechaOk) {
                    return false;
                }

                return true;
            });
        },

        cajasAbiertasFiltradas() {
            return this.controlesFiltrados.filter((item) => item.estado_normalizado === 'abierta');
        },

        cajasCerradasYAnuladasFiltradas() {
            return this.controlesFiltrados.filter((item) => ['cerrada', 'anulada'].includes(item.estado_normalizado));
        },

        cajasDisponiblesParaApertura() {
            const abiertasPorCajaId = new Set(this.cajasAbiertas.map((item) => Number(item.caja_id || 0)).filter(Boolean));
            return this.cajas.filter((item) => !abiertasPorCajaId.has(Number(item.id || 0)));
        },

        diferenciaCierre() {
            const sistema = Number(this.cierreForm.efectivo_sistema || 0);
            const contado = Number(this.cierreForm.efectivo_contado || 0);
            return contado - sistema;
        },

        detalleEstadoNormalizado() {
            return this.normalizarTexto(this.detalleItem?.estado || '');
        },

        detalleEstadoEtiqueta() {
            const estado = this.detalleEstadoNormalizado;

            if (estado === 'abierta') {
                return 'Abierta';
            }

            if (estado === 'cerrada') {
                return 'Cerrada';
            }

            if (estado === 'anulada') {
                return 'Anulada';
            }

            return this.fallback(this.detalleItem?.estado);
        },

        detalleResumen() {
            const item = this.detalleItem || {};
            const fechaEstado = this.obtenerFechaEstadoDetalle(item);

            return {
                montoApertura: this.formatearMoneda(item.monto_apertura),
                montoCierre: this.formatearMoneda(item.monto_cierre),
                diferencia: this.formatearMoneda(item.diferencia),
                fechaEstado,
            };
        },

        detalleFicha() {
            const item = this.detalleItem || {};
            const esAnulada = this.normalizarTexto(item?.estado || '') === 'anulada';

            return {
                cajaNombre: this.fallback(item.caja_nombre || item.caja?.nombre),
                cajaCodigo: this.fallback(item.caja_codigo || item.caja?.codigo),
                sucursalNombre: this.fallback(item.sucursal_nombre || item.sucursal?.nombre),
                usuarioApertura: this.fallback(this.fullUserName(item.usuario_apertura)),
                fechaApertura: this.formatearFechaConHora(item.fecha_apertura, item.hora_apertura),
                usuarioCierre: this.fallback(this.fullUserName(item.usuario_cierre)),
                fechaCierre: esAnulada
                    ? this.formatearFechaHoraUtcABogota(item.updated_at || item.fecha_cierre)
                    : this.formatearFechaConHora(item.fecha_cierre, item.hora_cierre),
                efectivoSistema: this.formatearMoneda(item.efectivo_sistema),
                efectivoContado: this.formatearMoneda(item.efectivo_contado),
                observacionApertura: this.fallback(item.observaciones_apertura),
                observacionCierre: this.fallback(item.observaciones_cierre),
            };
        },
    },

    mounted() {
        this.inicializarVista();
    },

    methods: {
        getAperturaBase() {
            return {
                caja_id: null,
                monto_apertura: null,
                observaciones_apertura: '',
            };
        },

        getCierreBase() {
            return {
                control_id: null,
                monto_cierre: null,
                efectivo_sistema: null,
                efectivo_contado: null,
                observaciones_cierre: '',
            };
        },

        getAnulacionBase() {
            return {
                control_id: null,
                observacion: '',
            };
        },

        fallback(value) {
            if (value === null || value === undefined || String(value).trim() === '') {
                return 'N/A';
            }

            return String(value);
        },

        fullUserName(user) {
            if (!user) {
                return '';
            }

            return [user.nombre, user.apellido].filter(Boolean).join(' ').trim();
        },

        normalizarTexto(value) {
            return String(value || '')
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .trim()
                .toLowerCase();
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

        formatearMoneda(value) {
            const amount = Number(value || 0);
            return new Intl.NumberFormat('es-CO', {
                style: 'currency',
                currency: 'COP',
                maximumFractionDigits: 0,
            }).format(amount);
        },

        formatearFecha(value) {
            if (!value) {
                return 'Sin fecha';
            }

            if (typeof value === 'string') {
                const match = value
                    .trim()
                    .match(/^(\d{4})-(\d{2})-(\d{2})(?:[ T](\d{2}):(\d{2})(?::(\d{2}))?)?$/);

                if (match) {
                    const [, year, month, day, hour = '00', minute = '00'] = match;
                    const hourNumber = Number(hour);
                    const displayHour = hourNumber % 12 || 12;
                    const suffix = hourNumber < 12 ? 'a. m.' : 'p. m.';

                    return `${day}/${month}/${year}, ${displayHour}:${minute} ${suffix}`;
                }
            }

            const fecha = new Date(value);

            if (Number.isNaN(fecha.getTime())) {
                return String(value);
            }

            return new Intl.DateTimeFormat('es-CO', {
                dateStyle: 'medium',
                timeStyle: 'short',
                timeZone: BOGOTA_TZ,
            }).format(fecha);
        },

        formatearHora12h(value) {
            if (!value) {
                return 'N/A';
            }

            const match = String(value).trim().match(/^(\d{1,2}):(\d{2})(?::\d{2})?$/);

            if (!match) {
                return this.fallback(value);
            }

            const hour24 = Number(match[1]);
            const minute = match[2];
            const hour12 = hour24 % 12 || 12;
            const suffix = hour24 < 12 ? 'a. m.' : 'p. m.';

            return `${hour12}:${minute} ${suffix}`;
        },

        formatearFechaSoloDisplay(value) {
            if (!value) {
                return 'Sin fecha';
            }

            const text = String(value).trim();
            const sqlDateMatch = text.match(/^(\d{4})-(\d{2})-(\d{2})/);

            if (sqlDateMatch) {
                const [, year, month, day] = sqlDateMatch;
                return `${day}/${month}/${year}`;
            }

            const fecha = new Date(value);

            if (Number.isNaN(fecha.getTime())) {
                return this.fallback(value);
            }

            return new Intl.DateTimeFormat('es-CO', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                timeZone: BOGOTA_TZ,
            }).format(fecha);
        },

        formatearFechaConHora(fecha, hora) {
            const fechaTexto = this.formatearFechaSoloDisplay(fecha);
            const horaTexto = hora ? this.formatearHora12h(hora) : '';

            if (!horaTexto || horaTexto === 'N/A') {
                return fechaTexto;
            }

            return `${fechaTexto} · ${horaTexto}`;
        },

        formatearFechaHoraUtcABogota(value) {
            if (!value) {
                return 'Sin fecha';
            }

            const text = String(value).trim();
            const sqlDateTime = text.match(/^(\d{4})-(\d{2})-(\d{2})[ T](\d{2}):(\d{2})(?::(\d{2}))?$/);

            let fecha;

            if (sqlDateTime) {
                const [, year, month, day, hour, minute, second = '00'] = sqlDateTime;
                fecha = new Date(`${year}-${month}-${day}T${hour}:${minute}:${second}Z`);
            } else {
                fecha = new Date(value);
            }

            if (Number.isNaN(fecha.getTime())) {
                return this.fallback(value);
            }

            return new Intl.DateTimeFormat('es-CO', {
                dateStyle: 'short',
                timeStyle: 'short',
                timeZone: BOGOTA_TZ,
            }).format(fecha);
        },

        obtenerFechaEstadoDetalle(item) {
            const estado = this.normalizarTexto(item?.estado || '');

            if (estado === 'abierta') {
                return this.formatearFechaConHora(item?.fecha_apertura || item?.created_at, item?.hora_apertura);
            }

            if (estado === 'cerrada') {
                return this.formatearFechaConHora(item?.fecha_cierre || item?.updated_at, item?.hora_cierre);
            }

            if (estado === 'anulada') {
                return this.formatearFechaHoraUtcABogota(item?.updated_at || item?.fecha_cierre);
            }

            return this.formatearFecha(item?.updated_at || item?.created_at);
        },

        extraerFechaSolo(value) {
            if (!value) {
                return '';
            }

            const text = String(value).trim();
            const sqlMatch = text.match(/^(\d{4}-\d{2}-\d{2})/);

            if (sqlMatch) {
                return sqlMatch[1];
            }

            const fecha = new Date(value);

            if (Number.isNaN(fecha.getTime())) {
                return '';
            }

            return new Intl.DateTimeFormat('en-CA', {
                timeZone: BOGOTA_TZ,
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
            }).format(fecha);
        },

        obtenerFechaReferenciaControl(item) {
            const estado = this.normalizarTexto(item?.estado || '');

            if (estado === 'abierta') {
                return this.extraerFechaSolo(item?.fecha_apertura || item?.created_at);
            }

            return this.extraerFechaSolo(item?.fecha_cierre || item?.updated_at || item?.fecha_apertura || item?.created_at);
        },

        obtenerMarcaTiempoBogota() {
            const parts = new Intl.DateTimeFormat('en-CA', {
                timeZone: BOGOTA_TZ,
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false,
            }).formatToParts(new Date());

            const map = Object.fromEntries(parts.map((part) => [part.type, part.value]));
            const fecha = `${map.year}-${map.month}-${map.day} ${map.hour}:${map.minute}:${map.second}`;
            const hora = `${map.hour}:${map.minute}:${map.second}`;

            return { fecha, hora };
        },

        normalizarControl(item) {
            const caja = this.cajas.find((it) => Number(it.id) === Number(item.caja_id));
            const estado = this.normalizarTexto(item.estado || 'abierta') || 'abierta';

            return {
                ...item,
                caja_nombre: item.caja?.nombre || item.caja_nombre || caja?.nombre || 'Caja sin nombre',
                caja_codigo: item.caja?.codigo || item.caja_codigo || caja?.codigo || '',
                sucursal_nombre: item.sucursal?.nombre || item.sucursal_nombre || caja?.sucursal_nombre || 'Sin asignar',
                estado_normalizado: estado,
            };
        },

        async inicializarVista() {
            this.loading = true;

            try {
                await Promise.all([this.listarCajas(), this.listarControlesCaja()]);
            } finally {
                this.loading = false;
            }
        },

        async recargarControles() {
            await this.listarControlesCaja(true);
        },

        limpiarFiltros() {
            this.filters.estado = 'todos';
            this.filters.sucursal = 'todas';
            this.filters.desde = '';
            this.filters.hasta = '';
        },

        async listarCajas() {
            try {
                const { data } = await api.get('/cajas');
                const listado = this.extraerLista(data, ['cajas']);
                this.cajas = listado.map((item) => ({
                    ...item,
                    sucursal_nombre: item.sucursal?.nombre || item.sucursal_nombre || '',
                }));
            } catch (error) {
                this.cajas = [];
            }
        },

        async apiListarControlCajas() {
            return api.get(CONTROL_CAJA_API.listar);
        },

        async apiAbrirCaja(payload) {
            return api.post(CONTROL_CAJA_API.abrir, payload);
        },

        async apiCerrarCaja(controlCajaId, payload) {
            return api.put(CONTROL_CAJA_API.cerrar(controlCajaId), payload);
        },

        async apiAnularCaja(controlCajaId, payload) {
            return api.post(CONTROL_CAJA_API.anular(controlCajaId), payload);
        },

        async listarControlesCaja(withPreload = false) {
            if (withPreload) {
                this.reloadingList = true;
            }

            try {
                const { data } = await this.apiListarControlCajas();
                const listado = this.extraerLista(data, ['controlCajas', 'controles']);
                this.controles = listado.map((item) => this.normalizarControl(item));
            } catch (error) {
                this.controles = [];
            } finally {
                if (withPreload) {
                    this.reloadingList = false;
                }
            }
        },

        abrirDialogoDetalle(item) {
            this.detalleItem = { ...item };
            this.detalleDialog = true;
        },

        abrirDialogoApertura() {
            this.validationMessage = '';

            if (!this.puedeAbrirNuevaCaja) {
                this.validationMessage = 'Solo puedes tener una caja abierta a la vez.';
                return;
            }

            this.aperturaForm = this.getAperturaBase();
            this.cajaSeleccionada = {};
            this.aperturaDialog = true;
        },

        abrirDialogoCierre(item) {
            this.validationMessage = '';
            this.cajaSeleccionada = { ...item };
            this.cierreForm = {
                ...this.getCierreBase(),
                control_id: Number(item.id),
                monto_cierre: Number(item.monto_apertura || 0),
                efectivo_sistema: Number(item.monto_apertura || 0),
            };
            this.cierreDialog = true;
        },

        abrirDialogoAnulacion(item) {
            this.validationMessage = '';
            this.cajaSeleccionada = { ...item };
            this.anulacionForm = {
                ...this.getAnulacionBase(),
                control_id: Number(item.id),
            };
            this.anulacionDialog = true;
        },

        cerrarDialogos() {
            this.aperturaDialog = false;
            this.cierreDialog = false;
            this.anulacionDialog = false;
            this.validationMessage = '';
            this.cajaSeleccionada = {};
            this.aperturaForm = this.getAperturaBase();
            this.cierreForm = this.getCierreBase();
            this.anulacionForm = this.getAnulacionBase();
        },

        validarApertura() {
            if (!this.aperturaForm.caja_id) {
                return 'Debes seleccionar una caja para abrir.';
            }

            if (Number(this.aperturaForm.monto_apertura || 0) < 0) {
                return 'El monto de apertura no puede ser negativo.';
            }

            if (!this.isAdministrador && !this.puedeAbrirNuevaCaja) {
                return 'Solo puedes tener una caja abierta a la vez.';
            }

            return '';
        },

        validarCierre() {
            if (!this.cierreForm.control_id) {
                return 'No se encontró la caja a cerrar.';
            }

            if (Number(this.cierreForm.monto_cierre || 0) < 0) {
                return 'El monto de cierre no puede ser negativo.';
            }

            return '';
        },

        validarAnulacion() {
            if (!this.anulacionForm.control_id) {
                return 'No se encontró la caja a anular.';
            }

            if (!this.anulacionForm.observacion) {
                return 'La observación de anulación es obligatoria.';
            }

            return '';
        },

        async confirmarAperturaCaja() {
            this.validationMessage = this.validarApertura();

            if (this.validationMessage) {
                return;
            }

            const caja = this.cajas.find((item) => Number(item.id) === Number(this.aperturaForm.caja_id));
            const time = this.obtenerMarcaTiempoBogota();
            const payload = {
                caja_id: Number(this.aperturaForm.caja_id),
                sucursal_id: Number(caja?.sucursal_id || 0),
                usuario_apertura_id: this.userId || null,
                monto_apertura: Number(this.aperturaForm.monto_apertura || 0),
                observaciones_apertura: this.aperturaForm.observaciones_apertura || null,
                fecha_apertura: time.fecha,
                hora_apertura: time.hora,
                estado: 'Abierta',
            };

            this.submittingAction = 'abrir';
            this.$emit('start-action', 'Abriendo caja...', null, null);

            try {
                await this.apiAbrirCaja(payload);
                await this.listarControlesCaja(true);
                this.cerrarDialogos();
            } catch (error) {
                this.validationMessage = this.resolverError(error);
            } finally {
                this.submittingAction = '';
                this.$emit('stop-action');
            }
        },

        async confirmarCierreCaja() {
            this.validationMessage = this.validarCierre();

            if (this.validationMessage) {
                return;
            }

            const time = this.obtenerMarcaTiempoBogota();
            const payload = {
                usuario_cierre_id: this.userId || null,
                monto_cierre: Number(this.cierreForm.monto_cierre || 0),
                efectivo_sistema: Number(this.cierreForm.efectivo_sistema || 0),
                efectivo_contado: Number(this.cierreForm.efectivo_contado || 0),
                diferencia: this.diferenciaCierre,
                observaciones_cierre: this.cierreForm.observaciones_cierre || null,
                fecha_cierre: time.fecha,
                hora_cierre: time.hora,
                estado: 'Cerrada',
            };

            this.submittingAction = 'cerrar';
            this.$emit('start-action', 'Cerrando caja...', null, null);

            try {
                await this.apiCerrarCaja(this.cierreForm.control_id, payload);
                await this.listarControlesCaja(true);
                this.cerrarDialogos();
            } catch (error) {
                this.validationMessage = this.resolverError(error);
            } finally {
                this.submittingAction = '';
                this.$emit('stop-action');
            }
        },

        async confirmarAnulacionCaja() {
            this.validationMessage = this.validarAnulacion();

            if (this.validationMessage) {
                return;
            }

            const payload = {
                estado: 'Anulada',
                observaciones_cierre: `ANULADA: ${this.anulacionForm.observacion}`,
            };

            this.submittingAction = 'anular';
            this.$emit('start-action', 'Anulando caja...', null, null);

            try {
                await this.apiAnularCaja(this.anulacionForm.control_id, payload);
                await this.listarControlesCaja(true);
                this.cerrarDialogos();
            } catch (error) {
                this.validationMessage = this.resolverError(error);
            } finally {
                this.submittingAction = '';
                this.$emit('stop-action');
            }
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
    padding: 14px;
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 14px;
    flex-wrap: wrap;
}

.select-field {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.select-field span {
    color: rgba(23, 48, 79, 0.72);
    font-size: 0.73rem;
    font-weight: 800;
    text-transform: uppercase;
}

.select-field select {
    border-radius: 10px;
    border: 1px solid rgba(23, 48, 79, 0.16);
    background: #ffffff;
    color: #17304f;
    min-height: 40px;
    min-width: 160px;
    padding: 0 10px;
}

.select-field input {
    border-radius: 10px;
    border: 1px solid rgba(23, 48, 79, 0.16);
    background: #ffffff;
    color: #17304f;
    min-height: 40px;
    min-width: 160px;
    padding: 0 10px;
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

.kpi-value--profile {
    font-size: 1.06rem;
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
    background: linear-gradient(180deg, #cf222e 0%, #9e1620 100%);
}

.kpi-card--states::before {
    background: linear-gradient(180deg, #f4b740 0%, #d99210 100%);
}

.lanes-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 14px;
}

.lane-card {
    border-radius: 20px;
    border: 1px solid rgba(23, 48, 79, 0.12);
    background: #ffffff;
    box-shadow: 0 12px 26px rgba(17, 36, 65, 0.09);
    padding: 12px;
}

.lane-head {
    display: flex;
    flex-direction: column;
    gap: 3px;
    margin-bottom: 12px;
}

.lane-kicker {
    text-transform: uppercase;
    letter-spacing: 0.11em;
    font-size: 0.68rem;
    color: rgba(23, 48, 79, 0.58);
}

.lane-head h3 {
    margin: 0;
    color: #17304f;
    font-size: 1.06rem;
}

.lane-head small {
    color: rgba(23, 48, 79, 0.64);
    font-weight: 700;
}

.cards-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 12px;
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

.cash-dot--danger {
    background: #dc2626;
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

.status-ok-soft {
    background: rgba(79, 140, 255, 0.16);
    color: #1f4bb5;
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
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    cursor: pointer;
}

.danger-button {
    border: 0;
    border-radius: 14px;
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: #ffffff;
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
    align-items: flex-start;
}

.detail-avatar {
    background: linear-gradient(135deg, #4f8cff 0%, #2563eb 100%);
    color: #ffffff;
}

.detail-header-copy {
    flex: 1;
}

.detail-subtitle {
    margin: 8px 0 0;
    color: rgba(23, 48, 79, 0.68);
}

.detail-body {
    padding-top: 18px;
}

.detail-kpis {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 10px;
    margin-bottom: 12px;
}

.detail-kpi {
    border-radius: 12px;
    border: 1px solid rgba(23, 48, 79, 0.12);
    background: #ffffff;
    padding: 10px;
}

.detail-kpi span {
    display: block;
    color: rgba(23, 48, 79, 0.62);
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    font-weight: 800;
}

.detail-kpi strong {
    display: block;
    margin-top: 6px;
    color: #17304f;
    font-size: 1.06rem;
    line-height: 1.2;
}

.detail-kpi small {
    display: block;
    margin-top: 4px;
    color: rgba(23, 48, 79, 0.66);
    font-size: 0.75rem;
    font-weight: 700;
}

.detail-kpi-apertura {
    border-color: rgba(45, 106, 159, 0.24);
    background: linear-gradient(145deg, rgba(79, 140, 255, 0.08), #ffffff 72%);
}

.detail-kpi-cierre {
    border-color: rgba(14, 166, 166, 0.24);
    background: linear-gradient(145deg, rgba(14, 166, 166, 0.1), #ffffff 72%);
}

.detail-kpi-diferencia {
    border-color: rgba(244, 183, 64, 0.28);
    background: linear-gradient(145deg, rgba(244, 183, 64, 0.15), #ffffff 72%);
}

.detail-kpi-estado {
    border-color: rgba(23, 48, 79, 0.2);
    background: linear-gradient(145deg, rgba(23, 48, 79, 0.08), #ffffff 72%);
}

.detail-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 10px;
}

.detail-panel {
    border-radius: 12px;
    border: 1px solid rgba(23, 48, 79, 0.12);
    background: #ffffff;
    padding: 10px;
}

.detail-panel--id {
    border-color: rgba(79, 140, 255, 0.24);
}

.detail-panel--inventory {
    border-color: rgba(14, 166, 166, 0.22);
}

.detail-panel--commercial {
    border-color: rgba(244, 183, 64, 0.28);
}

.detail-panel h4 {
    margin: 0;
    color: rgba(23, 48, 79, 0.62);
    text-transform: uppercase;
    letter-spacing: 0.06em;
    font-size: 0.73rem;
}

.detail-section-tag {
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
    padding: 4px 9px;
    font-size: 0.68rem;
    font-weight: 800;
    letter-spacing: 0.05em;
    text-transform: uppercase;
}

.detail-section-tag--id {
    background: rgba(79, 140, 255, 0.14);
    color: #1f4bb5;
}

.detail-section-tag--inventory {
    background: rgba(14, 166, 166, 0.14);
    color: #0c7676;
}

.detail-section-tag--commercial {
    background: rgba(244, 183, 64, 0.2);
    color: #8a5a00;
}

.detail-panel ul {
    margin: 10px 0 0;
    padding: 0;
    list-style: none;
}

.detail-panel li {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 8px;
    padding: 8px 0;
    border-bottom: 1px dashed rgba(23, 48, 79, 0.12);
}

.detail-panel li:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.detail-panel li span {
    color: rgba(23, 48, 79, 0.66);
    font-size: 0.78rem;
}

.detail-panel li strong {
    color: #17304f;
    font-size: 0.84rem;
    text-align: right;
    line-height: 1.35;
}

@media (max-width: 1240px) {
    .kpi-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .lanes-grid {
        grid-template-columns: 1fr;
    }

    .detail-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .detail-kpis {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 900px) {
    .dialog-grid--cols-2 {
        grid-template-columns: 1fr;
    }

    .field-full,
    .field-half {
        grid-column: span 1;
    }
}

@media (max-width: 760px) {
    .toolbar-card {
        align-items: stretch;
    }

    .kpi-grid,
    .detail-grid,
    .card-body,
    .detail-kpis {
        grid-template-columns: 1fr;
    }

    .stat-chip--wide {
        grid-column: auto;
    }
}
</style>

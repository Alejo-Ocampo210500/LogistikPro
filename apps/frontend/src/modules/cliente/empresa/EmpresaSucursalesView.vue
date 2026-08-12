<template>
    <section class="sucursales-shell">
        <article class="hero-card">
            <div>
                <span class="hero-kicker">Empresa</span>
                <h2>Modulo de sucursales</h2>
                <p>
                    Hola, {{ companyName }}. Administra sedes, contacto y estado operativo en tarjetas.
                </p>
            </div>
        </article>

        <article class="toolbar-card">
            <label class="search-field" for="search-sucursales">
                <i class="mdi mdi-magnify"></i>
                <input
                    id="search-sucursales"
                    v-model.trim="search"
                    type="text"
                    placeholder="Buscar por nombre, codigo, NIT, ciudad o responsable"
                />
            </label>

            <button type="button" class="submit-button" @click="abrirModalCrear">
                <i class="mdi mdi-plus"></i>
                <span>Nueva sucursal</span>
            </button>
        </article>

        <section class="kpi-grid" aria-label="Metricas de sucursales">
            <article class="kpi-card kpi-card--total">
                <div class="kpi-head">
                    <span>Total sucursales</span>
                    <i class="mdi mdi-office-building-marker-outline"></i>
                </div>
                <strong class="kpi-value">{{ totalSucursales }}</strong>
                <small class="kpi-note">Sedes registradas</small>
            </article>

            <article class="kpi-card kpi-card--active">
                <div class="kpi-head">
                    <span>Activas</span>
                    <i class="mdi mdi-check-decagram-outline"></i>
                </div>
                <strong class="kpi-value">{{ totalActivas }}</strong>
                <small class="kpi-note">Operando actualmente</small>
            </article>

            <article class="kpi-card kpi-card--inactive">
                <div class="kpi-head">
                    <span>Inactivas</span>
                    <i class="mdi mdi-store-off-outline"></i>
                </div>
                <strong class="kpi-value">{{ totalInactivas }}</strong>
                <small class="kpi-note">Fuera de operacion</small>
            </article>

            <article class="kpi-card kpi-card--cities">
                <div class="kpi-head">
                    <span>Ciudades cubiertas</span>
                    <i class="mdi mdi-map-marker-multiple-outline"></i>
                </div>
                <strong class="kpi-value">{{ totalCiudades }}</strong>
                <small class="kpi-note">Cobertura geografia</small>
            </article>
        </section>

        <section v-if="loading" class="cards-grid cards-grid--loading" aria-label="Cargando sucursales">
            <article v-for="n in 6" :key="`skeleton-${n}`" class="sucursal-card skeleton-card">
                <div class="skeleton-line skeleton-line--lg"></div>
                <div class="skeleton-line"></div>
                <div class="skeleton-line"></div>
                <div class="skeleton-line skeleton-line--sm"></div>
            </article>
        </section>

        <section v-else class="cards-grid" aria-label="Tarjetas de sucursales">
            <article
                v-for="item in filteredSucursales"
                :key="item.id"
                :class="['sucursal-card', esSucursalActiva(item) ? 'sucursal-card--active' : 'sucursal-card--inactive']"
            >
                <header class="card-head">
                    <div>
                        <small class="card-code">Cod. {{ item.codigo || 'N/A' }}</small>
                        <h3>{{ item.nombre || 'Sucursal sin nombre' }}</h3>
                    </div>
                    <span :class="['status-pill', esSucursalActiva(item) ? 'status-ok' : 'status-off']">
                        {{ esSucursalActiva(item) ? 'Activa' : 'Inactiva' }}
                    </span>
                </header>

                <div class="card-body">
                    <p><i class="mdi mdi-card-account-details-outline"></i> NIT: {{ item.nit || 'No registrado' }}</p>
                    <p><i class="mdi mdi-account-tie-outline"></i> Responsable: {{ item.responsable || 'No asignado' }}</p>
                    <p><i class="mdi mdi-map-marker-outline"></i> {{ item.ciudad_nombre || 'Sin ciudad' }}, {{ item.departamento_nombre || 'Sin departamento' }}</p>
                    <p><i class="mdi mdi-map"></i> {{ item.pais_nombre || 'Sin pais' }}</p>
                    <p><i class="mdi mdi-map-marker-radius-outline"></i> {{ item.direccion || 'Sin direccion' }}</p>
                    <p><i class="mdi mdi-phone-outline"></i> {{ item.telefono || 'Sin telefono' }}</p>
                    <p><i class="mdi mdi-email-outline"></i> {{ item.email || 'Sin correo' }}</p>
                </div>

                <footer class="card-actions">
                    <button type="button" class="action-button" @click="abrirModalEditar(item)" aria-label="Editar sucursal">
                        <i class="mdi mdi-pencil"></i>
                        <span class="button-tooltip">Editar</span>
                    </button>

                    <button type="button" class="action-button action-view" @click="abrirModalDetalle(item)" aria-label="Ver detalle sucursal">
                        <i class="mdi mdi-eye-outline"></i>
                        <span class="button-tooltip">Ver detalle</span>
                    </button>

                    <button
                        type="button"
                        :class="['action-button', esSucursalActiva(item) ? 'action-disable' : 'action-enable']"
                        @click="solicitarCambioEstado(item)"
                        :aria-label="esSucursalActiva(item) ? 'Inactivar sucursal' : 'Activar sucursal'"
                    >
                        <i :class="esSucursalActiva(item) ? 'mdi mdi-close-circle-outline' : 'mdi mdi-check-circle-outline'"></i>
                        <span class="button-tooltip">{{ esSucursalActiva(item) ? 'Inactivar' : 'Activar' }}</span>
                    </button>
                </footer>
            </article>

            <article v-if="!filteredSucursales.length" class="empty-card">
                <i class="mdi mdi-store-search-outline"></i>
                <h4>No encontramos sucursales</h4>
                <p>Ajusta el filtro o registra una nueva sucursal.</p>
            </article>
        </section>

        <v-dialog v-model="sucursalDialog" max-width="860px" persistent>
            <v-card class="dialog-card">
                <v-card-title class="dialog-card-title">
                    <v-avatar size="46" class="dialog-avatar">
                        <v-icon large>{{ editMode ? 'mdi-store-edit-outline' : 'mdi-store-plus-outline' }}</v-icon>
                    </v-avatar>
                    <div>
                        <span class="dialog-kicker">{{ editMode ? 'Editar sucursal' : 'Nueva sucursal' }}</span>
                        <h3 class="dialog-title">{{ editMode ? 'Actualizar sucursal' : 'Crear sucursal' }}</h3>
                        <p class="dialog-description">
                            Configura identificacion, contacto y ubicacion de la sede.
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
                            <span>Codigo</span>
                            <input v-model.trim="form.codigo" type="text" placeholder="Ej: BOG-001" />
                        </label>

                        <label class="field field-half">
                            <span>Nombre</span>
                            <input v-model.trim="form.nombre" type="text" placeholder="Ej: Sucursal Norte" />
                        </label>

                        <label class="field field-half">
                            <span>NIT</span>
                            <input v-model.trim="form.nit" type="text" placeholder="Ej: 900123456-7" />
                        </label>

                        <label class="field field-half">
                            <span>Responsable</span>
                            <input v-model.trim="form.responsable" type="text" placeholder="Ej: Laura Gomez" />
                        </label>

                        <label class="field field-full">
                            <span>Direccion</span>
                            <input v-model.trim="form.direccion" type="text" placeholder="Ej: Cra 15 # 100-20" />
                        </label>

                        <label class="field field-half">
                            <span>Telefono</span>
                            <input v-model.trim="form.telefono" type="tel" placeholder="Ej: 6017000000" />
                        </label>

                        <label class="field field-half">
                            <span>Email</span>
                            <input v-model.trim="form.email" type="email" placeholder="Ej: sede@empresa.com" />
                        </label>

                        <label class="field field-third">
                            <span>Pais</span>
                            <select v-model.number="form.pais_id">
                                <option :value="null">Selecciona pais</option>
                                <option v-for="pais in paises" :key="pais.id" :value="pais.id">{{ pais.nombre }}</option>
                            </select>
                        </label>

                        <label class="field field-third">
                            <span>Departamento</span>
                            <select v-model.number="form.departamento_id">
                                <option :value="null">Selecciona departamento</option>
                                <option v-for="departamento in departamentosDisponibles" :key="departamento.id" :value="departamento.id">
                                    {{ departamento.nombre }}
                                </option>
                            </select>
                        </label>

                        <label class="field field-third">
                            <span>Ciudad</span>
                            <select v-model.number="form.ciudad_id">
                                <option :value="null">Selecciona ciudad</option>
                                <option v-for="ciudad in ciudadesDisponibles" :key="ciudad.id" :value="ciudad.id">
                                    {{ ciudad.nombre }}
                                </option>
                            </select>
                        </label>

                        <label class="field field-half">
                            <span>Estado</span>
                            <select v-model="form.estado">
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </label>
                    </div>
                </v-card-text>

                <v-divider />

                <v-card-actions class="dialog-actions">
                    <button type="button" class="secondary-button" @click="cerrarModal">Cancelar</button>
                    <button type="button" class="submit-button" data-action-loader="true" @click="guardarSucursal">
                        {{ editMode ? 'Guardar cambios' : 'Crear sucursal' }}
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
                        <h3 class="dialog-title">{{ pendingAction === 'activar' ? 'Activar sucursal' : 'Inactivar sucursal' }}</h3>
                    </div>
                </v-card-title>

                <v-divider />

                <v-card-text class="dialog-card-body">
                    <p>
                        {{ pendingAction === 'activar' ? 'Deseas activar la sucursal' : 'Deseas inactivar la sucursal' }}
                        <strong>"{{ pendingSucursal ? pendingSucursal.nombre : '' }}"</strong>?
                    </p>
                </v-card-text>

                <v-divider />

                <v-card-actions class="dialog-actions">
                    <button type="button" class="secondary-button" @click="cerrarDialogoConfirmacion">Cancelar</button>
                    <button type="button" class="submit-button" data-action-loader="true" @click="confirmarCambioEstado">Aceptar</button>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <v-dialog v-model="detalleDialog" max-width="920px" persistent>
            <v-card class="dialog-card detail-dialog-card">
                <v-card-title class="dialog-card-title detail-header">
                    <v-avatar size="48" class="dialog-avatar detail-avatar">
                        <v-icon large>mdi-office-building-marker-outline</v-icon>
                    </v-avatar>

                    <div class="detail-header-copy">
                        <span class="dialog-kicker">Ficha de sucursal</span>
                        <h3 class="dialog-title">{{ detalleSucursal.nombre || 'Sucursal sin nombre' }}</h3>
                        <p class="detail-subtitle">Codigo {{ detalleSucursal.codigo || 'N/A' }} · NIT {{ detalleSucursal.nit || 'Sin registro' }}</p>
                    </div>

                    <span :class="['status-pill', esSucursalActiva(detalleSucursal) ? 'status-ok' : 'status-off']">
                        {{ esSucursalActiva(detalleSucursal) ? 'Activa' : 'Inactiva' }}
                    </span>
                </v-card-title>

                <v-divider />

                <v-card-text class="dialog-card-body detail-body">
                    <section class="detail-kpis" aria-label="Resumen sucursal">
                        <article class="detail-kpi detail-kpi--identity">
                            <span>ID Sucursal</span>
                            <strong>{{ detalleSucursal.id || 'N/A' }}</strong>
                            <small>Registro interno</small>
                        </article>

                        <article class="detail-kpi detail-kpi--contact">
                            <span>Responsable</span>
                            <strong>{{ detalleSucursal.responsable || 'Sin responsable' }}</strong>
                            <small>Encargado principal</small>
                        </article>

                        <article class="detail-kpi detail-kpi--location">
                            <span>Ubicacion</span>
                            <strong>{{ detalleSucursal.ciudad_nombre || 'Sin ciudad' }}</strong>
                            <small>{{ detalleSucursal.departamento_nombre || 'Sin departamento' }}</small>
                        </article>

                        <article class="detail-kpi detail-kpi--status">
                            <span>Estado actual</span>
                            <strong>{{ esSucursalActiva(detalleSucursal) ? 'Activa' : 'Inactiva' }}</strong>
                            <small>{{ detalleSucursal.estado || 'No definido' }}</small>
                        </article>
                    </section>

                    <section class="detail-grid" aria-label="Detalle completo sucursal">
                        <article class="detail-panel detail-panel--id">
                            <h4>Identificacion</h4>
                            <ul>
                                <li><span>Codigo</span><strong>{{ detalleSucursal.codigo || 'N/A' }}</strong></li>
                                <li><span>NIT</span><strong>{{ detalleSucursal.nit || 'N/A' }}</strong></li>
                                <li><span>Empresa ID</span><strong>{{ detalleSucursal.empresa_id || 'N/A' }}</strong></li>
                                <li><span>Estado</span><strong>{{ detalleSucursal.estado || 'N/A' }}</strong></li>
                            </ul>
                        </article>

                        <article class="detail-panel detail-panel--contact">
                            <h4>Contacto</h4>
                            <ul>
                                <li><span>Responsable</span><strong>{{ detalleSucursal.responsable || 'Sin dato' }}</strong></li>
                                <li><span>Telefono</span><strong>{{ detalleSucursal.telefono || 'Sin dato' }}</strong></li>
                                <li><span>Email</span><strong>{{ detalleSucursal.email || 'Sin dato' }}</strong></li>
                                <li><span>Direccion</span><strong>{{ detalleSucursal.direccion || 'Sin dato' }}</strong></li>
                            </ul>
                        </article>

                        <article class="detail-panel detail-panel--location">
                            <h4>Ubicacion</h4>
                            <ul>
                                <li><span>Pais</span><strong>{{ detalleSucursal.pais_nombre || detalleSucursal.pais_id || 'Sin dato' }}</strong></li>
                                <li><span>Departamento</span><strong>{{ detalleSucursal.departamento_nombre || detalleSucursal.departamento_id || 'Sin dato' }}</strong></li>
                                <li><span>Ciudad</span><strong>{{ detalleSucursal.ciudad_nombre || detalleSucursal.ciudad_id || 'Sin dato' }}</strong></li>
                                <li><span>ID Registro</span><strong>{{ detalleSucursal.id || 'Sin dato' }}</strong></li>
                            </ul>
                        </article>

                        <article class="detail-panel detail-panel--audit">
                            <h4>Trazabilidad</h4>
                            <ul>
                                <li><span>Creado por</span><strong>{{ detalleSucursal.created_by || 'Sin dato' }}</strong></li>
                                <li><span>Actualizado por</span><strong>{{ detalleSucursal.updated_by || 'Sin dato' }}</strong></li>
                                <li><span>Creado</span><strong>{{ formatearFecha(detalleSucursal.created_at) }}</strong></li>
                                <li><span>Actualizado</span><strong>{{ formatearFecha(detalleSucursal.updated_at) }}</strong></li>
                            </ul>
                        </article>
                    </section>
                </v-card-text>

                <v-divider />

                <v-card-actions class="dialog-actions">
                    <button type="button" class="secondary-button" @click="cerrarModalDetalle">
                        Cerrar
                    </button>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </section>
</template>

<script>
import api from '@/services/api';

export default {
    name: 'EmpresaSucursalesView',

    props: {
        session: {
            type: Object,
            default: null,
        },
    },

    data() {
        return {
            loading: false,
            sucursalDialog: false,
            confirmDialog: false,
            detalleDialog: false,
            editMode: false,
            editingId: null,
            pendingSucursal: null,
            detalleSucursal: {},
            pendingAction: '',
            validationMessage: '',
            isHydratingEditForm: false,
            search: '',
            sucursales: [],
            paises: [],
            departamentos: [],
            ciudades: [],
            form: this.formularioBase(),
        };
    },

    computed: {
        companyName() {
            return this.session?.empresa?.nombre_comercial || this.session?.empresa?.razon_social || 'equipo';
        },

        filteredSucursales() {
            const term = this.search.toLowerCase();

            if (!term) {
                return this.sucursales;
            }

            return this.sucursales.filter((item) =>
                [
                    item.codigo,
                    item.nombre,
                    item.nit,
                    item.responsable,
                    item.email,
                    item.telefono,
                    item.ciudad_nombre,
                    item.departamento_nombre,
                    item.pais_nombre,
                ]
                    .join(' ')
                    .toLowerCase()
                    .includes(term)
            );
        },

        totalSucursales() {
            return this.sucursales.length;
        },

        totalActivas() {
            return this.sucursales.filter((item) => this.esSucursalActiva(item)).length;
        },

        totalInactivas() {
            return this.sucursales.filter((item) => !this.esSucursalActiva(item)).length;
        },

        totalCiudades() {
            const uniques = new Set(
                this.sucursales
                    .map((item) => String(item.ciudad_nombre || item.ciudad_id || '').trim())
                    .filter(Boolean)
            );

            return uniques.size;
        },

        departamentosDisponibles() {
            const paisId = Number(this.form.pais_id || 0);

            if (!paisId) {
                return this.departamentos;
            }

            return this.departamentos.filter((item) => Number(item.pais_id) === paisId);
        },

        ciudadesDisponibles() {
            const departamentoId = Number(this.form.departamento_id || 0);

            if (!departamentoId) {
                return this.ciudades;
            }

            return this.ciudades.filter((item) => Number(item.departamento_id) === departamentoId);
        },
    },

    watch: {
        'form.pais_id'() {
            if (this.isHydratingEditForm) {
                return;
            }

            this.form.departamento_id = null;
            this.form.ciudad_id = null;
        },

        'form.departamento_id'() {
            if (this.isHydratingEditForm) {
                return;
            }

            this.form.ciudad_id = null;
        },
    },

    mounted() {
        this.inicializarVista();
    },

    methods: {
        formularioBase() {
            return {
                codigo: '',
                nombre: '',
                nit: '',
                direccion: '',
                telefono: '',
                email: '',
                ciudad_id: null,
                departamento_id: null,
                pais_id: null,
                responsable: '',
                estado: 'activo',
            };
        },

        async inicializarVista() {
            this.loading = true;

            try {
                await this.cargarCatalogos();
                await this.listarSucursales();
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
                return errores[0] || 'No se pudo completar la operacion.';
            }

            if (error?.response?.data?.mensaje) {
                return error.response.data.mensaje;
            }

            return 'No se pudo completar la operacion.';
        },

        extraerListaCatalogo(payload, posiblesLlaves = []) {
            if (Array.isArray(payload)) {
                if (payload.length === 1 && Array.isArray(payload[0])) {
                    return payload[0];
                }

                return payload;
            }

            if (payload && typeof payload === 'object') {
                for (const llave of posiblesLlaves) {
                    if (Array.isArray(payload[llave])) {
                        return payload[llave];
                    }
                }

                const primeraLista = Object.values(payload).find((value) => Array.isArray(value));
                return Array.isArray(primeraLista) ? primeraLista : [];
            }

            return [];
        },

        async cargarCatalogos() {
            const requests = await Promise.allSettled([
                api.get('/paises'),
                api.get('/departamentos'),
                api.get('/ciudades'),
            ]);

            const [paisesRes, departamentosRes, ciudadesRes] = requests;

            this.paises = paisesRes.status === 'fulfilled'
                ? this.extraerListaCatalogo(paisesRes.value?.data, ['paises'])
                : [];

            this.departamentos = departamentosRes.status === 'fulfilled'
                ? this.extraerListaCatalogo(departamentosRes.value?.data, ['departamentos'])
                : [];

            this.ciudades = ciudadesRes.status === 'fulfilled'
                ? this.extraerListaCatalogo(ciudadesRes.value?.data, ['ciudades'])
                : [];
        },

        normalizarSucursal(item) {
            const pais = this.paises.find((country) => Number(country.id) === Number(item.pais_id));
            const departamento = this.departamentos.find((dep) => Number(dep.id) === Number(item.departamento_id));
            const ciudad = this.ciudades.find((ciu) => Number(ciu.id) === Number(item.ciudad_id));

            return {
                ...item,
                pais_nombre: item.pais?.nombre || item.pais_nombre || pais?.nombre || '',
                departamento_nombre: item.departamento?.nombre || item.departamento_nombre || departamento?.nombre || '',
                ciudad_nombre: item.ciudad?.nombre || item.ciudad_nombre || ciudad?.nombre || '',
            };
        },

        async listarSucursales() {
            try {
                const { data } = await api.get('/sucursales');
                const listado = this.extraerListaCatalogo(data, ['sucursales']);
                this.sucursales = listado.map((item) => this.normalizarSucursal(item));
            } catch (error) {
                this.sucursales = [];
            }
        },

        normalizarTextoEstado(value) {
            return String(value || '')
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .trim()
                .toLowerCase();
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

        esSucursalActiva(item) {
            const estadoTexto = this.normalizarTextoEstado(item?.estado || item?.estado_nombre || item?.estado?.nombre);

            if (estadoTexto) {
                return estadoTexto === 'activo';
            }

            const estadoId = Number(item?.estado_id || 0);
            return estadoId === 1;
        },

        abrirModalCrear() {
            this.validationMessage = '';
            this.editMode = false;
            this.editingId = null;
            this.form = this.formularioBase();
            this.sucursalDialog = true;
        },

        abrirModalEditar(item) {
            this.validationMessage = '';
            this.editMode = true;
            this.editingId = Number(item.id);
            this.isHydratingEditForm = true;

            this.form = {
                codigo: item.codigo || '',
                nombre: item.nombre || '',
                nit: item.nit || '',
                direccion: item.direccion || '',
                telefono: item.telefono || '',
                email: item.email || '',
                ciudad_id: Number(item.ciudad_id) || null,
                departamento_id: Number(item.departamento_id) || null,
                pais_id: Number(item.pais_id) || null,
                responsable: item.responsable || '',
                estado: this.esSucursalActiva(item) ? 'activo' : 'inactivo',
            };

            this.sucursalDialog = true;

            this.$nextTick(() => {
                this.isHydratingEditForm = false;
            });
        },

        abrirModalDetalle(item) {
            this.detalleSucursal = { ...item };
            this.detalleDialog = true;
        },

        cerrarModalDetalle() {
            this.detalleDialog = false;
            this.detalleSucursal = {};
        },

        cerrarModal() {
            this.sucursalDialog = false;
            this.editMode = false;
            this.editingId = null;
            this.validationMessage = '';
            this.form = this.formularioBase();
        },

        validarFormulario() {
            if (!this.form.codigo) {
                return 'El codigo es obligatorio.';
            }

            if (!this.form.nombre) {
                return 'El nombre es obligatorio.';
            }

            if (!this.form.nit) {
                return 'El NIT es obligatorio.';
            }

            if (!this.form.responsable) {
                return 'El responsable es obligatorio.';
            }

            if (!this.form.direccion) {
                return 'La direccion es obligatoria.';
            }

            if (!this.form.email) {
                return 'El email es obligatorio.';
            }

            if (!this.form.pais_id || !this.form.departamento_id || !this.form.ciudad_id) {
                return 'Debes seleccionar pais, departamento y ciudad.';
            }

            return '';
        },

        async guardarSucursal() {
            this.validationMessage = this.validarFormulario();

            if (this.validationMessage) {
                return;
            }

            const payload = {
                codigo: this.form.codigo,
                nombre: this.form.nombre,
                nit: this.form.nit,
                direccion: this.form.direccion,
                telefono: this.form.telefono,
                email: this.form.email,
                ciudad_id: this.form.ciudad_id,
                departamento_id: this.form.departamento_id,
                pais_id: this.form.pais_id,
                responsable: this.form.responsable,
                estado: this.form.estado,
            };

            const actionLabel = this.editMode ? 'Actualizando sucursal...' : 'Creando sucursal...';
            this.$emit('start-action', actionLabel, null, null);

            try {
                await this.esperarTresSegundos();

                if (this.editMode && this.editingId) {
                    await api.put(`/sucursales/${this.editingId}/actualizar`, payload);
                } else {
                    await api.post('/sucursales/crear', payload);
                }

                this.cerrarModal();
                await this.listarSucursales();
            } catch (error) {
                this.validationMessage = this.resolverError(error);
            } finally {
                this.$emit('stop-action');
            }
        },

        solicitarCambioEstado(item) {
            this.pendingSucursal = item;
            this.pendingAction = this.esSucursalActiva(item) ? 'inactivar' : 'activar';
            this.confirmDialog = true;
        },

        cerrarDialogoConfirmacion() {
            this.confirmDialog = false;
            this.pendingSucursal = null;
            this.pendingAction = '';
        },

        async confirmarCambioEstado() {
            if (!this.pendingSucursal?.id) {
                this.cerrarDialogoConfirmacion();
                return;
            }

            const activar = this.pendingAction === 'activar';
            const estadoTexto = activar ? 'activo' : 'inactivo';
            const estadoId = activar ? 1 : 2;

            this.$emit('start-action', activar ? 'Activando sucursal...' : 'Inactivando sucursal...', null, null);

            try {
                await this.esperarTresSegundos();

                await api.post(`/sucursales/${this.pendingSucursal.id}/cambiarEstado`, {
                    estado: estadoTexto,
                    estado_id: estadoId,
                });

                await this.listarSucursales();
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
.sucursales-shell {
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
    max-width: 760px;
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

.kpi-card--active::before {
    background: linear-gradient(180deg, #0ea6a6 0%, #0c7676 100%);
}

.kpi-card--inactive::before {
    background: linear-gradient(180deg, #d66161 0%, #9f2f2f 100%);
}

.kpi-card--cities::before {
    background: linear-gradient(180deg, #f4b740 0%, #d99210 100%);
}

.cards-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
}

.sucursal-card {
    position: relative;
    overflow: hidden;
    border-radius: 20px;
    border: 1px solid rgba(23, 48, 79, 0.12);
    background:
        radial-gradient(circle at 100% 0, rgba(244, 183, 64, 0.16), transparent 42%),
        linear-gradient(140deg, #ffffff 0%, #f7fbff 100%);
    box-shadow: 0 14px 28px rgba(17, 36, 65, 0.1);
    padding: 14px;
    display: flex;
    flex-direction: column;
    gap: 11px;
    transition: transform 180ms ease, box-shadow 180ms ease;
}

.sucursal-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 18px 34px rgba(17, 36, 65, 0.16);
}

.sucursal-card::before {
    content: '';
    position: absolute;
    inset: 0 auto 0 0;
    width: 5px;
}

.sucursal-card--active::before {
    background: linear-gradient(180deg, #0ea6a6 0%, #0c7676 100%);
}

.sucursal-card--inactive::before {
    background: linear-gradient(180deg, #d66161 0%, #9f2f2f 100%);
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
    gap: 6px;
}

.card-body p {
    margin: 0;
    color: rgba(23, 48, 79, 0.82);
    font-size: 0.86rem;
    display: flex;
    align-items: flex-start;
    gap: 7px;
}

.card-body i {
    color: rgba(23, 48, 79, 0.58);
    margin-top: 2px;
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
.field select {
    width: 100%;
    border-radius: 8px;
    border: 1px solid rgba(23, 48, 79, 0.22);
    background: #ffffff;
    color: #17304f;
    padding: 11px 12px;
    outline: none;
}

.field input:focus,
.field select:focus {
    border-color: rgba(23, 48, 79, 0.4);
    box-shadow: 0 0 0 2px rgba(23, 48, 79, 0.08);
}

.field-full {
    grid-column: span 6;
}

.field-half {
    grid-column: span 3;
}

.field-third {
    grid-column: span 2;
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
    background: linear-gradient(135deg, #f4b740 0%, #ffd277 45%, #d99210 100%);
    color: #0b1530;
    cursor: pointer;
}

.action-button i {
    font-size: 18px;
}

.action-enable {
    background: #16a34a;
    color: #ffffff;
}

.action-disable {
    background: #dc2626;
    color: #ffffff;
}

.action-view {
    background: linear-gradient(135deg, #4f8cff 0%, #2563eb 100%);
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

    .dialog-grid--cols-2 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .detail-kpis {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .detail-grid {
        grid-template-columns: 1fr;
    }

    .field-full,
    .field-half,
    .field-third {
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

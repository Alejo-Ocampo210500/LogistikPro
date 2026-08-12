<template>
    <section class="clients-shell">
        <article class="hero-card">
            <div>
                <span class="hero-kicker">Clientes</span>
                <h2>Listado de clientes</h2>
                <p>
                    Consulta, crea y administra la base de clientes de tu empresa. Activa o inactiva perfiles,
                    actualiza datos comerciales y controla limites de credito desde un solo lugar.
                </p>
            </div>
        </article>

        <article class="toolbar-card">
            <label class="search-field" for="search-clientes">
                <i class="mdi mdi-magnify"></i>
                <input
                    id="search-clientes"
                    v-model.trim="search"
                    type="text"
                    placeholder="Buscar por nombre, documento, telefono o ciudad"
                />
            </label>

            <button type="button" class="submit-button" @click="abrirModalCrear">
                <i class="mdi mdi-plus"></i>
                <span>Crear cliente</span>
            </button>
        </article>

        <section class="kpi-grid" aria-label="Metricas de clientes">
            <article class="kpi-card kpi-card--total">
                <div class="kpi-head">
                    <span>Total clientes</span>
                    <i class="mdi mdi-account-group-outline"></i>
                </div>
                <strong class="kpi-value">{{ totalClientes }}</strong>
                <small class="kpi-note">Base total registrada</small>
            </article>

            <article class="kpi-card kpi-card--active">
                <div class="kpi-head">
                    <span>Activos</span>
                    <i class="mdi mdi-check-decagram-outline"></i>
                </div>
                <strong class="kpi-value">{{ totalActivos }}</strong>
                <small class="kpi-note">Con estado operativo</small>
            </article>

            <article class="kpi-card kpi-card--inactive">
                <div class="kpi-head">
                    <span>Inactivos</span>
                    <i class="mdi mdi-account-off-outline"></i>
                </div>
                <strong class="kpi-value">{{ totalInactivos }}</strong>
                <small class="kpi-note">Registros suspendidos</small>
            </article>

            <article class="kpi-card kpi-card--credit">
                <div class="kpi-head">
                    <span>Cupo total</span>
                    <i class="mdi mdi-cash-multiple"></i>
                </div>
                <strong class="kpi-value">{{ money(cupoTotal) }}</strong>
                <small class="kpi-note">Limite de credito consolidado</small>
            </article>
        </section>

        <article class="table-card" aria-label="Listado de clientes en tabla">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Tipo</th>
                            <th>Tipo documento</th>
                            <th>Numero documento</th>
                            <th>Celular</th>
                            <th>Limite credito</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="cliente in filteredClientes" :key="cliente.id">
                            <td>
                                {{ resolverNombrePrincipal(cliente) }}
                            </td>
                            <td>
                                <span :class="['person-type-pill', cliente.tipo_persona === 'juridica' ? 'person-type-pill--company' : 'person-type-pill--person']">
                                    {{ cliente.tipo_persona === 'juridica' ? 'Juridica' : 'Natural' }}
                                </span>
                            </td>
                            <td>
                                {{ cliente.tipo_documento_nombre || 'Documento' }}
                            </td>
                            <td>
                                {{ cliente.numero_documento || 'Sin numero' }}
                            </td>
                            <td>
                                {{ cliente.celular || 'Sin celular' }}
                            </td>
                            <td>
                                {{ money(cliente.limite_credito) }}
                            </td>
                            <td>
                                <span :class="['status-pill', esClienteActivo(cliente) ? 'status-ok' : 'status-off']">
                                    {{ esClienteActivo(cliente) ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td>
                                <div class="row-actions">
                                    <button
                                        type="button"
                                        class="action-button action-view"
                                        @click="verDetalle(cliente)"
                                        aria-label="Ver detalle cliente"
                                    >
                                        <i class="mdi mdi-eye-outline"></i>
                                        <span class="button-tooltip">Ver detalle</span>
                                    </button>

                                    <button
                                        type="button"
                                        class="action-button"
                                        @click="abrirModalEditar(cliente)"
                                        aria-label="Editar cliente"
                                    >
                                        <i class="mdi mdi-pencil"></i>
                                        <span class="button-tooltip">Editar</span>
                                    </button>

                                    <button
                                        type="button"
                                        :class="['action-button', esClienteActivo(cliente) ? 'action-disable' : 'action-enable']"
                                        @click="solicitarCambioEstado(cliente)"
                                        :aria-label="esClienteActivo(cliente) ? 'Inactivar cliente' : 'Activar cliente'"
                                    >
                                        <i :class="esClienteActivo(cliente) ? 'mdi mdi-close-circle-outline' : 'mdi mdi-check-circle-outline'"></i>
                                        <span class="button-tooltip">{{ esClienteActivo(cliente) ? 'Inactivar' : 'Activar' }}</span>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="!filteredClientes.length">
                            <td colspan="8" class="empty-row">No hay clientes registrados para este criterio.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </article>

        <v-dialog v-model="clientDialog" max-width="920px" persistent>
            <v-card class="dialog-card">
                <v-card-title class="dialog-card-title">
                    <v-avatar size="46" class="dialog-avatar">
                        <v-icon large>{{ editMode ? 'mdi-account-edit-outline' : 'mdi-account-plus-outline' }}</v-icon>
                    </v-avatar>
                    <div>
                        <span class="dialog-kicker">{{ editMode ? 'Editar cliente' : 'Nuevo cliente' }}</span>
                        <h3 class="dialog-title">{{ editMode ? 'Actualizar cliente' : 'Crear cliente' }}</h3>
                        <p class="dialog-description">
                            Configura la identificacion, contacto, ubicacion y condiciones de credito del cliente.
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
                            <span>Tipo de persona</span>
                            <select v-model="form.tipo_persona">
                                <option value="natural">Natural</option>
                                <option value="juridica">Juridica</option>
                            </select>
                        </label>

                        <label class="field field-half">
                            <span>Estado</span>
                            <select v-model.number="form.estado_id">
                                <option v-for="estado in estados" :key="estado.id" :value="estado.id">
                                    {{ estado.nombre }}
                                </option>
                            </select>
                        </label>

                        <label class="field field-half">
                            <span>Tipo documento</span>
                            <select v-model.number="form.tipo_documento_id">
                                <option :value="null">Selecciona un tipo de documento</option>
                                <option v-for="tipo in tiposDocumento" :key="tipo.id" :value="tipo.id">
                                    {{ tipo.nombre }}
                                </option>
                            </select>
                        </label>

                        <label class="field field-half">
                            <span>Numero documento</span>
                            <input v-model.trim="form.numero_documento" type="tel" inputmode="numeric" data-only-numeric="true" placeholder="Ej: 1023344556" />
                        </label>

                        <label v-if="form.tipo_persona === 'natural'" class="field field-half">
                            <span>Nombres</span>
                            <input v-model.trim="form.nombre" type="text" placeholder="Ej: Laura" />
                        </label>

                        <label v-if="form.tipo_persona === 'natural'" class="field field-half">
                            <span>Apellidos</span>
                            <input v-model.trim="form.apellido" type="text" placeholder="Ej: Gonzalez" />
                        </label>

                        <label v-if="form.tipo_persona === 'juridica'" class="field field-full">
                            <span>Razon social</span>
                            <input v-model.trim="form.razon_social" type="text" placeholder="Ej: Comercializadora Altamar S.A.S." />
                        </label>

                        <label v-if="form.tipo_persona === 'juridica'" class="field field-full">
                            <span>Nombre comercial</span>
                            <input v-model.trim="form.nombre_comercial" type="text" placeholder="Ej: Altamar" />
                        </label>

                        <label class="field field-half">
                            <span>Email</span>
                            <input v-model.trim="form.email" type="email" placeholder="Ej: cliente@correo.com" />
                        </label>

                        <label class="field field-half">
                            <span>Celular</span>
                            <input v-model.trim="form.celular" type="tel" inputmode="numeric" data-only-numeric="true" placeholder="Ej: 3001234567" />
                        </label>

                        <label class="field field-half">
                            <span>Telefono</span>
                            <input v-model.trim="form.telefono" type="tel" inputmode="numeric" data-only-numeric="true" placeholder="Ej: 6017000000" />
                        </label>

                        <label class="field field-half">
                            <span>Fecha de nacimiento</span>
                            <input v-model="form.fecha_nacimiento" type="date" />
                        </label>

                        <label class="field field-half">
                            <span>Genero</span>
                            <select v-model="form.genero">
                                <option :value="null">Selecciona</option>
                                <option value="masculino">Masculino</option>
                                <option value="femenino">Femenino</option>
                            </select>
                        </label>

                        <label class="field field-full">
                            <span>Direccion</span>
                            <input v-model.trim="form.direccion" type="text" placeholder="Ej: Calle 45 # 8 - 52" />
                        </label>

                        <label class="field field-third">
                            <span>Pais</span>
                            <select v-model.number="form.pais_id">
                                <option :value="null">Selecciona pais</option>
                                <option v-for="pais in paises" :key="pais.id" :value="pais.id">
                                    {{ pais.nombre }}
                                </option>
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

                        <label class="field field-third">
                            <span>Limite credito</span>
                            <input v-model.number="form.limite_credito" type="number" min="0" step="0.01" placeholder="Ej: 5000000.00" />
                        </label>

                        <label class="field field-third">
                            <span>Saldo credito</span>
                            <input v-model.number="form.saldo_credito" type="number" min="0" step="0.01" placeholder="Ej: 1250000.00" />
                        </label>

                        <label class="field field-third">
                            <span>Dias credito</span>
                            <input v-model.number="form.dias_credito" type="number" min="0" placeholder="Ej: 30" />
                        </label>
                    </div>
                </v-card-text>

                <v-divider />

                <v-card-actions class="dialog-actions">
                    <button type="button" class="secondary-button" @click="cerrarModal">
                        Cancelar
                    </button>
                    <button type="button" class="submit-button" data-action-loader="true" @click="guardarCliente">
                        {{ editMode ? 'Guardar cambios' : 'Crear cliente' }}
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
                        <h3 class="dialog-title">{{ pendingAction === 'activar' ? 'Activar cliente' : 'Inactivar cliente' }}</h3>
                    </div>
                </v-card-title>

                <v-divider />

                <v-card-text class="dialog-card-body">
                    <p>
                        {{ pendingAction === 'activar' ? 'Deseas activar el cliente' : 'Deseas inactivar el cliente' }}
                        <strong>"{{ pendingClient ? resolverNombrePrincipal(pendingClient) : '' }}"</strong>?
                    </p>
                </v-card-text>

                <v-divider />

                <v-card-actions class="dialog-actions">
                    <button type="button" class="secondary-button" @click="cerrarDialogoConfirmacion">Cancelar</button>
                    <button type="button" class="submit-button" data-action-loader="true" @click="confirmarCambioEstado">Aceptar</button>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <ClienteDetalleDialog
            v-model="detalleDialog"
            :cliente="detalleCliente"
            :estado-activo-id="obtenerEstadoActivoId()"
            @edit="editarDesdeDetalle"
        />
    </section>
</template>

<script>
import api from '@/services/api';
import ClienteDetalleDialog from '@/components/cliente/clientes/ClienteDetalleDialog.vue';

export default {
    name: 'ClientesCrearView',

    components: {
        ClienteDetalleDialog,
    },

    props: {
        session: {
            type: Object,
            default: null,
        },
    },

    data() {
        return {
            search: '',
            clientDialog: false,
            editMode: false,
            editingId: null,
            confirmDialog: false,
            pendingClient: null,
            pendingAction: '',
            detalleDialog: false,
            detalleCliente: null,
            validationMessage: '',
            isHydratingEditForm: false,
            clientes: [],
            tiposDocumento: [],
            paises: [],
            departamentos: [],
            ciudades: [],
            estados: [
                { id: 1, nombre: 'Activo' },
                { id: 2, nombre: 'Inactivo' },
            ],
            form: this.formularioBase(),
        };
    },

    computed: {
        filteredClientes() {
            if (!this.search) {
                return this.clientes;
            }

            const term = this.search.toLowerCase();
            return this.clientes.filter((item) =>
                [
                    this.resolverNombrePrincipal(item),
                    item.nombre_comercial,
                    item.numero_documento,
                    item.email,
                    item.celular,
                    item.telefono,
                    item.ciudad_nombre,
                    item.departamento_nombre,
                ]
                    .join(' ')
                    .toLowerCase()
                    .includes(term)
            );
        },

        totalClientes() {
            return this.clientes.length;
        },

        totalActivos() {
            return this.clientes.filter((item) => this.esClienteActivo(item)).length;
        },

        totalInactivos() {
            return this.clientes.filter((item) => !this.esClienteActivo(item)).length;
        },

        cupoTotal() {
            return this.clientes.reduce((acc, item) => acc + Number(item.limite_credito || 0), 0);
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

        detalleDialog(newValue) {
            if (!newValue) {
                this.detalleCliente = null;
            }
        },
    },

    mounted() {
        this.inicializarVista();
    },

    methods: {
        formularioBase() {
            return {
                empresa_id: this.session?.empresa_id || this.session?.empresa?.id || null,
                tipo_persona: 'natural',
                tipo_documento_id: null,
                numero_documento: '',
                nombre: '',
                apellido: '',
                razon_social: '',
                nombre_comercial: '',
                email: '',
                celular: '',
                telefono: '',
                direccion: '',
                pais_id: null,
                departamento_id: null,
                ciudad_id: null,
                fecha_nacimiento: '',
                genero: null,
                limite_credito: 0,
                saldo_credito: 0,
                dias_credito: 0,
                estado_id: 1,
                creado_por: this.session?.user?.id || null,
                actualizado_por: this.session?.user?.id || null,
            };
        },

        async inicializarVista() {
            await this.cargarCatalogos();
            await this.listarClientes();
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
                api.get('/tipo-documento'),
                api.get('/paises'),
                api.get('/departamentos'),
                api.get('/ciudades'),
                api.get('/estados'),
            ]);

            const [tipoDocumentoRes, paisesRes, departamentosRes, ciudadesRes, estadosRes] = requests;

            this.tiposDocumento = tipoDocumentoRes.status === 'fulfilled'
                ? this.extraerListaCatalogo(tipoDocumentoRes.value?.data, ['tipos_documento', 'tiposDocumento', 'tipo_documento'])
                : [];

            this.paises = paisesRes.status === 'fulfilled'
                ? this.extraerListaCatalogo(paisesRes.value?.data, ['paises'])
                : [];

            this.departamentos = departamentosRes.status === 'fulfilled'
                ? this.extraerListaCatalogo(departamentosRes.value?.data, ['departamentos'])
                : [];

            this.ciudades = ciudadesRes.status === 'fulfilled'
                ? this.extraerListaCatalogo(ciudadesRes.value?.data, ['ciudades'])
                : [];

            if (estadosRes.status === 'fulfilled') {
                const estadosRemotos = this.extraerListaCatalogo(estadosRes.value?.data, ['estados']);

                if (estadosRemotos.length) {
                    this.estados = estadosRemotos;
                }
            }
        },

        normalizarCliente(item) {
            const tipoDocumento = this.tiposDocumento.find((tipo) => Number(tipo.id) === Number(item.tipo_documento_id));
            const pais = this.paises.find((country) => Number(country.id) === Number(item.pais_id));
            const departamento = this.departamentos.find((dep) => Number(dep.id) === Number(item.departamento_id));
            const ciudad = this.ciudades.find((ciu) => Number(ciu.id) === Number(item.ciudad_id));

            return {
                ...item,
                tipo_documento_nombre: item.tipo_documento?.nombre || item.tipo_documento_nombre || tipoDocumento?.nombre || '',
                pais_nombre: item.pais?.nombre || item.pais_nombre || pais?.nombre || '',
                departamento_nombre: item.departamento?.nombre || item.departamento_nombre || departamento?.nombre || '',
                ciudad_nombre: item.ciudad?.nombre || item.ciudad_nombre || ciudad?.nombre || '',
            };
        },

        async listarClientes() {
            try {
                const { data } = await api.get('/clientes');
                const listado = this.extraerListaCatalogo(data, ['clientes']);
                this.clientes = listado.map((item) => this.normalizarCliente(item));
            } catch (error) {
                this.clientes = [];
            }
        },

        normalizarTextoEstado(value) {
            return String(value || '')
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .trim()
                .toLowerCase();
        },

        obtenerEstadoIdPorNombre(nombreObjetivo) {
            const objetivo = this.normalizarTextoEstado(nombreObjetivo);

            if (!objetivo) {
                return null;
            }

            const encontrado = this.estados.find((estado) =>
                this.normalizarTextoEstado(estado?.nombre) === objetivo
            );

            return encontrado?.id ? Number(encontrado.id) : null;
        },

        obtenerEstadoActivoId() {
            return this.obtenerEstadoIdPorNombre('Activo') || 1;
        },

        obtenerEstadoInactivoId() {
            return this.obtenerEstadoIdPorNombre('Inactivo') || 2;
        },

        esClienteActivo(cliente) {
            const estadoId = Number(cliente?.estado_id || 0);
            const estadoActivoId = this.obtenerEstadoActivoId();

            if (estadoId > 0) {
                return estadoId === estadoActivoId;
            }

            const estadoNombre = cliente?.estado?.nombre || cliente?.estado_nombre || '';
            return this.normalizarTextoEstado(estadoNombre) === 'activo';
        },

        resolverNombrePrincipal(cliente) {
            if (cliente.tipo_persona === 'juridica') {
                return cliente.razon_social || cliente.nombre_comercial || 'Sin razon social';
            }

            const nombreCompleto = `${cliente.nombre || ''} ${cliente.apellido || ''}`.trim();
            return nombreCompleto || cliente.nombre || 'Sin nombre';
        },

        money(value) {
            return new Intl.NumberFormat('es-CO', {
                style: 'currency',
                currency: 'COP',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0,
            }).format(Number(value || 0));
        },

        valorPresente(value) {
            return value !== null && value !== undefined && String(value).trim() !== '';
        },

        esEmailValido(value) {
            if (!value) {
                return false;
            }

            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(value).trim());
        },

        obtenerIdCatalogo(catalogo, posiblesIds = [], posiblesNombres = []) {
            for (const candidate of posiblesIds) {
                const numericId = Number(candidate || 0);

                if (numericId > 0) {
                    return numericId;
                }
            }

            const normalizar = (value) => String(value || '').trim().toLowerCase();

            for (const nombre of posiblesNombres) {
                const nombreNormalizado = normalizar(nombre);

                if (!nombreNormalizado) {
                    continue;
                }

                const match = catalogo.find((item) => normalizar(item?.nombre) === nombreNormalizado);

                if (match?.id) {
                    return Number(match.id);
                }
            }

            return null;
        },

        fechaParaInputDate(value) {
            if (!value) {
                return '';
            }

            const raw = String(value).trim();

            // Backend often returns ISO datetime; date input needs YYYY-MM-DD.
            const isoMatch = raw.match(/^(\d{4}-\d{2}-\d{2})/);
            if (isoMatch) {
                return isoMatch[1];
            }

            const latamMatch = raw.match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
            if (latamMatch) {
                const [, day, month, year] = latamMatch;
                return `${year}-${month}-${day}`;
            }

            const parsed = new Date(raw);
            if (!Number.isNaN(parsed.getTime())) {
                const year = parsed.getFullYear();
                const month = String(parsed.getMonth() + 1).padStart(2, '0');
                const day = String(parsed.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }

            return '';
        },

        reiniciarFormulario() {
            this.form = this.formularioBase();
            this.validationMessage = '';
            this.editMode = false;
            this.editingId = null;
        },

        abrirModalCrear() {
            this.reiniciarFormulario();
            this.clientDialog = true;
        },

        verDetalle(item) {
            this.detalleCliente = {
                ...item,
            };
            this.detalleDialog = true;
        },

        editarDesdeDetalle(cliente) {
            if (!cliente) {
                return;
            }

            this.detalleDialog = false;
            this.abrirModalEditar(cliente);
        },

        abrirModalEditar(item) {
            const tipoDocumentoId = this.obtenerIdCatalogo(
                this.tiposDocumento,
                [item.tipo_documento_id, item.tipo_documento?.id],
                [item.tipo_documento_nombre, item.tipo_documento?.nombre]
            );
            const paisId = this.obtenerIdCatalogo(
                this.paises,
                [item.pais_id, item.pais?.id],
                [item.pais_nombre, item.pais?.nombre]
            );
            const departamentoId = this.obtenerIdCatalogo(
                this.departamentos,
                [item.departamento_id, item.departamento?.id],
                [item.departamento_nombre, item.departamento?.nombre, item.departamento]
            );
            const ciudadId = this.obtenerIdCatalogo(
                this.ciudades,
                [item.ciudad_id, item.ciudad?.id],
                [item.ciudad_nombre, item.ciudad?.nombre, item.ciudad]
            );
            const estadoId = this.obtenerIdCatalogo(
                this.estados,
                [item.estado_id, item.estado?.id],
                [item.estado_nombre, item.estado?.nombre]
            );

            this.validationMessage = '';
            this.editMode = true;
            this.editingId = item.id;
            this.isHydratingEditForm = true;
            this.form = {
                empresa_id: item.empresa_id || this.session?.empresa_id || this.session?.empresa?.id || null,
                tipo_persona: item.tipo_persona || 'natural',
                tipo_documento_id: tipoDocumentoId,
                numero_documento: item.numero_documento || '',
                nombre: item.nombre || '',
                apellido: item.apellido || '',
                razon_social: item.razon_social || '',
                nombre_comercial: item.nombre_comercial || '',
                email: item.email || '',
                celular: item.celular || '',
                telefono: item.telefono || '',
                direccion: item.direccion || '',
                pais_id: paisId,
                departamento_id: departamentoId,
                ciudad_id: ciudadId,
                fecha_nacimiento: this.fechaParaInputDate(item.fecha_nacimiento),
                genero: item.genero || null,
                limite_credito: Number(item.limite_credito || 0),
                saldo_credito: Number(item.saldo_credito || 0),
                dias_credito: Number(item.dias_credito || 0),
                estado_id: estadoId || this.obtenerEstadoActivoId(),
                creado_por: item.creado_por || this.session?.user?.id || null,
                actualizado_por: this.session?.user?.id || null,
            };

            this.$nextTick(() => {
                this.isHydratingEditForm = false;
            });

            this.clientDialog = true;
        },

        cerrarModal() {
            this.clientDialog = false;
            this.reiniciarFormulario();
        },

        validarFormulario() {
            if (!this.valorPresente(this.form.tipo_persona)) {
                this.validationMessage = 'El tipo de persona es obligatorio.';
                return false;
            }

            if (!this.valorPresente(this.form.tipo_documento_id)) {
                this.validationMessage = 'El tipo de documento es obligatorio.';
                return false;
            }

            if (!this.valorPresente(this.form.numero_documento)) {
                this.validationMessage = 'El numero de documento es obligatorio.';
                return false;
            }

            if (this.form.tipo_persona === 'natural') {
                if (!this.valorPresente(this.form.nombre) || !this.valorPresente(this.form.apellido)) {
                    this.validationMessage = 'Nombre y apellido son obligatorios para cliente natural.';
                    return false;
                }
            }

            if (this.form.tipo_persona === 'juridica' && !this.valorPresente(this.form.razon_social)) {
                this.validationMessage = 'La razon social es obligatoria para cliente juridico.';
                return false;
            }

            if (!this.esEmailValido(this.form.email)) {
                this.validationMessage = 'Debes ingresar un correo electronico valido.';
                return false;
            }

            if (!this.valorPresente(this.form.celular)) {
                this.validationMessage = 'El celular es obligatorio.';
                return false;
            }

            if (!this.valorPresente(this.form.pais_id) || !this.valorPresente(this.form.departamento_id) || !this.valorPresente(this.form.ciudad_id)) {
                this.validationMessage = 'Debes completar pais, departamento y ciudad.';
                return false;
            }

            if (!this.valorPresente(this.form.estado_id)) {
                this.validationMessage = 'El estado es obligatorio.';
                return false;
            }

            if (Number(this.form.limite_credito || 0) < 0 || Number(this.form.saldo_credito || 0) < 0 || Number(this.form.dias_credito || 0) < 0) {
                this.validationMessage = 'Los valores de credito y dias no pueden ser negativos.';
                return false;
            }

            if (Number(this.form.limite_credito || 0) > Number(this.form.saldo_credito || 0)) {
                this.validationMessage = 'El limite de credito no puede ser mayor que el saldo credito.';
                return false;
            }

            this.validationMessage = '';
            return true;
        },

        payloadCliente() {
            const basePayload = {
                tipo_persona: this.form.tipo_persona,
                tipo_documento_id: this.form.tipo_documento_id,
                numero_documento: this.form.numero_documento,
                nombre: this.form.tipo_persona === 'natural' ? this.form.nombre : this.form.nombre || 'N/A',
                apellido: this.form.tipo_persona === 'natural' ? this.form.apellido : this.form.apellido || 'N/A',
                razon_social: this.form.tipo_persona === 'juridica' ? this.form.razon_social : null,
                nombre_comercial: this.form.tipo_persona === 'juridica' ? this.form.nombre_comercial || null : null,
                email: this.form.email,
                celular: this.form.celular,
                telefono: this.form.telefono || null,
                direccion: this.form.direccion || null,
                pais_id: this.form.pais_id,
                departamento_id: this.form.departamento_id,
                ciudad_id: this.form.ciudad_id,
                fecha_nacimiento: this.form.fecha_nacimiento || null,
                genero: this.form.genero || null,
                limite_credito: Number(this.form.limite_credito || 0),
                saldo_credito: Number(this.form.saldo_credito || 0),
                dias_credito: Number(this.form.dias_credito || 0),
                estado_id: this.form.estado_id,
            };

            return basePayload;
        },

        async guardarCliente() {
            if (!this.validarFormulario()) {
                return;
            }

            const actionLabel = this.editMode ? 'Guardando cambios de cliente...' : 'Creando cliente...';
            this.$emit('start-action', actionLabel, null, null);

            try {
                await this.esperarTresSegundos();

                const payload = this.payloadCliente();

                if (this.editMode) {
                    const { data } = await api.put(`/clientes/${this.editingId}/actualizar`, payload);
                    const clienteActualizado = data?.cliente || data?.data || null;

                    if (clienteActualizado) {
                        this.clientes = this.clientes.map((item) =>
                            Number(item.id) === Number(this.editingId) ? this.normalizarCliente(clienteActualizado) : item
                        );
                    }
                } else {
                    const { data } = await api.post('/clientes/crear', payload);
                    const clienteCreado = data?.cliente || data?.data || null;

                    if (clienteCreado) {
                        this.clientes.unshift(this.normalizarCliente(clienteCreado));
                    } else {
                        const nextId = this.clientes.length
                            ? Math.max(...this.clientes.map((item) => Number(item.id) || 0)) + 1
                            : 1;

                        this.clientes.unshift({
                            id: nextId,
                            ...payload,
                        });
                    }
                }

                this.cerrarModal();
                await this.listarClientes();
            } catch (error) {
                this.validationMessage = this.resolverError(error);
            } finally {
                this.$emit('stop-action');
            }
        },

        solicitarCambioEstado(item) {
            this.pendingClient = item;
            this.pendingAction = this.esClienteActivo(item) ? 'inactivar' : 'activar';
            this.confirmDialog = true;
        },

        cerrarDialogoConfirmacion() {
            this.confirmDialog = false;
            this.pendingClient = null;
            this.pendingAction = '';
        },

        async confirmarCambioEstado() {
            if (!this.pendingClient) {
                this.cerrarDialogoConfirmacion();
                return;
            }

            const clienteId = this.pendingClient.id;
            const estadoObjetivoId = this.pendingAction === 'activar'
                ? this.obtenerEstadoActivoId()
                : this.obtenerEstadoInactivoId();
            const actionLabel = `${this.pendingAction === 'activar' ? 'Activando' : 'Inactivando'} cliente...`;

            this.confirmDialog = false;
            this.$emit('start-action', actionLabel, null, null);

            try {
                await this.esperarTresSegundos();

                const { data } = await api.post(`/clientes/${clienteId}/cambiarEstado`, {
                    estado_id: estadoObjetivoId,
                });

                const clienteActualizado = data?.cliente || data?.data || null;

                if (clienteActualizado) {
                    this.clientes = this.clientes.map((item) =>
                        Number(item.id) === Number(clienteId) ? this.normalizarCliente(clienteActualizado) : item
                    );
                }

                await this.listarClientes();
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
.clients-shell {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.hero-card {
    display: flex;
    justify-content: flex-start;
    gap: 14px;
    border-radius: 18px;
    border: 1px solid rgba(23, 48, 79, 0.12);
    background:
        radial-gradient(circle at 92% 8%, rgba(244, 183, 64, 0.24), transparent 35%),
        linear-gradient(120deg, #ffffff 0%, #f5f9ff 100%);
    padding: 18px;
}

.hero-kicker {
    display: inline-block;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    font-size: 0.68rem;
    color: rgba(23, 48, 79, 0.62);
}

.hero-card h2 {
    margin: 8px 0;
    color: #17304f;
    font-size: clamp(1.15rem, 2.2vw, 1.6rem);
}

.hero-card p {
    margin: 0;
    color: rgba(23, 48, 79, 0.74);
    max-width: 760px;
}

.toolbar-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 14px;
    border-radius: 16px;
    border: 1px solid rgba(23, 48, 79, 0.1);
    background: rgba(255, 255, 255, 0.9);
}

.search-field {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 10px;
    border-radius: 12px;
    border: 1px solid rgba(23, 48, 79, 0.14);
    background: #ffffff;
    padding: 0 12px;
    min-height: 44px;
}

.search-field i {
    color: rgba(23, 48, 79, 0.58);
}

.search-field input {
    border: 0;
    outline: none;
    width: 100%;
    color: #17304f;
    font-size: 0.95rem;
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

.kpi-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    color: rgba(23, 48, 79, 0.74);
    font-size: 0.82rem;
}

.kpi-head i {
    font-size: 1.1rem;
    color: #17304f;
}

.kpi-value {
    display: block;
    margin-top: 8px;
    color: #17304f;
    font-size: clamp(1.15rem, 1.6vw, 1.45rem);
}

.kpi-note {
    display: block;
    margin-top: 4px;
    color: rgba(23, 48, 79, 0.58);
    font-size: 0.78rem;
}

.table-card {
    padding: 24px;
    border-radius: 24px;
    background: rgba(255, 255, 255, 0.92);
    border: 1px solid rgba(23, 48, 79, 0.08);
    box-shadow: 0 20px 48px rgba(14, 28, 54, 0.08);
}

.table-wrap {
    overflow: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 980px;
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
    white-space: nowrap;
}

td strong,
td small {
    display: block;
}

td small {
    margin-top: 4px;
    color: rgba(23, 48, 79, 0.58);
}

.person-type-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 86px;
    padding: 7px 12px;
    border-radius: 999px;
    font-size: 0.76rem;
    font-weight: 800;
}

.person-type-pill--person {
    color: #1d4ed8;
    background: rgba(59, 130, 246, 0.14);
}

.person-type-pill--company {
    color: #0b5c56;
    background: rgba(16, 185, 129, 0.17);
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

.field-full {
    grid-column: span 6;
}

.field-half {
    grid-column: span 3;
}

.field-third {
    grid-column: span 2;
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
    justify-content: center;
    gap: 8px;
    cursor: pointer;
}

.flash {
    border-radius: 12px;
    padding: 10px 12px;
    font-size: 0.88rem;
    font-weight: 700;
    margin-bottom: 14px;
}

.flash.error {
    background: rgba(255, 123, 123, 0.16);
    color: #9b2f2f;
}

@media (max-width: 1120px) {
    .kpi-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .dialog-grid--cols-2 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .field-full,
    .field-half,
    .field-third {
        grid-column: span 1;
    }
}

@media (max-width: 720px) {
    .toolbar-card {
        flex-direction: column;
        align-items: stretch;
    }

    .kpi-grid {
        grid-template-columns: 1fr;
    }

    .table-card {
        padding: 16px;
        border-radius: 16px;
    }

    .dialog-title {
        font-size: 1.22rem;
    }

    .dialog-card-title {
        align-items: flex-start;
    }
}
</style>

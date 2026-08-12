<template>
    <section class="proveedores-shell">
        <article class="hero-card">
            <div>
                <span class="hero-kicker">Proveedores</span>
                <h2>Listado de proveedores</h2>
                <p>
                    Consulta, busca y administra la información de todos los proveedores registrados.
                    Accede rápidamente a sus datos comerciales, contacto, crédito y estado.
                </p>
            </div>
        </article>

        <article class="toolbar-card">
            <label class="search-field" for="search-proveedores">
                <i class="mdi mdi-magnify"></i>
                <input id="search-proveedores" v-model.trim="search" type="text"
                    placeholder="Buscar por nombre, NIT, ciudad o telefono" />
            </label>

            <button type="button" class="submit-button" @click="registrarProveedor">
                <i class="mdi mdi-plus"></i>
                <span>Registrar proveedor</span>
            </button>
        </article>

        <section class="kpi-grid" aria-label="Metricas de proveedores">
            <article class="kpi-card kpi-card--total">
                <div class="kpi-head">
                    <span>Total proveedores</span>
                    <i class="mdi mdi-account-group-outline"></i>
                </div>
                <strong class="kpi-value">{{ totalProveedores }}</strong>
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
                    <span>Total empresas inactivas</span>
                    <i class="mdi mdi-account-off-outline"></i>
                </div>
                <strong class="kpi-value">{{ totalEmpresasInactivas }}</strong>
                <small class="kpi-note">Registros con estado inactivo</small>
            </article>

            <article class="kpi-card kpi-card--credit">
                <div class="kpi-head">
                    <span>Cupo total</span>
                    <i class="mdi mdi-cash-multiple"></i>
                </div>
                <strong class="kpi-value">{{ money(cupoTotal) }}</strong>
                <small class="kpi-note">Capacidad de credito consolidada</small>
            </article>

        </section>

        <article class="table-card" aria-label="Listado de proveedores en tabla">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Proveedor</th>
                            <th>Tipo documento</th>
                            <th>Documento</th>
                            <th>Ubicacion</th>
                            <th>Contacto</th>
                            <th>Correo</th>
                            <th>Sitio web</th>
                            <th>Cupo</th>
                            <th>Dias credito</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="proveedor in filteredProveedores" :key="proveedor.id">
                            <td>
                                <strong>{{ proveedor.nombre_comercial || proveedor.razon_social }}</strong>
                                <small>{{ proveedor.razon_social }}</small>
                            </td>
                            <td>
                                <strong>{{ proveedor.tipo_documento_nombre || 'Documento' }}</strong>
                            </td>
                            <td>
                                <span class="doc-number">{{ proveedor.numero_documento }}</span>
                                <small v-if="proveedor.codigo_verificacion">DV {{ proveedor.codigo_verificacion }}</small>
                            </td>
                            <td>
                                <strong>{{ proveedor.ciudad_nombre || '-' }}</strong>
                                <small>{{ proveedor.departamento_nombre || '-' }}</small>
                            </td>
                            <td>
                                <strong>{{ proveedor.telefono || 'Sin telefono' }}</strong>
                                <small>{{ proveedor.celular || 'Sin celular' }}</small>
                            </td>
                            <td>
                                <strong>{{ proveedor.email || 'Sin correo' }}</strong>
                            </td>
                            <td>
                                <a
                                    v-if="proveedor.sitio_web"
                                    :href="normalizarSitioWeb(proveedor.sitio_web)"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="web-link"
                                >
                                    <i class="mdi mdi-web"></i>
                                    <span>{{ resumenSitioWeb(proveedor.sitio_web) }}</span>
                                </a>
                                <span v-else class="web-link web-link--empty">Sin web</span>
                            </td>
                            <td>
                                <strong class="money-cell">{{ money(proveedor.cupo_credito) }}</strong>
                            </td>
                            <td>
                                <span :class="['days-credit-chip', diasCreditoClass(proveedor.dias_credito)]">
                                    {{ Number(proveedor.dias_credito || 0) }} dias
                                </span>
                            </td>
                            <td>
                                <span :class="['status-pill', esProveedorActivo(proveedor) ? 'status-ok' : 'status-off']">
                                    {{ esProveedorActivo(proveedor) ? 'Activa' : 'Inactiva' }}
                                </span>
                            </td>
                            <td>
                                <div class="row-actions">
                                    <button
                                        type="button"
                                        class="action-button"
                                        @click="editarProveedor(proveedor)"
                                        aria-label="Editar proveedor"
                                    >
                                        <i class="mdi mdi-pencil"></i>
                                        <span class="button-tooltip">Editar</span>
                                    </button>

                                    <button
                                        type="button"
                                        :class="['action-button', esProveedorActivo(proveedor) ? 'action-disable' : 'action-enable']"
                                        @click="solicitarCambioEstado(proveedor)"
                                        :aria-label="esProveedorActivo(proveedor) ? 'Desactivar proveedor' : 'Activar proveedor'"
                                    >
                                        <i :class="esProveedorActivo(proveedor) ? 'mdi mdi-close-circle-outline' : 'mdi mdi-check-circle-outline'"></i>
                                        <span class="button-tooltip">{{ esProveedorActivo(proveedor) ? 'Desactivar' : 'Activar' }}</span>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="!filteredProveedores.length">
                            <td colspan="11" class="empty-row">No encontramos proveedores con ese criterio de busqueda.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </article>

        <v-dialog v-model="registrarDialog" max-width="900px" persistent>
            <v-card class="dialog-card">
                <div class="dialog-topbar">
                    <div class="dialog-topbar-main">
                        <v-avatar size="48" class="dialog-avatar">
                            <v-icon large>mdi-truck-plus-outline</v-icon>
                        </v-avatar>
                        <div class="dialog-topbar-copy">
                            <span class="dialog-kicker">{{ isEditMode ? 'Editar proveedor' : 'Nuevo proveedor' }}</span>
                            <h3 class="dialog-title">{{ isEditMode ? 'Actualizar proveedor' : 'Registrar proveedor' }}</h3>
                            <p class="dialog-description">
                                {{ isEditMode
                                    ? 'Ajusta la informacion comercial, ubicacion y cartera del proveedor seleccionado.'
                                    : 'Configura informacion comercial, ubicacion y cartera para dejar el proveedor listo en operaciones.' }}
                            </p>
                        </div>
                    </div>

                    <span class="dialog-meta-pill">
                        <i :class="isEditMode ? 'mdi mdi-pencil-circle-outline' : 'mdi mdi-flash-outline'"></i>
                        {{ isEditMode ? 'Modo edicion' : 'Registro rapido' }}
                    </span>
                </div>

                <v-divider />

                <v-card-text class="dialog-card-body">
                    <p v-if="validationMessage" class="validation-banner">{{ validationMessage }}</p>

                    <div class="dialog-layout">
                        <aside class="dialog-aside">
                            <h4>Checklist</h4>
                            <p>Completa cada bloque para habilitar el siguiente paso del registro.</p>

                            <div class="checklist-stack">
                                <button
                                    v-for="item in checklistItems"
                                    :key="item.key"
                                    type="button"
                                    :class="[
                                        'check-item',
                                        { 'check-item--done': item.done, 'check-item--active': activeBlock === item.key }
                                    ]"
                                    @click="abrirBloque(item.key)"
                                    :disabled="!puedeAbrirBloque(item.key)"
                                >
                                    <span class="check-dot">
                                        <i :class="item.done ? 'mdi mdi-check-bold' : 'mdi mdi-circle-medium'"></i>
                                    </span>
                                    <span class="check-copy">
                                        <strong>{{ item.title }}</strong>
                                        <small>{{ item.done ? 'Completado' : 'Pendiente' }}</small>
                                    </span>
                                </button>
                            </div>
                        </aside>

                        <div class="dialog-form">
                            <section class="form-panel" :class="{ 'form-panel--done': isBloqueCompleto('identificacion') }">
                                <button type="button" class="panel-toggle" @click="abrirBloque('identificacion')">
                                    <h4 class="dialog-section-title">Identificacion y empresa</h4>
                                    <span class="panel-status">{{ isBloqueCompleto('identificacion') ? 'Completado' : 'En curso' }}</span>
                                </button>

                                <div v-show="activeBlock === 'identificacion'" class="dialog-grid">
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
                                        <input v-model.trim="form.numero_documento" type="tel" inputmode="numeric" data-only-numeric="true" placeholder="Ej: 900123456" />
                                    </label>

                                    <label class="field field-half">
                                        <span>Codigo verificacion</span>
                                        <input v-model.trim="form.codigo_verificacion" type="tel" inputmode="numeric" data-only-numeric="true" placeholder="Ej: 8" />
                                    </label>

                                    <label class="field field-full">
                                        <span>Razon social</span>
                                        <input v-model.trim="form.razon_social" type="text" placeholder="Ej: Proveeduria Integral Andina S.A.S." />
                                    </label>

                                    <label class="field field-full">
                                        <span>Nombre comercial</span>
                                        <input v-model.trim="form.nombre_comercial" type="text" placeholder="Ej: PIA Supply" />
                                    </label>

                                    <div class="field field-full block-action-row">
                                        <button
                                            type="button"
                                            class="submit-button block-next-button"
                                            :disabled="!isBloqueCompleto('identificacion')"
                                            @click="avanzarBloque('identificacion')"
                                        >
                                            Completar y continuar
                                            <i class="mdi mdi-arrow-right"></i>
                                        </button>
                                    </div>
                                </div>
                            </section>

                            <section class="form-panel" :class="{ 'form-panel--done': isBloqueCompleto('ubicacion') }">
                                <button type="button" class="panel-toggle" @click="abrirBloque('ubicacion')">
                                    <h4 class="dialog-section-title">Contacto y ubicacion fiscal</h4>
                                    <span class="panel-status">{{ isBloqueCompleto('ubicacion') ? 'Completado' : 'En curso' }}</span>
                                </button>

                                <div v-show="activeBlock === 'ubicacion'" class="dialog-grid">
                                    <label class="field field-full">
                                        <span>Direccion</span>
                                        <input v-model.trim="form.direccion" type="text" placeholder="Ej: Cl 127 # 7-19" />
                                    </label>

                                    <label class="field field-half">
                                        <span>Telefono</span>
                                        <input v-model.trim="form.telefono" type="tel" inputmode="numeric" data-only-numeric="true" placeholder="Ej: 6017421100" />
                                    </label>

                                    <label class="field field-half">
                                        <span>Celular</span>
                                        <input v-model.trim="form.celular" type="tel" inputmode="numeric" data-only-numeric="true" placeholder="Ej: 3206103344" />
                                    </label>

                                    <label class="field field-full">
                                        <span>Email</span>
                                        <input v-model.trim="form.email" type="email" placeholder="Ej: contacto@proveedor.co" />
                                    </label>

                                    <label class="field field-full">
                                        <span>Sitio web</span>
                                        <input v-model.trim="form.sitio_web" type="text" placeholder="Ej: proveedor.com" />
                                    </label>

                                    <label class="field field-half">
                                        <span>Pais</span>
                                        <select v-model.number="form.pais_id">
                                            <option :value="null">Selecciona un pais</option>
                                            <option v-for="pais in paises" :key="pais.id" :value="pais.id">
                                                {{ pais.nombre }}
                                            </option>
                                        </select>
                                    </label>

                                    <label class="field field-half">
                                        <span>Departamento</span>
                                        <select v-model.number="form.departamento_id">
                                            <option :value="null">Selecciona un departamento</option>
                                            <option v-for="departamento in departamentosDisponibles" :key="departamento.id" :value="departamento.id">
                                                {{ departamento.nombre }}
                                            </option>
                                        </select>
                                    </label>

                                    <label class="field field-half">
                                        <span>Ciudad</span>
                                        <select v-model.number="form.ciudad_id">
                                            <option :value="null">Selecciona una ciudad</option>
                                            <option v-for="ciudad in ciudadesDisponibles" :key="ciudad.id" :value="ciudad.id">
                                                {{ ciudad.nombre }}
                                            </option>
                                        </select>
                                    </label>

                                    <label class="field field-half">
                                        <span>Codigo postal</span>
                                        <input v-model.trim="form.codigo_postal" type="tel" inputmode="numeric" data-only-numeric="true" placeholder="Ej: 110111" />
                                    </label>

                                    <div class="field field-full block-action-row">
                                        <button
                                            type="button"
                                            class="submit-button block-next-button"
                                            :disabled="!isBloqueCompleto('ubicacion')"
                                            @click="avanzarBloque('ubicacion')"
                                        >
                                            Completar y continuar
                                            <i class="mdi mdi-arrow-right"></i>
                                        </button>
                                    </div>
                                </div>
                            </section>

                            <section class="form-panel" :class="{ 'form-panel--done': isBloqueCompleto('cartera') }">
                                <button type="button" class="panel-toggle" @click="abrirBloque('cartera')">
                                    <h4 class="dialog-section-title">Estado y cartera</h4>
                                    <span class="panel-status">{{ isBloqueCompleto('cartera') ? 'Completado' : 'En curso' }}</span>
                                </button>

                                <div v-show="activeBlock === 'cartera'" class="dialog-grid">
                                    <label class="field field-half">
                                        <span>Cupo credito</span>
                                        <input v-model.number="form.cupo_credito" type="number" min="0" placeholder="Ej: 120000000" />
                                    </label>

                                    <label class="field field-half">
                                        <span>Dias credito</span>
                                        <input v-model.number="form.dias_credito" type="number" min="0" placeholder="Ej: 30" />
                                    </label>

                                    <label class="field field-half">
                                        <span>Estado</span>
                                        <select v-model.number="form.estado_id">
                                            <option :value="null">Selecciona un estado</option>
                                            <option v-for="estado in estados" :key="estado.id" :value="estado.id">
                                                {{ estado.nombre }}
                                            </option>
                                        </select>
                                    </label>

                                    <label class="field field-full">
                                        <span>Observaciones</span>
                                        <textarea v-model.trim="form.observaciones" rows="3" placeholder="Notas internas del proveedor"></textarea>
                                    </label>
                                </div>
                            </section>
                        </div>
                    </div>
                </v-card-text>

                <v-divider />

                <v-card-actions class="dialog-actions">
                    <button type="button" class="secondary-button" @click="cerrarModalRegistrar">
                        Cancelar
                    </button>
                    <button type="button" class="submit-button" @click="guardarProveedorFront">
                        {{ isEditMode ? 'Guardar cambios' : 'Guardar proveedor' }}
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
                        <h3 class="dialog-title">{{ pendingAction === 'activar' ? 'Activar proveedor' : 'Desactivar proveedor' }}</h3>
                    </div>
                </v-card-title>

                <v-divider />

                <v-card-text class="dialog-card-body">
                    <p>
                        {{ pendingAction === 'activar' ? 'Deseas activar el proveedor' : 'Deseas desactivar el proveedor' }}
                        <strong>"{{ pendingProveedor ? (pendingProveedor.nombre_comercial || pendingProveedor.razon_social) : '' }}"</strong>?
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
    name: 'ProvedoresListarView',

    data() {
        return {
            rules: {
                required: (value) => !!value || 'Este campo es requerido.',
                email: (value) => !value || /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(value).trim()) || 'Correo electrónico inválido.',
                nonNegative: (value) => Number(value ?? 0) >= 0 || 'El valor no puede ser negativo.',
            },
            search: '',
            registrarDialog: false,
            isEditMode: false,
            editingProveedorId: null,
            isHydratingEditForm: false,
            confirmDialog: false,
            pendingProveedor: null,
            pendingAction: '',
            activeBlock: 'identificacion',
            proveedores: [],
            validationMessage: '',
            fieldErrors: {},
            tiposDocumento: [],
            paises: [],
            departamentos: [],
            ciudades: [],
            estados: [],
            form: {
                tipo_documento_id: null,
                numero_documento: '',
                codigo_verificacion: '',
                razon_social: '',
                nombre_comercial: '',
                direccion: '',
                telefono: '',
                celular: '',
                email: '',
                sitio_web: '',
                pais_id: null,
                departamento_id: null,
                ciudad_id: null,
                codigo_postal: '',
                cupo_credito: null,
                estado_id: null,
                dias_credito: null,
                observaciones: '',
            },
        };
    },

    mounted() {
        this.inicializarVista();
    },

    computed: {
        totalProveedores() {
            return this.proveedores.length;
        },

        filteredProveedores() {
            if (!this.search) {
                return this.proveedores;
            }

            const term = this.search.toLowerCase();

            return this.proveedores.filter((item) =>
                [
                    item.razon_social,
                    item.nombre_comercial,
                    item.numero_documento,
                    item.ciudad_nombre,
                    item.departamento_nombre,
                    item.telefono,
                    item.celular,
                    item.email,
                    item.sitio_web,
                ]
                    .join(' ')
                    .toLowerCase()
                    .includes(term)
            );
        },

        totalActivos() {
            return this.proveedores.filter((item) => this.esProveedorActivo(item)).length;
        },

        totalEmpresasInactivas() {
            return this.proveedores.filter((item) => !this.esProveedorActivo(item)).length;
        },

        cupoTotal() {
            return this.proveedores.reduce((acc, item) => acc + Number(item.cupo_credito || 0), 0);
        },

        promedioDiasCredito() {
            if (!this.proveedores.length) {
                return 0;
            }

            const totalDias = this.proveedores.reduce((acc, item) => acc + Number(item.dias_credito || 0), 0);
            return Math.round(totalDias / this.proveedores.length);
        },

        checklistItems() {
            return [
                {
                    key: 'identificacion',
                    title: 'Identificacion fiscal',
                    done: this.isBloqueCompleto('identificacion'),
                },
                {
                    key: 'ubicacion',
                    title: 'Ubicacion tributaria',
                    done: this.isBloqueCompleto('ubicacion'),
                },
                {
                    key: 'cartera',
                    title: 'Condiciones de credito',
                    done: this.isBloqueCompleto('cartera'),
                },
            ];
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
        registrarDialog(value) {
            if (value) {
                this.activeBlock = 'identificacion';
            }
        },

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

    methods: {
        async inicializarVista() {
            await Promise.all([this.cargarCatalogosUbicacionYDocumento(), this.listarProveedores()]);
        },

        registrarProveedor() {
            this.isEditMode = false;
            this.editingProveedorId = null;
            this.limpiarFormularioProveedor();
            this.resetValidation();
            this.registrarDialog = true;
        },

        async editarProveedor(proveedor) {
            if (
                !this.tiposDocumento.length ||
                !this.paises.length ||
                !this.departamentos.length ||
                !this.ciudades.length ||
                !this.estados.length
            ) {
                await this.cargarCatalogosUbicacionYDocumento();
            }

            const tipoDocumentoId = this.obtenerIdCatalogo(
                this.tiposDocumento,
                [proveedor.tipo_documento_id, proveedor.tipo_documento?.id],
                [proveedor.tipo_documento_nombre, proveedor.tipo_documento?.nombre]
            );
            const paisId = this.obtenerIdCatalogo(
                this.paises,
                [proveedor.pais_id, proveedor.pais?.id],
                [proveedor.pais_nombre, proveedor.pais?.nombre]
            );
            const departamentoId = this.obtenerIdCatalogo(
                this.departamentos,
                [proveedor.departamento_id, proveedor.departamento?.id],
                [proveedor.departamento_nombre, proveedor.departamento?.nombre, proveedor.departamento]
            );
            const ciudadId = this.obtenerIdCatalogo(
                this.ciudades,
                [proveedor.ciudad_id, proveedor.ciudad?.id],
                [proveedor.ciudad_nombre, proveedor.ciudad?.nombre, proveedor.ciudad]
            );
            const estadoId = this.obtenerIdCatalogo(
                this.estados,
                [proveedor.estado_id, proveedor.estado?.id],
                [proveedor.estado_nombre, proveedor.estado?.nombre]
            );

            this.isEditMode = true;
            this.editingProveedorId = proveedor.id;
            this.resetValidation();
            this.isHydratingEditForm = true;
            this.form = {
                tipo_documento_id: tipoDocumentoId,
                numero_documento: proveedor.numero_documento || '',
                codigo_verificacion: proveedor.codigo_verificacion || '',
                razon_social: proveedor.razon_social || '',
                nombre_comercial: proveedor.nombre_comercial || '',
                direccion: proveedor.direccion || '',
                telefono: proveedor.telefono || '',
                celular: proveedor.celular || '',
                email: proveedor.email || '',
                sitio_web: proveedor.sitio_web || '',
                pais_id: paisId,
                departamento_id: departamentoId,
                ciudad_id: ciudadId,
                codigo_postal: proveedor.codigo_postal || '',
                cupo_credito: proveedor.cupo_credito ?? null,
                estado_id: estadoId,
                dias_credito: proveedor.dias_credito ?? null,
                observaciones: proveedor.observaciones || '',
            };

            this.$nextTick(() => {
                this.isHydratingEditForm = false;
            });

            const primerBloquePendiente = ['identificacion', 'ubicacion', 'cartera'].find(
                (bloque) => !this.isBloqueCompleto(bloque)
            );

            this.activeBlock = primerBloquePendiente || 'identificacion';
            this.registrarDialog = true;
        },

        solicitarCambioEstado(proveedor) {
            this.pendingProveedor = proveedor;
            this.pendingAction = this.esProveedorActivo(proveedor) ? 'desactivar' : 'activar';
            this.confirmDialog = true;
        },

        cerrarDialogoConfirmacion() {
            this.confirmDialog = false;
            this.pendingProveedor = null;
            this.pendingAction = '';
        },

        async confirmarCambioEstado() {
            if (!this.pendingProveedor) {
                this.cerrarDialogoConfirmacion();
                return;
            }

            const proveedorId = this.pendingProveedor.id;
            const estadoObjetivoId = this.pendingAction === 'activar'
                ? this.obtenerEstadoActivoId()
                : this.obtenerEstadoInactivoId();

            if (!estadoObjetivoId) {
                this.validationMessage = 'No fue posible resolver el estado de destino para el proveedor.';
                this.cerrarDialogoConfirmacion();
                return;
            }

            const actionLabel = `${this.pendingAction === 'activar' ? 'Activando' : 'Desactivando'} proveedor...`;

            this.confirmDialog = false;
            this.$emit('start-action', actionLabel, null, null);

            try {
                await this.esperarTresSegundos();
                await api.post(`/provedores/${proveedorId}/cambiarEstado`, {
                    estado_id: estadoObjetivoId,
                });
                await this.listarProveedores();
                this.cerrarDialogoConfirmacion();
            } catch (error) {
                const mensaje = this.resolverError(error);
                this.validationMessage = mensaje;
            } finally {
                this.$emit('stop-action');
            }
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

        async cargarCatalogosUbicacionYDocumento() {
            try {
                const [tipoDocumentoRes, paisesRes, departamentosRes, ciudadesRes, estadosRes] = await Promise.all([
                    api.get('/tipo-documento'),
                    api.get('/paises'),
                    api.get('/departamentos'),
                    api.get('/ciudades'),
                    api.get('/estados'),
                ]);

                this.tiposDocumento = this.extraerListaCatalogo(tipoDocumentoRes?.data, ['tipos_documento', 'tiposDocumento']);
                this.paises = this.extraerListaCatalogo(paisesRes?.data, ['paises']);
                this.departamentos = this.extraerListaCatalogo(departamentosRes?.data, ['departamentos']);
                this.ciudades = this.extraerListaCatalogo(ciudadesRes?.data, ['ciudades']);
                this.estados = this.extraerListaCatalogo(estadosRes?.data, ['estados']);
            } catch (error) {
                this.tiposDocumento = [];
                this.paises = [];
                this.departamentos = [];
                this.ciudades = [];
                this.estados = [];
            }
        },

        normalizarProveedor(item) {
            const tipoDocumento = this.tiposDocumento.find((tipo) => Number(tipo.id) === Number(item.tipo_documento_id));
            const departamento = this.departamentos.find((dep) => Number(dep.id) === Number(item.departamento_id));
            const ciudad = this.ciudades.find((ciu) => Number(ciu.id) === Number(item.ciudad_id));

            return {
                ...item,
                tipo_documento_nombre: item.tipo_documento?.nombre || item.tipo_documento_nombre || tipoDocumento?.nombre || '',
                departamento_nombre: item.departamento?.nombre || item.departamento_nombre || departamento?.nombre || '',
                ciudad_nombre: item.ciudad?.nombre || item.ciudad_nombre || ciudad?.nombre || '',
            };
        },

        async listarProveedores() {
            try {
                const { data } = await api.get('/provedores');
                const listado = Array.isArray(data?.provedores) ? data.provedores : [];
                this.proveedores = listado.map((item) => this.normalizarProveedor(item));
            } catch (error) {
                this.proveedores = [];
            }
        },

        esperarTresSegundos() {
            return new Promise((resolve) => {
                window.setTimeout(resolve, 3000);
            });
        },

        valorPresente(value) {
            return value !== null && value !== undefined && String(value).trim() !== '';
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

        isBloqueCompleto(bloque) {
            if (bloque === 'identificacion') {
                return [
                    this.form.tipo_documento_id,
                    this.form.numero_documento,
                    this.form.razon_social,
                ].every((value) => this.valorPresente(value));
            }

            if (bloque === 'ubicacion') {
                return [
                    this.form.pais_id,
                    this.form.departamento_id,
                    this.form.ciudad_id,
                ].every((value) => this.valorPresente(value));
            }

            if (bloque === 'cartera') {
                return [
                    this.form.estado_id,
                ].every((value) => this.valorPresente(value));
            }

            return false;
        },

        puedeAbrirBloque(bloque) {
            const orden = ['identificacion', 'ubicacion', 'cartera'];
            const index = orden.indexOf(bloque);

            if (index <= 0) {
                return true;
            }

            if (this.isBloqueCompleto(bloque)) {
                return true;
            }

            for (let i = 0; i < index; i += 1) {
                if (!this.isBloqueCompleto(orden[i])) {
                    return false;
                }
            }

            return true;
        },

        abrirBloque(bloque) {
            if (!this.puedeAbrirBloque(bloque)) {
                return;
            }

            this.activeBlock = bloque;
        },

        avanzarBloque(bloque) {
            if (!this.isBloqueCompleto(bloque)) {
                return;
            }

            const orden = ['identificacion', 'ubicacion', 'cartera'];
            const index = orden.indexOf(bloque);
            const siguiente = orden[index + 1];

            if (siguiente) {
                this.activeBlock = siguiente;
            }
        },

        cerrarModalRegistrar() {
            this.registrarDialog = false;
            this.limpiarFormularioProveedor();
            this.isEditMode = false;
            this.editingProveedorId = null;
            this.activeBlock = 'identificacion';
            this.resetValidation();
        },

        limpiarFormularioProveedor() {
            this.form = {
                tipo_documento_id: null,
                numero_documento: '',
                codigo_verificacion: '',
                razon_social: '',
                nombre_comercial: '',
                direccion: '',
                telefono: '',
                celular: '',
                email: '',
                sitio_web: '',
                pais_id: null,
                departamento_id: null,
                ciudad_id: null,
                codigo_postal: '',
                cupo_credito: null,
                estado_id: null,
                dias_credito: null,
                observaciones: '',
            };
        },

        async guardarProveedorFront() {
            this.resetValidation();

            if (!this.validarFormularioProveedor()) {
                return;
            }

            this.$emit('start-action', this.isEditMode ? 'Actualizando proveedor...' : 'Creando proveedor...', null, null);

            try {
                await this.esperarTresSegundos();

                const payload = {
                    tipo_documento_id: this.form.tipo_documento_id,
                    numero_documento: this.form.numero_documento,
                    codigo_verificacion: this.form.codigo_verificacion || null,
                    razon_social: this.form.razon_social,
                    nombre_comercial: this.form.nombre_comercial || null,
                    direccion: this.form.direccion || null,
                    telefono: this.form.telefono || null,
                    celular: this.form.celular || null,
                    email: this.form.email || null,
                    sitio_web: this.form.sitio_web || null,
                    pais_id: this.form.pais_id,
                    departamento_id: this.form.departamento_id,
                    ciudad_id: this.form.ciudad_id,
                    codigo_postal: this.form.codigo_postal || null,
                    cupo_credito: this.form.cupo_credito ?? 0,
                    estado_id: this.form.estado_id,
                    dias_credito: this.form.dias_credito ?? 0,
                    observaciones: this.form.observaciones || null,
                };

                if (this.isEditMode && this.editingProveedorId) {
                    await api.put(`/provedores/${this.editingProveedorId}/actualizar`, payload);
                } else {
                    await api.post('/provedores/crear', payload);
                }
                await this.listarProveedores();
                this.cerrarModalRegistrar();
            } catch (error) {
                this.validationMessage = this.resolverError(error);
            } finally {
                this.$emit('stop-action');
            }
        },

        money(value) {
            return new Intl.NumberFormat('es-CO', {
                style: 'currency',
                currency: 'COP',
                maximumFractionDigits: 0,
            }).format(Number(value || 0));
        },

        normalizarSitioWeb(url) {
            const limpio = String(url || '').trim();

            if (!limpio) {
                return '#';
            }

            if (/^https?:\/\//i.test(limpio)) {
                return limpio;
            }

            return `https://${limpio}`;
        },

        resumenSitioWeb(url) {
            const limpio = String(url || '').trim();
            const sinProtocolo = limpio.replace(/^https?:\/\//i, '').replace(/\/$/, '');

            if (sinProtocolo.length <= 26) {
                return sinProtocolo;
            }

            return `${sinProtocolo.slice(0, 26)}...`;
        },

        diasCreditoClass(value) {
            const dias = Number(value || 0);

            if (dias <= 0) {
                return 'days-credit-chip--none';
            }

            if (dias <= 30) {
                return 'days-credit-chip--short';
            }

            return 'days-credit-chip--long';
        },

        resetValidation() {
            this.validationMessage = '';
            this.fieldErrors = {};

            if (this.$refs.proveedorForm && typeof this.$refs.proveedorForm.resetValidation === 'function') {
                this.$refs.proveedorForm.resetValidation();
            }
        },

        aplicarRegla(ruleFn, value) {
            const resultado = ruleFn(value);
            return resultado === true ? '' : String(resultado || 'Campo inválido.');
        },

        validarFormularioProveedor() {
            const errores = {};

            const requiredFields = [
                { key: 'tipo_documento_id', label: 'Tipo documento', block: 'identificacion' },
                { key: 'numero_documento', label: 'Número documento', block: 'identificacion' },
                { key: 'razon_social', label: 'Razón social', block: 'identificacion' },
                { key: 'pais_id', label: 'País', block: 'ubicacion' },
                { key: 'departamento_id', label: 'Departamento', block: 'ubicacion' },
                { key: 'ciudad_id', label: 'Ciudad', block: 'ubicacion' },
            ];

            requiredFields.forEach(({ key, label }) => {
                const error = this.aplicarRegla(this.rules.required, this.form[key]);
                if (error) {
                    errores[key] = `${label}: ${error}`;
                }
            });

            const emailError = this.aplicarRegla(this.rules.email, this.form.email);
            if (emailError) {
                errores.email = emailError;
            }

            const cupoError = this.aplicarRegla(this.rules.nonNegative, this.form.cupo_credito);
            if (cupoError) {
                errores.cupo_credito = cupoError;
            }

            const diasError = this.aplicarRegla(this.rules.nonNegative, this.form.dias_credito);
            if (diasError) {
                errores.dias_credito = diasError;
            }

            this.fieldErrors = errores;

            const hayErrores = Object.keys(errores).length > 0;

            if (!hayErrores) {
                return true;
            }

            const primerBloqueConError = requiredFields.find((field) => errores[field.key])?.block;
            if (primerBloqueConError) {
                this.activeBlock = primerBloqueConError;
            }

            const primerMensaje = Object.values(errores)[0] || 'Verifica los campos del formulario antes de continuar.';
            this.validationMessage = primerMensaje;
            return false;
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

        esProveedorActivo(proveedor) {
            const estadoId = Number(proveedor?.estado_id || 0);
            const estadoActivoId = this.obtenerEstadoActivoId();

            if (estadoId > 0) {
                return estadoId === estadoActivoId;
            }

            const estadoNombre =
                proveedor?.estado?.nombre ||
                proveedor?.estado_nombre ||
                '';

            return this.normalizarTextoEstado(estadoNombre) === 'activo';
        },
    },
};
</script>

<style scoped>
.proveedores-shell {
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
    background: linear-gradient(180deg, #f4b740 0%, #d99210 100%);
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
    font-size: clamp(1.25rem, 2vw, 1.65rem);
    line-height: 1.1;
}

.kpi-note {
    display: block;
    margin-top: 6px;
    color: rgba(23, 48, 79, 0.64);
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

.kpi-card--credit::before {
    background: linear-gradient(180deg, #f4b740 0%, #d99210 100%);
}

.kpi-card--days::before {
    background: linear-gradient(180deg, #2f8f6d 0%, #1c5f47 100%);
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
}

.search-field {
    display: flex;
    align-items: center;
    gap: 8px;
    border-radius: 10px;
    border: 1px solid rgba(23, 48, 79, 0.14);
    background: #f8fbff;
    padding: 0 10px;
    width: min(560px, 100%);
}

.search-field i {
    color: rgba(23, 48, 79, 0.58);
    font-size: 19px;
}

.search-field input {
    border: 0;
    outline: none;
    width: 100%;
    height: 40px;
    background: transparent;
    color: #17304f;
}

.toolbar-meta {
    font-size: 0.78rem;
    color: rgba(23, 48, 79, 0.68);
    font-weight: 700;
}

.submit-button {
    border: 0;
    border-radius: 14px;
    background: linear-gradient(135deg, #f4b740 0%, #ffd277 45%, #d99210 100%);
    color: #0b1530;
    font-weight: 800;
    height: 44px;
    min-width: 180px;
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
    cursor: pointer;
}

.dialog-card {
    border-radius: 24px;
    overflow: hidden;
    border: 1px solid rgba(23, 48, 79, 0.14);
    box-shadow: 0 26px 52px rgba(23, 48, 79, 0.22);
}

.dialog-topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 14px;
    background:
        radial-gradient(circle at 95% 10%, rgba(244, 183, 64, 0.2), transparent 42%),
        linear-gradient(125deg, #f5f9ff 0%, #ffffff 100%);
    padding: 18px 22px;
}

.dialog-topbar-main {
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.dialog-topbar-copy {
    max-width: 620px;
}

.dialog-avatar {
    background: linear-gradient(135deg, #f4b740 0%, #ffd277 45%, #d99210 100%);
    color: #0b1530;
    box-shadow: 0 8px 16px rgba(217, 146, 16, 0.3);
}

.dialog-avatar-alert {
    background: linear-gradient(135deg, #d66161 0%, #9f2f2f 100%);
    color: #ffffff;
    box-shadow: 0 8px 16px rgba(159, 47, 47, 0.35);
}

.dialog-meta-pill {
    border-radius: 999px;
    border: 1px solid rgba(23, 48, 79, 0.16);
    background: rgba(255, 255, 255, 0.9);
    color: #17304f;
    font-size: 0.74rem;
    font-weight: 800;
    letter-spacing: 0.03em;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 12px;
}

.dialog-kicker {
    display: block;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    font-size: 0.68rem;
    color: rgba(23, 48, 79, 0.62);
}

.dialog-title {
    margin: 4px 0;
    color: #17304f;
    font-size: 1.15rem;
}

.dialog-description {
    margin: 0;
    color: rgba(23, 48, 79, 0.72);
    font-size: 0.84rem;
}

.dialog-card-body {
    padding: 16px 20px 20px;
}

.validation-banner {
    margin: 0 0 12px;
    border-radius: 10px;
    border: 1px solid rgba(159, 47, 47, 0.28);
    background: rgba(214, 97, 97, 0.1);
    color: #8b1f1f;
    font-size: 0.82rem;
    font-weight: 700;
    padding: 10px 12px;
}

.dialog-layout {
    display: grid;
    grid-template-columns: 220px minmax(0, 1fr);
    gap: 14px;
}

.dialog-aside {
    border-radius: 16px;
    border: 1px solid rgba(23, 48, 79, 0.12);
    background:
        radial-gradient(circle at 100% 0, rgba(244, 183, 64, 0.16), transparent 48%),
        #f8fbff;
    padding: 14px;
}

.dialog-aside h4 {
    margin: 0;
    color: #17304f;
    font-size: 0.92rem;
}

.dialog-aside p {
    margin: 8px 0 10px;
    color: rgba(23, 48, 79, 0.72);
    font-size: 0.8rem;
    line-height: 1.45;
}

.checklist-stack {
    display: grid;
    gap: 8px;
}

.dialog-form {
    display: grid;
    gap: 12px;
}

.form-panel {
    border-radius: 16px;
    border: 1px solid rgba(23, 48, 79, 0.1);
    background: #ffffff;
    padding: 12px;
}

.form-panel--done {
    border-color: rgba(13, 127, 127, 0.28);
    box-shadow: inset 0 0 0 1px rgba(13, 127, 127, 0.14);
}

.check-item {
    width: 100%;
    border: 1px solid rgba(23, 48, 79, 0.14);
    border-radius: 12px;
    background: #ffffff;
    padding: 10px;
    display: flex;
    align-items: center;
    gap: 10px;
    text-align: left;
    cursor: pointer;
    transition: transform 160ms ease, border-color 160ms ease, box-shadow 160ms ease;
}

.check-item:disabled {
    cursor: not-allowed;
    opacity: 0.6;
}

.check-item:not(:disabled):hover {
    transform: translateY(-1px);
    border-color: rgba(23, 48, 79, 0.26);
}

.check-item--active {
    border-color: rgba(45, 106, 159, 0.42);
    box-shadow: 0 0 0 2px rgba(45, 106, 159, 0.12);
}

.check-item--done {
    border-color: rgba(13, 127, 127, 0.3);
    background: rgba(13, 127, 127, 0.06);
}

.check-dot {
    width: 28px;
    height: 28px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: rgba(23, 48, 79, 0.1);
    color: #17304f;
}

.check-item--done .check-dot {
    background: rgba(13, 127, 127, 0.16);
    color: #0c7676;
}

.check-copy {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.check-copy strong {
    color: #17304f;
    font-size: 0.8rem;
}

.check-copy small {
    color: rgba(23, 48, 79, 0.62);
    font-size: 0.72rem;
    font-weight: 700;
}

.panel-toggle {
    width: 100%;
    border: 0;
    background: transparent;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    text-align: left;
    padding: 0;
    cursor: pointer;
    margin-bottom: 8px;
}

.panel-status {
    border-radius: 999px;
    border: 1px solid rgba(23, 48, 79, 0.16);
    background: rgba(23, 48, 79, 0.06);
    color: rgba(23, 48, 79, 0.78);
    font-size: 0.7rem;
    font-weight: 800;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    padding: 6px 10px;
}

.form-panel--done .panel-status {
    border-color: rgba(13, 127, 127, 0.34);
    background: rgba(13, 127, 127, 0.14);
    color: #0c7676;
}

.dialog-section-title {
    margin: 0 0 8px;
    color: #17304f;
    font-size: 0.82rem;
    font-weight: 900;
    letter-spacing: 0.07em;
    text-transform: uppercase;
}

.dialog-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
}

.field-full {
    grid-column: 1 / -1;
}

.field {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.field span {
    color: rgba(23, 48, 79, 0.72);
    font-size: 0.76rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    font-weight: 800;
}

.field input,
.field textarea,
.field select {
    border: 1px solid rgba(23, 48, 79, 0.2);
    border-radius: 12px;
    background: #f8fbff;
    color: #17304f;
    padding: 10px 12px;
    outline: none;
}

.field textarea {
    resize: vertical;
    min-height: 88px;
}

.block-action-row {
    margin-top: 2px;
}

.block-next-button {
    min-width: 240px;
    justify-content: center;
}

.block-next-button:disabled {
    opacity: 0.55;
    cursor: not-allowed;
    filter: saturate(0.7);
}

.dialog-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    padding: 14px 20px 18px;
    background: #ffffff;
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
    min-width: 1320px;
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
    font-weight: 800;
}

td strong,
td small {
    display: block;
}

td strong {
    margin: 0;
    color: #17304f;
    font-size: 0.86rem;
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

.doc-number {
    color: #17304f;
    font-weight: 800;
    font-size: 0.82rem;
}

.money-cell {
    color: #0e6a6a;
}

.web-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 8px;
    border-radius: 999px;
    background: rgba(45, 106, 159, 0.12);
    border: 1px solid rgba(45, 106, 159, 0.26);
    color: #17304f;
    text-decoration: none;
    font-size: 0.74rem;
    font-weight: 700;
    max-width: 220px;
}

.web-link i {
    font-size: 14px;
}

.web-link span {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.web-link--empty {
    background: rgba(23, 48, 79, 0.08);
    border-color: rgba(23, 48, 79, 0.16);
    color: rgba(23, 48, 79, 0.7);
}

.days-credit-chip {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 94px;
    border-radius: 999px;
    padding: 6px 10px;
    font-size: 0.76rem;
    font-weight: 800;
}

.days-credit-chip--none {
    background: rgba(23, 48, 79, 0.1);
    color: rgba(23, 48, 79, 0.78);
}

.days-credit-chip--short {
    background: rgba(109, 211, 160, 0.18);
    color: #186843;
}

.days-credit-chip--long {
    background: rgba(244, 183, 64, 0.22);
    color: #946103;
}

.row-actions {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
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

.action-enable {
    background: #16a34a;
    color: #ffffff;
}

.action-disable {
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
    border-radius: 8px;
    padding: 4px 8px;
    font-size: 0.72rem;
    transition: all 120ms ease;
}

.action-button:hover .button-tooltip {
    opacity: 1;
    visibility: visible;
}

.empty-row {
    text-align: center;
    color: rgba(23, 48, 79, 0.72);
    font-weight: 700;
    padding: 18px 12px;
}

@media (max-width: 1024px) {
    .kpi-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .hero-card {
        flex-direction: column;
    }
}

@media (max-width: 768px) {
    .kpi-grid {
        grid-template-columns: 1fr;
    }

    .toolbar-card {
        flex-direction: column;
        align-items: stretch;
    }

    .search-field {
        width: 100%;
    }

    .submit-button {
        width: 100%;
    }

    .dialog-layout {
        grid-template-columns: 1fr;
    }

    .dialog-topbar {
        flex-direction: column;
        align-items: flex-start;
    }

    .dialog-grid {
        grid-template-columns: 1fr;
    }

    .dialog-actions {
        flex-direction: column-reverse;
    }

    .secondary-button,
    .dialog-actions .submit-button {
        width: 100%;
    }

    table {
        min-width: 1160px;
    }

}
</style>

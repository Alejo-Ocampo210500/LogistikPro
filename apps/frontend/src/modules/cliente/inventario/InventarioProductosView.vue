<template>
    <section class="products-shell">
        <article class="hero-card">
            <div>
                <span class="hero-kicker">Inventario</span>
                <h2>Listado de productos</h2>
                <p>Administra catalogo, precios y existencias en una sola vista operativa.</p>
            </div>
        </article>

        <article class="toolbar-card">
            <label class="search-field" for="search-productos">
                <i class="mdi mdi-magnify"></i>
                <input
                    id="search-productos"
                    v-model.trim="searchQuery"
                    type="text"
                    placeholder="Buscar por nombre, codigo o codigo de barras"
                />
            </label>

            <label class="status-field" for="status-productos">
                <i class="mdi mdi-tune-variant"></i>
                <select id="status-productos" v-model="statusFilter" aria-label="Filtrar productos por estado">
                    <option value="todos">Todos</option>
                    <option value="activos">Activos</option>
                    <option value="inactivos">Inactivos</option>
                </select>
            </label>

            <button type="button" class="submit-button" @click="abrirModalCrear">
                <i class="mdi mdi-plus"></i>
                <span>Crear producto</span>
            </button>
        </article>

        <section class="kpi-grid" aria-label="Metricas de productos">
            <article v-for="card in metricCards" :key="card.key" :class="['kpi-card', card.cardClass]">
                <div class="kpi-head">
                    <span>{{ card.label }}</span>
                    <i :class="card.icon"></i>
                </div>
                <strong class="kpi-value">{{ card.value }}</strong>
                <small class="kpi-note">{{ card.note }}</small>
            </article>
        </section>

        <article class="table-card">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Codigo</th>
                            <th>Codigo barras</th>
                            <th>Imagen</th>
                            <th>Producto</th>
                            <th>Categoria</th>
                            <th>Marca</th>
                            <th>Costo</th>
                            <th>Precio venta</th>
                            <th>Stock</th>
                            <th>Stock minimo</th>
                            <th>Stock maximo</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in productosFiltrados" :key="item.id">
                            <td>
                                <span class="code-pill">{{ item.codigo || 'N/A' }}</span>
                            </td>
                            <td>
                                <span class="barcode-pill">{{ item.codigo_barras || 'N/A' }}</span>
                            </td>
                            <td>
                                <div class="thumb-cell">
                                    <img
                                        v-if="resolverImagenProducto(item)"
                                        :src="resolverImagenProducto(item)"
                                        :alt="item.nombre || 'Imagen producto'"
                                        class="product-thumb"
                                        loading="lazy"
                                    />
                                    <div v-else class="product-thumb product-thumb--fallback">
                                        <i class="mdi mdi-image-off-outline"></i>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <strong>{{ item.nombre }}</strong>
                                <small>{{ item.descripcion || 'Sin descripcion' }}</small>
                            </td>
                            <td>
                                <span class="category-pill">{{ resolverNombreCategoria(item.categoria_id) }}</span>
                            </td>
                            <td>
                                <span class="brand-pill">{{ resolverNombreMarca(item.marca_id) }}</span>
                            </td>
                            <td>
                                <span class="money-pill money-pill-cost">{{ formatearMoneda(item.costo) }}</span>
                            </td>
                            <td>
                                <span class="money-pill money-pill-sale">{{ formatearMoneda(item.precio_venta) }}</span>
                            </td>
                            <td>
                                <div class="stock-cell">
                                    <strong>{{ item.stock }}</strong>
                                    <span :class="['stock-pill', resolverClaseStock(item)]">{{ resolverEtiquetaStock(item) }}</span>
                                </div>
                            </td>
                            <td>{{ item.stock_minimo }}</td>
                            <td>{{ item.stock_maximo }}</td>
                            <td>
                                <span :class="['status-pill', estaActivo(item) ? 'status-ok' : 'status-off']">
                                    {{ estaActivo(item) ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td>
                                <div class="row-actions">
                                    <button type="button" class="action-button action-edit"
                                        @click="abrirModalEditar(item)">
                                        <i class="mdi mdi-pencil"></i>
                                        <span class="button-tooltip">Editar</span>
                                    </button>

                                    <button type="button" class="action-button action-view" @click="verDetalle(item)">
                                        <i class="mdi mdi-eye-outline"></i>
                                        <span class="button-tooltip">Ver detalles</span>
                                    </button>

                                    <button type="button"
                                        :class="['action-button', estaActivo(item) ? 'action-disable' : 'action-enable']"
                                        @click="solicitarCambioEstado(item)">
                                        <i
                                            :class="estaActivo(item) ? 'mdi mdi-close-circle-outline' : 'mdi mdi-check-circle-outline'"></i>
                                        <span class="button-tooltip">
                                            {{ estaActivo(item) ? 'Inactivar' : 'Activar' }}
                                        </span>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="!productosFiltrados.length">
                            <td colspan="13" class="empty-row">No hay productos para mostrar con los filtros seleccionados.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </article>

        <ProductoCreateDialog
            v-model="productoDialog"
            :edit-mode="editMode"
            :producto="productoForm"
            :categorias="categorias"
            :marcas="marcas"
            :unidades="unidades"
            :impuestos="impuestos"
            :impuestos-message="impuestosMessage"
            :estados="estados"
            :validation-message="validationMessage"
            @close="cerrarModal"
            @save="guardarProducto"
        />

        <v-dialog v-model="confirmDialog" max-width="520px" persistent>
            <v-card class="dialog-card">
                <v-card-title class="dialog-card-title">
                    <v-avatar size="46" class="dialog-avatar dialog-avatar-alert">
                        <v-icon large>mdi-alert-circle-outline</v-icon>
                    </v-avatar>
                    <div>
                        <span class="dialog-kicker">Confirmar accion</span>
                        <h3 class="dialog-title">{{ pendingAction === 'activar' ? 'Activar producto' : 'Inactivar producto' }}</h3>
                    </div>
                </v-card-title>

                <v-divider />

                <v-card-text class="dialog-card-body">
                    <p>
                        {{ pendingAction === 'activar' ? 'Deseas activar el producto' : 'Deseas inactivar el producto' }}
                        <strong>"{{ pendingProducto ? pendingProducto.nombre : '' }}"</strong>?
                    </p>
                </v-card-text>

                <v-divider />

                <v-card-actions class="dialog-actions">
                    <button type="button" class="secondary-button" @click="cerrarDialogoConfirmacion">Cancelar</button>
                    <button type="button" class="submit-button" data-action-loader="true" @click="confirmarCambioEstado">Aceptar</button>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <v-dialog v-model="detalleDialog" max-width="980px" persistent>
            <v-card class="dialog-card detail-dialog-card">
                <v-card-title class="dialog-card-title detail-header">
                    <v-avatar size="48" class="dialog-avatar detail-avatar">
                        <v-icon large>mdi-package-variant-closed</v-icon>
                    </v-avatar>

                    <div class="detail-header-copy">
                        <span class="dialog-kicker">Ficha de producto</span>
                        <h3 class="dialog-title">{{ detalleProducto.nombre || 'Producto sin nombre' }}</h3>
                        <p class="detail-subtitle">
                            {{ resolverNombreCategoria(detalleProducto.categoria_id) }}
                            ·
                            {{ resolverNombreMarca(detalleProducto.marca_id) }}
                        </p>
                    </div>

                    <span :class="['status-pill', estaActivo(detalleProducto) ? 'status-ok' : 'status-off']">
                        {{ estaActivo(detalleProducto) ? 'Activo' : 'Inactivo' }}
                    </span>
                </v-card-title>

                <v-divider />

                <v-card-text class="dialog-card-body detail-body">
                    <section class="detail-image-panel" aria-label="Imagen del producto">
                        <div class="detail-image-wrap">
                            <img
                                v-if="resolverImagenProducto(detalleProducto)"
                                :src="resolverImagenProducto(detalleProducto)"
                                :alt="detalleProducto.nombre || 'Imagen del producto'"
                                class="detail-image"
                                loading="lazy"
                            />
                            <div v-else class="detail-image detail-image--fallback">
                                <i class="mdi mdi-image-off-outline"></i>
                                <span>Producto sin imagen</span>
                            </div>
                        </div>
                    </section>

                    <section class="detail-kpis" aria-label="Resumen del producto">
                        <article class="detail-kpi detail-kpi-stock">
                            <span>Stock actual</span>
                            <strong>{{ Number(detalleProducto.stock) || 0 }}</strong>
                            <small :class="['stock-pill', resolverClaseStock(detalleProducto)]">
                                {{ resolverEtiquetaStock(detalleProducto) }}
                            </small>
                        </article>

                        <article class="detail-kpi detail-kpi-cost">
                            <span>Costo</span>
                            <strong>{{ formatearMoneda(detalleProducto.costo) }}</strong>
                            <small>Valor unitario de compra</small>
                        </article>

                        <article class="detail-kpi detail-kpi-sale">
                            <span>Precio de venta</span>
                            <strong>{{ formatearMoneda(detalleProducto.precio_venta) }}</strong>
                            <small>Valor unitario de comercializacion</small>
                        </article>

                        <article class="detail-kpi detail-kpi-capital">
                            <span>Capital en stock</span>
                            <strong>{{ formatearMoneda((Number(detalleProducto.stock) || 0) * (Number(detalleProducto.costo) || 0)) }}</strong>
                            <small>Stock x costo</small>
                        </article>
                    </section>

                    <section class="detail-grid" aria-label="Detalles del producto">
                        <article class="detail-panel detail-panel--id">
                            <h4><span class="detail-section-tag detail-section-tag--id">Identificacion</span></h4>
                            <ul>
                                <li><span>Codigo interno</span><strong>{{ detalleProducto.codigo || 'N/A' }}</strong></li>
                                <li><span>Codigo de barras</span><strong>{{ detalleProducto.codigo_barras || 'N/A' }}</strong></li>
                                <li><span>Unidad de medida</span><strong>{{ resolverNombreUnidad(detalleProducto.unidad_medida_id) }}</strong></li>
                                <li><span>Impuesto</span><strong>{{ resolverNombreImpuesto(detalleProducto.impuesto_id) }}</strong></li>
                            </ul>
                        </article>

                        <article class="detail-panel detail-panel--inventory">
                            <h4><span class="detail-section-tag detail-section-tag--inventory">Inventario</span></h4>
                            <ul>
                                <li><span>Stock minimo</span><strong>{{ Number(detalleProducto.stock_minimo) || 0 }}</strong></li>
                                <li><span>Stock maximo</span><strong>{{ detalleProducto.stock_maximo === null || detalleProducto.stock_maximo === '' ? 'N/A' : Number(detalleProducto.stock_maximo) }}</strong></li>
                                <li><span>Maneja inventario</span><strong>{{ detalleProducto.maneja_inventario ? 'Si' : 'No' }}</strong></li>
                                <li><span>Es servicio</span><strong>{{ detalleProducto.es_servicio ? 'Si' : 'No' }}</strong></li>
                            </ul>
                        </article>

                        <article class="detail-panel detail-panel--commercial">
                            <h4><span class="detail-section-tag detail-section-tag--commercial">Comercial</span></h4>
                            <ul>
                                <li><span>Permite descuento</span><strong>{{ detalleProducto.permite_descuento ? 'Si' : 'No' }}</strong></li>
                                <li><span>Venta libre</span><strong>{{ detalleProducto.venta_libre ? 'Si' : 'No' }}</strong></li>
                                <li><span>Categoria</span><strong>{{ resolverNombreCategoria(detalleProducto.categoria_id) }}</strong></li>
                                <li><span>Marca</span><strong>{{ resolverNombreMarca(detalleProducto.marca_id) }}</strong></li>
                            </ul>
                        </article>
                    </section>

                    <section class="detail-description" aria-label="Descripcion del producto">
                        <h4>Descripcion</h4>
                        <p>{{ detalleProducto.descripcion || 'Este producto no tiene descripcion registrada.' }}</p>
                    </section>
                </v-card-text>

                <v-divider />

                <v-card-actions class="dialog-actions">
                    <button type="button" class="secondary-button" @click="cerrarDetalle">
                        Cerrar
                    </button>
                    <button type="button" class="submit-button" @click="abrirModalEditar(detalleProducto)">
                        Editar producto
                    </button>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </section>
</template>

<script>
import api from '@/services/api';
import ProductoCreateDialog from '@/components/cliente/inventario/ProductoCreateDialog.vue';

export default {
    name: 'InventarioProductosView',

    components: {
        ProductoCreateDialog,
    },

    props: {
        session: {
            type: Object,
            default: null,
        },
    },

    data() {
        return {
            productoDialog: false,
            editMode: false,
            editingId: null,
            confirmDialog: false,
            detalleDialog: false,
            pendingProducto: null,
            pendingAction: '',
            validationMessage: '',
            searchQuery: '',
            statusFilter: 'todos',
            estados: [
                { id: 1, nombre: 'Activo' },
                { id: 2, nombre: 'Inactivo' },
            ],
            categorias: [],
            marcas: [],
            unidades: [],
            impuestos: [],
            impuestosMessage: '',
            productos: [],
            productoForm: {},
            detalleProducto: {},
        };
    },

    mounted() {
        this.inicializarVista();
    },

    computed: {
        totalProductos() {
            return this.productos.length;
        },

        totalStockBajo() {
            return this.productos.filter(item => Number(item.stock) > 0 && Number(item.stock) <= Number(item.stock_minimo)).length;
        },

        totalAgotados() {
            return this.productos.filter(item => Number(item.stock) <= 0).length;
        },

        valorInventario() {
            return this.productos.reduce((acc, item) => {
                const stock = Number(item.stock) || 0;
                const costo = Number(item.costo) || 0;
                return acc + stock * costo;
            }, 0);
        },

        metricCards() {
            return [
                {
                    key: 'total',
                    label: 'Total de productos',
                    value: this.totalProductos,
                    note: 'Catalogo registrado',
                    icon: 'mdi mdi-cube-outline',
                    cardClass: 'kpi-card--total',
                },
                {
                    key: 'stock-bajo',
                    label: 'Productos con stock bajo',
                    value: this.totalStockBajo,
                    note: 'Requieren reposicion',
                    icon: 'mdi mdi-alert-outline',
                    cardClass: 'kpi-card--low',
                },
                {
                    key: 'agotados',
                    label: 'Productos agotados',
                    value: this.totalAgotados,
                    note: 'Sin unidades disponibles',
                    icon: 'mdi mdi-close-octagon-outline',
                    cardClass: 'kpi-card--out',
                },
                {
                    key: 'valor',
                    label: 'Valor del inventario (costo)',
                    value: this.formatearMoneda(this.valorInventario),
                    note: 'Capital comprometido',
                    icon: 'mdi mdi-cash-multiple',
                    cardClass: 'kpi-card--value',
                },
            ];
        },

        productosFiltrados() {
            const query = this.searchQuery.toLowerCase();

            return this.productos.filter(item => {
                const cumpleBusqueda =
                    !query
                    || (item.nombre || '').toLowerCase().includes(query)
                    || (item.codigo || '').toLowerCase().includes(query)
                    || (item.codigo_barras || '').toLowerCase().includes(query);

                const activo = this.estaActivo(item);
                const cumpleEstado =
                    this.statusFilter === 'todos'
                    || (this.statusFilter === 'activos' && activo)
                    || (this.statusFilter === 'inactivos' && !activo);

                return cumpleBusqueda && cumpleEstado;
            });
        },
    },

    methods: {
        async inicializarVista() {
            await Promise.all([
                this.cargarCatalogos(),
                this.listarProductos(),
            ]);
        },

        extraerListaCatalogo(payload, keys = []) {
            if (Array.isArray(payload)) {
                return payload;
            }

            for (const key of keys) {
                if (Array.isArray(payload?.[key])) {
                    return payload[key];
                }
            }

            if (Array.isArray(payload?.data)) {
                return payload.data;
            }

            return [];
        },

        normalizarImpuestos(payload) {
            const lista = this.extraerListaCatalogo(payload, ['impuestos', 'impuesto']);

            return lista
                .map(item => ({
                    ...item,
                    id: item.id ?? item.impuesto_id ?? null,
                    nombre: item.nombre ?? item.nombre_impuesto ?? item.descripcion ?? item.codigo ?? 'Sin nombre',
                }))
                .filter(item => Number(item.id) > 0);
        },

        extraerListaProductos(payload) {
            if (Array.isArray(payload)) {
                return payload;
            }

            if (Array.isArray(payload?.productos)) {
                return payload.productos;
            }

            if (Array.isArray(payload?.data)) {
                return payload.data;
            }

            return [];
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

        async cargarCatalogos() {
            await Promise.all([
                this.listarCategorias(),
                this.listarMarcas(),
                this.listarUnidades(),
                this.listarImpuestos(),
            ]);
        },

        async listarCategorias() {
            try {
                const { data } = await api.get('/categorias');
                this.categorias = this.extraerListaCatalogo(data, ['categorias']);
            } catch (error) {
                this.categorias = [];
            }
        },

        async listarMarcas() {
            try {
                const { data } = await api.get('/marcas');
                this.marcas = this.extraerListaCatalogo(data, ['marcas']);
            } catch (error) {
                this.marcas = [];
            }
        },

        extraerColeccionUnidades(data) {
            if (Array.isArray(data?.unidades)) {
                return data.unidades;
            }

            if (Array.isArray(data?.unidades_medida)) {
                return data.unidades_medida;
            }

            if (Array.isArray(data?.data)) {
                return data.data;
            }

            return [];
        },

        async listarUnidades() {
            try {
                const { data } = await api.get('/unidades-medida');
                this.unidades = this.extraerColeccionUnidades(data);
            } catch (error) {
                this.unidades = [];
            }
        },

        async listarImpuestos() {
            try {
                const { data } = await api.get('/impuestos');
                this.impuestos = this.normalizarImpuestos(data);

                if (!this.impuestos.length) {
                    this.impuestosMessage = 'No hay impuestos disponibles para esta empresa.';
                    return;
                }

                this.impuestosMessage = '';
            } catch (error) {
                this.impuestos = [];
                this.impuestosMessage = this.resolverError(error);
            }
        },

        async listarProductos() {
            try {
                const { data } = await api.get('/productos');
                this.productos = this.extraerListaProductos(data);
            } catch (error) {
                this.productos = [];
            }
        },

        esperarTresSegundos() {
            return new Promise(resolve => {
                window.setTimeout(resolve, 3000);
            });
        },

        formatearMoneda(valor) {
            return new Intl.NumberFormat('es-CO', {
                style: 'currency',
                currency: 'COP',
                maximumFractionDigits: 0,
            }).format(Number(valor) || 0);
        },

        estaActivo(item) {
            return Number(item.estado_id) === 1;
        },

        resolverEtiquetaStock(item) {
            const stock = Number(item.stock) || 0;
            const minimo = Number(item.stock_minimo) || 0;

            if (stock <= 0) {
                return 'Agotado';
            }

            if (stock <= minimo) {
                return 'Stock bajo';
            }

            return 'Stock ok';
        },

        resolverClaseStock(item) {
            const stock = Number(item.stock) || 0;
            const minimo = Number(item.stock_minimo) || 0;

            if (stock <= 0) {
                return 'stock-out';
            }

            if (stock <= minimo) {
                return 'stock-low';
            }

            return 'stock-ok';
        },

        resolverNombreCategoria(categoriaId) {
            const categoria = this.categorias.find(item => Number(item.id) === Number(categoriaId));
            return categoria ? categoria.nombre : 'Sin categoria';
        },

        resolverNombreMarca(marcaId) {
            const marca = this.marcas.find(item => Number(item.id) === Number(marcaId));
            return marca ? marca.nombre : 'Sin marca';
        },

        resolverNombreUnidad(unidadId) {
            const unidad = this.unidades.find(item => Number(item.id) === Number(unidadId));
            return unidad ? unidad.nombre : 'Sin unidad';
        },

        resolverNombreImpuesto(impuestoId) {
            if (!impuestoId) {
                return 'Sin impuesto';
            }

            const impuesto = this.impuestos.find(item => Number(item.id) === Number(impuestoId));
            return impuesto ? impuesto.nombre : 'Sin impuesto';
        },

        resolverImagenProducto(item) {
            return item.imagen_url || item.imagen || '';
        },

        crearBaseProducto() {
            return {
                codigo: '',
                codigo_barras: '',
                nombre: '',
                descripcion: '',
                categoria_id: null,
                marca_id: null,
                unidad_medida_id: null,
                impuesto_id: null,
                costo: 0,
                precio_venta: 0,
                stock: 0,
                stock_minimo: 0,
                stock_maximo: 0,
                maneja_inventario: true,
                permite_descuento: false,
                es_servicio: false,
                venta_libre: false,
                estado_id: 1,
                imagen: null,
                imagen_url: '',
            };
        },

        abrirModalCrear() {
            this.validationMessage = '';
            this.editMode = false;
            this.editingId = null;
            this.productoForm = this.crearBaseProducto();
            this.productoDialog = true;
        },

        abrirModalEditar(item) {
            this.validationMessage = '';
            this.detalleDialog = false;
            this.editMode = true;
            this.editingId = item.id;
            this.productoForm = {
                ...this.crearBaseProducto(),
                ...item,
            };
            this.productoDialog = true;
        },

        cerrarModal() {
            this.productoDialog = false;
            this.validationMessage = '';
        },

        verDetalle(item) {
            this.detalleProducto = {
                ...this.crearBaseProducto(),
                ...item,
            };
            this.detalleDialog = true;
        },

        cerrarDetalle() {
            this.detalleDialog = false;
            this.detalleProducto = {};
        },

        async guardarProducto(payload) {
            if (!payload.nombre) {
                this.validationMessage = 'El nombre del producto es obligatorio.';
                return;
            }

            if (!payload.codigo) {
                this.validationMessage = 'El codigo interno del producto es obligatorio.';
                return;
            }

            if (Number(payload.precio_venta) < Number(payload.costo)) {
                this.validationMessage = 'El precio de venta no puede ser menor al costo.';
                return;
            }

            const actionLabel = this.editMode ? 'Guardando cambios de producto...' : 'Creando producto...';
            this.$emit('start-action', actionLabel, null, null);

            try {
                await this.esperarTresSegundos();

                const requestPayload = new FormData();
                requestPayload.append('codigo', payload.codigo || '');
                requestPayload.append('codigo_barras', payload.codigo_barras || '');
                requestPayload.append('nombre', payload.nombre || '');
                requestPayload.append('descripcion', payload.descripcion || '');
                requestPayload.append('categoria_id', String(payload.categoria_id || ''));
                requestPayload.append('marca_id', payload.marca_id ? String(payload.marca_id) : '');
                requestPayload.append('unidad_medida_id', String(payload.unidad_medida_id || ''));
                requestPayload.append('impuesto_id', payload.impuesto_id ? String(payload.impuesto_id) : '');
                requestPayload.append('costo', String(Number(payload.costo) || 0));
                requestPayload.append('precio_venta', String(Number(payload.precio_venta) || 0));
                requestPayload.append('stock', String(Number(payload.stock) || 0));
                requestPayload.append('stock_minimo', String(Number(payload.stock_minimo) || 0));
                requestPayload.append(
                    'stock_maximo',
                    payload.stock_maximo === '' || payload.stock_maximo === null
                        ? ''
                        : String(Number(payload.stock_maximo) || 0)
                );
                requestPayload.append('maneja_inventario', payload.maneja_inventario ? '1' : '0');
                requestPayload.append('permite_descuento', payload.permite_descuento ? '1' : '0');
                requestPayload.append('es_servicio', payload.es_servicio ? '1' : '0');
                requestPayload.append('venta_libre', payload.venta_libre ? '1' : '0');
                requestPayload.append('estado_id', String(payload.estado_id || 1));

                if (payload.imagen instanceof File) {
                    requestPayload.append('imagen', payload.imagen);
                }

                if (this.editMode && this.editingId) {
                    requestPayload.append('_method', 'PUT');
                    await api.post(`/productos/${this.editingId}/actualizar`, requestPayload, {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                        },
                    });
                    await this.listarProductos();
                } else {
                    await api.post('/productos/crear', requestPayload, {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                        },
                    });
                    await this.listarProductos();
                }

                this.cerrarModal();
            } catch (error) {
                this.validationMessage = this.resolverError(error);
            } finally {
                this.$emit('stop-action');
            }
        },

        solicitarCambioEstado(item) {
            this.pendingProducto = item;
            this.pendingAction = this.estaActivo(item) ? 'inactivar' : 'activar';
            this.confirmDialog = true;
        },

        cerrarDialogoConfirmacion() {
            this.confirmDialog = false;
            this.pendingProducto = null;
            this.pendingAction = '';
        },

        async confirmarCambioEstado() {
            if (!this.pendingProducto) {
                this.cerrarDialogoConfirmacion();
                return;
            }

            const productoId = this.pendingProducto.id;
            const actionLabel = `${this.pendingAction === 'activar' ? 'Activando' : 'Inactivando'} producto...`;

            this.confirmDialog = false;
            this.$emit('start-action', actionLabel, null, null);

            try {
                await this.esperarTresSegundos();
                await api.post(`/productos/${productoId}/cambiarEstado`);
                await this.listarProductos();
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

.action-view {
    background: linear-gradient(135deg, #4f8cff, #2563eb);
    color: #fff;
}

.action-view:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 18px rgba(37, 99, 235, 0.35);
}
.products-shell {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.hero-card {
    padding: 22px 24px;
    border-radius: 24px;
    background: linear-gradient(135deg, #f3f7ff 0%, #fff9ea 100%);
    border: 1px solid rgba(23, 48, 79, 0.08);
    box-shadow: 0 20px 48px rgba(14, 28, 54, 0.07);
}

.hero-kicker {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-transform: uppercase;
    letter-spacing: 0.14em;
    font-size: 0.72rem;
    font-weight: 700;
    color: rgba(23, 48, 79, 0.6);
}

h2 {
    margin: 8px 0 6px;
    font-size: 1.42rem;
    color: #17304f;
}

.hero-card p {
    margin: 0;
    color: rgba(23, 48, 79, 0.68);
    line-height: 1.5;
}

.toolbar-card {
    padding: 16px;
    border-radius: 20px;
    border: 1px solid rgba(23, 48, 79, 0.08);
    background: rgba(255, 255, 255, 0.96);
    display: flex;
    gap: 12px;
    align-items: center;
}

.search-field {
    flex: 1;
    min-width: 280px;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    border: 1px solid rgba(23, 48, 79, 0.16);
    background: #ffffff;
    border-radius: 12px;
    height: 46px;
    padding: 0 12px;
}

.search-field i {
    color: rgba(23, 48, 79, 0.56);
    font-size: 1.1rem;
}

.search-field input {
    border: 0;
    outline: 0;
    width: 100%;
    color: #17304f;
}

.status-field {
    min-width: 180px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: 1px solid rgba(23, 48, 79, 0.16);
    border-radius: 12px;
    height: 46px;
    padding: 0 12px;
    background: #ffffff;
}

.status-field select {
    border: 0;
    width: 100%;
    height: 100%;
    background: transparent;
    color: #17304f;
    padding: 0;
    outline: 0;
}

.status-field i {
    color: rgba(23, 48, 79, 0.56);
    font-size: 1rem;
}

.kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 12px;
}

.kpi-card {
    border-radius: 18px;
    border: 1px solid transparent;
    background: #ffffff;
    box-shadow: 0 16px 34px rgba(14, 28, 54, 0.08);
    padding: 14px 16px;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.kpi-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 20px 38px rgba(14, 28, 54, 0.12);
}

.kpi-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.kpi-head span {
    color: rgba(23, 48, 79, 0.7);
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    font-weight: 800;
}

.kpi-head i {
    font-size: 1.2rem;
}

.kpi-value {
    display: block;
    color: #17304f;
    font-size: 1.35rem;
    line-height: 1.2;
}

.kpi-note {
    display: block;
    margin-top: 4px;
    color: rgba(23, 48, 79, 0.58);
    font-size: 0.78rem;
}

.kpi-card--total {
    border-color: rgba(47, 104, 223, 0.22);
    background: linear-gradient(135deg, rgba(232, 241, 255, 0.85) 0%, rgba(255, 255, 255, 0.98) 100%);
}

.kpi-card--total .kpi-head i {
    color: #2f68df;
}

.kpi-card--low {
    border-color: rgba(245, 158, 11, 0.22);
    background: linear-gradient(135deg, rgba(255, 244, 217, 0.85) 0%, rgba(255, 255, 255, 0.98) 100%);
}

.kpi-card--low .kpi-head i {
    color: #b7791f;
}

.kpi-card--out {
    border-color: rgba(220, 38, 38, 0.22);
    background: linear-gradient(135deg, rgba(255, 231, 231, 0.88) 0%, rgba(255, 255, 255, 0.98) 100%);
}

.kpi-card--out .kpi-head i {
    color: #c53030;
}

.kpi-card--value {
    border-color: rgba(22, 163, 74, 0.22);
    background: linear-gradient(135deg, rgba(229, 252, 237, 0.88) 0%, rgba(255, 255, 255, 0.98) 100%);
}

.kpi-card--value .kpi-head i {
    color: #1f8a4d;
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
    min-width: 1360px;
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

.code-pill,
.barcode-pill {
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
    padding: 5px 10px;
    font-size: 0.76rem;
    font-weight: 800;
    letter-spacing: 0.02em;
}

.code-pill {
    background: rgba(23, 48, 79, 0.08);
    color: #17304f;
}

.barcode-pill {
    background: rgba(23, 48, 79, 0.05);
    color: rgba(23, 48, 79, 0.78);
}

.category-pill,
.brand-pill {
    display: inline-flex;
    align-items: center;
    border-radius: 10px;
    padding: 6px 10px;
    font-size: 0.78rem;
    font-weight: 700;
}

.category-pill {
    background: rgba(47, 104, 223, 0.12);
    color: #244eac;
}

.brand-pill {
    background: rgba(14, 116, 144, 0.12);
    color: #0b6077;
}

.money-pill {
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
    padding: 6px 10px;
    font-size: 0.78rem;
    font-weight: 800;
}

.money-pill-cost {
    background: rgba(156, 163, 175, 0.18);
    color: #4b5563;
}

.money-pill-sale {
    background: rgba(22, 163, 74, 0.16);
    color: #186843;
}

.thumb-cell {
    width: 48px;
}

.product-thumb {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    object-fit: cover;
    border: 1px solid rgba(23, 48, 79, 0.12);
    display: block;
}

.product-thumb--fallback {
    display: grid;
    place-items: center;
    background: linear-gradient(135deg, #ecf3fc 0%, #fff3d8 100%);
    color: rgba(23, 48, 79, 0.6);
}

.stock-cell {
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.stock-pill {
    border-radius: 999px;
    padding: 5px 10px;
    font-size: 0.72rem;
    font-weight: 800;
}

.stock-ok {
    background: rgba(109, 211, 160, 0.18);
    color: #186843;
}

.stock-low {
    background: rgba(255, 188, 92, 0.22);
    color: #9b6500;
}

.stock-out {
    background: rgba(255, 123, 123, 0.16);
    color: #9b2f2f;
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
    flex-wrap: nowrap;
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
    cursor: pointer;
    transition: all .2s ease;
}

/* Editar */
.action-edit {
    background: linear-gradient(135deg, #f4b740 0%, #ffd277 45%, #d99210 100%);
    color: #0b1530;
}

/* Ver */
.action-view {
    background: linear-gradient(135deg, #4f8cff 0%, #2563eb 100%);
    color: #fff;
}

/* Activar */
.action-enable {
    background: linear-gradient(135deg, #34d399 0%, #16a34a 100%);
    color: #fff;
}

/* Inactivar */
.action-disable {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: #fff;
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

.dialog-card-body {
    padding: 20px 24px;
}

.dialog-actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    padding: 14px 24px 20px;
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

.detail-avatar {
    background: linear-gradient(135deg, #2563eb 0%, #0ea5e9 55%, #22d3ee 100%);
    color: #ffffff;
}

.detail-subtitle {
    margin: 8px 0 0;
    color: rgba(23, 48, 79, 0.64);
    font-weight: 600;
}

.detail-body {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.detail-image-panel {
    border-radius: 16px;
    border: 1px solid rgba(23, 48, 79, 0.1);
    background: rgba(255, 255, 255, 0.96);
    padding: 12px;
}

.detail-image-wrap {
    max-width: 260px;
}

.detail-image {
    width: 100%;
    height: 180px;
    object-fit: cover;
    border-radius: 12px;
    border: 1px solid rgba(23, 48, 79, 0.12);
    display: block;
}

.detail-image--fallback {
    background: linear-gradient(135deg, #ecf3fc 0%, #fff3d8 100%);
    color: rgba(23, 48, 79, 0.64);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 6px;
    font-size: 0.84rem;
    font-weight: 700;
}

.detail-image--fallback i {
    font-size: 1.5rem;
}

.detail-kpis {
    display: grid;
    gap: 12px;
    grid-template-columns: repeat(4, minmax(0, 1fr));
}

.detail-kpi {
    border-radius: 16px;
    border: 1px solid rgba(23, 48, 79, 0.12);
    background: #ffffff;
    padding: 12px;
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.detail-kpi span {
    color: rgba(23, 48, 79, 0.62);
    font-size: 0.74rem;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    font-weight: 700;
}

.detail-kpi strong {
    color: #17304f;
    font-size: 1.12rem;
}

.detail-kpi small {
    color: rgba(23, 48, 79, 0.6);
    font-size: 0.74rem;
}

.detail-kpi-stock {
    background: linear-gradient(135deg, rgba(232, 241, 255, 0.92) 0%, rgba(255, 255, 255, 0.96) 100%);
}

.detail-kpi-cost {
    background: linear-gradient(135deg, rgba(245, 245, 245, 0.95) 0%, rgba(255, 255, 255, 0.98) 100%);
}

.detail-kpi-sale {
    background: linear-gradient(135deg, rgba(229, 252, 237, 0.92) 0%, rgba(255, 255, 255, 0.98) 100%);
}

.detail-kpi-capital {
    background: linear-gradient(135deg, rgba(255, 244, 217, 0.95) 0%, rgba(255, 255, 255, 0.98) 100%);
}

.detail-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 12px;
}

.detail-panel {
    border-radius: 16px;
    border: 1px solid rgba(23, 48, 79, 0.1);
    background: rgba(255, 255, 255, 0.96);
    padding: 14px;
}

.detail-panel--id {
    border-color: rgba(37, 99, 235, 0.2);
    background: linear-gradient(155deg, rgba(234, 243, 255, 0.9) 0%, rgba(255, 255, 255, 0.98) 55%);
}

.detail-panel--inventory {
    border-color: rgba(245, 158, 11, 0.22);
    background: linear-gradient(155deg, rgba(255, 245, 226, 0.9) 0%, rgba(255, 255, 255, 0.98) 55%);
}

.detail-panel--commercial {
    border-color: rgba(22, 163, 74, 0.2);
    background: linear-gradient(155deg, rgba(233, 251, 240, 0.9) 0%, rgba(255, 255, 255, 0.98) 55%);
}

.detail-panel h4,
.detail-description h4 {
    margin: 0 0 10px;
    font-size: 0.88rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: rgba(23, 48, 79, 0.7);
}

.detail-section-tag {
    display: inline-flex;
    align-items: center;
    padding: 5px 10px;
    border-radius: 999px;
    font-size: 0.72rem;
    letter-spacing: 0.1em;
    font-weight: 800;
}

.detail-section-tag--id {
    background: rgba(37, 99, 235, 0.16);
    color: #1d4ed8;
}

.detail-section-tag--inventory {
    background: rgba(245, 158, 11, 0.2);
    color: #b45309;
}

.detail-section-tag--commercial {
    background: rgba(22, 163, 74, 0.18);
    color: #15803d;
}

.detail-panel ul {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.detail-panel li {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    border-bottom: 1px dashed rgba(23, 48, 79, 0.14);
    padding-bottom: 8px;
}

.detail-panel li:last-child {
    border-bottom: 0;
    padding-bottom: 0;
}

.detail-panel li span {
    color: rgba(23, 48, 79, 0.64);
    font-size: 0.8rem;
}

.detail-panel li strong {
    color: #17304f;
    text-align: right;
}

.detail-description {
    border-radius: 16px;
    border: 1px solid rgba(23, 48, 79, 0.1);
    background: rgba(255, 255, 255, 0.96);
    padding: 14px;
}

.detail-description p {
    margin: 0;
    color: rgba(23, 48, 79, 0.74);
    line-height: 1.55;
}

@media (max-width: 1200px) {
    .kpi-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .detail-kpis {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .detail-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 900px) {
    .toolbar-card {
        flex-direction: column;
        align-items: stretch;
    }

    .search-field,
    .status-field {
        min-width: 100%;
    }
}

@media (max-width: 700px) {
    .kpi-grid {
        grid-template-columns: 1fr;
    }

    .row-actions {
        gap: 6px;
    }

    .detail-kpis {
        grid-template-columns: 1fr;
    }
}
</style>

<template>
    <section class="pos-shell">
        <article class="hero-card">
            <div>
                <span class="hero-kicker">Punto de venta</span>
                <h2>Nueva venta POS</h2>
                <p>
                    Selecciona productos por tarjeta, agregalos al panel lateral y finaliza la venta con cliente,
                    metodo de pago y descuentos por item cuando esten permitidos.
                </p>
            </div>
            <div class="hero-resume">
                <article>
                    <small>Productos visibles</small>
                    <strong>{{ productosFiltrados.length }}</strong>
                </article>
                <article>
                    <small>Productos agregados</small>
                    <strong>{{ totalItemsCarrito }}</strong>
                </article>
                <article>
                    <small>Unidades totales</small>
                    <strong>{{ totalUnidadesCarrito }}</strong>
                </article>
                <article>
                    <small>Total preliminar</small>
                    <strong>{{ money(totalCarrito) }}</strong>
                </article>
            </div>
        </article>

        <article v-if="flashMessage" :class="['flash', flashType === 'error' ? 'flash--error' : 'flash--success']">
            {{ flashMessage }}
        </article>

        <div class="pos-layout">
            <section class="catalog-column">
                <article class="toolbar-card">
                    <label class="search-field" for="search-pos-productos">
                        <i class="mdi mdi-magnify"></i>
                        <input
                            id="search-pos-productos"
                            v-model.trim="searchQuery"
                            type="text"
                            placeholder="Buscar por nombre, codigo, codigo de barras, categoria o marca"
                        />
                    </label>

                    <label class="status-field" for="filter-categoria-pos">
                        <i class="mdi mdi-shape-outline"></i>
                        <select id="filter-categoria-pos" v-model.number="categoriaFilter">
                            <option value="todos">Todas las categorias</option>
                            <option v-for="categoria in categorias" :key="categoria.id" :value="categoria.id">
                                {{ categoria.nombre }}
                            </option>
                        </select>
                    </label>

                    <label class="status-field" for="filter-marca-pos">
                        <i class="mdi mdi-tag-multiple-outline"></i>
                        <select id="filter-marca-pos" v-model.number="marcaFilter">
                            <option value="todos">Todas las marcas</option>
                            <option v-for="marca in marcas" :key="marca.id" :value="marca.id">
                                {{ marca.nombre }}
                            </option>
                        </select>
                    </label>
                </article>

                <section class="products-grid" aria-label="Listado de productos POS">
                    <article v-for="producto in productosFiltrados" :key="producto.id" class="product-card">
                        <div class="product-media" :class="{ 'product-media--fallback': !resolverImagenProducto(producto) }">
                            <img
                                v-if="resolverImagenProducto(producto)"
                                :src="resolverImagenProducto(producto)"
                                :alt="producto.nombre"
                                loading="lazy"
                            />
                            <div v-else class="product-fallback">
                                <i class="mdi mdi-image-off-outline"></i>
                                <span>Sin foto</span>
                            </div>
                            <span class="stock-pill" :class="resolverClaseStock(producto)">
                                {{ resolverTextoStock(producto) }}
                            </span>
                        </div>

                        <div class="product-body">
                            <strong class="product-name">{{ producto.nombre }}</strong>
                            <small class="product-meta">
                                {{ producto.categoria_nombre }} · {{ producto.marca_nombre }}
                            </small>
                            <small class="product-code">
                                Cod: {{ producto.codigo || 'N/A' }} · Barras: {{ producto.codigo_barras || 'N/A' }}
                            </small>

                            <div class="product-tags">
                                <span :class="['tag-pill', producto.permite_descuento ? 'tag-pill--success' : 'tag-pill--muted']">
                                    {{ producto.permite_descuento ? 'Permite descuento' : 'Sin descuento' }}
                                </span>
                                    <span class="tag-pill tag-pill--stock">Stock {{ stockDisponible(producto) }}</span>
                            </div>

                            <div class="product-prices">
                                <span>{{ money(producto.precio_venta) }}</span>
                                <small>Costo {{ money(producto.costo) }}</small>
                            </div>

                            <button
                                type="button"
                                class="submit-button add-button"
                                :disabled="!puedeAgregarProducto(producto)"
                                @click="agregarProducto(producto)"
                            >
                                <i class="mdi mdi-plus"></i>
                                <span>{{ puedeAgregarProducto(producto) ? 'Agregar' : 'Sin stock' }}</span>
                            </button>
                        </div>
                    </article>

                    <article v-if="!productosFiltrados.length" class="empty-state">
                        <i class="mdi mdi-package-variant-closed-remove"></i>
                        <strong>No hay productos para este filtro</strong>
                        <small>Ajusta el buscador o los filtros de categoria y marca.</small>
                    </article>
                </section>
            </section>

            <aside class="sale-column">
                <article class="cart-card">
                    <header class="cart-header">
                        <div>
                            <span class="hero-kicker">Resumen venta</span>
                            <h3>Productos agregados</h3>
                        </div>
                        <span class="cart-badge">{{ totalItemsCarrito }}</span>
                    </header>

                    <section v-if="carrito.length" class="cart-list" aria-label="Items agregados al carrito">
                        <article v-for="item in carrito" :key="item.producto_id" class="cart-item">
                            <div class="cart-item-head">
                                <strong>{{ item.nombre }}</strong>
                                <button type="button" class="icon-action" @click="eliminarItem(item.producto_id)">
                                    <i class="mdi mdi-trash-can-outline"></i>
                                </button>
                            </div>

                            <small>{{ money(item.precio_unitario) }} c/u</small>

                            <div class="qty-row">
                                <button type="button" class="qty-btn" @click="restarCantidad(item.producto_id)">
                                    <i class="mdi mdi-minus"></i>
                                </button>
                                <strong>{{ item.cantidad }}</strong>
                                <button
                                    type="button"
                                    class="qty-btn"
                                    :disabled="!puedeIncrementarItem(item.producto_id)"
                                    @click="sumarCantidad(item.producto_id)"
                                >
                                    <i class="mdi mdi-plus"></i>
                                </button>
                            </div>

                            <small class="line-total">Subtotal: {{ money(calcularSubtotalItem(item)) }}</small>
                        </article>
                    </section>

                    <section v-else class="empty-cart">
                        <i class="mdi mdi-cart-outline"></i>
                        <strong>Aun no agregas productos</strong>
                        <small>Usa el boton Agregar debajo de cada mini card.</small>
                    </section>

                    <footer class="cart-footer">
                        <div>
                            <small>Subtotal</small>
                            <strong>{{ money(subtotalCarrito) }}</strong>
                        </div>
                        <div>
                            <small>Descuentos</small>
                            <strong class="discount-text">-{{ money(totalDescuentoCarrito) }}</strong>
                        </div>
                        <div>
                            <small>Total</small>
                            <strong>{{ money(totalCarrito) }}</strong>
                        </div>

                        <button
                            type="button"
                            class="submit-button checkout-button"
                            :disabled="!carrito.length"
                            @click="abrirModalVenta"
                        >
                            <i class="mdi mdi-cash-register"></i>
                            <span>Realizar venta</span>
                        </button>
                    </footer>
                </article>
            </aside>
        </div>

        <v-dialog v-model="ventaDialog" max-width="980px" persistent>
            <v-card class="dialog-card">
                <v-card-title class="dialog-card-title">
                    <v-avatar size="46" class="dialog-avatar">
                        <v-icon large>mdi-point-of-sale</v-icon>
                    </v-avatar>
                    <div>
                        <span class="dialog-kicker">Confirmar venta</span>
                        <h3 class="dialog-title">Datos de facturacion POS</h3>
                        <p class="dialog-description">
                            Define cliente, forma de pago y descuentos aplicables por producto.
                        </p>
                    </div>
                </v-card-title>

                <v-divider />

                <v-card-text class="dialog-card-body">
                    <div class="dialog-grid dialog-grid--cols-2">
                        <label class="field field-full">
                            <span>Cliente</span>
                            <select v-model.number="ventaForm.cliente_id">
                                <option :value="null">Selecciona cliente</option>
                                <option v-for="cliente in clientes" :key="cliente.id" :value="cliente.id">
                                    {{ cliente.nombre_mostrar }}
                                </option>
                            </select>
                        </label>

                        <label class="field field-half">
                            <span>Metodo de pago</span>
                            <select v-model.number="ventaForm.metodo_pago_id">
                                <option :value="null">Selecciona metodo de pago</option>
                                <option v-for="metodo in metodosPago" :key="metodo.id" :value="metodo.id">
                                    {{ metodo.nombre }}
                                </option>
                            </select>
                        </label>

                        <label class="field field-half">
                            <span>Referencia de pago</span>
                            <input
                                v-model.trim="ventaForm.referencia_pago"
                                type="text"
                                placeholder="Ej: Voucher, transaccion o referencia"
                            />
                        </label>

                        <label class="field field-full">
                            <span>Observaciones</span>
                            <textarea
                                v-model.trim="ventaForm.observaciones"
                                rows="2"
                                placeholder="Nota de venta opcional"
                            ></textarea>
                        </label>
                    </div>

                    <section class="sale-items-editor" aria-label="Calculadora de descuentos por item">
                        <header class="sale-items-editor__header">
                            <strong>Detalle de productos en facturacion</strong>
                            <small>{{ totalItemsCarrito }} productos · {{ totalUnidadesCarrito }} unidades</small>
                        </header>

                        <article
                            v-for="(item, index) in carrito"
                            :key="`editor-${item.producto_id}`"
                            :class="['sale-item-row', item.permite_descuento ? 'sale-item-row--discount' : 'sale-item-row--locked']"
                        >
                            <div class="sale-item-row__head">
                                <div class="sale-item-row__identity">
                                    <span class="item-index">Item {{ index + 1 }}</span>
                                    <div class="sale-item-row__copy">
                                        <strong>{{ item.nombre }}</strong>
                                        <small>{{ item.cantidad }} x {{ money(item.precio_unitario) }}</small>
                                    </div>
                                </div>

                                <div class="sale-item-row__price">
                                    <small>Subtotal</small>
                                    <small>{{ item.cantidad }} x {{ money(item.precio_unitario) }}</small>
                                    <strong>{{ money(calcularSubtotalItem(item)) }}</strong>
                                </div>
                            </div>

                            <div v-if="item.permite_descuento" class="discount-editor">
                                <label class="field field-half">
                                    <span>Tipo descuento</span>
                                    <select v-model="item.descuento_tipo">
                                        <option value="porcentaje">Porcentaje (%)</option>
                                        <option value="monto">Monto fijo</option>
                                    </select>
                                </label>

                                <label class="field field-half">
                                    <span>Valor descuento</span>
                                    <input
                                        :value="item.descuento_valor"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        @input="actualizarDescuento(item.producto_id, $event.target.value)"
                                    />
                                </label>

                                <div class="quick-discounts">
                                    <button
                                        type="button"
                                        class="chip-button"
                                        @click="aplicarDescuentoRapido(item.producto_id, 5)"
                                    >5%</button>
                                    <button
                                        type="button"
                                        class="chip-button"
                                        @click="aplicarDescuentoRapido(item.producto_id, 10)"
                                    >10%</button>
                                    <button
                                        type="button"
                                        class="chip-button"
                                        @click="aplicarDescuentoRapido(item.producto_id, 15)"
                                    >15%</button>
                                    <button
                                        type="button"
                                        class="chip-button"
                                        @click="aplicarDescuentoRapido(item.producto_id, 20)"
                                    >20%</button>
                                    <button
                                        type="button"
                                        class="chip-button chip-button--muted"
                                        @click="aplicarDescuentoRapido(item.producto_id, 0)"
                                    >Limpiar</button>
                                </div>
                            </div>

                            <div v-else class="discount-disabled">
                                <i class="mdi mdi-lock-outline"></i>
                                <span>Este producto no permite descuento.</span>
                            </div>

                            <div class="sale-item-row__totals">
                                <small>Descuento: {{ money(calcularDescuentoItem(item)) }}</small>
                                <strong>Total item: {{ money(calcularTotalItem(item)) }}</strong>
                            </div>
                        </article>
                    </section>

                    <article class="totals-panel">
                        <div>
                            <small>Subtotal</small>
                            <strong>{{ money(subtotalCarrito) }}</strong>
                        </div>
                        <div>
                            <small>Descuentos</small>
                            <strong class="discount-text">-{{ money(totalDescuentoCarrito) }}</strong>
                        </div>
                        <div>
                            <small>Total neto</small>
                            <strong>{{ money(totalCarrito) }}</strong>
                        </div>
                    </article>
                </v-card-text>

                <v-divider />

                <v-card-actions class="dialog-actions">
                    <button type="button" class="secondary-button" @click="cerrarModalVenta">Cancelar</button>
                    <button type="button" class="submit-button" data-action-loader="true" @click="confirmarVentaFront">Confirmar venta</button>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </section>
</template>

<script>
import api from '@/services/api';

export default {
    name: 'PostNuevaVentaView',

    data() {
        return {
            searchQuery: '',
            categoriaFilter: 'todos',
            marcaFilter: 'todos',
            ventaDialog: false,
            flashMessage: '',
            flashType: 'success',
            productos: [],
            categorias: [],
            marcas: [],
            clientes: [],
            metodosPago: [],
            carrito: [],
            ventaForm: {
                cliente_id: null,
                metodo_pago_id: null,
                referencia_pago: '',
                observaciones: '',
            },
        };
    },

    computed: {
        productosFiltrados() {
            const query = this.searchQuery.toLowerCase();

            return this.productos.filter((item) => {
                const coincideTexto = !query
                    || this.construirTextoBusqueda(item).includes(query);

                const coincideCategoria = this.categoriaFilter === 'todos'
                    || Number(item.categoria_id) === Number(this.categoriaFilter);

                const coincideMarca = this.marcaFilter === 'todos'
                    || Number(item.marca_id) === Number(this.marcaFilter);

                return coincideTexto && coincideCategoria && coincideMarca && this.estaActivo(item);
            });
        },

        totalItemsCarrito() {
            return this.carrito.length;
        },

        totalUnidadesCarrito() {
            return this.carrito.reduce((acc, item) => acc + Number(item.cantidad || 0), 0);
        },

        subtotalCarrito() {
            return this.carrito.reduce((acc, item) => acc + this.calcularSubtotalItem(item), 0);
        },

        totalDescuentoCarrito() {
            return this.carrito.reduce((acc, item) => acc + this.calcularDescuentoItem(item), 0);
        },

        totalCarrito() {
            return Math.max(this.subtotalCarrito - this.totalDescuentoCarrito, 0);
        },
    },

    mounted() {
        this.inicializarVista();
    },

    methods: {
        async inicializarVista() {
            this.$emit('start-action', 'Cargando catalogo POS...', null, null);

            try {
                await Promise.all([
                    this.listarProductos(),
                    this.listarClientes(),
                    this.listarCategorias(),
                    this.listarMarcas(),
                    this.listarMetodosPago(),
                ]);
            } finally {
                this.$emit('stop-action');
            }
        },

        async listarCategorias() {
            try {
                const { data } = await api.get('/categorias');
                this.categorias = this.extraerLista(data, ['categorias']);
            } catch (error) {
                this.categorias = [];
            }
        },

        async listarMarcas() {
            try {
                const { data } = await api.get('/marcas');
                this.marcas = this.extraerLista(data, ['marcas']);
            } catch (error) {
                this.marcas = [];
            }
        },

        async listarMetodosPago() {
            try {
                const { data } = await api.get('/metodos-pago/listar');
                this.metodosPago = this.extraerLista(data, ['metodos_pago', 'metodosPago']).map((item) => ({
                    id: item.id,
                    nombre: item.nombre || item.metodo_pago || 'Metodo',
                }));
            } catch (error) {
                this.metodosPago = [];
            }
        },

        extraerLista(payload, keys = []) {
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

        async listarProductos() {
            try {
                const { data } = await api.get('/productos');
                const lista = this.extraerLista(data, ['productos']);
                this.productos = lista.map(item => this.normalizarProducto(item));
            } catch (error) {
                this.productos = [];
                this.mostrarFlash('No fue posible cargar productos para el POS.', 'error');
            }
        },

        async listarClientes() {
            try {
                const { data } = await api.get('/clientes');
                const lista = this.extraerLista(data, ['clientes']);
                this.clientes = lista.map(item => this.normalizarCliente(item));
            } catch (error) {
                this.clientes = [];
            }
        },

        normalizarProducto(item) {
            return {
                id: item.id,
                nombre: item.nombre || 'Producto sin nombre',
                codigo: item.codigo || '',
                codigo_barras: item.codigo_barras || '',
                descripcion: item.descripcion || '',
                categoria_id: item.categoria_id,
                marca_id: item.marca_id,
                costo: Number(item.costo) || 0,
                precio_venta: Number(item.precio_venta) || 0,
                stock: Number(item.stock) || 0,
                stock_minimo: Number(item.stock_minimo) || 0,
                estado_id: Number(item.estado_id || 1),
                permite_descuento: this.parseBooleanFromApi(item.permite_descuento, true),
                categoria_nombre: item.categoria_nombre || item.categoria?.nombre || 'Sin categoria',
                marca_nombre: item.marca_nombre || item.marca?.nombre || 'Sin marca',
                imagen_url: item.imagen_url || item.imagen || item.foto || item.foto_url || item.url_foto || '',
            };
        },

        parseBooleanFromApi(value, fallback = false) {
            if (typeof value === 'boolean') {
                return value;
            }

            if (typeof value === 'number') {
                return value === 1;
            }

            if (typeof value === 'string') {
                const normalized = value.trim().toLowerCase();

                if (['1', 'true', 'si', 'yes'].includes(normalized)) {
                    return true;
                }

                if (['0', 'false', 'no', ''].includes(normalized)) {
                    return false;
                }
            }

            return fallback;
        },

        normalizarCliente(item) {
            const nombreNatural = `${item.nombre || ''} ${item.apellido || ''}`.trim();
            const nombreJuridico = item.razon_social || item.nombre_comercial || '';

            return {
                id: item.id,
                nombre_mostrar: nombreJuridico || nombreNatural || item.email || `Cliente #${item.id}`,
            };
        },

        construirTextoBusqueda(item) {
            return [
                item.nombre,
                item.codigo,
                item.codigo_barras,
                item.descripcion,
                item.categoria_nombre,
                item.marca_nombre,
            ]
                .join(' ')
                .toLowerCase();
        },

        resolverImagenProducto(producto) {
            return producto.imagen_url || '';
        },

        estaActivo(item) {
            return Number(item.estado_id) === 1;
        },

        resolverTextoStock(item) {
            const disponible = this.stockDisponible(item);

            if (disponible <= 0) {
                return 'Agotado';
            }

            if (disponible <= Number(item.stock_minimo)) {
                return 'Stock bajo';
            }

            return 'Disponible';
        },

        resolverClaseStock(item) {
            const disponible = this.stockDisponible(item);

            if (disponible <= 0) {
                return 'stock-pill--out';
            }

            if (disponible <= Number(item.stock_minimo)) {
                return 'stock-pill--low';
            }

            return 'stock-pill--ok';
        },

        cantidadEnCarrito(productoId) {
            const item = this.carrito.find(row => Number(row.producto_id) === Number(productoId));
            return item ? Number(item.cantidad || 0) : 0;
        },

        stockDisponible(producto) {
            const stockBase = Number(producto?.stock) || 0;
            const productoId = producto?.id || producto?.producto_id;
            const cantidad = this.cantidadEnCarrito(productoId);
            return Math.max(stockBase - cantidad, 0);
        },

        puedeAgregarProducto(producto) {
            return this.stockDisponible(producto) > 0;
        },

        puedeIncrementarItem(productoId) {
            const producto = this.productos.find(row => Number(row.id) === Number(productoId));
            return producto ? this.stockDisponible(producto) > 0 : false;
        },

        agregarProducto(producto) {
            if (!this.puedeAgregarProducto(producto)) {
                this.mostrarFlash(`No hay stock disponible para ${producto.nombre}.`, 'error');
                return;
            }

            const existente = this.carrito.find(item => Number(item.producto_id) === Number(producto.id));

            if (existente) {
                existente.cantidad += 1;
                return;
            }

            this.carrito.push({
                producto_id: producto.id,
                nombre: producto.nombre,
                precio_unitario: Number(producto.precio_venta) || 0,
                cantidad: 1,
                permite_descuento: this.parseBooleanFromApi(producto.permite_descuento, true),
                descuento_tipo: 'porcentaje',
                descuento_valor: 0,
            });
        },

        sumarCantidad(productoId) {
            const item = this.carrito.find(row => Number(row.producto_id) === Number(productoId));

            if (!item) {
                return;
            }

            if (!this.puedeIncrementarItem(productoId)) {
                this.mostrarFlash(`No puedes agregar mas unidades de ${item.nombre}.`, 'error');
                return;
            }

            item.cantidad += 1;
        },

        restarCantidad(productoId) {
            const item = this.carrito.find(row => Number(row.producto_id) === Number(productoId));

            if (!item) {
                return;
            }

            if (item.cantidad <= 1) {
                this.eliminarItem(productoId);
                return;
            }

            item.cantidad -= 1;
        },

        eliminarItem(productoId) {
            this.carrito = this.carrito.filter(item => Number(item.producto_id) !== Number(productoId));
        },

        calcularSubtotalItem(item) {
            return (Number(item.precio_unitario) || 0) * (Number(item.cantidad) || 0);
        },

        calcularDescuentoItem(item) {
            if (!item.permite_descuento) {
                return 0;
            }

            const subtotal = this.calcularSubtotalItem(item);
            const valor = Number(item.descuento_valor) || 0;

            if (item.descuento_tipo === 'monto') {
                return Math.min(Math.max(valor, 0), subtotal);
            }

            const porcentaje = Math.min(Math.max(valor, 0), 100);
            return subtotal * (porcentaje / 100);
        },

        calcularTotalItem(item) {
            return Math.max(this.calcularSubtotalItem(item) - this.calcularDescuentoItem(item), 0);
        },

        actualizarDescuento(productoId, value) {
            const item = this.carrito.find(row => Number(row.producto_id) === Number(productoId));

            if (!item || !item.permite_descuento) {
                return;
            }

            const numero = Number(value);
            item.descuento_valor = Number.isFinite(numero) ? numero : 0;
        },

        aplicarDescuentoRapido(productoId, porcentaje) {
            const item = this.carrito.find(row => Number(row.producto_id) === Number(productoId));

            if (!item || !item.permite_descuento) {
                return;
            }

            item.descuento_tipo = 'porcentaje';
            item.descuento_valor = Number(porcentaje) || 0;
        },

        abrirModalVenta() {
            if (!this.carrito.length) {
                this.mostrarFlash('Agrega al menos un producto antes de realizar la venta.', 'error');
                return;
            }

            this.ventaDialog = true;
        },

        cerrarModalVenta() {
            this.ventaDialog = false;
        },

        confirmarVentaFront() {
            if (!this.ventaForm.cliente_id) {
                this.mostrarFlash('Debes seleccionar un cliente para continuar.', 'error');
                return;
            }

            if (!this.ventaForm.metodo_pago_id) {
                this.mostrarFlash('Debes seleccionar un metodo de pago para continuar.', 'error');
                return;
            }

            this.$emit('start-action', 'Confirmando venta POS...', null, null);

            window.setTimeout(() => {
                this.ventaDialog = false;
                this.carrito = [];
                this.ventaForm = {
                    cliente_id: null,
                    metodo_pago_id: null,
                    referencia_pago: '',
                    observaciones: '',
                };

                this.$emit('stop-action');
                this.mostrarFlash('Venta preparada en frontend correctamente. Lista para conectar con backend.', 'success');
            }, 1400);
        },

        mostrarFlash(message, type = 'success') {
            this.flashMessage = message;
            this.flashType = type;

            window.setTimeout(() => {
                if (this.flashMessage === message) {
                    this.flashMessage = '';
                }
            }, 3200);
        },

        money(value) {
            return new Intl.NumberFormat('es-CO', {
                style: 'currency',
                currency: 'COP',
                maximumFractionDigits: 0,
            }).format(Number(value) || 0);
        },
    },
};
</script>

<style scoped>
.pos-shell {
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
    display: flex;
    justify-content: space-between;
    gap: 16px;
}

.hero-kicker {
    display: inline-flex;
    align-items: center;
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
    max-width: 780px;
}

.hero-resume {
    display: grid;
    grid-template-columns: repeat(4, minmax(100px, 1fr));
    gap: 10px;
    min-width: 420px;
}

.hero-resume article {
    border-radius: 14px;
    padding: 10px 12px;
    background: rgba(255, 255, 255, 0.7);
    border: 1px solid rgba(23, 48, 79, 0.12);
    display: grid;
    gap: 4px;
}

.hero-resume small {
    color: rgba(23, 48, 79, 0.64);
    font-size: 0.76rem;
}

.hero-resume strong {
    color: #17304f;
    font-size: 1rem;
}

.flash {
    border-radius: 12px;
    padding: 11px 14px;
    font-size: 0.9rem;
    font-weight: 600;
}

.flash--success {
    color: #0f5132;
    background: rgba(209, 231, 221, 0.8);
    border: 1px solid rgba(15, 81, 50, 0.2);
}

.flash--error {
    color: #842029;
    background: rgba(248, 215, 218, 0.86);
    border: 1px solid rgba(132, 32, 41, 0.22);
}

.pos-layout {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 360px;
    gap: 14px;
    align-items: start;
}

.catalog-column {
    display: grid;
    gap: 12px;
}

.toolbar-card {
    padding: 14px;
    border-radius: 20px;
    border: 1px solid rgba(23, 48, 79, 0.08);
    background: rgba(255, 255, 255, 0.96);
    display: flex;
    gap: 10px;
    align-items: center;
}

.search-field {
    flex: 1;
    min-width: 260px;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    border: 1px solid rgba(23, 48, 79, 0.16);
    background: #ffffff;
    border-radius: 12px;
    height: 44px;
    padding: 0 12px;
}

.search-field i,
.status-field i {
    color: rgba(23, 48, 79, 0.56);
}

.search-field input,
.status-field select {
    border: 0;
    outline: 0;
    width: 100%;
    background: transparent;
    color: #17304f;
}

.status-field {
    min-width: 180px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: 1px solid rgba(23, 48, 79, 0.16);
    border-radius: 12px;
    height: 44px;
    padding: 0 12px;
    background: #ffffff;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
    gap: 12px;
}

.product-card {
    border-radius: 18px;
    border: 1px solid rgba(23, 48, 79, 0.1);
    background: #fff;
    box-shadow: 0 10px 22px rgba(14, 28, 54, 0.08);
    overflow: hidden;
    display: grid;
    grid-template-rows: 136px 1fr;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.product-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 16px 28px rgba(14, 28, 54, 0.13);
}

.product-media {
    position: relative;
    background: #e9eff7;
}

.product-media img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-media--fallback {
    background: linear-gradient(135deg, #ecf3fc 0%, #fff3d8 100%);
}

.product-fallback {
    height: 100%;
    display: grid;
    place-items: center;
    color: rgba(23, 48, 79, 0.6);
    gap: 2px;
}

.product-fallback i {
    font-size: 1.5rem;
}

.stock-pill {
    position: absolute;
    top: 8px;
    right: 8px;
    border-radius: 999px;
    padding: 4px 9px;
    font-size: 0.68rem;
    font-weight: 700;
    border: 1px solid transparent;
}

.stock-pill--ok {
    color: #0a5e33;
    background: rgba(209, 231, 221, 0.94);
    border-color: rgba(10, 94, 51, 0.25);
}

.stock-pill--low {
    color: #856404;
    background: rgba(255, 243, 205, 0.94);
    border-color: rgba(133, 100, 4, 0.26);
}

.stock-pill--out {
    color: #842029;
    background: rgba(248, 215, 218, 0.94);
    border-color: rgba(132, 32, 41, 0.24);
}

.product-body {
    display: grid;
    gap: 7px;
    padding: 12px;
    align-content: start;
}

.product-name {
    color: #17304f;
    line-height: 1.2;
}

.product-meta,
.product-code {
    color: rgba(23, 48, 79, 0.62);
    font-size: 0.76rem;
}

.product-prices {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    margin-top: 2px;
}

.product-tags {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}

.tag-pill {
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
    padding: 4px 8px;
    font-size: 0.68rem;
    font-weight: 800;
    letter-spacing: 0.02em;
}

.tag-pill--success {
    color: #186843;
    background: rgba(109, 211, 160, 0.16);
    border: 1px solid rgba(24, 104, 67, 0.18);
}

.tag-pill--muted {
    color: rgba(23, 48, 79, 0.75);
    background: rgba(23, 48, 79, 0.08);
    border: 1px solid rgba(23, 48, 79, 0.14);
}

.tag-pill--stock {
    color: #244eac;
    background: rgba(47, 104, 223, 0.12);
    border: 1px solid rgba(47, 104, 223, 0.2);
}

.product-prices span {
    color: #17304f;
    font-weight: 700;
}

.product-prices small {
    color: rgba(23, 48, 79, 0.62);
    font-size: 0.72rem;
}

.add-button {
    margin-top: 6px;
    width: 100%;
    justify-content: center;
}

.add-button:disabled,
.qty-btn:disabled {
    opacity: 0.55;
    cursor: not-allowed;
}

.empty-state {
    grid-column: 1 / -1;
    border-radius: 16px;
    border: 1px dashed rgba(23, 48, 79, 0.2);
    background: #fff;
    min-height: 170px;
    display: grid;
    place-items: center;
    text-align: center;
    color: rgba(23, 48, 79, 0.7);
    padding: 18px;
}

.empty-state i {
    font-size: 1.7rem;
}

.sale-column {
    position: sticky;
    top: 12px;
}

.cart-card {
    border-radius: 20px;
    border: 1px solid rgba(23, 48, 79, 0.1);
    background: #fff;
    box-shadow: 0 14px 30px rgba(14, 28, 54, 0.08);
    overflow: hidden;
    display: grid;
    grid-template-rows: auto minmax(140px, 1fr) auto;
    max-height: calc(100vh - 170px);
}

.table-card {
    padding: 14px;
    border-radius: 24px;
    background: rgba(255, 255, 255, 0.92);
    border: 1px solid rgba(23, 48, 79, 0.08);
    box-shadow: 0 20px 48px rgba(14, 28, 54, 0.08);
}

.cart-header {
    padding: 14px;
    border-bottom: 1px solid rgba(23, 48, 79, 0.08);
    display: flex;
    justify-content: space-between;
    gap: 8px;
    background: linear-gradient(180deg, rgba(23, 48, 79, 0.03), rgba(23, 48, 79, 0));
}

.cart-header h3 {
    margin: 6px 0 0;
    color: #17304f;
    font-size: 1.06rem;
}

.cart-badge {
    min-width: 30px;
    height: 30px;
    border-radius: 999px;
    display: grid;
    place-items: center;
    background: #17304f;
    color: #fff;
    font-weight: 700;
    font-size: 0.82rem;
}

.cart-list {
    overflow: auto;
    padding: 10px;
    display: grid;
    gap: 8px;
}

.cart-item {
    border: 1px solid rgba(23, 48, 79, 0.1);
    border-radius: 12px;
    padding: 10px;
    display: grid;
    gap: 7px;
    background: rgba(255, 255, 255, 0.9);
}

.cart-item-head {
    display: flex;
    justify-content: space-between;
    gap: 8px;
    color: #17304f;
}

.icon-action {
    border: 0;
    background: transparent;
    color: rgba(132, 32, 41, 0.88);
    cursor: pointer;
}

.qty-row {
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.qty-btn {
    border: 1px solid rgba(23, 48, 79, 0.2);
    background: #fff;
    width: 26px;
    height: 26px;
    border-radius: 8px;
    cursor: pointer;
}

.line-total {
    color: rgba(23, 48, 79, 0.72);
    font-weight: 600;
}

.empty-cart {
    display: grid;
    place-items: center;
    text-align: center;
    gap: 4px;
    color: rgba(23, 48, 79, 0.68);
    padding: 20px;
}

.empty-cart i {
    font-size: 1.7rem;
}

.cart-footer {
    border-top: 1px solid rgba(23, 48, 79, 0.08);
    padding: 12px;
    display: grid;
    gap: 7px;
}

.cart-footer div {
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: #17304f;
}

.checkout-button {
    width: 100%;
    justify-content: center;
    margin-top: 4px;
}

.checkout-button:disabled {
    opacity: 0.55;
    cursor: not-allowed;
}

.discount-text {
    color: #8b1a1a;
}

.dialog-card {
    border-radius: 20px;
    overflow: hidden;
}

.dialog-card-title {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    padding: 20px;
}

.dialog-avatar {
    background: linear-gradient(135deg, #17304f, #2563eb);
    color: #fff;
}

.dialog-kicker {
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: rgba(23, 48, 79, 0.62);
}

.dialog-title {
    margin: 2px 0 4px;
    color: #17304f;
}

.dialog-description {
    margin: 0;
    font-size: 0.9rem;
    color: rgba(23, 48, 79, 0.67);
}

.dialog-card-body {
    padding: 18px 20px;
    display: grid;
    gap: 14px;
}

.dialog-grid {
    display: grid;
    gap: 10px;
}

.dialog-grid--cols-2 {
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

.field {
    display: grid;
    gap: 6px;
}

.field span {
    font-size: 0.77rem;
    font-weight: 700;
    color: rgba(23, 48, 79, 0.72);
    text-transform: uppercase;
    letter-spacing: 0.04em;
}

.field input,
.field select,
.field textarea {
    width: 100%;
    border: 1px solid rgba(23, 48, 79, 0.18);
    border-radius: 10px;
    background: #fff;
    min-height: 42px;
    padding: 10px 12px;
    color: #17304f;
    outline: 0;
}

.field textarea {
    min-height: 70px;
    resize: vertical;
}

.field-full {
    grid-column: 1 / -1;
}

.field-half {
    grid-column: span 1;
}

.sale-items-editor {
    display: grid;
    gap: 10px;
}

.sale-items-editor__header {
    border: 1px solid rgba(23, 48, 79, 0.12);
    border-radius: 12px;
    background: linear-gradient(180deg, rgba(23, 48, 79, 0.04), rgba(23, 48, 79, 0.01));
    padding: 10px 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
}

.sale-items-editor__header strong {
    color: #17304f;
    font-size: 0.9rem;
}

.sale-items-editor__header small {
    color: rgba(23, 48, 79, 0.68);
    font-weight: 700;
}

.sale-item-row {
    border: 1px solid rgba(23, 48, 79, 0.12);
    border-radius: 12px;
    padding: 12px;
    display: grid;
    gap: 10px;
    background: #ffffff;
}

.sale-item-row--discount {
    border-left: 4px solid rgba(24, 104, 67, 0.55);
}

.sale-item-row--locked {
    border-left: 4px solid rgba(23, 48, 79, 0.3);
    background: linear-gradient(180deg, rgba(23, 48, 79, 0.02), rgba(23, 48, 79, 0));
}

.sale-item-row__head {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 10px;
}

.sale-item-row__head strong {
    color: #17304f;
}

.sale-item-row__head small {
    color: rgba(23, 48, 79, 0.65);
}

.sale-item-row__identity {
    display: flex;
    align-items: flex-start;
    gap: 10px;
}

.sale-item-row__copy {
    display: grid;
    gap: 2px;
}

.item-index {
    min-width: 56px;
    text-align: center;
    border-radius: 999px;
    padding: 4px 8px;
    font-size: 0.68rem;
    font-weight: 800;
    color: #17304f;
    background: rgba(23, 48, 79, 0.1);
    border: 1px solid rgba(23, 48, 79, 0.18);
}

.sale-item-row__price {
    display: grid;
    justify-items: end;
    gap: 2px;
}

.sale-item-row__price small {
    color: rgba(23, 48, 79, 0.6);
    font-size: 0.75rem;
    font-weight: 700;
}

.sale-item-row__price strong {
    font-size: 1rem;
}

.discount-editor {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px;
}

.quick-discounts {
    grid-column: 1 / -1;
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.chip-button {
    border: 1px solid rgba(37, 99, 235, 0.22);
    border-radius: 999px;
    background: rgba(37, 99, 235, 0.08);
    color: #1d4ed8;
    padding: 4px 10px;
    font-size: 0.76rem;
    font-weight: 700;
    cursor: pointer;
}

.chip-button--muted {
    border-color: rgba(23, 48, 79, 0.2);
    background: rgba(23, 48, 79, 0.06);
    color: rgba(23, 48, 79, 0.75);
}

.discount-disabled {
    border-radius: 10px;
    background: rgba(23, 48, 79, 0.06);
    border: 1px dashed rgba(23, 48, 79, 0.16);
    color: rgba(23, 48, 79, 0.72);
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 11px;
    font-size: 0.86rem;
}

.sale-item-row__totals {
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: #17304f;
}

.sale-item-row__totals small {
    color: rgba(23, 48, 79, 0.68);
}

.totals-panel {
    border-radius: 14px;
    border: 1px solid rgba(23, 48, 79, 0.12);
    background: #f9fbff;
    padding: 10px 12px;
    display: grid;
    gap: 6px;
}

.totals-panel div {
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: #17304f;
}

.dialog-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    padding: 14px 20px 20px;
}

.submit-button,
.secondary-button {
    border-radius: 14px;
    height: 44px;
    min-height: 44px;
    min-width: 122px;
    padding: 0 18px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    font-weight: 700;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.submit-button {
    border: 0;
    color: #0b1530;
    background: linear-gradient(135deg, #f4b740 0%, #ffd277 45%, #d99210 100%);
    box-shadow: 0 8px 18px rgba(217, 146, 16, 0.22);
}

.submit-button:hover {
    transform: translateY(-1px);
}

.secondary-button {
    border: 1px solid rgba(23, 48, 79, 0.14);
    color: #17304f;
    background: rgba(255, 255, 255, 0.95);
}

.dialog-card {
    border-radius: 28px;
    overflow: hidden;
    background: #ffffff;
    box-shadow: 0 26px 62px rgba(15, 34, 65, 0.18);
}

.dialog-card-title {
    background: linear-gradient(180deg, rgba(23, 48, 79, 0.03), rgba(23, 48, 79, 0));
}

.dialog-avatar {
    background: linear-gradient(135deg, #f4b740 0%, #ffd277 45%, #d99210 100%);
    color: #0b1530;
}

@media (max-width: 1280px) {
    .pos-layout {
        grid-template-columns: 1fr;
    }

    .sale-column {
        position: static;
    }

    .cart-card {
        max-height: none;
    }
}

@media (max-width: 940px) {
    .hero-card {
        flex-direction: column;
    }

    .hero-resume {
        min-width: 0;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .toolbar-card {
        flex-wrap: wrap;
    }

    .status-field {
        min-width: 0;
        flex: 1;
    }

    .dialog-grid--cols-2,
    .discount-editor {
        grid-template-columns: 1fr;
    }

    .field-half,
    .field-full {
        grid-column: span 1;
    }
}

@media (max-width: 640px) {
    .products-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .hero-resume {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .dialog-actions {
        flex-direction: column-reverse;
    }

    .dialog-actions button {
        width: 100%;
        justify-content: center;
    }
}
</style>
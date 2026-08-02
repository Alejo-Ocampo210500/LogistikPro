<template>
    <v-dialog :value="value" max-width="920px" persistent @input="emitirInput">
        <v-card class="dialog-card">
            <v-card-title class="dialog-card-title">
                <v-avatar size="46" class="dialog-avatar">
                    <v-icon large>mdi-cube-send</v-icon>
                </v-avatar>
                <div>
                    <span class="dialog-kicker">{{ editMode ? 'Editar producto' : 'Nuevo producto' }}</span>
                    <h3 class="dialog-title">{{ editMode ? 'Actualizar producto de inventario' : 'Crear producto de inventario' }}</h3>
                    <p class="dialog-description">
                        Registra la informacion principal del producto para tener control de precio, stock y reglas comerciales.
                    </p>
                </div>
            </v-card-title>

            <v-divider />

            <v-card-text class="dialog-card-body">
                <div v-if="validationMessage" class="flash error">
                    {{ validationMessage }}
                </div>

                <div class="dialog-grid">
                    <label class="field field-third">
                        <span>Codigo interno</span>
                        <input v-model.trim="localForm.codigo" type="text" placeholder="Ej: PRD-001" />
                    </label>

                    <label class="field field-third">
                        <span>Codigo de barras</span>
                        <input v-model.trim="localForm.codigo_barras" type="text" placeholder="Ej: 7701234567890" />
                    </label>

                    <label class="field field-third">
                        <span>Estado</span>
                        <select v-model.number="localForm.estado_id">
                            <option v-for="estado in estados" :key="estado.id" :value="estado.id">
                                {{ estado.nombre }}
                            </option>
                        </select>
                    </label>

                    <label class="field field-full">
                        <span>Nombre</span>
                        <input v-model.trim="localForm.nombre" type="text" placeholder="Ej: Leche Entera 1L" />
                    </label>

                    <label class="field field-full">
                        <span>Descripcion</span>
                        <textarea v-model.trim="localForm.descripcion" rows="3" placeholder="Descripcion corta del producto"></textarea>
                    </label>

                    <label class="field field-quarter">
                        <span>Categoria</span>
                        <select v-model.number="localForm.categoria_id">
                            <option :value="null">Seleccionar</option>
                            <option v-for="categoria in categorias" :key="categoria.id" :value="categoria.id">
                                {{ categoria.nombre }}
                            </option>
                        </select>
                    </label>

                    <label class="field field-quarter">
                        <span>Marca</span>
                        <select v-model.number="localForm.marca_id">
                            <option :value="null">Seleccionar</option>
                            <option v-for="marca in marcas" :key="marca.id" :value="marca.id">
                                {{ marca.nombre }}
                            </option>
                        </select>
                    </label>

                    <label class="field field-quarter">
                        <span>Unidad de medida</span>
                        <select v-model.number="localForm.unidad_medida_id">
                            <option :value="null">Seleccionar</option>
                            <option v-for="unidad in unidades" :key="unidad.id" :value="unidad.id">
                                {{ unidad.nombre }}
                            </option>
                        </select>
                    </label>

                    <label class="field field-quarter">
                        <span>Impuesto</span>
                        <select v-model.number="localForm.impuesto_id">
                            <option :value="null">Seleccionar</option>
                            <option v-for="impuesto in impuestos" :key="impuesto.id" :value="impuesto.id">
                                {{ impuesto.nombre }}
                            </option>
                        </select>
                        <small v-if="impuestosMessage" class="field-note field-note--warning">{{ impuestosMessage }}</small>
                    </label>

                    <label class="field field-third">
                        <span>Costo</span>
                        <input v-model.number="localForm.costo" type="number" min="0" step="0.01" placeholder="0.00" />
                    </label>

                    <label class="field field-third">
                        <span>Precio de venta</span>
                        <input v-model.number="localForm.precio_venta" type="number" min="0" step="0.01" placeholder="0.00" />
                    </label>

                    <label class="field field-third">
                        <span>Stock actual</span>
                        <input v-model.number="localForm.stock" type="number" min="0" step="1" placeholder="0" />
                    </label>

                    <label class="field field-third">
                        <span>Stock minimo</span>
                        <input v-model.number="localForm.stock_minimo" type="number" min="0" step="1" placeholder="0" />
                    </label>

                    <label class="field field-third">
                        <span>Stock maximo</span>
                        <input v-model.number="localForm.stock_maximo" type="number" min="0" step="1" placeholder="0" />
                    </label>

                    <div class="field field-full rules-wrap">
                        <span>Reglas del producto</span>

                        <div class="rules-grid">
                            <label :class="['rule-item', { 'rule-item--active': localForm.maneja_inventario }]">
                                <input v-model="localForm.maneja_inventario" type="checkbox" />
                                <i class="mdi mdi-archive-check-outline"></i>
                                <span>Maneja inventario</span>
                            </label>

                            <label :class="['rule-item', { 'rule-item--active': localForm.permite_descuento }]">
                                <input v-model="localForm.permite_descuento" type="checkbox" />
                                <i class="mdi mdi-ticket-percent-outline"></i>
                                <span>Permite descuento</span>
                            </label>

                            <label :class="['rule-item', { 'rule-item--active': localForm.es_servicio }]">
                                <input v-model="localForm.es_servicio" type="checkbox" />
                                <i class="mdi mdi-tools"></i>
                                <span>Es servicio</span>
                            </label>

                            <label :class="['rule-item', { 'rule-item--active': localForm.venta_libre }]">
                                <input v-model="localForm.venta_libre" type="checkbox" />
                                <i class="mdi mdi-cash-fast"></i>
                                <span>Venta libre</span>
                            </label>
                        </div>
                    </div>
                </div>
            </v-card-text>

            <v-divider />

            <v-card-actions class="dialog-actions">
                <button type="button" class="secondary-button" @click="$emit('close')">
                    Cancelar
                </button>
                <button type="button" class="submit-button" data-action-loader="true" @click="emitirGuardar">
                    {{ editMode ? 'Guardar cambios' : 'Crear producto' }}
                </button>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    name: 'ProductoCreateDialog',

    props: {
        value: {
            type: Boolean,
            default: false,
        },
        editMode: {
            type: Boolean,
            default: false,
        },
        producto: {
            type: Object,
            default: () => ({}),
        },
        categorias: {
            type: Array,
            default: () => [],
        },
        marcas: {
            type: Array,
            default: () => [],
        },
        unidades: {
            type: Array,
            default: () => [],
        },
        impuestos: {
            type: Array,
            default: () => [],
        },
        impuestosMessage: {
            type: String,
            default: '',
        },
        estados: {
            type: Array,
            default: () => [],
        },
        validationMessage: {
            type: String,
            default: '',
        },
    },

    data() {
        return {
            localForm: this.buildDefaultForm(this.producto),
        };
    },

    watch: {
        value(newValue) {
            if (newValue) {
                this.localForm = this.buildDefaultForm(this.producto);
            }
        },
        producto: {
            deep: true,
            handler(nextValue) {
                if (this.value) {
                    this.localForm = this.buildDefaultForm(nextValue);
                }
            },
        },
    },

    methods: {
        emitInput(nextValue) {
            this.$emit('input', nextValue);
        },

        buildDefaultForm(base = {}) {
            return {
                id: base.id || null,
                codigo: base.codigo || '',
                codigo_barras: base.codigo_barras || '',
                nombre: base.nombre || '',
                descripcion: base.descripcion || '',
                categoria_id: base.categoria_id || null,
                marca_id: base.marca_id || null,
                unidad_medida_id: base.unidad_medida_id || null,
                impuesto_id: base.impuesto_id || null,
                costo: Number(base.costo) || 0,
                precio_venta: Number(base.precio_venta) || 0,
                stock: Number(base.stock) || 0,
                stock_minimo: Number(base.stock_minimo) || 0,
                stock_maximo: Number(base.stock_maximo) || 0,
                maneja_inventario: Boolean(base.maneja_inventario ?? true),
                permite_descuento: Boolean(base.permite_descuento ?? false),
                es_servicio: Boolean(base.es_servicio ?? false),
                venta_libre: Boolean(base.venta_libre ?? false),
                estado_id: Number(base.estado_id) || 1,
            };
        },

        emitirGuardar() {
            const payload = {
                ...this.localForm,
                costo: Number(this.localForm.costo) || 0,
                precio_venta: Number(this.localForm.precio_venta) || 0,
                stock: Number(this.localForm.stock) || 0,
                stock_minimo: Number(this.localForm.stock_minimo) || 0,
                stock_maximo: Number(this.localForm.stock_maximo) || 0,
            };

            this.$emit('save', payload);
        },
    },
};
</script>

<style scoped>
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
    max-width: 640px;
}

.dialog-card-body {
    padding: 20px 24px;
}

.dialog-grid {
    display: grid;
    gap: 16px;
    grid-template-columns: repeat(12, minmax(0, 1fr));
}

.field {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.field-note {
    font-size: 0.74rem;
    line-height: 1.35;
}

.field-note--warning {
    color: #b45309;
}

.field-full {
    grid-column: span 12;
}

.field-third {
    grid-column: span 4;
}

.field-quarter {
    grid-column: span 3;
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

.rules-wrap {
    border: 1px solid rgba(23, 48, 79, 0.14);
    border-radius: 12px;
    padding: 12px;
    gap: 10px;
    background: rgba(247, 250, 255, 0.72);
}

.rules-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px;
}

.rule-item {
    display: flex;
    gap: 10px;
    align-items: center;
    border: 1px solid rgba(23, 48, 79, 0.12);
    border-radius: 12px;
    padding: 10px 12px;
    background: #ffffff;
    color: #17304f;
    font-size: 0.86rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.16s ease;
}

.rule-item input {
    width: 16px;
    height: 16px;
    margin: 0;
}

.rule-item i {
    font-size: 1rem;
    color: rgba(23, 48, 79, 0.72);
}

.rule-item--active {
    border-color: rgba(217, 146, 16, 0.42);
    background: rgba(255, 242, 214, 0.78);
}

.rule-item--active i {
    color: #d99210;
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
    margin-bottom: 14px;
}

.flash.error {
    background: rgba(255, 123, 123, 0.16);
    color: #9b2f2f;
}

@media (max-width: 940px) {
    .field-third,
    .field-quarter {
        grid-column: span 6;
    }
}

@media (max-width: 700px) {
    .rules-grid {
        grid-template-columns: 1fr;
    }

    .field-third,
    .field-quarter,
    .field-full {
        grid-column: span 12;
    }

    .dialog-card-title {
        align-items: flex-start;
    }
}
</style>

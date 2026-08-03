<template>
    <section class="table-card suscripciones-card">
        <div class="table-head">
            <div>
                <span class="table-kicker">Configuración de suscripciones</span>
                <h2>Planes Disponibles</h2>
            </div>

            <div class="table-actions">
                <button type="button" class="create-button" @click="dialog = true" data-action-loader="true">
                    Crear plan
                </button>
                <span class="table-count">{{ planes.length }} registros</span>
            </div>
        </div>

        <div v-if="formError" class="flash error">
            {{ formError }}
        </div>

        <div class="table-wrap">
            <v-data-table dense :headers="headers" :items="planes" item-key="nombre" class="elevation-1 Planes-table">
                <template slot="item.nombre" slot-scope="{ item }">
                    <span class="plan-name-pill">{{ item.nombre }}</span>
                </template>
                <template slot="item.precio" slot-scope="{ item }">
                    <span class="price-chip">COP {{ formatNumber(item.precio) }}</span>
                </template>
                <template slot="item.estado" slot-scope="{ item }">
                    <span :class="['status-pill', getStatusClass(item.estado)]" style="font-size: 0.8rem; font-weight: 700; padding: 4px 10px; border-radius: 999px; display: inline-flex;">
                        {{ item.estado ? item.estado.nombre : '-' }}
                    </span>
                </template>
                <template slot="item.acciones" slot-scope="{ item }">
                    <button type="button" class="action-button-edit" @click="openEditDialog(item)" aria-label="Editar plan" style="border: 0; border-radius: 999px; width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 45%, #1d4ed8 100%); color: white; cursor: pointer;">
                        <i class="mdi mdi-pencil" style="font-size: 16px;"></i>
                    </button>
                </template>
            </v-data-table>
        </div>

        <v-dialog v-model="dialog" max-width="720px" persistent>
            <v-card class="dialog-card">
                <v-card-title class="dialog-card-title">
                    <v-avatar size="46" class="dialog-avatar">
                        <v-icon large>mdi-rocket-launch</v-icon>
                    </v-avatar>
                    <div>
                        <span class="dialog-kicker">{{ isEditMode ? 'Editar plan' : 'Nuevo plan' }}</span>
                        <h3 class="dialog-title">{{ isEditMode ? 'Actualizar plan de suscripción' : 'Crear plan de suscripción' }}</h3>
                        <p class="dialog-description">Configura un plan con precio, duración y estado. El plan quedará disponible para asignar a empresas.</p>
                    </div>
                </v-card-title>

                <v-divider />

                <v-card-text>
                    <v-form ref="planForm" v-model="formValid" lazy-validation>
                        <v-alert v-if="formError" type="error" dense text class="dialog-alert">
                            {{ formError }}
                        </v-alert>

                        <div class="dialog-grid">
                            <v-text-field
                                v-model="form.nombre"
                                label="Nombre"
                                :rules="nombreRules"
                                outlined
                                dense
                                required
                            />

                            <v-textarea
                                v-model="form.descripcion"
                                label="Descripción"
                                :rules="descripcionRules"
                                outlined
                                dense
                                rows="3"
                            />

                            <v-text-field
                                v-model="precioDisplay"
                                type="text"
                                label="Precio (COP)"
                                prefix="COP"
                                :rules="precioRules"
                                outlined
                                dense
                                required
                                @input="updatePrecioDisplay"
                            />

                            <v-text-field
                                v-model="form.duracion_meses"
                                type="number"
                                label="Duración (meses)"
                                :rules="duracionRules"
                                outlined
                                dense
                                required
                            />

                            <v-select
                                v-model.number="form.estado_id"
                                :items="estados"
                                item-text="nombre"
                                item-value="id"
                                label="Estado"
                                outlined
                                dense
                                required
                                :rules="[v => !!v || 'El estado es obligatorio.']"
                            />
                        </div>
                    </v-form>
                </v-card-text>

                <v-divider />

                <v-card-actions class="dialog-actions">
                    <button type="button" class="secondary-button" @click="closeDialog" :disabled="loading">
                        Cancelar
                    </button>
                    <button type="button" class="submit-button" @click="submitPlan" :disabled="loading" data-action-loader="true">
                        <span v-if="loading" class="button-inline">
                            <span class="button-spinner" aria-hidden="true"></span>
                            Guardando plan...
                        </span>
                        <span v-else>Guardar plan</span>
                    </button>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </section>
</template>

<script>
import api from '@/services/api';

export default {
    name: 'PlanesEmpresa',

    props: {
        planes: {
            type: Array,
            default: () => [],
        },
        estados: {
            type: Array,
            default: () => [],
        },
    },

    data() {
        return {
            dialog: false,
            loading: false,
            isEditMode: false,
            selectedPlanId: null,
            formValid: false,
            formError: '',
            precioDisplay: '',
            form: {
                nombre: '',
                descripcion: '',
                precio: '',
                duracion_meses: '',
                estado_id: null,
            },
            headers: [
                { text: 'Nombre del Plan', value: 'nombre' },
                { text: 'Descripción', value: 'descripcion' },
                { text: 'Precio', value: 'precio' },
                { text: 'Duración (meses)', value: 'duracion_meses' },
                { text: 'Estado', value: 'estado' },
                { text: 'Acciones', value: 'acciones', sortable: false },
            ],
        };
    },

    computed: {
        nombreRules() {
            return [
                v => !!v || 'El nombre es obligatorio.',
                v => (v && v.length <= 100) || 'Máximo 100 caracteres.',
            ];
        },
        descripcionRules() {
            return [
                v => (v === '' || (v === null) || (v && v.length <= 250)) || 'Máximo 250 caracteres.',
            ];
        },
        precioRules() {
            return [
                v => !!v || 'El precio es obligatorio.',
                v => this.unformatNumber(v) >= 0 || 'El precio debe ser mayor o igual a 0.',
            ];
        },
        duracionRules() {
            return [
                v => !!v || 'La duración es obligatoria.',
                v => Number(v) >= 1 || 'La duración debe ser al menos 1 mes.',
            ];
        },
    },

    methods: {
        formatNumber(value) {
            if (value === null || value === undefined || value === '') {
                return '';
            }
            const parsed = parseFloat(value);
            if (isNaN(parsed)) return '';
            return new Intl.NumberFormat('es-CO', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 2
            }).format(parsed);
        },

        unformatNumber(value) {
            if (!value) return 0;
            const cleanStr = String(value)
                .replace(/\./g, '')
                .replace(/,/g, '.');
            return parseFloat(cleanStr) || 0;
        },

        updatePrecioDisplay(value) {
            const cleanInput = String(value).replace(/[^0-9.,]/g, '');
            const containsComma = cleanInput.includes(',');
            const parts = cleanInput.split(',');
            const integerPart = parts[0].replace(/\D/g, '');
            let formatted = this.formatNumber(integerPart);
            
            if (containsComma) {
                const decimalPart = parts[1] ? parts[1].replace(/\D/g, '').slice(0, 2) : '';
                formatted += ',' + decimalPart;
            }
            
            this.precioDisplay = formatted;
            this.form.precio = this.unformatNumber(formatted);
        },

        openEditDialog(plan) {
            this.isEditMode = true;
            this.selectedPlanId = plan.id;
            this.form = {
                nombre: plan.nombre,
                descripcion: plan.descripcion || '',
                precio: plan.precio,
                duracion_meses: plan.duracion_meses,
                estado_id: plan.estado_id,
            };
            this.precioDisplay = this.formatNumber(plan.precio);
            this.dialog = true;
        },

        closeDialog() {
            this.dialog = false;
            this.loading = false;
            this.isEditMode = false;
            this.selectedPlanId = null;
            this.formError = '';
            this.precioDisplay = '';
            this.form = {
                nombre: '',
                descripcion: '',
                precio: '',
                duracion_meses: '',
                estado_id: this.estados.length ? this.estados[0].id : null,
            };
            this.$nextTick(() => {
                if (this.$refs.planForm) {
                    this.$refs.planForm.resetValidation();
                }
            });
        },

        getStatusClass(estado) {
            if (!estado) return 'status-off';
            const name = String(estado.nombre).toLowerCase();
            if (name === 'activo') return 'status-ok';
            if (name === 'inactivo' || name === 'bloqueado' || name === 'cancelada') return 'status-off';
            return 'status-warning';
        },

        async submitPlan() {
            this.formError = '';
            const valid = this.$refs.planForm ? this.$refs.planForm.validate() : false;

            if (!valid) {
                this.formError = 'Revisa los campos obligatorios y corrige los errores.';
                return;
            }

            this.loading = true;
            const actionMsg = this.isEditMode ? 'Actualizando plan...' : 'Creando plan...';
            this.$emit('start-action', actionMsg, null, 2200);

            try {
                if (this.isEditMode) {
                    await api.put(`/planes/${this.selectedPlanId}`, {
                        nombre: this.form.nombre,
                        descripcion: this.form.descripcion,
                        precio: Number(this.form.precio),
                        duracion_meses: Number(this.form.duracion_meses),
                        estado_id: Number(this.form.estado_id),
                    });
                    this.closeDialog();
                    this.$emit('plan-updated');
                } else {
                    await api.post('/planes', {
                        nombre: this.form.nombre,
                        descripcion: this.form.descripcion,
                        precio: Number(this.form.precio),
                        duracion_meses: Number(this.form.duracion_meses),
                        estado_id: Number(this.form.estado_id),
                    });
                    this.closeDialog();
                    this.$emit('plan-created');
                }
            } catch (error) {
                this.formError = error.response && error.response.data && error.response.data.mensaje
                    ? error.response.data.mensaje
                    : 'No fue posible guardar el plan. Revisa la información e intenta de nuevo.';
            } finally {
                this.loading = false;
            }
        },
    },
};
</script>

<style scoped>
.table-card {
    padding: 24px;
    border-radius: 28px;
    background: rgba(255, 255, 255, 0.92);
    border: 1px solid rgba(23, 48, 79, 0.08);
    box-shadow: 0 20px 48px rgba(14, 28, 54, 0.08);
}

.table-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 20px;
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
    font-size: 1.5rem;
    color: #17304f;
}

.table-count {
    padding: 9px 12px;
    border-radius: 999px;
    background: rgba(250, 175, 1, 0.12);
    color: #996600;
    font-weight: 800;
}

.table-actions {
    display: flex;
    align-items: center;
    gap: 12px;
}

.create-button,
.submit-button {
    border: none;
    border-radius: 14px;
    padding: 11px 18px;
    background: linear-gradient(135deg, #f4b740 0%, #ffd277 45%, #d99210 100%);
    color: #0b1530;
    font-weight: 800;
    cursor: pointer;
    transition: transform 0.2s ease, filter 0.2s ease;
}

.create-button:hover,
.submit-button:hover {
    transform: translateY(-1px);
}

.create-button:disabled,
.submit-button:disabled {
    opacity: 0.7;
    cursor: wait;
}

.secondary-button {
    border: 1px solid rgba(23, 48, 79, 0.12);
    background: rgba(255, 255, 255, 0.95);
    color: #17304f;
    padding: 11px 18px;
    border-radius: 14px;
    font-weight: 800;
    cursor: pointer;
}

.secondary-button:disabled {
    opacity: 0.7;
    cursor: wait;
}

.table-wrap {
    overflow-x: auto;
    overflow-y: visible;
    -webkit-overflow-scrolling: touch;
    overscroll-behavior-x: contain;
}

.suscripciones-table .v-data-table__wrapper {
    overflow-x: auto;
    overflow-y: visible;
    -webkit-overflow-scrolling: touch;
}

.suscripciones-table .v-data-table__wrapper table {
    width: max-content;
    min-width: 920px;
}

.plan-name-pill {
    display: inline-flex;
    align-items: center;
    padding: 8px 12px;
    border-radius: 999px;
    background: rgba(244, 171, 58, 0.12);
    color: #ad6f00;
    font-weight: 800;
}

.price-chip {
    display: inline-flex;
    align-items: center;
    padding: 6px 10px;
    border-radius: 999px;
    background: rgba(23, 48, 79, 0.08);
    color: #17304f;
    font-weight: 700;
}


.suscripciones-table th,
.suscripciones-table td {
    padding: 14px 12px;
    text-align: left;
    border-bottom: 1px solid rgba(23, 48, 79, 0.08);
}

.suscripciones-table th {
    color: rgba(23, 48, 79, 0.64);
    font-size: 0.84rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
}

.suscripciones-table td {
    color: #17304f;
}

.suscripciones-table tbody tr:hover {
    background: rgba(244, 247, 255, 0.78);
}

.dialog-card {
    border-radius: 28px;
    overflow: hidden;
}

.dialog-card-title {
    display: flex;
    gap: 18px;
    align-items: center;
    padding: 24px;
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
    font-size: 1.6rem;
    color: #17304f;
}

.dialog-description {
    margin-top: 8px;
    color: rgba(23, 48, 79, 0.68);
    line-height: 1.6;
    max-width: 520px;
}

.dialog-alert {
    margin-bottom: 18px;
}

.dialog-grid {
    display: grid;
    gap: 18px;
}

.dialog-actions {
    padding: 18px 24px 22px;
    justify-content: flex-end;
    gap: 12px;
}

.button-inline {
    display: inline-flex;
    align-items: center;
    gap: 10px;
}

.button-spinner {
    width: 16px;
    height: 16px;
    border-radius: 999px;
    border: 2px solid rgba(11, 21, 48, 0.24);
    border-top-color: #0b1530;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.status-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.status-ok {
    background: rgba(109, 211, 160, 0.18);
    color: #186843;
}
.status-off {
    background: rgba(255, 123, 123, 0.16);
    color: #9b2f2f;
}
.status-warning {
    background: rgba(250, 175, 1, 0.16);
    color: #996600;
}

@media (max-width: 900px) {
    .table-card {
        padding: 16px;
    }

    .table-head {
        flex-direction: column;
    }

    .table-actions {
        width: 100%;
        justify-content: space-between;
        flex-wrap: wrap;
    }
}
</style>

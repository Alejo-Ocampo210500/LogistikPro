<template>
    <div class="sidebar-wrapper">

        <v-list dense nav class="sidebar-menu">
            <template v-for="item in menuFiltrado">
                <v-list-group
                    v-if="item.children && item.children.length"
                    :key="`group-${item.title}`"
                    class="menu-group"
                    append-icon="mdi-chevron-down"
                >
                    <template #activator>
                        <v-list-item-icon>
                            <v-icon>
                                {{ item.icon }}
                            </v-icon>
                        </v-list-item-icon>

                        <v-list-item-content>
                            <v-list-item-title class="menu-title">
                                {{ item.title }}
                            </v-list-item-title>
                        </v-list-item-content>
                    </template>

                    <v-list-item
                        v-for="child in item.children"
                        :key="`child-${item.title}-${child.title}`"
                        :to="child.route"
                        router
                        exact
                        class="menu-item menu-subitem"
                    >
                        <v-list-item-icon>
                            <v-icon>
                                {{ child.icon }}
                            </v-icon>
                        </v-list-item-icon>

                        <v-list-item-content>
                            <v-list-item-title class="menu-title">
                                {{ child.title }}
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </v-list-group>

                <v-list-item
                    v-else
                    :key="`item-${item.title}`"
                    :to="item.route"
                    router
                    exact
                    class="menu-item"
                >
                    <v-list-item-icon>
                        <v-icon>
                            {{ item.icon }}
                        </v-icon>
                    </v-list-item-icon>

                    <v-list-item-content>
                        <v-list-item-title class="menu-title">
                            {{ item.title }}
                        </v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
            </template>
        </v-list>

        <div class="sidebar-footer">

            <v-btn block text class="logout-button mb-3" :loading="cerrandoSesion" @click="cerrarSesion">
                <v-icon left size="19">mdi-logout</v-icon>
                Cerrar sesión
            </v-btn>

            <div class="sidebar-footer-line"></div>

            <div class="sidebar-footer-content">
                <v-icon size="14" class="mr-1">
                    mdi-copyright
                </v-icon>

                <span>
                    2026 Softnova
                </span>
            </div>

        </div>

    </div>
</template>

<script>
import menu from '@/plugins/menu'

export default {
    name: 'Sidebar',

    data() {
        return {
            menu,
            cerrandoSesion: false,
        }
    },

    computed: {
        menuFiltrado() {
            // Cuando actives permisos:
            //
            // return this.menu.filter(
            //     item => !item.permiso || this.$can(item.permiso)
            // )

            return this.filtrarMenu(this.menu)
        },
    },

    methods: {
        filtrarMenu(items) {
            return items.reduce((acumulado, item) => {
                const puedeVerPadre = !item.permiso || !this.$can || this.$can(item.permiso)

                if (item.children && item.children.length) {
                    if (!puedeVerPadre) {
                        return acumulado
                    }

                    const hijos = item.children.filter(
                        child => !child.permiso || !this.$can || this.$can(child.permiso),
                    )

                    if (hijos.length) {
                        acumulado.push({
                            ...item,
                            children: hijos,
                        })
                    }

                    return acumulado
                }

                if (puedeVerPadre) {
                    acumulado.push(item)
                }

                return acumulado
            }, [])
        },

        async cerrarSesion() {
            this.cerrandoSesion = true

            try {
                await this.$api.post('/auth/logout')
            } catch (error) {
                this.$toast.error('Error al cerrar sesión. Por favor, inténtalo de nuevo.')
            } finally {
                this.$clearApiSession()
                this.cerrandoSesion = false
                await this.$router.push('/login')
            }
        },
    },
}
</script>

<style scoped>
.sidebar-wrapper {
    display: flex;
    flex-direction: column;

    /* Ocupa únicamente el espacio restante después del logo. */
    flex: 1 0 auto;

    background: transparent;
}

.sidebar-menu {
    flex: 1;

    background: transparent !important;

    padding: 8px 12px 30px;
}

.menu-group {
    margin: 4px 0;
}

::v-deep .menu-group > .v-list-group__header {
    position: relative;

    min-height: 46px;
    border-radius: 8px;
    padding-left: 12px !important;

    transition: background .2s ease, box-shadow .2s ease;
}

::v-deep .menu-group > .v-list-group__header:hover {
    background:
        linear-gradient(90deg,
            rgba(245, 182, 59, 0.22) 0%,
            rgba(245, 182, 59, 0.08) 35%,
            rgba(11, 33, 66, 0.70) 100%) !important;
}

::v-deep .menu-group > .v-list-group__header .v-icon {
    color: rgba(255, 255, 255, 0.72);
    transition: color .2s ease;
}

::v-deep .menu-group > .v-list-group__header .menu-title {
    font-weight: 600;
}

::v-deep .menu-group > .v-list-group__header:hover .v-icon {
    color: #F5B63B !important;
}

::v-deep .menu-group > .v-list-group__header .v-list-group__header__append-icon .v-icon {
    font-size: 19px;
    color: rgba(245, 182, 59, 0.8);
    transition: transform .2s ease, color .2s ease;
}

::v-deep .menu-group.v-list-group--active > .v-list-group__header {
    background: rgba(245, 182, 59, 0.08) !important;
    box-shadow: inset 0 0 0 1px rgba(245, 182, 59, 0.2);
}

::v-deep .menu-group.v-list-group--active > .v-list-group__header .menu-title {
    color: #FFFFFF !important;
}

::v-deep .menu-group.v-list-group--active > .v-list-group__header .v-list-group__header__append-icon .v-icon {
    color: #F5B63B !important;
}

::v-deep .menu-group > .v-list-group__items {
    margin: 4px 0 0 18px;
    padding-left: 12px;
    border-left: 1px solid rgba(245, 182, 59, 0.25);
}

.menu-subitem {
    min-height: 40px;
    margin: 2px 0;
    margin-left: 0;
    padding-left: 10px !important;

    background: rgba(15, 45, 83, 0.24) !important;
    border-radius: 7px;
}

.menu-subitem .v-list-item__icon {
    margin-right: 14px !important;
}

.menu-subitem .v-icon {
    font-size: 18px;
    color: rgba(255, 255, 255, 0.62);
}

.menu-subitem .menu-title {
    font-size: 13px;
    color: rgba(255, 255, 255, 0.78) !important;
}

.menu-item {
    position: relative;

    min-height: 46px;

    margin: 4px 0;

    padding-left: 12px !important;

    border-radius: 8px;

    overflow: hidden;

    transition:
        background .2s ease,
        border-color .2s ease,
        transform .2s ease,
        box-shadow .2s ease;
}

.menu-item:hover {
    background:
        linear-gradient(90deg,
            rgba(245, 182, 59, 0.22) 0%,
            rgba(245, 182, 59, 0.08) 35%,
            rgba(11, 33, 66, 0.70) 100%) !important;

    transform: translateX(2px);
}

.menu-item.v-list-item--active {
    background:
        linear-gradient(90deg,
            rgba(245, 182, 59, 0.18) 0%,
            rgba(245, 182, 59, 0.07) 38%,
            rgba(15, 45, 83, 0.28) 100%) !important;

    border-left: 2px solid rgba(245, 182, 59, 0.75);

    box-shadow:
        inset 0 0 12px rgba(245, 182, 59, 0.025);
}

.menu-item .v-list-item__icon {
    margin-right: 18px !important;
}

.menu-item .v-icon {
    color: rgba(255, 255, 255, 0.72);

    font-size: 21px;

    transition:
        color .2s ease,
        transform .2s ease;
}

.menu-item:hover .v-icon {
    color: #F5B63B !important;
}

.menu-item.v-list-item--active .v-icon {
    color: #F5B63B !important;
}

.menu-title {
    color: rgba(255, 255, 255, 0.88) !important;

    font-size: 14px;
    font-weight: 500;

    transition: color .2s ease;
}

.menu-item:hover .menu-title {
    color: #FFFFFF !important;
}

.menu-item.v-list-item--active .menu-title {
    color: #FFFFFF !important;

    font-weight: 600;
}

.sidebar-footer {
    margin-top: auto;

    padding: 10px 18px 20px;
}

.logout-button {
    color: rgba(255, 255, 255, 0.78) !important;
    border-radius: 10px;
    letter-spacing: 0.02em;
    text-transform: none;
}

.logout-button:hover {
    color: #F5B63B !important;
    background: rgba(245, 182, 59, 0.1) !important;
}

.sidebar-footer-line {
    width: 100%;
    height: 1px;

    margin-bottom: 15px;

    background:
        linear-gradient(90deg,
            transparent 0%,
            rgba(245, 182, 59, 0.25) 20%,
            rgba(245, 182, 59, 0.80) 50%,
            rgba(245, 182, 59, 0.25) 80%,
            transparent 100%);
}

.sidebar-footer-content {
    display: flex;
    align-items: center;
    justify-content: center;

    color: rgba(255, 255, 255, 0.52);

    font-size: 11px;
    font-weight: 500;

    letter-spacing: 0.4px;
}

.sidebar-footer-content .v-icon {
    color: #F5B63B !important;

    opacity: 0.85;
}

::v-deep .v-list-item::before {
    opacity: 0 !important;
}

::v-deep .v-list-item--active::before,
::v-deep .theme--dark.v-list-item--active::before {
    opacity: 0 !important;
}
</style>

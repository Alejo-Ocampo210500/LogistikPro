<template>
    <div class="sidebar-wrapper">

        <v-list dense nav class="sidebar-menu">
            <v-list-item v-for="item in menuFiltrado" :key="item.title" :to="item.route" router exact class="menu-item">
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
        </v-list>

        <div class="sidebar-footer">

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
        }
    },

    computed: {
        menuFiltrado() {
            // Cuando actives permisos:
            //
            // return this.menu.filter(
            //     item => !item.permiso || this.$can(item.permiso)
            // )

            return this.menu
        },
    },
}
</script>

<style scoped>
.sidebar-wrapper {
    display: flex;
    flex-direction: column;

    height: 100%;
    min-height: 100%;

    background:
        radial-gradient(circle at 5% 4%,
            rgba(245, 182, 59, 0.55) 0%,
            rgba(245, 182, 59, 0.32) 12%,
            rgba(245, 182, 59, 0.14) 28%,
            transparent 48%),
        radial-gradient(circle at 100% 55%,
            rgba(34, 91, 166, 0.45) 0%,
            rgba(19, 55, 110, 0.18) 35%,
            transparent 55%),
        linear-gradient(155deg,
            #173B70 0%,
            #102D58 20%,
            #0B2142 42%,
            #07162D 70%,
            #061327 100%) !important;
}

.sidebar-menu {
    flex: 1;

    background: transparent !important;

    padding: 8px 12px 30px;
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
            rgba(245, 182, 59, 0.36) 0%,
            rgba(245, 182, 59, 0.15) 35%,
            rgba(15, 45, 83, 0.75) 100%) !important;

    border-left: 3px solid #F5B63B;

    box-shadow:
        inset 0 0 18px rgba(245, 182, 59, 0.05);
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

.menu-item.v-list-item--active::after {
    content: '';

    position: absolute;

    right: 8px;
    top: 50%;

    width: 5px;
    height: 5px;

    border-radius: 50%;

    background: #F5B63B;

    box-shadow:
        0 0 8px rgba(245, 182, 59, 0.75);

    transform: translateY(-50%);
}

.sidebar-footer {
    margin-top: auto;

    padding: 10px 18px 20px;
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
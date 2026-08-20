<template>
    <v-app dark class="logistik-app">

        <!-- SIDEBAR -->
        <v-navigation-drawer v-model="drawer" app :permanent="$vuetify.breakpoint.mdAndUp"
            :temporary="$vuetify.breakpoint.smAndDown" width="245" class="admin-drawer">
            <!-- CERRAR EN MOBILE -->
            <div class="drawer-mobile-close d-md-none">
                <v-btn icon color="white" aria-label="Cerrar menú" @click="drawer = false">
                    <v-icon>mdi-close</v-icon>
                </v-btn>
            </div>

            <!-- LOGO -->
            <div class="drawer-brand">
                <img src="/images/softnova.png" alt="Softnova">
            </div>

            <!-- MENÚ -->
            <Sidebar />

        </v-navigation-drawer>

        <!-- CONTENIDO -->
        <v-main class="admin-main">
            <Nuxt />
        </v-main>

        <!-- FOOTER -->
        <Footer />

    </v-app>
</template>

<script>
import Footer from '../components/footer.vue'
import Sidebar from '../components/Sidebar.vue'

export default {
    name: 'LogistikLayout',

    components: {
        Sidebar,
        Footer
    },

    data() {
        return {
            drawer: null
        }
    }
}
</script>

<style>
/* =========================================================
   APP
========================================================= */

html,
body,
#__nuxt,
#__layout {
    background: #07162D;
}

.logistik-app {
    background: #07162D !important;
    color: #243b53;
}


/* =========================================================
   SIDEBAR
========================================================= */

.admin-drawer {
    background:
        radial-gradient(circle at 15% 5%,
            rgba(245, 182, 59, 0.11) 0%,
            rgba(245, 182, 59, 0.04) 18%,
            transparent 30%),
        radial-gradient(circle at 25% 15%,
            rgba(22, 65, 124, 0.45) 0%,
            rgba(11, 33, 66, 0.20) 32%,
            transparent 48%),
        linear-gradient(180deg,
            #0B2142 0%,
            #07162D 48%,
            #061327 100%) !important;

    border-right: 1px solid rgba(255, 255, 255, 0.08) !important;

    box-shadow:
        8px 0 30px rgba(0, 0, 0, 0.18),
        15px 0 50px rgba(0, 0, 0, 0.08);

    z-index: 210 !important;
}


/* =========================================================
   BOTÓN CERRAR MOBILE
========================================================= */

.drawer-mobile-close {
    display: flex;
    justify-content: flex-end;
    padding: 10px 10px 0;
}


/* =========================================================
   LOGO
========================================================= */

.drawer-brand {
    border-bottom: 1px solid rgba(255, 255, 255, 0.10);
    margin: 0 14px 10px;
    padding: 20px 10px 18px;
    text-align: center;
}

.drawer-brand img {
    width: 172px;
    height: 112px;

    display: block;
    margin: 0 auto;

    object-fit: contain;
}

.drawer-brand span {
    color: #FFFFFF;

    display: block;

    font-size: 15px;
    font-weight: 700;

    margin-top: 8px;
}

.drawer-brand small {
    color: rgba(255, 255, 255, 0.72);

    display: block;

    font-size: 11px;

    margin-top: 2px;
}


/* =========================================================
   PARTE INFERIOR SIDEBAR
========================================================= */

.drawer-bottom {
    position: absolute;

    bottom: 18px;
    left: 18px;
    right: 18px;
}


/* =========================================================
   MAIN
========================================================= */

.admin-main {
    background: #F4F7FB !important;
}


/* =========================================================
   TOP BAR TITLE
========================================================= */

.topbar-title span,
.topbar-title small {
    display: block;
}

.topbar-title span {
    color: #1f2933;

    font-size: 18px;
    font-weight: 700;
}

.topbar-title small {
    color: #6b7280;

    font-size: 12px;

    margin-top: 1px;
}


/* =========================================================
   TOP BAR USER
========================================================= */

.topbar-user {
    align-items: center;

    border-right: 1px solid #e2e8f0;

    gap: 10px;

    margin-right: 4px;
    padding-right: 14px;
}

.topbar-user .v-avatar span {
    color: #FFFFFF;

    font-size: 13px;
    font-weight: 950;
}

.topbar-user strong,
.topbar-user small {
    display: block;
    line-height: 1.1;
}

.topbar-user strong {
    color: #17365d;

    font-size: 13px;
    font-weight: 900;

    max-width: 150px;

    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.topbar-user small {
    color: #7b8794;

    font-size: 11px;

    margin-top: 3px;
}


/* =========================================================
   VUETIFY
========================================================= */

.admin-drawer .v-navigation-drawer__content {
    background: transparent !important;
}

.admin-drawer .v-list {
    background: transparent !important;
}


/* =========================================================
   SCROLLBAR SIDEBAR
========================================================= */

.admin-drawer .v-navigation-drawer__content::-webkit-scrollbar {
    width: 5px;
}

.admin-drawer .v-navigation-drawer__content::-webkit-scrollbar-track {
    background: transparent;
}

.admin-drawer .v-navigation-drawer__content::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.14);
    border-radius: 10px;
}

.admin-drawer .v-navigation-drawer__content::-webkit-scrollbar-thumb:hover {
    background: rgba(245, 182, 59, 0.45);
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 959px) {

    .admin-drawer {
        box-shadow: 14px 0 40px rgba(0, 0, 0, 0.35) !important;
    }

    .drawer-brand {
        padding-top: 5px;
    }

    .drawer-brand img {
        width: 155px;
        height: 95px;
    }
}
</style>
<template>
    <div>

        <v-app-bar app flat color="#07162D" height="82" class="px-md-8">
            <v-container class="d-flex align-center py-0 header-main-row">
                <a href="/" class="d-flex align-center">
                    <v-img src="/branding/logoPrincipal.png" contain max-width="65" max-height="65" />
                </a>

                <div class="d-flex align-center justify-center brand-wordmark" aria-label="LogistikPro">
                    <span class="wordmark-logistik">Logistik</span><span class="wordmark-pro">Pro</span>
                </div>

                <v-spacer />

                <div class="d-none d-md-flex align-center">
                    <v-btn v-for="item in navItems" :key="item.text" :href="item.href" text dark class="mx-1">
                        {{ item.text }}
                    </v-btn>

                    <v-btn rounded class="ml-4 px-6 brand-gold-btn" nuxt to="/login">
                        Iniciar sesión
                    </v-btn>
                </div>

                <!-- Botón móvil -->
                <v-btn icon dark class="d-md-none" @click="drawer = true">
                    <v-icon>mdi-menu</v-icon>
                </v-btn>
            </v-container>
        </v-app-bar>

        <!-- DRAWER MÓVIL -->
        <v-navigation-drawer v-model="drawer" temporary app right color="#07162D" width="300">
            <div class="pa-6">
                <v-img src="/branding/isotipo_sin_fondo.png" contain max-width="92" class="mx-auto" />
            </div>

            <v-divider dark />

            <v-list nav dense color="transparent" class="mt-4">
                <v-list-item v-for="item in navItems" :key="item.text" :href="item.href" @click="drawer = false">
                    <v-list-item-icon>
                        <v-icon dark>
                            {{ item.icon }}
                        </v-icon>
                    </v-list-item-icon>

                    <v-list-item-content>
                        <v-list-item-title class="white--text">
                            {{ item.text }}
                        </v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
            </v-list>

            <div class="pa-6">
                <v-btn block x-large rounded class="brand-gold-btn" nuxt to="/login">
                    Iniciar sesión
                </v-btn>
            </div>
        </v-navigation-drawer>
    </div>
</template>

<script>
export default {
    name: 'HeaderLayout',

    data() {
        return {
            drawer: false,

            navItems: [
                {
                    text: 'Inicio',
                    href: '#inicio',
                    icon: 'mdi-home-outline'
                },
                {
                    text: 'Plataforma',
                    href: '#plataforma',
                    icon: 'mdi-view-dashboard-outline'
                },
                {
                    text: 'Planes',
                    href: '#planes',
                    icon: 'mdi-currency-usd'
                },
                {
                    text: 'Contacto',
                    href: '#contacto',
                    icon: 'mdi-email-outline'
                }
            ]
        }
    }
}
</script>

<style scoped>
.v-app-bar {
    border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
}

.v-btn {
    text-transform: none;
    letter-spacing: 0;
}

.header-main-row {
    position: relative;
}

.brand-gold-btn {
    background: linear-gradient(135deg, #f4c96b 0%, #d9a441 100%) !important;
    color: #13294b !important;
    font-weight: 700;
    box-shadow: 0 10px 24px rgba(217, 164, 65, 0.3);
}

.brand-gold-btn:hover {
    filter: brightness(1.04);
}

.brand-wordmark {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    font-size: 2rem;
    font-weight: 900;
    letter-spacing: -0.04em;
    white-space: nowrap;
    line-height: 0.98;
    text-shadow: 0 10px 20px rgba(2, 10, 22, 0.3);
    pointer-events: none;
}

.wordmark-logistik {
    color: #ffffff;
}

.wordmark-pro {
    color: #f4b640;
}

@media (max-width: 1264px) {
    .brand-wordmark {
        font-size: 1.7rem;
    }
}

@media (max-width: 960px) {
    .brand-wordmark {
        font-size: 1.45rem;
        letter-spacing: -0.03em;
    }
}

@media (max-width: 420px) {
    .brand-wordmark {
        font-size: 1.3rem;
    }
}
</style>

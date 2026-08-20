Login 

PASSPORT  backend 

rutas de logueo y cierre de sesion 

Token de passport al iniciar sesion tiene que guardar en session storage , crear middleware llamado auth.js para validar el session storage 

al loguear ir a una pagina que se llame /logistikPro que debe estar creada en pages dentro de una carpeta logistik 



<template>
   <v-app dark class="logistik-app">
       <v-navigation-drawer v-model="drawer" app :permanent="$vuetify.breakpoint.mdAndUp"
           :temporary="$vuetify.breakpoint.smAndDown" width="245" class="admin-drawer">
           <div class="drawer-mobile-close d-md-none">
               <v-btn icon color="white" aria-label="Cerrar menú" @click="drawer = false">
                   <v-icon>mdi-close</v-icon>
               </v-btn>
           </div>


           <div class="drawer-brand">
               <img src="/images/softnova.png" alt="Softnova">
           </div>


           <Sidebar />


       </v-navigation-drawer>
       <v-main>
           <Nuxt />
       </v-main>


       <Footer />
   </v-app>
</template>


<script>
import Footer from '../components/footer.vue';
import Sidebar from '../components/Sidebar.vue';
export default {
   name: 'LogistikLayout',


   components: {
       Sidebar,
       Footer
   }


}
</script>


<style>
.admin-shell {
   color: #243b53;
}


.admin-drawer {
   background:
       radial-gradient(circle at 18% 8%, rgba(114, 237, 240, .18), transparent 30%),
       linear-gradient(180deg, #0f2c61 0%, #155f56 42%, #0c5a4f 100%) !important;
   border-right: 1px solid rgba(255, 255, 255, .12) !important;
   box-shadow: 12px 0 34px rgba(8, 37, 63, .16);
   z-index: 210 !important;
}


.drawer-mobile-close {
   display: flex;
   justify-content: flex-end;
   padding: 10px 10px 0;
}


.drawer-brand {
   border-bottom: 1px solid rgba(255, 255, 255, .12);
   margin: 0 14px 10px;
   padding: 20px 10px 18px;
   text-align: center;
}


.drawer-brand img {
   height: 112px;
   margin: 0 auto;
   object-fit: contain;
   width: 172px;
}


.drawer-brand span {
   color: #fff;
   display: block;
   font-size: 15px;
   font-weight: 700;
   margin-top: 8px;
}


.drawer-brand small {
   color: rgba(255, 255, 255, .72);
   display: block;
   font-size: 11px;
   margin-top: 2px;
}


.drawer-bottom {
   bottom: 18px;
   left: 18px;
   position: absolute;
   right: 18px;
}


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


.topbar-user {
   align-items: center;
   border-right: 1px solid #e2e8f0;
   gap: 10px;
   margin-right: 4px;
   padding-right: 14px;
}


.topbar-user .v-avatar span {
   color: #fff;
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
</style>


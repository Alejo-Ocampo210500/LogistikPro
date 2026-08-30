const menu = [
    {
        title: 'Inicio',
        icon: 'mdi-view-dashboard-outline',
        route: '/inicio/principal',
        // permiso: 'panel.ver',
    },
    {
        title: 'Inventario',
        icon: 'mdi-package-variant-closed',
        permiso: 'inventario.ver',
        children: [
            {
                title: 'Productos',
                icon: 'mdi-package-variant-closed',
                route: '/modulo-parametrizacion/productos',
                permiso: 'productos.ver',
            },
            {
                title: 'Categorías',
                icon: 'mdi-format-list-bulleted-type',
                route: '/modulo-parametrizacion/categorias',
                permiso: 'categorias.ver',
            },
            {
                title: 'Marcas',
                icon: 'mdi-tag-multiple-outline',
                route: '/modulo-parametrizacion/marcas',
                permiso: 'marcas.ver',
            },
            {
                title: 'Bodegas',
                icon: 'mdi-warehouse',
                route: '/modulo-inventario/bodegas',
                permiso: 'bodegas.ver',
            },
            {
                title: 'Kardex',
                icon: 'mdi-book-open-page-variant-outline',
                route: '/modulo-inventario/kardex',
                permiso: 'kardex.ver',
            },
        ],
    },
    {
        title: 'Sitio público',
        icon: 'mdi-palette-outline',
        route: '/modulo-parametrizacion/sitio-publico',
        permiso: 'administrar-sitio',
    },
    {
        title: 'Gestión de Contenido',
        icon: 'mdi-image-multiple-outline',
        route: '/modulo-parametrizacion/imagenes',
        permiso: 'imagenes.ver',
    },
    {
        title: 'Usuarios',
        icon: 'mdi-account-group-outline',
        route: '/usuarios/gestion-usuarios',
        permiso: 'usuarios.ver',
    },
    {
        title: 'Roles y permisos',
        icon: 'mdi-shield-account-outline',
        route: '/modulo-parametrizacion/roles',
        permiso: 'roles.ver',
    },
    {
        title: 'Configuración General',
        icon: 'mdi-cog',
        route: '/configuracion/empresa',
        permiso: 'configuracion.empresa.ver',
    },
      {
        title: 'Admin',
        icon: 'mdi-cog',
        route: '/configuracion/empresa',
        permiso: 'configuracion.empresa.ver',
    },
]

export default menu

const menu = [
    {
        title: 'Inicio',
        icon: 'mdi-view-dashboard-outline',
        route: '/modulo-parametrizacion',
        permiso: 'panel.ver',
    },
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
        route: '/modulo-parametrizacion/usuarios',
        permiso: 'usuarios.ver',
    },
    {
        title: 'Roles y permisos',
        icon: 'mdi-shield-account-outline',
        route: '/modulo-parametrizacion/roles',
        permiso: 'roles.ver',
    },
]

export default menu
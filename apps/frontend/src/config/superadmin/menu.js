const superadminMenu = [
  {
    id: 'inicio',
    label: 'Inicio',
    subtitle: 'Dashboard',
    children: []
  },
  {
    id: 'empresas',
    label: 'Empresas',
    subtitle: 'Gestión de empresas',
    children: [
      {
        id: 'empresas-listado',
        label: 'Listado de Empresas',
      },
      {
        id: 'empresas-crear',
        label: 'Crear Empresas',
      },
      // {
      //   id: 'empresas-editar',
      //   label: 'Editar Empresas',
      // },
      {
        id: 'empresas-suscripciones',
        label: 'Suscripciones',
      },
      {
        id: 'empresas-pagos',
        label: 'Pagos',
      },
      {
        id: 'mesa-de-ayuda',
        label: 'Mesa de ayuda',
      },
      {
        id: 'empresas-suspendidas',
        label: 'Empresas Suspendidas',
      },
      {
        id: 'empresas-vencidas',
        label: 'Empresas Vencidas',
      },
    ]
  },
  {
    id: 'mantenimiento',
    label: 'Mantenimiento',
    subtitle: 'Alta y cambios',
    children: [
      {
        id: 'usuarios-globales',
        label: 'Usuarios Globales',
      },
      {
        id: 'modulos',
        label: 'Módulos del Sistema',
      },
      {
        id: 'empresas-planes',
        label: 'Planes',
      },
      {
        id: 'configuracion',
        label: 'Configuración General',
      },
      {
        id: 'roles-globales',
        label: 'Roles Globales',
      },
      {
        id: 'permisos',
        label: 'Permisos',
      },
      {
        id: 'metodos-pago',
        label: 'Métodos de Pago',
      },
      {
        id: 'estados-sistema',
        label: 'Estados del Sistema',
      },
    ]
  },
  {
    id: 'facturacion',
    label: 'Facturación',
    subtitle: 'Facturación',
    children: [
      {
        id: 'facturacion-listado',
        label: 'Facturas',
      },
      {
        id: 'historial-pagos',
        label: 'Historial de Pagos',
      },
      {
        id: 'ingresos',
        label: 'Ingresos',
      },
      {
        id: 'cupones',
        label: 'Cupones',
      },
      {
        id: 'promociones',
        label: 'Promociones',
      },
      {
        id: 'facturas-por-cobrar',
        label: 'Facturas por cobrar',
      },
      {
        id: 'notas-credito',
        label: 'Notas Crédito',
      },
      {
        id: 'reembolsos',
        label: 'Reembolsos',
      },
    ]
  },
  {
    id: 'reportes',
    label: 'Reportes',
    subtitle: 'Reportes',
    children: [
      {
        id: 'ingresos-reportes',
        label: 'Ingresos',
      },
      {
        id: 'historial-renovaciones',
        label: 'Historial de Renovaciones',
      },
      {
        id: 'historial-suscripciones',
        label: 'Historial de Suscripciones',
      },
      {
        id: 'reporte-empresas',
        label: 'Empresas',
      },
      {
        id: 'reporte-facturacion',
        label: 'Facturación',
      },
      {
        id: 'reporte-uso',
        label: 'Uso del Sistema',
      },
      {
        id: 'reporte-planes',
        label: 'Planes Más Vendidos',
      },
    ]
  },
  {
    id: 'supervisor',
    label: 'Supervisor',
    subtitle: 'Detalle total',
    children: [
      // {
      //   id: 'auditoria',
      //   label: 'Auditoría',
      // },
      {
        id: 'logs',
        label: 'Logs del Sistema',
      },
      {
        id: 'estadisticas',
        label: 'Estadísticas Globales',
      },
      {
        id: 'soporte',
        label: 'Tickets y Soporte',
      },
      {
        id: 'auditoria',
        label: 'Auditoría',
      },
      {
        id: 'alertas',
        label: 'Alertas del Sistema',
      },
      {
        id: 'respaldos',
        label: 'Respaldos',
      },
      {
        id: 'monitor-servidor',
        label: 'Monitor del Servidor',
      },
      {
        id: 'colas',
        label: 'Cola de Procesos',
      },
      {
        id: 'errores',
        label: 'Registro de Errores',
      },
    ]
  },
  {
    id: 'notificaciones',
    label: 'Notificaciones',
    subtitle: 'Centro de alertas',
    children: [
      {
        id: 'notificaciones-sistema',
        label: 'Notificaciones',
      },
      {
        id: 'renovaciones-proximas',
        label: 'Renovaciones Próximas',
      },
      {
        id: 'pagos-pendientes',
        label: 'Pagos Pendientes',
      },
      {
        id: 'incidencias',
        label: 'Incidencias',
      },
    ]
  },
  {
    id: 'seguridad',
    label: 'Seguridad',
    subtitle: 'Control de acceso',
    children: [
      {
        id: 'sesiones',
        label: 'Sesiones Activas',
      },
      {
        id: 'accesos',
        label: 'Historial de Accesos',
      },
      {
        id: 'bloqueos',
        label: 'Bloqueos',
      },
    ]
  },

];

export default superadminMenu;

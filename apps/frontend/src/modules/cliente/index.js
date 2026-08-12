import ModuleWorkspace from '@/modules/cliente/shared/ModuleWorkspace.vue';
import DashboardResumenView from '@/modules/cliente/dashboard/DashboardResumenView.vue';
import DashboardWorkspaceView from '@/modules/cliente/dashboard/DashboardWorkspaceView.vue';
import PosWorkspaceView from '@/modules/cliente/pos/PosWorkspaceView.vue';
import InventarioWorkspaceView from '@/modules/cliente/inventario/InventarioWorkspaceView.vue';
import InventarioCategroiasView from '@/modules/cliente/inventario/InventarioCategroias.vue';
import InventarioMarcasView from '@/modules/cliente/inventario/InventarioMarcas.vue';
import ComprasWorkspaceView from '@/modules/cliente/compras/ComprasWorkspaceView.vue';
import ClientesWorkspaceView from '@/modules/cliente/clientes/ClientesWorkspaceView.vue';
import ProveedoresWorkspaceView from '@/modules/cliente/proveedores/ProveedoresWorkspaceView.vue';
import CajaWorkspaceView from '@/modules/cliente/caja/CajaWorkspaceView.vue';
import FinanzasWorkspaceView from '@/modules/cliente/finanzas/FinanzasWorkspaceView.vue';
import ReportesWorkspaceView from '@/modules/cliente/reportes/ReportesWorkspaceView.vue';
import MarketingWorkspaceView from '@/modules/cliente/marketing/MarketingWorkspaceView.vue';
import EmpleadosWorkspaceView from '@/modules/cliente/empleados/EmpleadosWorkspaceView.vue';
import EmpresaWorkspaceView from '@/modules/cliente/empresa/EmpresaWorkspaceView.vue';
import NotificacionesWorkspaceView from '@/modules/cliente/notificaciones/NotificacionesWorkspaceView.vue';
import ConfiguracionWorkspaceView from '@/modules/cliente/configuracion/ConfiguracionWorkspaceView.vue';
import InventarioUnidadesView from '@/modules/cliente/inventario/InventarioUnidadesView.vue';
import ProvedoresListarView from '@/modules/cliente/proveedores/ProvedoresListarView.vue';
import InventarioProductosView from '@/modules/cliente/inventario/InventarioProductosView.vue';
import ClientesCrearView from '@/modules/cliente/clientes/ClientesCrearView.vue';
import EmpresaSucursalesView from '@/modules/cliente/empresa/EmpresaSucursalesView.vue';
import CajaListarView from '@/modules/cliente/caja/CajaListarView.vue';
import CajaControlView from '@/modules/cliente/caja/CajaControlView.vue';
import InventarioCodigosBarrasView from '@/modules/cliente/inventario/InventarioCodigosBarrasView.vue';
import PostNuevaVentaView from '@/modules/cliente/pos/PostNuevaVentaView.vue';

const prefixRegistry = [
  { prefix: 'dashboard-', component: DashboardWorkspaceView },
  { prefix: 'pos-', component: PosWorkspaceView },
  { prefix: 'inventario-', component: InventarioWorkspaceView },
  { prefix: 'compras-', component: ComprasWorkspaceView },
  { prefix: 'clientes-', component: ClientesWorkspaceView },
  { prefix: 'proveedores-', component: ProveedoresWorkspaceView },
  { prefix: 'caja-', component: CajaWorkspaceView },
  { prefix: 'finanzas-', component: FinanzasWorkspaceView },
  { prefix: 'reportes-', component: ReportesWorkspaceView },
  { prefix: 'marketing-', component: MarketingWorkspaceView },
  { prefix: 'empleados-', component: EmpleadosWorkspaceView },
  { prefix: 'empresa-', component: EmpresaWorkspaceView },
  { prefix: 'notificaciones-', component: NotificacionesWorkspaceView },
  { prefix: 'configuracion-', component: ConfiguracionWorkspaceView },
];

export function resolveClientModuleComponent(moduleId) {
  if (!moduleId) {
    return ModuleWorkspace;
  }

  if (moduleId === 'dashboard-resumen') {
    return DashboardResumenView;
  }

  if (moduleId === 'inventario-categorias') {
    return InventarioCategroiasView;
  }

  if (moduleId === 'clientes-crear' || moduleId === 'clientes-lista') {
    return ClientesCrearView;
  }

  if (moduleId === 'inventario-marcas') {
    return InventarioMarcasView;
  }

  if (moduleId === 'inventario-unidades') {
    return InventarioUnidadesView;
  }

  if (moduleId === 'proveedores-lista') {
    return ProvedoresListarView;
  }

  if (moduleId === 'inventario-productos'){
    return InventarioProductosView;
  }

  if(moduleId === 'empresa-sucursales'){
    return EmpresaSucursalesView;
  }

  if (moduleId === 'caja-cajas') {
    return CajaListarView;
  }

  if(moduleId === 'caja-control'){
    return CajaControlView;
  }

  if(moduleId === 'inventario-codigos-barras'){
    return InventarioCodigosBarrasView;
  }

  if(moduleId === 'pos-nueva-venta'){
    return PostNuevaVentaView;
  }

  const found = prefixRegistry.find(entry => moduleId.startsWith(entry.prefix));
  return found ? found.component : ModuleWorkspace;
}

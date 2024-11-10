import AdminDashboard from '../components/admin/AdminDashboard.vue'
import AdminLogin from '../components/admin/AdminLogin.vue'
import Reports from '../components/admin/Reports.vue'
import SecuritySettings from '../components/admin/SecuritySettings.vue'
import MaintenanceMode from '../components/admin/MaintenanceMode.vue'

export const adminRoutes = [
  {
    path: '/admin',
    redirect: '/admin/login'
  },
  {
    path: '/admin/login',
    name: 'admin-login',
    component: AdminLogin,
    meta: { requiresAuth: false }
  },
  {
    path: '/admin/dashboard',
    name: 'admin-dashboard',
    component: AdminDashboard,
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/admin/reports',
    name: 'admin-reports',
    component: Reports,
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/admin/security',
    name: 'admin-security',
    component: SecuritySettings,
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/admin/maintenance',
    name: 'admin-maintenance',
    component: MaintenanceMode,
    meta: { requiresAuth: true, requiresAdmin: true }
  }
]

// Navigation guard for admin routes
export function setupAdminNavigationGuards(router) {
  router.beforeEach((to, from, next) => {
    const requiresAuth = to.matched.some(record => record.meta.requiresAuth)
    const requiresAdmin = to.matched.some(record => record.meta.requiresAdmin)
    const isLoggedIn = !!localStorage.getItem('admin_token')
    
    if (requiresAuth && !isLoggedIn) {
      next('/admin/login')
    } else if (requiresAdmin && !isLoggedIn) {
      next('/admin/login')
    } else if (to.path === '/admin/login' && isLoggedIn) {
      next('/admin/dashboard')
    } else {
      next()
    }
  })
}
import { createRouter, createWebHistory } from 'vue-router'
import Login from '../views/Login.vue'
import Dashboard from '../views/Dashboard.vue'
import AdminUsers from '../views/admin/AdminUsers.vue'
import AdminClasses from '../views/admin/AdminClasses.vue'
import AdminSchedules from '../views/admin/AdminSchedules.vue'

const routes = [
  { path: '/login', name: 'login', component: Login, meta: { requiresGuest: true } },
  { path: '/', name: 'dashboard', component: Dashboard, meta: { requiresAuth: true } },
  { path: '/admin/users', name: 'admin-users', component: AdminUsers, meta: { requiresAuth: true, role: 'Admin' } },
  { path: '/admin/classes', name: 'admin-classes', component: AdminClasses, meta: { requiresAuth: true, role: 'Admin' } },
  { path: '/admin/schedules', name: 'admin-schedules', component: AdminSchedules, meta: { requiresAuth: true, role: 'Admin' } },
  { path: '/:pathMatch(.*)*', redirect: '/' },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to, _from, next) => {
  const token = localStorage.getItem('token')
  const role = localStorage.getItem('role')

  if (to.meta.requiresAuth && !token) {
    next({ name: 'login' })
  } else if (to.meta.requiresGuest && token) {
    next({ name: 'dashboard' })
  } else if (to.meta.role && to.meta.role !== role) {
    next({ name: 'dashboard' })
  } else {
    next()
  }
})

export default router

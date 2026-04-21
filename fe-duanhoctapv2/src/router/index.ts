import { createRouter, createWebHistory } from 'vue-router'
import Login from '../views/Login.vue'
import Dashboard from '../views/Dashboard.vue'
import AdminUsers from '../views/admin/AdminUsers.vue'
import AdminClasses from '../views/admin/AdminClasses.vue'
import AdminSchedules from '../views/admin/AdminSchedules.vue'
import AdminTuitions from '../views/admin/AdminTuitions.vue'

const routes = [
  { path: '/login', name: 'login', component: Login, meta: { requiresGuest: true } },
  { path: '/', name: 'dashboard', component: Dashboard, meta: { requiresAuth: true } },
  { path: '/profile', name: 'profile', component: () => import('../views/Profile.vue'), meta: { requiresAuth: true } },
  { path: '/admin/users', name: 'admin-users', component: AdminUsers, meta: { requiresAuth: true, role: 'Admin' } },
  { path: '/admin/users/:id', name: 'admin-user-details', component: () => import('../views/admin/AdminUserDetails.vue'), meta: { requiresAuth: true, role: 'Admin' } },
  { path: '/admin/subjects', name: 'admin-subjects', component: () => import('../views/admin/AdminSubjects.vue'), meta: { requiresAuth: true, role: 'Admin' } },
  { path: '/admin/study-slots', name: 'admin-study-slots', component: () => import('../views/admin/AdminStudySlots.vue'), meta: { requiresAuth: true, role: 'Admin' } },
  { path: '/admin/classes', name: 'admin-classes', component: AdminClasses, meta: { requiresAuth: true, role: 'Admin' } },
  { path: '/admin/schedules', name: 'admin-schedules', component: AdminSchedules, meta: { requiresAuth: true, role: 'Admin' } },
  { path: '/admin/schedules/:id', name: 'admin-class-schedules', component: () => import('../views/admin/AdminClassSchedules.vue'), meta: { requiresAuth: true, role: 'Admin' } },
  { path: '/admin/tuitions', name: 'admin-tuitions', component: AdminTuitions, meta: { requiresAuth: true, role: 'Admin' } },
  { path: '/:pathMatch(.*)*', redirect: '/' },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to, _from, next) => {
  const token = localStorage.getItem('token')
  const role = localStorage.getItem('role')
  if (to.meta.requiresAuth && !token) return next({ name: 'login' })
  if (to.meta.requiresGuest && token) return next({ name: 'dashboard' })
  if (to.meta.role && to.meta.role !== role) return next({ name: 'dashboard' })
  next()
})

export default router

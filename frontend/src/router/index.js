import { createRouter, createWebHistory } from 'vue-router';
import HomeView from '../views/HomeView.vue';
import AdminLayout from '../layouts/AdminLayout.vue';

// Helper functions for Auth
const getToken = () => localStorage.getItem('token');
const getUser = () => {
    const user = localStorage.getItem('user');
    return user ? JSON.parse(user) : null;
};

const routes = [
  // ── WEB ──
  {
    path: '/',
    name: 'home',
    component: HomeView
  },

  // ── AUTH ──
  {
    path: '/login',
    name: 'login',
    component: () => import('../views/LoginView.vue')
  },

  // ── ADMIN ──
  {
    path: '/admin',
    component: AdminLayout,
    meta: { requiresAdmin: true },
    children: [
      {
        path: 'dashboard',
        name: 'admin-dashboard',
        component: () => import('../views/admin/AdminDashboard.vue')
      },
      {
        path: 'users',
        name: 'admin-users',
        component: () => import('../views/admin/UsersView.vue')
      },
      {
        path: 'students',
        name: 'admin-students',
        component: () => import('../views/admin/StudentsView.vue')
      },
      {
        path: 'guardians',
        name: 'admin-guardians',
        component: () => import('../views/admin/GuardiansView.vue')
      },
      {
        path: 'teachers',
        name: 'admin-teachers',
        component: () => import('../views/admin/TeachersView.vue')
      },
      {
        path: 'subjects',
        name: 'admin-subjects',
        component: () => import('../views/admin/SubjectsView.vue')
      },
      {
        path: 'classrooms',
        name: 'admin-classrooms',
        component: () => import('../views/admin/ClassroomsView.vue')
      },
      {
        path: 'classes',
        name: 'admin-classes',
        component: () => import('../views/admin/ClassesView.vue')
      },
      {
        path: 'schedules',
        name: 'admin-schedules',
        component: () => import('../views/admin/SchedulesView.vue')
      },
      {
        path: 'enrollments',
        name: 'admin-enrollments',
        component: () => import('../views/admin/EnrollmentsView.vue')
      },
      {
        path: 'tuition',
        name: 'admin-tuition',
        component: () => import('../views/admin/TuitionView.vue')
      },
      {
        path: 'attendance',
        name: 'admin-attendance',
        component: () => import('../views/admin/AttendanceView.vue')
      },
      {
        path: 'audit',
        name: 'admin-audit',
        component: () => import('../views/admin/SystemAuditView.vue')
      },
    ]
  },

  // ── 404 fallback ──
  { path: '/:pathMatch(.*)*', redirect: '/' },
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
});

// ── NAVIGATION GUARD ──
router.beforeEach((to, from, next) => {
  const user = getUser();
  const token = getToken();

  const publicPages = ['/', '/login'];
  const isPublic = publicPages.includes(to.path);

  // 1. Nếu trang không công khai và không có token -> Về login
  if (!isPublic && !token) {
    return next({ name: 'login' });
  }

  // 2. Nếu đã đăng nhập mà cố vào login -> Vào dashboard
  if (to.name === 'login' && token) {
    return next({ name: 'admin-dashboard' });
  }

  // 3. Kiểm tra quyền Admin dựa trên Meta
  if (to.matched.some(route => route.meta.requiresAdmin)) {
    if (!user || !token) {
      return next({ name: 'login' });
    }
    if (user.role !== 'admin') {
      alert('Bạn không có quyền truy cập vào khu vực này!');
      return next({ name: 'home' });
    }
  }

  next();
});

export default router;

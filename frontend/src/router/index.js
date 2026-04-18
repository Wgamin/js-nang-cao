import { createRouter, createWebHistory } from 'vue-router';
import HomeView from '../views/HomeView.vue';
import AdminLayout from '../layouts/AdminLayout.vue';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('../views/LoginView.vue')
    },
    {
      path: '/admin',
      component: AdminLayout,
      redirect: '/admin/dashboard',
      children: [
        {
          path: 'dashboard',
          name: 'admin-dashboard',
          component: () => import('../views/admin/AdminDashboard.vue')
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
          path: 'students',
          name: 'admin-students',
          component: () => import('../views/admin/StudentsView.vue')
        },
        // Placeholder components for remaining management routes
        {
          path: 'users',
          name: 'admin-users',
          component: () => import('../views/admin/UsersView.vue')
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
          path: 'audit',
          name: 'admin-audit',
          component: () => import('../views/admin/SystemAuditView.vue')
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
      ]
    }
  ]
});

export default router;

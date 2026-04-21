<script setup lang="ts">
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'

const role = ref(localStorage.getItem('role') || '')
const userName = ref(localStorage.getItem('userName') || '')

const isAdmin = computed(() => role.value === 'Admin')
const isTeacher = computed(() => role.value === 'Teacher')
const isParent = computed(() => role.value === 'Parent')

const router = useRouter()
const logout = async () => {
  const token = localStorage.getItem('token')
  try {
    await fetch('http://localhost:8000/api/logout', {
      method: 'POST',
      headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
    })
  } catch {}
  localStorage.clear()
  router.push('/login')
}
</script>

<template>
  <div class="layout">
    <aside class="sidebar">
      <div class="sidebar-logo">🎓 Học Thêm Pro</div>
      <nav class="sidebar-nav">
        <router-link class="nav-item" to="/" exact-active-class="nav-item--active">
          <span>🏠</span> Trang Chủ
        </router-link>

        <template v-if="isAdmin">
          <div class="nav-section">QUẢN LÝ HỆ THỐNG</div>
          <router-link class="nav-item" to="/admin/users" active-class="nav-item--active">
            <span>👥</span> Quản Lý Users
          </router-link>
          <router-link class="nav-item" to="/admin/subjects" active-class="nav-item--active">
            <span>📚</span> Quản Lý Môn Học
          </router-link>
          <router-link class="nav-item" to="/admin/study-slots" active-class="nav-item--active">
            <span>🕒</span> Quản Lý Ca Học
          </router-link>
          <router-link class="nav-item" to="/admin/classes" active-class="nav-item--active">
            <span>🎓</span> Quản Lý Lớp Học
          </router-link>
          <router-link class="nav-item" to="/admin/schedules" active-class="nav-item--active">
            <span>📅</span> Quản Lý Lịch Học
          </router-link>
          <router-link class="nav-item" to="/admin/tuitions" active-class="nav-item--active">
            <span>💰</span> Quản Lý Học Phí
          </router-link>
        </template>

        <template v-if="isTeacher">
          <div class="nav-section">GIÁO VIÊN</div>
          <router-link class="nav-item" to="/" active-class="nav-item--active">
            <span>📋</span> Lớp & Điểm Danh
          </router-link>
        </template>

        <template v-if="isParent">
          <div class="nav-section">PHỤ HUYNH</div>
          <router-link class="nav-item" to="/" active-class="nav-item--active">
            <span>👨‍👩‍👧</span> Thông Tin Con Em
          </router-link>
        </template>
      </nav>
      <div class="sidebar-footer">
        <div class="user-info">
          <div class="user-avatar">{{ userName.charAt(0).toUpperCase() }}</div>
          <div>
            <div class="user-name">{{ userName }}</div>
            <span class="badge" :class="role === 'Admin' ? 'badge-admin' : role === 'Teacher' ? 'badge-teacher' : role === 'Parent' ? 'badge-parent' : 'badge-student'">
              {{ role === 'Parent' ? 'Phụ Huynh' : role }}
            </span>
          </div>
        </div>
        <button id="logout-btn" class="btn btn-danger btn-sm" style="width:100%;justify-content:center;margin-top:12px" @click="logout">
          🚪 Đăng xuất
        </button>
      </div>
    </aside>
    <main class="main-content">
      <slot />
    </main>
  </div>
</template>

<style scoped>
.layout { display: flex; min-height: 100vh; }
.sidebar {
  width: 260px;
  background: #312e81;
  display: flex; flex-direction: column;
  position: sticky; top: 0; height: 100vh; overflow-y: auto; flex-shrink: 0;
}
.sidebar-logo { color: #fff; font-size: 18px; font-weight: 800; padding: 24px 20px 18px; border-bottom: 1px solid rgba(255,255,255,0.1); }
.sidebar-nav { flex: 1; padding: 12px; display: flex; flex-direction: column; gap: 2px; }
.nav-section { font-size: 10px; font-weight: 700; color: rgba(255,255,255,0.4); letter-spacing: 0.1em; padding: 12px 14px 4px; text-transform: uppercase; }
.nav-item { display: flex; align-items: center; gap: 10px; padding: 11px 14px; border-radius: 8px; color: rgba(255,255,255,0.65); text-decoration: none; font-size: 14px; font-weight: 500; transition: all 0.15s; }
.nav-item:hover, .nav-item--active { background: rgba(255,255,255,0.12); color: #fff; }
.sidebar-footer { padding: 16px; border-top: 1px solid rgba(255,255,255,0.1); }
.user-info { display: flex; align-items: center; gap: 10px; }
.user-avatar { width: 38px; height: 38px; border-radius: 50%; background: #4f46e5; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 15px; flex-shrink: 0; }
.user-name { color: #fff; font-size: 13px; font-weight: 600; margin-bottom: 4px; }
.badge-parent { background: #fef3c7; color: #d97706; }
.main-content { flex: 1; overflow-y: auto; background: var(--color-bg); }
@media (max-width: 768px) { .sidebar { display: none; } }
</style>

<template>
  <div class="app-container">
    <!-- SIDEBAR -->
    <aside class="sidebar">
      <div class="brand">
        <h2>EduManager</h2>
        <p>Hệ thống quản lý</p>
      </div>

      <nav class="nav-menu">
        <router-link
          v-for="(item, index) in navItems"
          :key="index"
          :to="item.path"
          class="nav-item"
          active-class="active"
        >
          <span class="material-symbols-outlined nav-icon">{{ item.icon }}</span>
          <span class="nav-text">{{ item.name }}</span>
        </router-link>
      </nav>

      <div class="sidebar-bottom">
        <button class="btn-create">
          Thêm đăng ký mới
        </button>
        <button class="btn-logout" @click="handleLogout">
          <span class="material-symbols-outlined">logout</span>
          Đăng xuất
        </button>
      </div>
    </aside>

    <!-- MAIN WRAPPER -->
    <div class="main-wrapper">
      <!-- TOPBAR -->
      <header class="topbar">
        <div class="search-box">
          <span class="material-symbols-outlined search-icon">search</span>
          <input type="text" placeholder="Tìm kiếm học viên, lớp học..." />
        </div>

        <div class="topbar-right">
          <button class="icon-btn">
            <span class="material-symbols-outlined">notifications</span>
          </button>
          <button class="icon-btn">
            <span class="material-symbols-outlined">settings</span>
          </button>
          <div class="user-profile">
            <div class="user-info">
              <span class="user-name">Admin Edu</span>
              <span class="user-role">Quản trị viên</span>
            </div>
            <img src="https://ui-avatars.com/api/?name=Admin+Edu&background=0D8ABC&color=fff" alt="Avatar" class="avatar" />
          </div>
        </div>
      </header>

      <!-- PAGE CONTENT -->
      <main class="page-content">
        <router-view />
      </main>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router';

const router = useRouter();

const navItems = [
  { name: 'Bảng điều khiển', path: '/admin/dashboard', icon: 'dashboard' },
  { name: 'Quản lý người dùng', path: '/admin/users', icon: 'group' },
  { name: 'Học viên', path: '/admin/students', icon: 'school' },
  { name: 'Phụ huynh', path: '/admin/guardians', icon: 'family_restroom' },
  { name: 'Giáo viên', path: '/admin/teachers', icon: 'person_4' },
  { name: 'Môn học', path: '/admin/subjects', icon: 'menu_book' },
  { name: 'Phòng học', path: '/admin/classrooms', icon: 'meeting_room' },
  { name: 'Lớp học', path: '/admin/classes', icon: 'class' },
  { name: 'Lịch học', path: '/admin/schedules', icon: 'calendar_month' },
  { name: 'Ghi danh', path: '/admin/enrollments', icon: 'person_add' },
];

const handleLogout = () => {
  router.push('/login');
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

* {
  box-sizing: border-box;
}

.app-container {
  display: flex;
  min-height: 100vh;
  background-color: #fafbfd;
  font-family: 'Plus Jakarta Sans', sans-serif;
  color: #1e293b;
}

/* SIDEBAR */
.sidebar {
  width: 260px;
  background-color: #ffffff;
  border-right: 1px solid #f1f5f9;
  display: flex;
  flex-direction: column;
  height: 100vh;
  position: fixed;
  left: 0;
  top: 0;
}

.brand {
  padding: 30px 24px;
}

.brand h2 {
  font-size: 22px;
  font-weight: 800;
  color: #1e40af;
  margin: 0 0 4px 0;
}

.brand p {
  font-size: 13px;
  color: #64748b;
  margin: 0;
  font-weight: 500;
}

.nav-menu {
  flex: 1;
  overflow-y: auto;
  padding: 0 16px;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.nav-menu::-webkit-scrollbar {
  display: none;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 12px 16px;
  border-radius: 8px;
  text-decoration: none;
  color: #475569;
  font-weight: 600;
  font-size: 14px;
  transition: all 0.2s;
}

.nav-icon {
  font-size: 20px;
  color: #64748b;
}

.nav-item:hover {
  background-color: #f8fafc;
  color: #1e293b;
}

.nav-item:hover .nav-icon {
  color: #1e293b;
}

.nav-item.active {
  background-color: #eff6ff;
  color: #1d4ed8;
}

.nav-item.active .nav-icon {
  color: #1d4ed8;
}

.sidebar-bottom {
  padding: 24px;
  border-top: 1px solid #f1f5f9;
}

.btn-create {
  width: 100%;
  background-color: #1d4ed8;
  color: #ffffff;
  border: none;
  padding: 12px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  margin-bottom: 16px;
  transition: background-color 0.2s;
}

.btn-create:hover {
  background-color: #1e40af;
}

.btn-logout {
  width: 100%;
  display: flex;
  align-items: center;
  gap: 12px;
  background: transparent;
  border: none;
  color: #64748b;
  font-weight: 600;
  font-size: 14px;
  cursor: pointer;
  padding: 8px 0;
  transition: color 0.2s;
}

.btn-logout:hover {
  color: #1e293b;
}

/* MAIN WRAPPER */
.main-wrapper {
  flex: 1;
  margin-left: 260px;
  display: flex;
  flex-direction: column;
}

/* TOPBAR */
.topbar {
  height: 80px;
  background-color: #ffffff;
  border-bottom: 1px solid #f1f5f9;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 40px;
  position: sticky;
  top: 0;
  z-index: 10;
}

.search-box {
  position: relative;
  width: 380px;
}

.search-icon {
  position: absolute;
  left: 16px;
  top: 50%;
  transform: translateY(-50%);
  color: #94a3b8;
  font-size: 20px;
  pointer-events: none;
}

.search-box input {
  width: 100%;
  background-color: #f1f5f9;
  border: 1px solid transparent;
  border-radius: 999px;
  padding: 12px 16px 12px 48px;
  font-size: 14px;
  color: #334155;
  outline: none;
  transition: border-color 0.2s, background-color 0.2s;
}

.search-box input:focus {
  background-color: #ffffff;
  border-color: #cbd5e1;
}

.topbar-right {
  display: flex;
  align-items: center;
  gap: 20px;
}

.icon-btn {
  background: transparent;
  border: none;
  color: #64748b;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 4px;
  transition: color 0.2s;
}

.icon-btn:hover {
  color: #1e293b;
}

.user-profile {
  display: flex;
  align-items: center;
  gap: 12px;
  border-left: 1px solid #e2e8f0;
  padding-left: 20px;
  margin-left: 8px;
  cursor: pointer;
}

.user-info {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
}

.user-name {
  font-size: 14px;
  font-weight: 700;
  color: #1e293b;
}

.user-role {
  font-size: 11px;
  color: #94a3b8;
  font-weight: 500;
}

.avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  object-fit: cover;
}

/* PAGE CONTENT */
.page-content {
  flex: 1;
  padding: 32px 40px;
  overflow-y: auto;
}
</style>

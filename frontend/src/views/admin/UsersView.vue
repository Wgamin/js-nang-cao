<script setup>
import { ref } from 'vue'

const users = ref([
  { id: 1, name: 'Ngô Kiến Huy', initials: 'KH', avatarColor: 'indigo', email: 'kienhuy.admin@edumanager.vn', role: 'Quản trị viên', roleClass: 'purple', status: 'Hoạt động', statusClass: 'success', lastLogin: '2 giờ trước' },
  { id: 2, name: 'Phan Lệ Thu', initials: 'LT', avatarColor: 'rose', email: 'thu.phan@edumanager.vn', role: 'Quản lý Đào tạo', roleClass: 'blue', status: 'Hoạt động', statusClass: 'success', lastLogin: '10 phút trước' },
  { id: 3, name: 'Vũ Quốc Toản', initials: 'QT', avatarColor: 'blue', email: 'toan.vu@edumanager.vn', role: 'Giáo vụ', roleClass: 'orange', status: 'Tạm khóa', statusClass: 'warning', lastLogin: '3 ngày trước' },
  { id: 4, name: 'Lâm Vỹ Dạ', initials: 'VD', avatarColor: 'orange', email: 'vyda.lam@edumanager.vn', role: 'Kế toán', roleClass: 'emerald', status: 'Hoạt động', statusClass: 'success', lastLogin: 'Vừa xong' },
])

const quickStats = [
  { label: 'Tổng người dùng', value: '12', icon: 'shield_person', color: '#1d4ed8', bg: 'rgba(29,78,216,0.1)' },
  { label: 'Quản trị viên', value: '2', icon: 'admin_panel_settings', color: '#8b5cf6', bg: 'rgba(139,92,246,0.1)' },
  { label: 'Giáo vụ & Đào tạo', value: '7', icon: 'support_agent', color: '#f59e0b', bg: 'rgba(245,158,11,0.1)' },
  { label: 'Đang khóa', value: '1', icon: 'lock', color: '#ef4444', bg: 'rgba(239,68,68,0.1)' },
]
</script>

<template>
  <div class="page-container">
    <header class="page-header">
      <div>
        <h1 class="page-title">Quản lý Người dùng</h1>
        <p class="page-subtitle">Quản trị phân quyền và bảo mật tài khoản hệ thống</p>
      </div>
      <button class="btn-primary">
        <span class="material-symbols-outlined">person_add</span>
        Thêm Người dùng
      </button>
    </header>

    <!-- Quick Stats -->
    <div class="stats-grid">
      <div v-for="stat in quickStats" :key="stat.label" class="stat-card">
        <div class="stat-icon" :style="{ color: stat.color, backgroundColor: stat.bg }">
          <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">{{ stat.icon }}</span>
        </div>
        <div class="stat-info">
          <span class="stat-label">{{ stat.label }}</span>
          <span class="stat-value">{{ stat.value }}</span>
        </div>
      </div>
    </div>

    <!-- Main Card -->
    <div class="content-card">
      <div class="table-actions">
        <!-- Search & Filter -->
        <div class="filter-group">
          <div class="search-box">
            <span class="material-symbols-outlined search-icon">search</span>
            <input type="text" class="search-input" placeholder="Tìm kiếm theo tên, email..." />
          </div>
          
          <div class="select-box">
            <select class="form-select">
              <option value="">Tất cả Vai trò</option>
              <option value="admin">Quản trị viên</option>
              <option value="manager">Quản lý Đào tạo</option>
              <option value="staff">Giáo vụ</option>
              <option value="acc">Kế toán</option>
            </select>
            <span class="material-symbols-outlined select-icon">expand_more</span>
          </div>
        </div>

        <!-- Export / Utility -->
        <div class="action-group">
          <button class="btn-secondary">
            <span class="material-symbols-outlined">security</span>
            Phân quyền
          </button>
        </div>
      </div>

      <!-- Table -->
      <div class="table-responsive">
        <table class="data-table">
          <thead>
            <tr>
              <th>NGƯỜI DÙNG</th>
              <th>VAI TRÒ</th>
              <th>ĐĂNG NHẬP LẦN CUỐI</th>
              <th>TRẠNG THÁI</th>
              <th class="text-right">THAO TÁC</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in users" :key="user.id">
              <td>
                <div class="user-cell">
                  <div class="avatar" :class="'avatar-' + user.avatarColor">{{ user.initials }}</div>
                  <div>
                    <h4 class="user-name">{{ user.name }}</h4>
                    <span class="user-email">{{ user.email }}</span>
                  </div>
                </div>
              </td>
              <td>
                <span class="role-badge" :class="'role-' + user.roleClass">{{ user.role }}</span>
              </td>
              <td>
                <div class="login-info">
                  <span class="material-symbols-outlined icon-small">schedule</span>
                  <span>{{ user.lastLogin }}</span>
                </div>
              </td>
              <td>
                <span class="badge" :class="'badge-' + user.statusClass">{{ user.status }}</span>
              </td>
              <td class="text-right">
                <button class="icon-btn" title="Chỉnh sửa quyền">
                  <span class="material-symbols-outlined">manage_accounts</span>
                </button>
                <button class="icon-btn" title="Khóa tài khoản">
                  <span class="material-symbols-outlined">lock</span>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="pagination-footer">
        <span class="page-info">Hiển thị 1 - 4 trên 12 người dùng</span>
        <div class="pagination">
          <button class="page-btn"><span class="material-symbols-outlined">chevron_left</span></button>
          <button class="page-btn active">1</button>
          <button class="page-btn">2</button>
          <button class="page-btn"><span class="material-symbols-outlined">chevron_right</span></button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.page-container {
  animation: fadeIn 0.4s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.page-title {
  font-size: 26px;
  font-weight: 800;
  color: #1e293b;
  margin: 0 0 4px 0;
}

.page-subtitle {
  font-size: 14px;
  color: #64748b;
  font-weight: 500;
  margin: 0;
}

.btn-primary {
  display: flex;
  align-items: center;
  gap: 8px;
  background-color: #1d4ed8;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 12px;
  font-weight: 600;
  font-size: 14px;
  cursor: pointer;
  transition: background-color 0.2s;
  box-shadow: 0 4px 6px -1px rgba(29, 78, 216, 0.2);
}

.btn-primary:hover {
  background-color: #1e40af;
}

.btn-secondary {
  display: flex;
  align-items: center;
  gap: 8px;
  background-color: #ffffff;
  color: #475569;
  border: 1px solid #e2e8f0;
  padding: 10px 16px;
  border-radius: 12px;
  font-weight: 600;
  font-size: 13px;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-secondary:hover {
  background-color: #f8fafc;
  border-color: #cbd5e1;
}

/* Stats */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
  margin-bottom: 24px;
}

.stat-card {
  background-color: #ffffff;
  padding: 20px;
  border-radius: 16px;
  display: flex;
  align-items: center;
  gap: 16px;
  box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
}

.stat-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.stat-icon .material-symbols-outlined {
  font-size: 24px;
}

.stat-info {
  display: flex;
  flex-direction: column;
}

.stat-label {
  font-size: 12px;
  font-weight: 700;
  color: #64748b;
  text-transform: uppercase;
  margin-bottom: 4px;
}

.stat-value {
  font-size: 24px;
  font-weight: 800;
  color: #1e293b;
}

/* Main Card */
.content-card {
  background-color: #ffffff;
  border-radius: 16px;
  box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
  overflow: hidden;
}

.table-actions {
  display: flex;
  justify-content: space-between;
  padding: 20px 24px;
  border-bottom: 1px solid #f1f5f9;
}

.filter-group {
  display: flex;
  gap: 16px;
}

.search-box {
  position: relative;
  width: 280px;
}

.search-icon {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: #94a3b8;
  font-size: 20px;
}

.search-input {
  width: 100%;
  border: 1px solid #e2e8f0;
  padding: 10px 16px 10px 40px;
  border-radius: 12px;
  font-size: 13px;
  font-weight: 500;
  color: #334155;
  outline: none;
  transition: all 0.2s;
}

.search-input:focus {
  border-color: #1d4ed8;
  box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.1);
}

.select-box {
  position: relative;
}

.form-select {
  appearance: none;
  border: 1px solid #e2e8f0;
  padding: 10px 40px 10px 16px;
  border-radius: 12px;
  font-size: 13px;
  font-weight: 600;
  color: #475569;
  background-color: #ffffff;
  outline: none;
  cursor: pointer;
  transition: all 0.2s;
}

.form-select:hover {
  border-color: #cbd5e1;
}

.form-select:focus {
  border-color: #1d4ed8;
}

.select-icon {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: #94a3b8;
  pointer-events: none;
}

.action-group {
  display: flex;
  gap: 12px;
}

/* Table */
.table-responsive {
  width: 100%;
  overflow-x: auto;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
}

.data-table th {
  text-align: left;
  padding: 16px 24px;
  font-size: 11px;
  font-weight: 700;
  color: #94a3b8;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  background-color: #f8fafc;
  border-bottom: 1px solid #f1f5f9;
}

.data-table td {
  padding: 16px 24px;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
}

.data-table tr:hover td {
  background-color: #f8fafc;
}

.user-cell {
  display: flex;
  align-items: center;
  gap: 12px;
}

.avatar {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 14px;
}

.avatar-blue { background: #dbeafe; color: #1e40af; }
.avatar-orange { background: #ffedd5; color: #9a3412; }
.avatar-rose { background: #ffe4e6; color: #e11d48; }
.avatar-indigo { background: #e0e7ff; color: #3730a3; }

.user-name {
  margin: 0 0 2px 0;
  font-size: 14px;
  font-weight: 700;
  color: #1e293b;
}

.user-email {
  font-size: 12px;
  color: #64748b;
}

.role-badge {
  font-size: 12px;
  font-weight: 600;
  padding: 4px 10px;
  border-radius: 6px;
  border: 1px solid transparent;
}

.role-purple { background-color: #f3e8ff; color: #7e22ce; border-color: #e9d5ff; }
.role-blue { background-color: #dbeafe; color: #1d4ed8; border-color: #bfdbfe; }
.role-orange { background-color: #ffedd5; color: #c2410c; border-color: #fed7aa; }
.role-emerald { background-color: #d1fae5; color: #047857; border-color: #a7f3d0; }

.login-info {
  display: flex;
  align-items: center;
  gap: 6px;
  color: #64748b;
  font-size: 13px;
  font-weight: 500;
}

.icon-small {
  font-size: 16px;
}

.badge {
  padding: 6px 12px;
  border-radius: 999px;
  font-size: 11px;
  font-weight: 700;
}

.badge-success { background-color: #dcfce7; color: #15803d; }
.badge-warning { background-color: #fee2e2; color: #b91c1c; }

.text-right { text-align: right; }

.icon-btn {
  background: transparent;
  border: none;
  cursor: pointer;
  color: #64748b;
  padding: 8px;
  border-radius: 8px;
  transition: all 0.2s;
  display: inline-flex;
}

.icon-btn:hover {
  background-color: #f1f5f9;
  color: #1d4ed8;
}

/* Pagination */
.pagination-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 24px;
  border-top: 1px solid #f1f5f9;
  background-color: #ffffff;
}

.page-info {
  font-size: 13px;
  font-weight: 500;
  color: #64748b;
}

.pagination {
  display: flex;
  gap: 4px;
}

.page-btn {
  min-width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid #e2e8f0;
  background-color: #ffffff;
  color: #475569;
  border-radius: 8px;
  font-weight: 600;
  font-size: 13px;
  cursor: pointer;
  transition: all 0.2s;
}

.page-btn:hover {
  background-color: #f8fafc;
  border-color: #cbd5e1;
}

.page-btn.active {
  background-color: #1d4ed8;
  color: #ffffff;
  border-color: #1d4ed8;
}
</style>

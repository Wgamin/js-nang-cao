<script setup>
import { ref } from 'vue'

const parents = ref([
  { id: 1, name: 'Nguyễn Văn Hùng', initials: 'VH', avatarColor: 'blue', phone: '098-765-4321', relation: 'Bố', students: ['Nguyễn Văn Hoàng (K24)'], connect: 'Zalo, SMS' },
  { id: 2, name: 'Lê Thị Mai', initials: 'TM', avatarColor: 'rose', phone: '090-123-4567', relation: 'Mẹ', students: ['Phạm Minh Tú (A02)', 'Phạm Bảo Hân (K10)'], connect: 'App Giáo Dục' },
  { id: 3, name: 'Trần Quốc Anh', initials: 'QA', avatarColor: 'indigo', phone: '091-222-3333', relation: 'Bố', students: ['Trần Linh Anh (Nghỉ)'], connect: 'SMS' },
  { id: 4, name: 'Bùi Thị Hà', initials: 'TH', avatarColor: 'orange', phone: '093-456-7890', relation: 'Bà', students: ['Lê Duy Kiên (K10)'], connect: 'Cuộc gọi' },
])

const quickStats = [
  { label: 'Tổng Phụ Huynh', value: '850', icon: 'escalator_warning', color: '#1d4ed8', bg: 'rgba(29,78,216,0.1)' },
  { label: 'Cài App Edu', value: '620', icon: 'app_shortcut', color: '#10b981', bg: 'rgba(16,185,129,0.1)' },
  { label: 'Cần Liên Hệ Tới Hạn', value: '15', icon: 'perm_phone_msg', color: '#f59e0b', bg: 'rgba(245,158,11,0.1)' },
  { label: 'Ngắt Kết Nối', value: '4', icon: 'no_sim', color: '#ef4444', bg: 'rgba(239,68,68,0.1)' },
]
</script>

<template>
  <div class="page-container">
    <header class="page-header">
      <div>
        <h1 class="page-title">Quản lý Phụ huynh</h1>
        <p class="page-subtitle">Quản lý thông tin và kênh trao đổi với gia đình học viên</p>
      </div>
      <button class="btn-primary">
        <span class="material-symbols-outlined">person_add</span>
        Thêm Phụ Huynh
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
            <input type="text" class="search-input" placeholder="Tìm kiếm SĐT, tên phụ huynh..." />
          </div>
          
          <div class="select-box">
            <select class="form-select">
              <option value="">Kênh trao đổi</option>
              <option value="app">App Giáo dục</option>
              <option value="zalo">Zalo</option>
              <option value="sms">SMS</option>
            </select>
            <span class="material-symbols-outlined select-icon">expand_more</span>
          </div>
        </div>

        <!-- Export / Utility -->
        <div class="action-group">
          <button class="btn-secondary">
            <span class="material-symbols-outlined">mail</span>
            Gửi thông báo loạt
          </button>
        </div>
      </div>

      <!-- Table -->
      <div class="table-responsive">
        <table class="data-table">
          <thead>
            <tr>
              <th>THÔNG TIN PHỤ HUYNH</th>
              <th>QUAN HỆ</th>
              <th>HỌC VIÊN LIÊN KẾT</th>
              <th>KÊNH TRAO ĐỔI</th>
              <th class="text-right">THAO TÁC</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="parent in parents" :key="parent.id">
              <td>
                <div class="user-cell">
                  <div class="avatar" :class="'avatar-' + parent.avatarColor">{{ parent.initials }}</div>
                  <div>
                    <h4 class="user-name">{{ parent.name }}</h4>
                    <span class="user-phone">SĐT: {{ parent.phone }}</span>
                  </div>
                </div>
              </td>
              <td>
                <span class="relation-badge">{{ parent.relation }}</span>
              </td>
              <td>
                <div class="student-list">
                  <span v-for="std in parent.students" :key="std" class="student-item">
                     <span class="material-symbols-outlined icon-small">school</span>
                     {{ std }}
                  </span>
                </div>
              </td>
              <td>
                <span class="channel-text">{{ parent.connect }}</span>
              </td>
              <td class="text-right">
                <button class="icon-btn" title="Gọi điện">
                  <span class="material-symbols-outlined text-green">call</span>
                </button>
                <button class="icon-btn" title="Tin nhắn">
                  <span class="material-symbols-outlined text-blue">chat</span>
                </button>
                <button class="icon-btn" title="Báo cáo">
                  <span class="material-symbols-outlined">assignment</span>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="pagination-footer">
        <span class="page-info">Hiển thị 1 - 4 trên 850 phụ huynh</span>
        <div class="pagination">
          <button class="page-btn"><span class="material-symbols-outlined">chevron_left</span></button>
          <button class="page-btn active">1</button>
          <button class="page-btn">2</button>
          <button class="page-btn"><span class="material-symbols-outlined">more_horiz</span></button>
          <button class="page-btn">85</button>
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

.user-phone {
  font-size: 12px;
  color: #64748b;
}

.relation-badge {
  font-size: 12px;
  font-weight: 600;
  padding: 4px 10px;
  border-radius: 6px;
  background-color: #f1f5f9;
  color: #475569;
}

.student-list {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.student-item {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 13px;
  font-weight: 500;
  color: #1d4ed8;
}

.icon-small {
  font-size: 14px;
}

.channel-text {
  font-size: 13px;
  font-weight: 600;
  color: #334155;
  background: rgba(248, 250, 252, 1);
  border: 1px solid #e2e8f0;
  padding: 4px 10px;
  border-radius: 6px;
}

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
}

.text-green { color: #10b981; }
.text-blue { color: #3b82f6; }

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

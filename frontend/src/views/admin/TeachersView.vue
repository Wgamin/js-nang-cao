<script setup>
import { ref } from 'vue'

const teachers = ref([
  { id: 1, name: 'Cô Nguyễn Minh Anh', initials: 'MA', avatarColor: 'rose', phone: '090-111-2222', subject: 'Tiếng Anh IELTS', classes: 'ENG-101, ENG-205', rating: 5.0, status: 'Đang dạy', statusClass: 'success' },
  { id: 2, name: 'Thầy Trần Văn Bình', initials: 'VB', avatarColor: 'blue', phone: '091-222-3333', subject: 'Toán Tư duy', classes: 'MATH-S, MATH-A', rating: 4.8, status: 'Đang dạy', statusClass: 'success' },
  { id: 3, name: 'Cô Phạm Trà My', initials: 'TM', avatarColor: 'indigo', phone: '098-333-4444', subject: 'Ngữ Văn Ngoại khóa', classes: 'LIT-01', rating: 4.5, status: 'Nghỉ phép', statusClass: 'warning' },
  { id: 4, name: 'Thầy Lê Hoàng Quân', initials: 'HQ', avatarColor: 'orange', phone: '094-444-5555', subject: 'Luyện thi Vật Lý', classes: 'PHY-12, PHY-11', rating: 4.9, status: 'Đang dạy', statusClass: 'success' },
])

const quickStats = [
  { label: 'Tổng Giáo viên', value: '86', icon: 'record_voice_over', color: '#1d4ed8', bg: 'rgba(29,78,216,0.1)' },
  { label: 'Đang Giảng dạy', value: '75', icon: 'school', color: '#10b981', bg: 'rgba(16,185,129,0.1)' },
  { label: 'Đang Nghỉ phép', value: '11', icon: 'event_busy', color: '#f59e0b', bg: 'rgba(245,158,11,0.1)' },
  { label: 'Đánh giá Hài lòng', value: '4.8/5', icon: 'stars', color: '#8b5cf6', bg: 'rgba(139,92,246,0.1)' },
]
</script>

<template>
  <div class="page-container">
    <header class="page-header">
      <div>
        <h1 class="page-title">Quản lý Giáo viên</h1>
        <p class="page-subtitle">Hồ sơ trích ngang, phân công chuyên môn và đánh giá chất lượng</p>
      </div>
      <button class="btn-primary">
        <span class="material-symbols-outlined">person_add</span>
        Thêm Giáo viên
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
            <input type="text" class="search-input" placeholder="Tìm kiếm tên, số điện thoại..." />
          </div>
          
          <div class="select-box">
            <select class="form-select">
              <option value="">Tất cả môn học</option>
              <option value="Toán">Toán Học</option>
              <option value="Anh">Tiếng Anh</option>
              <option value="Van">Ngữ Văn</option>
              <option value="Ly">Vật Lý</option>
            </select>
            <span class="material-symbols-outlined select-icon">expand_more</span>
          </div>
        </div>

        <!-- Export / Utility -->
        <div class="action-group">
          <button class="btn-secondary">
            <span class="material-symbols-outlined">event_note</span>
            Lịch làm việc
          </button>
          <button class="btn-secondary">
            <span class="material-symbols-outlined">download</span>
            Xuất file
          </button>
        </div>
      </div>

      <!-- Table -->
      <div class="table-responsive">
        <table class="data-table">
          <thead>
            <tr>
              <th>GIÁO VIÊN</th>
              <th>SỐ ĐIỆN THOẠI</th>
              <th>CHUYÊN MÔN</th>
              <th>ĐÁNH GIÁ</th>
              <th>TRẠNG THÁI</th>
              <th class="text-right">THAO TÁC</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="teacher in teachers" :key="teacher.id">
              <td>
                <div class="user-cell">
                  <div class="avatar" :class="'avatar-' + teacher.avatarColor">{{ teacher.initials }}</div>
                  <div>
                    <h4 class="user-name">{{ teacher.name }}</h4>
                    <span class="meta-classes flex items-center gap">
                      <span class="material-symbols-outlined icon-small text-indigo">play_lesson</span> 
                      {{ teacher.classes }}
                    </span>
                  </div>
                </div>
              </td>
              <td><span class="cell-text">{{ teacher.phone }}</span></td>
              <td>
                <span class="subject-badge">{{ teacher.subject }}</span>
              </td>
              <td>
                <div class="rating-cell">
                   <span class="material-symbols-outlined icon-filled text-yellow">star</span>
                   <span class="rating-val">{{ teacher.rating }}</span>
                </div>
              </td>
              <td>
                <span class="badge" :class="'badge-' + teacher.statusClass">{{ teacher.status }}</span>
              </td>
              <td class="text-right">
                <button class="icon-btn" title="Chỉnh sửa">
                  <span class="material-symbols-outlined">edit</span>
                </button>
                <button class="icon-btn" title="Lịch dạy">
                  <span class="material-symbols-outlined">calendar_month</span>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="pagination-footer">
        <span class="page-info">Hiển thị 1 - 4 trên 86 giáo viên</span>
        <div class="pagination">
          <button class="page-btn"><span class="material-symbols-outlined">chevron_left</span></button>
          <button class="page-btn active">1</button>
          <button class="page-btn">2</button>
          <button class="page-btn">3</button>
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

.data-table tr:last-child td {
  border-bottom: none;
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
  margin: 0 0 4px 0;
  font-size: 14px;
  font-weight: 700;
  color: #1e293b;
}

.meta-classes {
  font-size: 11px;
  color: #64748b;
  font-weight: 600;
}

.flex { display: flex; }
.items-center { align-items: center; }
.gap { gap: 4px; }
.text-indigo { color: #6366f1; }
.text-yellow { color: #f59e0b; }

.icon-small { font-size: 14px; }
.icon-filled { font-variation-settings: 'FILL' 1; }

.cell-text {
  font-size: 13px;
  font-weight: 600;
  color: #334155;
}

.subject-badge {
  font-size: 12px;
  font-weight: 600;
  padding: 4px 10px;
  border-radius: 6px;
  background-color: #f8fafc;
  border: 1px solid #e2e8f0;
  color: #475569;
}

.rating-cell {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  background: #fffbeb;
  padding: 4px 10px;
  border-radius: 99px;
  border: 1px solid #fef3c7;
}

.rating-val {
  font-size: 12px;
  font-weight: 800;
  color: #b45309;
}

.badge {
  padding: 6px 12px;
  border-radius: 999px;
  font-size: 11px;
  font-weight: 700;
}

.badge-success { background-color: #d1fae5; color: #047857; }
.badge-warning { background-color: #fef3c7; color: #b45309; }
.badge-danger { background-color: #fee2e2; color: #b91c1c; }

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

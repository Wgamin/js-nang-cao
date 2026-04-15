<script setup>
import { ref } from 'vue'

const subjects = ref([
  { id: 1, name: 'Tiếng Anh Giao tiếp Mầm non', code: 'ENG-KIDS', type: 'Ngôn ngữ', level: 'Mầm non', duration: '24 buổi', activeClasses: 5, status: 'Hoạt động', statusClass: 'success' },
  { id: 2, name: 'Toán Tư duy Nâng cao', code: 'MATH-ADV', type: 'Toán học', level: 'Cấp 1', duration: '48 buổi', activeClasses: 3, status: 'Hoạt động', statusClass: 'success' },
  { id: 3, name: 'Khoa học Không gian', code: 'SCI-SPC', type: 'Khoa học', level: 'Cấp 2', duration: '12 buổi', activeClasses: 0, status: 'Ngừng đào tạo', statusClass: 'danger' },
  { id: 4, name: 'Luyện thi IELTS 6.5+', code: 'IEL-65', type: 'Luyện thi', level: 'THPT & Sinh viên', duration: '72 buổi', activeClasses: 8, status: 'Hoạt động', statusClass: 'success' },
  { id: 5, name: 'Mỹ thuật Sáng tạo', code: 'ART-CR', type: 'Năng khiếu', level: 'Mọi lứa tuổi', duration: 'Tự do', activeClasses: 2, status: 'Đang bảo trì tài liệu', statusClass: 'warning' },
])

const quickStats = [
  { label: 'Tổng Môn Học', value: '24', icon: 'menu_book', color: '#1d4ed8', bg: 'rgba(29,78,216,0.1)' },
  { label: 'Đang Đào tạo', value: '18', icon: 'auto_stories', color: '#10b981', bg: 'rgba(16,185,129,0.1)' },
  { label: 'Ngừng Đào tạo', value: '4', icon: 'bookmark_remove', color: '#ef4444', bg: 'rgba(239,68,68,0.1)' },
  { label: 'Tổng Lớp Đang Mở', value: '42', icon: 'class', color: '#8b5cf6', bg: 'rgba(139,92,246,0.1)' },
]
</script>

<template>
  <div class="page-container">
    <header class="page-header">
      <div>
        <h1 class="page-title">Quản lý Môn học</h1>
        <p class="page-subtitle">Quản lý danh sách chương trình đào tạo, thời lượng và giáo trình</p>
      </div>
      <button class="btn-primary">
        <span class="material-symbols-outlined">add_circle</span>
        Thêm Môn học mới
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
            <input type="text" class="search-input" placeholder="Tìm kiếm mã môn, tên môn..." />
          </div>
          
          <div class="select-box">
            <select class="form-select">
              <option value="">Phân loại Nhóm môn</option>
              <option value="ngon-ngu">Ngôn ngữ</option>
              <option value="toan">Toán học</option>
              <option value="nang-khieu">Năng khiếu</option>
              <option value="luyen-thi">Luyện thi</option>
            </select>
            <span class="material-symbols-outlined select-icon">expand_more</span>
          </div>
        </div>

        <!-- Export / Utility -->
        <div class="action-group">
          <button class="btn-secondary">
            <span class="material-symbols-outlined">library_books</span>
            Kho Giáo trình
          </button>
        </div>
      </div>

      <!-- Table -->
      <div class="table-responsive">
        <table class="data-table">
          <thead>
            <tr>
              <th>MÃ MÔN</th>
              <th>TÊN MÔN HỌC</th>
              <th>CẤP ĐỘ / KHỐI</th>
              <th>THỜI LƯỢNG</th>
              <th>LỚP ĐANG MỞ</th>
              <th>TRẠNG THÁI</th>
              <th class="text-right">THAO TÁC</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="subject in subjects" :key="subject.id">
              <td>
                <span class="subject-code">{{ subject.code }}</span>
              </td>
              <td>
                <div>
                  <h4 class="subject-title">{{ subject.name }}</h4>
                  <span class="subject-type">Thuộc nhóm: {{ subject.type }}</span>
                </div>
              </td>
              <td><span class="cell-text">{{ subject.level }}</span></td>
              <td>
                <div class="duration-cell">
                   <span class="material-symbols-outlined icon-small">schedule</span>
                   {{ subject.duration }}
                </div>
              </td>
              <td>
                <span v-if="subject.activeClasses > 0" class="class-count">
                  {{ subject.activeClasses }} Lớp
                </span>
                <span v-else class="text-muted">Không có</span>
              </td>
              <td>
                <span class="badge" :class="'badge-' + subject.statusClass">{{ subject.status }}</span>
              </td>
              <td class="text-right">
                <button class="icon-btn" title="Chỉnh sửa chi tiết">
                  <span class="material-symbols-outlined">edit_square</span>
                </button>
                <button class="icon-btn" title="Chương trình/Giáo trình">
                  <span class="material-symbols-outlined text-blue">menu_book</span>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="pagination-footer">
        <span class="page-info">Hiển thị 1 - 5 trên 24 môn học</span>
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

.subject-code {
  font-family: monospace;
  font-size: 14px;
  font-weight: 700;
  color: #1d4ed8;
  background-color: rgba(239, 246, 255, 0.8);
  padding: 4px 10px;
  border-radius: 8px;
}

.subject-title {
  margin: 0 0 4px 0;
  font-size: 14px;
  font-weight: 700;
  color: #1e293b;
}

.subject-type {
  font-size: 12px;
  color: #64748b;
}

.duration-cell {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 13px;
  font-weight: 600;
  color: #475569;
  background-color: #f8fafc;
  padding: 4px 10px;
  border: 1px solid #f1f5f9;
  border-radius: 8px;
}

.icon-small {
  font-size: 16px;
  color: #94a3b8;
}

.cell-text {
  font-size: 13px;
  font-weight: 600;
  color: #334155;
}

.class-count {
  font-size: 13px;
  font-weight: 700;
  color: #1d4ed8;
  display: inline-flex;
  align-items: center;
  gap: 4px;
}

.class-count::before {
  content: '';
  display: inline-block;
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background-color: #1d4ed8;
}

.text-muted {
  font-size: 12px;
  color: #94a3b8;
  font-weight: 500;
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
  color: #1e293b;
}

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

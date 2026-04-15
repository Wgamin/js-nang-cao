<script setup>
import { ref } from 'vue'

const enrollments = ref([
  { id: 1, appCode: 'APP-2024-081', studentInfo: { name: 'Nguyễn Trần Bảo Anh', parent: 'Chị Lan', phone: '098-111-2222' }, course: 'Tiếng Anh Mầm non', date: '16/04/2024', tuition: 'Chưa thanh toán', tuitionClass: 'danger', status: 'Chờ duyệt', statusClass: 'warning' },
  { id: 2, appCode: 'APP-2024-082', studentInfo: { name: 'Lê Duy Thành', parent: 'Anh Thành', phone: '090-333-4444' }, course: 'Toán Tư duy', date: '15/04/2024', tuition: 'Đã cọc 50%', tuitionClass: 'info', status: 'Đã xếp lớp', statusClass: 'success' },
  { id: 3, appCode: 'APP-2024-083', studentInfo: { name: 'Phạm Minh Phương', parent: 'Cô Sáu', phone: '091-555-6666' }, course: 'Luyện thi Vật Lý', date: '15/04/2024', tuition: 'Đã thanh toán đủ', tuitionClass: 'success', status: 'Đã xếp lớp', statusClass: 'success' },
  { id: 4, appCode: 'APP-2024-084', studentInfo: { name: 'Vũ Hải Đăng', parent: 'Bác Bình', phone: '093-777-8888' }, course: 'Ngữ Văn Ngoại khóa', date: '10/04/2024', tuition: 'Hoàn phí', tuitionClass: 'warning', status: 'Đã hủy', statusClass: 'danger' },
])

const quickStats = [
  { label: 'Tổng Đơn Ghi Danh', value: '45', icon: 'how_to_reg', color: '#1d4ed8', bg: 'rgba(29,78,216,0.1)' },
  { label: 'Chờ Chấp Thuận', value: '12', icon: 'pending_actions', color: '#f59e0b', bg: 'rgba(245,158,11,0.1)' },
  { label: 'Đã Xếp Lớp', value: '28', icon: 'assignment_turned_in', color: '#10b981', bg: 'rgba(16,185,129,0.1)' },
  { label: 'Yêu cầu Hủy/Hoàn phí', value: '5', icon: 'request_quote', color: '#ef4444', bg: 'rgba(239,68,68,0.1)' },
]
</script>

<template>
  <div class="page-container">
    <header class="page-header">
      <div>
        <h1 class="page-title">Quản lý Ghi danh</h1>
        <p class="page-subtitle">Kiểm soát đơn nhập học mới, xử lý xếp lớp và học phí ban đầu</p>
      </div>
      <button class="btn-primary">
        <span class="material-symbols-outlined">post_add</span>
        Tạo Đơn Ghi danh
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
            <input type="text" class="search-input" placeholder="Tìm kiếm mã đơn, học viên..." />
          </div>
          
          <div class="select-box">
            <select class="form-select">
              <option value="">Trạng thái Ghi danh</option>
              <option value="cho">Chờ duyệt</option>
              <option value="xep">Đã xếp lớp</option>
              <option value="huy">Đã hủy</option>
            </select>
            <span class="material-symbols-outlined select-icon">expand_more</span>
          </div>

          <div class="select-box">
            <select class="form-select">
              <option value="">Thanh toán</option>
              <option value="chua">Chưa thanh toán</option>
              <option value="coc">Đã đặt cọc</option>
              <option value="full">Đã xong</option>
            </select>
            <span class="material-symbols-outlined select-icon">expand_more</span>
          </div>
        </div>

        <!-- Export / Utility -->
        <div class="action-group">
          <button class="btn-secondary">
            <span class="material-symbols-outlined">download</span>
            Biểu mẫu CSV
          </button>
        </div>
      </div>

      <!-- Table -->
      <div class="table-responsive">
        <table class="data-table">
          <thead>
            <tr>
              <th>MÃ HỒ SƠ</th>
              <th>THÔNG TIN HỌC VIÊN</th>
              <th>KHÓA HỌC ĐĂNG KÝ</th>
              <th>HỌC PHÍ</th>
              <th>TRẠNG THÁI</th>
              <th class="text-right">THAO TÁC</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="app in enrollments" :key="app.id">
              <td>
                <span class="app-code">{{ app.appCode }}</span>
                <div class="date-text mt-1">Đăng ký: {{ app.date }}</div>
              </td>
              <td>
                <div class="student-block">
                  <h4 class="student-name">{{ app.studentInfo.name }}</h4>
                  <div class="parent-info">
                    <span class="material-symbols-outlined icon-micro">contact_phone</span>
                    {{ app.studentInfo.parent }} - {{ app.studentInfo.phone }}
                  </div>
                </div>
              </td>
              <td>
                <span class="course-text">{{ app.course }}</span>
              </td>
              <td>
                <span class="tuition-pill" :class="'tuition-' + app.tuitionClass">
                  {{ app.tuition }}
                </span>
              </td>
              <td>
                <span class="badge" :class="'badge-' + app.statusClass">{{ app.status }}</span>
              </td>
              <td class="text-right">
                <button class="icon-btn" title="Duyệt / Xếp lớp">
                  <span class="material-symbols-outlined text-green">how_to_reg</span>
                </button>
                <button class="icon-btn" title="Chỉnh sửa">
                  <span class="material-symbols-outlined">edit_square</span>
                </button>
                <button class="icon-btn" title="Hủy bỏ">
                  <span class="material-symbols-outlined text-red">delete</span>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="pagination-footer">
        <span class="page-info">Hiển thị 1 - 4 trên 45 đơn đăng ký</span>
        <div class="pagination">
          <button class="page-btn"><span class="material-symbols-outlined">chevron_left</span></button>
          <button class="page-btn active">1</button>
          <button class="page-btn">2</button>
          <button class="page-btn">3</button>
          <button class="page-btn"><span class="material-symbols-outlined">more_horiz</span></button>
          <button class="page-btn">5</button>
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

.app-code {
  font-family: monospace;
  font-size: 13px;
  font-weight: 800;
  color: #1d4ed8;
  background-color: rgba(239, 246, 255, 0.8);
  padding: 4px 8px;
  border-radius: 6px;
}

.date-text {
  font-size: 11px;
  color: #94a3b8;
  font-weight: 500;
}

.mt-1 { margin-top: 4px; }

.student-block {
  display: flex;
  flex-direction: column;
}

.student-name {
  margin: 0 0 4px 0;
  font-size: 14px;
  font-weight: 700;
  color: #1e293b;
}

.parent-info {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 12px;
  color: #64748b;
}

.icon-micro {
  font-size: 14px;
  color: #94a3b8;
}

.course-text {
  font-size: 13px;
  font-weight: 600;
  color: #334155;
}

.tuition-pill {
  font-size: 12px;
  font-weight: 700;
  padding: 4px 8px;
  border: 1px solid transparent;
  border-radius: 6px;
}

.tuition-danger { background-color: #fee2e2; color: #b91c1c; border-color: #fecaca; }
.tuition-info { background-color: #dbeafe; color: #1d4ed8; border-color: #bfdbfe; }
.tuition-success { background-color: #d1fae5; color: #047857; border-color: #a7f3d0; }
.tuition-warning { background-color: #fef3c7; color: #b45309; border-color: #fde68a; }


.badge {
  padding: 6px 12px;
  border-radius: 999px;
  font-size: 11px;
  font-weight: 700;
}

.badge-success { background-color: #dcfce7; color: #15803d; }
.badge-warning { background-color: #fef9c3; color: #a16207; }
.badge-danger { background-color: #fef2f2; color: #ef4444; }

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

.text-green { color: #10b981; }
.text-red { color: #ef4444; }

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

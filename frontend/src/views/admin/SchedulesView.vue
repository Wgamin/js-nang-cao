<script setup>
import { ref } from 'vue'

const schedules = ref([
  { id: 1, time: '08:00 - 09:30', class: 'Lớp ENG-101', subject: 'Tiếng Anh Giao tiếp', teacher: 'Nguyễn Minh Anh', room: 'P.302', status: 'Đã hoàn thành', statusClass: 'success' },
  { id: 2, time: '10:00 - 11:30', class: 'Lớp MATH-S', subject: 'Toán Tư duy', teacher: 'Trần Văn Bình', room: 'P.105', status: 'Đang diễn ra', statusClass: 'warning' },
  { id: 3, time: '14:00 - 16:00', class: 'Lớp ART-01', subject: 'Mỹ thuật Sáng tạo', teacher: 'Giáo viên Thỉnh giảng', room: 'Art Studio', status: 'Sắp diễn ra', statusClass: 'info' },
  { id: 4, time: '18:00 - 20:00', class: 'Lớp PHY-12', subject: 'Luyện thi Vật Lý', teacher: 'Lê Hoàng Quân', room: 'Lab 02', status: 'Sắp diễn ra', statusClass: 'info' },
  { id: 5, time: '19:30 - 21:00', class: 'Lớp LIT-01', subject: 'Ngữ Văn Nâng cao', teacher: 'Phạm Trà My', room: 'P.401', status: 'Giáo viên Nghỉ', statusClass: 'danger' },
])

const quickStats = [
  { label: 'Tổng Ca Học Lịch Trong Ngày', value: '18', icon: 'calendar_month', color: '#1d4ed8', bg: 'rgba(29,78,216,0.1)' },
  { label: 'Đang Diễn Ra', value: '4', icon: 'run_circle', color: '#f59e0b', bg: 'rgba(245,158,11,0.1)' },
  { label: 'Sắp Tới', value: '10', icon: 'schedule', color: '#10b981', bg: 'rgba(16,185,129,0.1)' },
  { label: 'Đóng/Hủy ca', value: '1', icon: 'cancel_schedule_send', color: '#ef4444', bg: 'rgba(239,68,68,0.1)' },
]
</script>

<template>
  <div class="page-container">
    <header class="page-header">
      <div>
        <h1 class="page-title">Quản lý Lịch học</h1>
        <p class="page-subtitle">Sắp xếp thời khóa biểu và theo dõi ca giảng dạy hàng ngày</p>
      </div>
      <button class="btn-primary">
        <span class="material-symbols-outlined">edit_calendar</span>
        Tạo Lịch Tập thể
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
          <!-- Phân Lịch Theo Tuần / Theo Ngày -->
          <div class="select-box" style="width: 200px;">
            <div class="date-picker-mock">
               <span class="material-symbols-outlined icon-mid">event</span>
               <span class="text-sm font-semibold text-slate-700">Hôm nay, 16/04</span>
            </div>
          </div>
          
          <div class="select-box">
            <select class="form-select">
              <option value="">Lọc theo Giáo viên</option>
              <option value="gv1">Nguyễn Minh Anh</option>
              <option value="gv2">Lê Hoàng Quân</option>
            </select>
            <span class="material-symbols-outlined select-icon">expand_more</span>
          </div>

          <div class="select-box">
            <select class="form-select">
              <option value="">Lọc theo Không gian (Phòng)</option>
              <option value="p302">P.302</option>
              <option value="p105">P.105</option>
            </select>
            <span class="material-symbols-outlined select-icon">expand_more</span>
          </div>
        </div>

        <!-- Export / Utility -->
        <div class="action-group">
          <button class="btn-secondary">
            <span class="material-symbols-outlined">grid_view</span>
            Xem dạng Thời khóa biểu
          </button>
        </div>
      </div>

      <!-- Table -->
      <div class="table-responsive">
        <table class="data-table">
          <thead>
            <tr>
              <th>THỜI GIAN</th>
              <th>LỚP HỌC & MÔN</th>
              <th>GIÁO VIÊN</th>
              <th>PHÒNG HỌC</th>
              <th>TRẠNG THÁI</th>
              <th class="text-right">THAO TÁC</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="sch in schedules" :key="sch.id">
              <td>
                <div class="time-block">
                  <span class="material-symbols-outlined clock-icon">schedule</span>
                  <span class="time-text">{{ sch.time }}</span>
                </div>
              </td>
              <td>
                <div>
                  <h4 class="class-title">{{ sch.class }}</h4>
                  <span class="subject-text">Môn: {{ sch.subject }}</span>
                </div>
              </td>
              <td>
                <div class="teacher-cell">
                   <span class="material-symbols-outlined icon-small">person</span>
                   <span>{{ sch.teacher }}</span>
                </div>
              </td>
              <td>
                <span class="room-pill">
                  <span class="material-symbols-outlined icon-smaller">sensor_door</span>
                  {{ sch.room }}
                </span>
              </td>
              <td>
                <span class="badge" :class="'badge-' + sch.statusClass">{{ sch.status }}</span>
              </td>
              <td class="text-right">
                <button class="icon-btn" title="Chỉnh sửa ca học">
                  <span class="material-symbols-outlined">edit_square</span>
                </button>
                <button class="icon-btn" title="Điểm danh học viên">
                  <span class="material-symbols-outlined text-blue">fact_check</span>
                </button>
                <button class="icon-btn" title="Hủy ca / Chuyển lịch">
                  <span class="material-symbols-outlined text-red">free_cancellation</span>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="pagination-footer">
        <span class="page-info">Hiển thị 1 - 5 ca học</span>
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

.date-picker-mock {
  display: flex;
  align-items: center;
  gap: 8px;
  border: 1px solid #e2e8f0;
  padding: 10px 16px;
  border-radius: 12px;
  font-size: 13px;
  cursor: pointer;
  background-color: #f8fafc;
}

.date-picker-mock:hover {
  border-color: #cbd5e1;
}

.icon-mid { color: #1d4ed8; font-size: 18px; }
.text-sm { font-size: 13px; }
.font-semibold { font-weight: 600; }
.text-slate-700 { color: #334155; }

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
  min-width: 180px;
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

.time-block {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background-color: #f1f5f9;
  padding: 6px 12px;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
}

.clock-icon {
  font-size: 18px;
  color: #1d4ed8;
}

.time-text {
  font-size: 13px;
  font-weight: 800;
  color: #1e293b;
  font-family: monospace;
}

.class-title {
  margin: 0 0 4px 0;
  font-size: 14px;
  font-weight: 700;
  color: #1e293b;
}

.subject-text {
  font-size: 12px;
  color: #64748b;
  font-weight: 500;
}

.teacher-cell {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 13px;
  font-weight: 600;
  color: #334155;
}

.icon-small {
  font-size: 18px;
  color: #94a3b8;
}

.icon-smaller {
  font-size: 16px;
}

.room-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 13px;
  font-weight: 700;
  color: #0f172a;
  background-color: rgba(241, 245, 249, 0.5);
  padding: 4px 10px;
  border-radius: 6px;
  border: 1px solid #e2e8f0;
}

.badge {
  padding: 6px 12px;
  border-radius: 999px;
  font-size: 11px;
  font-weight: 700;
}

.badge-success { background-color: #d1fae5; color: #047857; }
.badge-warning { background-color: #fef3c7; color: #b45309; }
.badge-info { background-color: #dbeafe; color: #1d4ed8; }
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

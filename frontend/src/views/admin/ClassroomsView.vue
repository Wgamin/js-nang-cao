<script setup>
import { ref } from 'vue'

const classrooms = ref([
  { id: 1, code: 'P.302', building: 'Tòa A', capacity: 30, equipment: ['Máy chiếu', 'Bảng thông minh', 'Điều hòa'], status: 'Đang dùng', statusClass: 'warning', currentClass: 'Lớp ENG-101' },
  { id: 2, code: 'P.105', building: 'Tòa A', capacity: 20, equipment: ['Tivi', 'Bảng phấn', 'Điều hòa'], status: 'Đang dùng', statusClass: 'warning', currentClass: 'Lớp MATH-S' },
  { id: 3, code: 'Lab 02', building: 'Tòa B (Thực hành)', capacity: 40, equipment: ['40 Máy tính', 'Máy chiếu', 'Tai nghe'], status: 'Trống', statusClass: 'success', currentClass: null },
  { id: 4, code: 'Art Studio', building: 'Tòa C', capacity: 15, equipment: ['Giá vẽ', 'Bồn rửa', 'Điều hòa'], status: 'Trống', statusClass: 'success', currentClass: null },
  { id: 5, code: 'P.401', building: 'Tòa A', capacity: 25, equipment: ['Máy chiếu', 'Điều hòa'], status: 'Bảo trì', statusClass: 'danger', currentClass: null },
])

const quickStats = [
  { label: 'Tổng Phòng Học', value: '24', icon: 'meeting_room', color: '#1d4ed8', bg: 'rgba(29,78,216,0.1)' },
  { label: 'Đang Sử Dụng', value: '8', icon: 'cast_for_education', color: '#f59e0b', bg: 'rgba(245,158,11,0.1)' },
  { label: 'Đang Trống', value: '12', icon: 'check_circle', color: '#10b981', bg: 'rgba(16,185,129,0.1)' },
  { label: 'Đang Bảo Trì', value: '4', icon: 'handyman', color: '#ef4444', bg: 'rgba(239,68,68,0.1)' },
]
</script>

<template>
  <div class="page-container">
    <header class="page-header">
      <div>
        <h1 class="page-title">Quản lý Phòng học</h1>
        <p class="page-subtitle">Kiểm soát tình trạng cơ sở vật chất và điều phối phòng học trống</p>
      </div>
      <button class="btn-primary">
        <span class="material-symbols-outlined">add_business</span>
        Thêm Phòng học mới
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
            <input type="text" class="search-input" placeholder="Tìm kiếm phòng, khu vực..." />
          </div>
          
          <div class="select-box">
            <select class="form-select">
              <option value="">Lọc theo Trạng thái</option>
              <option value="trong">Trống</option>
              <option value="dang-dung">Đang sử dụng</option>
              <option value="bao-tri">Bảo trì</option>
            </select>
            <span class="material-symbols-outlined select-icon">expand_more</span>
          </div>
          <div class="select-box">
            <select class="form-select">
              <option value="">Lọc theo Tòa nhà</option>
              <option value="A">Tòa A</option>
              <option value="B">Tòa B</option>
              <option value="C">Tòa C</option>
            </select>
            <span class="material-symbols-outlined select-icon">expand_more</span>
          </div>
        </div>

        <!-- Export / Utility -->
        <div class="action-group">
          <button class="btn-secondary">
            <span class="material-symbols-outlined">map</span>
            Sơ đồ phòng học
          </button>
        </div>
      </div>

      <!-- Table -->
      <div class="table-responsive">
        <table class="data-table">
          <thead>
            <tr>
              <th>MÃ PHÒNG</th>
              <th>KHU VỰC / TÒA NHÀ</th>
              <th>SỨC CHỨA</th>
              <th>TRANG THIẾT BỊ</th>
              <th>TRẠNG THÁI & LỚP HỌC</th>
              <th class="text-right">THAO TÁC</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="room in classrooms" :key="room.id">
              <td>
                <span class="room-code">
                  <span class="material-symbols-outlined icon-small" style="font-variation-settings: 'FILL' 1">sensor_door</span>
                  {{ room.code }}
                </span>
              </td>
              <td>
                <span class="cell-text font-bold">{{ room.building }}</span>
              </td>
              <td>
                <div class="capacity-cell">
                   <span class="material-symbols-outlined icon-small">group</span>
                   <span>{{ room.capacity }} HV</span>
                </div>
              </td>
              <td>
                <div class="equipment-list">
                  <span v-for="(item, idx) in room.equipment" :key="idx" class="equip-item">
                    {{ item }}
                  </span>
                </div>
              </td>
              <td>
                <div class="status-cell">
                  <span class="badge" :class="'badge-' + room.statusClass">{{ room.status }}</span>
                  <span v-if="room.currentClass" class="current-class">
                    <span class="material-symbols-outlined icon-small text-blue">play_lesson</span>
                    {{ room.currentClass }}
                  </span>
                </div>
              </td>
              <td class="text-right">
                <button class="icon-btn" title="Chỉnh sửa chi tiết">
                  <span class="material-symbols-outlined">edit_square</span>
                </button>
                <button class="icon-btn" title="Lịch sử dụng phòng">
                  <span class="material-symbols-outlined text-blue">calendar_month</span>
                </button>
                <button class="icon-btn" title="Báo cáo bảo trì hỏng hóc">
                  <span class="material-symbols-outlined text-red">build_circle</span>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="pagination-footer">
        <span class="page-info">Hiển thị 1 - 5 trên 24 phòng học</span>
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
  width: 250px;
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

.room-code {
  font-size: 15px;
  font-weight: 800;
  color: #1e293b;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}

.icon-small {
  font-size: 18px;
  color: #64748b;
}

.cell-text {
  font-size: 13px;
  color: #334155;
}

.font-bold {
  font-weight: 700;
}

.capacity-cell {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 13px;
  font-weight: 600;
  color: #475569;
}

.equipment-list {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}

.equip-item {
  font-size: 11px;
  font-weight: 600;
  padding: 4px 8px;
  background-color: #f1f5f9;
  color: #475569;
  border-radius: 6px;
}

.status-cell {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 8px;
}

.current-class {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  font-weight: 700;
  color: #1d4ed8;
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

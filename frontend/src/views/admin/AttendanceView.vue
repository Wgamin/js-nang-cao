<script setup>
import { ref, computed } from 'vue'

const alertStudents = ref([
  { id: 1, name: 'Nguyễn Minh Tuấn', studentId: 'STU-9921', className: 'IELTS Master - L2', absentCount: 8, totalCount: 24, rate: 33, lastAbsent: 'Hôm qua, 14:30', avatarColor: 'blue', initials: 'MT' },
  { id: 2, name: 'Trần Thị Mai Anh', studentId: 'STU-8845', className: 'TOEIC 750+ Elite', absentCount: 5, totalCount: 20, rate: 25, lastAbsent: '12/10/2023', avatarColor: 'rose', initials: 'MA' },
]);

const searchQuery = ref('')
const classFilter = ref('')

const filteredStudents = computed(() => {
  return alertStudents.value.filter(s => {
    const matchesSearch = s.name.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
                          s.studentId.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchesClass = !classFilter.value || s.className === classFilter.value
    return matchesSearch && matchesClass
  })
})

function getAvatarColor(color) {
  const map = {
    'blue': '#3b82f6',
    'rose': '#e11d48',
    'orange': '#ea580c',
    'indigo': '#4f46e5'
  }
  return map[color] || '#cbd5e1'
}
</script>

<template>
  <div class="admin-page">
    <!-- Header Section -->
    <div class="headers">
      <div class="header-left">
        <h1 class="title">Cảnh Báo Chuyên Cần</h1>
        <p class="subtitle max-w-2xl">Theo dõi và quản lý các học viên có tỉ lệ vắng học vượt mức quy định (20%). Hệ thống tự động cập nhật danh sách dựa trên dữ liệu điểm danh hàng ngày.</p>
      </div>
    </div>

    <!-- Stats Grid Layout -->
    <div class="dashboard-grid">
      <div class="primary-card">
        <div class="z-10">
          <span class="live-badge">Live Alert</span>
          <h3 class="hero-number">42</h3>
          <p class="hero-text">Học viên đang bị cảnh báo</p>
        </div>
        <div class="z-10 mt-4">
          <p class="hero-desc">Tăng 12% so với tháng trước. Vui lòng liên hệ phụ huynh để cập nhật tình hình học tập.</p>
          <button class="btn btn-white">Xuất báo cáo PDF</button>
        </div>
      </div>

      <div class="stats-sub-grid">
        <div class="stat-box">
          <div class="stat-icon-box" style="background-color: #fef2f2; color: #ef4444;">
            <span class="material-symbols-outlined">trending_up</span>
          </div>
          <div>
            <p class="stat-label uppercase tracking-widest">Tỉ lệ vắng trung bình</p>
            <p class="stat-value">14.5%</p>
          </div>
        </div>
        
        <div class="stat-box">
          <div class="stat-icon-box" style="background-color: #eff6ff; color: #3b82f6;">
            <span class="material-symbols-outlined">history</span>
          </div>
          <div>
            <p class="stat-label uppercase tracking-widest">Phản hồi phụ huynh</p>
            <p class="stat-value">28/42</p>
          </div>
        </div>
        
        <div class="chart-box">
          <div class="chart-header">
            <h4>Diễn biến nghỉ học trong tuần</h4>
            <div class="chart-legend">
              <span class="legend-dot"></span>
              <span class="legend-text">Tuần này</span>
            </div>
          </div>
          <div class="chart-bars">
            <div class="bar" style="height: 40%; opacity: 0.2"></div>
            <div class="bar" style="height: 60%; opacity: 0.2"></div>
            <div class="bar" style="height: 30%; opacity: 0.2"></div>
            <div class="bar" style="height: 85%; opacity: 1"></div>
            <div class="bar" style="height: 50%; opacity: 0.2"></div>
            <div class="bar" style="height: 45%; opacity: 0.2"></div>
            <div class="bar" style="height: 20%; opacity: 0.2"></div>
          </div>
          <div class="chart-labels">
            <span>THỨ 2</span><span>THỨ 3</span><span>THỨ 4</span><span>THỨ 5</span><span>THỨ 6</span><span>THỨ 7</span><span>CN</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Table Card -->
    <div class="section-title-wrap">
      <h3 class="section-title">Danh sách học viên vắng nhiều</h3>
    </div>

    <div class="content-box">
      <div class="toolbar">
        <div class="search-wrapper">
          <span class="material-symbols-outlined search-icon">search</span>
          <input v-model="searchQuery" type="text" class="input-search" placeholder="Tìm theo ID hoặc Tên học viên..." />
        </div>
        
        <div class="filters">
          <div class="select-wrapper">
            <select v-model="classFilter" class="select-filter">
              <option value="">Tất cả các lớp</option>
              <option value="IELTS Master - L2">IELTS Master - L2</option>
              <option value="TOEIC 750+ Elite">TOEIC 750+ Elite</option>
              <option value="Business English - B1">Business English - B1</option>
            </select>
            <span class="material-symbols-outlined select-arrow">expand_more</span>
          </div>
        </div>
      </div>

      <!-- Table Section -->
      <div class="table-container">
        <table class="user-table">
          <thead>
            <tr>
              <th style="width: 25%">Học viên</th>
              <th style="width: 20%">Lớp học</th>
              <th style="width: 15%" class="text-center">Buổi vắng</th>
              <th style="width: 15%">Tỉ lệ vắng</th>
              <th style="width: 15%">Ngày vắng gần nhất</th>
              <th style="width: 10%" class="text-right">Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="student in filteredStudents" :key="student.id" class="table-row">
              <td>
                <div class="user-info">
                  <div class="avatar" :style="{ backgroundColor: getAvatarColor(student.avatarColor) }">{{ student.initials }}</div>
                  <div>
                    <p class="user-full-name">{{ student.name }}</p>
                    <p class="user-email-text">ID: {{ student.studentId }}</p>
                  </div>
                </div>
              </td>
              <td>
                <span class="user-full-name" style="font-size: 13px;">{{ student.className }}</span>
              </td>
              <td class="text-center">
                <span class="user-full-name">{{ student.absentCount }}/{{ student.totalCount }}</span>
              </td>
              <td>
                <div class="progress-wrapper">
                  <div class="progress-bar">
                    <div class="progress-fill" :style="{ width: student.rate + '%' }"></div>
                  </div>
                  <span class="progress-text">{{ student.rate }}%</span>
                </div>
              </td>
              <td>
                <span class="user-email-text">{{ student.lastAbsent }}</span>
              </td>
              <td class="text-right">
                <div class="actions">
                  <button class="action-btn" title="Gọi phụ huynh">
                    <span class="material-symbols-outlined" style="color: #ef4444;">call</span>
                  </button>
                  <button class="action-btn" title="Xem chi tiết">
                    <span class="material-symbols-outlined" style="color: #2563eb;">visibility</span>
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="filteredStudents.length === 0">
              <td colspan="6" class="empty-state">
                Không tìm thấy học viên nào phù hợp.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Footer -->
      <div class="table-footer flex-footer">
        <p>Hiển thị <strong>1-{{ filteredStudents.length }}</strong> của {{ alertStudents.length }} học viên</p>
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
/* Base Styles */
.admin-page { background-color: #f8fafc; min-height: 100vh; padding: 0 4px; font-family: 'Inter', system-ui, -apple-system, sans-serif; color: #1e293b; }
.headers { margin-bottom: 32px; }
.title { font-size: 28px; font-weight: 800; color: #0f172a; margin: 0 0 8px 0; }
.subtitle { font-size: 14px; color: #64748b; margin: 0; line-height: 1.6; }
.max-w-2xl { max-width: 42rem; }

/* Grid System */
.dashboard-grid { display: grid; grid-template-columns: 1fr 2fr; gap: 24px; margin-bottom: 32px; }
.stats-sub-grid { display: grid; grid-template-columns: 1fr 1fr; grid-template-rows: auto auto; gap: 24px; }
.chart-box { grid-column: span 2; background-color: #ffffff; padding: 24px; border-radius: 12px; border: 1px solid #f1f5f9; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05); }

/* Primary Alert Card */
.primary-card { background-color: #2563eb; color: #ffffff; padding: 32px; border-radius: 16px; display: flex; flex-direction: column; justify-content: space-between; position: relative; overflow: hidden; box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.2); }
.primary-card::after { content: ''; position: absolute; right: -32px; bottom: -32px; width: 192px; height: 192px; background: rgba(255, 255, 255, 0.1); border-radius: 50%; filter: blur(24px); pointer-events: none; }
.live-badge { display: inline-flex; align-items: center; padding: 4px 12px; background: rgba(255, 255, 255, 0.2); border-radius: 99px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 16px; }
.hero-number { font-size: 48px; font-weight: 900; margin: 0 0 4px 0; }
.hero-text { font-size: 16px; font-weight: 600; color: #bfdbfe; margin: 0; }
.hero-desc { font-size: 13px; opacity: 0.9; margin: 0 0 20px 0; line-height: 1.5; }
.z-10 { position: relative; z-index: 10; }

.btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s ease; border: 1px solid transparent; }
.btn-white { background: #ffffff; color: #2563eb; }
.btn-white:hover { background: #f8fafc; transform: translateY(-1px); }

/* Small Stat Boxes */
.stat-box { background-color: #ffffff; padding: 24px; border-radius: 12px; border: 1px solid #f1f5f9; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05); display: flex; align-items: center; gap: 20px; }
.stat-icon-box { width: 56px; height: 56px; border-radius: 12px; display: flex; align-items: center; justify-content: center; }
.stat-icon-box .material-symbols-outlined { font-size: 28px; }
.stat-label { font-size: 11px; font-weight: 700; color: #64748b; margin: 0 0 4px 0; display: block; }
.uppercase { text-transform: uppercase; }
.tracking-widest { letter-spacing: 0.1em; }
.stat-value { font-size: 24px; font-weight: 800; color: #0f172a; margin: 0; }

/* Chart Area */
.chart-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
.chart-header h4 { font-size: 14px; font-weight: 700; color: #0f172a; margin: 0; }
.chart-legend { display: flex; align-items: center; gap: 8px; }
.legend-dot { width: 10px; height: 10px; background: #2563eb; border-radius: 50%; }
.legend-text { font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; }

.chart-bars { display: flex; align-items: flex-end; justify-content: space-between; height: 100px; padding: 0 16px; margin-bottom: 12px; }
.bar { width: 40px; background: #2563eb; border-radius: 6px 6px 0 0; }
.chart-labels { display: flex; justify-content: space-between; padding: 0 16px; font-size: 10px; font-weight: 700; color: #94a3b8; }
.chart-labels span { width: 40px; text-align: center; }

/* Table Section Title */
.section-title-wrap { margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; }
.section-title { font-size: 18px; font-weight: 800; color: #0f172a; margin: 0; }

/* Content Box */
.content-box { background-color: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05); overflow: hidden; }

/* Toolbar */
.toolbar { padding: 20px 24px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; gap: 16px; flex-wrap: wrap; }
.search-wrapper { position: relative; flex: 1; max-width: 400px; }
.search-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 20px; }
.input-search { width: 100%; padding: 10px 12px 10px 40px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; background-color: #f8fafc; transition: all 0.2s; outline: none; }
.input-search:focus { background-color: #ffffff; border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }

.filters { display: flex; gap: 12px; align-items: center; }
.select-wrapper { position: relative; min-width: 220px; }
.select-filter { width: 100%; appearance: none; padding: 10px 36px 10px 14px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; font-weight: 500; color: #475569; background-color: #ffffff; cursor: pointer; outline: none; }
.select-arrow { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; }

/* Table */
.table-container { min-height: 200px; position: relative; overflow-x: auto; }
.user-table { width: 100%; border-collapse: collapse; }
.user-table th { text-align: left; padding: 14px 24px; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; background-color: #f8fafc; border-bottom: 1px solid #f1f5f9; }
.table-row { transition: background-color 0.2s ease; }
.table-row:hover { background-color: #f8fafc; }
.user-table td { padding: 16px 24px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }

/* Custom elements */
.user-info { display: flex; align-items: center; gap: 12px; }
.avatar { width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #ffffff; font-weight: 700; font-size: 13px; }
.user-full-name { font-size: 14px; font-weight: 600; color: #1e293b; margin: 0; }
.user-email-text { font-size: 12px; color: #64748b; margin: 0; }

.progress-wrapper { display: flex; align-items: center; gap: 12px; }
.progress-bar { flex: 1; height: 6px; background: #f1f5f9; border-radius: 99px; overflow: hidden; }
.progress-fill { height: 100%; background: #ef4444; border-radius: 99px; }
.progress-text { font-size: 13px; font-weight: 800; color: #ef4444; min-width: 32px; }

/* Actions */
.actions { display: flex; justify-content: flex-end; gap: 4px; }
.action-btn { width: 34px; height: 34px; border-radius: 6px; border: 1px solid transparent; background-color: #f8fafc; color: #94a3b8; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; }
.action-btn:hover { background-color: #f1f5f9; filter: brightness(0.95); }
.action-btn .material-symbols-outlined { font-size: 20px; }
.text-right { text-align: right; }
.text-center { text-align: center; }

/* Empty state & Footer */
.empty-state { text-align: center; padding: 60px 0; color: #94a3b8; }
.table-footer { padding: 16px 24px; font-size: 13px; color: #64748b; border-top: 1px solid #f1f5f9; background-color: #f8fafc; }
.flex-footer { display: flex; justify-content: space-between; align-items: center; }
.pagination { display: flex; gap: 8px; }
.page-btn { display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; font-size: 12px; font-weight: 700; color: #475569; background: #ffffff; border: 1px solid #e2e8f0; cursor: pointer; transition: 0.2s; }
.page-btn:hover { background: #f1f5f9; }
.page-btn.active { background: #2563eb; color: #ffffff; border-color: #2563eb; }

@media (max-width: 1024px) {
  .dashboard-grid { grid-template-columns: 1fr; }
}
</style>

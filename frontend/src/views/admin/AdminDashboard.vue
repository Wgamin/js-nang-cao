<script setup>
import { ref, computed } from 'vue'

const today = new Date().toLocaleDateString('vi-VN', { day: '2-digit', month: 'long', year: 'numeric' })
const activeTab = ref('Tháng')

const stats = ref([
    { label: 'Tổng học viên', value: '1,284', icon: 'school', color: '#2563eb', trend: '+12%', trendColor: '#10b981' },
    { label: 'Tổng giáo viên', value: '86', icon: 'record_voice_over', color: '#f97316', trend: 'Ổn định', trendColor: '#64748b' },
    { label: 'Tổng lớp học', value: '42', icon: 'class', color: '#a78bfa', trend: '+4', trendColor: '#10b981' },
    { label: 'Tổng môn học', value: '24', icon: 'menu_book', color: '#64748b', trend: '12 Khối', trendColor: '#64748b' },
])

const rawChartData = [
  { label: 'T01', val: 45 },
  { label: 'T02', val: 65 },
  { label: 'T03', val: 55 },
  { label: 'T04', val: 80 },
  { label: 'T05', val: 70 },
  { label: 'T06', val: 95 },
]

const upcomingClasses = ref([
  { code: 'LỚP ENG-101', name: 'Tiếng Anh Giao tiếp Cơ bản', time: '14:00 - 15:30', room: 'P.302', students: 18, color: '#3b82f6' },
  { code: 'LỚP MATH-S', name: 'Toán Tư duy Nâng cao', time: '15:45 - 17:15', room: 'P.105', students: 12, color: '#f59e0b' },
  { code: 'LỚP PHY-12', name: 'Luyện thi Vật Lý 12', time: '18:00 - 19:30', room: 'Lab 02', students: 25, color: '#8b5cf6' },
  { code: 'LỚP ART-KIDS', name: 'Mỹ thuật Sáng tạo', time: '18:00 - 19:30', room: 'Art Studio', students: 10, color: '#10b981' },
])

const spotlight = {
    role: 'GIÁO VIÊN CỦA THÁNG',
    ten: 'Cô Nguyễn Minh Anh',
    chuyen_mon: 'Tiếng Anh IELTS',
    img: 'https://ui-avatars.com/api/?name=Nguyen+Minh+Anh&background=random'
}

</script>

<template>
  <div class="dashboard-page">
    <div class="db-header">
      <h1>Chào buổi sáng, Admin</h1>
      <p>Dưới đây là tóm tắt hoạt động của trung tâm trong ngày hôm nay.</p>
    </div>

    <div class="stat-grid">
      <div v-for="s in stats" :key="s.label" class="stat-card">
        <div class="stat-top">
          <div class="stat-icon-wrap" :style="{ color: s.color, backgroundColor: s.color + '15' }">
            <span class="material-symbols-outlined icon-24" style="font-variation-settings: 'FILL' 1;">{{ s.icon }}</span>
          </div>
          <span class="stat-trend" :style="{ color: s.trendColor }">
            {{ s.trend }} <span v-if="s.trend.includes('+')" class="material-symbols-outlined icon-14">trending_up</span>
          </span>
        </div>
        <p class="stat-label">{{ s.label }}</p>
        <h3 class="stat-value">{{ s.value }}</h3>
      </div>
    </div>

    <div class="content-grid">
      <div class="col-left">
        <!-- CHart Card -->
        <div class="chart-card">
          <div class="card-header">
            <div>
              <h3>Sự tăng trưởng học viên</h3>
              <p>Thống kê 6 tháng gần nhất (2024)</p>
            </div>
            <div class="chart-tabs">
              <button :class="{ active: activeTab === 'Tuần' }" @click="activeTab = 'Tuần'">Tuần</button>
              <button :class="{ active: activeTab === 'Tháng' }" @click="activeTab = 'Tháng'">Tháng</button>
            </div>
          </div>
          <div class="bar-chart">
            <div v-for="d in rawChartData" :key="d.label" class="bar-group">
              <div class="bar-track">
                <div class="bar-fill" :style="{ height: d.val + '%', opacity: d.val / 100 + 0.2 }"></div>
              </div>
              <span class="bar-lbl">{{ d.label }}</span>
            </div>
          </div>
        </div>

        <div class="bottom-cards">
          <!-- System Notice -->
          <div class="notice-card">
            <p class="notice-label">THÔNG BÁO HỆ THỐNG</p>
            <h3 class="notice-title">Nâng cấp học liệu khối 12 đã hoàn tất</h3>
            <button class="notice-btn">Xem chi tiết</button>
            <span class="material-symbols-outlined notice-decor">star</span>
          </div>

          <!-- Spotlight Teacher -->
          <div class="teacher-card">
            <p class="teacher-label">{{ spotlight.role }}</p>
            <div class="teacher-info-wrap">
              <img :src="spotlight.img" :alt="spotlight.ten" class="teacher-avatar" />
              <div class="teacher-details">
                <h3>{{ spotlight.ten }}</h3>
                <p>Chuyên môn: <br>{{ spotlight.chuyen_mon }}</p>
                <div class="stars">
                  <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1">star</span>
                  <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1">star</span>
                  <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1">star</span>
                  <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1">star</span>
                  <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1">star</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-right">
        <!-- Classes List -->
        <div class="classes-card">
          <div class="card-header c-header">
            <h3>Lớp sắp diễn ra</h3>
            <router-link to="/admin/schedules" class="see-all">Xem tất cả</router-link>
          </div>
          <div class="class-list">
            <div v-for="c in upcomingClasses" :key="c.code" class="class-item">
              <div class="class-border" :style="{ backgroundColor: c.color }"></div>
              <div class="class-content">
                <div class="class-top">
                  <span class="class-code" :style="{ color: c.color }">{{ c.code }}</span>
                  <span class="class-time">{{ c.time }}</span>
                </div>
                <h4 class="class-name">{{ c.name }}</h4>
                <div class="class-bottom">
                  <span class="class-meta"><span class="material-symbols-outlined icon-14">meeting_room</span> {{ c.room }}</span>
                  <span class="class-meta"><span class="material-symbols-outlined icon-14">group</span> {{ c.students }} HV</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Room Status -->
        <div class="room-status-card">
          <h3 class="room-title">TRẠNG THÁI PHÒNG HỌC</h3>
          <div class="room-pills">
            <span class="rpill rgreen">12 Trống</span>
            <span class="rpill rred">08 Đang sử dụng</span>
            <span class="rpill rgray">04 Bảo trì</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.dashboard-page {
  animation: fadeIn 0.4s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.db-header {
  margin-bottom: 24px;
}

.db-header h1 {
  font-size: 26px;
  font-weight: 800;
  color: #1e293b;
  margin: 0 0 4px 0;
}

.db-header p {
  font-size: 14px;
  color: #64748b;
  margin: 0;
  font-weight: 500;
}

/* Stat Grid */
.stat-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
  margin-bottom: 24px;
}

.stat-card {
  background-color: #ffffff;
  border-radius: 16px;
  padding: 24px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
}

.stat-top {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 12px;
}

.stat-icon-wrap {
  width: 40px;
  height: 40px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.icon-24 { font-size: 24px; }
.icon-14 { font-size: 14px; }

.stat-trend {
  font-size: 12px;
  font-weight: 700;
  display: flex;
  align-items: center;
  gap: 4px;
  background-color: #f8fafc;
  padding: 4px 8px;
  border-radius: 999px;
}

.stat-label {
  font-size: 13px;
  color: #64748b;
  font-weight: 600;
  margin: 0 0 4px 0;
}

.stat-value {
  font-size: 28px;
  font-weight: 800;
  color: #0f172a;
  margin: 0;
}

/* Content Grid */
.content-grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 20px;
}

.col-left {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

/* Chart Card */
.chart-card {
  background-color: #ffffff;
  border-radius: 16px;
  padding: 24px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 32px;
}

.card-header h3 {
  font-size: 16px;
  font-weight: 700;
  color: #1e293b;
  margin: 0 0 4px 0;
}

.card-header p {
  font-size: 13px;
  color: #64748b;
  margin: 0;
}

.chart-tabs {
  background-color: #f1f5f9;
  padding: 4px;
  border-radius: 99px;
  display: flex;
  gap: 4px;
}

.chart-tabs button {
  background: transparent;
  border: none;
  padding: 6px 16px;
  border-radius: 99px;
  font-size: 12px;
  font-weight: 600;
  color: #64748b;
  cursor: pointer;
  transition: all 0.2s;
}

.chart-tabs button.active {
  background-color: #1d4ed8;
  color: #ffffff;
}

.bar-chart {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  height: 200px;
  padding: 0 20px;
  gap: 20px;
}

.bar-group {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  height: 100%;
}

.bar-track {
  width: 32px;
  height: 100%;
  position: relative;
  display: flex;
  align-items: flex-end;
  justify-content: center;
}

.bar-fill {
  width: 100%;
  background-color: #1d4ed8;
  border-radius: 6px 6px 0 0;
}

.bar-lbl {
  font-size: 12px;
  font-weight: 600;
  color: #64748b;
}

/* Bottom Cards */
.bottom-cards {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

.notice-card {
  background-color: #1d4ed8;
  border-radius: 16px;
  padding: 24px;
  color: #ffffff;
  position: relative;
  overflow: hidden;
  box-shadow: 0 10px 15px -3px rgba(29, 78, 216, 0.3);
}

.notice-label {
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.1em;
  color: rgba(255, 255, 255, 0.8);
  margin: 0 0 12px 0;
}

.notice-title {
  font-size: 18px;
  font-weight: 700;
  margin: 0 0 20px 0;
  line-height: 1.4;
  position: relative;
  z-index: 2;
}

.notice-btn {
  background-color: rgba(255, 255, 255, 0.2);
  border: none;
  padding: 8px 16px;
  border-radius: 8px;
  color: #ffffff;
  font-weight: 600;
  font-size: 12px;
  cursor: pointer;
  position: relative;
  z-index: 2;
}

.notice-decor {
  position: absolute;
  right: -20px;
  bottom: -20px;
  font-size: 120px;
  color: rgba(255, 255, 255, 0.1);
  transform: rotate(15deg);
}

.teacher-card {
  background-color: #f8fafc;
  border-radius: 16px;
  padding: 24px;
  border: 1px solid #e2e8f0;
}

.teacher-label {
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.1em;
  color: #1d4ed8;
  margin: 0 0 16px 0;
}

.teacher-info-wrap {
  display: flex;
  align-items: center;
  gap: 16px;
}

.teacher-avatar {
  width: 64px;
  height: 64px;
  border-radius: 12px;
  object-fit: cover;
}

.teacher-details h3 {
  font-size: 15px;
  font-weight: 700;
  color: #1e293b;
  margin: 0 0 4px 0;
}

.teacher-details p {
  font-size: 12px;
  color: #64748b;
  margin: 0 0 8px 0;
  line-height: 1.4;
}

.stars {
  display: flex;
  gap: 2px;
  color: #fbbf24;
  font-size: 14px;
}

/* Col Right */
.col-right {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

/* Classes Card */
.classes-card {
  background-color: #f8fafc;
  border-radius: 16px;
  padding: 24px;
}

.classes-card .c-header h3 {
  margin: 0;
}

.see-all {
  font-size: 13px;
  font-weight: 700;
  color: #1d4ed8;
  text-decoration: none;
}

.class-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.class-item {
  background-color: #ffffff;
  border-radius: 12px;
  display: flex;
  overflow: hidden;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.class-border {
  width: 4px;
}

.class-content {
  padding: 16px;
  flex: 1;
}

.class-top {
  display: flex;
  justify-content: space-between;
  margin-bottom: 6px;
}

.class-code {
  font-size: 10px;
  font-weight: 800;
  letter-spacing: 0.05em;
  text-transform: uppercase;
}

.class-time {
  font-size: 11px;
  font-weight: 600;
  color: #64748b;
}

.class-name {
  font-size: 14px;
  font-weight: 700;
  color: #1e293b;
  margin: 0 0 8px 0;
}

.class-bottom {
  display: flex;
  gap: 16px;
}

.class-meta {
  font-size: 12px;
  font-weight: 500;
  color: #64748b;
  display: flex;
  align-items: center;
  gap: 4px;
}

/* Room Status Card */
.room-status-card {
  background-color: #f8fafc;
  border-radius: 16px;
  padding: 24px;
}

.room-title {
  font-size: 13px;
  font-weight: 700;
  color: #64748b;
  letter-spacing: 0.05em;
  margin: 0 0 16px 0;
}

.room-pills {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
}

.rpill {
  font-size: 12px;
  font-weight: 700;
  padding: 8px 16px;
  border-radius: 999px;
  display: inline-block;
}

.rgreen {
  background-color: #d1fae5;
  color: #047857;
}

.rred {
  background-color: #fee2e2;
  color: #b91c1c;
}

.rgray {
  background-color: #f1f5f9;
  color: #475569;
}
</style>

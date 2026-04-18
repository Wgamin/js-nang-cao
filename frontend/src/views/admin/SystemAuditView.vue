<script setup>
import { ref } from 'vue'

const overCapacityAlerts = ref([
  { name: 'Lớp IELTS 01', code: 'CR-2024-IEL01', current: 26, max: 25, room: 'Phòng A102' },
  { name: 'Giao tiếp Cơ bản 04', code: 'CR-2024-COM04', current: 32, max: 30, room: 'Phòng B205' },
]);

const missingTeacherClasses = ref([
  { name: 'ReactJS Advanced', schedule: 'Thứ 2, 4, 6 (18:30)' },
  { name: 'Python for Data Science', schedule: 'Thứ 3, 5, 7 (19:00)' },
  { name: 'Business English B2', schedule: 'Chủ nhật (08:00)' },
]);

const missingSubjectCourses = ref([
  { id: 1, courseName: 'Fullstack Web Dev', classCode: 'WD-09', issue: 'Thiếu môn "NodeJS Fundamentals"' },
  { id: 2, courseName: 'English for IT', classCode: 'EIT-02', issue: 'Thiếu môn "Technical Writing"' },
]);
</script>

<template>
  <div class="admin-page">
    <!-- Header Section -->
    <div class="headers">
      <div class="header-left">
        <h1 class="title">Kiểm tra dữ liệu hệ thống (v2)</h1>
        <p class="subtitle max-w-2xl">Hệ thống tự động quét và phát hiện các sai sót về sĩ số, phân bổ nhân sự và cấu trúc chương trình học.</p>
      </div>
    </div>

    <!-- Bento Grid Summary -->
    <div class="stats-container grid-4">
      <!-- Card 1 -->
      <div class="alert-card border-red">
        <div class="card-top">
          <span class="icon-box bg-red-light text-red">
            <span class="material-symbols-outlined icon-filled">warning</span>
          </span>
          <span class="badge badge-danger outline-red">KHẨN CẤP</span>
        </div>
        <div class="card-bottom">
          <p class="card-label">Vượt sĩ số</p>
          <h3 class="card-value">12 Lớp</h3>
        </div>
      </div>

      <!-- Card 2 -->
      <div class="alert-card border-orange">
        <div class="card-top">
          <span class="icon-box bg-orange-light text-orange">
            <span class="material-symbols-outlined icon-filled">person_off</span>
          </span>
        </div>
        <div class="card-bottom">
          <p class="card-label">Thiếu giáo viên</p>
          <h3 class="card-value text-orange">08 Lớp</h3>
        </div>
      </div>

      <!-- Card 3 -->
      <div class="alert-card">
        <div class="card-top">
          <span class="icon-box bg-blue-light text-blue">
            <span class="material-symbols-outlined icon-filled">menu_book</span>
          </span>
        </div>
        <div class="card-bottom">
          <p class="card-label">Thiếu môn học</p>
          <h3 class="card-value text-blue">03 Lớp</h3>
        </div>
      </div>

      <!-- Card 4 -->
      <div class="alert-card primary-alert-card text-center flex-center flex-col">
        <span class="material-symbols-outlined icon-large mb-2">verified_user</span>
        <p class="font-bold">Đang quét dữ liệu</p>
        <p class="text-xs opacity-80 mt-1">Lần cuối: 5 phút trước</p>
      </div>
    </div>

    <!-- Alerts Canvas -->
    <div class="canvas-grid">
      <!-- Category: Vượt Sĩ Số -->
      <section class="section-block">
        <div class="section-title-wrapper">
          <div class="indicator bg-red"></div>
          <h3 class="section-title">Cảnh báo Vượt sĩ số tối đa</h3>
        </div>
        <div class="grid-2">
          <div v-for="alert in overCapacityAlerts" :key="alert.code" class="over-capacity-item border-left-red">
            <div class="capacity-circle">
              {{ alert.current }}/{{ alert.max }}
            </div>
            <div class="flex-1">
              <h4 class="item-title">{{ alert.name }}</h4>
              <p class="item-subtitle italic">Mã lớp: {{ alert.code }}</p>
              <div class="badge-group">
                <span class="badge badge-danger-small uppercase">Critical</span>
                <span class="badge badge-gray-small uppercase">{{ alert.room }}</span>
              </div>
            </div>
            <button class="action-arrow">
              <span class="material-symbols-outlined">chevron_right</span>
            </button>
          </div>
        </div>
      </section>

      <!-- Category: Thiếu Giáo Viên -->
      <section class="section-block mt-24">
        <div class="section-title-wrapper">
          <div class="indicator bg-orange"></div>
          <h3 class="section-title text-orange">Lớp học chưa gán giáo viên</h3>
        </div>
        <div class="content-box p-24">
          <div class="grid-3">
            <div v-for="cls in missingTeacherClasses" :key="cls.name" class="teacher-missing-card relative overflow-hidden group">
              <div class="bg-icon group-hover-visible">
                <span class="material-symbols-outlined text-60">person_search</span>
              </div>
              <h4 class="item-title z-10 relative">{{ cls.name }}</h4>
              <p class="item-subtitle mb-16 z-10 relative">Lịch học: {{ cls.schedule }}</p>
              
              <div class="warning-bar text-orange bg-orange-light z-10 relative">
                <span class="material-symbols-outlined font-14">priority_high</span>
                Chưa gán GV
              </div>
              
              <button class="btn btn-block btn-gray text-primary z-10 relative hover-primary">
                GÁN GIÁO VIÊN NGAY
              </button>
            </div>
          </div>
        </div>
      </section>

      <!-- Category: Thiếu Môn Học -->
      <section class="section-block mt-24">
        <div class="section-title-wrapper">
          <div class="indicator bg-blue"></div>
          <h3 class="section-title">Cấu trúc chương trình học thiếu sót</h3>
        </div>
        <div class="content-box">
          <div class="table-container no-min-height">
            <table class="user-table">
              <thead>
                <tr>
                  <th style="width: 30%">Khóa học</th>
                  <th style="width: 20%">Lớp</th>
                  <th style="width: 35%">Tình trạng môn học</th>
                  <th style="width: 15%">Thao tác</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="course in missingSubjectCourses" :key="course.id" class="table-row">
                  <td>
                    <div class="font-semibold text-14">{{ course.courseName }}</div>
                  </td>
                  <td>
                    <span class="badge badge-gray-soft">{{ course.classCode }}</span>
                  </td>
                  <td>
                    <div class="flex items-center gap-2 text-red font-medium">
                      <span class="material-symbols-outlined font-16">warning</span>
                      {{ course.issue }}
                    </div>
                  </td>
                  <td>
                    <button class="btn-link text-primary uppercase">Thêm môn</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<style scoped>
/* Base Styles */
.admin-page { background-color: #f8fafc; min-height: 100vh; padding: 0 4px 40px 4px; font-family: 'Inter', system-ui, -apple-system, sans-serif; color: #1e293b; }
.headers { margin-bottom: 32px; }
.title { font-size: 28px; font-weight: 800; color: #0f172a; margin: 0 0 8px 0; }
.subtitle { font-size: 14px; color: #64748b; margin: 0; line-height: 1.5; }
.max-w-2xl { max-width: 42rem; }

/* Grids */
.stats-container { display: grid; gap: 24px; margin-bottom: 40px; }
.grid-4 { grid-template-columns: repeat(4, 1fr); }
.grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
.grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }

/* Colors & Helpers */
.bg-red-light { background-color: #fef2f2; }
.text-red { color: #ef4444; }
.bg-red { background-color: #ef4444; }
.border-red { border-left-color: #ef4444 !important; border-left-width: 4px !important; }

.bg-orange-light { background-color: #fffbeb; }
.text-orange { color: #f59e0b; }
.bg-orange { background-color: #f59e0b; }
.border-orange { border-left-color: #f59e0b !important; border-left-width: 4px !important; }

.bg-blue-light { background-color: #eff6ff; }
.text-blue { color: #2563eb; }
.bg-blue { background-color: #2563eb; }

.bg-gray-light { background-color: #f8fafc; }
.text-gray { color: #64748b; }

.text-primary { color: #2563eb; }
.hover-primary:hover { background-color: #2563eb; color: white !important; }

.flex-1 { flex: 1; }
.flex-center { display: flex; align-items: center; justify-content: center; }
.flex-col { flex-direction: column; }
.items-center { align-items: center; }
.gap-2 { gap: 8px; }
.z-10 { z-index: 10; }
.relative { position: relative; }
.font-bold { font-weight: 700; }
.font-semibold { font-weight: 600; }
.font-medium { font-weight: 500; }
.italic { font-style: italic; }
.uppercase { text-transform: uppercase; }
.text-center { text-align: center; }
.mt-24 { margin-top: 24px; }
.mb-16 { margin-bottom: 16px; }
.mb-2 { margin-bottom: 8px; }
.mt-1 { margin-top: 4px; }
.text-xs { font-size: 12px; }
.text-14 { font-size: 14px; }
.font-14 { font-size: 14px; }
.font-16 { font-size: 16px; }
.opacity-80 { opacity: 0.8; }
.p-24 { padding: 24px; }

/* Typography icons */
.icon-filled { font-variation-settings: 'FILL' 1; }
.icon-large { font-size: 36px; }

/* Alert Cards */
.alert-card { background-color: #ffffff; padding: 24px; border-radius: 12px; display: flex; flex-direction: column; justify-content: space-between; border: 1px solid #f1f5f9; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); min-height: 140px; }
.primary-alert-card { background: #2563eb; color: #ffffff; border: none; box-shadow: 0 10px 15px -3px rgba(37,99,235,0.2); }
.card-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; }
.icon-box { display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 8px; }
.card-bottom { display: flex; flex-direction: column; gap: 4px; }
.card-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; margin: 0; }
.card-value { font-size: 24px; font-weight: 800; color: #0f172a; margin: 0; }

/* Section Title */
.section-block { display: flex; flex-direction: column; gap: 16px; }
.section-title-wrapper { display: flex; align-items: center; gap: 12px; padding: 0 8px; margin-bottom: 16px; }
.indicator { width: 8px; height: 24px; border-radius: 99px; }
.section-title { font-size: 18px; font-weight: 700; margin: 0; color: #0f172a; }

/* Over Capacity Items */
.over-capacity-item { background: #ffffff; padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 20px; border: 1px solid #f1f5f9; border-left: 4px solid transparent; box-shadow: 0 1px 2px rgba(0,0,0,0.05); transition: transform 0.2s; cursor: pointer; }
.border-left-red { border-left-color: #ef4444; }
.over-capacity-item:hover { transform: translateX(4px); }
.capacity-circle { width: 64px; height: 64px; border-radius: 50%; background: #fef2f2; color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: 900; }
.item-title { font-size: 16px; font-weight: 700; color: #0f172a; margin: 0 0 4px 0; }
.item-subtitle { font-size: 13px; color: #64748b; margin: 0 0 12px 0; }
.badge-group { display: flex; gap: 8px; }
.action-arrow { background: transparent; border: none; color: #94a3b8; cursor: pointer; padding: 8px; border-radius: 8px; transition: 0.2s; }
.action-arrow:hover { color: #2563eb; background-color: #f8fafc; }

/* Missing Teacher Card */
.teacher-missing-card { background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; display: flex; flex-direction: column; }
.bg-icon { position: absolute; top: 0; right: 0; padding: 16px; opacity: 0.05; transition: opacity 0.3s; pointer-events: none; }
.text-60 { font-size: 60px; color: #0f172a; }
.group:hover .group-hover-visible { opacity: 0.1; }
.warning-bar { display: inline-flex; align-items: center; gap: 8px; padding: 8px 12px; border-radius: 8px; font-size: 13px; font-weight: 700; margin-bottom: 16px; align-self: flex-start;}

.btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 16px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer; transition: all 0.2s ease; border: none; }
.btn-block { width: 100%; }
.btn-gray { background-color: #e2e8f0; }
.btn-link { background: transparent; border: none; font-size: 12px; font-weight: 700; cursor: pointer; padding: 4px 8px; border-radius: 4px; transition: 0.2s; }
.btn-link:hover { text-decoration: underline; background: #eff6ff; }

/* Badges */
.badge { display: inline-flex; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: 700; }
.badge-danger { background-color: #fef2f2; color: #ef4444; }
.outline-red { border: 1px solid rgba(239, 68, 68, 0.2); }
.badge-danger-small { background-color: #fef2f2; color: #ef4444; padding: 2px 6px; }
.badge-gray-small { background-color: #f1f5f9; color: #64748b; padding: 2px 6px; }
.badge-gray-soft { background-color: #f1f5f9; color: #475569; padding: 4px 12px; border-radius: 99px; font-size: 12px; font-weight: 600; }

/* Content Box & Table */
.content-box { background-color: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px 0 rgba(0,0,0,0.05); overflow: hidden; }
.table-container { overflow-x: auto; }
.no-min-height { min-height: auto; }
.user-table { width: 100%; border-collapse: collapse; }
.user-table th { text-align: left; padding: 14px 24px; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; background-color: #f8fafc; border-bottom: 1px solid #f1f5f9; }
.table-row { transition: background-color 0.2s ease; }
.table-row:hover { background-color: #f8fafc; }
.user-table td { padding: 16px 24px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }

@media (max-width: 1024px) {
  .grid-4, .grid-3, .grid-2 { grid-template-columns: 1fr; }
}
</style>

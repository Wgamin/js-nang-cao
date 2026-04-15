<template>
  <div class="space-y-8">
    <!-- Header Section -->
    <div class="mb-8">
      <h2 class="text-3xl font-extrabold tracking-tight text-on-surface mb-2">Kiểm tra dữ liệu hệ thống (v2)</h2>
      <p class="text-on-surface-variant max-w-2xl">Hệ thống tự động quét và phát hiện các sai sót về sĩ số, phân bổ nhân sự và cấu trúc chương trình học.</p>
    </div>

    <!-- Bento Grid Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
      <div class="bg-surface-container-lowest p-6 rounded-xl shadow-[0px_20px_40px_rgba(0,74,198,0.05)] flex flex-col justify-between border border-outline-variant/10">
        <div class="flex justify-between items-start mb-4">
          <span class="p-2 bg-error/10 text-error rounded-lg">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">warning</span>
          </span>
          <span class="text-xs font-bold text-error bg-error/5 px-2 py-1 rounded">KHẨN CẤP</span>
        </div>
        <div>
          <p class="text-label-md text-on-surface-variant uppercase tracking-wider font-semibold text-[10px] mb-1">Vượt sĩ số</p>
          <h3 class="text-2xl font-bold">12 Lớp</h3>
        </div>
      </div>
      <div class="bg-surface-container-lowest p-6 rounded-xl shadow-[0px_20px_40px_rgba(0,74,198,0.05)] flex flex-col justify-between border-l-4 border-error border-t border-r border-b border-outline-variant/10">
        <div class="flex justify-between items-start mb-4">
          <span class="p-2 bg-tertiary/10 text-tertiary rounded-lg">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">person_off</span>
          </span>
        </div>
        <div>
          <p class="text-label-md text-on-surface-variant uppercase tracking-wider font-semibold text-[10px] mb-1">Thiếu giáo viên</p>
          <h3 class="text-2xl font-bold text-tertiary">08 Lớp</h3>
        </div>
      </div>
      <div class="bg-surface-container-lowest p-6 rounded-xl shadow-[0px_20px_40px_rgba(0,74,198,0.05)] flex flex-col justify-between border border-outline-variant/10">
        <div class="flex justify-between items-start mb-4">
          <span class="p-2 bg-blue-100 text-blue-600 rounded-lg">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">menu_book</span>
          </span>
        </div>
        <div>
          <p class="text-label-md text-on-surface-variant uppercase tracking-wider font-semibold text-[10px] mb-1">Thiếu môn học</p>
          <h3 class="text-2xl font-bold text-blue-700">03 Lớp</h3>
        </div>
      </div>
      <div class="bg-gradient-to-br from-primary to-primary-container p-6 rounded-xl shadow-lg text-white flex flex-col justify-center items-center text-center">
        <span class="material-symbols-outlined text-4xl mb-2">verified_user</span>
        <p class="font-bold">Đang quét dữ liệu</p>
        <p class="text-xs opacity-80">Lần cuối: 5 phút trước</p>
      </div>
    </div>

    <!-- Alerts Canvas -->
    <div class="space-y-6">
      <!-- Category: Vượt Sĩ Số -->
      <section>
        <div class="flex items-center gap-2 mb-4 px-2">
          <div class="w-2 h-6 bg-error rounded-full"></div>
          <h3 class="font-bold text-lg">Cảnh báo Vượt sĩ số tối đa</h3>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
          <div v-for="alert in overCapacityAlerts" :key="alert.code" class="bg-white p-5 rounded-xl flex items-center gap-5 border-l-4 border-error hover:translate-x-1 transition-transform cursor-pointer shadow-sm border-t border-r border-b border-outline-variant/10">
            <div class="w-16 h-16 bg-error-container text-on-error-container rounded-full flex items-center justify-center font-black text-lg">
              {{ alert.current }}/{{ alert.max }}
            </div>
            <div class="flex-1">
              <h4 class="font-bold text-on-surface text-lg">{{ alert.name }}</h4>
              <p class="text-sm text-on-surface-variant mb-3 italic">Mã lớp: {{ alert.code }}</p>
              <div class="flex gap-2">
                <span class="px-2 py-0.5 bg-error/10 text-error text-[10px] font-bold rounded uppercase">Critical</span>
                <span class="px-2 py-0.5 bg-surface-container-low text-on-surface-variant text-[10px] font-bold rounded uppercase">{{ alert.room }}</span>
              </div>
            </div>
            <button class="p-2 text-outline hover:text-primary transition-colors">
              <span class="material-symbols-outlined">chevron_right</span>
            </button>
          </div>
        </div>
      </section>

      <!-- Category: Thiếu Giáo Viên -->
      <section>
        <div class="flex items-center gap-2 mb-4 px-2">
          <div class="w-2 h-6 bg-tertiary rounded-full"></div>
          <h3 class="font-bold text-lg text-tertiary">Lớp học chưa gán giáo viên</h3>
        </div>
        <div class="bg-surface-container-low rounded-2xl p-6 border border-outline-variant/10 shadow-sm">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div v-for="cls in missingTeacherClasses" :key="cls.name" class="bg-surface-container-lowest p-5 rounded-xl shadow-sm relative overflow-hidden group border border-outline-variant/10">
              <div class="absolute top-0 right-0 p-2 opacity-10 group-hover:opacity-20 transition-opacity">
                <span class="material-symbols-outlined text-6xl">person_search</span>
              </div>
              <h4 class="font-bold text-on-surface">{{ cls.name }}</h4>
              <p class="text-xs text-on-surface-variant mb-4">Lịch học: {{ cls.schedule }}</p>
              <div class="flex items-center gap-2 text-tertiary font-bold text-sm bg-tertiary/10 p-2 rounded-lg mb-4">
                <span class="material-symbols-outlined text-sm">priority_high</span>
                Chưa gán GV
              </div>
              <button class="w-full py-2 bg-surface-container-high text-primary font-bold text-xs rounded hover:bg-primary hover:text-white transition-colors">
                GÁN GIÁO VIÊN NGAY
              </button>
            </div>
          </div>
        </div>
      </section>

      <!-- Category: Thiếu Môn Học -->
      <section>
        <div class="flex items-center gap-2 mb-4 px-2">
          <div class="w-2 h-6 bg-blue-500 rounded-full"></div>
          <h3 class="font-bold text-lg">Cấu trúc chương trình học thiếu sót</h3>
        </div>
        <div class="bg-white rounded-xl shadow-[0px_20px_40px_rgba(0,74,198,0.05)] overflow-hidden border border-outline-variant/10">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-surface-container-low">
                <th class="px-6 py-4 text-label-md uppercase tracking-widest font-bold text-[10px] text-on-surface-variant">Khóa học</th>
                <th class="px-6 py-4 text-label-md uppercase tracking-widest font-bold text-[10px] text-on-surface-variant">Lớp</th>
                <th class="px-6 py-4 text-label-md uppercase tracking-widest font-bold text-[10px] text-on-surface-variant">Tình trạng môn học</th>
                <th class="px-6 py-4 text-label-md uppercase tracking-widest font-bold text-[10px] text-on-surface-variant">Thao tác</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/10">
              <tr v-for="course in missingSubjectCourses" :key="course.id" class="hover:bg-surface-container-high transition-colors">
                <td class="px-6 py-5">
                  <div class="font-semibold">{{ course.courseName }}</div>
                </td>
                <td class="px-6 py-5">
                  <span class="px-3 py-1 bg-surface-container rounded-full text-xs font-medium">{{ course.classCode }}</span>
                </td>
                <td class="px-6 py-5 text-error font-medium flex items-center gap-2">
                  <span class="material-symbols-outlined text-sm">warning</span>
                  {{ course.issue }}
                </td>
                <td class="px-6 py-5">
                  <button class="text-primary font-bold text-xs hover:underline uppercase tracking-tight">Thêm môn</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
const overCapacityAlerts = [
  { name: 'Lớp IELTS 01', code: 'CR-2024-IEL01', current: 26, max: 25, room: 'Phòng A102' },
  { name: 'Giao tiếp Cơ bản 04', code: 'CR-2024-COM04', current: 32, max: 30, room: 'Phòng B205' },
];

const missingTeacherClasses = [
  { name: 'ReactJS Advanced', schedule: 'Thứ 2, 4, 6 (18:30)' },
  { name: 'Python for Data Science', schedule: 'Thứ 3, 5, 7 (19:00)' },
  { name: 'Business English B2', schedule: 'Chủ nhật (08:00)' },
];

const missingSubjectCourses = [
  { id: 1, courseName: 'Fullstack Web Dev', classCode: 'WD-09', issue: 'Thiếu môn "NodeJS Fundamentals"' },
  { id: 2, courseName: 'English for IT', classCode: 'EIT-02', issue: 'Thiếu môn "Technical Writing"' },
];
</script>

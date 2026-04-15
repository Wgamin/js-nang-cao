<template>
  <div class="space-y-8">
    <!-- Header Section -->
    <div class="mb-10">
      <h2 class="text-3xl font-black tracking-tight text-on-surface mb-2">Cảnh Báo Chuyên Cần</h2>
      <p class="text-on-surface-variant max-w-2xl leading-relaxed">Theo dõi và quản lý các học viên có tỉ lệ vắng học vượt mức quy định (20%). Hệ thống tự động cập nhật danh sách dựa trên dữ liệu điểm danh hàng ngày.</p>
    </div>

    <!-- Bento Grid Layout -->
    <div class="grid grid-cols-12 gap-6">
      <div class="col-span-12 lg:col-span-4 bg-primary text-white p-8 rounded-xl relative overflow-hidden flex flex-col justify-between shadow-xl shadow-primary/20">
        <div class="relative z-10">
          <span class="inline-flex items-center px-3 py-1 bg-white/20 rounded-full text-xs font-bold tracking-widest uppercase mb-4">Live Alert</span>
          <h3 class="text-4xl font-black mb-1">42</h3>
          <p class="text-primary-fixed-dim text-lg font-medium">Học viên đang bị cảnh báo</p>
        </div>
        <div class="relative z-10 mt-8">
          <p class="text-sm opacity-80 mb-4 leading-relaxed">Tăng 12% so với tháng trước. Vui lòng liên hệ phụ huynh để cập nhật tình hình học tập.</p>
          <button class="px-6 py-2 bg-white text-primary rounded-lg font-bold text-sm hover:bg-primary-fixed transition-colors">
            Xuất báo cáo PDF
          </button>
        </div>
        <div class="absolute -right-8 -bottom-8 w-48 h-48 bg-white/10 rounded-full blur-3xl"></div>
      </div>

      <div class="col-span-12 lg:col-span-8 grid grid-cols-2 gap-6">
        <div class="bg-surface-container-lowest p-6 rounded-xl flex items-center gap-6 border border-outline-variant/10 shadow-sm">
          <div class="w-14 h-14 bg-error-container rounded-xl flex items-center justify-center text-error">
            <span class="material-symbols-outlined text-3xl">trending_up</span>
          </div>
          <div>
            <p class="text-label-md text-on-surface-variant font-semibold uppercase tracking-wider mb-1">Tỉ lệ vắng trung bình</p>
            <p class="text-2xl font-black text-on-surface">14.5%</p>
          </div>
        </div>
        <div class="bg-surface-container-lowest p-6 rounded-xl flex items-center gap-6 border border-outline-variant/10 shadow-sm">
          <div class="w-14 h-14 bg-secondary-container rounded-xl flex items-center justify-center text-on-secondary-container">
            <span class="material-symbols-outlined text-3xl">history</span>
          </div>
          <div>
            <p class="text-label-md text-on-surface-variant font-semibold uppercase tracking-wider mb-1">Phản hồi phụ huynh</p>
            <p class="text-2xl font-black text-on-surface">28/42</p>
          </div>
        </div>
        <div class="col-span-2 bg-surface-container-low p-6 rounded-xl border-none">
          <div class="flex justify-between items-center mb-4">
            <h4 class="font-bold text-on-surface">Diễn biến nghỉ học trong tuần</h4>
            <div class="flex gap-2">
              <span class="w-3 h-3 bg-primary rounded-full"></span>
              <span class="text-xs font-bold text-on-surface-variant uppercase">Tuần này</span>
            </div>
          </div>
          <div class="flex items-end justify-between h-24 px-4">
            <div class="w-12 bg-primary/20 rounded-t-lg h-[40%]"></div>
            <div class="w-12 bg-primary/20 rounded-t-lg h-[60%]"></div>
            <div class="w-12 bg-primary/20 rounded-t-lg h-[30%]"></div>
            <div class="w-12 bg-primary rounded-t-lg h-[85%]"></div>
            <div class="w-12 bg-primary/20 rounded-t-lg h-[50%]"></div>
            <div class="w-12 bg-primary/20 rounded-t-lg h-[45%]"></div>
            <div class="w-12 bg-primary/20 rounded-t-lg h-[20%]"></div>
          </div>
          <div class="flex justify-between mt-2 text-[10px] font-bold text-on-surface-variant opacity-60">
            <span>THỨ 2</span><span>THỨ 3</span><span>THỨ 4</span><span>THỨ 5</span><span>THỨ 6</span><span>THỨ 7</span><span>CN</span>
          </div>
        </div>
      </div>

      <div class="col-span-12 mt-4">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-xl font-black text-on-surface">Danh sách học viên vắng nhiều</h3>
          <div class="flex gap-3">
            <select class="bg-surface-container-lowest border-none text-xs font-bold py-2 rounded-lg focus:ring-primary/20 shadow-sm">
              <option>Tất cả các lớp</option>
              <option>IELTS Foundation - A2</option>
              <option>Business English - B1</option>
            </select>
            <button class="bg-surface-container-lowest p-2 rounded-lg text-on-surface-variant shadow-sm hover:text-primary border border-outline-variant/10">
              <span class="material-symbols-outlined">filter_list</span>
            </button>
          </div>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl overflow-hidden border border-outline-variant/10 shadow-sm">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-surface-container-low">
                <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-widest">Học viên</th>
                <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-widest">Lớp học</th>
                <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-widest text-center">Buổi vắng</th>
                <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-widest">Tỉ lệ vắng</th>
                <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-widest">Ngày vắng gần nhất</th>
                <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-widest text-right">Thao tác</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/10">
              <tr v-for="student in alertStudents" :key="student.id" class="hover:bg-surface-container-high transition-colors group">
                <td class="px-6 py-5">
                  <div class="flex items-center gap-3">
                    <img :src="student.avatar" class="w-10 h-10 rounded-full object-cover" />
                    <div>
                      <p class="font-bold text-on-surface">{{ student.name }}</p>
                      <p class="text-xs text-on-surface-variant">ID: {{ student.studentId }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-5">
                  <span class="text-sm font-medium text-on-surface-variant">{{ student.className }}</span>
                </td>
                <td class="px-6 py-5 text-center">
                  <span class="text-sm font-black text-on-surface">{{ student.absentCount }}/{{ student.totalCount }}</span>
                </td>
                <td class="px-6 py-5">
                  <div class="flex items-center gap-3">
                    <div class="flex-1 h-2 bg-surface-container-high rounded-full overflow-hidden">
                      <div class="h-full bg-error rounded-full" :style="{ width: student.rate + '%' }"></div>
                    </div>
                    <span class="text-sm font-black text-error">{{ student.rate }}%</span>
                  </div>
                </td>
                <td class="px-6 py-5">
                  <span class="text-sm text-on-surface-variant">{{ student.lastAbsent }}</span>
                </td>
                <td class="px-6 py-5 text-right">
                  <div class="flex justify-end gap-2">
                    <button class="p-2 bg-error/10 text-error rounded-lg hover:bg-error hover:text-white transition-all active:scale-95">
                      <span class="material-symbols-outlined text-[20px]">call</span>
                    </button>
                    <button class="p-2 bg-primary/10 text-primary rounded-lg hover:bg-primary hover:text-white transition-all active:scale-95">
                      <span class="material-symbols-outlined text-[20px]">visibility</span>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
          <div class="px-6 py-6 bg-surface-container-low flex items-center justify-between">
            <p class="text-xs font-bold text-on-surface-variant uppercase tracking-widest">Trang 1 trên 4</p>
            <div class="flex gap-2">
              <button class="w-10 h-10 flex items-center justify-center rounded-lg bg-surface-container-lowest text-on-surface shadow-sm opacity-50 cursor-not-allowed">
                <span class="material-symbols-outlined">chevron_left</span>
              </button>
              <button class="w-10 h-10 flex items-center justify-center rounded-lg bg-primary text-white shadow-lg shadow-primary/20">1</button>
              <button class="w-10 h-10 flex items-center justify-center rounded-lg bg-surface-container-lowest text-on-surface shadow-sm hover:bg-surface-container-highest transition-colors">2</button>
              <button class="w-10 h-10 flex items-center justify-center rounded-lg bg-surface-container-lowest text-on-surface shadow-sm hover:bg-surface-container-highest transition-colors">
                <span class="material-symbols-outlined">chevron_right</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const alertStudents = [
  { id: 1, name: 'Nguyễn Minh Tuấn', studentId: 'STU-9921', className: 'IELTS Master - L2', absentCount: 8, totalCount: 24, rate: 33, lastAbsent: 'Hôm qua, 14:30', avatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuBS3fWw9y8l2UClUAXG1vkdG3Yfm4xogcq0sIvZc8LH_JB1NWleAslCIfCncNOqN6N1JzcqV38MkjRfRVDb6Vw1pIAeOBgChu6H-nL9nvscN4-IPvRfPXbMNYBgIPMM0XGIlylWF4yJMvyY0f8CQKobNnmr6jYFmQ6cZtbdr_0YKaeB58xF2rUVhIYsdKeLCTUnTaLow4WzrSY2fkoiBSi9F8cY8dy2vz3VSovbw4yntmBC-4tzHZlsVlNWQ_r3WzzmoxXhAzRvKDPj' },
  { id: 2, name: 'Trần Thị Mai Anh', studentId: 'STU-8845', className: 'TOEIC 750+ Elite', absentCount: 5, totalCount: 20, rate: 25, lastAbsent: '12/10/2023', avatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuB7l0T-uOZ8bcZESFifVj2PQXe9LolSdGFsOs1pO8n9JXK6CJzRT1foV8epc9jAdH-pR2x1oGOIMz8YC35IV14FFByHG2rAHkKtmdDTogUGEUPjzpC-4TIRMoEfjiGc-kvFFI9EbSLtMrLGGolC4HsIFrMlCIDB3uPT7xMs3fsgTr0s9YV4EzuLX7TwbkykZVjkgBA6vj4hLijusTkrKgrM1Yj0RyEtRmPJ3ya1M5dAU6xbk9KM1vTrlD6fY8wBrwNUZBZHm_pVjzTR' },
];
</script>

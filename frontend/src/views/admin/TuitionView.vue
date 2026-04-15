<template>
  <div class="space-y-8">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
      <div>
        <h2 class="text-3xl font-extrabold tracking-tight text-slate-900">Quản lý Học phí</h2>
        <p class="text-slate-500 mt-1">Theo dõi và quản lý các hóa đơn học tập hàng tháng.</p>
      </div>
      <button class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-primary to-primary-container text-white rounded-xl font-bold shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-95 transition-all">
        <span class="material-symbols-outlined text-lg">add_circle</span>
        <span>Create Invoice</span>
      </button>
    </div>

    <!-- Bento Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="bg-surface-container-lowest p-6 rounded-2xl flex flex-col justify-between min-h-[160px] group border border-outline-variant/10 shadow-sm border-b-2 hover:border-primary transition-all duration-300">
        <div class="flex justify-between items-start">
          <div class="p-3 bg-blue-50 text-primary rounded-xl">
            <span class="material-symbols-outlined">account_balance_wallet</span>
          </div>
          <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">+12.5%</span>
        </div>
        <div>
          <p class="text-sm font-medium text-slate-500 mb-1">Tổng Doanh Thu</p>
          <h3 class="text-2xl font-black text-slate-900">1.250.000.000 <span class="text-sm font-semibold">₫</span></h3>
        </div>
      </div>
      <div class="bg-surface-container-lowest p-6 rounded-xl flex flex-col justify-between min-h-[160px] border border-outline-variant/10 shadow-sm border-b-2 hover:border-secondary transition-all duration-300">
        <div class="flex justify-between items-start">
          <div class="p-3 bg-indigo-50 text-secondary rounded-xl">
            <span class="material-symbols-outlined">pending_actions</span>
          </div>
          <span class="text-xs font-bold text-slate-400 bg-slate-100 px-2 py-1 rounded-full">42 Hóa đơn</span>
        </div>
        <div>
          <p class="text-sm font-medium text-slate-500 mb-1">Chờ Thanh Toán</p>
          <h3 class="text-2xl font-black text-slate-900">320.450.000 <span class="text-sm font-semibold">₫</span></h3>
        </div>
      </div>
      <div class="bg-surface-container-lowest p-6 rounded-xl flex flex-col justify-between min-h-[160px] border border-outline-variant/10 shadow-sm border-b-2 hover:border-error transition-all duration-300">
        <div class="flex justify-between items-start">
          <div class="p-3 bg-red-50 text-error rounded-xl">
            <span class="material-symbols-outlined">event_busy</span>
          </div>
          <span class="text-xs font-bold text-error bg-red-100 px-2 py-1 rounded-full">8 Quá hạn</span>
        </div>
        <div>
          <p class="text-sm font-medium text-slate-500 mb-1">Hóa Đơn Quá Hạn</p>
          <h3 class="text-2xl font-black text-slate-900">54.200.000 <span class="text-sm font-semibold">₫</span></h3>
        </div>
      </div>
    </div>

    <!-- Table Container -->
    <div class="bg-surface-container-lowest rounded-3xl overflow-hidden border border-outline-variant/10 shadow-sm">
      <div class="px-8 py-6 flex items-center justify-between">
        <div class="flex items-center gap-4">
          <h4 class="text-lg font-bold text-slate-900">Danh sách hóa đơn</h4>
          <div class="flex bg-slate-100 p-1 rounded-lg">
            <button class="px-4 py-1.5 text-xs font-bold bg-white text-primary rounded-md shadow-sm">Tất cả</button>
            <button class="px-4 py-1.5 text-xs font-bold text-slate-500 hover:text-primary transition-colors">Đã trả</button>
            <button class="px-4 py-1.5 text-xs font-bold text-slate-500 hover:text-primary transition-colors">Chưa trả</button>
          </div>
        </div>
        <button class="p-2 text-slate-400 hover:text-primary transition-colors">
          <span class="material-symbols-outlined">filter_list</span>
        </button>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-surface-container-low">
              <th class="px-8 py-4 text-[11px] font-black uppercase tracking-wider text-slate-500">Mã hóa đơn</th>
              <th class="px-8 py-4 text-[11px] font-black uppercase tracking-wider text-slate-500">Học sinh</th>
              <th class="px-8 py-4 text-[11px] font-black uppercase tracking-wider text-slate-500">Khóa học / Tháng</th>
              <th class="px-8 py-4 text-[11px] font-black uppercase tracking-wider text-slate-500 text-right">Số tiền</th>
              <th class="px-8 py-4 text-[11px] font-black uppercase tracking-wider text-slate-500">Hạn thanh toán</th>
              <th class="px-8 py-4 text-[11px] font-black uppercase tracking-wider text-slate-500">Trạng thái</th>
              <th class="px-8 py-4 text-[11px] font-black uppercase tracking-wider text-slate-500 text-center">Thao tác</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="invoice in invoices" :key="invoice.id" class="hover:bg-slate-50 transition-colors group">
              <td class="px-8 py-5 font-bold text-primary text-sm">{{ invoice.code }}</td>
              <td class="px-8 py-5">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs" :class="invoice.avatarBgClass">{{ invoice.initials }}</div>
                  <span class="text-sm font-semibold text-slate-900">{{ invoice.student }}</span>
                </div>
              </td>
              <td class="px-8 py-5 text-sm text-slate-600">{{ invoice.course }}</td>
              <td class="px-8 py-5 text-sm font-black text-slate-900 text-right">{{ invoice.amount }} ₫</td>
              <td class="px-8 py-5 text-sm text-slate-500">{{ invoice.dueDate }}</td>
              <td class="px-8 py-5">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold" :class="invoice.statusClass">{{ invoice.status }}</span>
              </td>
              <td class="px-8 py-5">
                <div class="flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                  <button class="p-2 text-slate-400 hover:text-primary transition-colors">
                    <span class="material-symbols-outlined text-lg">visibility</span>
                  </button>
                  <button class="p-2 text-slate-400 hover:text-primary transition-colors">
                    <span class="material-symbols-outlined text-lg">print</span>
                  </button>
                  <button class="p-2 text-slate-400 hover:text-error transition-colors">
                    <span class="material-symbols-outlined text-lg">delete</span>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="px-8 py-6 border-t border-slate-100 flex items-center justify-between bg-surface-container-low/20">
        <p class="text-xs text-slate-500">Hiển thị 1-4 của 120 hóa đơn</p>
        <div class="flex items-center gap-2">
          <button class="p-2 rounded-lg hover:bg-slate-100 disabled:opacity-30" disabled>
            <span class="material-symbols-outlined text-lg">chevron_left</span>
          </button>
          <button class="w-8 h-8 rounded-lg bg-primary text-white text-xs font-bold shadow-sm">1</button>
          <button class="w-8 h-8 rounded-lg hover:bg-white text-slate-600 text-xs font-bold border border-outline-variant/10">2</button>
          <button class="p-2 rounded-lg hover:bg-white border border-outline-variant/10">
            <span class="material-symbols-outlined text-lg">chevron_right</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const invoices = [
  { id: 1, code: '#INV-2024001', student: 'Nguyễn Lâm', initials: 'NL', course: 'IELTS Foundation / Th. 03', amount: '4.500.000', dueDate: '15/03/2024', status: 'Đã thanh toán', statusClass: 'bg-emerald-100 text-emerald-700', avatarBgClass: 'bg-blue-100 text-blue-700' },
  { id: 2, code: '#INV-2024002', student: 'Trần Văn Tú', initials: 'TV', course: 'TOEIC Speaking / Th. 03', amount: '3.200.000', dueDate: '20/03/2024', status: 'Đang chờ', statusClass: 'bg-amber-100 text-amber-700', avatarBgClass: 'bg-purple-100 text-purple-700' },
  { id: 3, code: '#INV-2024003', student: 'Lê Hồng Hạnh', initials: 'LH', course: 'Tiếng Anh Giao Tiếp / Th. 03', amount: '2.800.000', dueDate: '10/03/2024', status: 'Quá hạn', statusClass: 'bg-rose-100 text-rose-700', avatarBgClass: 'bg-rose-100 text-rose-700' },
  { id: 4, code: '#INV-2024004', student: 'Phạm Minh', initials: 'PM', course: 'Luyện thi SAT / Th. 03', amount: '6.500.000', dueDate: '25/03/2024', status: 'Đang chờ', statusClass: 'bg-amber-100 text-amber-700', avatarBgClass: 'bg-blue-100 text-blue-700' },
];
</script>

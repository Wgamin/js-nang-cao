<script setup>
import { ref, computed } from 'vue'

const invoices = ref([
  { id: 1, code: '#INV-2024001', student: 'Nguyễn Lâm', avatarColor: 'blue', initials: 'NL', course: 'IELTS Foundation / Th. 03', amount: '4.500.000', dueDate: '15/03/2024', status: 'Đã thanh toán' },
  { id: 2, code: '#INV-2024002', student: 'Trần Văn Tú', avatarColor: 'rose', initials: 'TV', course: 'TOEIC Speaking / Th. 03', amount: '3.200.000', dueDate: '20/03/2024', status: 'Đang chờ' },
  { id: 3, code: '#INV-2024003', student: 'Lê Hồng Hạnh', avatarColor: 'orange', initials: 'LH', course: 'Tiếng Anh Giao Tiếp / Th. 03', amount: '2.800.000', dueDate: '10/03/2024', status: 'Quá hạn' },
  { id: 4, code: '#INV-2024004', student: 'Phạm Minh', avatarColor: 'indigo', initials: 'PM', course: 'Luyện thi SAT / Th. 03', amount: '6.500.000', dueDate: '25/03/2024', status: 'Đang chờ' },
]);

const searchQuery = ref('')
const statusFilter = ref('')

const filteredInvoices = computed(() => {
  return invoices.value.filter(inv => {
    const matchesSearch = inv.code.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
                          inv.student.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchesStatus = !statusFilter.value || inv.status === statusFilter.value
    return matchesSearch && matchesStatus
  })
})

const quickStats = computed(() => [
  { label: 'Tổng Doanh Thu', value: '1.250.000.000 ₫', sub: '+12.5%', icon: 'account_balance_wallet', color: '#10b981', bg: '#ecfdf5' },
  { label: 'Chờ Thanh Toán', value: '320.450.000 ₫', sub: '42 Hóa đơn', icon: 'pending_actions', color: '#f59e0b', bg: '#fffbeb' },
  { label: 'Hóa Đơn Quá Hạn', value: '54.200.000 ₫', sub: '8 Quá hạn', icon: 'event_busy', color: '#ef4444', bg: '#fef2f2' },
])

function getStatusClass(status) {
  if (status === 'Đã thanh toán') return 'badge-success'
  if (status === 'Đang chờ') return 'badge-warning'
  if (status === 'Quá hạn') return 'badge-danger'
  return 'badge-gray'
}

function getAvatarColor(color) {
  const map = {
    'blue': '#3b82f6',
    'rose': '#e11d48',
    'orange': '#ea580c',
    'indigo': '#4f46e5'
  }
  return map[color] || '#cbd5e1'
}

function deleteInvoice(id) {
  if (confirm('Bạn có chắc chắn muốn xóa hóa đơn này?')) {
    invoices.value = invoices.value.filter(i => i.id !== id)
  }
}
</script>

<template>
  <div class="admin-page">
    <div class="headers">
      <div class="header-left">
        <h1 class="title">Quản lý Học phí</h1>
        <p class="subtitle">Theo dõi và quản lý các hóa đơn học tập hàng tháng.</p>
      </div>
      <button class="btn btn-primary">
        <span class="material-symbols-outlined">add_circle</span>
        Tạo Hóa đơn mới
      </button>
    </div>

    <!-- Quick Stats -->
    <div class="stats-container" style="grid-template-columns: repeat(3, 1fr);">
      <div v-for="stat in quickStats" :key="stat.label" class="stat-card">
        <div class="stat-icon-box" :style="{ color: stat.color, backgroundColor: stat.bg }">
          <span class="material-symbols-outlined">{{ stat.icon }}</span>
        </div>
        <div class="stat-content flex-1 max-w-full">
          <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <p class="stat-label">{{ stat.label }}</p>
            <span class="stat-sub-badge" :style="{ color: stat.color, backgroundColor: stat.bg, fontWeight: 700, fontSize: '11px', padding: '2px 8px', borderRadius: '4px' }">{{ stat.sub }}</span>
          </div>
          <h3 class="stat-value" style="margin-top: 4px;">{{ stat.value }}</h3>
        </div>
      </div>
    </div>

    <!-- Main Card -->
    <div class="content-box">
      <div class="toolbar">
        <div class="search-wrapper">
          <span class="material-symbols-outlined search-icon">search</span>
          <input v-model="searchQuery" type="text" class="input-search" placeholder="Tìm theo mã hoặc tên học viên..." />
        </div>
        
        <div class="filters">
          <div class="select-wrapper">
            <select v-model="statusFilter" class="select-filter">
              <option value="">Tất cả trạng thái</option>
              <option value="Đã thanh toán">Đã thanh toán</option>
              <option value="Đang chờ">Đang chờ</option>
              <option value="Quá hạn">Quá hạn</option>
            </select>
            <span class="material-symbols-outlined select-arrow">expand_more</span>
          </div>
          
          <button class="btn btn-secondary">
            <span class="material-symbols-outlined">filter_list</span>
            Bộ lọc nâng cao
          </button>
        </div>
      </div>

      <!-- Table -->
      <div class="table-container">
        <table class="user-table">
          <thead>
            <tr>
              <th style="width: 15%">Mã hóa đơn</th>
              <th style="width: 20%">Học sinh</th>
              <th style="width: 20%">Khóa học / Tháng</th>
              <th style="width: 15%" class="text-right">Số tiền</th>
              <th style="width: 15%">Hạn thanh toán</th>
              <th style="width: 10%">Trạng thái</th>
              <th style="width: 5%" class="text-right">Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="inv in filteredInvoices" :key="inv.id" class="table-row">
              <td>
                <span class="invoice-code">{{ inv.code }}</span>
              </td>
              <td>
                <div class="user-info">
                  <div class="avatar" :style="{ backgroundColor: getAvatarColor(inv.avatarColor) }">{{ inv.initials }}</div>
                  <span class="user-full-name">{{ inv.student }}</span>
                </div>
              </td>
              <td>
                <span class="course-text">{{ inv.course }}</span>
              </td>
              <td class="text-right">
                <strong class="amount-text">{{ inv.amount }} ₫</strong>
              </td>
              <td>
                <span class="date-text">{{ inv.dueDate }}</span>
              </td>
              <td>
                <span class="badge" :class="getStatusClass(inv.status)">{{ inv.status }}</span>
              </td>
              <td class="text-right">
                <div class="actions">
                  <button class="action-btn" title="Xem chi tiết">
                    <span class="material-symbols-outlined">visibility</span>
                  </button>
                  <button class="action-btn" title="In hóa đơn">
                    <span class="material-symbols-outlined">print</span>
                  </button>
                  <button class="action-btn btn-delete" @click="deleteInvoice(inv.id)" title="Xóa">
                    <span class="material-symbols-outlined">delete</span>
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="filteredInvoices.length === 0">
              <td colspan="7" class="empty-state">
                Không tìm thấy hóa đơn nào phù hợp.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Footer -->
      <div class="table-footer flex-footer">
        <p>Hiển thị <strong>1-{{ filteredInvoices.length }}</strong> của {{ invoices.length }} hóa đơn</p>
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
.headers { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
.title { font-size: 24px; font-weight: 700; color: #0f172a; margin: 0 0 4px 0; }
.subtitle { font-size: 14px; color: #64748b; margin: 0; }

/* Buttons */
.btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s ease; border: 1px solid transparent; }
.btn-primary { background-color: #2563eb; color: #ffffff; box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2), 0 2px 4px -2px rgba(37, 99, 235, 0.1); }
.btn-primary:hover { background-color: #1d4ed8; transform: translateY(-1px); box-shadow: 0 6px 8px -1px rgba(37, 99, 235, 0.25); }
.btn-secondary { background-color: #ffffff; color: #475569; border-color: #e2e8f0; }
.btn-secondary:hover { background-color: #f8fafc; border-color: #cbd5e1; }

/* Stats Cards */
.stats-container { display: grid; gap: 20px; margin-bottom: 28px; }
.stat-card { background-color: #ffffff; padding: 20px; border-radius: 12px; display: flex; items: center; gap: 16px; border: 1px solid #f1f5f9; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05); transition: transform 0.2s ease; }
.stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
.stat-icon-box { width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.stat-icon-box .material-symbols-outlined { font-size: 24px; }
.stat-label { font-size: 13px; font-weight: 600; color: #64748b; margin: 0 0 2px 0; display: block; }
.stat-value { font-size: 22px; font-weight: 700; color: #0f172a; margin: 0; }

/* Content Box */
.content-box { background-color: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05); overflow: hidden; }

/* Toolbar */
.toolbar { padding: 20px 24px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; gap: 16px; flex-wrap: wrap; }
.search-wrapper { position: relative; flex: 1; max-width: 400px; }
.search-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 20px; }
.input-search { width: 100%; padding: 10px 12px 10px 40px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; background-color: #f8fafc; transition: all 0.2s; outline: none; }
.input-search:focus { background-color: #ffffff; border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }

.filters { display: flex; gap: 12px; align-items: center; }
.select-wrapper { position: relative; min-width: 180px; }
.select-filter { width: 100%; appearance: none; padding: 10px 36px 10px 14px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; font-weight: 500; color: #475569; background-color: #ffffff; cursor: pointer; outline: none; }
.select-arrow { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; }

/* Table */
.table-container { min-height: 300px; position: relative; }
.user-table { width: 100%; border-collapse: collapse; }
.user-table th { text-align: left; padding: 14px 24px; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.025em; background-color: #f8fafc; border-bottom: 1px solid #f1f5f9; }
.table-row { transition: background-color 0.2s ease; }
.table-row:hover { background-color: #f8fafc; }
.user-table td { padding: 16px 24px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }

/* Invoices specifics */
.invoice-code { font-family: monospace; font-size: 13px; font-weight: 700; color: #2563eb; }
.course-text { font-size: 13px; color: #475569; font-weight: 500; }
.amount-text { font-size: 14px; font-weight: 700; color: #0f172a; }
.date-text { font-size: 13px; color: #64748b; font-weight: 500; }

.user-info { display: flex; align-items: center; gap: 12px; }
.avatar { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #ffffff; font-weight: 700; font-size: 12px; }
.user-full-name { font-size: 14px; font-weight: 600; color: #1e293b; margin: 0; }

/* Badges */
.badge { display: inline-flex; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
.badge-success { background-color: #ecfdf5; color: #10b981; }
.badge-warning { background-color: #fffbeb; color: #f59e0b; }
.badge-danger { background-color: #fef2f2; color: #ef4444; }
.badge-gray { background-color: #f1f5f9; color: #64748b; }

/* Actions */
.actions { display: flex; justify-content: flex-end; gap: 4px; opacity: 0; transition: opacity 0.2s; }
.table-row:hover .actions { opacity: 1; }
.action-btn { width: 34px; height: 34px; border-radius: 6px; border: 1px solid transparent; background-color: transparent; color: #94a3b8; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; }
.action-btn:hover { background-color: #f1f5f9; color: #2563eb; }
.btn-delete:hover { color: #dc2626; background-color: #fef2f2; }
.action-btn .material-symbols-outlined { font-size: 20px; }
.text-right { text-align: right; }

/* Empty state & Footer */
.empty-state { text-align: center; padding: 60px 0; color: #94a3b8; }
.table-footer { padding: 16px 24px; font-size: 13px; color: #64748b; border-top: 1px solid #f1f5f9; background-color: #f8fafc; }
.flex-footer { display: flex; justify-content: space-between; align-items: center; }
.pagination { display: flex; gap: 8px; }
.page-btn { display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; font-size: 12px; font-weight: 700; color: #475569; background: #ffffff; border: 1px solid #e2e8f0; cursor: pointer; transition: 0.2s; }
.page-btn:hover { background: #f1f5f9; }
.page-btn.active { background: #2563eb; color: #ffffff; border-color: #2563eb; }
</style>

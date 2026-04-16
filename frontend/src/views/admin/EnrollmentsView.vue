<script setup>
import { ref, computed } from 'vue'

// --- State ---
const enrollments = ref([
  { id: 1, appCode: 'APP-2024-081', studentName: 'Nguyễn Trần Bảo Anh', parentName: 'Chị Lan', phone: '0981112222', course: 'Tiếng Anh Mầm non', date: '2024-04-16', tuitionStatus: 'Chưa thanh toán', status: 'Chờ duyệt' },
  { id: 2, appCode: 'APP-2024-082', studentName: 'Lê Duy Thành', parentName: 'Anh Thành', phone: '0903334444', course: 'Toán Tư duy', date: '2024-04-15', tuitionStatus: 'Đã cọc 50%', status: 'Đã xếp lớp' },
  { id: 3, appCode: 'APP-2024-083', studentName: 'Phạm Minh Phương', parentName: 'Cô Sáu', phone: '0915556666', course: 'Luyện thi Vật Lý', date: '2024-04-15', tuitionStatus: 'Đã xong', status: 'Đã xếp lớp' },
  { id: 4, appCode: 'APP-2024-084', studentName: 'Vũ Hải Đăng', parentName: 'Bác Bình', phone: '0937778888', course: 'Ngữ Văn Ngoại khóa', date: '2024-04-10', tuitionStatus: 'Hoàn phí', status: 'Đã hủy' },
])

const isModalOpen = ref(false)
const isEditing = ref(false)
const searchQuery = ref('')
const statusFilter = ref('')
const tuitionFilter = ref('')

const initialEnrollment = {
  id: null,
  appCode: '',
  studentName: '',
  parentName: '',
  phone: '',
  course: 'Tiếng Anh Mầm non',
  date: new Date().toISOString().split('T')[0],
  tuitionStatus: 'Chưa thanh toán',
  status: 'Chờ duyệt'
}

const currentEnrollment = ref({ ...initialEnrollment })

// --- Computed ---
const filteredEnrollments = computed(() => {
  return enrollments.value.filter(e => {
    const matchesSearch = e.studentName.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
                         e.appCode.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                         e.phone.includes(searchQuery.value)
    const matchesStatus = !statusFilter.value || e.status === statusFilter.value
    const matchesTuition = !tuitionFilter.value || e.tuitionStatus === tuitionFilter.value
    return matchesSearch && matchesStatus && matchesTuition
  })
})

const quickStats = computed(() => {
  const total = enrollments.value.length
  const pending = enrollments.value.filter(e => e.status === 'Chờ duyệt').length
  const enrolled = enrollments.value.filter(e => e.status === 'Đã xếp lớp').length
  const refund = enrollments.value.filter(e => e.tuitionStatus === 'Hoàn phí').length
  
  return [
    { label: 'Tổng Hồ Sơ', value: total, icon: 'how_to_reg', color: '#1d4ed8', bg: 'rgba(29,78,216,0.1)' },
    { label: 'Chờ Chấp Thuận', value: pending, icon: 'pending_actions', color: '#f59e0b', bg: 'rgba(245,158,11,0.1)' },
    { label: 'Đã Xếp Lớp', value: enrolled, icon: 'assignment_turned_in', color: '#10b981', bg: 'rgba(16,185,129,0.1)' },
    { label: 'Yêu Cầu Hoàn Phí', value: refund, icon: 'request_quote', color: '#ef4444', bg: 'rgba(239,68,68,0.1)' },
  ]
})

// --- Actions ---
function openAddModal() {
  isEditing.value = false
  currentEnrollment.value = { ...initialEnrollment, appCode: `APP-2024-${String(enrollments.value.length + 85).padStart(3, '0')}` }
  isModalOpen.value = true
}

function openEditModal(enrollment) {
  isEditing.value = true
  currentEnrollment.value = { ...enrollment }
  isModalOpen.value = true
}

function closeModal() {
  isModalOpen.value = false
}

function saveEnrollment() {
  if (isEditing.value) {
    const index = enrollments.value.findIndex(e => e.id === currentEnrollment.value.id)
    if (index !== -1) {
      enrollments.value[index] = { ...currentEnrollment.value }
    }
  } else {
    const newId = enrollments.value.length ? Math.max(...enrollments.value.map(e => e.id)) + 1 : 1
    enrollments.value.push({
      ...currentEnrollment.value,
      id: newId
    })
  }
  closeModal()
}

function deleteEnrollment(id) {
  if (confirm('Bạn có chắc chắn muốn xóa hồ sơ ghi danh này?')) {
    enrollments.value = enrollments.value.filter(e => e.id !== id)
  }
}

function getTuitionClass(tuition) {
  if (tuition === 'Chưa thanh toán') return 'tuition-danger'
  if (tuition === 'Đã cọc 50%') return 'tuition-info'
  if (tuition === 'Đã xong') return 'tuition-success'
  return 'tuition-warning'
}

function getStatusClass(status) {
  if (status === 'Đã xếp lớp') return 'badge-success'
  if (status === 'Chờ duyệt') return 'badge-warning'
  return 'badge-danger'
}
</script>

<template>
  <div class="page-container">
    <header class="page-header">
      <div>
        <h1 class="page-title">Quản lý Ghi danh</h1>
        <p class="page-subtitle">Xử lý hồ sơ nhập học, thu phí ban đầu và điều phối xếp lớp</p>
      </div>
      <button class="btn-primary" @click="openAddModal">
        <span class="material-symbols-outlined">post_add</span>
        Tạo Đơn Ghi danh
      </button>
    </header>

    <!-- Stats -->
    <div class="stats-grid">
      <div v-for="stat in quickStats" :key="stat.label" class="stat-card">
        <div class="stat-icon" :style="{ color: stat.color, backgroundColor: stat.bg }">
          <span class="material-symbols-outlined">{{ stat.icon }}</span>
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
        <div class="filter-group">
          <div class="search-box">
            <span class="material-symbols-outlined search-icon">search</span>
            <input v-model="searchQuery" type="text" class="search-input" placeholder="Tìm mã đơn, tên học viên..." />
          </div>
          
          <div class="select-box">
            <select v-model="statusFilter" class="form-select">
              <option value="">Trạng thái Ghi danh</option>
              <option value="Chờ duyệt">Chờ xét duyệt</option>
              <option value="Đã xếp lớp">Đã xếp lớp</option>
              <option value="Đã hủy">Đã hủy đơn</option>
            </select>
            <span class="material-symbols-outlined select-icon">expand_more</span>
          </div>

          <div class="select-box">
            <select v-model="tuitionFilter" class="form-select">
              <option value="">Lọc học phí</option>
              <option value="Chưa thanh toán">Chưa nộp phí</option>
              <option value="Đã cọc 50%">Đã đặt cọc</option>
              <option value="Đã xong">Đã hoàn tất</option>
            </select>
            <span class="material-symbols-outlined select-icon">expand_more</span>
          </div>
        </div>

        <div class="action-group">
          <button class="btn-secondary">
            <span class="material-symbols-outlined">download</span>
            Xuất báo cáo
          </button>
        </div>
      </div>

      <!-- Table -->
      <div class="table-responsive">
        <table class="data-table">
          <thead>
            <tr>
              <th>MÃ HỒ SƠ</th>
              <th>THÔNG TIN HỌC VIÊN & PHỤ HUYNH</th>
              <th>KHÓA HỌC</th>
              <th>HỌC PHÍ</th>
              <th>TRẠNG THÁI</th>
              <th class="text-right">THAO TÁC</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="app in filteredEnrollments" :key="app.id">
              <td>
                <div class="code-block">
                  <span class="app-code">{{ app.appCode }}</span>
                  <span class="reg-date">{{ app.date }}</span>
                </div>
              </td>
              <td>
                <div class="student-info">
                  <h4 class="student-name">{{ app.studentName }}</h4>
                  <div class="contact-meta">
                    <span class="material-symbols-outlined">person</span> {{ app.parentName }} 
                    <span class="separator">•</span>
                    <span class="material-symbols-outlined">call</span> {{ app.phone }}
                  </div>
                </div>
              </td>
              <td><span class="course-badge">{{ app.course }}</span></td>
              <td>
                <span class="tuition-pill" :class="getTuitionClass(app.tuitionStatus)">
                  {{ app.tuitionStatus }}
                </span>
              </td>
              <td>
                <span class="badge" :class="getStatusClass(app.status)">{{ app.status }}</span>
              </td>
              <td class="text-right">
                <button class="icon-btn" title="Phê duyệt hồ sơ" @click="openEditModal(app)">
                  <span class="material-symbols-outlined">how_to_reg</span>
                </button>
                <button class="icon-btn text-danger" title="Hủy hồ sơ" @click="deleteEnrollment(app.id)">
                  <span class="material-symbols-outlined">delete</span>
                </button>
              </td>
            </tr>
            <tr v-if="filteredEnrollments.length === 0">
              <td colspan="6" class="empty-state">Không tìm thấy hồ sơ ghi danh nào.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="pagination-footer">
        <span class="page-info">Hiển thị {{ filteredEnrollments.length }} hồ sơ</span>
        <div class="pagination">
          <button class="page-btn"><span class="material-symbols-outlined">chevron_left</span></button>
          <button class="page-btn active">1</button>
          <button class="page-btn"><span class="material-symbols-outlined">chevron_right</span></button>
        </div>
      </div>
    </div>

    <!-- Enrollment Modal -->
    <Transition name="fade">
      <div v-if="isModalOpen" class="modal-overlay" @click.self="closeModal">
        <div class="modal-content premium-modal">
          <div class="modal-header">
            <div class="header-info">
              <h2 class="modal-title">{{ isEditing ? 'Cập Nhật Đơn Ghi Danh' : 'Tạo Đơn Ghi Danh Mới' }}</h2>
              <p class="modal-subtitle">Tiếp nhận thông tin đăng ký và chuẩn bị hồ sơ tài chính cho học viên mới.</p>
            </div>
            <button class="close-btn" @click="closeModal">
              <span class="material-symbols-outlined">close</span>
            </button>
          </div>

          <form @submit.prevent="saveEnrollment">
            <div class="modal-body">
              <div class="form-row">
                <div class="form-group flex-2">
                  <label>HỌ VÀ TÊN HỌC VIÊN</label>
                  <div class="input-wrapper">
                    <span class="material-symbols-outlined input-icon">face</span>
                    <input v-model="currentEnrollment.studentName" type="text" placeholder="Nguyễn Văn A" required />
                  </div>
                </div>
                <div class="form-group flex-1">
                  <label>MÃ HỒ SƠ</label>
                  <div class="input-wrapper">
                    <input v-model="currentEnrollment.appCode" type="text" readonly class="readonly-input" />
                  </div>
                </div>
              </div>

              <div class="form-row">
                <div class="form-group flex-1">
                  <label>TÊN PHỤ HUYNH</label>
                  <div class="input-wrapper">
                    <span class="material-symbols-outlined input-icon">person</span>
                    <input v-model="currentEnrollment.parentName" type="text" placeholder="Bố/Mẹ học viên" required />
                  </div>
                </div>
                <div class="form-group flex-1">
                  <label>SỐ ĐIỆN THOẠI</label>
                  <div class="input-wrapper">
                    <span class="material-symbols-outlined input-icon">call</span>
                    <input v-model="currentEnrollment.phone" type="tel" placeholder="09xx..." required />
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label>KHÓA HỌC ĐĂNG KÝ</label>
                <div class="select-wrapper">
                  <span class="material-symbols-outlined input-icon" style="z-index: 1; left: 16px;">history_edu</span>
                  <select v-model="currentEnrollment.course" style="padding-left: 48px;">
                    <option value="Tiếng Anh Mầm non">Tiếng Anh Mầm non</option>
                    <option value="Toán Tư duy">Toán Tư duy</option>
                    <option value="Luyện thi Vật Lý">Luyện thi Vật Lý</option>
                    <option value="Ngữ Văn Ngoại khóa">Ngữ Văn Ngoại khóa</option>
                  </select>
                  <span class="material-symbols-outlined expand-icon">expand_more</span>
                </div>
              </div>

              <div class="form-row">
                <div class="form-group flex-1">
                  <label>TRẠNG THÁI HỌC PHÍ</label>
                  <div class="select-wrapper">
                    <select v-model="currentEnrollment.tuitionStatus">
                      <option value="Chưa thanh toán">Chưa thanh toán</option>
                      <option value="Đã cọc 50%">Đã đặt cọc 50%</option>
                      <option value="Đã xong">Đã hoàn tất học phí</option>
                      <option value="Hoàn phí">Cần hoàn phí</option>
                    </select>
                    <span class="material-symbols-outlined expand-icon">expand_more</span>
                  </div>
                </div>
                <div class="form-group flex-1">
                  <label>TIẾN ĐỘ HỒ SƠ</label>
                  <div class="select-wrapper">
                    <select v-model="currentEnrollment.status">
                      <option value="Chờ duyệt">Đang chờ xét duyệt</option>
                      <option value="Đã xếp lớp">Đã phê duyệt & Xếp lớp</option>
                      <option value="Đã hủy">Hủy đơn đăng ký</option>
                    </select>
                    <span class="material-symbols-outlined expand-icon">expand_more</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn-cancel" @click="closeModal">Hủy bỏ</button>
              <button type="submit" class="btn-submit">{{ isEditing ? 'Cập nhật' : 'Tạo hồ sơ' }}</button>
            </div>
          </form>
        </div>
      </div>
    </Transition>
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

.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
.page-title { font-family: var(--font-outfit); font-size: 28px; font-weight: 800; color: var(--on-surface); margin: 0 0 4px 0; }
.page-subtitle { font-size: 14px; color: var(--on-surface-variant); font-weight: 500; }

.btn-primary {
  display: flex; align-items: center; gap: 8px; background-color: var(--primary); color: white; border: none; padding: 10px 20px; border-radius: 12px; font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(0, 74, 198, 0.2);
}
.btn-primary:hover { background-color: var(--primary-hover); transform: translateY(-1px); }

.btn-secondary {
  display: flex; align-items: center; gap: 8px; background-color: var(--surface-container-highest); color: var(--on-surface); border: 1px solid var(--outline-variant); padding: 10px 16px; border-radius: 12px; font-weight: 600; font-size: 13px; cursor: pointer;
}

/* Stats */
.stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 24px; }
.stat-card { background-color: white; padding: 20px; border-radius: 20px; display: flex; align-items: center; gap: 16px; box-shadow: var(--shadow-sm); border: 1px solid var(--surface-container); }
.stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; }
.stat-info { display: flex; flex-direction: column; }
.stat-label { font-size: 12px; font-weight: 700; color: var(--on-surface-variant); text-transform: uppercase; margin-bottom: 2px; }
.stat-value { font-family: var(--font-outfit); font-size: 22px; font-weight: 800; color: var(--on-surface); }

/* Main Card */
.content-card { background-color: white; border-radius: 24px; box-shadow: var(--shadow-md); border: 1px solid var(--surface-container); overflow: hidden; }
.table-actions { display: flex; justify-content: space-between; padding: 20px 24px; border-bottom: 1px solid var(--surface-container); }
.filter-group { display: flex; gap: 16px; }
.search-box { position: relative; width: 320px; }
.search-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--outline); font-size: 20px; }
.search-input { width: 100%; border: 1.5px solid var(--outline-variant); padding: 10px 16px 10px 44px; border-radius: 14px; font-size: 14px; font-weight: 500; color: var(--on-surface); outline: none; background-color: var(--surface-container-lowest); }

.select-box { position: relative; }
.form-select { appearance: none; border: 1.5px solid var(--outline-variant); padding: 10px 40px 10px 16px; border-radius: 14px; font-size: 14px; font-weight: 600; color: var(--on-surface-variant); background-color: white; cursor: pointer; }
.select-icon { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: var(--outline); pointer-events: none; }

/* Table */
.table-responsive { width: 100%; overflow-x: auto; }
.data-table { width: 100%; border-collapse: collapse; }
.data-table th { text-align: left; padding: 16px 24px; font-size: 11px; font-weight: 800; color: var(--outline); text-transform: uppercase; letter-spacing: 0.1em; background-color: var(--surface-container-low); border-bottom: 1px solid var(--surface-container); }
.data-table td { padding: 16px 24px; border-bottom: 1px solid var(--surface-container); vertical-align: middle; }
.data-table tr:hover td { background-color: var(--surface-container-lowest); }

.code-block { display: flex; flex-direction: column; gap: 4px; }
.app-code { font-family: monospace; font-size: 13px; font-weight: 800; color: var(--primary); background: #eff6ff; padding: 4px 8px; border-radius: 6px; width: fit-content; }
.reg-date { font-size: 11px; font-weight: 700; color: var(--outline); }

.student-info { display: flex; flex-direction: column; gap: 2px; }
.student-name { margin: 0; font-size: 15px; font-weight: 700; color: var(--on-surface); }
.contact-meta { font-size: 12px; color: var(--outline); font-weight: 600; display: flex; align-items: center; gap: 4px; }
.contact-meta span { font-size: 14px; }
.separator { color: var(--outline-variant); margin: 0 4px; }

.course-badge { font-size: 12px; font-weight: 700; color: var(--on-surface); background: var(--surface-container-low); padding: 6px 12px; border-radius: 8px; border: 1px solid var(--outline-variant); }

.tuition-pill { font-size: 11px; font-weight: 800; padding: 6px 12px; border-radius: 8px; text-transform: uppercase; }
.tuition-danger { background: #fee2e2; color: #b91c1c; }
.tuition-info { background: #dbeafe; color: #1d4ed8; }
.tuition-success { background: #dcfce7; color: #15803d; }
.tuition-warning { background: #fef3c7; color: #b45309; }

.badge { padding: 6px 14px; border-radius: 999px; font-size: 12px; font-weight: 700; }
.badge-success { background-color: #dcfce7; color: #15803d; }
.badge-warning { background-color: #fef9c3; color: #a16207; }

.icon-btn { background: transparent; border: none; cursor: pointer; color: var(--outline); padding: 10px; border-radius: 10px; transition: all 0.2s; display: inline-flex; }
.icon-btn:hover { background-color: var(--surface-container); color: var(--primary); }

.empty-state { padding: 40px; text-align: center; color: var(--outline); font-weight: 600; }

/* Modals */
.modal-overlay { position: fixed; inset: 0; background-color: rgba(0, 0, 0, 0.5); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; z-index: 1000; }
.premium-modal { background: white; width: 100%; max-width: 580px; border-radius: 32px; box-shadow: var(--shadow-xl); overflow: hidden; animation: modalIn 0.4s cubic-bezier(0.16, 1, 0.3, 1); }

@keyframes modalIn { from { transform: scale(0.95) translateY(20px); opacity: 0; } to { transform: scale(1) translateY(0); opacity: 1; } }

.modal-header { padding: 32px 40px 16px 40px; display: flex; justify-content: space-between; align-items: flex-start; }
.modal-title { font-family: var(--font-outfit); font-size: 24px; font-weight: 800; color: var(--on-surface); margin: 0 0 8px 0; }
.modal-subtitle { font-size: 13px; color: var(--on-surface-variant); font-weight: 500; line-height: 1.6; }
.close-btn { background: var(--surface-container); border: none; color: var(--outline); cursor: pointer; padding: 8px; border-radius: 12px; }

.modal-body { padding: 24px 40px 40px 40px; }
.form-group { margin-bottom: 24px; }
.form-group label { display: block; font-size: 11px; font-weight: 900; color: var(--outline); margin-bottom: 10px; letter-spacing: 0.1em; }
.form-row { display: flex; gap: 20px; }
.flex-1 { flex: 1; }
.flex-2 { flex: 2; }

.input-wrapper { position: relative; display: flex; align-items: center; }
.input-icon { position: absolute; left: 16px; color: var(--outline); font-size: 20px; }
.input-wrapper input { width: 100%; background-color: var(--surface-container-lowest); border: 1.5px solid var(--outline-variant); padding: 14px 16px 14px 48px; border-radius: 16px; font-size: 15px; font-weight: 600; color: var(--on-surface); outline: none; }
.readonly-input { background-color: var(--surface-container-low) !important; color: var(--outline) !important; cursor: not-allowed; }

.select-wrapper { position: relative; display: flex; align-items: center; }
.select-wrapper select { width: 100%; appearance: none; background-color: var(--surface-container-lowest); border: 1.5px solid var(--outline-variant); padding: 14px 40px 14px 16px; border-radius: 16px; font-size: 15px; font-weight: 600; color: var(--on-surface); outline: none; cursor: pointer; }
.expand-icon { position: absolute; right: 16px; color: var(--outline); pointer-events: none; }

.modal-footer { padding: 0 40px 40px 40px; display: flex; justify-content: flex-end; gap: 16px; }
.btn-cancel { background: transparent; color: var(--outline); font-weight: 800; border: none; cursor: pointer; font-size: 15px; }
.btn-submit { background-color: var(--primary); color: white; border: none; padding: 14px 32px; border-radius: 16px; font-weight: 800; font-size: 15px; cursor: pointer; box-shadow: 0 8px 16px rgba(0, 74, 198, 0.25); }

/* Transitions */
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

.pagination-footer { display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; }
.page-btn { min-width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border: 1.5px solid var(--outline-variant); background-color: white; border-radius: 10px; font-weight: 700; cursor: pointer; }
.page-btn.active { background-color: var(--primary); color: white; border-color: var(--primary); }
</style>

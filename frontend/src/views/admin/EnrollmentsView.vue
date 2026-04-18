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
    { label: 'Tổng Hồ Sơ', value: total, icon: 'how_to_reg', color: '#2563eb', bg: '#eff6ff' },
    { label: 'Chờ Chấp Thuận', value: pending, icon: 'pending_actions', color: '#f59e0b', bg: '#fffbeb' },
    { label: 'Đã Xếp Lớp', value: enrolled, icon: 'assignment_turned_in', color: '#10b981', bg: '#ecfdf5' },
    { label: 'Yêu Cầu Hoàn Phí', value: refund, icon: 'request_quote', color: '#ef4444', bg: '#fef2f2' },
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
  <div class="admin-page">
    <div class="headers">
      <div class="header-left">
        <h1 class="title">Quản lý Ghi danh</h1>
        <p class="subtitle">Xử lý hồ sơ nhập học, thu phí ban đầu và điều phối xếp lớp</p>
      </div>
      <button class="btn btn-primary" @click="openAddModal">
        <span class="material-symbols-outlined">post_add</span>
        Tạo Đơn Ghi danh
      </button>
    </div>

    <!-- Quick Stats -->
    <div class="stats-container">
      <div v-for="stat in quickStats" :key="stat.label" class="stat-card">
        <div class="stat-icon-box" :style="{ color: stat.color, backgroundColor: stat.bg }">
          <span class="material-symbols-outlined">{{ stat.icon }}</span>
        </div>
        <div class="stat-content">
          <p class="stat-label">{{ stat.label }}</p>
          <h3 class="stat-value">{{ stat.value }}</h3>
        </div>
      </div>
    </div>

    <!-- Main Card -->
    <div class="content-box">
      <div class="toolbar">
        <div class="search-wrapper">
          <span class="material-symbols-outlined search-icon">search</span>
          <input v-model="searchQuery" type="text" class="input-search" placeholder="Tìm mã đơn, tên học viên..." />
        </div>
        
        <div class="filters">
          <div class="select-wrapper">
            <select v-model="statusFilter" class="select-filter">
              <option value="">Trạng thái Ghi danh</option>
              <option value="Chờ duyệt">Chờ xét duyệt</option>
              <option value="Đã xếp lớp">Đã xếp lớp</option>
              <option value="Đã hủy">Đã hủy đơn</option>
            </select>
            <span class="material-symbols-outlined select-arrow">expand_more</span>
          </div>

          <div class="select-wrapper">
            <select v-model="tuitionFilter" class="select-filter">
              <option value="">Lọc học phí</option>
              <option value="Chưa thanh toán">Chưa nộp phí</option>
              <option value="Đã cọc 50%">Đã đặt cọc</option>
              <option value="Đã xong">Đã hoàn tất</option>
            </select>
            <span class="material-symbols-outlined select-arrow">expand_more</span>
          </div>
          
          <button class="btn btn-secondary">
            <span class="material-symbols-outlined">download</span>
            Xuất báo cáo
          </button>
        </div>
      </div>

      <!-- Table -->
      <div class="table-container">
        <table class="user-table">
          <thead>
            <tr>
              <th style="width: 15%">Mã hồ sơ</th>
              <th style="width: 30%">Thông tin học viên & Phụ huynh</th>
              <th style="width: 15%">Khóa học</th>
              <th style="width: 15%">Học phí</th>
              <th style="width: 15%">Trạng thái</th>
              <th style="width: 10%" class="text-right">Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="app in filteredEnrollments" :key="app.id" class="table-row">
              <td>
                <div class="code-block">
                  <span class="app-code">{{ app.appCode }}</span>
                  <span class="reg-date">{{ app.date }}</span>
                </div>
              </td>
              <td>
                <div class="student-info">
                  <p class="student-name">{{ app.studentName }}</p>
                  <p class="contact-meta">
                    <span class="material-symbols-outlined">person</span> {{ app.parentName }} 
                    <span class="separator">•</span>
                    <span class="material-symbols-outlined">call</span> {{ app.phone }}
                  </p>
                </div>
              </td>
              <td>
                <span class="course-badge">{{ app.course }}</span>
              </td>
              <td>
                <span class="tuition-pill" :class="getTuitionClass(app.tuitionStatus)">
                  {{ app.tuitionStatus }}
                </span>
              </td>
              <td>
                <span class="badge" :class="getStatusClass(app.status)">{{ app.status }}</span>
              </td>
              <td class="text-right">
                <div class="actions">
                  <button class="action-btn" title="Phê duyệt hồ sơ" @click="openEditModal(app)">
                    <span class="material-symbols-outlined">how_to_reg</span>
                  </button>
                  <button class="action-btn btn-delete" title="Hủy hồ sơ" @click="deleteEnrollment(app.id)">
                    <span class="material-symbols-outlined">delete</span>
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="filteredEnrollments.length === 0">
              <td colspan="6" class="empty-state">
                Không tìm thấy hồ sơ ghi danh nào.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Footer -->
      <div class="table-footer">
        <p>Hiển thị <strong>{{ filteredEnrollments.length }}</strong> hồ sơ</p>
      </div>
    </div>

    <!-- Enrollment Modal -->
    <Transition name="modal-fade">
      <div v-if="isModalOpen" class="modal-backdrop" @click.self="closeModal">
        <div class="modal-box">
          <div class="modal-header">
            <div class="modal-header-text">
              <h2 class="modal-title">{{ isEditing ? 'Cập Nhật Đơn Ghi Danh' : 'Tạo Đơn Ghi Danh Mới' }}</h2>
              <p class="modal-subtitle">Tiếp nhận thông tin đăng ký và chuẩn bị hồ sơ tài chính cho học viên mới.</p>
            </div>
            <button class="btn-close-minimal" @click="closeModal" title="Đóng">
              <span class="material-symbols-outlined">close</span>
            </button>
          </div>

          <form @submit.prevent="saveEnrollment">
            <div class="modal-body">
              <div class="form-grid">
                <div class="form-item span-2">
                  <label class="form-label">Họ và tên học viên</label>
                  <input v-model="currentEnrollment.studentName" type="text" placeholder="Ví dụ: Nguyễn Văn A" required class="form-input" />
                </div>

                <div class="form-item">
                  <label class="form-label">Mã hồ sơ (Tự động)</label>
                  <input v-model="currentEnrollment.appCode" type="text" readonly class="form-input" style="background-color: #f1f5f9; color: #94a3b8; cursor: not-allowed;" />
                </div>
                
                <div class="form-item">
                  <label class="form-label">Tên phụ huynh</label>
                  <input v-model="currentEnrollment.parentName" type="text" placeholder="Bố/Mẹ học viên" required class="form-input" />
                </div>

                <div class="form-item">
                  <label class="form-label">Số điện thoại</label>
                  <input v-model="currentEnrollment.phone" type="tel" placeholder="09xx..." required class="form-input" />
                </div>
                
                <div class="form-item">
                  <label class="form-label">Khóa học đăng ký</label>
                  <div class="select-container">
                    <select v-model="currentEnrollment.course" class="form-select">
                      <option value="Tiếng Anh Mầm non">Tiếng Anh Mầm non</option>
                      <option value="Toán Tư duy">Toán Tư duy</option>
                      <option value="Luyện thi Vật Lý">Luyện thi Vật Lý</option>
                      <option value="Ngữ Văn Ngoại khóa">Ngữ Văn Ngoại khóa</option>
                    </select>
                    <span class="material-symbols-outlined select-icon-minimal">expand_more</span>
                  </div>
                </div>

                <div class="form-item">
                  <label class="form-label">Trạng thái học phí</label>
                  <div class="select-container">
                    <select v-model="currentEnrollment.tuitionStatus" class="form-select">
                      <option value="Chưa thanh toán">Chưa thanh toán</option>
                      <option value="Đã cọc 50%">Đã đặt cọc 50%</option>
                      <option value="Đã xong">Đã hoàn tất học phí</option>
                      <option value="Hoàn phí">Cần hoàn phí</option>
                    </select>
                    <span class="material-symbols-outlined select-icon-minimal">expand_more</span>
                  </div>
                </div>

                <div class="form-item">
                  <label class="form-label">Tiến độ hồ sơ</label>
                  <div class="select-container">
                    <select v-model="currentEnrollment.status" class="form-select">
                      <option value="Chờ duyệt">Đang chờ xét duyệt</option>
                      <option value="Đã xếp lớp">Đã phê duyệt & Xếp lớp</option>
                      <option value="Đã hủy">Hủy đơn đăng ký</option>
                    </select>
                    <span class="material-symbols-outlined select-icon-minimal">expand_more</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="modal-footer-minimal">
              <button type="button" class="btn btn-simple" @click="closeModal">Hủy bỏ</button>
              <button type="submit" class="btn btn-solid-primary">{{ isEditing ? 'Cập nhật hồ sơ' : 'Tạo hồ sơ' }}</button>
            </div>
          </form>
        </div>
      </div>
    </Transition>
  </div>
</template>

<style scoped>
/* Base Styles */
.admin-page { background-color: #f8fafc; min-height: 100vh; padding: 0 4px; font-family: 'Inter', system-ui, -apple-system, sans-serif; color: #1e293b; }
.headers { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
.title { font-size: 24px; font-weight: 700; color: #0f172a; margin: 0 0 4px 0; }
.subtitle { font-size: 14px; color: #64748b; margin: 0; }

.header-actions { display: flex; gap: 12px; }

/* Buttons */
.btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s ease; border: 1px solid transparent; }
.btn-primary { background-color: #2563eb; color: #ffffff; box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2), 0 2px 4px -2px rgba(37, 99, 235, 0.1); }
.btn-primary:hover { background-color: #1d4ed8; transform: translateY(-1px); box-shadow: 0 6px 8px -1px rgba(37, 99, 235, 0.25); }
.btn-secondary { background-color: #ffffff; color: #475569; border-color: #e2e8f0; }
.btn-secondary:hover { background-color: #f8fafc; border-color: #cbd5e1; }
.btn-refresh { width: 40px; height: 40px; padding: 0; justify-content: center; background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; color: #64748b; display: flex; align-items: center; cursor: pointer; transition: 0.2s;}
.btn-refresh:hover { background-color: #f8fafc; color: #2563eb; border-color: #cbd5e1; }

/* Stats Cards */
.stats-container { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 28px; }
.stat-card { background-color: #ffffff; padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 16px; border: 1px solid #f1f5f9; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px -1px rgba(0, 0, 0, 0.05); transition: transform 0.2s ease; }
.stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
.stat-icon-box { width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.stat-icon-box .material-symbols-outlined { font-size: 24px; }
.stat-label { font-size: 13px; font-weight: 600; color: #64748b; margin: 0 0 2px 0; }
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
.user-table th { text-align: left; padding: 14px 24px; font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.025em; background-color: #f8fafc; border-bottom: 1px solid #f1f5f9; }
.table-row { transition: background-color 0.2s ease; }
.table-row:hover { background-color: #f8fafc; }
.user-table td { padding: 16px 24px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }

/* Customs for Enrollments */
.code-block { display: flex; flex-direction: column; gap: 4px; }
.app-code { font-family: monospace; font-size: 13px; font-weight: 700; color: #2563eb; background: #eff6ff; padding: 4px 8px; border-radius: 6px; width: fit-content; border: 1px solid #dbeafe; }
.reg-date { font-size: 12px; font-weight: 600; color: #64748b; }

.student-info { display: flex; flex-direction: column; gap: 4px; }
.student-name { margin: 0; font-size: 14px; font-weight: 600; color: #1e293b; }
.contact-meta { font-size: 12px; color: #64748b; margin: 0; display: flex; align-items: center; gap: 4px; }
.separator { color: #cbd5e1; }
.contact-meta .material-symbols-outlined { font-size: 14px; }

.course-badge { font-size: 13px; font-weight: 600; color: #475569; background: #f8fafc; padding: 6px 12px; border-radius: 8px; border: 1px solid #e2e8f0; }

.tuition-pill { font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 6px; text-transform: uppercase; }
.tuition-danger { background: #fef2f2; color: #ef4444; }
.tuition-info { background: #eff6ff; color: #2563eb; }
.tuition-success { background: #ecfdf5; color: #10b981; }
.tuition-warning { background: #fffaf5; color: #ea580c; }

/* Badges */
.badge { display: inline-flex; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; }
.badge-success { background-color: #ecfdf5; color: #10b981; }
.badge-warning { background-color: #fffbeb; color: #f59e0b; }
.badge-danger { background-color: #fef2f2; color: #ef4444; }

/* Actions */
.actions { display: flex; justify-content: flex-end; gap: 4px; }
.action-btn { width: 34px; height: 34px; border-radius: 6px; border: 1px solid transparent; background-color: transparent; color: #64748b; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; }
.action-btn:hover { background-color: #f1f5f9; color: #2563eb; }
.btn-delete:hover { color: #dc2626; background-color: #fef2f2; }
.action-btn .material-symbols-outlined { font-size: 20px; }
.text-right { text-align: right; }

/* Empty state & Footer */
.empty-state { text-align: center; padding: 60px 0; color: #94a3b8; }
.table-footer { padding: 16px 24px; font-size: 13px; color: #64748b; }

/* Modal Styles */
.modal-backdrop { position: fixed; inset: 0; background-color: rgba(15, 23, 42, 0.4); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 20px; }
.modal-box { background-color: #ffffff; width: 100%; max-width: 580px; border-radius: 16px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); border: 1px solid #f1f5f9; animation: modalIn 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
@keyframes modalIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
.modal-header { padding: 24px 28px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: flex-start; }
.modal-title { font-size: 18px; font-weight: 700; color: #0f172a; margin: 0 0 4px 0; }
.modal-subtitle { font-size: 13px; color: #64748b; margin: 0; }
.btn-close-minimal { background: transparent; border: none; color: #94a3b8; cursor: pointer; padding: 4px; border-radius: 6px; transition: all 0.2s; }
.btn-close-minimal:hover { background-color: #f1f5f9; color: #475569; }

/* Modal Body */
.modal-body { padding: 28px; }
.form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
.form-item.span-2 { grid-column: span 2; }
.form-label { display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 8px; }
.form-input, .form-select { width: 100%; padding: 10px 14px; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; color: #0f172a; outline: none; transition: all 0.2s; }
.form-input:focus, .form-select:focus { background-color: #ffffff; border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }

.select-container { position: relative; }
.select-icon-minimal { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; font-size: 20px; }
.form-select { appearance: none; padding-right: 40px; }

/* Modal Footer */
.modal-footer-minimal { padding: 20px 28px; background-color: #f8fafc; border-top: 1px solid #f1f5f9; border-radius: 0 0 16px 16px; display: flex; justify-content: flex-end; gap: 12px; }
.btn-simple { background-color: transparent; border: 1px solid #e2e8f0; color: #475569; font-weight: 600; padding: 10px 18px; border-radius: 8px; cursor: pointer;}
.btn-simple:hover { background-color: #f1f5f9; border-color: #cbd5e1; }
.btn-solid-primary { background-color: #2563eb; color: #ffffff; border: none; font-weight: 600; padding: 10px 18px; border-radius: 8px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); cursor: pointer;}
.btn-solid-primary:hover { background-color: #1d4ed8; }

.modal-fade-enter-active, .modal-fade-leave-active { transition: opacity 0.2s ease; }
.modal-fade-enter-from, .modal-fade-leave-to { opacity: 0; }
</style>

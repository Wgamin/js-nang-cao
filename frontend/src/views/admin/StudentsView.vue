<script setup>
import { ref, computed } from 'vue'
import * as XLSX from 'xlsx'

// --- State ---
const students = ref([
  { id: 1, name: 'Nguyễn Văn Hoàng', code: 'STD-24001', email: 'hoang.nv@student.vn', phone: '0901234567', parent: 'Nguyễn Văn Hùng', status: 'Đang học', class: 'IELTS Foundation' },
  { id: 2, name: 'Phạm Minh Tú', code: 'STD-24042', email: 'tu.pm@student.vn', phone: '0988776554', parent: 'Lê Thị Mai', status: 'Bảo lưu', class: 'Toeic 650+' },
  { id: 3, name: 'Trần Linh Anh', code: 'STD-23988', email: 'anh.tl@student.vn', phone: '0912334112', parent: 'Trần Quốc Anh', status: 'Nghỉ học', class: 'Giao tiếp Cơ bản' },
  { id: 4, name: 'Lê Duy Kiên', code: 'STD-24115', email: 'kien.ld@student.vn', phone: '0977112233', parent: 'Lê Duy Mạnh', status: 'Đang học', class: 'IELTS Intensive' },
])

const isModalOpen = ref(false)
const isEditing = ref(false)
const searchQuery = ref('')
const statusFilter = ref('')
const fileInput = ref(null)

const initialStudent = {
  id: null,
  name: '',
  email: '',
  phone: '',
  code: '',
  class: '',
  parent: '',
  status: 'Đang học'
}

const currentStudent = ref({ ...initialStudent })

// --- Computed ---
const filteredStudents = computed(() => {
  return students.value.filter(s => {
    const studentName = s.name || ''
    const studentCode = s.code || ''
    const studentPhone = s.phone || ''
    const matchesSearch = studentName.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
                         studentCode.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                         studentPhone.includes(searchQuery.value)
    const matchesStatus = !statusFilter.value || s.status === statusFilter.value
    return matchesSearch && matchesStatus
  })
})

const quickStats = computed(() => [
  { label: 'Tổng Học Viên', value: students.value.length, icon: 'groups', color: '#2563eb', bg: '#eff6ff' },
  { label: 'Đang theo học', value: students.value.filter(s => s.status === 'Đang học').length, icon: 'school', color: '#10b981', bg: '#ecfdf5' },
  { label: 'Bảo Lưu', value: students.value.filter(s => s.status === 'Bảo lưu').length, icon: 'pause_circle', color: '#f59e0b', bg: '#fffbeb' },
  { label: 'Đã nghỉ học', value: students.value.filter(s => s.status === 'Nghỉ học').length, icon: 'person_off', color: '#ef4444', bg: '#fef2f2' },
])

// --- Helper Functions ---
const getInitials = (name) => {
  if (!name) return '??'
  const parts = name.split(' ')
  return parts.length > 1 ? (parts[0][0] + parts[parts.length - 1][0]).toUpperCase() : parts[0][0].toUpperCase()
}

const getAvatarColor = (id) => {
  const colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444']
  return colors[id % 4]
}

const getStatusBadgeClass = (status) => {
  const map = {
    'Đang học': 'badge-success',
    'Bảo lưu': 'badge-warning',
    'Nghỉ học': 'badge-danger'
  }
  return map[status] || 'badge-gray'
}

// --- Actions ---
function openAddModal() {
  isEditing.value = false
  currentStudent.value = { ...initialStudent, code: `STD-24${String(students.value.length + 1).padStart(3, '0')}` }
  isModalOpen.value = true
}

function openEditModal(student) {
  isEditing.value = true
  currentStudent.value = { ...student }
  isModalOpen.value = true
}

function closeModal() {
  isModalOpen.value = false
}

function saveStudent() {
  if (isEditing.value) {
    const index = students.value.findIndex(s => s.id === currentStudent.value.id)
    if (index !== -1) {
      students.value[index] = { ...currentStudent.value }
    }
  } else {
    const newId = students.value.length ? Math.max(...students.value.map(s => s.id)) + 1 : 1
    students.value.push({ 
      ...currentStudent.value, 
      id: newId
    })
  }
  closeModal()
}

function deleteStudent(id) {
  if (confirm('Bạn có chắc chắn muốn xóa học viên này?')) {
    students.value = students.value.filter(s => s.id !== id)
  }
}

// --- Excel Logic ---
function triggerExcelImport() {
  fileInput.value.click()
}

function handleExcelImport(e) {
  const file = e.target.files[0]
  if (!file) return
  const reader = new FileReader()
  reader.onload = (event) => {
    const data = new Uint8Array(event.target.result)
    const workbook = XLSX.read(data, { type: 'array' })
    const sheet = workbook.Sheets[workbook.SheetNames[0]]
    const jsonData = XLSX.utils.sheet_to_json(sheet)
    
    jsonData.forEach(row => {
      const newId = students.value.length ? Math.max(...students.value.map(s => s.id)) + 1 : 1
      students.value.push({
        id: newId,
        name: row['Họ tên'] || 'N/A',
        code: row['Mã HV'] || `STD-24${String(students.value.length + 1).padStart(3, '0')}`,
        email: row['Email'] || '',
        phone: row['SĐT'] || '',
        parent: row['Phụ huynh'] || '',
        status: row['Trạng thái'] || 'Đang học',
        class: row['Lớp'] || 'Chưa xếp lớp'
      })
    })
    alert(`Đã nhập thành công ${jsonData.length} học viên.`)
    e.target.value = ''
  }
  reader.readAsArrayBuffer(file)
}

function downloadTemplate() {
  const data = [{ 'Họ tên': 'Nguyễn Văn A', 'Mã HV': 'STD-24005', 'Email': 'a@example.com', 'SĐT': '0123456789', 'Phụ huynh': 'Nguyễn Văn B', 'Trạng thái': 'Đang học', 'Lớp': 'IELTS Foundation' }]
  const ws = XLSX.utils.json_to_sheet(data)
  const wb = XLSX.utils.book_new()
  XLSX.utils.book_append_sheet(wb, ws, 'Template')
  XLSX.writeFile(wb, 'Mau_nhap_hoc_vien.xlsx')
}
</script>

<template>
  <div class="admin-page">
    <!-- Header Section -->
    <div class="headers">
      <div class="header-left">
        <h1 class="title">Quản lý học viên</h1>
        <p class="subtitle">Hồ sơ cá nhân, liên hệ phụ huynh và lộ trình học tập của học viên.</p>
      </div>
      <div class="header-actions">
        <div class="utility-group">
          <button class="btn btn-secondary" @click="downloadTemplate">
            <span class="material-symbols-outlined">download</span>
            Mẫu Excel
          </button>
          <button class="btn btn-secondary" @click="triggerExcelImport">
            <span class="material-symbols-outlined">upload_file</span>
            Nhập Excel
          </button>
          <input type="file" ref="fileInput" hidden @change="handleExcelImport" accept=".xlsx, .xls" />
        </div>
        <button class="btn btn-primary" @click="openAddModal">
          <span class="material-symbols-outlined">person_add</span>
          Thêm học viên
        </button>
      </div>
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

    <!-- Content Area -->
    <div class="content-box">
      <!-- Toolbar -->
      <div class="toolbar">
        <div class="search-wrapper">
          <span class="material-symbols-outlined search-icon">search</span>
          <input 
            v-model="searchQuery" 
            type="text" 
            placeholder="Tìm theo tên học viên, mã số, số điện thoại..."
            class="input-search"
          />
        </div>
        
        <div class="filters">
          <div class="select-wrapper">
            <select v-model="statusFilter" class="select-filter">
              <option value="">Tất cả trạng thái</option>
              <option value="Đang học">Đang học</option>
              <option value="Bảo lưu">Bảo lưu</option>
              <option value="Nghỉ học">Nghỉ học</option>
            </select>
            <span class="material-symbols-outlined select-arrow">expand_more</span>
          </div>
          
          <button class="btn-refresh" title="Làm mới">
            <span class="material-symbols-outlined">refresh</span>
          </button>
        </div>
      </div>

      <!-- Table Section -->
      <div class="table-container">
        <table class="user-table">
          <thead>
            <tr>
              <th style="width: 30%">Học viên & Lớp học</th>
              <th style="width: 20%">Mã số & Liên hệ</th>
              <th style="width: 20%">Phụ huynh</th>
              <th style="width: 15%">Trạng thái</th>
              <th style="width: 15%" class="text-right">Hành động</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="student in filteredStudents" :key="student.id" class="table-row">
              <td>
                <div class="user-info">
                  <div class="avatar" :style="{ backgroundColor: getAvatarColor(student.id) }">
                    {{ getInitials(student.name) }}
                  </div>
                  <div class="user-text">
                    <p class="user-full-name">{{ student.name }}</p>
                    <div class="user-email-text">
                      Lớp: <strong style="color: #2563eb;">{{ student.class || 'Chưa xếp lớp' }}</strong>
                    </div>
                  </div>
                </div>
              </td>
              <td>
                <div class="id-contact">
                  <span class="code-badge">{{ student.code }}</span>
                  <div class="phone-link">
                    <span class="material-symbols-outlined">call</span>
                    {{ student.phone }}
                  </div>
                </div>
              </td>
              <td>
                <div class="parent-cell">
                  <p class="account-name">{{ student.parent }}</p>
                  <p class="user-email-text">Người đại diện</p>
                </div>
              </td>
              <td>
                <span class="badge" :class="getStatusBadgeClass(student.status)">
                  {{ student.status }}
                </span>
              </td>
              <td class="text-right">
                <div class="actions">
                  <button class="action-btn" @click="openEditModal(student)" title="Chỉnh sửa hồ sơ">
                    <span class="material-symbols-outlined">edit</span>
                  </button>
                  <button class="action-btn" title="Gửi thông báo">
                    <span class="material-symbols-outlined">notifications</span>
                  </button>
                  <button class="action-btn btn-delete" @click="deleteStudent(student.id)" title="Xóa">
                    <span class="material-symbols-outlined">delete</span>
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="filteredStudents.length === 0">
              <td colspan="5" class="empty-state">
                <p>Không tìm thấy học viên nào phù hợp.</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination Footer -->
      <div class="table-footer">
        <p>Hiển thị <strong>{{ filteredStudents.length }}</strong> học viên</p>
      </div>
    </div>

    <!-- Standard Modal -->
    <Transition name="modal-fade">
      <div v-if="isModalOpen" class="modal-backdrop" @click.self="closeModal">
        <div class="modal-box">
          <div class="modal-header">
            <div class="modal-header-text">
              <h2 class="modal-title">{{ isEditing ? 'Cập nhật hồ sơ học viên' : 'Tiếp nhận học viên mới' }}</h2>
              <p class="modal-subtitle">Ghi nhận đầy đủ thông tin cá nhân và xếp lớp ban đầu.</p>
            </div>
            <button class="btn-close-minimal" @click="closeModal" title="Đóng">
              <span class="material-symbols-outlined">close</span>
            </button>
          </div>
          
          <form @submit.prevent="saveStudent">
            <div class="modal-body">
              <div class="form-grid">
                <!-- Row 1: Name & Phone -->
                <div class="form-item span-2">
                  <label class="form-label">Họ và tên học viên</label>
                  <input v-model="currentStudent.name" type="text" placeholder="Ví dụ: Nguyễn Văn A" required class="form-input" />
                </div>
                
                <div class="form-item">
                  <label class="form-label">Số điện thoại liên hệ</label>
                  <input v-model="currentStudent.phone" type="text" placeholder="0xxx xxx xxx" required class="form-input" />
                </div>
                
                <div class="form-item">
                  <label class="form-label">Địa chỉ Email</label>
                  <input v-model="currentStudent.email" type="email" placeholder="student@example.com" class="form-input" />
                </div>

                <!-- Row 2: Parent & Class -->
                <div class="form-item">
                  <label class="form-label">Người đại diện (Phụ huynh)</label>
                  <input v-model="currentStudent.parent" type="text" placeholder="Họ tên cha/mẹ" required class="form-input" />
                </div>

                <div class="form-item">
                  <label class="form-label">Lớp học hiện tại</label>
                  <input v-model="currentStudent.class" type="text" placeholder="Ví dụ: IELTS 6.5+" class="form-input" />
                </div>

                <!-- Row 3: Code & Status -->
                <div class="form-item">
                  <label class="form-label">Mã số định danh</label>
                  <input v-model="currentStudent.code" type="text" readonly disabled class="form-input" />
                </div>

                <div class="form-item">
                  <label class="form-label">Trạng thái học tập</label>
                  <div class="select-container">
                    <select v-model="currentStudent.status" class="form-select">
                      <option value="Đang học">Đang học</option>
                      <option value="Bảo lưu">Bảo lưu</option>
                      <option value="Nghỉ học">Nghỉ học</option>
                    </select>
                    <span class="material-symbols-outlined select-icon-minimal">expand_more</span>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer-minimal">
              <button type="button" class="btn btn-simple" @click="closeModal">Hủy bỏ</button>
              <button type="submit" class="btn btn-solid-primary">
                {{ isEditing ? 'Lưu thay đổi' : 'Tạo hồ sơ' }}
              </button>
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
.utility-group { display: flex; gap: 8px; }

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

/* Common Typo */
.user-info { display: flex; align-items: center; gap: 12px; }
.avatar { width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #ffffff; font-weight: 700; font-size: 14px; }
.user-full-name { font-size: 14px; font-weight: 600; color: #1e293b; margin: 0; }
.user-email-text { font-size: 12px; color: #64748b; margin: 0; }
.account-name { font-size: 14px; color: #475569; font-weight: 500; margin: 0 0 2px 0;}

/* Custom columns in StudentView */
.id-contact { display: flex; flex-direction: column; gap: 6px; align-items: flex-start; }
.code-badge { font-family: monospace; font-size: 12px; font-weight: 700; color: #2563eb; background: #eff6ff; padding: 2px 8px; border-radius: 6px; }
.phone-link { display: flex; align-items: center; gap: 4px; font-size: 13px; color: #475569; }
.phone-link .material-symbols-outlined { font-size: 14px; }
.parent-cell { display: flex; flex-direction: column; }

/* Badges */
.badge { display: inline-flex; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; }
.badge-success { background-color: #ecfdf5; color: #10b981; }
.badge-warning { background-color: #fffaf5; color: #ea580c; }
.badge-danger { background-color: #fef2f2; color: #ef4444; }
.badge-gray { background-color: #f1f5f9; color: #475569; }

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
.modal-box { background-color: #ffffff; width: 100%; max-width: 540px; border-radius: 16px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); border: 1px solid #f1f5f9; animation: modalIn 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
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
.form-input:disabled { background-color: #f1f5f9; color: #94a3b8; cursor: not-allowed; }

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

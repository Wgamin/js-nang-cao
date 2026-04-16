<script setup>
import { ref, computed } from 'vue'

// --- State ---
const students = ref([
  { id: 1, name: 'Nguyễn Văn Hoàng', initials: 'NH', avatarColor: 'blue', email: 'hoang.nv@gmail.com', code: 'STD-2024-001', status: 'Đang học', statusClass: 'success', class: 'IELTS Foundation - K24', parent: 'Nguyễn Văn Hùng' },
  { id: 2, name: 'Phạm Minh Tú', initials: 'MT', avatarColor: 'orange', email: 'minhtu.pham@gmail.com', code: 'STD-2024-042', status: 'Bảo lưu', statusClass: 'warning', class: 'Toeic 650+ T01', parent: 'Lê Thị Mai' },
  { id: 3, name: 'Trần Linh Anh', initials: 'LA', avatarColor: 'rose', email: 'linhanh.tr@gmail.com', code: 'STD-2023-988', status: 'Nghỉ học', statusClass: 'danger', class: 'Giao tiếp Cơ bản', parent: 'Trần Quốc Anh' },
  { id: 4, name: 'Lê Duy Kiên', initials: 'DK', avatarColor: 'indigo', email: 'duykien.le@gmail.com', code: 'STD-2024-115', status: 'Đang học', statusClass: 'success', class: 'IELTS Intensive - K10', parent: 'Lê Duy Mạnh' },
])

const searchQuery = ref('')
const statusFilter = ref('')
const isModalOpen = ref(false)
const isEditing = ref(false)

const initialStudent = {
  id: null,
  name: '',
  email: '',
  code: '',
  class: 'Chưa xếp lớp',
  parent: '',
  status: 'Đang học',
  statusClass: 'success',
  avatarColor: 'blue'
}

const currentStudent = ref({ ...initialStudent })

// --- Computed ---
const filteredStudents = computed(() => {
  return students.value.filter(s => {
    const matchesSearch = s.name.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
                         s.code.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchesStatus = !statusFilter.value || s.status === statusFilter.value
    return matchesSearch && matchesStatus
  })
})

const quickStats = computed(() => [
  { label: 'Tổng học viên', value: students.value.length, icon: 'group', color: '#1d4ed8', bg: 'rgba(29,78,216,0.1)' },
  { label: 'Đang hoạt động', value: students.value.filter(s => s.status === 'Đang học').length, icon: 'verified_user', color: '#10b981', bg: 'rgba(16,185,129,0.1)' },
  { label: 'Đang bảo lưu', value: students.value.filter(s => s.status === 'Bảo lưu').length, icon: 'pause_circle', color: '#f59e0b', bg: 'rgba(245,158,11,0.1)' },
  { label: 'Đã thôi học', value: students.value.filter(s => s.status === 'Nghỉ học').length, icon: 'cancel', color: '#ef4444', bg: 'rgba(239,68,68,0.1)' },
])

// --- Actions ---
function openAddModal() {
  isEditing.value = false
  currentStudent.value = { ...initialStudent, code: `STD-${new Date().getFullYear()}-${String(students.value.length + 1).padStart(3, '0')}` }
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
    const nameParts = currentStudent.value.name.split(' ')
    const initials = nameParts.length > 1 ? nameParts[0][0] + nameParts[nameParts.length - 1][0] : nameParts[0][0]
    
    students.value.push({
      ...currentStudent.value,
      id: newId,
      initials: initials.toUpperCase(),
      avatarColor: ['blue', 'orange', 'rose', 'indigo'][Math.floor(Math.random() * 4)]
    })
  }
  closeModal()
}

function deleteStudent(id) {
  if (confirm('Bạn có chắc chắn muốn xóa học viên này? Thao tác này không thể hoàn tác.')) {
    students.value = students.value.filter(s => s.id !== id)
  }
}

function handleStatusChange(e) {
  const statusMap = {
    'Đang học': 'success',
    'Bảo lưu': 'warning',
    'Nghỉ học': 'danger'
  }
  currentStudent.value.statusClass = statusMap[e.target.value] || 'success'
}
</script>

<template>
  <div class="page-container">
    <header class="page-header">
      <div>
        <h1 class="page-title">Quản lý Học viên</h1>
        <p class="page-subtitle">Hệ thống quản lý thông tin và hồ sơ học viên</p>
      </div>
      <button class="btn-primary" @click="openAddModal">
        <span class="material-symbols-outlined">add</span>
        Thêm Học viên
      </button>
    </header>

    <!-- Quick Stats -->
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
        <!-- Search & Filter -->
        <div class="filter-group">
          <div class="search-box">
            <span class="material-symbols-outlined search-icon">search</span>
            <input 
              v-model="searchQuery" 
              type="text" 
              class="search-input" 
              placeholder="Tìm kiếm theo mã, tên..." 
            />
          </div>
          
          <div class="select-box">
            <select v-model="statusFilter" class="form-select">
              <option value="">Tất cả trạng thái</option>
              <option value="Đang học">Đang học</option>
              <option value="Bảo lưu">Bảo lưu</option>
              <option value="Nghỉ học">Nghỉ học</option>
            </select>
            <span class="material-symbols-outlined select-icon">expand_more</span>
          </div>
        </div>

        <!-- Export / Utility -->
        <div class="action-group">
          <button class="btn-secondary">
            <span class="material-symbols-outlined">download</span>
            Xuất file
          </button>
        </div>
      </div>

      <!-- Table -->
      <div class="table-responsive">
        <table class="data-table">
          <thead>
            <tr>
              <th>HỌC VIÊN</th>
              <th>MÃ HV</th>
              <th>LỚP HIỆN TẠI</th>
              <th>PHỤ HUYNH</th>
              <th>TRẠNG THÁI</th>
              <th class="text-right">THAO TÁC</th>
            </tr>
          </thead>
          <transition-group name="list" tag="tbody">
            <tr v-for="student in filteredStudents" :key="student.id">
              <td>
                <div class="user-cell">
                  <div class="avatar" :class="'avatar-' + student.avatarColor">{{ student.initials }}</div>
                  <div>
                    <h4 class="user-name">{{ student.name }}</h4>
                    <span class="user-email">{{ student.email }}</span>
                  </div>
                </div>
              </td>
              <td><span class="cell-code">{{ student.code }}</span></td>
              <td><span class="cell-text">{{ student.class }}</span></td>
              <td><span class="cell-text">{{ student.parent }}</span></td>
              <td>
                <span class="badge" :class="'badge-' + student.statusClass">{{ student.status }}</span>
              </td>
              <td class="text-right">
                <button class="icon-btn" title="Chỉnh sửa" @click="openEditModal(student)">
                  <span class="material-symbols-outlined">edit</span>
                </button>
                <button class="icon-btn icon-danger" title="Xóa" @click="deleteStudent(student.id)">
                  <span class="material-symbols-outlined">delete</span>
                </button>
              </td>
            </tr>
            <tr v-if="filteredStudents.length === 0">
              <td colspan="6" class="empty-state">
                Không tìm thấy học viên nào phù hợp.
              </td>
            </tr>
          </transition-group>
        </table>
      </div>

      <!-- Pagination -->
      <div class="pagination-footer">
        <span class="page-info">Hiển thị {{ filteredStudents.length }} trên {{ students.length }} học viên</span>
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <Transition name="modal">
      <div v-if="isModalOpen" class="modal-overlay" @click.self="closeModal">
        <div class="modal-content">
          <div class="modal-header">
            <h3>{{ isEditing ? 'Chỉnh sửa Học viên' : 'Thêm Học viên mới' }}</h3>
            <button class="close-btn" @click="closeModal">
              <span class="material-symbols-outlined">close</span>
            </button>
          </div>
          <form @submit.prevent="saveStudent">
            <div class="modal-body">
              <div class="form-row">
                <div class="form-item">
                  <label>Họ và tên</label>
                  <input v-model="currentStudent.name" type="text" placeholder="Ví dụ: Nguyễn Văn A" required />
                </div>
                <div class="form-item">
                  <label>Mã học viên</label>
                  <input v-model="currentStudent.code" type="text" readonly disabled />
                </div>
              </div>
              <div class="form-row">
                <div class="form-item">
                  <label>Email</label>
                  <input v-model="currentStudent.email" type="email" placeholder="email@example.com" required />
                </div>
                <div class="form-item">
                  <label>Phụ huynh</label>
                  <input v-model="currentStudent.parent" type="text" placeholder="Tên phụ huynh" required />
                </div>
              </div>
              <div class="form-row">
                <div class="form-item">
                  <label>Lớp học hiện tại</label>
                  <input v-model="currentStudent.class" type="text" placeholder="Tên lớp học" />
                </div>
                <div class="form-item">
                  <label>Trạng thái</label>
                  <select v-model="currentStudent.status" @change="handleStatusChange">
                    <option value="Đang học">Đang học</option>
                    <option value="Bảo lưu">Bảo lưu</option>
                    <option value="Nghỉ học">Nghỉ học</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn-cancel" @click="closeModal">Hủy bỏ</button>
              <button type="submit" class="btn-submit">
                {{ isEditing ? 'Cập nhật' : 'Thêm mới' }}
              </button>
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

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.page-title {
  font-size: 26px;
  font-weight: 800;
  color: #1e293b;
  margin: 0 0 4px 0;
}

.page-subtitle {
  font-size: 14px;
  color: #64748b;
  font-weight: 500;
  margin: 0;
}

.btn-primary {
  display: flex;
  align-items: center;
  gap: 8px;
  background-color: #1d4ed8;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 12px;
  font-weight: 600;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 0 4px 6px -1px rgba(29, 78, 216, 0.2);
}

.btn-primary:hover {
  background-color: #1e40af;
  transform: translateY(-1px);
}

.btn-secondary {
  display: flex;
  align-items: center;
  gap: 8px;
  background-color: #ffffff;
  color: #475569;
  border: 1px solid #e2e8f0;
  padding: 10px 16px;
  border-radius: 12px;
  font-weight: 600;
  font-size: 13px;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-secondary:hover {
  background-color: #f8fafc;
  border-color: #cbd5e1;
}

/* Stats */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
  margin-bottom: 24px;
}

.stat-card {
  background-color: #ffffff;
  padding: 20px;
  border-radius: 16px;
  display: flex;
  align-items: center;
  gap: 16px;
  box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
}

.stat-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.stat-icon .material-symbols-outlined {
  font-size: 24px;
  font-variation-settings: 'FILL' 1;
}

.stat-info {
  display: flex;
  flex-direction: column;
}

.stat-label {
  font-size: 11px;
  font-weight: 700;
  color: #64748b;
  text-transform: uppercase;
  margin-bottom: 4px;
}

.stat-value {
  font-size: 24px;
  font-weight: 800;
  color: #1e293b;
}

/* Main Card */
.content-card {
  background-color: #ffffff;
  border-radius: 16px;
  box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
  overflow: hidden;
}

.table-actions {
  display: flex;
  justify-content: space-between;
  padding: 20px 24px;
  border-bottom: 1px solid #f1f5f9;
}

.filter-group {
  display: flex;
  gap: 16px;
}

.search-box {
  position: relative;
  width: 280px;
}

.search-icon {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: #94a3b8;
  font-size: 20px;
}

.search-input {
  width: 100%;
  border: 1px solid #e2e8f0;
  padding: 10px 16px 10px 40px;
  border-radius: 12px;
  font-size: 13px;
  font-weight: 500;
  color: #334155;
  outline: none;
  transition: all 0.2s;
}

.search-input:focus {
  border-color: #1d4ed8;
  box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.1);
}

.select-box {
  position: relative;
}

.form-select {
  appearance: none;
  border: 1px solid #e2e8f0;
  padding: 10px 40px 10px 16px;
  border-radius: 12px;
  font-size: 13px;
  font-weight: 600;
  color: #475569;
  background-color: #ffffff;
  outline: none;
  cursor: pointer;
  transition: all 0.2s;
}

.form-select:hover {
  border-color: #cbd5e1;
}

/* Table */
.table-responsive {
  width: 100%;
  overflow-x: auto;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
}

.data-table th {
  text-align: left;
  padding: 16px 24px;
  font-size: 11px;
  font-weight: 700;
  color: #94a3b8;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  background-color: #f8fafc;
  border-bottom: 1px solid #f1f5f9;
}

.data-table td {
  padding: 16px 24px;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
}

.data-table tr:hover td {
  background-color: #f8fafc;
}

.user-cell {
  display: flex;
  align-items: center;
  gap: 12px;
}

.avatar {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 14px;
}

.avatar-blue { background: #dbeafe; color: #1e40af; }
.avatar-orange { background: #ffedd5; color: #9a3412; }
.avatar-rose { background: #ffe4e6; color: #e11d48; }
.avatar-indigo { background: #e0e7ff; color: #3730a3; }

.user-name {
  margin: 0 0 2px 0;
  font-size: 14px;
  font-weight: 700;
  color: #1e293b;
}

.user-email {
  font-size: 12px;
  color: #64748b;
}

.cell-code {
  font-size: 12px;
  font-weight: 600;
  background-color: #f1f5f9;
  padding: 4px 8px;
  border-radius: 6px;
  color: #475569;
}

.cell-text {
  font-size: 13px;
  font-weight: 600;
  color: #334155;
}

.badge {
  padding: 6px 12px;
  border-radius: 999px;
  font-size: 11px;
  font-weight: 700;
}

.badge-success { background-color: #d1fae5; color: #047857; }
.badge-warning { background-color: #fef3c7; color: #b45309; }
.badge-danger { background-color: #fee2e2; color: #b91c1c; }

.text-right { text-align: right; }

.icon-btn {
  background: transparent;
  border: none;
  cursor: pointer;
  color: #64748b;
  padding: 8px;
  border-radius: 8px;
  transition: all 0.2s;
  display: inline-flex;
}

.icon-btn:hover {
  background-color: #f1f5f9;
  color: #1d4ed8;
}

.icon-danger:hover {
  background-color: #fee2e2;
  color: #ef4444;
}

.empty-state {
  text-align: center;
  padding: 40px !important;
  color: #64748b;
  font-style: italic;
}

/* Modal */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  backdrop-filter: blur(4px);
}

.modal-content {
  background: white;
  width: 100%;
  max-width: 600px;
  border-radius: 20px;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.modal-header {
  padding: 20px 24px;
  border-bottom: 1px solid #f1f5f9;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-header h3 {
  font-size: 20px;
  font-weight: 800;
  color: #1e293b;
}

.close-btn {
  background: none;
  border: none;
  cursor: pointer;
  color: #94a3b8;
  display: flex;
}

.modal-body {
  padding: 24px;
}

.form-row {
  display: flex;
  gap: 20px;
  margin-bottom: 20px;
}

.form-item {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-item label {
  font-size: 13px;
  font-weight: 700;
  color: #334155;
}

.form-item input, .form-item select {
  padding: 10px 16px;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  font-size: 14px;
  outline: none;
}

.form-item input:focus, .form-item select:focus {
  border-color: #1d4ed8;
  box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.1);
}

.modal-footer {
  padding: 20px 24px;
  background-color: #f8fafc;
  display: flex;
  justify-content: flex-end;
  gap: 12px;
}

.btn-cancel {
  padding: 10px 20px;
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  font-weight: 600;
  color: #475569;
  cursor: pointer;
}

.btn-submit {
  padding: 10px 24px;
  background-color: #1d4ed8;
  border: none;
  border-radius: 12px;
  font-weight: 600;
  color: white;
  cursor: pointer;
}

/* Animations */
.modal-enter-active, .modal-leave-active { transition: opacity 0.3s ease; }
.modal-enter-from, .modal-leave-to { opacity: 0; }

.list-enter-active, .list-leave-active { transition: all 0.4s ease; }
.list-enter-from, .list-leave-to { opacity: 0; transform: translateX(30px); }

.pagination-footer {
  padding: 16px 24px;
  border-top: 1px solid #f1f5f9;
}
</style>

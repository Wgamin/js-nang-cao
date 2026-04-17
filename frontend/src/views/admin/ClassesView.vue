<script setup>
import { ref, computed } from 'vue'

// --- Mock Data ---
const subjects = [
  { id: 1, name: 'Tiếng Anh IELTS', code: 'ENG-IELTS' },
  { id: 2, name: 'Toán Tư duy', code: 'MATH-L' },
  { id: 3, name: 'Lập trình Scratch', code: 'DEV-SCR' },
]

const teachers = [
  { id: 1, name: 'Nguyễn Minh Anh', initials: 'MA', avatar: 'https://i.pravatar.cc/150?u=1' },
  { id: 2, name: 'Trần Văn Bình', initials: 'VB', avatar: 'https://i.pravatar.cc/150?u=2' },
  { id: 3, name: 'Lê Hoàng Quân', initials: 'HQ', avatar: 'https://i.pravatar.cc/150?u=3' },
]

const rooms = [
  { id: 1, code: 'P.302', capacity: 30 },
  { id: 2, code: 'P.105', capacity: 20 },
  { id: 3, code: 'Lab.01', capacity: 25 },
]

// Sample Students (for detail view)
const mockStudents = [
  { id: 1, full_name: 'Nguyễn Văn Hoàng', student_code: 'STD101', avatar: '', class_id: 1 },
  { id: 2, full_name: 'Trần Minh Hải', student_code: 'STD102', avatar: '', class_id: 1 },
  { id: 3, full_name: 'Lê Thu Hà', student_code: 'STD103', avatar: '', class_id: 2 },
]

// --- State ---
const classes = ref([
  {
    id: 1, class_code: 'SF-IELTS-001', name: 'IELTS Foundation 01',
    subject_id: 1, teacher_id: 1, room_id: 1,
    max_students: 15, current_students: 12, billing_type: 'course', default_fee: '3500000',
    start_date: '2024-05-01', end_date: '2024-08-01', status: 'open', note: ''
  },
  {
    id: 2, class_code: 'SF-DEV-202', name: 'React.js Advanced',
    subject_id: 3, teacher_id: 2, room_id: 3,
    max_students: 20, current_students: 5, billing_type: 'monthly', default_fee: '1200000',
    start_date: '2024-06-15', end_date: '2024-12-15', status: 'planned', note: ''
  }
])

const isModalOpen = ref(false)
const isDetailModalOpen = ref(false)
const isDeleteModalOpen = ref(false)
const isEditing = ref(false)
const searchQuery = ref('')
const selectedClass = ref(null)
const classToDelete = ref(null)

const initialForm = {
  id: null,
  class_code: '',
  name: '',
  subject_id: '',
  teacher_id: '',
  room_id: '',
  max_students: 20,
  billing_type: 'monthly',
  default_fee: 0,
  start_date: '',
  end_date: '',
  status: 'planned',
  note: ''
}

const form = ref({ ...initialForm })

// --- Computed ---
const filteredClasses = computed(() => {
  return classes.value.filter(c => c.name.toLowerCase().includes(searchQuery.value.toLowerCase()) || c.class_code.toLowerCase().includes(searchQuery.value.toLowerCase()))
})

const stats = computed(() => [
  { label: 'Tổng số lớp', value: classes.value.length, icon: 'hub', color: '#2563eb', bg: '#eff6ff' },
  { label: 'Lớp đang mở', value: classes.value.filter(c => c.status === 'open').length, icon: 'door_open', color: '#10b981', bg: '#ecfdf5' },
  { label: 'Sắp khai giảng', value: classes.value.filter(c => c.status === 'planned').length, icon: 'calendar_today', color: '#f59e0b', bg: '#fffbeb' },
  { label: 'Lớp đã đóng', value: classes.value.filter(c => c.status === 'closed').length, icon: 'block', color: '#ef4444', bg: '#fef2f2' },
])

// --- Methods ---
function openAddModal() {
  isEditing.value = false
  form.value = { ...initialForm, class_code: 'CLS-' + Date.now().toString().slice(-6) }
  isModalOpen.value = true
}

function openEditModal(cls) {
  isEditing.value = true
  form.value = { ...cls }
  isModalOpen.value = true
}

function saveClass() {
  if (isEditing.value) {
    const index = classes.value.findIndex(c => c.id === form.value.id)
    if (index !== -1) classes.value[index] = { ...form.value }
  } else {
    classes.value.push({ ...form.value, id: Date.now(), current_students: 0 })
  }
  isModalOpen.value = false
}

function confirmDelete(cls) {
  classToDelete.value = cls
  isDeleteModalOpen.value = true
}

function executeDelete() {
  classes.value = classes.value.filter(c => c.id !== classToDelete.value?.id)
  isDeleteModalOpen.value = false
  classToDelete.value = null
}

function viewDetail(cls) {
  selectedClass.value = cls
  isDetailModalOpen.value = true
}

function transferStudent(studentId, newClassId) {
  const student = mockStudents.find(s => s.id === studentId)
  if (student) {
    const oldClass = classes.value.find(c => c.id === student.class_id)
    if (oldClass) oldClass.current_students--

    const newClass = classes.value.find(c => c.id === newClassId)
    if (newClass) newClass.current_students++

    student.class_id = newClassId
    alert(`Đã chuyển học viên sang lớp ${newClass?.name}`)
  }
}

// Helpers
function getSubjectName(id) { return subjects.find(s => s.id === id)?.name || 'N/A' }
function getTeacher(id) { return teachers.find(t => t.id === id) || { name: 'Chưa gán', initials: '?' } }
function getRoomCode(id) { return rooms.find(r => r.id === id)?.code || 'N/A' }
function getEnrolledStudents(classId) { return mockStudents.filter(s => s.class_id === classId) }

function formatPrice(val) {
  return new Intl.NumberFormat('vi-VN').format(val)
}

function getStatusClass(status) {
  if (status === 'open') return 'badge-success'
  if (status === 'planned') return 'badge-warning'
  return 'badge-gray'
}
</script>

<template>
  <div class="admin-page">
    <div class="headers">
      <div class="header-left">
        <h1 class="title">Quản lý Lớp học</h1>
        <p class="subtitle">Danh sách các lớp học hiện có trong hệ thống</p>
      </div>
      <div class="header-actions">
        <button class="btn btn-primary" @click="openAddModal">
          <span class="material-symbols-outlined">add</span>
          Thêm Lớp học mới
        </button>
      </div>
    </div>

    <!-- Stats -->
    <div class="stats-container">
      <div v-for="stat in stats" :key="stat.label" class="stat-card">
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
      <!-- Toolbar -->
      <div class="toolbar">
        <div class="search-wrapper">
          <span class="material-symbols-outlined search-icon">search</span>
          <input v-model="searchQuery" type="text" class="input-search" placeholder="Tìm kiếm tên lớp..." />
        </div>
        
        <div class="filters">
          <div class="select-wrapper">
            <select class="select-filter">
              <option value="">Tất cả môn học</option>
              <option v-for="s in subjects" :key="s.id" :value="s.id">{{ s.name }}</option>
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
              <th style="width: 20%">Tên lớp</th>
              <th style="width: 15%">Môn học</th>
              <th style="width: 15%">Giáo viên</th>
              <th style="width: 10%">Phòng</th>
              <th style="width: 10%">Sĩ số</th>
              <th style="width: 10%">Học phí</th>
              <th style="width: 10%">Trạng thái</th>
              <th class="text-right">Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="cls in filteredClasses" :key="cls.id" class="table-row">
              <td>
                <div class="class-info-cell" @click="viewDetail(cls)" style="cursor: pointer;">
                  <strong class="user-full-name">{{ cls.name }}</strong>
                  <span class="user-email-text">Mã: {{ cls.class_code }}</span>
                </div>
              </td>
              <td>
                <span class="class-badge-subject">{{ getSubjectName(cls.subject_id) }}</span>
              </td>
              <td>
                <div class="user-info">
                  <div class="avatar" style="background-color: #3b82f6;">{{ getTeacher(cls.teacher_id).initials }}</div>
                  <div class="user-text">
                    <p class="user-full-name">{{ getTeacher(cls.teacher_id).name }}</p>
                  </div>
                </div>
              </td>
              <td><span class="room-text">{{ getRoomCode(cls.room_id) }}</span></td>
              <td>
                <div class="capacity-indicator">
                  <span class="capacity-text" :class="{ 'text-danger': cls.current_students >= cls.max_students }">
                    {{ cls.current_students }}/{{ cls.max_students }}
                  </span>
                  <div class="progress-bg">
                    <div class="progress-bar" :style="{ width: (cls.current_students / cls.max_students * 100) + '%' }"></div>
                  </div>
                </div>
              </td>
              <td><span class="price-text">{{ formatPrice(cls.default_fee) }}đ</span></td>
              <td>
                <span class="badge" :class="getStatusClass(cls.status)">
                  {{ cls.status === 'open' ? 'Đang mở' : cls.status === 'planned' ? 'Sắp mở' : 'Đã đóng' }}
                </span>
              </td>
              <td class="text-right">
                <div class="actions">
                  <button class="action-btn" title="Chi tiết" @click="viewDetail(cls)">
                    <span class="material-symbols-outlined">visibility</span>
                  </button>
                  <button class="action-btn" title="Sửa" @click="openEditModal(cls)">
                    <span class="material-symbols-outlined">edit</span>
                  </button>
                  <button class="action-btn btn-delete" title="Xóa" @click="confirmDelete(cls)">
                    <span class="material-symbols-outlined">delete</span>
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="filteredClasses.length === 0">
              <td colspan="8" class="empty-state">Không tìm thấy lớp học nào.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="table-footer">
        <p>Hiển thị <strong>{{ filteredClasses.length }}</strong> lớp học</p>
      </div>
    </div>

    <!-- Form Modal -->
    <Transition name="modal-fade">
      <div v-if="isModalOpen" class="modal-backdrop" @click.self="isModalOpen = false">
        <div class="modal-box">
          <div class="modal-header">
            <div class="modal-header-text">
              <h2 class="modal-title">{{ isEditing ? 'Cập nhật Lớp học' : 'Thêm lớp học mới' }}</h2>
              <p class="modal-subtitle">Cung cấp thông tin chi tiết để khởi tạo lớp học trong hệ thống.</p>
            </div>
            <button class="btn-close-minimal" @click="isModalOpen = false">
              <span class="material-symbols-outlined">close</span>
            </button>
          </div>

          <form @submit.prevent="saveClass">
            <div class="modal-body">
              <div class="form-grid">
                <div class="form-item span-2">
                  <label class="form-label">Tên lớp học</label>
                  <input v-model="form.name" type="text" placeholder="Ví dụ: IELTS Foundation - T8/2023" required class="form-input" />
                </div>

                <div class="form-item">
                  <label class="form-label">Chọn Môn học</label>
                  <div class="select-container">
                    <select v-model="form.subject_id" required class="form-select">
                      <option value="" disabled>Chọn môn học...</option>
                      <option v-for="s in subjects" :key="s.id" :value="s.id">{{ s.name }}</option>
                    </select>
                    <span class="material-symbols-outlined select-icon-minimal">expand_more</span>
                  </div>
                </div>
                <div class="form-item">
                  <label class="form-label">Giáo viên giảng dạy</label>
                  <div class="select-container">
                    <select v-model="form.teacher_id" required class="form-select">
                      <option value="" disabled>Chọn giáo viên...</option>
                      <option v-for="t in teachers" :key="t.id" :value="t.id">{{ t.name }}</option>
                    </select>
                    <span class="material-symbols-outlined select-icon-minimal">expand_more</span>
                  </div>
                </div>

                <div class="form-item">
                  <label class="form-label">Phòng học</label>
                  <div class="select-container">
                    <select v-model="form.room_id" required class="form-select">
                      <option value="" disabled>Chọn phòng học...</option>
                      <option v-for="r in rooms" :key="r.id" :value="r.id">{{ r.code }}</option>
                    </select>
                    <span class="material-symbols-outlined select-icon-minimal">expand_more</span>
                  </div>
                </div>
                
                <div class="form-item">
                  <label class="form-label">Sĩ số tối đa</label>
                  <input v-model.number="form.max_students" type="number" class="form-input" />
                </div>

                <div class="form-item">
                  <label class="form-label">Học phí (VNĐ)</label>
                  <input v-model.number="form.default_fee" type="number" class="form-input" />
                </div>

                <div class="form-item">
                  <label class="form-label">Trạng thái lớp</label>
                  <div class="select-container">
                    <select v-model="form.status" required class="form-select">
                      <option value="planned">Sắp mở</option>
                      <option value="open">Đang mở</option>
                      <option value="closed">Đã đóng</option>
                    </select>
                    <span class="material-symbols-outlined select-icon-minimal">expand_more</span>
                  </div>
                </div>

                <div class="form-item">
                  <label class="form-label">Ngày khai giảng</label>
                  <input v-model="form.start_date" type="date" class="form-input" />
                </div>
                <div class="form-item">
                  <label class="form-label">Ngày kết thúc</label>
                  <input v-model="form.end_date" type="date" class="form-input" />
                </div>
              </div>
            </div>

            <div class="modal-footer-minimal">
              <button type="button" class="btn btn-simple" @click="isModalOpen = false">Hủy bỏ</button>
              <button type="submit" class="btn btn-solid-primary">Lưu thông tin</button>
            </div>
          </form>
        </div>
      </div>
    </Transition>

    <!-- Modal Detail View -->
    <Transition name="modal-fade">
      <div v-if="isDetailModalOpen" class="modal-backdrop" @click.self="isDetailModalOpen = false">
        <div class="modal-box">
          <div class="modal-header">
            <div class="modal-header-text">
              <h2 class="modal-title">{{ selectedClass?.name }}</h2>
              <p class="modal-subtitle">Danh sách học viên đang theo học tại lớp này.</p>
            </div>
            <button class="btn-close-minimal" @click="isDetailModalOpen = false">
              <span class="material-symbols-outlined">close</span>
            </button>
          </div>

          <div class="modal-body">
            <div class="class-summary">
              <div class="info-item">
                <span class="label">Môn học</span>
                <strong>{{ getSubjectName(selectedClass?.subject_id) }}</strong>
              </div>
              <div class="info-item">
                <span class="label">Giáo viên</span>
                <strong>{{ getTeacher(selectedClass?.teacher_id).name }}</strong>
              </div>
              <div class="info-item">
                <span class="label">Sĩ số</span>
                <strong :class="{ 'text-danger': selectedClass?.current_students >= selectedClass?.max_students }">
                  {{ selectedClass?.current_students }} / {{ selectedClass?.max_students }}
                </strong>
              </div>
            </div>

            <div class="student-list-container">
              <table class="user-table student-table">
                <thead>
                  <tr>
                    <th>Học viên</th>
                    <th>Mã HV</th>
                    <th class="text-right">Thao tác</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="student in getEnrolledStudents(selectedClass?.id)" :key="student.id">
                    <td>
                      <div class="user-info">
                        <div class="avatar" style="background-color: #3b82f6; width: 32px; height: 32px; font-size: 12px;">{{ student.full_name[0] }}</div>
                        <span class="user-full-name">{{ student.full_name }}</span>
                      </div>
                    </td>
                    <td><span class="code-badge">{{ student.student_code }}</span></td>
                    <td class="text-right">
                      <select @change="e => transferStudent(student.id, parseInt(e.target.value))" class="select-filter select-sm" style="width: auto; min-width: 0;">
                        <option value="" selected disabled>Chuyển lớp...</option>
                        <option v-for="c in classes.filter(c => c.id !== selectedClass.id)" :key="c.id" :value="c.id">
                          {{ c.name }}
                        </option>
                      </select>
                    </td>
                  </tr>
                  <tr v-if="getEnrolledStudents(selectedClass?.id).length === 0">
                    <td colspan="3" class="empty-state">Chưa có học viên trong lớp này.</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Delete Confirm Modal -->
    <Transition name="modal-fade">
      <div v-if="isDeleteModalOpen" class="modal-backdrop" @click.self="isDeleteModalOpen = false">
        <div class="modal-box" style="max-width: 480px; text-align: center;">
          <div class="modal-body" style="padding: 40px 32px;">
            <div style="width: 64px; height: 64px; border-radius: 50%; background: #fef2f2; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
              <span class="material-symbols-outlined" style="font-size: 32px; color: #ef4444;">warning</span>
            </div>
            <h2 class="modal-title">Xác nhận xóa lớp học</h2>
            <p style="color: #64748b; font-size: 14px; margin-top: 8px;">Bạn có chắc chắn muốn xóa lớp <strong>{{ classToDelete?.name }}</strong>? Hành động này không thể hoàn tác.</p>
            
            <div style="display: flex; gap: 12px; justify-content: center; margin-top: 32px;">
              <button class="btn btn-simple" @click="isDeleteModalOpen = false">Hủy bỏ</button>
              <button class="btn btn-solid-primary" style="background: #ef4444;" @click="executeDelete">Xác nhận xóa</button>
            </div>
          </div>
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

/* Common Typo */
.user-info { display: flex; align-items: center; gap: 12px; }
.avatar { width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #ffffff; font-weight: 700; font-size: 14px; }
.user-full-name { font-size: 14px; font-weight: 600; color: #1e293b; margin: 0; }
.user-email-text { font-size: 12px; color: #64748b; margin: 0; }

/* Specifics for Classes */
.class-info-cell { display: flex; flex-direction: column; gap: 2px; }
.class-badge-subject { background: #eff6ff; color: #2563eb; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 700; }
.room-text { font-size: 13px; font-weight: 600; color: #475569; }
.price-text { font-size: 13px; font-weight: 600; color: #2563eb; }
.code-badge { font-family: monospace; font-size: 12px; font-weight: 700; color: #2563eb; background: #eff6ff; padding: 2px 8px; border-radius: 6px; }

.capacity-indicator { display: flex; flex-direction: column; gap: 6px; }
.capacity-text { font-size: 13px; font-weight: 600; color: #475569; }
.progress-bg { height: 4px; width: 60px; background: #f1f5f9; border-radius: 2px; }
.progress-bar { height: 100%; background: #2563eb; border-radius: 2px; }
.text-danger { color: #ef4444 !important; }

/* Detail elements */
.class-summary { background: #f8fafc; border-radius: 12px; padding: 16px; display: flex; justify-content: space-around; margin-bottom: 24px; border: 1px solid #e2e8f0; }
.info-item { display: flex; flex-direction: column; align-items: center; gap: 4px; }
.info-item .label { font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; }
.info-item strong { font-size: 14px; color: #1e293b; }

.student-list-container { max-height: 300px; overflow-y: auto; }
.student-table th { padding: 8px 12px; }
.student-table td { padding: 12px; }
.select-sm { padding: 6px 12px; font-size: 12px; }

/* Badges */
.badge { display: inline-flex; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; }
.badge-success { background-color: #ecfdf5; color: #10b981; }
.badge-warning { background-color: #fffbeb; color: #f59e0b; }
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

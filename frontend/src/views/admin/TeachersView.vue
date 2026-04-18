<script setup>
import { ref, computed } from 'vue'

// --- State ---
const teachers = ref([
  { 
    id: 1, 
    name: 'Cô Nguyễn Minh Anh', 
    initials: 'MA', 
    avatarColor: 'rose', 
    phone: '0901112222', 
    email: 'anh.nm@gmail.com', 
    subject: 'Tiếng Anh IELTS', 
    classes: 'ENG-101, ENG-205', 
    schedule: 'T2 (18h-20h), T4 (18h-20h)',
    sessionsTaught: 42,
    rating: 5.0, 
    status: 'Đang dạy' 
  },
  { 
    id: 2, 
    name: 'Thầy Trần Văn Bình', 
    initials: 'VB', 
    avatarColor: 'blue', 
    phone: '0912223333', 
    email: 'binh.tv@gmail.com', 
    subject: 'Toán Tư duy', 
    classes: 'MATH-S, MATH-A', 
    schedule: 'T3 (17h-19h), T5 (17h-19h)',
    sessionsTaught: 38,
    rating: 4.8, 
    status: 'Đang dạy' 
  },
  { 
    id: 3, 
    name: 'Cô Phạm Trà My', 
    initials: 'TM', 
    avatarColor: 'indigo', 
    phone: '0983334444', 
    email: 'my.pt@gmail.com', 
    subject: 'Ngữ Văn Ngoại khóa', 
    classes: 'LIT-01', 
    schedule: 'T7 (08h-10h)',
    sessionsTaught: 12,
    rating: 4.5, 
    status: 'Nghỉ phép' 
  },
  { 
    id: 4, 
    name: 'Thầy Lê Hoàng Quân', 
    initials: 'HQ', 
    avatarColor: 'orange', 
    phone: '0944445555', 
    email: 'quan.lh@gmail.com', 
    subject: 'Luyện thi Vật Lý', 
    classes: 'PHY-12, PHY-11', 
    schedule: 'CN (14h-16h), CN (16h-18h)',
    sessionsTaught: 25,
    rating: 4.9, 
    status: 'Đang dạy' 
  },
])

const isModalOpen = ref(false)
const isEditing = ref(false)
const searchQuery = ref('')
const subjectFilter = ref('')

const initialTeacher = {
  id: null,
  name: '',
  phone: '',
  email: '',
  subject: 'Tiếng Anh IELTS',
  status: 'Đang dạy',
  rating: 5.0,
  classes: '',
  schedule: '',
  sessionsTaught: 0
}

const currentTeacher = ref({ ...initialTeacher })

// --- Computed ---
const filteredTeachers = computed(() => {
  return teachers.value.filter(t => {
    const matchesSearch = t.name.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
                         t.phone.includes(searchQuery.value) ||
                         t.email.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                         t.classes.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchesSubject = !subjectFilter.value || t.subject.includes(subjectFilter.value)
    return matchesSearch && matchesSubject
  })
})

const quickStats = computed(() => {
  const total = teachers.value.length
  const active = teachers.value.filter(t => t.status === 'Đang dạy').length
  const totalSessions = teachers.value.reduce((acc, t) => acc + t.sessionsTaught, 0)
  
  return [
    { label: 'Tổng Giáo viên', value: total, icon: 'record_voice_over', color: '#2563eb', bg: '#eff6ff' },
    { label: 'Đang Giảng dạy', value: active, icon: 'school', color: '#10b981', bg: '#ecfdf5' },
    { label: 'Tổng buổi đã dạy', value: totalSessions, icon: 'history_edu', color: '#f59e0b', bg: '#fffbeb' },
    { label: 'Sĩ số GV/Lớp', value: (total / (active || 1)).toFixed(1), icon: 'query_stats', color: '#7c3aed', bg: '#f5f3ff' },
  ]
})

// --- Actions ---
function openAddModal() {
  isEditing.value = false
  currentTeacher.value = { ...initialTeacher }
  isModalOpen.value = true
}

function openEditModal(teacher) {
  isEditing.value = true
  currentTeacher.value = { ...teacher }
  isModalOpen.value = true
}

function closeModal() {
  isModalOpen.value = false
}

function saveTeacher() {
  if (isEditing.value) {
    const index = teachers.value.findIndex(t => t.id === currentTeacher.value.id)
    if (index !== -1) {
      teachers.value[index] = { ...currentTeacher.value }
    }
  } else {
    const newId = teachers.value.length ? Math.max(...teachers.value.map(t => t.id)) + 1 : 1
    const nameParts = currentTeacher.value.name.split(' ')
    const initials = nameParts.length > 1 ? nameParts[0][0] + nameParts[nameParts.length - 1][0] : nameParts[0][0]
    
    teachers.value.push({
      ...currentTeacher.value,
      id: newId,
      initials: initials.toUpperCase(),
      avatarColor: ['blue', 'orange', 'rose', 'indigo'][Math.floor(Math.random() * 4)]
    })
  }
  closeModal()
}

function deleteTeacher(id) {
  if (confirm('Bạn có chắc chắn muốn xóa giáo viên này?')) {
    teachers.value = teachers.value.filter(t => t.id !== id)
  }
}

const getAvatarColor = (id) => {
  const colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444']
  return colors[id % 4]
}

const getStatusBadgeClass = (status) => {
  if (status === 'Đang dạy') return 'badge-success'
  if (status === 'Nghỉ phép') return 'badge-warning'
  return 'badge-gray'
}
</script>

<template>
  <div class="admin-page">
    <!-- Header Section -->
    <div class="headers">
      <div class="header-left">
        <h1 class="title">Quản lý Giáo viên</h1>
        <p class="subtitle">Hồ sơ trích ngang, phân công lớp học và theo dõi buổi dạy</p>
      </div>
      <button class="btn btn-primary" @click="openAddModal">
        <span class="material-symbols-outlined">person_add</span>
        Thêm Giáo viên
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

    <!-- Main Content -->
    <div class="content-box">
      <!-- Toolbar -->
      <div class="toolbar">
        <div class="search-wrapper">
          <span class="material-symbols-outlined search-icon">search</span>
          <input v-model="searchQuery" type="text" class="input-search" placeholder="Tìm theo tên, lớp học, SĐT..." />
        </div>
        
        <div class="filters">
          <div class="select-wrapper">
            <select v-model="subjectFilter" class="select-filter">
              <option value="">Tất cả chuyên môn</option>
              <option value="Toán">Toán Học</option>
              <option value="Tiếng Anh">Tiếng Anh</option>
              <option value="Ngữ Văn">Ngữ Văn</option>
              <option value="Vật Lý">Vật Lý</option>
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
              <th style="width: 30%">Giáo viên & Lớp dạy</th>
              <th style="width: 25%">Môn dạy & Lịch trình</th>
              <th style="width: 15%">Số buổi</th>
              <th style="width: 15%">Trạng thái</th>
              <th style="width: 15%" class="text-right">Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="teacher in filteredTeachers" :key="teacher.id" class="table-row">
              <td>
                <div class="user-info">
                  <div class="avatar" :style="{ backgroundColor: getAvatarColor(teacher.id) }">{{ teacher.initials }}</div>
                  <div class="user-text">
                    <p class="user-full-name">{{ teacher.name }}</p>
                    <div class="user-email-text">
                      Lớp: <strong style="color: #2563eb;">{{ teacher.classes }}</strong>
                    </div>
                  </div>
                </div>
              </td>
              <td>
                <div class="teaching-info">
                  <span class="subject-badge">{{ teacher.subject }}</span>
                  <div class="schedule-text">
                    <span class="material-symbols-outlined">calendar_today</span>
                    {{ teacher.schedule }}
                  </div>
                </div>
              </td>
              <td>
                <div class="sessions-counter">
                  <span class="count-num">{{ teacher.sessionsTaught }}</span>
                  <span class="count-unit">buổi</span>
                </div>
              </td>
              <td>
                <span class="badge" :class="getStatusBadgeClass(teacher.status)">
                  {{ teacher.status }}
                </span>
              </td>
              <td class="text-right">
                <div class="actions">
                  <button class="action-btn" title="Chỉnh sửa hồ sơ" @click="openEditModal(teacher)">
                    <span class="material-symbols-outlined">edit</span>
                  </button>
                  <button class="action-btn btn-delete" title="Xóa" @click="deleteTeacher(teacher.id)">
                    <span class="material-symbols-outlined">delete</span>
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="filteredTeachers.length === 0">
              <td colspan="5" class="empty-state">Không tìm thấy giáo viên nào.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Footer -->
      <div class="table-footer">
        <p>Hiển thị <strong>{{ filteredTeachers.length }}</strong> giáo viên</p>
      </div>
    </div>

    <!-- Teacher Modal -->
    <Transition name="modal-fade">
      <div v-if="isModalOpen" class="modal-backdrop" @click.self="closeModal">
        <div class="modal-box">
          <div class="modal-header">
            <div class="modal-header-text">
              <h2 class="modal-title">{{ isEditing ? 'Cập Nhật Hồ Sơ Giáo Viên' : 'Thêm Giáo Viên Mới' }}</h2>
              <p class="modal-subtitle">Ghi nhận thông tin công tác, phân lớp và lịch giảng dạy.</p>
            </div>
            <button class="btn-close-minimal" @click="closeModal" title="Đóng">
              <span class="material-symbols-outlined">close</span>
            </button>
          </div>

          <form @submit.prevent="saveTeacher">
            <div class="modal-body">
              <div class="form-grid">
                <!-- Row 1: Name & Phone -->
                <div class="form-item span-2">
                  <label class="form-label">Họ và tên giáo viên</label>
                  <input v-model="currentTeacher.name" type="text" placeholder="Ví dụ: Thầy Nguyễn Văn A" required class="form-input" />
                </div>
                
                <div class="form-item">
                  <label class="form-label">Số điện thoại</label>
                  <input v-model="currentTeacher.phone" type="tel" placeholder="09xx xxx xxx" required class="form-input" />
                </div>
                
                <div class="form-item">
                  <label class="form-label">Địa chỉ Email</label>
                  <input v-model="currentTeacher.email" type="email" placeholder="gv.example@edumanager.vn" required class="form-input" />
                </div>

                <!-- Row 2: Subject & Classes -->
                <div class="form-item">
                  <label class="form-label">Môn học chính</label>
                  <div class="select-container">
                    <select v-model="currentTeacher.subject" class="form-select">
                      <option value="Toán Tư duy">Toán Tư duy</option>
                      <option value="Tiếng Anh IELTS">Tiếng Anh IELTS</option>
                      <option value="Tiếng Anh Giao tiếp">Tiếng Anh Giao tiếp</option>
                      <option value="Ngữ Văn Ngoại khóa">Ngữ Văn Ngoại khóa</option>
                      <option value="Luyện thi Vật Lý">Luyện thi Vật Lý</option>
                    </select>
                    <span class="material-symbols-outlined select-icon-minimal">expand_more</span>
                  </div>
                </div>

                <div class="form-item">
                  <label class="form-label">Lớp đang dạy</label>
                  <input v-model="currentTeacher.classes" type="text" placeholder="Ví dụ: ENG-101, ENG-205" class="form-input" />
                </div>

                <!-- Row 3: Schedule -->
                <div class="form-item span-2">
                  <label class="form-label">Lịch dạy cố định</label>
                  <input v-model="currentTeacher.schedule" type="text" placeholder="Ví dụ: T2 (18h-20h), T4 (18h-20h)..." class="form-input" />
                </div>

                <!-- Row 4: Status & Sessions -->
                <div class="form-item">
                  <label class="form-label">Trạng thái</label>
                  <div class="select-container">
                    <select v-model="currentTeacher.status" class="form-select">
                      <option value="Đang dạy">Đang giảng dạy</option>
                      <option value="Nghỉ phép">Đang nghỉ phép</option>
                      <option value="Tạm nghỉ">Tạm ngừng giảng dạy</option>
                    </select>
                    <span class="material-symbols-outlined select-icon-minimal">expand_more</span>
                  </div>
                </div>

                <div class="form-item">
                  <label class="form-label">Số buổi đã dạy</label>
                  <input v-model.number="currentTeacher.sessionsTaught" type="number" min="0" class="form-input" />
                </div>
              </div>
            </div>

            <div class="modal-footer-minimal">
              <button type="button" class="btn btn-simple" @click="closeModal">Hủy bỏ</button>
              <button type="submit" class="btn btn-solid-primary">{{ isEditing ? 'Cập nhật hồ sơ' : 'Tạo giáo viên' }}</button>
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

/* Common Typo */
.user-info { display: flex; align-items: center; gap: 12px; }
.avatar { width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #ffffff; font-weight: 700; font-size: 14px; }
.user-full-name { font-size: 14px; font-weight: 600; color: #1e293b; margin: 0; }
.user-email-text { font-size: 12px; color: #64748b; margin: 0; }

/* Custom elements in TeachersView */
.teaching-info { display: flex; flex-direction: column; gap: 6px; align-items: flex-start; }
.subject-badge { font-size: 11px; font-weight: 700; background-color: #f1f5f9; padding: 3px 8px; border-radius: 6px; color: #475569; text-transform: uppercase;}
.schedule-text { display: flex; align-items: center; gap: 4px; font-size: 12px; color: #64748b; }
.schedule-text .material-symbols-outlined { font-size: 14px; }
.sessions-counter { display: inline-flex; align-items: baseline; gap: 4px; background-color: #f8fafc; padding: 4px 10px; border-radius: 8px; border: 1px solid #e2e8f0; }
.count-num { font-size: 14px; font-weight: 700; color: #1e293b; }
.count-unit { font-size: 11px; color: #64748b; font-weight: 500; }

/* Badges */
.badge { display: inline-flex; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; }
.badge-success { background-color: #ecfdf5; color: #10b981; }
.badge-warning { background-color: #fffaf5; color: #ea580c; }
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

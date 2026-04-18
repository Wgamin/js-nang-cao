<script setup>
import { ref, computed } from 'vue'

// --- State ---
const subjects = ref([
  { id: 1, name: 'Tiếng Anh Giao tiếp Mầm non', code: 'ENG-KIDS', type: 'Ngon ngu', level: 'Mầm non', duration: '24', activeClasses: 5, status: 'Hoạt động' },
  { id: 2, name: 'Toán Tư duy Nâng cao', code: 'MATH-ADV', type: 'Toan hoc', level: 'Cấp 1', duration: '48', activeClasses: 3, status: 'Hoạt động' },
  { id: 3, name: 'Khoa học Không gian', code: 'SCI-SPC', type: 'Khoa hoc', level: 'Cấp 2', duration: '12', activeClasses: 0, status: 'Ngừng đào tạo' },
  { id: 4, name: 'Luyện thi IELTS 6.5+', code: 'IEL-65', type: 'Luyen thi', level: 'THPT & Sinh viên', duration: '72', activeClasses: 8, status: 'Hoạt động' },
  { id: 5, name: 'Mỹ thuật Sáng tạo', code: 'ART-CR', type: 'Nang khieu', level: 'Mọi lứa tuổi', duration: '24', activeClasses: 2, status: 'Bảo trì' },
])

const isModalOpen = ref(false)
const isEditing = ref(false)
const searchQuery = ref('')
const typeFilter = ref('')

const initialSubject = {
  id: null,
  name: '',
  code: '',
  type: 'Ngon ngu',
  level: 'Cấp 1',
  duration: '',
  status: 'Hoạt động',
  activeClasses: 0
}

const currentSubject = ref({ ...initialSubject })

// --- Computed ---
const filteredSubjects = computed(() => {
  return subjects.value.filter(s => {
    const matchesSearch = s.name.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
                         s.code.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchesType = !typeFilter.value || s.type === typeFilter.value
    return matchesSearch && matchesType
  })
})

const quickStats = computed(() => {
  const total = subjects.value.length
  const active = subjects.value.filter(s => s.status === 'Hoạt động').length
  const totalClasses = subjects.value.reduce((acc, s) => acc + s.activeClasses, 0)
  
  return [
    { label: 'Tổng Môn Học', value: total, icon: 'menu_book', color: '#2563eb', bg: '#eff6ff' },
    { label: 'Đang Đào tạo', value: active, icon: 'auto_stories', color: '#10b981', bg: '#ecfdf5' },
    { label: 'Tổng Lớp Mở', value: totalClasses, icon: 'class', color: '#8b5cf6', bg: '#f5f3ff' },
    { label: 'Ngừng Đào tạo', value: total - active, icon: 'bookmark_remove', color: '#ef4444', bg: '#fef2f2' },
  ]
})

// --- Actions ---
function openAddModal() {
  isEditing.value = false
  currentSubject.value = { ...initialSubject, code: `SUB-${String(subjects.value.length + 1).padStart(3, '0')}` }
  isModalOpen.value = true
}

function openEditModal(subject) {
  isEditing.value = true
  currentSubject.value = { ...subject }
  isModalOpen.value = true
}

function closeModal() {
  isModalOpen.value = false
}

function saveSubject() {
  if (isEditing.value) {
    const index = subjects.value.findIndex(s => s.id === currentSubject.value.id)
    if (index !== -1) {
      subjects.value[index] = { ...currentSubject.value }
    }
  } else {
    const newId = subjects.value.length ? Math.max(...subjects.value.map(s => s.id)) + 1 : 1
    subjects.value.push({
      ...currentSubject.value,
      id: newId
    })
  }
  closeModal()
}

function deleteSubject(id) {
  if (confirm('Bạn có chắc chắn muốn xóa môn học này?')) {
    subjects.value = subjects.value.filter(s => s.id !== id)
  }
}

function getStatusClass(status) {
  if (status === 'Hoạt động') return 'badge-success'
  if (status === 'Bảo trì') return 'badge-warning'
  return 'badge-danger'
}
</script>

<template>
  <div class="admin-page">
    <div class="headers">
      <div class="header-left">
        <h1 class="title">Quản lý Môn học</h1>
        <p class="subtitle">Quản lý danh mục chương trình đào tạo và giáo trình học thuật</p>
      </div>
      <div class="header-actions">
        <button class="btn btn-secondary">
          <span class="material-symbols-outlined">library_books</span>
          Kho Giáo trình
        </button>
        <button class="btn btn-primary" @click="openAddModal">
          <span class="material-symbols-outlined">add_circle</span>
          Thêm Môn học mới
        </button>
      </div>
    </div>

    <!-- Stats -->
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
      <!-- Toolbar -->
      <div class="toolbar">
        <div class="search-wrapper">
          <span class="material-symbols-outlined search-icon">search</span>
          <input v-model="searchQuery" type="text" class="input-search" placeholder="Tìm tên hoặc mã môn..." />
        </div>
        
        <div class="filters">
          <div class="select-wrapper">
            <select v-model="typeFilter" class="select-filter">
              <option value="">Tất cả Nhóm môn</option>
              <option value="Ngon ngu">Ngôn ngữ</option>
              <option value="Toan hoc">Toán học</option>
              <option value="Khoa hoc">Khoa học</option>
              <option value="Nang khieu">Năng khiếu</option>
              <option value="Luyen thi">Luyện thi</option>
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
              <th style="width: 15%">Mã môn</th>
              <th style="width: 25%">Tên môn học & Nhóm</th>
              <th style="width: 15%">Cấp độ / Khối</th>
              <th style="width: 15%">Thời lượng</th>
              <th style="width: 15%">Lớp đang mở</th>
              <th style="width: 15%">Trạng thái</th>
              <th class="text-right">Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="subject in filteredSubjects" :key="subject.id" class="table-row">
              <td><span class="subject-code">{{ subject.code }}</span></td>
              <td>
                <div class="subject-info">
                  <h4 class="subject-title">{{ subject.name }}</h4>
                  <span class="subject-meta">{{ subject.type }}</span>
                </div>
              </td>
              <td><span class="level-text">{{ subject.level }}</span></td>
              <td>
                <div class="duration-badge">
                  <span class="material-symbols-outlined">schedule</span>
                  {{ subject.duration }} buổi
                </div>
              </td>
              <td>
                <div class="class-info">
                  <span class="class-num" :class="{ 'zero': subject.activeClasses === 0 }">{{ subject.activeClasses }}</span>
                  <span class="class-unit">Lớp</span>
                </div>
              </td>
              <td>
                <span class="badge" :class="getStatusClass(subject.status)">{{ subject.status }}</span>
              </td>
              <td class="text-right">
                <div class="actions">
                  <button class="action-btn" title="Sửa môn học" @click="openEditModal(subject)">
                    <span class="material-symbols-outlined">edit_square</span>
                  </button>
                  <button class="action-btn btn-delete" title="Xóa môn học" @click="deleteSubject(subject.id)">
                    <span class="material-symbols-outlined">delete</span>
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="filteredSubjects.length === 0">
              <td colspan="7" class="empty-state">Không tìm thấy môn học nào.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Footer -->
      <div class="table-footer">
        <p>Hiển thị <strong>{{ filteredSubjects.length }}</strong> môn học</p>
      </div>
    </div>

    <!-- Subject Modal -->
    <Transition name="modal-fade">
      <div v-if="isModalOpen" class="modal-backdrop" @click.self="closeModal">
        <div class="modal-box">
          <div class="modal-header">
            <div class="modal-header-text">
              <h2 class="modal-title">{{ isEditing ? 'Cập Nhật Môn Học' : 'Thêm Chương Trình Mới' }}</h2>
              <p class="modal-subtitle">Thông tin này định hình cấu trúc khóa học và giáo bám sát chương trình.</p>
            </div>
            <button class="btn-close-minimal" @click="closeModal" title="Đóng">
              <span class="material-symbols-outlined">close</span>
            </button>
          </div>

          <form @submit.prevent="saveSubject">
            <div class="modal-body">
              <div class="form-grid">
                <!-- Row 1 -->
                <div class="form-item span-2">
                  <label class="form-label">Tên môn học / Chương trình</label>
                  <input v-model="currentSubject.name" type="text" placeholder="Ví dụ: Tiếng Anh Giao tiếp Level 1" required class="form-input" />
                </div>

                <!-- Row 2 -->
                <div class="form-item">
                  <label class="form-label">Mã môn</label>
                  <input v-model="currentSubject.code" type="text" placeholder="ENG-AUTO" required class="form-input" />
                </div>
                
                <div class="form-item">
                  <label class="form-label">Nhóm môn học</label>
                  <div class="select-container">
                    <select v-model="currentSubject.type" class="form-select">
                      <option value="Ngon ngu">Ngôn ngữ</option>
                      <option value="Toan hoc">Toán học</option>
                      <option value="Khoa hoc">Khoa học</option>
                      <option value="Nang khieu">Năng khiếu</option>
                      <option value="Luyen thi">Luyện thi</option>
                    </select>
                    <span class="material-symbols-outlined select-icon-minimal">expand_more</span>
                  </div>
                </div>

                <!-- Row 3 -->
                <div class="form-item">
                  <label class="form-label">Cấp độ / Đối tượng</label>
                  <div class="select-container">
                    <select v-model="currentSubject.level" class="form-select">
                      <option value="Mầm non">Mầm non</option>
                      <option value="Cấp 1">Cấp 1 (Tiểu học)</option>
                      <option value="Cấp 2">Cấp 2 (THCS)</option>
                      <option value="Cấp 3">Cấp 3 (THPT)</option>
                      <option value="Sinh viên">Sinh viên & Người đi làm</option>
                    </select>
                    <span class="material-symbols-outlined select-icon-minimal">expand_more</span>
                  </div>
                </div>

                <div class="form-item">
                  <label class="form-label">Thời lượng (Buổi)</label>
                  <input v-model="currentSubject.duration" type="number" placeholder="24" required class="form-input" />
                </div>

                <!-- Row 4 -->
                <div class="form-item span-2">
                  <label class="form-label">Trạng thái đào tạo</label>
                  <div class="select-container">
                    <select v-model="currentSubject.status" class="form-select">
                      <option value="Hoạt động">Đang đào tạo (Hoạt động)</option>
                      <option value="Bảo trì">Tạm ngưng/Bảo trì</option>
                      <option value="Ngừng đào tạo">Ngừng đào tạo</option>
                    </select>
                    <span class="material-symbols-outlined select-icon-minimal">expand_more</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="modal-footer-minimal">
              <button type="button" class="btn btn-simple" @click="closeModal">Hủy bỏ</button>
              <button type="submit" class="btn btn-solid-primary">{{ isEditing ? 'Lưu hệ thống' : 'Tạo môn học' }}</button>
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
.subject-code { font-family: monospace; font-size: 13px; font-weight: 700; color: #2563eb; background: #eff6ff; padding: 4px 10px; border-radius: 6px; border: 1px solid #dbeafe; }
.subject-info { display: flex; flex-direction: column; gap: 2px; }
.subject-title { margin: 0; font-size: 14px; font-weight: 600; color: #1e293b; }
.subject-meta { font-size: 12px; color: #64748b; font-weight: 500; }
.level-text { font-size: 13px; font-weight: 500; color: #475569; }

.duration-badge { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600; color: #475569; background: #f1f5f9; padding: 4px 12px; border-radius: 99px; }
.duration-badge span { font-size: 14px; color: #64748b; }

.class-info { display: flex; align-items: baseline; gap: 4px; }
.class-num { font-size: 16px; font-weight: 700; color: #2563eb; }
.class-num.zero { color: #94a3b8; }
.class-unit { font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; }

/* Badges */
.badge { display: inline-flex; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; }
.badge-success { background-color: #ecfdf5; color: #10b981; }
.badge-warning { background-color: #fffaf5; color: #ea580c; }
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

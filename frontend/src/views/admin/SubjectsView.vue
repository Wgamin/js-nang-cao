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
    { label: 'Tổng Môn Học', value: total, icon: 'menu_book', color: '#1d4ed8', bg: 'rgba(29,78,216,0.1)' },
    { label: 'Đang Đào tạo', value: active, icon: 'auto_stories', color: '#10b981', bg: 'rgba(16,185,129,0.1)' },
    { label: 'Tổng Lớp Mở', value: totalClasses, icon: 'class', color: '#8b5cf6', bg: 'rgba(139,92,246,0.1)' },
    { label: 'Ngừng Đào tạo', value: total - active, icon: 'bookmark_remove', color: '#ef4444', bg: 'rgba(239,68,68,0.1)' },
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
  <div class="page-container">
    <header class="page-header">
      <div>
        <h1 class="page-title">Quản lý Môn học</h1>
        <p class="page-subtitle">Quản lý danh mục chương trình đào tạo và giáo trình học thuật</p>
      </div>
      <button class="btn-primary" @click="openAddModal">
        <span class="material-symbols-outlined">add_circle</span>
        Thêm Môn học mới
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
            <input v-model="searchQuery" type="text" class="search-input" placeholder="Tìm tên hoặc mã môn..." />
          </div>
          
          <div class="select-box">
            <select v-model="typeFilter" class="form-select">
              <option value="">Tất cả Nhóm môn</option>
              <option value="Ngon ngu">Ngôn ngữ</option>
              <option value="Toan hoc">Toán học</option>
              <option value="Khoa hoc">Khoa học</option>
              <option value="Nang khieu">Năng khiếu</option>
              <option value="Luyen thi">Luyện thi</option>
            </select>
            <span class="material-symbols-outlined select-icon">expand_more</span>
          </div>
        </div>

        <div class="action-group">
          <button class="btn-secondary">
            <span class="material-symbols-outlined">library_books</span>
            Kho Giáo trình
          </button>
        </div>
      </div>

      <!-- Table -->
      <div class="table-responsive">
        <table class="data-table">
          <thead>
            <tr>
              <th>MÃ MÔN</th>
              <th>TÊN MÔN HỌC & NHÓM</th>
              <th>CẤP ĐỘ / KHỐI</th>
              <th>THỜI LƯỢNG</th>
              <th>LỚP ĐANG MỞ</th>
              <th>TRẠNG THÁI</th>
              <th class="text-right">THAO TÁC</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="subject in filteredSubjects" :key="subject.id">
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
                <button class="icon-btn" title="Sửa môn học" @click="openEditModal(subject)">
                  <span class="material-symbols-outlined">edit_square</span>
                </button>
                <button class="icon-btn text-danger" title="Xóa môn học" @click="deleteSubject(subject.id)">
                  <span class="material-symbols-outlined">delete</span>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="pagination-footer">
        <span class="page-info">Hiển thị {{ filteredSubjects.length }} trên {{ subjects.length }} môn học</span>
        <div class="pagination">
          <button class="page-btn"><span class="material-symbols-outlined">chevron_left</span></button>
          <button class="page-btn active">1</button>
          <button class="page-btn"><span class="material-symbols-outlined">chevron_right</span></button>
        </div>
      </div>
    </div>

    <!-- Subject Modal -->
    <Transition name="fade">
      <div v-if="isModalOpen" class="modal-overlay" @click.self="closeModal">
        <div class="modal-content premium-modal">
          <div class="modal-header">
            <div class="header-info">
              <h2 class="modal-title">{{ isEditing ? 'Cập Nhật Môn Học' : 'Thêm Chương Trình Mới' }}</h2>
              <p class="modal-subtitle">Thông tin này định hình cấu trúc khóa học và giáo bám sát chương trình.</p>
            </div>
            <button class="close-btn" @click="closeModal">
              <span class="material-symbols-outlined">close</span>
            </button>
          </div>

          <form @submit.prevent="saveSubject">
            <div class="modal-body">
              <div class="form-group">
                <label>TÊN MÔN HỌC / CHƯƠNG TRÌNH</label>
                <div class="input-wrapper">
                  <span class="material-symbols-outlined input-icon">auto_stories</span>
                  <input v-model="currentSubject.name" type="text" placeholder="Ví dụ: Tiếng Anh Giao tiếp Level 1" required />
                </div>
              </div>

              <div class="form-row">
                <div class="form-group flex-1">
                  <label>MÃ MÔN</label>
                  <div class="input-wrapper">
                    <input v-model="currentSubject.code" type="text" placeholder="ENG-AUTO" required />
                  </div>
                </div>
                <div class="form-group flex-1">
                  <label>NHÓM MÔN HỌC</label>
                  <div class="select-wrapper">
                    <select v-model="currentSubject.type">
                      <option value="Ngon ngu">Ngôn ngữ</option>
                      <option value="Toan hoc">Toán học</option>
                      <option value="Khoa hoc">Khoa học</option>
                      <option value="Nang khieu">Năng khiếu</option>
                      <option value="Luyen thi">Luyện thi</option>
                    </select>
                    <span class="material-symbols-outlined expand-icon">expand_more</span>
                  </div>
                </div>
              </div>

              <div class="form-row">
                <div class="form-group flex-1">
                  <label>CẤP ĐỘ / ĐỐI TƯỢNG</label>
                  <div class="select-wrapper">
                    <select v-model="currentSubject.level">
                      <option value="Mầm non">Mầm non</option>
                      <option value="Cấp 1">Cấp 1 (Tiểu học)</option>
                      <option value="Cấp 2">Cấp 2 (THCS)</option>
                      <option value="Cấp 3">Cấp 3 (THPT)</option>
                      <option value="Sinh viên">Sinh viên & Người đi làm</option>
                    </select>
                    <span class="material-symbols-outlined expand-icon">expand_more</span>
                  </div>
                </div>
                <div class="form-group flex-1">
                  <label>THỜI LƯỢNG (BUỔI)</label>
                  <div class="input-wrapper">
                    <input v-model="currentSubject.duration" type="number" placeholder="24" required />
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label>TRẠNG THÁI ĐÀO TẠO</label>
                <div class="radio-group">
                  <label class="radio-item">
                    <input type="radio" v-model="currentSubject.status" value="Hoạt động" />
                    <span class="radio-mark"></span>
                    Đang đào tạo
                  </label>
                  <label class="radio-item">
                    <input type="radio" v-model="currentSubject.status" value="Ngừng đào tạo" />
                    <span class="radio-mark"></span>
                    Tạm ngừng
                  </label>
                </div>
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn-cancel" @click="closeModal">Hủy bỏ</button>
              <button type="submit" class="btn-submit">{{ isEditing ? 'Lưu hệ thống' : 'Tạo môn học' }}</button>
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
  font-family: var(--font-outfit);
  font-size: 28px;
  font-weight: 800;
  color: var(--on-surface);
  margin: 0 0 4px 0;
}

.page-subtitle {
  font-size: 14px;
  color: var(--on-surface-variant);
  font-weight: 500;
  margin: 0;
}

.btn-primary {
  display: flex;
  align-items: center;
  gap: 8px;
  background-color: var(--primary);
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 12px;
  font-weight: 600;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 0 4px 12px rgba(0, 74, 198, 0.2);
}

.btn-primary:hover {
  background-color: var(--primary-hover);
  transform: translateY(-1px);
}

.btn-secondary {
  display: flex;
  align-items: center;
  gap: 8px;
  background-color: var(--surface-container-highest);
  color: var(--on-surface);
  border: 1px solid var(--outline-variant);
  padding: 10px 16px;
  border-radius: 12px;
  font-weight: 600;
  font-size: 13px;
  cursor: pointer;
}

/* Stats */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
  margin-bottom: 24px;
}

.stat-card {
  background-color: white;
  padding: 20px;
  border-radius: 20px;
  display: flex;
  align-items: center;
  gap: 16px;
  box-shadow: var(--shadow-sm);
  border: 1px solid var(--surface-container);
}

.stat-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.stat-info { display: flex; flex-direction: column; }
.stat-label { font-size: 12px; font-weight: 700; color: var(--on-surface-variant); text-transform: uppercase; margin-bottom: 2px; }
.stat-value { font-family: var(--font-outfit); font-size: 22px; font-weight: 800; color: var(--on-surface); }

/* Main Card */
.content-card {
  background-color: white;
  border-radius: 24px;
  box-shadow: var(--shadow-md);
  border: 1px solid var(--surface-container);
  overflow: hidden;
}

.table-actions {
  display: flex;
  justify-content: space-between;
  padding: 20px 24px;
  border-bottom: 1px solid var(--surface-container);
}

.filter-group { display: flex; gap: 16px; }
.search-box { position: relative; width: 320px; }
.search-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--outline); font-size: 20px; }
.search-input {
  width: 100%;
  border: 1.5px solid var(--outline-variant);
  padding: 10px 16px 10px 44px;
  border-radius: 14px;
  font-size: 14px;
  font-weight: 500;
  color: var(--on-surface);
  outline: none;
  transition: all 0.2s;
  background-color: var(--surface-container-lowest);
}
.search-input:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(0, 74, 198, 0.08); }

.select-box { position: relative; }
.form-select {
  appearance: none;
  border: 1.5px solid var(--outline-variant);
  padding: 10px 40px 10px 16px;
  border-radius: 14px;
  font-size: 14px;
  font-weight: 600;
  color: var(--on-surface-variant);
  background-color: white;
  outline: none;
  cursor: pointer;
}
.select-icon { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: var(--outline); pointer-events: none; }

/* Table */
.table-responsive { width: 100%; overflow-x: auto; }
.data-table { width: 100%; border-collapse: collapse; }
.data-table th {
  text-align: left;
  padding: 16px 24px;
  font-size: 11px;
  font-weight: 800;
  color: var(--outline);
  text-transform: uppercase;
  letter-spacing: 0.1em;
  background-color: var(--surface-container-low);
  border-bottom: 1px solid var(--surface-container);
}
.data-table td { padding: 16px 24px; border-bottom: 1px solid var(--surface-container); vertical-align: middle; }
.data-table tr:hover td { background-color: var(--surface-container-lowest); }

.subject-code { font-family: monospace; font-size: 13px; font-weight: 800; color: var(--primary); background: #eff6ff; padding: 6px 12px; border-radius: 8px; border: 1px solid #dbeafe; }
.subject-info { display: flex; flex-direction: column; gap: 2px; }
.subject-title { margin: 0; font-size: 15px; font-weight: 700; color: var(--on-surface); }
.subject-meta { font-size: 12px; color: var(--outline); font-weight: 600; }
.level-text { font-size: 13px; font-weight: 600; color: var(--on-surface-variant); }

.duration-badge { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 700; color: #475569; background: var(--surface-container-low); padding: 4px 12px; border-radius: 99px; }
.duration-badge span { font-size: 14px; color: var(--outline); }

.class-info { display: flex; align-items: baseline; gap: 4px; }
.class-num { font-size: 16px; font-weight: 800; color: var(--primary); }
.class-num.zero { color: var(--outline); }
.class-unit { font-size: 11px; font-weight: 700; color: var(--outline); text-transform: uppercase; }

.badge { padding: 6px 14px; border-radius: 999px; font-size: 12px; font-weight: 700; }
.badge-success { background-color: #dcfce7; color: #15803d; }
.badge-warning { background-color: #fef3c7; color: #b45309; }
.badge-danger { background-color: #fee2e2; color: #b91c1c; }

.text-right { text-align: right; }
.text-danger { color: var(--error) !important; }

.icon-btn { background: transparent; border: none; cursor: pointer; color: var(--outline); padding: 10px; border-radius: 10px; transition: all 0.2s; display: inline-flex; }
.icon-btn:hover { background-color: var(--surface-container); color: var(--primary); }

.empty-state { padding: 40px; text-align: center; color: var(--outline); font-weight: 600; }

/* Modals */
.modal-overlay { position: fixed; inset: 0; background-color: rgba(0, 0, 0, 0.5); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; z-index: 1000; }
.premium-modal { background: white; width: 100%; max-width: 540px; border-radius: 32px; box-shadow: var(--shadow-xl); overflow: hidden; animation: modalIn 0.4s cubic-bezier(0.16, 1, 0.3, 1); }

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

.input-wrapper { position: relative; display: flex; align-items: center; }
.input-icon { position: absolute; left: 16px; color: var(--outline); font-size: 20px; }
.input-wrapper input { width: 100%; background-color: var(--surface-container-lowest); border: 1.5px solid var(--outline-variant); padding: 14px 16px 14px 48px; border-radius: 16px; font-size: 15px; font-weight: 600; color: var(--on-surface); outline: none; transition: all 0.3s; }
.input-wrapper input:focus { border-color: var(--primary); box-shadow: 0 0 0 5px rgba(0, 74, 198, 0.08); }

.select-wrapper { position: relative; display: flex; align-items: center; }
.select-wrapper select { width: 100%; appearance: none; background-color: var(--surface-container-lowest); border: 1.5px solid var(--outline-variant); padding: 14px 40px 14px 16px; border-radius: 16px; font-size: 15px; font-weight: 600; color: var(--on-surface); outline: none; cursor: pointer; }
.expand-icon { position: absolute; right: 16px; color: var(--outline); pointer-events: none; }

.radio-group { display: flex; gap: 32px; padding: 10px 0; }
.radio-item { display: flex; align-items: center; gap: 12px; font-weight: 700; color: var(--on-surface-variant); cursor: pointer; }
.radio-mark { width: 22px; height: 22px; border: 2.5px solid var(--outline-variant); border-radius: 50%; position: relative; }
input:checked + .radio-mark { border-color: var(--primary); }
input:checked + .radio-mark::after { content: ''; position: absolute; inset: 4px; background: var(--primary); border-radius: 50%; }

.modal-footer { padding: 0 40px 40px 40px; display: flex; justify-content: flex-end; gap: 16px; }
.btn-cancel { background: transparent; color: var(--outline); font-weight: 800; border: none; cursor: pointer; font-size: 15px; }
.btn-submit { background-color: var(--primary); color: white; border: none; padding: 14px 32px; border-radius: 16px; font-weight: 800; font-size: 15px; cursor: pointer; box-shadow: 0 8px 16px rgba(0, 74, 198, 0.25); transition: all 0.3s; }

/* Transitions */
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

.pagination-footer { display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; }
.page-info { font-size: 14px; font-weight: 600; color: var(--on-surface-variant); }
.pagination { display: flex; gap: 6px; }
.page-btn { min-width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border: 1.5px solid var(--outline-variant); background-color: white; color: var(--on-surface-variant); border-radius: 10px; font-weight: 700; font-size: 14px; cursor: pointer; }
.page-btn.active { background-color: var(--primary); color: white; border-color: var(--primary); }
</style>

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
    { label: 'Tổng Giáo viên', value: total, icon: 'record_voice_over', color: '#1d4ed8', bg: 'rgba(29,78,216,0.1)' },
    { label: 'Đang Giảng dạy', value: active, icon: 'school', color: '#10b981', bg: 'rgba(16,185,129,0.1)' },
    { label: 'Tổng buổi đã dạy', value: totalSessions, icon: 'history_edu', color: '#f59e0b', bg: 'rgba(245,158,11,0.1)' },
    { label: 'Sĩ số GV/Lớp', value: (total / (active || 1)).toFixed(1), icon: 'query_stats', color: '#8b5cf6', bg: 'rgba(139,92,246,0.1)' },
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
</script>

<template>
  <div class="page-container">
    <header class="page-header">
      <div>
        <h1 class="page-title">Quản lý Giáo viên</h1>
        <p class="page-subtitle">Hồ sơ trích ngang, phân công lớp học và theo dõi buổi dạy</p>
      </div>
      <button class="btn-primary" @click="openAddModal">
        <span class="material-symbols-outlined">person_add</span>
        Thêm Giáo viên
      </button>
    </header>

    <!-- Quick Stats -->
    <div class="stats-grid">
      <div v-for="stat in quickStats" :key="stat.label" class="stat-card">
        <div class="stat-icon" :style="{ color: stat.color, backgroundColor: stat.bg }">
          <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">{{ stat.icon }}</span>
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
            <input v-model="searchQuery" type="text" class="search-input" placeholder="Tìm theo tên, lớp học, SĐT..." />
          </div>
          
          <div class="select-box">
            <select v-model="subjectFilter" class="form-select">
              <option value="">Tất cả chuyên môn</option>
              <option value="Toán">Toán Học</option>
              <option value="Tiếng Anh">Tiếng Anh</option>
              <option value="Ngữ Văn">Ngữ Văn</option>
              <option value="Vật Lý">Vật Lý</option>
            </select>
            <span class="material-symbols-outlined select-icon">expand_more</span>
          </div>
        </div>

        <div class="action-group">
          <button class="btn-secondary">
            <span class="material-symbols-outlined">event_note</span>
            Lịch dạy tổng quát
          </button>
          <button class="btn-secondary">
            <span class="material-symbols-outlined">payments</span>
            Thống kê buổi dạy
          </button>
        </div>
      </div>

      <!-- Table -->
      <div class="table-responsive">
        <table class="data-table">
          <thead>
            <tr>
              <th>GIÁO VIÊN & LỚP DẠY</th>
              <th>MÔN DẠY & LỊCH TRÌNH</th>
              <th>SỐ BUỔI</th>
              <th>TRẠNG THÁI</th>
              <th class="text-right">THAO TÁC</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="teacher in filteredTeachers" :key="teacher.id">
              <td>
                <div class="user-cell">
                  <div class="avatar" :class="'avatar-' + teacher.avatarColor">{{ teacher.initials }}</div>
                  <div>
                    <h4 class="user-name">{{ teacher.name }}</h4>
                    <div class="meta-classes">
                      <span class="class-label">Lớp:</span>
                      <span class="class-values">{{ teacher.classes }}</span>
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
                <span class="badge" :class="teacher.status === 'Đang dạy' ? 'badge-success' : 'badge-warning'">
                  {{ teacher.status }}
                </span>
              </td>
              <td class="text-right">
                <button class="icon-btn" title="Chỉnh sửa hồ sơ" @click="openEditModal(teacher)">
                  <span class="material-symbols-outlined">edit</span>
                </button>
                <button class="icon-btn" title="Chi tiết buổi dạy">
                  <span class="material-symbols-outlined">assessment</span>
                </button>
                <button class="icon-btn text-danger" title="Xóa" @click="deleteTeacher(teacher.id)">
                  <span class="material-symbols-outlined">delete</span>
                </button>
              </td>
            </tr>
            <tr v-if="filteredTeachers.length === 0">
              <td colspan="5" class="empty-state">Không tìm thấy giáo viên nào.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="pagination-footer">
        <span class="page-info">Hiển thị {{ filteredTeachers.length }} trên {{ teachers.length }} giáo viên</span>
        <div class="pagination">
          <button class="page-btn"><span class="material-symbols-outlined">chevron_left</span></button>
          <button class="page-btn active">1</button>
          <button class="page-btn"><span class="material-symbols-outlined">chevron_right</span></button>
        </div>
      </div>
    </div>

    <!-- Teacher Modal -->
    <Transition name="fade">
      <div v-if="isModalOpen" class="modal-overlay" @click.self="closeModal">
        <div class="modal-content premium-modal">
          <div class="modal-header">
            <div class="header-info">
              <h2 class="modal-title">{{ isEditing ? 'Cập Nhật Hồ Sơ Giáo Viên' : 'Thêm Giáo Viên Mới' }}</h2>
              <p class="modal-subtitle">Ghi nhận thông tin công tác, phân lớp và lịch giảng dạy.</p>
            </div>
            <button class="close-btn" @click="closeModal">
              <span class="material-symbols-outlined">close</span>
            </button>
          </div>

          <form @submit.prevent="saveTeacher">
            <div class="modal-body">
              <!-- Basic Info Section -->
              <div class="form-section-title">THÔNG TIN CƠ BẢN</div>
              <div class="form-row">
                <div class="form-group flex-2">
                  <label>HỌ VÀ TÊN GIÁO VIÊN</label>
                  <div class="input-wrapper">
                    <span class="material-symbols-outlined input-icon">person_check</span>
                    <input v-model="currentTeacher.name" type="text" placeholder="Ví dụ: Thầy Nguyễn Văn A" required />
                  </div>
                </div>
                <div class="form-group flex-1">
                  <label>SỐ ĐIỆN THOẠI</label>
                  <div class="input-wrapper">
                    <input v-model="currentTeacher.phone" type="tel" placeholder="09xx xxx xxx" required />
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label>ĐỊA CHỈ EMAIL</label>
                <div class="input-wrapper">
                  <span class="material-symbols-outlined input-icon">mail</span>
                  <input v-model="currentTeacher.email" type="email" placeholder="gv.example@edumanager.vn" required />
                </div>
              </div>

              <!-- Teaching Assignment Section -->
              <div class="form-section-title">PHÂN CÔNG GIẢNG DẠY</div>
              <div class="form-row">
                <div class="form-group flex-1">
                  <label>MÔN HỌC CHÍNH</label>
                  <div class="select-wrapper">
                    <select v-model="currentTeacher.subject">
                      <option value="Toán Tư duy">Toán Tư duy</option>
                      <option value="Tiếng Anh IELTS">Tiếng Anh IELTS</option>
                      <option value="Tiếng Anh Giao tiếp">Tiếng Anh Giao tiếp</option>
                      <option value="Ngữ Văn Ngoại khóa">Ngữ Văn Ngoại khóa</option>
                      <option value="Luyện thi Vật Lý">Luyện thi Vật Lý</option>
                    </select>
                    <span class="material-symbols-outlined expand-icon">expand_more</span>
                  </div>
                </div>
                <div class="form-group flex-1">
                  <label>LỚP ĐANG DẠY</label>
                  <div class="input-wrapper">
                    <span class="material-symbols-outlined input-icon">door_open</span>
                    <input v-model="currentTeacher.classes" type="text" placeholder="Ví dụ: ENG-101, ENG-205" />
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label>LỊCH DẠY CỐ ĐỊNH</label>
                <div class="input-wrapper">
                  <span class="material-symbols-outlined input-icon">calendar_month</span>
                  <input v-model="currentTeacher.schedule" type="text" placeholder="Ví dụ: T2 (18h-20h), T4 (18h-20h)..." />
                </div>
              </div>

              <div class="form-row">
                <div class="form-group flex-1">
                  <label>TRẠNG THÁI</label>
                  <div class="select-wrapper">
                    <select v-model="currentTeacher.status">
                      <option value="Đang dạy">Đang giảng dạy</option>
                      <option value="Nghỉ phép">Đang nghỉ phép</option>
                      <option value="Tạm nghỉ">Tạm ngừng giảng dạy</option>
                    </select>
                    <span class="material-symbols-outlined expand-icon">expand_more</span>
                  </div>
                </div>
                <div class="form-group flex-1">
                  <label>SỐ BUỔI ĐÃ DẠY</label>
                  <div class="input-wrapper">
                    <span class="material-symbols-outlined input-icon">history_edu</span>
                    <input v-model.number="currentTeacher.sessionsTaught" type="number" min="0" />
                  </div>
                </div>
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn-cancel" @click="closeModal">Hủy bỏ</button>
              <button type="submit" class="btn-submit">{{ isEditing ? 'Cập nhật hồ sơ' : 'Tạo giáo viên' }}</button>
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

.stat-icon .material-symbols-outlined {
  font-size: 24px;
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

.action-group { display: flex; gap: 12px; }

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

.user-cell { display: flex; align-items: center; gap: 12px; }
.avatar { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 15px; font-family: var(--font-outfit); }
.avatar-rose { background: #ffe4e6; color: #e11d48; }
.avatar-blue { background: #dbeafe; color: #1e40af; }
.avatar-indigo { background: #e0e7ff; color: #3730a3; }
.avatar-orange { background: #ffedd5; color: #9a3412; }

.user-name { margin: 0 0 4px 0; font-size: 15px; font-weight: 700; color: var(--on-surface); }
.meta-classes { display: flex; align-items: center; gap: 6px; font-size: 11px; }
.class-label { color: var(--outline); font-weight: 700; text-transform: uppercase; font-size: 10px; }
.class-values { color: var(--primary); font-weight: 700; }

.teaching-info { display: flex; flex-direction: column; gap: 4px; }
.subject-badge { width: fit-content; font-size: 11px; font-weight: 800; padding: 4px 10px; border-radius: 6px; background-color: var(--surface-container-low); border: 1px solid var(--outline-variant); color: var(--on-surface-variant); text-transform: uppercase; }
.schedule-text { display: flex; align-items: center; gap: 6px; font-size: 12px; color: var(--outline); font-weight: 600; }
.schedule-text .material-symbols-outlined { font-size: 14px; }

.sessions-counter { display: flex; flex-direction: column; align-items: center; background: var(--surface-container-low); padding: 6px; border-radius: 10px; border: 1px solid var(--surface-container); }
.count-num { font-size: 16px; font-weight: 800; color: var(--on-surface); }
.count-unit { font-size: 10px; font-weight: 700; color: var(--outline); text-transform: uppercase; }

.badge { padding: 6px 14px; border-radius: 999px; font-size: 12px; font-weight: 700; }
.badge-success { background-color: #dcfce7; color: #15803d; }
.badge-warning { background-color: #fef3c7; color: #b45309; }

.icon-btn { background: transparent; border: none; cursor: pointer; color: var(--outline); padding: 10px; border-radius: 10px; transition: all 0.2s; display: inline-flex; }
.icon-btn:hover { background-color: var(--surface-container); color: var(--primary); }

/* Modals */
.modal-overlay { position: fixed; inset: 0; background-color: rgba(0, 0, 0, 0.5); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; z-index: 1000; }
.premium-modal { background: white; width: 100%; max-width: 580px; border-radius: 32px; box-shadow: var(--shadow-xl); overflow: hidden; animation: modalIn 0.4s cubic-bezier(0.16, 1, 0.3, 1); }

@keyframes modalIn { from { transform: scale(0.95) translateY(20px); opacity: 0; } to { transform: scale(1) translateY(0); opacity: 1; } }

.modal-header { padding: 32px 40px 16px 40px; display: flex; justify-content: space-between; align-items: flex-start; }
.modal-title { font-family: var(--font-outfit); font-size: 24px; font-weight: 800; color: var(--on-surface); margin: 0 0 8px 0; }
.modal-subtitle { font-size: 13px; color: var(--on-surface-variant); font-weight: 500; line-height: 1.6; }
.close-btn { background: var(--surface-container); border: none; color: var(--outline); cursor: pointer; padding: 8px; border-radius: 12px; }

.modal-body { padding: 24px 40px 40px 40px; }
.form-section-title { font-size: 11px; font-weight: 900; color: var(--primary); margin: 10px 0 20px 0; letter-spacing: 0.1em; border-bottom: 2px solid var(--surface-container); padding-bottom: 8px; }
.form-group { margin-bottom: 24px; }
.form-group label { display: block; font-size: 11px; font-weight: 900; color: var(--outline); margin-bottom: 10px; letter-spacing: 0.1em; }
.form-row { display: flex; gap: 20px; }
.flex-1 { flex: 1; }
.flex-2 { flex: 2; }

.input-wrapper { position: relative; display: flex; align-items: center; }
.input-icon { position: absolute; left: 16px; color: var(--outline); font-size: 20px; }
.input-wrapper input { width: 100%; background-color: var(--surface-container-lowest); border: 1.5px solid var(--outline-variant); padding: 14px 16px 14px 48px; border-radius: 16px; font-size: 15px; font-weight: 600; color: var(--on-surface); outline: none; transition: all 0.3s; }
.input-wrapper input:focus { border-color: var(--primary); box-shadow: 0 0 0 5px rgba(0, 74, 198, 0.08); }

.select-wrapper { position: relative; display: flex; align-items: center; }
.select-wrapper select { width: 100%; appearance: none; background-color: var(--surface-container-lowest); border: 1.5px solid var(--outline-variant); padding: 14px 40px 14px 16px; border-radius: 16px; font-size: 15px; font-weight: 600; color: var(--on-surface); outline: none; cursor: pointer; }
.expand-icon { position: absolute; right: 16px; color: var(--outline); pointer-events: none; }

.modal-footer { padding: 0 40px 40px 40px; display: flex; justify-content: flex-end; gap: 16px; }
.btn-cancel { background: transparent; color: var(--outline); font-weight: 800; border: none; cursor: pointer; font-size: 15px; }
.btn-submit { background-color: var(--primary); color: white; border: none; padding: 14px 32px; border-radius: 16px; font-weight: 800; font-size: 15px; cursor: pointer; box-shadow: 0 8px 16px rgba(0, 74, 198, 0.25); transition: all 0.3s; }

/* Transitions */
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

.pagination-footer { display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; }
.pagination { display: flex; gap: 6px; }
.page-btn { min-width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border: 1.5px solid var(--outline-variant); background-color: white; color: var(--on-surface-variant); border-radius: 10px; font-weight: 700; font-size: 14px; cursor: pointer; }
.page-btn.active { background-color: var(--primary); color: white; border-color: var(--primary); }
</style>

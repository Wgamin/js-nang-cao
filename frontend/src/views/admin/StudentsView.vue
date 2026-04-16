<script setup>
import { ref, computed } from 'vue'
import * as XLSX from 'xlsx'

// --- State ---
const students = ref([
  { id: 1, name: 'Nguyễn Văn Hoàng', code: 'STD-24001', email: 'hoang.nv@student.vn', phone: '0901234567', parent: 'Nguyễn Văn Hùng', status: 'Đang học', statusClass: 'success', class: 'IELTS Foundation', initials: 'VH', avatarColor: 'blue' },
  { id: 2, name: 'Phạm Minh Tú', code: 'STD-24042', email: 'tu.pm@student.vn', phone: '0988776554', parent: 'Lê Thị Mai', status: 'Bảo lưu', statusClass: 'warning', class: 'Toeic 650+', initials: 'MT', avatarColor: 'rose' },
  { id: 3, name: 'Trần Linh Anh', code: 'STD-23988', email: 'anh.tl@student.vn', phone: '0912334112', parent: 'Trần Quốc Anh', status: 'Nghỉ học', statusClass: 'danger', class: 'Giao tiếp Cơ bản', initials: 'LA', avatarColor: 'rose' },
  { id: 4, name: 'Lê Duy Kiên', code: 'STD-24115', email: 'kien.ld@student.vn', phone: '0977112233', parent: 'Lê Duy Mạnh', status: 'Đang học', statusClass: 'success', class: 'IELTS Intensive', initials: 'DK', avatarColor: 'indigo' },
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
    const matchesSearch = s.name.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
                         s.code.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                         s.phone.includes(searchQuery.value)
    const matchesStatus = !statusFilter.value || s.status === statusFilter.value
    return matchesSearch && matchesStatus
  })
})

const quickStats = computed(() => {
  const total = students.value.length
  const active = students.value.filter(s => s.status === 'Đang học').length
  const reserved = students.value.filter(s => s.status === 'Bảo lưu').length
  const resigned = students.value.filter(s => s.status === 'Nghỉ học').length
  
  return [
    { label: 'Tổng Học Viên', value: total, icon: 'group', color: '#1d4ed8', bg: 'rgba(29,78,216,0.1)' },
    { label: 'Đang theo học', value: active, icon: 'verified_user', color: '#10b981', bg: 'rgba(16,185,129,0.1)' },
    { label: 'Đang Bảo Lưu', value: reserved, icon: 'pause_circle', color: '#f59e0b', bg: 'rgba(245,158,11,0.1)' },
    { label: 'Đã nghỉ học', value: resigned, icon: 'cancel', color: '#ef4444', bg: 'rgba(239,68,68,0.1)' },
  ]
})

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
  const statusMap = { 'Đang học': 'success', 'Bảo lưu': 'warning', 'Nghỉ học': 'danger' }
  const studentData = { ...currentStudent.value, statusClass: statusMap[currentStudent.value.status] }

  if (isEditing.value) {
    const index = students.value.findIndex(s => s.id === studentData.id)
    if (index !== -1) {
      students.value[index] = studentData
    }
  } else {
    const newId = students.value.length ? Math.max(...students.value.map(s => s.id)) + 1 : 1
    const nameParts = studentData.name.split(' ')
    const initials = nameParts.length > 1 ? nameParts[0][0] + nameParts[nameParts.length - 1][0] : nameParts[0][0]
    
    students.value.push({ 
      ...studentData, 
      id: newId, 
      initials: initials.toUpperCase(),
      avatarColor: ['blue', 'rose', 'indigo', 'orange'][Math.floor(Math.random() * 4)]
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
      const name = row['Họ tên'] || 'N/A'
      const nameParts = name.split(' ')
      const initials = nameParts.length > 1 ? nameParts[0][0] + nameParts[nameParts.length - 1][0] : nameParts[0][0]

      students.value.push({
        id: newId,
        name: name,
        code: row['Mã HV'] || `STD-24${String(students.value.length + 1).padStart(3, '0')}`,
        email: row['Email'] || '',
        phone: row['SĐT'] || '',
        parent: row['Phụ huynh'] || '',
        status: row['Trạng thái'] || 'Đang học',
        statusClass: row['Trạng thái'] === 'Bảo lưu' ? 'warning' : row['Trạng thái'] === 'Nghỉ học' ? 'danger' : 'success',
        class: row['Lớp'] || 'Chưa xếp lớp',
        initials: initials.toUpperCase(),
        avatarColor: ['blue', 'rose', 'indigo', 'orange'][Math.floor(Math.random() * 4)]
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
  <div class="page-container">
    <header class="page-header">
      <div>
        <h1 class="page-title">Quản lý Học viên</h1>
        <p class="page-subtitle">Hồ sơ cá nhân, phụ huynh và lộ trình đào tạo học tập</p>
      </div>
      <div class="header-actions">
        <button class="btn-secondary" @click="downloadTemplate">
          <span class="material-symbols-outlined">download</span>
          Tải mẫu
        </button>
        <button class="btn-secondary" @click="triggerExcelImport">
          <span class="material-symbols-outlined">upload_file</span>
          Nhập Excel
        </button>
        <input type="file" ref="fileInput" hidden @change="handleExcelImport" accept=".xlsx, .xls" />
        <button class="btn-primary" @click="openAddModal">
          <span class="material-symbols-outlined">person_add</span>
          Thêm Học viên
        </button>
      </div>
    </header>

    <!-- Stats -->
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
        <div class="filter-group">
          <div class="search-box">
            <span class="material-symbols-outlined search-icon">search</span>
            <input v-model="searchQuery" type="text" class="search-input" placeholder="Tìm theo tên, SĐT, mã HV..." />
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

        <div class="action-group">
          <button class="btn-secondary">
            <span class="material-symbols-outlined">analytics</span>
            Báo cáo chuyên cần
          </button>
        </div>
      </div>

      <!-- Table -->
      <div class="table-responsive">
        <table class="data-table">
          <thead>
            <tr>
              <th>HỌC VIÊN & LỚP HỌC</th>
              <th>MÃ SỐ & LIÊN HỆ</th>
              <th>PHỤ HUYNH</th>
              <th>TRẠNG THÁI</th>
              <th class="text-right">THAO TÁC</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="student in filteredStudents" :key="student.id">
              <td>
                <div class="user-cell">
                  <div class="avatar" :class="'avatar-' + student.avatarColor">{{ student.initials }}</div>
                  <div>
                    <h4 class="user-name">{{ student.name }}</h4>
                    <div class="meta-classes">
                      <span class="class-label">Lớp:</span>
                      <span class="class-values">{{ student.class || 'Chưa xếp lớp' }}</span>
                    </div>
                  </div>
                </div>
              </td>
              <td>
                <div class="teaching-info">
                  <span class="code-badge">{{ student.code }}</span>
                  <div class="contact-details">
                    <span class="material-symbols-outlined">call</span>
                    {{ student.phone }}
                  </div>
                </div>
              </td>
              <td>
                <div class="parent-info-cell">
                  <span class="parent-name-text">{{ student.parent }}</span>
                  <span class="parent-sub">Đại diện gia đình</span>
                </div>
              </td>
              <td>
                <span class="badge" :class="'badge-' + student.statusClass">{{ student.status }}</span>
              </td>
              <td class="text-right">
                <button class="icon-btn" title="Chỉnh sửa hồ sơ" @click="openEditModal(student)">
                  <span class="material-symbols-outlined">edit</span>
                </button>
                <button class="icon-btn" title="Điểm danh cá nhân">
                  <span class="material-symbols-outlined">fact_check</span>
                </button>
                <button class="icon-btn text-danger" title="Xóa" @click="deleteStudent(student.id)">
                  <span class="material-symbols-outlined">delete</span>
                </button>
              </td>
            </tr>
            <tr v-if="filteredStudents.length === 0">
              <td colspan="5" class="empty-state">Không tìm thấy học viên nào.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="pagination-footer">
        <span class="page-info">Hiển thị {{ filteredStudents.length }} trên {{ students.length }} học viên</span>
        <div class="pagination">
          <button class="page-btn"><span class="material-symbols-outlined">chevron_left</span></button>
          <button class="page-btn active">1</button>
          <button class="page-btn"><span class="material-symbols-outlined">chevron_right</span></button>
        </div>
      </div>
    </div>

    <!-- Student Modal -->
    <Transition name="fade">
      <div v-if="isModalOpen" class="modal-overlay" @click.self="closeModal">
        <div class="modal-content premium-modal">
          <div class="modal-header">
            <div class="header-info">
              <h2 class="modal-title">{{ isEditing ? 'Cập Nhật Hồ Sơ Học Viên' : 'Tiếp Nhận Học Viên Mới' }}</h2>
              <p class="modal-subtitle">Ghi nhận thông tin chi tiết, liên hệ phụ huynh và xếp lớp học tập.</p>
            </div>
            <button class="close-btn" @click="closeModal">
              <span class="material-symbols-outlined">close</span>
            </button>
          </div>

          <form @submit.prevent="saveStudent">
            <div class="modal-body">
              <!-- Basic Info Section -->
              <div class="form-section-title">THÔNG TIN CƠ BẢN</div>
              <div class="form-row">
                <div class="form-group flex-2">
                  <label>HỌ VÀ TÊN HỌC VIÊN</label>
                  <div class="input-wrapper">
                    <span class="material-symbols-outlined input-icon">face</span>
                    <input v-model="currentStudent.name" type="text" placeholder="Ví dụ: Nguyễn Văn A" required />
                  </div>
                </div>
                <div class="form-group flex-1">
                  <label>SỐ ĐIỆN THOẠI</label>
                  <div class="input-wrapper">
                    <input v-model="currentStudent.phone" type="tel" placeholder="09xx xxx xxx" required />
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label>ĐỊA CHỈ EMAIL</label>
                <div class="input-wrapper">
                  <span class="material-symbols-outlined input-icon">mail</span>
                  <input v-model="currentStudent.email" type="email" placeholder="student@example.com" />
                </div>
              </div>

              <!-- Family & Class Section -->
              <div class="form-section-title">GIA ĐÌNH & ĐÀO TẠO</div>
              <div class="form-row">
                <div class="form-group flex-1">
                  <label>PHỤ HUYNH ĐẠI DIỆN</label>
                  <div class="input-wrapper">
                    <span class="material-symbols-outlined input-icon">escalator_warning</span>
                    <input v-model="currentStudent.parent" type="text" placeholder="Họ tên cha/mẹ..." required />
                  </div>
                </div>
                <div class="form-group flex-1">
                  <label>LỚP HỌC HIỆN TẠI</label>
                  <div class="input-wrapper">
                    <span class="material-symbols-outlined input-icon">school</span>
                    <input v-model="currentStudent.class" type="text" placeholder="Ví dụ: IELTS-101" />
                  </div>
                </div>
              </div>

              <div class="form-row">
                <div class="form-group flex-1">
                  <label>TRẠNG THÁI HỌC TẬP</label>
                  <div class="select-wrapper">
                    <select v-model="currentStudent.status">
                      <option value="Đang học">Đang học tập</option>
                      <option value="Bảo lưu">Bảo lưu kết quả</option>
                      <option value="Nghỉ học">Đã nghỉ học</option>
                    </select>
                    <span class="material-symbols-outlined expand-icon">expand_more</span>
                  </div>
                </div>
                <div class="form-group flex-1">
                  <label>MÃ HỌC VIÊN</label>
                  <div class="input-wrapper">
                    <input v-model="currentStudent.code" type="text" readonly class="readonly-input" />
                  </div>
                </div>
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn-cancel" @click="closeModal">Hủy bỏ</button>
              <button type="submit" class="btn-submit">{{ isEditing ? 'Cập nhật hồ sơ' : 'Tạo học viên' }}</button>
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

.header-actions { display: flex; gap: 12px; }

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
.code-badge { width: fit-content; font-family: monospace; font-size: 12px; font-weight: 800; padding: 4px 10px; border-radius: 6px; background-color: #eff6ff; border: 1px solid #dbeafe; color: var(--primary); }
.contact-details { display: flex; align-items: center; gap: 6px; font-size: 12px; color: var(--outline); font-weight: 600; }
.contact-details .material-symbols-outlined { font-size: 14px; }

.parent-info-cell { display: flex; flex-direction: column; gap: 2px; }
.parent-name-text { font-size: 13px; font-weight: 700; color: var(--on-surface-variant); }
.parent-sub { font-size: 11px; color: var(--outline); font-weight: 600; }

.badge { padding: 6px 14px; border-radius: 999px; font-size: 12px; font-weight: 700; }
.badge-success { background-color: #dcfce7; color: #15803d; }
.badge-warning { background-color: #fef3c7; color: #b45309; }
.badge-danger { background-color: #fee2e2; color: #b91c1c; }

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
.readonly-input { background: var(--surface-container-low) !important; color: var(--outline) !important; cursor: not-allowed; }

.select-wrapper { position: relative; display: flex; align-items: center; }
.select-wrapper select { width: 100%; appearance: none; background-color: var(--surface-container-lowest); border: 1.5px solid var(--outline-variant); padding: 14px 40px 14px 16px; border-radius: 16px; font-size: 15px; font-weight: 600; color: var(--on-surface); outline: none; cursor: pointer; }
.expand-icon { position: absolute; right: 16px; color: var(--outline); pointer-events: none; }

.modal-footer { padding: 0 40px 40px 40px; display: flex; justify-content: flex-end; gap: 16px; }
.btn-cancel { background: transparent; color: var(--outline); font-weight: 800; border: none; cursor: pointer; font-size: 15px; }
.btn-submit { background-color: var(--primary); color: white; border: none; padding: 14px 32px; border-radius: 16px; font-weight: 800; font-size: 15px; cursor: pointer; box-shadow: 0 8px 16px rgba(0, 74, 198, 0.25); transition: all 0.3s; }

/* Transitions */
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

.pagination-footer { display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; border-top: 1px solid var(--surface-container); }
.pagination { display: flex; gap: 6px; }
.page-btn { min-width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border: 1.5px solid var(--outline-variant); background-color: white; color: var(--on-surface-variant); border-radius: 10px; font-weight: 700; font-size: 14px; cursor: pointer; }
.page-btn.active { background-color: var(--primary); color: white; border-color: var(--primary); }
</style>

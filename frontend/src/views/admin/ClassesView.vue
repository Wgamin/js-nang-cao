<template>
  <div class="view-page">
    <header class="header-action">
      <div class="left">
        <h1>Quản lý Lớp học</h1>
        <p>Danh sách các lớp học hiện có trong hệ thống</p>
      </div>
      <button class="btn-primary-rounded" @click="openAddModal">
        <span class="material-symbols-outlined">add</span>
        Thêm Lớp học mới
      </button>
    </header>

    <section class="stats-bento">
      <div v-for="stat in stats" :key="stat.label" class="mini-card shadow-sm">
        <div class="card-info">
          <span class="label">{{ stat.label }}</span>
          <h2 :class="stat.accent">{{ stat.value }}</h2>
          <p class="meta" :class="stat.accent">{{ stat.meta }}</p>
        </div>
        <span class="material-symbols-outlined decor">{{ stat.icon }}</span>
      </div>
    </section>

    <section class="filter-bar shadow-sm">
      <div class="filter-left">
        <div class="search-wrapper-v2">
          <span class="material-symbols-outlined">search</span>
          <input type="text" placeholder="Tìm kiếm tên lớp..." />
        </div>
        <div class="select-wrapper-v2">
          <span class="material-symbols-outlined">filter_list</span>
          <select>
            <option>Tất cả môn học</option>
            <option v-for="s in subjects" :key="s.id" :value="s.id">{{ s.name }}</option>
          </select>
        </div>
      </div>
      <div class="filter-right">
        <span>Hiển thị {{ classes.length }} lớp</span>
      </div>
    </section>

    <div class="table-wrapper shadow-sm">
      <table class="data-table">
        <thead>
          <tr>
            <th>TÊN LỚP</th>
            <th>MÔN HỌC</th>
            <th>GIÁO VIÊN</th>
            <th>PHÒNG</th>
            <th>SĨ SỐ</th>
            <th>HỌC PHÍ</th>
            <th>TRẠNG THÁI</th>
            <th class="actions"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="cls in classes" :key="cls.id">
            <td>
              <div class="class-title" @click="viewDetail(cls)" style="cursor: pointer;">
                <strong>{{ cls.name }}</strong>
                <span>Mã: {{ cls.class_code }}</span>
              </div>
            </td>
            <td>
              <span class="badge-subject-v2">{{ getSubjectName(cls.subject_id) }}</span>
            </td>
            <td>
              <div class="teacher-cell">
                <div class="mini-avatar">{{ getTeacher(cls.teacher_id).initials }}</div>
                <span>{{ getTeacher(cls.teacher_id).name }}</span>
              </div>
            </td>
            <td><span class="txt-room-v2">{{ getRoomCode(cls.room_id) }}</span></td>
            <td>
              <div class="capacity-indicator">
                <span :class="{ 'text-danger': cls.current_students >= cls.max_students }">
                  {{ cls.current_students }}/{{ cls.max_students }}
                </span>
                <div class="mini-progress">
                  <div class="bar" :style="{ width: (cls.current_students / cls.max_students * 100) + '%' }"></div>
                </div>
              </div>
            </td>
            <td><span class="txt-price-v2">{{ formatPrice(cls.default_fee) }}đ</span></td>
            <td>
              <span class="badge-status-v2" :class="cls.status">
                <i class="dot"></i> {{ cls.status === 'open' ? 'Đang mở' : cls.status === 'planned' ? 'Sắp mở' : 'Đã đóng' }}
              </span>
            </td>
            <td class="actions">
              <div class="btn-group-v2">
                <button class="icon-btn-v2" title="Chi tiết" @click="viewDetail(cls)">
                  <span class="material-symbols-outlined">visibility</span>
                </button>
                <button class="icon-btn-v2" title="Sửa" @click="openEditModal(cls)">
                  <span class="material-symbols-outlined">edit</span>
                </button>
                <button class="icon-btn-v2 danger" title="Xóa" @click="confirmDelete(cls)">
                  <span class="material-symbols-outlined">delete</span>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal: Thêm/Sửa Lớp Học -->
    <Transition name="modal">
      <div v-if="isModalOpen" class="modal-overlay" @click.self="isModalOpen = false">
        <div class="modal-content premium-modal-v3 relative">
          <button class="close-btn-absolute" @click="isModalOpen = false">
            <span class="material-symbols-outlined">close</span>
          </button>

          <div class="modal-header-v3">
            <h3>{{ isEditing ? 'Cập nhật Lớp học' : 'Thêm lớp học mới' }}</h3>
            <p>Cung cấp thông tin chi tiết để khởi tạo lớp học trong hệ thống.</p>
          </div>

          <form @submit.prevent="saveClass" class="modal-form-v3">
            <div class="form-grid-v3">
              <!-- Row 1: Tên lớp -->
              <div class="form-group-v3 full-width">
                <label>Tên lớp học</label>
                <input v-model="form.name" type="text" placeholder="Ví dụ: IELTS Foundation - T8/2023" required />
              </div>

              <!-- Row 2: Môn học & Giáo viên -->
              <div class="form-group-v3">
                <label>Chọn Môn học</label>
                <select v-model="form.subject_id" required>
                  <option value="" disabled>Chọn môn học...</option>
                  <option v-for="s in subjects" :key="s.id" :value="s.id">{{ s.name }}</option>
                </select>
              </div>
              <div class="form-group-v3">
                <label>Giáo viên giảng dạy</label>
                <select v-model="form.teacher_id" required>
                  <option value="" disabled>Chọn giáo viên...</option>
                  <option v-for="t in teachers" :key="t.id" :value="t.id">{{ t.name }}</option>
                </select>
              </div>

              <!-- Row 3: Phòng học & Trạng thái -->
              <div class="form-group-v3">
                <label>Phòng học</label>
                <select v-model="form.room_id" required>
                  <option value="" disabled>Chọn phòng học...</option>
                  <option v-for="r in rooms" :key="r.id" :value="r.id">{{ r.code }}</option>
                </select>
              </div>
              <div class="form-group-v3">
                <label>Trạng thái lớp</label>
                <div class="radio-group-v3">
                  <label class="radio-label-v3">
                    <input type="radio" v-model="form.status" value="planned" />
                    <span>Planned</span>
                  </label>
                  <label class="radio-label-v3">
                    <input type="radio" v-model="form.status" value="open" />
                    <span>Open</span>
                  </label>
                  <label class="radio-label-v3">
                    <input type="radio" v-model="form.status" value="closed" />
                    <span>Closed</span>
                  </label>
                </div>
              </div>

              <!-- Row 4: Học phí & Sĩ số -->
              <div class="form-group-v3">
                <label>Thiết lập Học phí (VNĐ)</label>
                <div class="input-with-icon">
                  <span class="material-symbols-outlined">payments</span>
                  <input v-model.number="form.default_fee" type="number" />
                </div>
              </div>
              <div class="form-group-v3">
                <label>Sĩ số tối đa</label>
                <div class="input-with-icon">
                  <span class="material-symbols-outlined">groups</span>
                  <input v-model.number="form.max_students" type="number" />
                </div>
              </div>

              <!-- Row 5: Ngày bắt đầu & Kết thúc -->
              <div class="form-group-v3">
                <label>Ngày khai giảng</label>
                <input v-model="form.start_date" type="date" />
              </div>
              <div class="form-group-v3">
                <label>Ngày kết thúc dự kiến</label>
                <input v-model="form.end_date" type="date" />
              </div>
            </div>

            <div class="modal-footer-v3">
              <button type="button" class="btn-text-v3" @click="isModalOpen = false">Hủy bỏ</button>
              <button type="submit" class="btn-submit-v3">Lưu thông tin</button>
            </div>
          </form>
        </div>
      </div>
    </Transition>

    <!-- Modal: Chi tiết Lớp học & Danh sách học viên -->
    <Transition name="modal">
      <div v-if="isDetailModalOpen" class="modal-overlay" @click.self="isDetailModalOpen = false">
        <div class="modal-content premium-modal-v3 relative detail-modal">
          <button class="close-btn-absolute" @click="isDetailModalOpen = false">
            <span class="material-symbols-outlined">close</span>
          </button>

          <div class="modal-header-v3">
            <h3>{{ selectedClass?.name }}</h3>
            <p>Danh sách học viên đang theo học tại lớp này.</p>
          </div>

          <div class="detail-body-v3">
            <div class="class-info-summary shadow-sm">
              <div class="info-item">
                <span class="label">Môn học:</span>
                <strong>{{ getSubjectName(selectedClass?.subject_id) }}</strong>
              </div>
              <div class="info-item">
                <span class="label">Giáo viên:</span>
                <strong>{{ getTeacher(selectedClass?.teacher_id).name }}</strong>
              </div>
              <div class="info-item">
                <span class="label">Sĩ số:</span>
                <strong :class="{ 'text-danger': selectedClass?.current_students >= selectedClass?.max_students }">
                  {{ selectedClass?.current_students }} / {{ selectedClass?.max_students }}
                </strong>
              </div>
            </div>

            <div class="student-list-container">
              <table class="simple-table">
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
                      <div class="student-item">
                        <div class="mini-avatar blue">{{ student.full_name[0] }}</div>
                        <span>{{ student.full_name }}</span>
                      </div>
                    </td>
                    <td><span class="code-tag">{{ student.student_code }}</span></td>
                    <td class="text-right">
                      <div class="transfer-box">
                        <select @change="e => transferStudent(student.id, parseInt(e.target.value))" class="select-sm">
                          <option value="" selected disabled>Chuyển sang...</option>
                          <option v-for="c in classes.filter(c => c.id !== selectedClass.id)" :key="c.id" :value="c.id">
                            {{ c.name }}
                          </option>
                        </select>
                      </div>
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

    <!-- Modal: Xác nhận xóa lớp học -->
    <Transition name="modal">
      <div v-if="isDeleteModalOpen" class="modal-overlay" @click.self="isDeleteModalOpen = false">
        <div class="modal-content delete-confirm-modal">
          <div class="delete-warning-icon">
            <span class="material-symbols-outlined" style="font-size:40px;color:#ef4444;">warning</span>
          </div>
          <h3 class="delete-title">Xác nhận xóa lớp học</h3>
          <p class="delete-subtitle">
            Hành động này <strong style="color:#ef4444;">không thể hoàn tác</strong>. Bạn có chắc chắn muốn xóa lớp học:
          </p>
          <div class="delete-class-card">
            <span class="material-symbols-outlined" style="color:#64748b;">school</span>
            <div>
              <span class="delete-card-label">TÊN LỚP HỌC</span>
              <strong class="delete-card-name">{{ classToDelete?.name }}</strong>
            </div>
          </div>
          <p class="delete-warning-note">
            Tất cả dữ liệu liên quan đến học viên, điểm số và lịch trình của lớp học này sẽ bị xóa vĩnh viễn khỏi hệ
            thống.
          </p>
          <div class="delete-footer">
            <button class="btn-cancel-v3" @click="isDeleteModalOpen = false">Hủy bỏ</button>
            <button class="btn-danger-v3" @click="executeDelete">Xác nhận xóa</button>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

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
const stats = computed(() => [
  { label: 'Tổng số lớp', value: classes.value.length, meta: '+2 trong tháng', accent: 'primary', icon: 'hub' },
  { label: 'Lớp đang mở', value: classes.value.filter(c => c.status === 'open').length, meta: 'Hoạt động', accent: 'neutral', icon: 'door_open' },
  { label: 'Sắp khai giảng', value: classes.value.filter(c => c.status === 'planned').length, meta: 'Cần giáo viên', accent: 'warning', icon: 'calendar_today' },
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
    // 1. Decr current class count
    const oldClass = classes.value.find(c => c.id === student.class_id)
    if (oldClass) oldClass.current_students--

    // 2. Incr new class count
    const newClass = classes.value.find(c => c.id === newClassId)
    if (newClass) newClass.current_students++

    // 3. Update student
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
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

.view-page {
  font-family: 'Inter', sans-serif;
  color: #1e293b;
  padding: 32px;
  background: #fdfdfd;
  min-height: 100vh;
}

.shadow-sm {
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.relative {
  position: relative;
}

/* ===== HEADER ===== */
.header-action {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 32px;
}

.header-action h1 {
  font-size: 28px;
  font-weight: 800;
  color: #1e293b;
}

.header-action p {
  color: #64748b;
  font-size: 14px;
  margin-top: 4px;
}

.btn-primary-rounded {
  background: #2563eb;
  color: white;
  border: none;
  padding: 12px 24px;
  border-radius: 12px;
  font-weight: 700;
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
  transition: 0.2s;
}

.btn-primary-rounded:hover {
  background: #1d4ed8;
  transform: translateY(-1px);
}

/* ===== BENTO STATS ===== */
.stats-bento {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
  margin-bottom: 32px;
}

.mini-card {
  background: #ffffff;
  padding: 24px;
  border-radius: 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  position: relative;
  overflow: hidden;
  border: 1px solid #f1f5f9;
}

.mini-card .label {
  font-size: 12px;
  font-weight: 700;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.mini-card h2 {
  font-size: 32px;
  font-weight: 800;
  margin: 4px 0;
}

.mini-card .meta {
  font-size: 12px;
  font-weight: 600;
}

.mini-card h2.primary {
  color: #2563eb;
}

.mini-card h2.warning {
  color: #f59e0b;
}

.mini-card .decor {
  position: absolute;
  right: -10px;
  font-size: 64px;
  opacity: 0.05;
  color: #64748b;
}

/* ===== FILTER BAR ===== */
.filter-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #ffffff;
  padding: 16px 24px;
  border-radius: 16px;
  margin-bottom: 24px;
  border: 1px solid #f1f5f9;
}

.filter-left {
  display: flex;
  gap: 16px;
}

.search-wrapper-v2 {
  background: #f8fafc;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  padding: 8px 16px;
  display: flex;
  align-items: center;
  gap: 12px;
  width: 280px;
}

.search-wrapper-v2 input {
  background: transparent;
  border: none;
  outline: none;
  font-size: 14px;
  font-weight: 500;
}

.select-wrapper-v2 {
  background: #f8fafc;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  padding: 8px 16px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.select-wrapper-v2 select {
  background: transparent;
  border: none;
  outline: none;
  font-size: 14px;
  font-weight: 600;
  color: #475569;
}

.filter-right span {
  font-size: 13px;
  font-weight: 700;
  color: #94a3b8;
}

/* ===== TABLE ===== */
.table-wrapper {
  background: #ffffff;
  border-radius: 24px;
  overflow: hidden;
  border: 1px solid #f1f5f9;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
}

.data-table th {
  background: #f8fafc;
  text-align: left;
  padding: 16px 24px;
  font-size: 11px;
  font-weight: 800;
  color: #94a3b8;
  text-transform: uppercase;
  letter-spacing: 0.1em;
}

.data-table td {
  padding: 16px 24px;
  border-top: 1px solid #f8fafc;
  vertical-align: middle;
}

.class-title strong {
  display: block;
  font-size: 14px;
  font-weight: 700;
  color: #1e293b;
}

.class-title span {
  font-size: 11px;
  color: #94a3b8;
  font-weight: 700;
}

.badge-subject-v2 {
  background: #eef2ff;
  color: #4338ca;
  padding: 4px 12px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 700;
}

.teacher-cell {
  display: flex;
  align-items: center;
  gap: 12px;
}

.mini-avatar {
  width: 32px;
  height: 32px;
  background: #f1f5f9;
  color: #64748b;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 12px;
}

.txt-room-v2 {
  font-size: 13px;
  font-weight: 700;
  color: #64748b;
}

.capacity-indicator {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.capacity-indicator span {
  font-size: 13px;
  font-weight: 700;
}

.mini-progress {
  height: 4px;
  width: 60px;
  background: #f1f5f9;
  border-radius: 2px;
}

.mini-progress .bar {
  height: 100%;
  background: #2563eb;
  border-radius: 2px;
}

.txt-price-v2 {
  font-size: 14px;
  font-weight: 800;
  color: #2563eb;
}

.badge-status-v2 {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 11px;
  font-weight: 800;
}

.badge-status-v2.open {
  background: #dcfce7;
  color: #15803d;
}

.badge-status-v2.planned {
  background: #fef3c7;
  color: #b45309;
}

.badge-status-v2.closed {
  background: #f1f5f9;
  color: #475569;
}

.badge-status-v2 .dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: currentColor;
}

.btn-group-v2 {
  display: flex;
  gap: 8px;
}

.icon-btn-v2 {
  width: 32px;
  height: 32px;
  border: none;
  background: #f8fafc;
  color: #64748b;
  border-radius: 8px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: 0.2s;
}

.icon-btn-v2:hover {
  background: #eff6ff;
  color: #2563eb;
}

.icon-btn-v2.danger:hover {
  background: #fef2f2;
  color: #ef4444;
}

/* ===== MODALS (v3) ===== */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(6px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
}

.premium-modal-v3 {
  max-width: 760px;
  background: #ffffff;
  border-radius: 32px;
  overflow: hidden;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  margin: 20px;
}

.detail-modal {
  max-width: 600px;
}

.close-btn-absolute {
  position: absolute;
  top: 24px;
  right: 24px;
  border: none;
  background: transparent;
  color: #94a3b8;
  cursor: pointer;
  transition: 0.2s;
}

.close-btn-absolute:hover {
  color: #1e293b;
  transform: rotate(90deg);
}

.modal-header-v3 {
  padding: 32px 32px 0 32px;
}

.modal-header-v3 h3 {
  font-size: 22px;
  font-weight: 800;
  color: #1e293b;
  margin-bottom: 8px;
}

.modal-header-v3 p {
  font-size: 14px;
  color: #64748b;
  font-weight: 500;
}

.modal-form-v3 {
  padding: 32px;
}

.form-grid-v3 {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

.full-width {
  grid-column: span 2;
}

.form-group-v3 {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-group-v3 label {
  font-size: 12px;
  font-weight: 800;
  color: #1e293b;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.form-group-v3 input,
.form-group-v3 select {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  padding: 12px 16px;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 500;
  outline: none;
}

.input-with-icon {
  position: relative;
  display: flex;
  align-items: center;
}

.input-with-icon .material-symbols-outlined {
  position: absolute;
  left: 12px;
  font-size: 20px;
  color: #94a3b8;
}

.input-with-icon input {
  padding-left: 44px;
  width: 100%;
}

.radio-group-v3 {
  display: flex;
  gap: 16px;
  padding: 8px 0;
}

.radio-label-v3 {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
}

.modal-footer-v3 {
  grid-column: span 2;
  display: flex;
  justify-content: flex-end;
  gap: 16px;
  margin-top: 32px;
}

.btn-submit-v3 {
  background: #2563eb;
  color: white;
  border: none;
  padding: 14px 28px;
  border-radius: 16px;
  font-weight: 700;
  cursor: pointer;
}

.btn-text-v3 {
  background: transparent;
  border: none;
  color: #64748b;
  font-weight: 700;
  cursor: pointer;
}

/* ===== DETAIL VIEW ===== */
.detail-body-v3 {
  padding: 32px;
}

.class-info-summary {
  background: #f8fafc;
  border-radius: 20px;
  padding: 20px;
  display: flex;
  justify-content: space-around;
  margin-bottom: 32px;
  border: 1px solid #f1f5f9;
}

.info-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
}

.info-item .label {
  font-size: 11px;
  font-weight: 800;
  color: #94a3b8;
  text-transform: uppercase;
}

.info-item strong {
  font-size: 14px;
  color: #1e293b;
}

.student-list-container {
  max-height: 300px;
  overflow-y: auto;
}

.simple-table {
  width: 100%;
  border-collapse: collapse;
}

.simple-table th {
  text-align: left;
  font-size: 11px;
  color: #94a3b8;
  padding: 0 0 12px 0;
}

.simple-table td {
  padding: 12px 0;
  border-top: 1px solid #f1f5f9;
}

.student-item {
  display: flex;
  align-items: center;
  gap: 12px;
}

.mini-avatar.blue {
  background: #eff6ff;
  color: #2563eb;
}

.student-item span {
  font-size: 14px;
  font-weight: 600;
  color: #1e293b;
}

.transfer-box {
  display: flex;
  justify-content: flex-end;
}

.select-sm {
  background: #f1f5f9;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 6px 12px;
  font-size: 12px;
  font-weight: 700;
  color: #475569;
  outline: none;
  cursor: pointer;
  transition: 0.2s;
}

.select-sm:hover {
  border-color: #cbd5e1;
  background: #e2e8f0;
}

.code-tag {
  font-family: monospace;
  font-size: 12px;
  font-weight: 700;
  color: #6366f1;
  background: #eef2ff;
  padding: 2px 8px;
  border-radius: 6px;
}

.btn-secondary-sm {
  background: #f1f5f9;
  color: #475569;
  border: none;
  padding: 6px 12px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
}

/* ===== DELETE CONFIRM MODAL ===== */
.delete-confirm-modal {
  max-width: 480px;
  background: #ffffff;
  border-radius: 28px;
  padding: 40px 36px 32px;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  text-align: center;
  margin: 20px;
}

.delete-warning-icon {
  width: 72px;
  height: 72px;
  background: #fef2f2;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 20px;
}

.delete-title {
  font-size: 20px;
  font-weight: 800;
  color: #1e293b;
  margin-bottom: 10px;
}

.delete-subtitle {
  font-size: 14px;
  color: #64748b;
  line-height: 1.6;
  margin-bottom: 20px;
}

.delete-class-card {
  display: flex;
  align-items: center;
  gap: 14px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-left: 4px solid #ef4444;
  border-radius: 14px;
  padding: 16px 20px;
  text-align: left;
  margin-bottom: 20px;
}

.delete-card-label {
  font-size: 10px;
  font-weight: 800;
  color: #94a3b8;
  letter-spacing: 0.08em;
  display: block;
  text-transform: uppercase;
  margin-bottom: 4px;
}

.delete-card-name {
  font-size: 15px;
  font-weight: 800;
  color: #1e293b;
}

.delete-warning-note {
  font-size: 12px;
  color: #94a3b8;
  font-style: italic;
  line-height: 1.6;
  margin-bottom: 28px;
}

.delete-footer {
  display: flex;
  gap: 12px;
  justify-content: center;
}

.btn-cancel-v3 {
  padding: 12px 28px;
  background: #f1f5f9;
  border: none;
  border-radius: 14px;
  font-size: 14px;
  font-weight: 700;
  color: #475569;
  cursor: pointer;
  transition: 0.2s;
}

.btn-cancel-v3:hover {
  background: #e2e8f0;
}

.btn-danger-v3 {
  padding: 12px 28px;
  background: #ef4444;
  border: none;
  border-radius: 14px;
  font-size: 14px;
  font-weight: 700;
  color: #ffffff;
  cursor: pointer;
  box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
  transition: 0.2s;
}

.btn-danger-v3:hover {
  background: #dc2626;
  transform: translateY(-1px);
}

.empty-state {
  text-align: center;
  color: #94a3b8;
  padding: 40px 0;
  font-size: 14px;
}

/* Animations */
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-content {
  transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.modal-enter-from .modal-content {
  transform: scale(0.9) translateY(20px);
}
</style>

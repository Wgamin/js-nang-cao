<script setup>
import { ref } from 'vue'

// --- Mock Data ---
const classes = [
  { id: 1, name: 'IELTS Foundation 01', code: 'SF-IELTS-001' },
  { id: 2, name: 'React.js Advanced', code: 'SF-DEV-202' },
]

const teachers = [
  { id: 1, name: 'Nguyễn Minh Anh' },
  { id: 2, name: 'Trần Văn Bình' },
]

const rooms = [
  { id: 1, code: 'P.302' },
  { id: 2, code: 'P.105' },
  { id: 3, code: 'Lab.01' },
]

const schedules = ref([
  { id: 1, date: '2024-04-16', time: '08:00 - 09:30', class: 'IELTS Foundation 01', subject: 'Tiếng Anh', teacher: 'Nguyễn Minh Anh', room: 'P.302', status: 'completed', type: 'regular' },
  { id: 2, date: '2024-04-16', time: '10:00 - 11:30', class: 'React.js Advanced', subject: 'Lập trình', teacher: 'Trần Văn Bình', room: 'Lab.01', status: 'ongoing', type: 'regular' },
  { id: 3, date: '2024-04-16', time: '14:00 - 16:00', class: 'IELTS Foundation 01', subject: 'Tiếng Anh', teacher: 'Nguyễn Minh Anh', room: 'P.302', status: 'upcoming', type: 'makeup' },
  { id: 4, date: '2024-04-16', time: '19:30 - 21:00', class: 'React.js Advanced', subject: 'Lập trình', teacher: 'Trần Văn Bình', room: 'Lab.01', status: 'cancelled', type: 'regular' },
])

// --- State ---
const isTemplateModalOpen = ref(false)
const templateForm = ref({
  class_id: '',
  sessions: [
    { day: 'Wednesday', start: '14:00', end: '16:00' },
    { day: 'Friday', start: '09:30', end: '11:30' }
  ]
})

const daysOfWeek = [
  { val: 'Monday', label: 'Thứ Hai' },
  { val: 'Tuesday', label: 'Thứ Ba' },
  { val: 'Wednesday', label: 'Thứ Tư' },
  { val: 'Thursday', label: 'Thứ Năm' },
  { val: 'Friday', label: 'Thứ Sáu' },
  { val: 'Saturday', label: 'Thứ Bảy' },
  { val: 'Sunday', label: 'Chủ Nhật' },
]

// --- Methods ---
function addSession() {
  templateForm.value.sessions.push({ day: 'Monday', start: '08:00', end: '10:00' })
}

function removeSession(index) {
  templateForm.value.sessions.splice(index, 1)
}

function saveTemplate() {
  alert('Đã lưu lịch học mẫu cho lớp!')
  isTemplateModalOpen.value = false
}

function markAbsence(id) {
  const sch = schedules.value.find(s => s.id === id)
  if (sch) {
    sch.status = 'cancelled'
    alert('Đã đánh dấu nghỉ học cho ca này.')
  }
}

function markMakeup(id) {
  const sch = schedules.value.find(s => s.id === id)
  if (sch) {
    sch.type = 'makeup'
    alert('Đã chuyển ca này thành ca học bù.')
  }
}

function getStatusClass(status) {
  if (status === 'completed') return 'badge-success'
  if (status === 'ongoing') return 'badge-warning'
  if (status === 'upcoming') return 'badge-info'
  return 'badge-danger'
}
</script>

<template>
  <div class="admin-page">
    <div class="headers">
      <div class="header-left">
        <h1 class="title">Quản lý Lịch học</h1>
        <p class="subtitle">Sắp xếp thời khóa biểu và theo dõi vận hành hàng tuần</p>
      </div>
      <button class="btn btn-primary" @click="isTemplateModalOpen = true">
        <span class="material-symbols-outlined">calendar_add_on</span>
        Thiết lập Lịch mẫu
      </button>
    </div>

    <!-- Main Card -->
    <div class="content-box">
      <div class="toolbar">
        <div class="filters" style="flex: 1;">
          <div class="date-display">
            <span class="material-symbols-outlined">today</span>
            <strong>Hôm nay, 16/04/2024</strong>
          </div>
          <div class="select-wrapper">
            <select class="select-filter">
              <option>Tất cả giáo viên</option>
              <option v-for="t in teachers" :key="t.id">{{ t.name }}</option>
            </select>
            <span class="material-symbols-outlined select-arrow">expand_more</span>
          </div>
        </div>
        
        <div class="filters">
          <button class="btn btn-secondary">
            <span class="material-symbols-outlined">grid_view</span>
            Xem Thời khóa biểu
          </button>
        </div>
      </div>

      <!-- Table Section -->
      <div class="table-container">
        <table class="user-table">
          <thead>
            <tr>
              <th style="width: 15%">Thời gian</th>
              <th style="width: 25%">Lớp học</th>
              <th style="width: 20%">Giáo viên</th>
              <th style="width: 10%">Phòng</th>
              <th style="width: 10%">Loại ca</th>
              <th style="width: 10%">Trạng thái</th>
              <th class="text-right">Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="sch in schedules" :key="sch.id" class="table-row">
              <td>
                <div class="time-block">
                  <span class="material-symbols-outlined">schedule</span>
                  <strong>{{ sch.time }}</strong>
                </div>
              </td>
              <td>
                <div class="class-info">
                  <strong class="user-full-name">{{ sch.class }}</strong>
                  <span class="user-email-text">Môn: {{ sch.subject }}</span>
                </div>
              </td>
              <td>
                <div class="user-info">
                  <span class="material-symbols-outlined" style="color: #94a3b8; font-size: 20px;">person</span>
                  <span class="user-full-name">{{ sch.teacher }}</span>
                </div>
              </td>
              <td>
                <span class="room-tag">{{ sch.room }}</span>
              </td>
              <td>
                <span class="type-badge" :class="sch.type">
                  {{ sch.type === 'regular' ? 'Chính thức' : 'Học bù' }}
                </span>
              </td>
              <td>
                <span class="badge" :class="getStatusClass(sch.status)">
                  {{ sch.status === 'completed' ? 'Hoàn thành' : 
                     sch.status === 'ongoing' ? 'Đang học' : 
                     sch.status === 'upcoming' ? 'Sắp diễn ra' : 'Đã hủy/Nghỉ' }}
                </span>
              </td>
              <td class="text-right">
                <div class="actions">
                  <button class="action-btn" title="Điểm danh"><span class="material-symbols-outlined">fact_check</span></button>
                  <button class="action-btn" @click="markMakeup(sch.id)" title="Học bù"><span class="material-symbols-outlined" style="color: #f59e0b;">edit_calendar</span></button>
                  <button class="action-btn btn-delete" @click="markAbsence(sch.id)" title="Nghỉ học"><span class="material-symbols-outlined">event_busy</span></button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="table-footer">
        <p>Hiển thị <strong>{{ schedules.length }}</strong> lịch học</p>
      </div>
    </div>

    <!-- Modal Form -->
    <Transition name="modal-fade">
      <div v-if="isTemplateModalOpen" class="modal-backdrop" @click.self="isTemplateModalOpen = false">
        <div class="modal-box">
          <div class="modal-header">
            <div class="modal-header-text">
              <h2 class="modal-title">Thiết lập Lịch học mẫu</h2>
              <p class="modal-subtitle">Cấu hình thời gian học định kỳ cho lớp học.</p>
            </div>
            <button class="btn-close-minimal" @click="isTemplateModalOpen = false" title="Đóng">
              <span class="material-symbols-outlined">close</span>
            </button>
          </div>

          <div class="modal-body">
            <div class="form-grid">
              <div class="form-item span-2">
                <label class="form-label">Chọn lớp học</label>
                <div class="select-container">
                  <select v-model="templateForm.class_id" required class="form-select">
                    <option value="" disabled>Chọn một lớp học...</option>
                    <option v-for="c in classes" :key="c.id" :value="c.id">{{ c.name }}</option>
                  </select>
                  <span class="material-symbols-outlined select-icon-minimal">expand_more</span>
                </div>
              </div>
            </div>

            <div class="section-title-box">
              <label class="form-label mb-0">Khung giờ học</label>
              <span class="badge-info-sm">Đang áp dụng {{ templateForm.sessions.length }} buổi/tuần</span>
            </div>

            <div class="sessions-list">
              <div v-for="(session, index) in templateForm.sessions" :key="index" class="session-card">
                <div class="session-input-grid">
                  <div class="input-v3">
                    <label class="form-label" style="font-size: 11px;">Thứ trong tuần</label>
                    <div class="select-container">
                      <select v-model="session.day" class="form-select" style="padding: 8px 12px; font-size: 13px;">
                        <option v-for="d in daysOfWeek" :key="d.val" :value="d.val">{{ d.label }}</option>
                      </select>
                      <span class="material-symbols-outlined select-icon-minimal" style="font-size: 16px;">expand_more</span>
                    </div>
                  </div>
                  <div class="input-v3">
                    <label class="form-label" style="font-size: 11px;">Bắt đầu</label>
                    <div class="time-input-wrapper">
                      <input type="time" v-model="session.start" class="form-input" style="padding: 8px 12px; font-size: 13px;" />
                      <span class="material-symbols-outlined">schedule</span>
                    </div>
                  </div>
                  <div class="input-v3">
                    <label class="form-label" style="font-size: 11px;">Kết thúc</label>
                    <div class="time-input-wrapper">
                      <input type="time" v-model="session.end" class="form-input" style="padding: 8px 12px; font-size: 13px;" />
                      <span class="material-symbols-outlined">schedule</span>
                    </div>
                  </div>
                  <button class="btn-delete-session" @click="removeSession(index)">
                    <span class="material-symbols-outlined">delete</span>
                  </button>
                </div>
              </div>

              <button class="btn-add-session" @click="addSession">
                <span class="material-symbols-outlined">add_circle</span>
                Thêm khung giờ
              </button>
            </div>

            <div class="info-notice">
              <span class="material-symbols-outlined">info</span>
              <p>Lưu ý: Lịch học mẫu sẽ được áp dụng cho toàn bộ các buổi học trong khóa học hiện tại của lớp đã chọn. Bạn có thể điều chỉnh từng buổi ở sau lịch học.</p>
            </div>
          </div>

          <div class="modal-footer-minimal">
            <button class="btn btn-simple" @click="isTemplateModalOpen = false">Hủy bỏ</button>
            <button class="btn btn-solid-primary" @click="saveTemplate">Lưu thông tin</button>
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

/* Content Box */
.content-box { background-color: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05); overflow: hidden; }

/* Toolbar */
.toolbar { padding: 20px 24px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; gap: 16px; flex-wrap: wrap; }
.filters { display: flex; gap: 12px; align-items: center; }
.select-wrapper { position: relative; min-width: 180px; }
.select-filter { width: 100%; appearance: none; padding: 10px 36px 10px 14px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; font-weight: 500; color: #475569; background-color: #ffffff; cursor: pointer; outline: none; }
.select-arrow { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; }

.date-display { display: flex; align-items: center; gap: 8px; background: #f8fafc; padding: 10px 16px; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 14px; }
.date-display .material-symbols-outlined { color: #2563eb; font-size: 20px; }

/* Table */
.table-container { min-height: 300px; position: relative; }
.user-table { width: 100%; border-collapse: collapse; }
.user-table th { text-align: left; padding: 14px 24px; font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.025em; background-color: #f8fafc; border-bottom: 1px solid #f1f5f9; }
.table-row { transition: background-color 0.2s ease; }
.table-row:hover { background-color: #f8fafc; }
.user-table td { padding: 16px 24px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }

/* Customs for Schedules */
.time-block { display: flex; align-items: center; gap: 8px; color: #1e293b; }
.time-block span { color: #2563eb; font-size: 20px; }

.class-info { display: flex; flex-direction: column; gap: 2px; }
.user-full-name { font-size: 14px; font-weight: 600; color: #1e293b; margin: 0; }
.user-email-text { font-size: 12px; color: #64748b; margin: 0; }
.user-info { display: flex; align-items: center; gap: 8px; }

.room-tag { background: #f1f5f9; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 700; color: #475569; }

.type-badge { font-size: 12px; font-weight: 700; padding: 4px 10px; border-radius: 6px; }
.type-badge.regular { background: #eff6ff; color: #1d4ed8; }
.type-badge.makeup { background: #fffbeb; color: #b45309; }

/* Badges */
.badge { display: inline-flex; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; }
.badge-success { background-color: #ecfdf5; color: #10b981; }
.badge-warning { background-color: #fffbeb; color: #f59e0b; }
.badge-danger { background-color: #fef2f2; color: #ef4444; }
.badge-info { background-color: #eff6ff; color: #3b82f6; }

/* Actions */
.actions { display: flex; justify-content: flex-end; gap: 4px; }
.action-btn { width: 34px; height: 34px; border-radius: 6px; border: 1px solid transparent; background-color: transparent; color: #64748b; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; }
.action-btn:hover { background-color: #f1f5f9; color: #2563eb; }
.btn-delete:hover { color: #dc2626; background-color: #fef2f2; }
.action-btn .material-symbols-outlined { font-size: 20px; }
.text-right { text-align: right; }

/* Empty state & Footer */
.table-footer { padding: 16px 24px; font-size: 13px; color: #64748b; }

/* Modal Styles */
.modal-backdrop { position: fixed; inset: 0; background-color: rgba(15, 23, 42, 0.4); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 20px; }
.modal-box { background-color: #ffffff; width: 100%; max-width: 600px; border-radius: 16px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); border: 1px solid #f1f5f9; animation: modalIn 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
@keyframes modalIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
.modal-header { padding: 24px 28px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: flex-start; }
.modal-title { font-size: 18px; font-weight: 700; color: #0f172a; margin: 0 0 4px 0; }
.modal-subtitle { font-size: 13px; color: #64748b; margin: 0; }
.btn-close-minimal { background: transparent; border: none; color: #94a3b8; cursor: pointer; padding: 4px; border-radius: 6px; transition: all 0.2s; }
.btn-close-minimal:hover { background-color: #f1f5f9; color: #475569; }

/* Modal Body */
.modal-body { padding: 28px; }
.form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 24px; }
.form-item.span-2 { grid-column: span 2; }
.form-label { display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 8px; }
.mb-0 { margin-bottom: 0 !important; }
.form-input, .form-select { width: 100%; padding: 10px 14px; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; color: #0f172a; outline: none; transition: all 0.2s; }
.form-input:focus, .form-select:focus { background-color: #ffffff; border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }

.select-container { position: relative; }
.select-icon-minimal { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; font-size: 20px; }
.form-select { appearance: none; padding-right: 40px; }

/* Session Setup Custom */
.section-title-box { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
.badge-info-sm { background: #eff6ff; color: #2563eb; font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 6px; border: 1px solid #dbeafe; }

.session-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; margin-bottom: 12px; border-left: 4px solid #2563eb; }
.session-input-grid { display: grid; grid-template-columns: 1.5fr 1fr 1fr auto; gap: 12px; align-items: flex-end; }
.time-input-wrapper { position: relative; display: flex; align-items: center; }
.time-input-wrapper span { position: absolute; right: 8px; font-size: 16px; color: #94a3b8; pointer-events: none; }

.btn-delete-session { background: transparent; border: none; color: #ef4444; cursor: pointer; padding: 8px; border-radius: 6px; display: flex; align-items: center; justify-content: center; }
.btn-delete-session:hover { background: #fef2f2; }

.btn-add-session { width: 100%; padding: 12px; border: 1px dashed #cbd5e1; background: transparent; border-radius: 8px; display: flex; align-items: center; justify-content: center; gap: 8px; font-weight: 600; color: #64748b; cursor: pointer; transition: 0.2s; margin-top: 12px; }
.btn-add-session:hover { background: #f8fafc; border-color: #94a3b8; color: #1e293b; }

.info-notice { margin-top: 24px; background: #eff6ff; padding: 16px; border-radius: 8px; display: flex; gap: 12px; border: 1px solid #dbeafe; }
.info-notice span { color: #2563eb; }
.info-notice p { font-size: 13px; color: #1e3a8a; line-height: 1.5; font-weight: 500; margin: 0; }

/* Modal Footer */
.modal-footer-minimal { padding: 20px 28px; background-color: #f8fafc; border-top: 1px solid #f1f5f9; border-radius: 0 0 16px 16px; display: flex; justify-content: flex-end; gap: 12px; }
.btn-simple { background-color: transparent; border: 1px solid #e2e8f0; color: #475569; font-weight: 600; padding: 10px 18px; border-radius: 8px; cursor: pointer;}
.btn-simple:hover { background-color: #f1f5f9; border-color: #cbd5e1; }
.btn-solid-primary { background-color: #2563eb; color: #ffffff; border: none; font-weight: 600; padding: 10px 18px; border-radius: 8px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); cursor: pointer;}
.btn-solid-primary:hover { background-color: #1d4ed8; }

.modal-fade-enter-active, .modal-fade-leave-active { transition: opacity 0.2s ease; }
.modal-fade-enter-from, .modal-fade-leave-to { opacity: 0; }
</style>

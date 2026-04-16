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
</script>

<template>
  <div class="view-page">
    <header class="header-action">
      <div class="left">
        <h1>Quản lý Lịch học</h1>
        <p>Sắp xếp thời khóa biểu và theo dõi vận hành hàng tuần</p>
      </div>
      <button class="btn-primary-rounded" @click="isTemplateModalOpen = true">
        <span class="material-symbols-outlined">calendar_add_on</span>
        Thiết lập Lịch mẫu
      </button>
    </header>

    <section class="filter-bar shadow-sm">
      <div class="filter-left">
        <div class="date-display-v2">
          <span class="material-symbols-outlined">today</span>
          <strong>Hôm nay, 16/04/2024</strong>
        </div>
        <div class="select-wrapper-v2">
          <select>
            <option>Tất cả giáo viên</option>
            <option v-for="t in teachers" :key="t.id">{{ t.name }}</option>
          </select>
        </div>
      </div>
      <div class="filter-right">
        <button class="btn-outline-v2">
          <span class="material-symbols-outlined">grid_view</span>
          Xem Thời khóa biểu
        </button>
      </div>
    </section>

    <div class="table-wrapper shadow-sm">
      <table class="data-table">
        <thead>
          <tr>
            <th>THỜI GIAN</th>
            <th>LỚP HỌC</th>
            <th>GIÁO VIÊN</th>
            <th>PHÒNG</th>
            <th>LOẠI CA</th>
            <th>TRẠNG THÁI</th>
            <th class="actions"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="sch in schedules" :key="sch.id">
            <td>
              <div class="time-block-v2">
                <span class="material-symbols-outlined">schedule</span>
                <strong>{{ sch.time }}</strong>
              </div>
            </td>
            <td>
              <div class="class-info">
                <strong>{{ sch.class }}</strong>
                <span>Môn: {{ sch.subject }}</span>
              </div>
            </td>
            <td>
              <div class="teacher-cell-v2">
                <span class="material-symbols-outlined">person</span>
                <span>{{ sch.teacher }}</span>
              </div>
            </td>
            <td>
              <span class="room-tag-v2">{{ sch.room }}</span>
            </td>
            <td>
              <span class="type-badge" :class="sch.type">
                {{ sch.type === 'regular' ? 'Chính thức' : 'Học bù' }}
              </span>
            </td>
            <td>
              <span class="status-badge-v2" :class="sch.status">
                <i class="dot"></i>
                {{ sch.status === 'completed' ? 'Hoàn thành' : 
                   sch.status === 'ongoing' ? 'Đang học' : 
                   sch.status === 'upcoming' ? 'Sắp diễn ra' : 'Đã hủy/Nghỉ' }}
              </span>
            </td>
            <td class="actions">
              <div class="btn-group-v2">
                <button class="icon-btn-v2" title="Điểm danh"><span class="material-symbols-outlined">fact_check</span></button>
                <button class="icon-btn-v2" @click="markMakeup(sch.id)" title="Học bù"><span class="material-symbols-outlined text-warning">edit_calendar</span></button>
                <button class="icon-btn-v2 danger" @click="markAbsence(sch.id)" title="Nghỉ học"><span class="material-symbols-outlined">event_busy</span></button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal: Thiết lập Lịch học mẫu -->
    <Transition name="modal">
      <div v-if="isTemplateModalOpen" class="modal-overlay" @click.self="isTemplateModalOpen = false">
        <div class="modal-content premium-modal-v3 relative schedule-modal">
          <button class="close-btn-absolute" @click="isTemplateModalOpen = false">
            <span class="material-symbols-outlined">close</span>
          </button>
          
          <div class="modal-header-v3">
            <h3>Thiết lập Lịch học mẫu</h3>
            <p>Cấu hình thời gian học định kỳ cho lớp học.</p>
          </div>

          <div class="modal-body-v3">
            <div class="form-group-v3 full-width">
              <label>CHỌN LỚP HỌC</label>
              <select v-model="templateForm.class_id" required>
                <option value="" disabled>Chọn một lớp học...</option>
                <option v-for="c in classes" :key="c.id" :value="c.id">{{ c.name }}</option>
              </select>
            </div>

            <div class="section-title-box">
              <label>KHUNG GIỜ HỌC</label>
              <span class="badge-info-sm">Đang áp dụng {{ templateForm.sessions.length }} buổi/tuần</span>
            </div>

            <div class="sessions-list">
              <div v-for="(session, index) in templateForm.sessions" :key="index" class="session-card">
                <div class="session-input-grid">
                  <div class="input-v3">
                    <label>THỨ TRONG TUẦN</label>
                    <select v-model="session.day">
                      <option v-for="d in daysOfWeek" :key="d.val" :value="d.val">{{ d.label }}</option>
                    </select>
                  </div>
                  <div class="input-v3">
                    <label>BẮT ĐẦU</label>
                    <div class="time-input-wrapper">
                      <input type="time" v-model="session.start" />
                      <span class="material-symbols-outlined">schedule</span>
                    </div>
                  </div>
                  <div class="input-v3">
                    <label>KẾT THÚC</label>
                    <div class="time-input-wrapper">
                      <input type="time" v-model="session.end" />
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

          <div class="modal-footer-v3">
            <button class="btn-text-v3" @click="isTemplateModalOpen = false">Hủy bỏ</button>
            <button class="btn-submit-v3" @click="saveTemplate">Lưu thông tin</button>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>



<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

.view-page {
    font-family: 'Inter', sans-serif;
    color: #1e293b;
    padding: 32px;
    background: #fdfdfd;
    min-height: 100vh;
}

.shadow-sm { box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
.relative { position: relative; }

/* ===== HEADER ===== */
.header-action {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
}
.header-action h1 { font-size: 28px; font-weight: 800; color: #1e293b; }
.header-action p { color: #64748b; font-size: 14px; margin-top: 4px; }

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
.filter-left { display: flex; gap: 16px; align-items: center; }
.date-display-v2 {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #f8fafc;
    padding: 8px 16px;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    font-size: 14px;
}
.date-display-v2 .material-symbols-outlined { color: #2563eb; }

.select-wrapper-v2 select {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    padding: 8px 16px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
    color: #475569;
    outline: none;
}

.btn-outline-v2 {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    padding: 8px 16px;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    transition: 0.2s;
}

/* ===== TABLE ===== */
.table-wrapper {
    background: #ffffff;
    border-radius: 24px;
    overflow: hidden;
    border: 1px solid #f1f5f9;
}
.data-table { width: 100%; border-collapse: collapse; }
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
.data-table td { padding: 16px 24px; border-top: 1px solid #f8fafc; }

.time-block-v2 { display: flex; align-items: center; gap: 8px; color: #1e293b; }
.time-block-v2 span { color: #2563eb; font-size: 20px; }

.class-info strong { display: block; font-size: 14px; font-weight: 700; }
.class-info span { font-size: 12px; color: #64748b; }

.teacher-cell-v2 { display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; }
.teacher-cell-v2 span:first-child { color: #94a3b8; font-size: 20px; }

.room-tag-v2 {
    background: #f1f5f9;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 700;
    color: #475569;
}

.type-badge {
    font-size: 11px;
    font-weight: 800;
    padding: 4px 10px;
    border-radius: 8px;
    text-transform: uppercase;
}
.type-badge.regular { background: #e0f2fe; color: #0369a1; }
.type-badge.makeup { background: #fff7ed; color: #9a3412; }

.status-badge-v2 {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 11px;
    font-weight: 800;
    padding: 4px 10px;
    border-radius: 20px;
}
.status-badge-v2.completed { background: #dcfce7; color: #15803d; }
.status-badge-v2.ongoing { background: #fef3c7; color: #b45309; }
.status-badge-v2.upcoming { background: #eff6ff; color: #1d4ed8; }
.status-badge-v2.cancelled { background: #fee2e2; color: #b91c1c; }
.status-badge-v2 .dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

.btn-group-v2 { display: flex; gap: 8px; }
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
.icon-btn-v2.danger:hover { background: #fef2f2; color: #ef4444; }
.text-warning { color: #f59e0b; }

/* ===== MODAL ===== */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.schedule-modal { max-width: 600px; }

.premium-modal-v3 {
    background: #ffffff;
    border-radius: 32px;
    width: 100%;
    box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
}

.close-btn-absolute {
    position: absolute;
    top: 24px;
    right: 24px;
    border: none;
    background: transparent;
    color: #94a3b8;
    cursor: pointer;
}

.modal-header-v3 { padding: 32px 32px 0 32px; }
.modal-header-v3 h3 { font-size: 22px; font-weight: 800; color: #1e293b; margin-bottom: 8px; }
.modal-header-v3 p { font-size: 14px; color: #64748b; }

.modal-body-v3 { padding: 32px; }

.form-group-v3 { margin-bottom: 24px; }
.form-group-v3 label { font-size: 11px; font-weight: 800; color: #1e293b; margin-bottom: 8px; display: block; letter-spacing: 0.1em; }
.form-group-v3 select { width: 100%; padding: 12px 16px; border-radius: 12px; border: 1px solid #e2e8f0; background: #f8fafc; font-weight: 600; outline: none; }

.section-title-box { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
.section-title-box label { font-size: 11px; font-weight: 800; color: #1e293b; letter-spacing: 0.1em; }
.badge-info-sm { background: #eff6ff; color: #2563eb; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 8px; }

.session-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 16px;
    margin-bottom: 12px;
    border-left: 4px solid #2563eb;
}

.session-input-grid {
    display: grid;
    grid-template-columns: 1.5fr 1fr 1fr auto;
    gap: 12px;
    align-items: flex-end;
}

.input-v3 label { font-size: 10px; font-weight: 800; color: #94a3b8; margin-bottom: 6px; display: block; }
.input-v3 select, .input-v3 input { width: 100%; padding: 8px 12px; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 13px; font-weight: 600; outline: none; }

.time-input-wrapper { position: relative; display: flex; align-items: center; }
.time-input-wrapper span { position: absolute; right: 10px; font-size: 18px; color: #94a3b8; pointer-events: none; }

.btn-delete-session {
    background: transparent;
    border: none;
    color: #ef4444;
    cursor: pointer;
    padding: 8px;
}

.btn-add-session {
    width: 100%;
    margin-top: 8px;
    padding: 12px;
    border: 2px dashed #e2e8f0;
    background: transparent;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-weight: 700;
    color: #64748b;
    cursor: pointer;
    transition: 0.2s;
}
.btn-add-session:hover { background: #f8fafc; border-color: #cbd5e1; color: #1e293b; }

.info-notice {
    margin-top: 24px;
    background: #eff6ff;
    padding: 16px;
    border-radius: 12px;
    display: flex;
    gap: 12px;
}
.info-notice span { color: #2563eb; }
.info-notice p { font-size: 12px; color: #1e40af; line-height: 1.5; font-weight: 500; margin: 0; }

.modal-footer-v3 { padding: 0 32px 32px 32px; display: flex; justify-content: flex-end; gap: 16px; }
.btn-submit-v3 { background: #2563eb; color: white; border: none; padding: 12px 24px; border-radius: 12px; font-weight: 700; cursor: pointer; }
.btn-text-v3 { background: transparent; border: none; color: #64748b; font-weight: 700; cursor: pointer; }

/* Animations */
.modal-enter-active, .modal-leave-active { transition: opacity 0.3s ease; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
.modal-content { transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); }
.modal-enter-from .modal-content { transform: scale(0.9) translateY(20px); }
</style>

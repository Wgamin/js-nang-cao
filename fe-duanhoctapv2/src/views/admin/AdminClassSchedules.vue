<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useApi } from '../../utils/api'
import AppLayout from '../../components/AppLayout.vue'

const route = useRoute()
const router = useRouter()
const { fetchApi } = useApi()

const classId = Number(route.params.id)
const classInfo = ref<any>(null)
const schedules = ref<any[]>([])
const studySlotsList = ref<any[]>([]) 
const loading = ref(true)
const toast = ref<{ msg: string; type: string } | null>(null)

const showModal = ref(false)
const modalMode = ref<'create_single' | 'create_bulk' | 'edit'>('create_bulk')
const saving = ref(false)

const form = ref({
  id: null as number | null,
  class_id: classId,
  date: '', 
  study_slot_id: '' as any, 
  room: '',
  note: '',
})

const bulkForm = ref({
  class_id: classId,
  sessions_count: 10,
  start_date: '',
  study_slot_id: '' as any,
  selected_weekdays: [] as number[], // [1, 3, 5] cho Thứ 2, 4, 6
  room: '',
  note: '',
  generatedList: [] as any[]
})

const weekdaysOptions = [
    { label: 'Thứ 2', value: 1 },
    { label: 'Thứ 3', value: 2 },
    { label: 'Thứ 4', value: 3 },
    { label: 'Thứ 5', value: 4 },
    { label: 'Thứ 6', value: 5 },
    { label: 'Thứ 7', value: 6 },
    { label: 'Chủ Nhật', value: 0 },
]

const showToast = (msg: string, type = 'success') => {
  toast.value = { msg, type }
  setTimeout(() => { toast.value = null }, 3500)
}

const loadData = async () => {
  loading.value = true
  try {
    const [s, c, slots] = await Promise.all([
      fetchApi('/admin/schedules'),
      fetchApi('/admin/classes-list'),
      fetchApi('/admin/study-slots'),
    ])
    schedules.value = s.data.filter((item: any) => item.class_id === classId)
    studySlotsList.value = slots.data
    const foundClass = c.data.find((item: any) => item.id === classId)
    if (foundClass) classInfo.value = foundClass
    schedules.value.sort((a,b) => new Date(a.start_time).getTime() - new Date(b.start_time).getTime())
  } catch (e: any) {
    showToast(e.message, 'error')
  } finally {
    loading.value = false
  }
}

const getStatus = (start_time: string, end_time: string) => {
    const now = new Date().getTime()
    const start = new Date(start_time).getTime()
    const end = end_time ? new Date(end_time).getTime() : start + (2 * 60 * 60 * 1000)
    
    if (now < start) return { label: 'Sắp tới', class: 'status-upcoming' }
    if (now > end) return { label: 'Đã học', class: 'status-finished' }
    return { label: 'Đang diễn ra', class: 'status-now' }
}

const getSlotName = (start_time: string) => {
    const timeStr = new Date(start_time).toTimeString().slice(0, 8)
    const slot = studySlotsList.value.find(s => s.start_time === timeStr)
    return slot ? slot.name : 'Vãng lai'
}

const formatDateShort = (dt: string) =>
  dt ? new Date(dt).toLocaleString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' }) : '—'

const formatTime = (dt: string) =>
  dt ? new Date(dt).toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' }) : '—'

const openCreateSingle = () => {
    modalMode.value = 'create_single'
    form.value = { id: null, class_id: classId, date: '', study_slot_id: '', room: '', note: '' }
    showModal.value = true
}

const openCreateBulk = () => {
  modalMode.value = 'create_bulk'
  bulkForm.value = { 
    class_id: classId, 
    sessions_count: 10, 
    start_date: '', 
    study_slot_id: '', 
    selected_weekdays: [], 
    room: schedules.value.length > 0 ? schedules.value[0].room : '',
    note: '', 
    generatedList: [] 
  }
  showModal.value = true
}

const openEdit = (sched: any) => {
  modalMode.value = 'edit'
  const sTime = new Date(sched.start_time)
  const timeStr = sTime.toTimeString().slice(0, 8) 
  const foundSlot = studySlotsList.value.find(slot => slot.start_time === timeStr)

  form.value = {
    id: sched.id,
    class_id: sched.class_id,
    date: sched.start_time.split('T')[0],
    study_slot_id: foundSlot ? foundSlot.id : '',
    room: sched.room,
    note: sched.note || '',
  }
  showModal.value = true
}

const generateBulkList = () => {
  const { start_date, study_slot_id, room, sessions_count, selected_weekdays } = bulkForm.value
  
  if (!start_date || !study_slot_id || !room || !sessions_count || selected_weekdays.length === 0) {
    showToast('Vui lòng chọn Ngày BĐ, Ca học, Phòng, Số buổi và Thứ trong tuần.', 'error')
    return
  }
  
  const slot = studySlotsList.value.find(s => s.id === study_slot_id)
  if (!slot) return

  bulkForm.value.generatedList = []
  let currentDate = new Date(start_date)
  let createdCount = 0

  // Giới hạn an toàn để tránh loop vô tận
  let safeCounter = 0
  while (createdCount < sessions_count && safeCounter < 365) {
    const dayOfWeek = currentDate.getDay() // 0=Sun, 1=Mon...
    if (selected_weekdays.includes(dayOfWeek)) {
       const datePart = currentDate.toISOString().split('T')[0]
       bulkForm.value.generatedList.push({
         start_time: `${datePart} ${slot.start_time.slice(0, 5)}:00`,
         end_time: `${datePart} ${slot.end_time.slice(0, 5)}:00`,
         room: room,
         note: `Buổi ${createdCount + 1}`,
       })
       createdCount++
    }
    currentDate.setDate(currentDate.getDate() + 1)
    safeCounter++
  }
}

const save = async () => {
  saving.value = true
  try {
    if (modalMode.value === 'edit' || modalMode.value === 'create_single') {
        const slot = studySlotsList.value.find(s => s.id === form.value.study_slot_id)
        if(!slot) return
        const payload = {
            class_id: classId,
            start_time: `${form.value.date} ${slot.start_time.slice(0, 5)}:00`,
            end_time: `${form.value.date} ${slot.end_time.slice(0, 5)}:00`,
            room: form.value.room,
            note: form.value.note
        }
        if (modalMode.value === 'edit') {
            await fetchApi(`/admin/schedules/${form.value.id}`, { method: 'PUT', body: JSON.stringify(payload) })
        } else {
            await fetchApi('/admin/schedules', { method: 'POST', body: JSON.stringify(payload) })
        }
    } else {
      if (bulkForm.value.generatedList.length === 0) {
        showToast('Vui lòng sinh danh sách trước!', 'error')
        saving.value = false; return
      }
      const payloadSchedules = bulkForm.value.generatedList.map((item: any) => ({
        class_id: classId, start_time: item.start_time, end_time: item.end_time, room: item.room, note: item.note
      }))
      await fetchApi('/admin/schedules/bulk', { method: 'POST', body: JSON.stringify({ schedules: payloadSchedules }) })
    }
    showToast('Lưu thành công!'); showModal.value = false; await loadData()
  } catch (e: any) {
    showToast(e.message, 'error')
  } finally {
    saving.value = false
  }
}

const deleteSchedule = async (sched: any) => {
  if (!confirm(`Xóa buổi học này?`)) return
  try {
    await fetchApi(`/admin/schedules/${sched.id}`, { method: 'DELETE' })
    showToast('Đã xóa!'); await loadData()
  } catch (e: any) {
    showToast(e.message, 'error')
  }
}

const toggleWeekday = (val: number) => {
    const idx = bulkForm.value.selected_weekdays.indexOf(val)
    if (idx > -1) bulkForm.value.selected_weekdays.splice(idx, 1)
    else bulkForm.value.selected_weekdays.push(val)
}

onMounted(loadData)
</script>

<template>
  <AppLayout>
    <div class="page">
      <div v-if="loading" class="loading-center"><div class="spinner-blue"></div></div>
      <template v-else>
        
        <!-- Header Section -->
        <div class="header-banner">
          <div class="banner-content">
            <button class="btn-back" @click="router.push('/admin/schedules')">← Trang Tổng Quan</button>
            <div class="flex-row">
              <div class="class-info-main">
                <span class="class-code">LỚP: #{{ classId }}</span>
                <h1 class="class-title">{{ classInfo?.name }}</h1>
                <p class="teacher-info">👨‍🏫 Giảng viên: <strong>{{ classInfo?.teacher?.name || 'Chưa cập nhật' }}</strong></p>
              </div>
              <div class="header-actions">
                <button class="btn btn-glass" @click="openCreateSingle">➕ Thêm 1 Buổi</button>
                <button class="btn btn-primary btn-elevated" @click="openCreateBulk">⚡ Xếp Lịch Tự Động</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Dashboard Stats Row -->
        <div class="stats-row mt-4">
          <div class="stat-card">
            <div class="stat-icon icon-blue">📅</div>
            <div class="stat-body">
              <span class="stat-label">Tổng số buổi</span>
              <span class="stat-value">{{ schedules.length }}</span>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon icon-green">✅</div>
            <div class="stat-body">
              <span class="stat-label">Đã hoàn thành</span>
              <span class="stat-value">{{ schedules.filter(s => new Date(s.start_time).getTime() < new Date().getTime()).length }}</span>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon icon-orange">⏳</div>
            <div class="stat-body">
              <span class="stat-label">Sắp tới</span>
              <span class="stat-value">{{ schedules.filter(s => new Date(s.start_time).getTime() > new Date().getTime()).length }}</span>
            </div>
          </div>
        </div>

        <!-- Main Schedule Table Card -->
        <div class="card table-card mt-4">
            <div class="card-header-flex">
                <h4 class="card-title">🗓️ Danh Sách Chi Tiết Buổi Học</h4>
            </div>
            
            <div v-if="!schedules.length" class="empty-state">
              <div class="empty-icon">📭</div>
              <p>Chưa có lịch học nào được xếp. Hãy nhấn nút "Xếp Lịch Tự Động" để bắt đầu.</p>
            </div>
            
            <div v-else class="table-container">
              <table class="premium-table">
                <thead>
                  <tr>
                    <th width="70">Buổi</th>
                    <th>📅 Ngày Học</th>
                    <th>🕒 Ca / Thời Gian</th>
                    <th>📍 Phòng học</th>
                    <th>📝 Nội dung & Ghi chú</th>
                    <th>🏷️ Trạng thái</th>
                    <th width="120" class="text-right">Thao tác</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(sched, index) in schedules" :key="sched.id" :class="{'row-current': getStatus(sched.start_time, sched.end_time).label === 'Đang diễn ra'}">
                    <td class="stt-cell">{{ index + 1 }}</td>
                    <td>
                      <div class="date-display">
                        <span class="date-main">{{ formatDateShort(sched.start_time) }}</span>
                        <span class="date-sub">{{ new Date(sched.start_time).toLocaleDateString('vi-VN', { weekday: 'long' }) }}</span>
                      </div>
                    </td>
                    <td>
                      <div class="time-display">
                        <span class="slot-badge">{{ getSlotName(sched.start_time) }}</span>
                        <span class="time-range">{{ formatTime(sched.start_time) }} - {{ formatTime(sched.end_time) }}</span>
                      </div>
                    </td>
                    <td><span class="room-pill">{{ sched.room }}</span></td>
                    <td class="note-cell">
                      <p class="note-text" :title="sched.note">{{ sched.note || '—' }}</p>
                    </td>
                    <td>
                      <span class="status-badge" :class="getStatus(sched.start_time, sched.end_time).class">
                        {{ getStatus(sched.start_time, sched.end_time).label }}
                      </span>
                    </td>
                    <td>
                      <div class="action-row">
                        <button class="btn-circle btn-edit" @click="openEdit(sched)" title="Chỉnh sửa">✏️</button>
                        <button class="btn-circle btn-delete" @click="deleteSchedule(sched)" title="Xóa buổi này">🗑️</button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
        </div>
      </template>
    </div>

    <!-- Modals -->
    <Teleport to="body">
      <div v-if="showModal" class="modal-backdrop" @click.self="showModal = false">
        <div class="modal" :class="{'modal-large': modalMode === 'create_bulk'}">
          <div class="modal-header">
            <h3>{{ modalMode === 'edit' ? '✏️ Cập Nhật Buổi Học' : (modalMode === 'create_single' ? '➕ Thêm Buổi Học Lẻ' : '⚡ Xếp Lịch Tự Động') }}</h3>
            <button class="modal-close" @click="showModal = false">✕</button>
          </div>
          <div class="modal-body">
            
            <div v-if="modalMode === 'edit' || modalMode === 'create_single'" class="form-grid">
              <div class="form-group span-2">
                <label class="form-label">Ngày Học *</label>
                <input class="form-input" type="date" v-model="form.date" />
              </div>
              <div class="form-group span-2">
                <label class="form-label">Chọn Ca Học (Giờ chuẩn) *</label>
                <select class="form-input" v-model="form.study_slot_id">
                    <option value="" disabled>-- Chọn ca --</option>
                    <option v-for="slot in studySlotsList" :key="slot.id" :value="slot.id">{{ slot.name }} ({{ slot.start_time.slice(0,5) }} - {{ slot.end_time.slice(0,5) }})</option>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Phòng học *</label>
                <input class="form-input" v-model="form.room" />
              </div>
              <div class="form-group">
                <label class="form-label">Ghi chú nội dung</label>
                <input class="form-input" v-model="form.note" />
              </div>
            </div>

            <div v-else>
              <div class="bulk-config-card">
                <div class="form-grid">
                  <div class="form-group span-2">
                    <label class="form-label">Chọn Các Thứ Trong Tuần *</label>
                    <div class="weekday-selector">
                        <button v-for="opt in weekdaysOptions" :key="opt.value" 
                            type="button"
                            class="weekday-btn" 
                            :class="{ active: bulkForm.selected_weekdays.includes(opt.value) }"
                            @click="toggleWeekday(opt.value)">
                            {{ opt.label }}
                        </button>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="form-label">Ngày bắt đầu *</label>
                    <input class="form-input" type="date" v-model="bulkForm.start_date" />
                  </div>
                  <div class="form-group">
                    <label class="form-label">Ca sinh lịch *</label>
                    <select class="form-input" v-model="bulkForm.study_slot_id">
                      <option value="" disabled>-- Chọn ca --</option>
                      <option v-for="slot in studySlotsList" :key="slot.id" :value="slot.id">{{ slot.name }} ({{ slot.start_time.slice(0,5) }} - {{ slot.end_time.slice(0,5) }})</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label class="form-label">Tổng số buổi muốn sinh *</label>
                    <input class="form-input" type="number" v-model="bulkForm.sessions_count" />
                  </div>
                  <div class="form-group">
                    <label class="form-label">Mặc định phòng *</label>
                    <input class="form-input" v-model="bulkForm.room" />
                  </div>
                </div>
                <button type="button" class="btn btn-secondary w-full mt-4" @click="generateBulkList">⚡ Sinh Danh Sách Buổi Học</button>
              </div>

              <div v-if="bulkForm.generatedList.length > 0" class="mt-4">
                <h5 class="mb-2">Kiểm tra danh sách ({{ bulkForm.generatedList.length }} buổi):</h5>
                <div class="bulk-list-window">
                  <table class="mini-table">
                    <thead>
                      <tr><th>STT</th><th>Thứ</th><th>Ngày Học</th><th>Giờ</th><th>Phòng</th><th>Ghi Chú</th><th width="30"></th></tr>
                    </thead>
                    <tbody>
                      <tr v-for="(item, index) in bulkForm.generatedList" :key="index">
                        <td>{{ index + 1 }}</td>
                        <td class="fw-bold">{{ new Date(item.start_time).toLocaleDateString('vi-VN', { weekday: 'short' }) }}</td>
                        <td>{{ formatDateShort(item.start_time) }}</td>
                        <td class="text-primary fw-bold">{{ item.start_time.slice(11,16) }}</td>
                        <td><input type="text" class="input-transparent" v-model="item.room" style="width:60px" /></td>
                        <td><input type="text" class="input-transparent" v-model="item.note" placeholder="Note..." /></td>
                        <td><button class="text-danger" @click="bulkForm.generatedList.splice(index, 1)">✕</button></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-outline" @click="showModal = false">Đóng</button>
            <button class="btn btn-primary" :disabled="saving" @click="save">{{ saving ? 'Đang thực hiện...' : '💾 Lưu Lịch Chính Thức' }}</button>
          </div>
        </div>
      </div>
    </Teleport>

    <Teleport to="body">
      <div v-if="toast" class="toast" :class="`toast-${toast.type}`">{{ toast.msg }}</div>
    </Teleport>
  </AppLayout>
</template>

<style scoped>
.page { padding: 32px; background: #f1f5f9; min-height: 100vh; }
.flex-row { display:flex; justify-content:space-between; align-items:flex-end; }
.loading-center { display:flex;justify-content:center;padding:100px; }
.spinner-blue { width:40px;height:40px;border:4px solid rgba(79,70,229,.1);border-top-color:#4f46e5;border-radius:50%;animation:spin .8s linear infinite; }
@keyframes spin { to{transform:rotate(360deg)} }

/* Header Banner */
.header-banner { background: #fff; border-radius: 24px; padding: 32px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid #e2e8f0; }
.btn-back { background:none; border:none; color:#64748b; font-weight:600; cursor:pointer; margin-bottom:12px; font-size:13px; }
.class-code { color:#4f46e5; font-weight:800; font-size:12px; letter-spacing:0.1em; }
.class-title { font-size:28px; font-weight:900; color:#1e293b; margin: 4px 0 8px; }
.teacher-info { color:#64748b; font-size:14px; }
.header-actions { display:flex; gap:12px; }

/* Stats Row */
.stats-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
.stat-card { background:#fff; padding:20px; border-radius:20px; display:flex; align-items:center; gap:16px; border:1px solid #e2e8f0; }
.stat-icon { width:48px; height:48px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:20px; }
.icon-blue { background: #dbeafe; } .icon-green { background: #dcfce7; } .icon-orange { background: #ffedd5; }
.stat-label { font-size:12px; color:#64748b; font-weight:600; display:block; }
.stat-value { font-size:20px; font-weight:800; color:#1e293b; }

/* Table Section */
.table-card { background:#fff; border-radius:24px; overflow:hidden; border:1px solid #e2e8f0; box-shadow: 0 10px 30px rgba(0,0,0,0.02); }
.card-header-flex { padding: 24px; border-bottom: 1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center; }
.card-title { font-size:18px; font-weight:800; color:#1e293b; margin:0; }
.table-container { width:100%; overflow-x:auto; }
.premium-table { width:100%; border-collapse:collapse; }
.premium-table th { background:#f8fafc; padding:16px 20px; text-align:left; font-size:12px; text-transform:uppercase; color:#64748b; font-weight:700; letter-spacing:0.05em; }
.premium-table td { padding:16px 20px; border-bottom:1px solid #f1f5f9; vertical-align:middle; transition: background 0.2s; }
.premium-table tr:hover td { background:#f8fafc; }
.row-current td { background: #eff6ff !important; border-left: 4px solid #3b82f6; }

.stt-cell { font-weight:800; color:#94a3b8; text-align:center; }
.date-main { display:block; font-weight:700; color:#1e293b; font-size:15px; }
.date-sub { display:block; font-size:11px; color:#94a3b8; text-transform: capitalize; }
.slot-badge { background:#f1f5f9; color:#475569; padding:2px 8px; border-radius:6px; font-size:11px; font-weight:800; margin-bottom:4px; display:inline-block; }
.time-range { display:block; font-weight:600; color:#4f46e5; font-size:14px; }
.room-pill { background:#f0fdf4; color:#15803d; padding:4px 12px; border-radius:10px; font-weight:700; font-size:12px; border:1px solid #dcfce7; }
.note-cell { max-width: 250px; }
.note-text { margin:0; font-size:13px; color:#64748b; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }

/* Status Badges */
.status-badge { padding:6px 14px; border-radius:999px; font-size:11px; font-weight:700; display:inline-block; }
.status-upcoming { background:#e0e7ff; color:#4338ca; }
.status-finished { background:#f1f5f9; color:#64748b; }
.status-now { background:#dcfce7; color:#15803d; box-shadow: 0 0 10px rgba(34,197,94,0.2); animation: pulse 2s infinite; }
@keyframes pulse { 0% { opacity:1; } 50% { opacity:0.7; } 100% { opacity:1; } }

/* Actions */
.action-row { display:flex; gap:8px; justify-content: flex-end; }
.btn-circle { width:34px; height:34px; border-radius:50%; display:flex; align-items:center; justify-content:center; border:none; cursor:pointer; transition:all 0.2s; font-size:14px; }
.btn-edit { background:#f1f5f9; color:#475569; } .btn-edit:hover { background:#e2e8f0; transform:scale(1.1); }
.btn-delete { background:#fff1f2; color:#e11d48; } .btn-delete:hover { background:#ffe4e6; transform:scale(1.1); }

/* Custom Buttons */
.btn-glass { background:rgba(79,70,229,0.08); color:#4f46e5; border:1px solid rgba(79,70,229,0.1); font-weight:700; }
.btn-glass:hover { background:rgba(79,70,229,0.15); }
.btn-elevated { box-shadow: 0 4px 12px rgba(79,70,229,0.3); }

/* Modal Styles */
.modal-backdrop { position:fixed; inset:0; background:rgba(15,23,42,0.5); backdrop-filter:blur(8px); display:flex; align-items:center; justify-content:center; z-index:2000; padding:20px; }
.modal { background:#fff; border-radius:30px; width:100%; max-width:500px; max-height:92vh; display:flex; flex-direction:column; box-shadow:0 30px 60px rgba(0,0,0,0.2); }
.modal.modal-large { max-width: 900px; }
.modal-header { padding:24px 32px; border-bottom:1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center; }
.modal-header h3 { font-size:20px; font-weight:800; color:#1e293b; margin:0; }
.modal-close { background:#f1f5f9; border:none; width:36px; height:36px; border-radius:50%; cursor:pointer; font-size:16px; }
.modal-body { padding:32px; overflow-y:auto; flex:1; }
.modal-footer { padding:24px 32px; border-top:1px solid #f1f5f9; display:flex; gap:12px; justify-content:flex-end; }
.form-label { display:block; margin-bottom:8px; font-size:13px; font-weight:700; color:#334155; }
.form-input { width:100%; padding:12px 16px; border-radius:12px; border:1px solid #e2e8f0; font-size:14px; transition:border 0.2s; }
.form-input:focus { border-color:#4f46e5; outline:none; box-shadow:0 0 0 3px rgba(79,70,229,0.1); }
.span-2 { grid-column: span 2; }

/* Weekday Selector */
.weekday-selector { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:8px; }
.weekday-btn { border:1px solid #e2e8f0; background:#fff; padding:8px 14px; border-radius:12px; font-size:13px; font-weight:600; cursor:pointer; transition:all 0.2s; color:#64748b; }
.weekday-btn.active { background:#4f46e5; color:#fff; border-color:#4f46e5; box-shadow: 0 4px 10px rgba(79,70,229,0.2); }
.weekday-btn:hover:not(.active) { background:#f8fafc; border-color:#cbd5e1; }

.bulk-config-card { background:#f8fafc; padding:24px; border-radius:20px; border:1px solid #e2e8f0; }
.bulk-list-window { max-height:300px; overflow-y:auto; border:1px solid #f1f5f9; border-radius:12px; margin-top:16px; }
.mini-table { width:100%; border-collapse:collapse; font-size:13px; }
.mini-table th { background:#f1f5f9; padding:10px; text-align:left; color:#64748b; }
.mini-table td { padding:10px; border-bottom:1px solid #f8fafc; }
.input-transparent { background:none; border:none; width:100%; font-size:13px; font-weight:600; }
.input-transparent:focus { outline:none; border-bottom:1px solid #4f46e5; }
</style>

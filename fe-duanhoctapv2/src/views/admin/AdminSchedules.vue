<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useApi } from '../../utils/api'
import AppLayout from '../../components/AppLayout.vue'

const { fetchApi } = useApi()
const schedules = ref<any[]>([])
const classesList = ref<any[]>([])
const loading = ref(true)
const toast = ref<{ msg: string; type: string } | null>(null)
const showModal = ref(false)
const isEditing = ref(false)
const saving = ref(false)
const form = ref({
  id: null as number | null,
  class_id: '' as any,
  start_time: '',
  end_time: '',
  room: '',
  note: '',
  attendance_count: 0,
})

const showToast = (msg: string, type = 'success') => {
  toast.value = { msg, type }
  setTimeout(() => { toast.value = null }, 3500)
}

const toLocalInput = (dt: string) => {
  if (!dt) return ''
  const d = new Date(dt)
  d.setMinutes(d.getMinutes() - d.getTimezoneOffset())
  return d.toISOString().slice(0, 16)
}

const loadAll = async () => {
  loading.value = true
  try {
    const [s, c] = await Promise.all([
      fetchApi('/admin/schedules'),
      fetchApi('/admin/classes-list'),
    ])
    schedules.value = s.data
    classesList.value = c.data
  } catch (e: any) {
    showToast(e.message, 'error')
  } finally {
    loading.value = false
  }
}

const formatDate = (dt: string) =>
  dt ? new Date(dt).toLocaleString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : '—'

const openCreate = () => {
  isEditing.value = false
  form.value = { id: null, class_id: '', start_time: '', end_time: '', room: '', note: '', attendance_count: 0 }
  showModal.value = true
}

const openEdit = (sched: any) => {
  isEditing.value = true
  form.value = {
    id: sched.id,
    class_id: sched.class_id,
    start_time: toLocalInput(sched.start_time),
    end_time: toLocalInput(sched.end_time),
    room: sched.room,
    note: sched.note || '',
    attendance_count: sched.attendance_count || 0,
  }
  showModal.value = true
}

const save = async () => {
  saving.value = true
  try {
    console.log('📋 Form hiện tại:', form.value)
    
    // Convert attendance_count to number
    const attendanceCount = parseInt(form.value.attendance_count) || 0
    
    const payload = {
      class_id: form.value.class_id,
      start_time: form.value.start_time,
      end_time: form.value.end_time,
      room: form.value.room,
      note: form.value.note,
      attendance_count: attendanceCount,
    }
    console.log('📤 Payload gửi lên:', payload)
    console.log('Attendance count type:', typeof attendanceCount, 'Value:', attendanceCount)
    
    if (isEditing.value && form.value.id) {
      await fetchApi(`/admin/schedules/${form.value.id}`, { method: 'PUT', body: JSON.stringify(payload) })
      showToast('✅ Cập nhật lịch học thành công!')
    } else {
      await fetchApi('/admin/schedules', { method: 'POST', body: JSON.stringify(payload) })
      showToast('✅ Tạo lịch học thành công!')
    }
    showModal.value = false
    await loadAll()
  } catch (e: any) {
    showToast(e.message, 'error')
  } finally {
    saving.value = false
    }
  }

  const deleteSchedule = async (sched: any) => {
  if (!confirm(`Xóa buổi học ngày ${formatDate(sched.start_time)}?`)) return
  try {
    await fetchApi(`/admin/schedules/${sched.id}`, { method: 'DELETE' })
    showToast('🗑️ Đã xóa lịch học!')
    await loadAll()
  } catch (e: any) {
    showToast(e.message, 'error')
  }
}

const getAttendanceCount = (sched: any) => {
  return sched.attendance_count || 0
}

onMounted(loadAll)
</script>

<template>
  <AppLayout>
    <div class="page">
      <div class="page-header">
        <div>
          <h1 class="page-title">📅 Quản Lý Lịch Học</h1>
          <p class="text-muted">Tạo và quản lý lịch học cho từng lớp.</p>
        </div>
        <button id="create-schedule-btn" class="btn btn-primary" @click="openCreate">+ Thêm Lịch Học</button>
      </div>

      <div class="card">
        <div v-if="loading" class="loading-center"><div class="spinner-blue"></div></div>
        <div v-else-if="!schedules.length" class="empty-state">Chưa có lịch học nào.</div>
        <div v-else class="table-wrapper">
          <table class="table">
            <thead>
              <tr>
                <th>Lớp Học</th>
                <th>Giáo Viên</th>
                <th>Bắt Đầu</th>
                <th>Kết Thúc</th>
                <th>Phòng</th>
                <th>Ghi Chú</th>
                <th>Số Buổi</th>
                <th>Hành Động</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="sched in schedules" :key="sched.id">
                <td class="fw-bold">{{ sched.study_class?.name || '—' }}</td>
                <td class="text-muted">{{ sched.study_class?.teacher?.name || '—' }}</td>
                <td>{{ formatDate(sched.start_time) }}</td>
                <td>{{ sched.end_time ? formatDate(sched.end_time) : '—' }}</td>
                <td><span class="room-tag">{{ sched.room }}</span></td>
                <td class="text-muted">{{ sched.note || '—' }}</td>
                <td><span class="session-badge">Buổi {{ getAttendanceCount(sched) }}</span></td>
                <td>
                  <div class="action-btns">
                    <button class="btn btn-outline btn-sm" @click="openEdit(sched)">✏️ Sửa</button>
                    <button class="btn btn-danger btn-sm" @click="deleteSchedule(sched)">🗑️ Xóa</button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <Teleport to="body">
      <div v-if="showModal" class="modal-backdrop" @click.self="showModal = false">
        <div class="modal" id="schedule-modal">
          <div class="modal-header">
            <h3>{{ isEditing ? '✏️ Sửa Lịch Học' : '➕ Tạo Lịch Học Mới' }}</h3>
            <button class="modal-close" @click="showModal = false">✕</button>
          </div>
          <div class="modal-body">
            <div class="form-grid">
              <div class="form-group" style="grid-column:span 2">
                <label class="form-label">Lớp học *</label>
                <select class="form-input" v-model="form.class_id">
                  <option disabled value="">-- Chọn lớp --</option>
                  <option v-for="c in classesList" :key="c.id" :value="c.id">{{ c.name }}</option>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Thời gian bắt đầu *</label>
                <input class="form-input" type="datetime-local" v-model="form.start_time" />
              </div>
              <div class="form-group">
                <label class="form-label">Thời gian kết thúc *</label>
                <input class="form-input" type="datetime-local" v-model="form.end_time" />
              </div>
              <div class="form-group">
                <label class="form-label">Phòng học *</label>
                <input class="form-input" v-model="form.room" placeholder="Phòng 102, Zoom ID..." />
              </div>
              <div class="form-group">
                <label class="form-label">Ghi chú</label>
                <input class="form-input" v-model="form.note" placeholder="Ghi chú buổi học..." />
              </div>
              <div class="form-group">
                <label class="form-label">Số Buổi Học</label>
                <input class="form-input" v-model="form.attendance_count" placeholder="0" />
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-outline" @click="showModal = false">Hủy</button>
            <button id="save-schedule-btn" class="btn btn-primary" :disabled="saving" @click="save">
              <span v-if="saving" class="spinner"></span>
              {{ saving ? 'Đang lưu...' : '💾 Lưu' }}
            </button>
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
.page { padding: 28px 32px; }
.page-header { display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px;gap:16px; }
.page-title { font-size:24px;font-weight:800;margin-bottom:4px; }
.loading-center { display:flex;justify-content:center;padding:40px; }
.spinner-blue { width:32px;height:32px;border:3px solid rgba(79,70,229,.2);border-top-color:#4f46e5;border-radius:50%;animation:spin .8s linear infinite; }
@keyframes spin { to{transform:rotate(360deg)} }
.empty-state { text-align:center;padding:48px;color:var(--color-text-muted); }
.action-btns { display:flex;gap:8px; }
.room-tag { background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0;padding:2px 10px;border-radius:999px;font-size:12px;font-weight:500; }
.session-badge { background:#e0e7ff;color:#3730a3;border:1px solid #c7d2fe;padding:3px 10px;border-radius:999px;font-size:12px;font-weight:700; }
.form-grid { display:grid;grid-template-columns:1fr 1fr;gap:16px; }
.modal-backdrop { position:fixed;inset:0;background:rgba(15,23,42,.6);backdrop-filter:blur(4px);display:flex;align-items:center;justify-content:center;z-index:1000;padding:20px; }
.modal { background:#fff;border-radius:20px;width:100%;max-width:580px;max-height:90vh;display:flex;flex-direction:column;box-shadow:0 24px 60px rgba(0,0,0,.2);animation:modalIn .25s ease; }
@keyframes modalIn { from{opacity:0;transform:scale(.95)} to{opacity:1;transform:scale(1)} }
.modal-header { display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid var(--color-border); }
.modal-header h3 { font-size:17px;font-weight:700; }
.modal-close { background:none;border:none;cursor:pointer;color:var(--color-text-muted);font-size:18px;padding:4px 8px;border-radius:6px; }
.modal-body { padding:24px;overflow-y:auto;flex:1; }
.modal-footer { display:flex;gap:12px;justify-content:flex-end;padding:16px 24px;border-top:1px solid var(--color-border); }
</style>

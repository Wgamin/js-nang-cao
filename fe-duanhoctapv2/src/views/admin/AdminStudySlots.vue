<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useApi } from '../../utils/api'
import AppLayout from '../../components/AppLayout.vue'

const { fetchApi } = useApi()
const slots = ref<any[]>([])
const loading = ref(true)
const toast = ref<{ msg: string; type: string } | null>(null)
const showModal = ref(false)
const isEditing = ref(false)
const saving = ref(false)

const form = ref({
  id: null as number | null,
  name: '',
  start_time: '',
  end_time: '',
  description: '',
})

const showToast = (msg: string, type = 'success') => {
  toast.value = { msg, type }
  setTimeout(() => { toast.value = null }, 3500)
}

const loadSlots = async () => {
  loading.value = true
  try {
    const res = await fetchApi('/admin/study-slots')
    slots.value = res.data
  } catch (e: any) {
    showToast(e.message, 'error')
  } finally {
    loading.value = false
  }
}

const openCreate = () => {
  isEditing.value = false
  form.value = { id: null, name: '', start_time: '', end_time: '', description: '' }
  showModal.value = true
}

const openEdit = (slot: any) => {
  isEditing.value = true
  form.value = { ...slot }
  showModal.value = true
}

const save = async () => {
  saving.value = true
  try {
    if (isEditing.value && form.value.id) {
      await fetchApi(`/admin/study-slots/${form.value.id}`, { method: 'PUT', body: JSON.stringify(form.value) })
      showToast('✅ Cập nhật ca học thành công!')
    } else {
      await fetchApi('/admin/study-slots', { method: 'POST', body: JSON.stringify(form.value) })
      showToast('✅ Tạo ca học mới thành công!')
    }
    showModal.value = false
    await loadSlots()
  } catch (e: any) {
    showToast(e.message, 'error')
  } finally {
    saving.value = false
  }
}

const deleteSlot = async (slot: any) => {
  if (!confirm(`Xóa ca học ${slot.name}?`)) return
  try {
    await fetchApi(`/admin/study-slots/${slot.id}`, { method: 'DELETE' })
    showToast('🗑️ Đã xóa ca học!')
    await loadSlots()
  } catch (e: any) {
    showToast(e.message, 'error')
  }
}

onMounted(loadSlots)
</script>

<template>
  <AppLayout>
    <div class="page">
      <div class="page-header">
        <div>
          <h1 class="page-title">🕒 Quản Lý Ca Học (Study Slots)</h1>
          <p class="text-muted">Định nghĩa các khung giờ học cố định để Admin chọn khi xếp lịch, tránh sai sót.</p>
        </div>
        <button class="btn btn-primary" @click="openCreate">+ Thêm Ca Học</button>
      </div>

      <div class="card">
        <div v-if="loading" class="loading-center"><div class="spinner-blue"></div></div>
        <div v-else-if="!slots.length" class="empty-state">Chưa có ca học nào được tạo.</div>
        <div v-else class="table-wrapper">
          <table class="table">
            <thead>
              <tr>
                <th>Tên Ca</th>
                <th>Giờ Bắt Đầu</th>
                <th>Giờ Kết Thúc</th>
                <th>Mô Tả</th>
                <th class="text-center">Hành Động</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="slot in slots" :key="slot.id">
                <td class="fw-bold">{{ slot.name }}</td>
                <td><span class="time-tag">{{ slot.start_time.slice(0, 5) }}</span></td>
                <td><span class="time-tag">{{ slot.end_time.slice(0, 5) }}</span></td>
                <td class="text-muted">{{ slot.description || '—' }}</td>
                <td>
                  <div class="action-btns justify-center">
                    <button class="btn btn-outline btn-sm" @click="openEdit(slot)">✏️ Sửa</button>
                    <button class="btn btn-danger btn-sm" @click="deleteSlot(slot)">🗑️ Xóa</button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Modal Form -->
    <Teleport to="body">
      <div v-if="showModal" class="modal-backdrop" @click.self="showModal = false">
        <div class="modal">
          <div class="modal-header">
            <h3>{{ isEditing ? '✏️ Sửa Ca Học' : '➕ Tạo Ca Học Mới' }}</h3>
            <button class="modal-close" @click="showModal = false">✕</button>
          </div>
          <div class="modal-body">
            <div class="form-group mb-3">
              <label class="form-label">Tên Ca *</label>
              <input class="form-input" v-model="form.name" placeholder="VD: Ca 1, Ca Sáng..." />
            </div>
            <div class="form-grid mb-3">
              <div class="form-group">
                <label class="form-label">Giờ Bắt Đầu *</label>
                <input class="form-input" type="time" v-model="form.start_time" />
              </div>
              <div class="form-group">
                <label class="form-label">Giờ Kết Thúc *</label>
                <input class="form-input" type="time" v-model="form.end_time" />
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Mô Tả</label>
              <textarea class="form-input" v-model="form.description" rows="3" placeholder="Ghi chú thêm về ca học..."></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-outline" @click="showModal = false">Hủy</button>
            <button class="btn btn-primary" :disabled="saving" @click="save">
              <span v-if="saving" class="spinner"></span>
              {{ saving ? 'Đang lưu...' : '💾 Lưu Ca Học' }}
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
.justify-center { justify-content: center; }
.time-tag { background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; padding: 2px 10px; border-radius: 6px; font-weight: 700; font-size: 13px;}

.form-grid { display:grid;grid-template-columns:1fr 1fr;gap:16px; }
.modal-backdrop { position:fixed;inset:0;background:rgba(15,23,42,.6);backdrop-filter:blur(4px);display:flex;align-items:center;justify-content:center;z-index:1000;padding:20px; }
.modal { background:#fff;border-radius:20px;width:100%;max-width:500px;max-height:90vh;display:flex;flex-direction:column;box-shadow:0 24px 60px rgba(0,0,0,.2);animation:modalIn .25s ease; }
@keyframes modalIn { from{opacity:0;transform:scale(.95)} to{opacity:1;transform:scale(1)} }
.modal-header { display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid var(--color-border); }
.modal-header h3 { font-size:17px;font-weight:700; }
.modal-close { background:none;border:none;cursor:pointer;color:var(--color-text-muted);font-size:18px;padding:4px 8px;border-radius:6px; }
.modal-body { padding:24px;overflow-y:auto;flex:1; }
.modal-footer { display:flex;gap:12px;justify-content:flex-end;padding:16px 24px;border-top:1px solid var(--color-border); }
</style>

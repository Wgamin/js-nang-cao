<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useApi } from '../../utils/api'
import AppLayout from '../../components/AppLayout.vue'

const { fetchApi } = useApi()
const classes = ref<any[]>([])
const teachers = ref<any[]>([])
const studentsList = ref<any[]>([])
const loading = ref(true)
const toast = ref<{ msg: string; type: string } | null>(null)
const showModal = ref(false)
const isEditing = ref(false)
const saving = ref(false)
const form = ref({ id: null as number | null, name: '', teacher_id: '' as any, description: '', status: 'active', student_ids: [] as number[] })

const showToast = (msg: string, type = 'success') => {
  toast.value = { msg, type }
  setTimeout(() => { toast.value = null }, 3500)
}

const loadAll = async () => {
  loading.value = true
  try {
    const [c, t, s] = await Promise.all([
      fetchApi('/admin/classes'),
      fetchApi('/admin/teachers'),
      fetchApi('/admin/students-list'),
    ])
    classes.value = c.data
    teachers.value = t.data
    studentsList.value = s.data
  } catch (e: any) {
    showToast(e.message, 'error')
  } finally {
    loading.value = false
  }
}

const openCreate = () => {
  isEditing.value = false
  form.value = { id: null, name: '', teacher_id: '', description: '', status: 'active', student_ids: [] }
  showModal.value = true
}

const openEdit = (cls: any) => {
  isEditing.value = true
  form.value = {
    id: cls.id,
    name: cls.name,
    teacher_id: cls.teacher_id,
    description: cls.description || '',
    status: cls.status,
    student_ids: cls.students?.map((s: any) => s.id) || [],
  }
  showModal.value = true
}

const toggleStudent = (id: number) => {
  const idx = form.value.student_ids.indexOf(id)
  if (idx === -1) form.value.student_ids.push(id)
  else form.value.student_ids.splice(idx, 1)
}

const save = async () => {
  saving.value = true
  try {
    const payload = {
      name: form.value.name,
      teacher_id: form.value.teacher_id,
      description: form.value.description,
      status: form.value.status,
      student_ids: form.value.student_ids,
    }
    if (isEditing.value && form.value.id) {
      await fetchApi(`/admin/classes/${form.value.id}`, { method: 'PUT', body: JSON.stringify(payload) })
      showToast('✅ Cập nhật lớp thành công!')
    } else {
      await fetchApi('/admin/classes', { method: 'POST', body: JSON.stringify(payload) })
      showToast('✅ Tạo lớp thành công!')
    }
    showModal.value = false
    await loadAll()
  } catch (e: any) {
    showToast(e.message, 'error')
  } finally {
    saving.value = false
  }
}

const deleteClass = async (cls: any) => {
  if (!confirm(`Xóa lớp "${cls.name}"?`)) return
  try {
    await fetchApi(`/admin/classes/${cls.id}`, { method: 'DELETE' })
    showToast('🗑️ Đã xóa lớp!')
    await loadAll()
  } catch (e: any) {
    showToast(e.message, 'error')
  }
}

onMounted(loadAll)
</script>

<template>
  <AppLayout>
    <div class="page">
      <div class="page-header">
        <div>
          <h1 class="page-title">🏫 Quản Lý Lớp Học</h1>
          <p class="text-muted">Tạo lớp, phân công giáo viên và đăng ký học sinh.</p>
        </div>
        <button id="create-class-btn" class="btn btn-primary" @click="openCreate">+ Thêm Lớp</button>
      </div>

      <div class="card">
        <div v-if="loading" class="loading-center"><div class="spinner-blue"></div></div>
        <div v-else-if="!classes.length" class="empty-state">Chưa có lớp học nào.</div>
        <div v-else class="table-wrapper">
          <table class="table">
            <thead>
              <tr>
                <th>Tên Lớp</th>
                <th>Giáo Viên</th>
                <th>Mô Tả</th>
                <th>Trạng Thái</th>
                <th>Số HS</th>
                <th>Hành Động</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="cls in classes" :key="cls.id">
                <td class="fw-bold">{{ cls.name }}</td>
                <td>{{ cls.teacher?.name || '—' }}</td>
                <td class="text-muted cell-truncate">{{ cls.description || '—' }}</td>
                <td>
                  <span class="status-badge" :class="cls.status === 'active' ? 'status-active' : 'status-inactive'">
                    {{ cls.status === 'active' ? '✅ Hoạt động' : '⏸ Dừng' }}
                  </span>
                </td>
                <td class="text-center">{{ cls.students_count ?? 0 }}</td>
                <td>
                  <div class="action-btns">
                    <button class="btn btn-outline btn-sm" @click="openEdit(cls)">✏️ Sửa</button>
                    <button class="btn btn-danger btn-sm" @click="deleteClass(cls)">🗑️ Xóa</button>
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
        <div class="modal large-modal" id="class-modal">
          <div class="modal-header">
            <h3>{{ isEditing ? '✏️ Sửa Lớp Học' : '➕ Tạo Lớp Mới' }}</h3>
            <button class="modal-close" @click="showModal = false">✕</button>
          </div>
          <div class="modal-body">
            <div class="form-grid">
              <div class="form-group" style="grid-column:span 2">
                <label class="form-label">Tên lớp *</label>
                <input class="form-input" v-model="form.name" placeholder="Toán 12A1" />
              </div>
              <div class="form-group">
                <label class="form-label">Giáo viên phụ trách *</label>
                <select class="form-input" v-model="form.teacher_id">
                  <option disabled value="">-- Chọn giáo viên --</option>
                  <option v-for="t in teachers" :key="t.id" :value="t.id">{{ t.name }}</option>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Trạng thái</label>
                <select class="form-input" v-model="form.status">
                  <option value="active">Hoạt động</option>
                  <option value="inactive">Ngừng hoạt động</option>
                </select>
              </div>
              <div class="form-group" style="grid-column:span 2">
                <label class="form-label">Mô tả khóa học</label>
                <textarea class="form-input" v-model="form.description" rows="2" placeholder="Ôn thi đại học, nâng cao..."></textarea>
              </div>
            </div>

            <div class="students-section">
              <label class="form-label">Học sinh trong lớp ({{ form.student_ids.length }} đã chọn)</label>
              <div class="students-grid">
                <label v-for="s in studentsList" :key="s.id" class="student-check">
                  <input type="checkbox" :checked="form.student_ids.includes(s.id)" @change="toggleStudent(s.id)" />
                  <span class="student-check-name">{{ s.name }}</span>
                  <span class="text-muted" style="font-size:11px">{{ s.email }}</span>
                </label>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-outline" @click="showModal = false">Hủy</button>
            <button id="save-class-btn" class="btn btn-primary" :disabled="saving" @click="save">
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
.cell-truncate { max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
.action-btns { display:flex;gap:8px; }
.text-center { text-align:center; }
.status-badge { display:inline-block;padding:3px 10px;border-radius:999px;font-size:12px;font-weight:600; }
.status-active { background:#dcfce7;color:#15803d; }
.status-inactive { background:#fee2e2;color:#b91c1c; }
.form-grid { display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px; }
.students-section { border-top:1px solid var(--color-border);padding-top:16px; }
.students-grid { display:grid;grid-template-columns:repeat(2,1fr);gap:8px;margin-top:12px;max-height:220px;overflow-y:auto;padding-right:4px; }
.student-check { display:flex;align-items:center;gap:8px;padding:8px 10px;border:1px solid var(--color-border);border-radius:8px;cursor:pointer;transition:all .15s; }
.student-check:hover { border-color:var(--color-primary);background:var(--color-primary-light); }
.student-check input { accent-color:var(--color-primary); }
.student-check-name { font-size:13px;font-weight:500;flex:1; }
.modal-backdrop { position:fixed;inset:0;background:rgba(15,23,42,.6);backdrop-filter:blur(4px);display:flex;align-items:center;justify-content:center;z-index:1000;padding:20px; }
.modal { background:#fff;border-radius:20px;width:100%;max-width:620px;max-height:90vh;display:flex;flex-direction:column;box-shadow:0 24px 60px rgba(0,0,0,.2);animation:modalIn .25s ease; }
.large-modal { max-width:680px; }
@keyframes modalIn { from{opacity:0;transform:scale(.95)} to{opacity:1;transform:scale(1)} }
.modal-header { display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid var(--color-border); }
.modal-header h3 { font-size:17px;font-weight:700; }
.modal-close { background:none;border:none;cursor:pointer;color:var(--color-text-muted);font-size:18px;padding:4px 8px;border-radius:6px; }
.modal-body { padding:24px;overflow-y:auto;flex:1; }
.modal-footer { display:flex;gap:12px;justify-content:flex-end;padding:16px 24px;border-top:1px solid var(--color-border); }
</style>

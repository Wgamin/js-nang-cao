<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useApi } from '../../utils/api'
import AppLayout from '../../components/AppLayout.vue'

const router = useRouter()

const { fetchApi } = useApi()

// Data
const users = ref<any[]>([])
const allStudents = ref<any[]>([])   // Tất cả học sinh (cho Parent chọn con)
const parentsList = ref<any[]>([])   // Tất cả phụ huynh (cho Student chọn cha mẹ)
const loading = ref(true)
const filterRole = ref('')
const toast = ref<{ msg: string; type: string } | null>(null)

// Modal state
const showModal = ref(false)
const isEditing = ref(false)
const saving = ref(false)
const form = ref({
  id: null as number | null,
  name: '', email: '', password: '',
  phone: '', address: '',
  role: 'Student',
  parent_id: '' as any,   // Dùng khi role = Student
  child_ids: [] as number[], // Dùng khi role = Parent
})

// Computed
const isParentRole = computed(() => form.value.role === 'Parent')
const isStudentRole = computed(() => form.value.role === 'Student')

const showToast = (msg: string, type = 'success') => {
  toast.value = { msg, type }
  setTimeout(() => { toast.value = null }, 3500)
}

// Lấy tên phụ huynh từ parent_id
const getParentName = (parentId: number | null) =>
  parentsList.value.find(p => p.id === parentId)?.name ?? '—'

// Toggle học sinh trong danh sách child_ids
const toggleChild = (id: number) => {
  const idx = form.value.child_ids.indexOf(id)
  if (idx === -1) form.value.child_ids.push(id)
  else form.value.child_ids.splice(idx, 1)
}

// Load dữ liệu
const loadAll = async () => {
  loading.value = true
  try {
    const [u, s, p] = await Promise.all([
      fetchApi(`/admin/users${filterRole.value ? '?role=' + filterRole.value : ''}`),
      fetchApi('/admin/students-list'),
      fetchApi('/admin/parents-list'),
    ])
    users.value = u.data
    allStudents.value = s.data
    parentsList.value = p.data
  } catch (e: any) {
    showToast(e.message, 'error')
  } finally {
    loading.value = false
  }
}

// Mở modal tạo mới
const openCreate = () => {
  isEditing.value = false
  form.value = {
    id: null, name: '', email: '', password: '',
    phone: '', address: '', role: 'Student',
    parent_id: '', child_ids: [],
  }
  showModal.value = true
}

// Mở modal sửa
const openEdit = (user: any) => {
  isEditing.value = true
  form.value = {
    id: user.id,
    name: user.name,
    email: user.email,
    password: '',
    phone: user.phone || '',
    address: user.address || '',
    role: user.roles[0] || 'Student',
    parent_id: user.parent_id || '',
    // Nếu là Parent, lấy danh sách id các con đã có
    child_ids: (user.children || []).map((c: any) => c.id),
  }
  showModal.value = true
}

// Lưu
const save = async () => {
  saving.value = true
  try {
    const payload: any = {
      name: form.value.name,
      email: form.value.email,
      phone: form.value.phone,
      address: form.value.address,
      role: form.value.role,
    }
    if (form.value.password) payload.password = form.value.password

    // Student: gán parent_id
    if (isStudentRole.value) {
      payload.parent_id = form.value.parent_id || null
    } else {
      payload.parent_id = null
    }

    // Parent: gán child_ids
    if (isParentRole.value) {
      payload.child_ids = form.value.child_ids
    }

    if (isEditing.value && form.value.id) {
      await fetchApi(`/admin/users/${form.value.id}`, {
        method: 'PUT',
        body: JSON.stringify(payload),
      })
      showToast('✅ Cập nhật user thành công!')
    } else {
      await fetchApi('/admin/users', {
        method: 'POST',
        body: JSON.stringify(payload),
      })
      showToast('✅ Tạo user thành công!')
    }
    showModal.value = false
    await loadAll()
  } catch (e: any) {
    showToast(e.message, 'error')
  } finally {
    saving.value = false
  }
}

// Xóa
const deleteUser = async (user: any) => {
  if (!confirm(`Xóa user "${user.name}"?`)) return
  try {
    await fetchApi(`/admin/users/${user.id}`, { method: 'DELETE' })
    showToast('🗑️ Đã xóa user!')
    await loadAll()
  } catch (e: any) {
    showToast(e.message, 'error')
  }
}

// ----- EXPORT / IMPORT EXCEL -----
const importing = ref(false)

const exportExcel = async () => {
  const token = localStorage.getItem('token')
  importing.value = true // mượn tạm spinner
  try {
    const res = await fetch(`http://localhost:8000/api/admin/export?table=users`, {
      headers: { 
        Authorization: `Bearer ${token}`,
        Accept: 'application/json' 
      }
    })
    if (!res.ok) throw new Error('Không thể xuất file. Lỗi API.')
    
    // Convert to Blob for downloading
    const blob = await res.blob()
    const url = window.URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = `users_export_${Date.now()}.xlsx`
    a.click()
    window.URL.revokeObjectURL(url)
  } catch (e: any) {
    showToast(e.message, 'error')
  } finally {
    importing.value = false
  }
}

const importExcel = async (event: any) => {
  const file = event.target.files[0]
  if (!file) return

  const formData = new FormData()
  formData.append('table', 'users')
  formData.append('file', file)

  importing.value = true
  const token = localStorage.getItem('token')
  try {
    const res = await fetch('http://localhost:8000/api/admin/import', {
      method: 'POST',
      headers: { Authorization: `Bearer ${token}` },
      body: formData
    })
    const body = await res.json()
    if (res.ok) {
      showToast('📥 Nhập dữ liệu thành công!')
      await loadAll()
    } else {
      showToast(body.message || 'Lỗi nhập dữ liệu', 'error')
    }
  } catch (e: any) {
    showToast(e.message, 'error')
  } finally {
    importing.value = false
    event.target.value = '' // reset
  }
}

onMounted(loadAll)
</script>

<template>
  <AppLayout>
    <div class="page">
      <!-- Header -->
      <div class="page-header">
        <div>
          <h1 class="page-title">👥 Quản Lý Users</h1>
          <p class="text-muted">Tạo, sửa, xóa tài khoản và quản lý liên kết Phụ huynh – Học sinh.</p>
        </div>
        <div style="display:flex; gap:12px">
          <!-- IMPORT EXCEL -->
          <label class="btn btn-outline" style="cursor:pointer" :class="{'disabled': importing}">
            <span v-if="importing" class="spinner"></span>
            {{ importing ? 'Đang nhập...' : '📥 Nhập Excel' }}
            <input type="file" style="display:none" accept=".xlsx,.xls,.csv" @change="importExcel" :disabled="importing">
          </label>
          <!-- EXPORT EXCEL -->
          <button class="btn btn-outline" @click="exportExcel">📤 Xuất Excel</button>

          <button id="create-user-btn" class="btn btn-primary" @click="openCreate">+ Thêm User</button>
        </div>
      </div>

      <!-- Filter -->
      <div class="filter-bar card">
        <label class="form-label" style="margin:0">Lọc vai trò:</label>
        <select class="form-input" style="width:190px" v-model="filterRole" @change="loadAll">
          <option value="">Tất cả</option>
          <option value="Admin">Admin</option>
          <option value="Teacher">Giáo viên</option>
          <option value="Student">Học sinh</option>
          <option value="Parent">Phụ huynh</option>
        </select>
        <span class="result-count">{{ users.length }} kết quả</span>
      </div>

      <!-- Table -->
      <div class="card mt-4">
        <div v-if="loading" class="loading-center"><div class="spinner-blue"></div></div>
        <div v-else-if="!users.length" class="empty-state">Không có user nào.</div>
        <div v-else class="table-wrapper">
          <table class="table">
            <thead>
              <tr>
                <th>Họ Tên</th>
                <th>Email</th>
                <th>Vai Trò</th>
                <th>Số ĐT</th>
                <th>Liên Kết Gia Đình</th>
                <th>Ngày Tạo</th>
                <th>Hành Động</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in users" :key="user.id">
                <td class="fw-bold">{{ user.name }}</td>
                <td class="text-muted">{{ user.email }}</td>
                <td>
                  <span class="badge"
                    :class="user.roles[0] === 'Admin' ? 'badge-admin'
                      : user.roles[0] === 'Teacher' ? 'badge-teacher'
                      : user.roles[0] === 'Parent' ? 'badge-parent'
                      : 'badge-student'">
                    {{ user.roles[0] === 'Parent' ? '👨‍👩‍👧 Phụ Huynh'
                       : user.roles[0] === 'Teacher' ? '🧑‍🏫 Giáo Viên'
                       : user.roles[0] === 'Student' ? '📚 Học Sinh'
                       : user.roles[0] || '—' }}
                  </span>
                </td>
                <td>{{ user.phone || '—' }}</td>
                <!-- Cột liên kết gia đình -->
                <td>
                  <!-- Nếu là Phụ huynh: hiện danh sách con -->
                  <div v-if="user.roles[0] === 'Parent'">
                    <div v-if="user.children && user.children.length" class="family-tags">
                      <span v-for="child in user.children" :key="child.id" class="family-tag family-tag--child">
                        📚 {{ child.name }}
                      </span>
                    </div>
                    <span v-else class="text-muted" style="font-size:13px">Chưa có học sinh</span>
                  </div>
                  <!-- Nếu là Học sinh: hiện tên phụ huynh -->
                  <div v-else-if="user.roles[0] === 'Student'">
                    <span v-if="user.parent_id" class="family-tag family-tag--parent">
                      👨‍👩‍👧 {{ getParentName(user.parent_id) }}
                    </span>
                    <span v-else class="text-muted" style="font-size:13px">—</span>
                  </div>
                  <span v-else class="text-muted">—</span>
                </td>
                <td class="text-muted">{{ new Date(user.created_at).toLocaleDateString('vi-VN') }}</td>
                <td>
                  <div class="action-btns">
                    <button class="btn btn-outline btn-sm" @click="router.push('/admin/users/' + user.id)">ℹ️ Chi Tiết</button>
                    <button class="btn btn-outline btn-sm" @click="openEdit(user)">✏️ Sửa</button>
                    <button class="btn btn-danger btn-sm" @click="deleteUser(user)">🗑️ Xóa</button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ===== MODAL ===== -->
    <Teleport to="body">
      <div v-if="showModal" class="modal-backdrop" @click.self="showModal = false">
        <div class="modal" :class="isParentRole ? 'modal-large' : ''" id="user-modal">
          <div class="modal-header">
            <h3>{{ isEditing ? '✏️ Sửa User' : '➕ Thêm User Mới' }}</h3>
            <button class="modal-close" @click="showModal = false">✕</button>
          </div>

          <div class="modal-body">
            <!-- Thông tin cơ bản -->
            <div class="section-label">Thông tin tài khoản</div>
            <div class="form-grid">
              <div class="form-group">
                <label class="form-label">Họ tên *</label>
                <input class="form-input" v-model="form.name" placeholder="Nguyễn Văn A" />
              </div>
              <div class="form-group">
                <label class="form-label">Email *</label>
                <input class="form-input" type="email" v-model="form.email" placeholder="user@example.com" />
              </div>
              <div class="form-group">
                <label class="form-label">
                  Mật khẩu {{ isEditing ? '(để trống nếu không đổi)' : '*' }}
                </label>
                <input class="form-input" type="password" v-model="form.password" placeholder="••••••" />
              </div>
              <div class="form-group">
                <label class="form-label">Vai trò *</label>
                <select class="form-input" v-model="form.role">
                  <option value="Admin">🔐 Admin</option>
                  <option value="Teacher">🧑‍🏫 Giáo viên</option>
                  <option value="Student">📚 Học sinh</option>
                  <option value="Parent">👨‍👩‍👧 Phụ huynh</option>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Số điện thoại</label>
                <input class="form-input" v-model="form.phone" placeholder="0901234567" />
              </div>
              <div class="form-group">
                <label class="form-label">Địa chỉ</label>
                <input class="form-input" v-model="form.address" placeholder="123 Nguyễn Trãi, Quận 1..." />
              </div>
            </div>

            <!-- === Học sinh: Chọn phụ huynh === -->
            <template v-if="isStudentRole">
              <div class="divider"></div>
              <div class="section-label">👨‍👩‍👧 Liên kết Phụ huynh</div>
              <div class="form-group" style="margin-top:12px">
                <label class="form-label">Phụ huynh quản lý học sinh này</label>
                <select class="form-input" v-model="form.parent_id">
                  <option value="">— Không có —</option>
                  <option v-for="p in parentsList" :key="p.id" :value="p.id">
                    {{ p.name }} ({{ p.email }})
                  </option>
                </select>
                <p class="hint-text">Phụ huynh sẽ theo dõi được lịch học và điểm danh của học sinh này.</p>
              </div>
            </template>

            <!-- === Phụ huynh: Chọn danh sách con === -->
            <template v-if="isParentRole">
              <div class="divider"></div>
              <div class="section-label">
                📚 Học sinh do phụ huynh này quản lý
                <span class="child-count-badge">{{ form.child_ids.length }} đã chọn</span>
              </div>
              <p class="hint-text" style="margin:8px 0 12px">
                Tick vào học sinh mà phụ huynh này sẽ theo dõi. Phụ huynh sẽ thấy lịch học, điểm danh và học phí của các con này.
              </p>

              <div v-if="!allStudents.length" class="empty-state" style="padding:16px">
                Chưa có học sinh nào trong hệ thống.
              </div>

              <div v-else class="students-checklist">
                <label
                  v-for="student in allStudents"
                  :key="student.id"
                  class="student-check-item"
                  :class="{ checked: form.child_ids.includes(student.id) }"
                  @click="toggleChild(student.id)"
                >
                  <div class="check-box">
                    <span v-if="form.child_ids.includes(student.id)">✓</span>
                  </div>
                  <div class="check-avatar">{{ student.name.charAt(0) }}</div>
                  <div class="check-info">
                    <div class="check-name">{{ student.name }}</div>
                    <div class="check-email">{{ student.email }}</div>
                  </div>
                  <!-- Hiện phụ huynh hiện tại nếu có -->
                  <div v-if="student.parent_id && student.parent_id !== form.id" class="current-parent-tag">
                    ⚠️ Đang dùng PH khác
                  </div>
                </label>
              </div>
            </template>
          </div>

          <div class="modal-footer">
            <button class="btn btn-outline" @click="showModal = false">Hủy</button>
            <button id="save-user-btn" class="btn btn-primary" :disabled="saving" @click="save">
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

.filter-bar { display:flex;align-items:center;gap:16px;padding:14px 20px; }
.result-count { margin-left:auto;color:var(--color-text-muted);font-size:13px; }

.loading-center { display:flex;justify-content:center;padding:40px; }
.spinner-blue { width:32px;height:32px;border:3px solid rgba(79,70,229,.2);border-top-color:#4f46e5;border-radius:50%;animation:spin .8s linear infinite; }
@keyframes spin { to{transform:rotate(360deg)} }
.empty-state { text-align:center;padding:48px;color:var(--color-text-muted); }
.action-btns { display:flex;gap:8px;flex-wrap:nowrap; }

/* Family column */
.family-tags { display:flex;flex-wrap:wrap;gap:4px; }
.family-tag { display:inline-block;padding:3px 8px;border-radius:999px;font-size:12px;font-weight:500; }
.family-tag--child { background:#dbeafe;color:#1d4ed8; }
.family-tag--parent { background:#fef3c7;color:#d97706; }

/* Modal */
.modal-backdrop { position:fixed;inset:0;background:rgba(15,23,42,.65);backdrop-filter:blur(4px);display:flex;align-items:center;justify-content:center;z-index:1000;padding:20px; }
.modal { background:#fff;border-radius:20px;width:100%;max-width:600px;max-height:90vh;display:flex;flex-direction:column;box-shadow:0 24px 60px rgba(0,0,0,.22);animation:modalIn .25s ease; }
.modal-large { max-width:720px; }
@keyframes modalIn { from{opacity:0;transform:scale(.95)} to{opacity:1;transform:scale(1)} }
.modal-header { display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid var(--color-border); }
.modal-header h3 { font-size:17px;font-weight:700; }
.modal-close { background:none;border:none;cursor:pointer;color:var(--color-text-muted);font-size:18px;padding:4px 8px;border-radius:6px;line-height:1; }
.modal-close:hover { background:#f1f5f9; }
.modal-body { padding:24px;overflow-y:auto;flex:1; }
.modal-footer { display:flex;gap:12px;justify-content:flex-end;padding:16px 24px;border-top:1px solid var(--color-border); }

.form-grid { display:grid;grid-template-columns:1fr 1fr;gap:16px; }
.divider { height:1px;background:var(--color-border);margin:20px 0; }
.section-label { font-size:13px;font-weight:700;color:var(--color-primary);text-transform:uppercase;letter-spacing:.06em;display:flex;align-items:center;gap:8px; }
.hint-text { font-size:12px;color:var(--color-text-muted);line-height:1.5; }

.child-count-badge { background:var(--color-primary);color:#fff;font-size:11px;padding:2px 8px;border-radius:999px;font-weight:600;margin-left:auto; }

/* Students checklist for Parent */
.students-checklist { display:flex;flex-direction:column;gap:6px;max-height:280px;overflow-y:auto;border:1px solid var(--color-border);border-radius:12px;padding:8px; }
.student-check-item {
  display:flex;align-items:center;gap:12px;padding:10px 12px;border-radius:10px;
  cursor:pointer;transition:background .15s;border:1.5px solid transparent;
}
.student-check-item:hover { background:#f8fafc; }
.student-check-item.checked { background:#eef2ff;border-color:var(--color-primary); }
.check-box {
  width:22px;height:22px;border-radius:6px;border:2px solid #cbd5e1;
  display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;
  color:#fff;flex-shrink:0;transition:all .15s;
}
.checked .check-box { background:var(--color-primary);border-color:var(--color-primary); }
.check-avatar {
  width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#4f46e5,#06b6d4);
  color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;
  font-size:15px;flex-shrink:0;
}
.check-info { flex:1;min-width:0; }
.check-name { font-size:14px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
.check-email { font-size:12px;color:var(--color-text-muted); }
.current-parent-tag { font-size:11px;color:#d97706;background:#fef3c7;padding:2px 8px;border-radius:999px;white-space:nowrap;font-weight:500; }

@media (max-width:768px) {
  .page { padding:16px; }
  .form-grid { grid-template-columns:1fr; }
}
</style>

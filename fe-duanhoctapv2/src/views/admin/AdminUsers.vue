<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useApi } from '../../utils/api'
import AppLayout from '../../components/AppLayout.vue'

const { fetchApi } = useApi()
const users = ref<any[]>([])
const loading = ref(true)
const toast = ref<{ msg: string; type: string } | null>(null)

// Modal
const showModal = ref(false)
const isEditing = ref(false)
const saving = ref(false)
const form = ref({ id: null as number | null, name: '', email: '', password: '', phone: '', address: '', role: 'Student' })
const filterRole = ref('')

const showToast = (msg: string, type = 'success') => {
  toast.value = { msg, type }
  setTimeout(() => { toast.value = null }, 3500)
}

const loadUsers = async () => {
  loading.value = true
  try {
    const res = await fetchApi(`/admin/users${filterRole.value ? '?role=' + filterRole.value : ''}`)
    users.value = res.data
  } catch (e: any) {
    showToast(e.message, 'error')
  } finally {
    loading.value = false
  }
}

const openCreate = () => {
  isEditing.value = false
  form.value = { id: null, name: '', email: '', password: '', phone: '', address: '', role: 'Student' }
  showModal.value = true
}

const openEdit = (user: any) => {
  isEditing.value = true
  form.value = { id: user.id, name: user.name, email: user.email, password: '', phone: user.phone || '', address: user.address || '', role: user.roles[0] || 'Student' }
  showModal.value = true
}

const save = async () => {
  saving.value = true
  try {
    const payload: any = { name: form.value.name, email: form.value.email, phone: form.value.phone, address: form.value.address, role: form.value.role }
    if (form.value.password) payload.password = form.value.password

    if (isEditing.value && form.value.id) {
      await fetchApi(`/admin/users/${form.value.id}`, { method: 'PUT', body: JSON.stringify(payload) })
      showToast('✅ Cập nhật user thành công!')
    } else {
      payload.password = form.value.password
      await fetchApi('/admin/users', { method: 'POST', body: JSON.stringify(payload) })
      showToast('✅ Tạo user thành công!')
    }
    showModal.value = false
    await loadUsers()
  } catch (e: any) {
    showToast(e.message, 'error')
  } finally {
    saving.value = false
  }
}

const deleteUser = async (user: any) => {
  if (!confirm(`Xóa user "${user.name}"?`)) return
  try {
    await fetchApi(`/admin/users/${user.id}`, { method: 'DELETE' })
    showToast('🗑️ Đã xóa user!')
    await loadUsers()
  } catch (e: any) {
    showToast(e.message, 'error')
  }
}

onMounted(loadUsers)
</script>

<template>
  <AppLayout>
    <div class="page">
      <div class="page-header">
        <div>
          <h1 class="page-title">👥 Quản Lý Users</h1>
          <p class="text-muted">Tạo, sửa, xóa tài khoản cho Admin, Giáo viên và Học sinh.</p>
        </div>
        <button id="create-user-btn" class="btn btn-primary" @click="openCreate">+ Thêm User</button>
      </div>

      <!-- Filter -->
      <div class="filter-bar card">
        <label class="form-label" style="margin:0">Lọc theo vai trò:</label>
        <select class="form-input" style="width:200px" v-model="filterRole" @change="loadUsers">
          <option value="">Tất cả</option>
          <option value="Admin">Admin</option>
          <option value="Teacher">Giáo viên</option>
          <option value="Student">Học sinh</option>
        </select>
        <span class="result-count">{{ users.length }} kết quả</span>
      </div>

      <!-- Table -->
      <div class="card mt-4">
        <div v-if="loading" class="loading-center">
          <div class="spinner-blue"></div>
        </div>
        <div v-else-if="!users.length" class="empty-state">Không có user nào.</div>
        <div v-else class="table-wrapper">
          <table class="table">
            <thead>
              <tr>
                <th>Họ Tên</th>
                <th>Email</th>
                <th>Số Điện Thoại</th>
                <th>Địa Chỉ</th>
                <th>Vai Trò</th>
                <th>Ngày Tạo</th>
                <th>Hành Động</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in users" :key="user.id">
                <td class="fw-bold">{{ user.name }}</td>
                <td class="text-muted">{{ user.email }}</td>
                <td>{{ user.phone || '—' }}</td>
                <td>{{ user.address || '—' }}</td>
                <td>
                  <span class="badge" :class="user.roles[0] === 'Admin' ? 'badge-admin' : user.roles[0] === 'Teacher' ? 'badge-teacher' : 'badge-student'">
                    {{ user.roles[0] || '—' }}
                  </span>
                </td>
                <td class="text-muted">{{ new Date(user.created_at).toLocaleDateString('vi-VN') }}</td>
                <td>
                  <div class="action-btns">
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

    <!-- Modal -->
    <Teleport to="body">
      <div v-if="showModal" class="modal-backdrop" @click.self="showModal = false">
        <div class="modal" id="user-modal">
          <div class="modal-header">
            <h3>{{ isEditing ? '✏️ Sửa User' : '➕ Thêm User Mới' }}</h3>
            <button class="modal-close" @click="showModal = false">✕</button>
          </div>
          <div class="modal-body">
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
                <label class="form-label">Mật khẩu {{ isEditing ? '(để trống nếu không đổi)' : '*' }}</label>
                <input class="form-input" type="password" v-model="form.password" placeholder="••••••" />
              </div>
              <div class="form-group">
                <label class="form-label">Vai trò *</label>
                <select class="form-input" v-model="form.role">
                  <option value="Admin">Admin</option>
                  <option value="Teacher">Giáo viên</option>
                  <option value="Student">Học sinh</option>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Số điện thoại</label>
                <input class="form-input" v-model="form.phone" placeholder="0901234567" />
              </div>
              <div class="form-group" style="grid-column:span 2">
                <label class="form-label">Địa chỉ</label>
                <input class="form-input" v-model="form.address" placeholder="123 Đường ABC, Quận 1, TP.HCM" />
              </div>
            </div>
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
.filter-bar { display:flex;align-items:center;gap:16px; padding:14px 20px; }
.result-count { margin-left:auto;color:var(--color-text-muted);font-size:13px; }
.loading-center { display:flex;justify-content:center;padding:40px; }
.spinner-blue { width:32px;height:32px;border:3px solid rgba(79,70,229,.2);border-top-color:#4f46e5;border-radius:50%;animation:spin .8s linear infinite; }
@keyframes spin { to{transform:rotate(360deg)} }
.empty-state { text-align:center;padding:48px;color:var(--color-text-muted); }
.action-btns { display:flex;gap:8px; }
.form-grid { display:grid;grid-template-columns:1fr 1fr;gap:16px; }
/* Modal reuse global styles */
.modal-backdrop { position:fixed;inset:0;background:rgba(15,23,42,.6);backdrop-filter:blur(4px);display:flex;align-items:center;justify-content:center;z-index:1000;padding:20px; }
.modal { background:#fff;border-radius:20px;width:100%;max-width:600px;max-height:90vh;display:flex;flex-direction:column;box-shadow:0 24px 60px rgba(0,0,0,.2);animation:modalIn .25s ease; }
@keyframes modalIn { from{opacity:0;transform:scale(.95)} to{opacity:1;transform:scale(1)} }
.modal-header { display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid var(--color-border); }
.modal-header h3 { font-size:17px;font-weight:700; }
.modal-close { background:none;border:none;cursor:pointer;color:var(--color-text-muted);font-size:18px;padding:4px 8px;border-radius:6px; }
.modal-body { padding:24px;overflow-y:auto; }
.modal-footer { display:flex;gap:12px;justify-content:flex-end;padding:16px 24px;border-top:1px solid var(--color-border); }
</style>

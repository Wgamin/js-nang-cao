<script setup>
import { ref, computed } from 'vue'

// --- State ---
const users = ref([
  { id: 1, name: 'Ngô Kiến Huy', initials: 'KH', avatarColor: 'indigo', email: 'kienhuy.admin@edumanager.vn', role: 'Quản trị viên', roleClass: 'purple', status: 'active', lastLogin: '2 giờ trước' },
  { id: 2, name: 'Phan Lệ Thu', initials: 'LT', avatarColor: 'rose', email: 'thu.phan@edumanager.vn', role: 'Quản lý Đào tạo', roleClass: 'blue', status: 'active', lastLogin: '10 phút trước' },
  { id: 3, name: 'Vũ Quốc Toản', initials: 'QT', avatarColor: 'blue', email: 'toan.vu@edumanager.vn', role: 'Giáo vụ', roleClass: 'orange', status: 'locked', lastLogin: '3 ngày trước' },
  { id: 4, name: 'Lâm Vỹ Dạ', initials: 'VD', avatarColor: 'orange', email: 'vyda.lam@edumanager.vn', role: 'Kế toán', roleClass: 'emerald', status: 'active', lastLogin: 'Vừa xong' },
])

const isModalOpen = ref(false)
const isEditing = ref(false)
const searchQuery = ref('')
const roleFilter = ref('')

const initialUser = {
  id: null,
  name: '',
  email: '',
  password: '',
  role: 'Nhân viên',
  status: 'active'
}

const currentUser = ref({ ...initialUser })

// --- Computed ---
const filteredUsers = computed(() => {
  return users.value.filter(u => {
    const matchesSearch = u.name.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
                         u.email.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchesRole = !roleFilter.value || u.role === roleFilter.value
    return matchesSearch && matchesRole
  })
})

const quickStats = computed(() => [
  { label: 'Tổng người dùng', value: users.value.length, icon: 'shield_person', color: '#1d4ed8', bg: 'rgba(29,78,216,0.1)' },
  { label: 'Quản trị viên', value: users.value.filter(u => u.role === 'Quản trị viên').length, icon: 'admin_panel_settings', color: '#8b5cf6', bg: 'rgba(139,92,246,0.1)' },
  { label: 'Nhân viên', value: users.value.filter(u => u.role !== 'Quản trị viên').length, icon: 'support_agent', color: '#f59e0b', bg: 'rgba(245,158,11,0.1)' },
  { label: 'Đang khóa', value: users.value.filter(u => u.status === 'locked').length, icon: 'lock', color: '#ef4444', bg: 'rgba(239,68,68,0.1)' },
])

// --- Actions ---
function openAddModal() {
  isEditing.value = false
  currentUser.value = { ...initialUser }
  isModalOpen.value = true
}

function openEditModal(user) {
  isEditing.value = true
  currentUser.value = { ...user }
  isModalOpen.value = true
}

function closeModal() {
  isModalOpen.value = false
}

function saveUser() {
  if (isEditing.value) {
    const index = users.value.findIndex(u => u.id === currentUser.value.id)
    if (index !== -1) {
      // Preserve avatar details if editing
      const roleClassMap = {
        'Quản trị viên': 'purple',
        'Quản lý Đào tạo': 'blue',
        'Giáo vụ': 'orange',
        'Kế toán': 'emerald',
        'Nhân viên': 'blue'
      }
      users.value[index] = { 
        ...currentUser.value,
        roleClass: roleClassMap[currentUser.value.role] || 'blue'
      }
    }
  } else {
    const newId = users.value.length ? Math.max(...users.value.map(u => u.id)) + 1 : 1
    const nameParts = currentUser.value.name.split(' ')
    const initials = nameParts.length > 1 ? nameParts[0][0] + nameParts[nameParts.length - 1][0] : nameParts[0][0]
    const roleClassMap = {
      'Quản trị viên': 'purple',
      'Quản lý Đào tạo': 'blue',
      'Giáo vụ': 'orange',
      'Kế toán': 'emerald',
      'Nhân viên': 'blue'
    }
    
    users.value.push({
      ...currentUser.value,
      id: newId,
      initials: initials.toUpperCase(),
      avatarColor: ['blue', 'orange', 'rose', 'indigo'][Math.floor(Math.random() * 4)],
      roleClass: roleClassMap[currentUser.value.role] || 'blue',
      lastLogin: 'Chưa bao giờ'
    })
  }
  closeModal()
}

function toggleLockStatus(user) {
  user.status = user.status === 'active' ? 'locked' : 'active'
}

function deleteUser(id) {
  if (confirm('Bạn có chắc chắn muốn xóa người dùng này?')) {
    users.value = users.value.filter(u => u.id !== id)
  }
}

const getRoleClass = (role) => {
  const map = {
    'Quản trị viên': 'role-purple',
    'Quản lý Đào tạo': 'role-blue',
    'Giáo vụ': 'role-orange',
    'Kế toán': 'role-emerald',
    'Nhân viên': 'role-blue'
  }
  return map[role] || 'role-blue'
}
</script>

<template>
  <div class="page-container">
    <header class="page-header">
      <div>
        <h1 class="page-title">Quản lý Người dùng</h1>
        <p class="page-subtitle">Quản trị phân quyền và bảo mật tài khoản hệ thống</p>
      </div>
      <button class="btn-primary" @click="openAddModal">
        <span class="material-symbols-outlined">person_add</span>
        Thêm Người dùng
      </button>
    </header>

    <!-- Quick Stats -->
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
        <!-- Search & Filter -->
        <div class="filter-group">
          <div class="search-box">
            <span class="material-symbols-outlined search-icon">search</span>
            <input v-model="searchQuery" type="text" class="search-input" placeholder="Tìm kiếm theo tên, email..." />
          </div>
          
          <div class="select-box">
            <select v-model="roleFilter" class="form-select">
              <option value="">Tất cả Vai trò</option>
              <option value="Quản trị viên">Quản trị viên</option>
              <option value="Quản lý Đào tạo">Quản lý Đào tạo</option>
              <option value="Giáo vụ">Giáo vụ</option>
              <option value="Kế toán">Kế toán</option>
            </select>
            <span class="material-symbols-outlined select-icon">expand_more</span>
          </div>
        </div>

        <!-- Export / Utility -->
        <div class="action-group">
          <button class="btn-secondary">
            <span class="material-symbols-outlined">security</span>
            Phân quyền
          </button>
        </div>
      </div>

      <!-- Table -->
      <div class="table-responsive">
        <table class="data-table">
          <thead>
            <tr>
              <th>NGƯỜI DÙNG</th>
              <th>VAI TRÒ</th>
              <th>ĐĂNG NHẬP LẦN CUỐI</th>
              <th>TRẠNG THÁI</th>
              <th class="text-right">THAO TÁC</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in filteredUsers" :key="user.id">
              <td>
                <div class="user-cell">
                  <div class="avatar" :class="'avatar-' + user.avatarColor">{{ user.initials }}</div>
                  <div>
                    <h4 class="user-name">{{ user.name }}</h4>
                    <span class="user-email">{{ user.email }}</span>
                  </div>
                </div>
              </td>
              <td>
                <span class="role-badge" :class="getRoleClass(user.role)">{{ user.role }}</span>
              </td>
              <td>
                <div class="login-info">
                  <span class="material-symbols-outlined icon-small">schedule</span>
                  <span>{{ user.lastLogin }}</span>
                </div>
              </td>
              <td>
                <span class="badge" :class="user.status === 'active' ? 'badge-success' : 'badge-warning'">
                  {{ user.status === 'active' ? 'Hoạt động' : 'Tạm khóa' }}
                </span>
              </td>
              <td class="text-right">
                <button class="icon-btn" title="Chỉnh sửa & Gán vai trò" @click="openEditModal(user)">
                  <span class="material-symbols-outlined">manage_accounts</span>
                </button>
                <button class="icon-btn" :class="{ 'text-danger': user.status === 'active' }" 
                        :title="user.status === 'active' ? 'Khóa tài khoản' : 'Mở khóa tài khoản'"
                        @click="toggleLockStatus(user)">
                  <span class="material-symbols-outlined">{{ user.status === 'active' ? 'lock' : 'lock_open' }}</span>
                </button>
                <button class="icon-btn icon-delete" title="Xóa người dùng" @click="deleteUser(user.id)">
                  <span class="material-symbols-outlined">delete</span>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="pagination-footer">
        <span class="page-info">Hiển thị {{ filteredUsers.length }} trên {{ users.length }} người dùng</span>
        <div class="pagination">
          <button class="page-btn"><span class="material-symbols-outlined">chevron_left</span></button>
          <button class="page-btn active">1</button>
          <button class="page-btn"><span class="material-symbols-outlined">chevron_right</span></button>
        </div>
      </div>
    </div>

    <!-- Modals -->
    <Transition name="fade">
      <div v-if="isModalOpen" class="modal-overlay" @click.self="closeModal">
        <!-- Add User Modal Content -->
        <div v-if="!isEditing" class="modal-content premium-modal">
          <div class="modal-header">
            <div class="header-info">
              <h2 class="modal-title">Thêm người dùng mới</h2>
              <p class="modal-subtitle">Điền đầy đủ thông tin để cấp quyền truy cập cho thành viên mới.</p>
            </div>
            <button class="close-btn" @click="closeModal">
              <span class="material-symbols-outlined">close</span>
            </button>
          </div>

          <form @submit.prevent="saveUser">
            <div class="modal-body">
              <div class="form-group">
                <label>HỌ VÀ TÊN</label>
                <div class="input-wrapper">
                  <span class="material-symbols-outlined input-icon">person</span>
                  <input v-model="currentUser.name" type="text" placeholder="Nhập họ và tên đầy đủ" required />
                </div>
              </div>

              <div class="form-group">
                <label>EMAIL LIÊN HỆ</label>
                <div class="input-wrapper">
                  <span class="material-symbols-outlined input-icon">mail</span>
                  <input v-model="currentUser.email" type="email" placeholder="example@scholarflow.vn" required />
                </div>
              </div>

              <div class="form-row">
                <div class="form-group">
                  <label>MẬT KHẨU</label>
                  <div class="input-wrapper">
                    <span class="material-symbols-outlined input-icon">lock</span>
                    <input v-model="currentUser.password" type="password" placeholder="••••••••" required />
                  </div>
                </div>
                <div class="form-group">
                  <label>VAI TRÒ</label>
                  <div class="select-wrapper">
                    <span class="material-symbols-outlined select-icon">badge</span>
                    <select v-model="currentUser.role">
                      <option value="Nhân viên">Nhân viên</option>
                      <option value="Giáo vụ">Giáo vụ</option>
                      <option value="Quản lý Đào tạo">Quản lý Đào tạo</option>
                      <option value="Quản trị viên">Quản trị viên</option>
                    </select>
                    <span class="material-symbols-outlined expand-icon">expand_more</span>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label>TRẠNG THÁI TÀI KHOẢN</label>
                <div class="radio-group">
                  <label class="radio-item">
                    <input type="radio" v-model="currentUser.status" value="active" />
                    <span class="radio-mark"></span>
                    Đang hoạt động
                  </label>
                  <label class="radio-item">
                    <input type="radio" v-model="currentUser.status" value="locked" />
                    <span class="radio-mark"></span>
                    Đang khóa
                  </label>
                </div>
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn-cancel" @click="closeModal">Hủy bỏ</button>
              <button type="submit" class="btn-submit">Tạo tài khoản</button>
            </div>
          </form>
        </div>

        <!-- Edit User Modal Content -->
        <div v-else class="modal-content premium-modal edit-modal">
          <div class="modal-header">
            <div class="header-info">
              <h2 class="modal-title">Chỉnh sửa & Gán vai trò</h2>
            </div>
            <button class="close-btn" @click="closeModal">
              <span class="material-symbols-outlined">close</span>
            </button>
          </div>

          <form @submit.prevent="saveUser">
            <div class="modal-body">
              <!-- User Profile Card in Modal -->
              <div class="user-profile-summary">
                <div class="profile-avatar" :class="'avatar-' + currentUser.avatarColor">
                  {{ currentUser.initials }}
                </div>
                <div class="profile-info">
                  <h3 class="profile-name">{{ currentUser.name }}</h3>
                  <span class="profile-id">ID: ADM-99231</span>
                </div>
              </div>

              <div class="form-group">
                <label>HỌ VÀ TÊN</label>
                <div class="input-wrapper">
                  <input v-model="currentUser.name" type="text" />
                  <span class="material-symbols-outlined trailing-icon">edit</span>
                </div>
              </div>

              <div class="form-group">
                <label>EMAIL</label>
                <div class="input-wrapper disabled">
                  <input :value="currentUser.email" type="email" disabled />
                  <span class="material-symbols-outlined trailing-icon">lock</span>
                </div>
              </div>

              <div class="form-row">
                <div class="form-group flex-1">
                  <label>THAY ĐỔI VAI TRÒ</label>
                  <div class="select-wrapper">
                    <select v-model="currentUser.role">
                      <option value="Nhân viên">Nhân viên</option>
                      <option value="Giáo vụ">Giáo vụ</option>
                      <option value="Quản lý Đào tạo">Quản lý Đào tạo</option>
                      <option value="Quản trị viên">Quản trị viên</option>
                    </select>
                    <span class="material-symbols-outlined expand-icon">expand_more</span>
                  </div>
                </div>
                <div class="form-group flex-1">
                  <label>CẬP NHẬT TRẠNG THÁI</label>
                  <div class="status-toggle-wrapper">
                    <span>{{ currentUser.status === 'active' ? 'Đang hoạt động' : 'Tạm khóa' }}</span>
                    <label class="switch">
                      <input type="checkbox" :checked="currentUser.status === 'active'" @change="currentUser.status = $event.target.checked ? 'active' : 'locked'" />
                      <span class="slider round"></span>
                    </label>
                  </div>
                </div>
              </div>

              <div class="info-box">
                <span class="material-symbols-outlined info-icon">info</span>
                <p>Thay đổi vai trò sẽ ảnh hưởng đến quyền truy cập các tính năng của người dùng này trong hệ thống <strong>ScholarFlow</strong>.</p>
              </div>
            </div>

            <div class="modal-footer justify-center">
              <button type="button" class="btn-outline-rounded" @click="closeModal">Quay lại</button>
              <button type="submit" class="btn-primary-rounded shadow">Lưu thay đổi</button>
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
  padding: 0 4px;
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
  transition: all 0.2s;
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

.stat-info {
  display: flex;
  flex-direction: column;
}

.stat-label {
  font-size: 12px;
  font-weight: 700;
  color: var(--on-surface-variant);
  text-transform: uppercase;
  margin-bottom: 2px;
}

.stat-value {
  font-family: var(--font-outfit);
  font-size: 24px;
  font-weight: 800;
  color: var(--on-surface);
}

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

.filter-group {
  display: flex;
  gap: 16px;
}

.search-box {
  position: relative;
  width: 320px;
}

.search-icon {
  position: absolute;
  left: 14px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--outline);
  font-size: 20px;
}

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

.search-input:focus {
  border-color: var(--primary);
  box-shadow: 0 0 0 4px rgba(0, 74, 198, 0.08);
}

.select-box {
  position: relative;
}

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
  transition: all 0.2s;
}

.form-select:hover {
  border-color: var(--outline);
}

.select-icon {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--outline);
  pointer-events: none;
}

/* Table */
.table-responsive {
  width: 100%;
  overflow-x: auto;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
}

.data-table th {
  text-align: left;
  padding: 16px 24px;
  font-size: 11px;
  font-weight: 800;
  color: var(--outline);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  background-color: var(--surface-container-low);
  border-bottom: 1px solid var(--surface-container);
}

.data-table td {
  padding: 16px 24px;
  border-bottom: 1px solid var(--surface-container);
  vertical-align: middle;
}

.data-table tr:hover td {
  background-color: var(--surface-container-lowest);
}

.user-cell {
  display: flex;
  align-items: center;
  gap: 12px;
}

.avatar {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 800;
  font-size: 15px;
  font-family: var(--font-outfit);
}

.avatar-blue { background: #dbeafe; color: #1e40af; }
.avatar-orange { background: #ffedd5; color: #9a3412; }
.avatar-rose { background: #ffe4e6; color: #e11d48; }
.avatar-indigo { background: #e0e7ff; color: #3730a3; }

.user-name {
  margin: 0 0 2px 0;
  font-size: 15px;
  font-weight: 700;
  color: var(--on-surface);
}

.user-email {
  font-size: 13px;
  color: var(--on-surface-variant);
}

.role-badge {
  font-size: 11px;
  font-weight: 800;
  padding: 6px 12px;
  border-radius: 8px;
  text-transform: uppercase;
  letter-spacing: 0.02em;
}

.role-purple { background-color: #f3e8ff; color: #7e22ce; }
.role-blue { background-color: #dbeafe; color: #1d4ed8; }
.role-orange { background-color: #ffedd5; color: #c2410c; }
.role-emerald { background-color: #d1fae5; color: #047857; }

.login-info {
  display: flex;
  align-items: center;
  gap: 6px;
  color: var(--on-surface-variant);
  font-size: 13px;
  font-weight: 500;
}

.badge {
  padding: 6px 14px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 700;
}

.badge-success { background-color: #dcfce7; color: #15803d; }
.badge-warning { background-color: #fee2e2; color: #b91c1c; }

.icon-btn {
  background: transparent;
  border: none;
  cursor: pointer;
  color: var(--outline);
  padding: 10px;
  border-radius: 10px;
  transition: all 0.2s;
  display: inline-flex;
}

.icon-btn:hover {
  background-color: var(--surface-container);
  color: var(--primary);
}

.icon-delete:hover {
  background-color: #fef2f2;
  color: var(--error);
}

/* Pagination */
.pagination-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 24px;
}

.page-info {
  font-size: 14px;
  font-weight: 600;
  color: var(--on-surface-variant);
}

.pagination {
  display: flex;
  gap: 6px;
}

.page-btn {
  min-width: 38px;
  height: 38px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1.5px solid var(--outline-variant);
  background-color: white;
  color: var(--on-surface-variant);
  border-radius: 10px;
  font-weight: 700;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.2s;
}

.page-btn:hover {
  border-color: var(--outline);
}

.page-btn.active {
  background-color: var(--primary);
  color: white;
  border-color: var(--primary);
}

/* Premium Modals */
.modal-overlay {
  position: fixed;
  inset: 0;
  background-color: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(8px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.premium-modal {
  background: white;
  width: 100%;
  max-width: 520px;
  border-radius: 32px;
  box-shadow: var(--shadow-xl);
  border: 1px solid rgba(255, 255, 255, 0.4);
  overflow: hidden;
  animation: modalIn 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes modalIn {
  from { transform: scale(0.95) translateY(20px); opacity: 0; }
  to { transform: scale(1) translateY(0); opacity: 1; }
}

.modal-header {
  padding: 32px 40px 16px 40px;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

.modal-title {
  font-family: var(--font-outfit);
  font-size: 24px;
  font-weight: 800;
  color: var(--on-surface);
  margin: 0 0 8px 0;
}

.modal-subtitle {
  font-size: 14px;
  color: var(--on-surface-variant);
  font-weight: 500;
  line-height: 1.6;
}

.close-btn {
  background: var(--surface-container);
  border: none;
  color: var(--outline);
  cursor: pointer;
  padding: 8px;
  border-radius: 12px;
  transition: all 0.2s;
}

.close-btn:hover {
  background-color: var(--surface-container-highest);
  color: var(--on-surface);
}

.modal-body {
  padding: 24px 40px 40px 40px;
}

.form-group {
  margin-bottom: 24px;
}

.form-group label {
  display: block;
  font-size: 11px;
  font-weight: 900;
  color: var(--outline);
  margin-bottom: 10px;
  letter-spacing: 0.1em;
}

.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.input-icon {
  position: absolute;
  left: 16px;
  color: var(--outline);
  font-size: 20px;
}

.input-wrapper input {
  width: 100%;
  background-color: var(--surface-container-lowest);
  border: 1.5px solid var(--outline-variant);
  padding: 14px 16px 14px 48px;
  border-radius: 16px;
  font-size: 15px;
  font-weight: 600;
  color: var(--on-surface);
  outline: none;
  transition: all 0.3s;
}

.edit-modal .input-wrapper input {
  padding-left: 16px;
}

.input-wrapper input:focus {
  border-color: var(--primary);
  background-color: white;
  box-shadow: 0 0 0 5px rgba(0, 74, 198, 0.08);
}

.input-wrapper.disabled input {
  background-color: var(--surface-container-low);
  color: var(--outline);
  cursor: not-allowed;
}

.trailing-icon {
  position: absolute;
  right: 16px;
  color: var(--outline-variant);
}

.select-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.select-wrapper select {
  width: 100%;
  appearance: none;
  background-color: var(--surface-container-lowest);
  border: 1.5px solid var(--outline-variant);
  padding: 14px 48px 14px 48px;
  border-radius: 16px;
  font-size: 15px;
  font-weight: 600;
  color: var(--on-surface);
  outline: none;
  cursor: pointer;
}

.edit-modal .select-wrapper select {
  padding-left: 16px;
}

.expand-icon {
  position: absolute;
  right: 16px;
  color: var(--outline);
}

/* Profile Card in Edit Modal */
.user-profile-summary {
  background: linear-gradient(to right, var(--surface-container-low), var(--surface-container-lowest));
  border-radius: 20px;
  padding: 20px;
  display: flex;
  align-items: center;
  gap: 20px;
  margin-bottom: 28px;
  border: 1px solid var(--surface-container);
}

.profile-avatar {
  width: 64px;
  height: 64px;
  border-radius: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  font-weight: 900;
  box-shadow: var(--shadow-md);
}

.profile-name {
  font-family: var(--font-outfit);
  font-size: 18px;
  font-weight: 800;
  color: var(--on-surface);
  margin-bottom: 4px;
}

.profile-id {
  font-size: 13px;
  font-weight: 700;
  color: var(--outline);
}

.status-toggle-wrapper {
  background-color: white;
  border: 1.5px solid var(--outline-variant);
  padding: 12px 20px;
  border-radius: 16px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 14px;
  font-weight: 700;
  color: var(--on-surface-variant);
}

.info-box {
  background-color: #eff6ff;
  border-radius: 16px;
  padding: 16px 20px;
  display: flex;
  gap: 16px;
  margin-top: 12px;
  border: 1px solid #dbeafe;
}

.info-icon { color: var(--primary); }

.info-box p {
  margin: 0;
  font-size: 13px;
  color: #1e40af;
  line-height: 1.6;
  font-weight: 500;
}

.modal-footer {
  padding: 0 40px 40px 40px;
  display: flex;
  justify-content: flex-end;
  gap: 16px;
}

.justify-center { justify-content: center; }

.btn-cancel {
  background: transparent;
  color: var(--outline);
  font-weight: 800;
  border: none;
  cursor: pointer;
  font-size: 15px;
}

.btn-submit {
  background-color: var(--primary);
  color: white;
  border: none;
  padding: 14px 32px;
  border-radius: 16px;
  font-weight: 800;
  font-size: 15px;
  cursor: pointer;
  box-shadow: 0 8px 16px rgba(0, 74, 198, 0.25);
  transition: all 0.3s;
}

.btn-submit:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 24px rgba(0, 74, 198, 0.3);
}

.btn-outline-rounded {
  background: white;
  border: 2px solid var(--outline-variant);
  color: var(--on-surface-variant);
  padding: 14px 36px;
  border-radius: 100px;
  font-weight: 800;
  cursor: pointer;
}

.btn-primary-rounded {
  background-color: var(--primary);
  color: white;
  border: none;
  padding: 14px 40px;
  border-radius: 100px;
  font-weight: 800;
  cursor: pointer;
  box-shadow: var(--shadow-lg);
}

/* Radio Custom */
.radio-group { display: flex; gap: 32px; }
.radio-item { display: flex; align-items: center; gap: 12px; font-weight: 700; color: var(--on-surface-variant); cursor: pointer; }
.radio-mark { width: 22px; height: 22px; border: 2.5px solid var(--outline-variant); border-radius: 50%; position: relative; }
input:checked + .radio-mark { border-color: var(--primary); }
input:checked + .radio-mark::after { content: ''; position: absolute; inset: 4px; background: var(--primary); border-radius: 50%; }

/* Switch Custom */
.switch { position: relative; width: 48px; height: 26px; }
.slider { position: absolute; inset: 0; background-color: var(--outline-variant); border-radius: 34px; cursor: pointer; transition: 0.4s; }
.slider:before { position: absolute; content: ""; height: 20px; width: 20px; left: 3px; bottom: 3px; background-color: white; border-radius: 50%; transition: 0.4s; }
input:checked + .slider { background-color: var(--primary); }
input:checked + .slider:before { transform: translateX(22px); }

/* Custom Colors for text */
.text-danger { color: var(--error) !important; }
</style>


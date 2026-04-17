<script setup>
import { ref, computed } from 'vue'

// --- State ---
const guardians = ref([
  { id: 1, name: 'Nguyễn Văn Hùng', initials: 'VH', avatarColor: 'blue', phone: '0987654321', email: 'hung.nv@gmail.com', relation: 'Bố', students: ['Nguyễn Văn Hoàng (K24)'], connect: 'Zalo', status: 'active' },
  { id: 2, name: 'Lê Thị Mai', initials: 'TM', avatarColor: 'rose', phone: '0901234567', email: 'mai.le@yahoo.com', relation: 'Mẹ', students: ['Phạm Minh Tú (A02)', 'Phạm Bảo Hân (K10)'], connect: 'App Giáo Dục', status: 'active' },
  { id: 3, name: 'Trần Quốc Anh', initials: 'QA', avatarColor: 'indigo', phone: '0912223333', email: 'anh.tran@outlook.com', relation: 'Bố', students: ['Trần Linh Anh (Nghỉ)'], connect: 'SMS', status: 'inactive' },
  { id: 4, name: 'Bùi Thị Hà', initials: 'TH', avatarColor: 'orange', phone: '0934567890', email: 'ha.bui@gmail.com', relation: 'Bà', students: ['Lê Duy Kiên (K10)'], connect: 'Cuộc gọi', status: 'active' },
])

const isModalOpen = ref(false)
const isEditing = ref(false)
const searchQuery = ref('')
const connectFilter = ref('')

const initialGuardian = {
  id: null,
  name: '',
  phone: '',
  email: '',
  relation: 'Bố',
  connect: 'Zalo',
  status: 'active',
  students: []
}

const currentGuardian = ref({ ...initialGuardian })

// --- Computed ---
const filteredGuardians = computed(() => {
  return guardians.value.filter(g => {
    const matchesSearch = g.name.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
                         g.phone.includes(searchQuery.value) ||
                         g.email.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchesConnect = !connectFilter.value || g.connect === connectFilter.value
    return matchesSearch && matchesConnect
  })
})

const quickStats = computed(() => [
  { label: 'Tổng Phụ Huynh', value: guardians.value.length, icon: 'escalator_warning', color: '#2563eb', bg: '#eff6ff' },
  { label: 'Đang hoạt động', value: guardians.value.filter(g => g.status === 'active').length, icon: 'how_to_reg', color: '#10b981', bg: '#ecfdf5' },
  { label: 'Kênh App Edu', value: guardians.value.filter(g => g.connect === 'App Giáo Dục').length, icon: 'app_shortcut', color: '#8b5cf6', bg: '#f5f3ff' },
  { label: 'Cần cập nhật', value: guardians.value.filter(g => !g.email).length, icon: 'priority_high', color: '#ef4444', bg: '#fef2f2' },
])

// --- Actions ---
function openAddModal() {
  isEditing.value = false
  currentGuardian.value = { ...initialGuardian }
  isModalOpen.value = true
}

function openEditModal(guardian) {
  isEditing.value = true
  currentGuardian.value = { ...guardian }
  isModalOpen.value = true
}

function closeModal() {
  isModalOpen.value = false
}

function saveGuardian() {
  if (isEditing.value) {
    const index = guardians.value.findIndex(g => g.id === currentGuardian.value.id)
    if (index !== -1) {
      guardians.value[index] = { ...currentGuardian.value }
    }
  } else {
    const newId = guardians.value.length ? Math.max(...guardians.value.map(g => g.id)) + 1 : 1
    const nameParts = currentGuardian.value.name.split(' ')
    const initials = nameParts.length > 1 ? nameParts[0][0] + nameParts[nameParts.length - 1][0] : nameParts[0][0]
    
    guardians.value.push({
      ...currentGuardian.value,
      id: newId,
      initials: initials.toUpperCase(),
      avatarColor: ['blue', 'orange', 'rose', 'indigo'][Math.floor(Math.random() * 4)],
      students: [] // Initially no students linked
    })
  }
  closeModal()
}

function deleteGuardian(id) {
  if (confirm('Bạn có chắc chắn muốn xóa phụ huynh này?')) {
    guardians.value = guardians.value.filter(g => g.id !== id)
  }
}

const getAvatarColor = (id) => {
  const colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444']
  return colors[id % 4]
}
</script>

<template>
  <div class="admin-page">
    <div class="headers">
      <div class="header-left">
        <h1 class="title">Quản lý Phụ huynh</h1>
        <p class="subtitle">Quản lý thông tin liên hệ và kết nối với gia đình học viên</p>
      </div>
      <button class="btn btn-primary" @click="openAddModal">
        <span class="material-symbols-outlined">person_add</span>
        Thêm Phụ Huynh
      </button>
    </div>

    <!-- Quick Stats -->
    <div class="stats-container">
      <div v-for="stat in quickStats" :key="stat.label" class="stat-card">
        <div class="stat-icon-box" :style="{ color: stat.color, backgroundColor: stat.bg }">
          <span class="material-symbols-outlined">{{ stat.icon }}</span>
        </div>
        <div class="stat-content">
          <p class="stat-label">{{ stat.label }}</p>
          <h3 class="stat-value">{{ stat.value }}</h3>
        </div>
      </div>
    </div>

    <!-- Main Card -->
    <div class="content-box">
      <div class="toolbar">
        <div class="search-wrapper">
          <span class="material-symbols-outlined search-icon">search</span>
          <input v-model="searchQuery" type="text" class="input-search" placeholder="Tìm tên, SĐT hoặc email..." />
        </div>
        
        <div class="filters">
          <div class="select-wrapper">
            <select v-model="connectFilter" class="select-filter">
              <option value="">Kênh trao đổi</option>
              <option value="App Giáo Dục">App Giáo dục</option>
              <option value="Zalo">Zalo</option>
              <option value="SMS">SMS</option>
              <option value="Cuộc gọi">Cuộc gọi</option>
            </select>
            <span class="material-symbols-outlined select-arrow">expand_more</span>
          </div>
          
          <button class="btn btn-secondary">
            <span class="material-symbols-outlined">mail</span>
            Gửi thông báo loạt
          </button>
        </div>
      </div>

      <!-- Table -->
      <div class="table-container">
        <table class="user-table">
          <thead>
            <tr>
              <th style="width: 30%">Thành viên gia đình</th>
              <th style="width: 15%">Quan hệ</th>
              <th style="width: 25%">Học viên liên kết</th>
              <th style="width: 15%">Kết nối</th>
              <th style="width: 15%" class="text-right">Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="guardian in filteredGuardians" :key="guardian.id" class="table-row">
              <td>
                <div class="user-info">
                  <div class="avatar" :style="{ backgroundColor: getAvatarColor(guardian.id) }">{{ guardian.initials }}</div>
                  <div class="user-text">
                    <p class="user-full-name">{{ guardian.name }}</p>
                    <p class="user-email-text">{{ guardian.phone }} • {{ guardian.email }}</p>
                  </div>
                </div>
              </td>
              <td>
                <span class="relation-badge">{{ guardian.relation }}</span>
              </td>
              <td>
                <div class="student-links">
                  <div v-for="student in guardian.students" :key="student" class="student-tag">
                    <span class="material-symbols-outlined">school</span>
                    {{ student }}
                  </div>
                  <span v-if="guardian.students.length === 0" class="empty-text">Chưa liên kết học viên</span>
                </div>
              </td>
              <td>
                <div class="connect-badge" :data-type="guardian.connect">
                  <span class="material-symbols-outlined">
                    {{ guardian.connect === 'Zalo' ? 'chat' : guardian.connect === 'SMS' ? 'textsms' : guardian.connect === 'Cuộc gọi' ? 'call' : 'smartphone' }}
                  </span>
                  {{ guardian.connect }}
                </div>
              </td>
              <td class="text-right">
                <div class="actions">
                  <button class="action-btn" title="Chỉnh sửa" @click="openEditModal(guardian)">
                    <span class="material-symbols-outlined">edit</span>
                  </button>
                  <button class="action-btn" title="Lịch sử liên hệ">
                    <span class="material-symbols-outlined">history</span>
                  </button>
                  <button class="action-btn btn-delete" title="Xóa" @click="deleteGuardian(guardian.id)">
                    <span class="material-symbols-outlined">delete</span>
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="filteredGuardians.length === 0">
              <td colspan="5" class="empty-state">
                Không tìm thấy phụ huynh nào phù hợp.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Footer -->
      <div class="table-footer">
        <p>Hiển thị <strong>{{ filteredGuardians.length }}</strong> phụ huynh</p>
      </div>
    </div>

    <!-- Guardian Modal -->
    <Transition name="modal-fade">
      <div v-if="isModalOpen" class="modal-backdrop" @click.self="closeModal">
        <div class="modal-box">
          <div class="modal-header">
            <div class="modal-header-text">
              <h2 class="modal-title">{{ isEditing ? 'Cập nhật thông tin Phụ huynh' : 'Thêm Phụ Huynh mới' }}</h2>
              <p class="modal-subtitle">Vui lòng nhập chính xác thông tin để duy trì kênh liên lạc với gia đình.</p>
            </div>
            <button class="btn-close-minimal" @click="closeModal" title="Đóng">
              <span class="material-symbols-outlined">close</span>
            </button>
          </div>

          <form @submit.prevent="saveGuardian">
            <div class="modal-body">
              <div class="form-grid">
                <div class="form-item span-2">
                  <label class="form-label">Họ và tên Phụ huynh</label>
                  <input v-model="currentGuardian.name" type="text" placeholder="Ví dụ: Nguyễn Văn Hoàng" required class="form-input" />
                </div>

                <div class="form-item">
                  <label class="form-label">Số điện thoại</label>
                  <input v-model="currentGuardian.phone" type="tel" placeholder="09xx xxx xxx" required class="form-input" />
                </div>
                
                <div class="form-item">
                  <label class="form-label">Mối quan hệ</label>
                  <div class="select-container">
                    <select v-model="currentGuardian.relation" class="form-select">
                      <option value="Bố">Bố</option>
                      <option value="Mẹ">Mẹ</option>
                      <option value="Ông">Ông</option>
                      <option value="Bà">Bà</option>
                      <option value="Người giám hộ">Người giám hộ</option>
                    </select>
                    <span class="material-symbols-outlined select-icon-minimal">expand_more</span>
                  </div>
                </div>

                <div class="form-item span-2">
                  <label class="form-label">Địa chỉ Email</label>
                  <input v-model="currentGuardian.email" type="email" placeholder="example@gmail.com" class="form-input" />
                </div>

                <div class="form-item">
                  <label class="form-label">Kênh kết nối chính</label>
                  <div class="select-container">
                    <select v-model="currentGuardian.connect" class="form-select">
                      <option value="App Giáo Dục">App Giáo Dục</option>
                      <option value="Zalo">Zalo</option>
                      <option value="SMS">SMS</option>
                      <option value="Cuộc gọi">Cuộc gọi</option>
                    </select>
                    <span class="material-symbols-outlined select-icon-minimal">expand_more</span>
                  </div>
                </div>
                
                <div class="form-item">
                  <label class="form-label">Trạng thái</label>
                  <div class="status-box">
                    <div class="status-label">{{ currentGuardian.status === 'active' ? 'Đang hoạt động' : 'Ngừng liên hệ' }}</div>
                    <div class="toggle-group">
                      <label class="switch-flat">
                        <input type="checkbox" :checked="currentGuardian.status === 'active'" @change="currentGuardian.status = $event.target.checked ? 'active' : 'inactive'" />
                        <span class="slider-flat"></span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="modal-footer-minimal">
              <button type="button" class="btn btn-simple" @click="closeModal">Hủy bỏ</button>
              <button type="submit" class="btn btn-solid-primary">{{ isEditing ? 'Lưu thay đổi' : 'Tạo phụ huynh' }}</button>
            </div>
          </form>
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
.btn-refresh { width: 40px; height: 40px; padding: 0; justify-content: center; background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; color: #64748b; display: flex; align-items: center; cursor: pointer; transition: 0.2s;}
.btn-refresh:hover { background-color: #f8fafc; color: #2563eb; border-color: #cbd5e1; }

/* Stats Cards */
.stats-container { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 28px; }
.stat-card { background-color: #ffffff; padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 16px; border: 1px solid #f1f5f9; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px -1px rgba(0, 0, 0, 0.05); transition: transform 0.2s ease; }
.stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
.stat-icon-box { width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.stat-icon-box .material-symbols-outlined { font-size: 24px; }
.stat-label { font-size: 13px; font-weight: 600; color: #64748b; margin: 0 0 2px 0; }
.stat-value { font-size: 22px; font-weight: 700; color: #0f172a; margin: 0; }

/* Content Box */
.content-box { background-color: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05); overflow: hidden; }

/* Toolbar */
.toolbar { padding: 20px 24px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; gap: 16px; flex-wrap: wrap; }
.search-wrapper { position: relative; flex: 1; max-width: 400px; }
.search-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 20px; }
.input-search { width: 100%; padding: 10px 12px 10px 40px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; background-color: #f8fafc; transition: all 0.2s; outline: none; }
.input-search:focus { background-color: #ffffff; border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }

.filters { display: flex; gap: 12px; align-items: center; }
.select-wrapper { position: relative; min-width: 180px; }
.select-filter { width: 100%; appearance: none; padding: 10px 36px 10px 14px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; font-weight: 500; color: #475569; background-color: #ffffff; cursor: pointer; outline: none; }
.select-arrow { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; }

/* Table */
.table-container { min-height: 300px; position: relative; }
.user-table { width: 100%; border-collapse: collapse; }
.user-table th { text-align: left; padding: 14px 24px; font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.025em; background-color: #f8fafc; border-bottom: 1px solid #f1f5f9; }
.table-row { transition: background-color 0.2s ease; }
.table-row:hover { background-color: #f8fafc; }
.user-table td { padding: 16px 24px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }

/* Common Typo */
.user-info { display: flex; align-items: center; gap: 12px; }
.avatar { width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #ffffff; font-weight: 700; font-size: 14px; }
.user-full-name { font-size: 14px; font-weight: 600; color: #1e293b; margin: 0; }
.user-email-text { font-size: 12px; color: #64748b; margin: 0; }

/* Customs for Guardians */
.relation-badge { font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 6px; background-color: #f8fafc; color: #475569; border: 1px solid #e2e8f0; }

.student-links { display: flex; flex-direction: column; gap: 4px; }
.student-tag { display: inline-flex; align-items: center; gap: 4px; font-size: 12px; font-weight: 600; color: #2563eb; background: #eff6ff; padding: 3px 8px; border-radius: 6px; width: fit-content; }
.student-tag .material-symbols-outlined { font-size: 14px; }
.empty-text { font-size: 12px; font-style: italic; color: #94a3b8; }

.connect-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 6px; background: #f8fafc; color: #1e293b; border: 1px solid #e2e8f0; }
.connect-badge .material-symbols-outlined { font-size: 14px; }

/* Badges */
.badge { display: inline-flex; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; }
.badge-success { background-color: #ecfdf5; color: #10b981; }
.badge-warning { background-color: #fffbeb; color: #f59e0b; }
.badge-danger { background-color: #fef2f2; color: #ef4444; }

/* Actions */
.actions { display: flex; justify-content: flex-end; gap: 4px; }
.action-btn { width: 34px; height: 34px; border-radius: 6px; border: 1px solid transparent; background-color: transparent; color: #64748b; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; }
.action-btn:hover { background-color: #f1f5f9; color: #2563eb; }
.btn-delete:hover { color: #dc2626; background-color: #fef2f2; }
.action-btn .material-symbols-outlined { font-size: 20px; }
.text-right { text-align: right; }

/* Empty state & Footer */
.empty-state { text-align: center; padding: 60px 0; color: #94a3b8; }
.table-footer { padding: 16px 24px; font-size: 13px; color: #64748b; }

/* Modal Styles */
.modal-backdrop { position: fixed; inset: 0; background-color: rgba(15, 23, 42, 0.4); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 20px; }
.modal-box { background-color: #ffffff; width: 100%; max-width: 580px; border-radius: 16px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); border: 1px solid #f1f5f9; animation: modalIn 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
@keyframes modalIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
.modal-header { padding: 24px 28px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: flex-start; }
.modal-title { font-size: 18px; font-weight: 700; color: #0f172a; margin: 0 0 4px 0; }
.modal-subtitle { font-size: 13px; color: #64748b; margin: 0; }
.btn-close-minimal { background: transparent; border: none; color: #94a3b8; cursor: pointer; padding: 4px; border-radius: 6px; transition: all 0.2s; }
.btn-close-minimal:hover { background-color: #f1f5f9; color: #475569; }

/* Modal Body */
.modal-body { padding: 28px; }
.form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
.form-item.span-2 { grid-column: span 2; }
.form-label { display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 8px; }
.form-input, .form-select { width: 100%; padding: 10px 14px; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; color: #0f172a; outline: none; transition: all 0.2s; }
.form-input:focus, .form-select:focus { background-color: #ffffff; border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }

.select-container { position: relative; }
.select-icon-minimal { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; font-size: 20px; }
.form-select { appearance: none; padding-right: 40px; }

/* Status Box & Toggle Flat */
.status-box { background-color: #f8fafc; padding: 14px 18px; border-radius: 10px; border: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
.toggle-group { display: flex; align-items: center; gap: 12px; }
.switch-flat { position: relative; display: inline-block; width: 40px; height: 22px; }
.switch-flat input { opacity: 0; width: 0; height: 0; }
.slider-flat { position: absolute; cursor: pointer; inset: 0; background-color: #cbd5e1; transition: .2s; border-radius: 34px; }
.slider-flat:before { position: absolute; content: ""; height: 16px; width: 16px; left: 3px; bottom: 3px; background-color: white; transition: .2s; border-radius: 50%; }
input:checked + .slider-flat { background-color: #2563eb; }
input:checked + .slider-flat:before { transform: translateX(18px); }
.status-label { font-size: 14px; font-weight: 600; color: #475569; }

/* Modal Footer */
.modal-footer-minimal { padding: 20px 28px; background-color: #f8fafc; border-top: 1px solid #f1f5f9; border-radius: 0 0 16px 16px; display: flex; justify-content: flex-end; gap: 12px; }
.btn-simple { background-color: transparent; border: 1px solid #e2e8f0; color: #475569; font-weight: 600; padding: 10px 18px; border-radius: 8px; cursor: pointer;}
.btn-simple:hover { background-color: #f1f5f9; border-color: #cbd5e1; }
.btn-solid-primary { background-color: #2563eb; color: #ffffff; border: none; font-weight: 600; padding: 10px 18px; border-radius: 8px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); cursor: pointer;}
.btn-solid-primary:hover { background-color: #1d4ed8; }

.modal-fade-enter-active, .modal-fade-leave-active { transition: opacity 0.2s ease; }
.modal-fade-enter-from, .modal-fade-leave-to { opacity: 0; }
</style>

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
  { label: 'Tổng Phụ Huynh', value: guardians.value.length, icon: 'escalator_warning', color: '#1d4ed8', bg: 'rgba(29,78,216,0.1)' },
  { label: 'Đang hoạt động', value: guardians.value.filter(g => g.status === 'active').length, icon: 'how_to_reg', color: '#10b981', bg: 'rgba(16,185,129,0.1)' },
  { label: 'Kênh App Edu', value: guardians.value.filter(g => g.connect === 'App Giáo Dục').length, icon: 'app_shortcut', color: '#8b5cf6', bg: 'rgba(139,92,246,0.1)' },
  { label: 'Cần cập nhật', value: guardians.value.filter(g => !g.email).length, icon: 'priority_high', color: '#ef4444', bg: 'rgba(239,68,68,0.1)' },
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
</script>

<template>
  <div class="page-container">
    <header class="page-header">
      <div>
        <h1 class="page-title">Quản lý Phụ huynh</h1>
        <p class="page-subtitle">Quản lý thông tin liên hệ và kết nối với gia đình học viên</p>
      </div>
      <button class="btn-primary" @click="openAddModal">
        <span class="material-symbols-outlined">person_add</span>
        Thêm Phụ Huynh
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
            <input v-model="searchQuery" type="text" class="search-input" placeholder="Tìm tên, SĐT hoặc email..." />
          </div>
          
          <div class="select-box">
            <select v-model="connectFilter" class="form-select">
              <option value="">Kênh trao đổi</option>
              <option value="App Giáo Dục">App Giáo dục</option>
              <option value="Zalo">Zalo</option>
              <option value="SMS">SMS</option>
              <option value="Cuộc gọi">Cuộc gọi</option>
            </select>
            <span class="material-symbols-outlined select-icon">expand_more</span>
          </div>
        </div>

        <div class="action-group">
          <button class="btn-secondary">
            <span class="material-symbols-outlined">mail</span>
            Gửi thông báo loạt
          </button>
          <button class="btn-secondary">
            <span class="material-symbols-outlined">download</span>
            Xuất dữ liệu
          </button>
        </div>
      </div>

      <!-- Table -->
      <div class="table-responsive">
        <table class="data-table">
          <thead>
            <tr>
              <th>THÀNH VIÊN GIA ĐÌNH</th>
              <th>QUAN HỆ</th>
              <th>HỌC VIÊN LIÊN KẾT</th>
              <th>KẾT NỐI</th>
              <th class="text-right">THAO TÁC</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="guardian in filteredGuardians" :key="guardian.id">
              <td>
                <div class="user-cell">
                  <div class="avatar" :class="'avatar-' + guardian.avatarColor">{{ guardian.initials }}</div>
                  <div>
                    <h4 class="user-name">{{ guardian.name }}</h4>
                    <span class="user-subinfo">{{ guardian.phone }} • {{ guardian.email }}</span>
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
                <button class="icon-btn" title="Chỉnh sửa" @click="openEditModal(guardian)">
                  <span class="material-symbols-outlined">edit</span>
                </button>
                <button class="icon-btn" title="Lịch sử liên hệ">
                  <span class="material-symbols-outlined">history</span>
                </button>
                <button class="icon-btn text-danger" title="Xóa" @click="deleteGuardian(guardian.id)">
                  <span class="material-symbols-outlined">delete</span>
                </button>
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

      <!-- Pagination -->
      <div class="pagination-footer">
        <span class="page-info">Hiển thị {{ filteredGuardians.length }} trên {{ guardians.length }} phụ huynh</span>
        <div class="pagination">
          <button class="page-btn"><span class="material-symbols-outlined">chevron_left</span></button>
          <button class="page-btn active">1</button>
          <button class="page-btn"><span class="material-symbols-outlined">chevron_right</span></button>
        </div>
      </div>
    </div>

    <!-- Guardian Modal -->
    <Transition name="fade">
      <div v-if="isModalOpen" class="modal-overlay" @click.self="closeModal">
        <div class="modal-content premium-modal">
          <div class="modal-header">
            <div class="header-info">
              <h2 class="modal-title">{{ isEditing ? 'Cập nhật thông tin Phụ huynh' : 'Thêm Phụ Huynh mới' }}</h2>
              <p class="modal-subtitle">Vui lòng nhập chính xác thông tin để duy trì kênh liên lạc với gia đình.</p>
            </div>
            <button class="close-btn" @click="closeModal">
              <span class="material-symbols-outlined">close</span>
            </button>
          </div>

          <form @submit.prevent="saveGuardian">
            <div class="modal-body">
              <div class="form-group">
                <label>HỌ VÀ TÊN PHỤ HUYNH</label>
                <div class="input-wrapper">
                  <span class="material-symbols-outlined input-icon">person</span>
                  <input v-model="currentGuardian.name" type="text" placeholder="Ví dụ: Nguyễn Văn Hoàng" required />
                </div>
              </div>

              <div class="form-row">
                <div class="form-group flex-1">
                  <label>SỐ ĐIỆN THOẠI</label>
                  <div class="input-wrapper">
                    <span class="material-symbols-outlined input-icon">call</span>
                    <input v-model="currentGuardian.phone" type="tel" placeholder="09xx xxx xxx" required />
                  </div>
                </div>
                <div class="form-group flex-1">
                  <label>MỐI QUAN HỆ</label>
                  <div class="select-wrapper">
                    <select v-model="currentGuardian.relation">
                      <option value="Bố">Bố</option>
                      <option value="Mẹ">Mẹ</option>
                      <option value="Ông">Ông</option>
                      <option value="Bà">Bà</option>
                      <option value="Người giám hộ">Người giám hộ</option>
                    </select>
                    <span class="material-symbols-outlined expand-icon">expand_more</span>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label>ĐỊA CHỈ EMAIL</label>
                <div class="input-wrapper">
                  <span class="material-symbols-outlined input-icon">mail</span>
                  <input v-model="currentGuardian.email" type="email" placeholder="example@gmail.com" />
                </div>
              </div>

              <div class="form-row">
                <div class="form-group flex-1">
                  <label>KÊNH KẾT NỐI CHÍNH</label>
                  <div class="select-wrapper">
                    <select v-model="currentGuardian.connect">
                      <option value="App Giáo Dục">App Giáo Dục</option>
                      <option value="Zalo">Zalo</option>
                      <option value="SMS">SMS</option>
                      <option value="Cuộc gọi">Cuộc gọi</option>
                    </select>
                    <span class="material-symbols-outlined expand-icon">expand_more</span>
                  </div>
                </div>
                <div class="form-group flex-1">
                  <label>TRẠNG THÁI</label>
                  <div class="status-toggle-wrapper">
                    <span>{{ currentGuardian.status === 'active' ? 'Hoạt động' : 'Ngừng liên hệ' }}</span>
                    <label class="switch">
                      <input type="checkbox" :checked="currentGuardian.status === 'active'" @change="currentGuardian.status = $event.target.checked ? 'active' : 'inactive'" />
                      <span class="slider round"></span>
                    </label>
                  </div>
                </div>
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn-cancel" @click="closeModal">Hủy bỏ</button>
              <button type="submit" class="btn-submit">{{ isEditing ? 'Lưu thay đổi' : 'Tạo phụ huynh' }}</button>
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

.stat-info { display: flex; flex-direction: column; }
.stat-label { font-size: 12px; font-weight: 700; color: var(--on-surface-variant); text-transform: uppercase; margin-bottom: 2px; }
.stat-value { font-family: var(--font-outfit); font-size: 24px; font-weight: 800; color: var(--on-surface); }

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

.filter-group { display: flex; gap: 16px; }
.search-box { position: relative; width: 320px; }
.search-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--outline); font-size: 20px; }
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
.search-input:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(0, 74, 198, 0.08); }

.select-box { position: relative; }
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
}
.select-icon { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: var(--outline); pointer-events: none; }

.action-group { display: flex; gap: 12px; }

/* Table */
.table-responsive { width: 100%; overflow-x: auto; }
.data-table { width: 100%; border-collapse: collapse; }
.data-table th {
  text-align: left;
  padding: 16px 24px;
  font-size: 11px;
  font-weight: 800;
  color: var(--outline);
  text-transform: uppercase;
  letter-spacing: 0.1em;
  background-color: var(--surface-container-low);
  border-bottom: 1px solid var(--surface-container);
}
.data-table td { padding: 16px 24px; border-bottom: 1px solid var(--surface-container); vertical-align: middle; }
.data-table tr:hover td { background-color: var(--surface-container-lowest); }

.user-cell { display: flex; align-items: center; gap: 12px; }
.avatar { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 15px; font-family: var(--font-outfit); }
.avatar-blue { background: #dbeafe; color: #1e40af; }
.avatar-orange { background: #ffedd5; color: #9a3412; }
.avatar-rose { background: #ffe4e6; color: #e11d48; }
.avatar-indigo { background: #e0e7ff; color: #3730a3; }

.user-name { margin: 0 0 2px 0; font-size: 15px; font-weight: 700; color: var(--on-surface); }
.user-subinfo { font-size: 12px; color: var(--on-surface-variant); font-weight: 500; }

.relation-badge { font-size: 12px; font-weight: 700; padding: 4px 12px; border-radius: 8px; background-color: var(--surface-container); color: var(--on-surface-variant); border: 1px solid var(--outline-variant); }

.student-links { display: flex; flex-direction: column; gap: 6px; }
.student-tag { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600; color: var(--primary); background: #eff6ff; padding: 4px 10px; border-radius: 6px; border: 1px solid #dbeafe; width: fit-content; }
.student-tag .material-symbols-outlined { font-size: 14px; }
.empty-text { font-size: 12px; font-style: italic; color: var(--outline); }

.connect-badge { display: inline-flex; align-items: center; gap: 8px; font-size: 12px; font-weight: 700; padding: 6px 12px; border-radius: 8px; background: var(--surface-container-low); color: var(--on-surface); border: 1px solid var(--outline-variant); }
.connect-badge .material-symbols-outlined { font-size: 16px; }
.connect-badge[data-type="App Giáo Dục"] { background: #f3e8ff; color: #7e22ce; border-color: #e9d5ff; }
.connect-badge[data-type="Zalo"] { background: #dbeafe; color: #1d4ed8; border-color: #bfdbfe; }

.text-right { text-align: right; }
.text-danger { color: var(--error) !important; }

.icon-btn { background: transparent; border: none; cursor: pointer; color: var(--outline); padding: 10px; border-radius: 10px; transition: all 0.2s; display: inline-flex; }
.icon-btn:hover { background-color: var(--surface-container); color: var(--primary); }

.empty-state { padding: 40px; text-align: center; color: var(--outline); font-weight: 500; }

/* Modals */
.modal-overlay { position: fixed; inset: 0; background-color: rgba(0, 0, 0, 0.5); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; z-index: 1000; }
.premium-modal { background: white; width: 100%; max-width: 540px; border-radius: 32px; box-shadow: var(--shadow-xl); overflow: hidden; animation: modalIn 0.4s cubic-bezier(0.16, 1, 0.3, 1); }

@keyframes modalIn { from { transform: scale(0.95) translateY(20px); opacity: 0; } to { transform: scale(1) translateY(0); opacity: 1; } }

.modal-header { padding: 32px 40px 16px 40px; display: flex; justify-content: space-between; align-items: flex-start; }
.modal-title { font-family: var(--font-outfit); font-size: 24px; font-weight: 800; color: var(--on-surface); margin: 0 0 8px 0; }
.modal-subtitle { font-size: 14px; color: var(--on-surface-variant); font-weight: 500; line-height: 1.6; }
.close-btn { background: var(--surface-container); border: none; color: var(--outline); cursor: pointer; padding: 8px; border-radius: 12px; }

.modal-body { padding: 24px 40px 40px 40px; }
.form-group { margin-bottom: 24px; }
.form-group label { display: block; font-size: 11px; font-weight: 900; color: var(--outline); margin-bottom: 10px; letter-spacing: 0.1em; }
.form-row { display: flex; gap: 20px; }
.flex-1 { flex: 1; }

.input-wrapper { position: relative; display: flex; align-items: center; }
.input-icon { position: absolute; left: 16px; color: var(--outline); font-size: 20px; }
.input-wrapper input { width: 100%; background-color: var(--surface-container-lowest); border: 1.5px solid var(--outline-variant); padding: 14px 16px 14px 48px; border-radius: 16px; font-size: 15px; font-weight: 600; color: var(--on-surface); outline: none; transition: all 0.3s; }
.input-wrapper input:focus { border-color: var(--primary); box-shadow: 0 0 0 5px rgba(0, 74, 198, 0.08); }

.select-wrapper { position: relative; display: flex; align-items: center; }
.select-wrapper select { width: 100%; appearance: none; background-color: var(--surface-container-lowest); border: 1.5px solid var(--outline-variant); padding: 14px 40px 14px 16px; border-radius: 16px; font-size: 15px; font-weight: 600; color: var(--on-surface); outline: none; cursor: pointer; }
.expand-icon { position: absolute; right: 16px; color: var(--outline); pointer-events: none; }

.status-toggle-wrapper { background-color: white; border: 1.5px solid var(--outline-variant); padding: 12px 20px; border-radius: 16px; display: flex; justify-content: space-between; align-items: center; font-size: 14px; font-weight: 700; color: var(--on-surface-variant); }

.modal-footer { padding: 0 40px 40px 40px; display: flex; justify-content: flex-end; gap: 16px; }
.btn-cancel { background: transparent; color: var(--outline); font-weight: 800; border: none; cursor: pointer; font-size: 15px; }
.btn-submit { background-color: var(--primary); color: white; border: none; padding: 14px 32px; border-radius: 16px; font-weight: 800; font-size: 15px; cursor: pointer; box-shadow: 0 8px 16px rgba(0, 74, 198, 0.25); transition: all 0.3s; }
.btn-submit:hover { transform: translateY(-2px); box-shadow: 0 12px 24px rgba(0, 74, 198, 0.3); }

/* Switch */
.switch { position: relative; width: 44px; height: 24px; }
.switch input { opacity: 0; width: 0; height: 0; }
.slider { position: absolute; cursor: pointer; inset: 0; background-color: var(--outline-variant); border-radius: 34px; transition: .4s; }
.slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; border-radius: 50%; transition: .4s; }
input:checked + .slider { background-color: #10b981; }
input:checked + .slider:before { transform: translateX(20px); }

/* Transitions */
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

.pagination-footer { display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; }
.page-info { font-size: 14px; font-weight: 600; color: var(--on-surface-variant); }
.pagination { display: flex; gap: 6px; }
.page-btn { min-width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border: 1.5px solid var(--outline-variant); background-color: white; color: var(--on-surface-variant); border-radius: 10px; font-weight: 700; font-size: 14px; cursor: pointer; }
.page-btn.active { background-color: var(--primary); color: white; border-color: var(--primary); }
</style>

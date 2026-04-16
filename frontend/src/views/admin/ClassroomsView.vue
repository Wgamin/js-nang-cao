<script setup>
import { ref, computed } from 'vue'

// --- State ---
const classrooms = ref([
  { id: 1, code: 'P.302', building: 'Tòa A', capacity: 30, equipment: ['Máy chiếu', 'Bảng thông minh', 'Điều hòa'], status: 'Đang dùng', currentClass: 'Lớp ENG-101' },
  { id: 2, code: 'P.105', building: 'Tòa A', capacity: 20, equipment: ['Tivi', 'Bảng phấn', 'Điều hòa'], status: 'Đang dùng', currentClass: 'Lớp MATH-S' },
  { id: 3, code: 'Lab 02', building: 'Tòa B (Thực hành)', capacity: 40, equipment: ['40 Máy tính', 'Máy chiếu', 'Tai nghe'], status: 'Trống', currentClass: null },
  { id: 4, code: 'Art Studio', building: 'Tòa C', capacity: 15, equipment: ['Giá vẽ', 'Bồn rửa', 'Điều hòa'], status: 'Trống', currentClass: null },
  { id: 5, code: 'P.401', building: 'Tòa A', capacity: 25, equipment: ['Máy chiếu', 'Điều hòa'], status: 'Bảo trì', currentClass: null },
])

const isModalOpen = ref(false)
const isEditing = ref(false)
const searchQuery = ref('')
const statusFilter = ref('')
const buildingFilter = ref('')

const initialClassroom = {
  id: null,
  code: '',
  building: 'Tòa A',
  capacity: 20,
  equipment: [],
  status: 'Trống',
  currentClass: null
}

const currentClassroom = ref({ ...initialClassroom })

// --- Computed ---
const filteredClassrooms = computed(() => {
  return classrooms.value.filter(c => {
    const matchesSearch = c.code.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchesStatus = !statusFilter.value || c.status === statusFilter.value
    const matchesBuilding = !buildingFilter.value || c.building.includes(buildingFilter.value)
    return matchesSearch && matchesStatus && matchesBuilding
  })
})

const quickStats = computed(() => {
  const total = classrooms.value.length
  const using = classrooms.value.filter(c => c.status === 'Đang dùng').length
  const empty = classrooms.value.filter(c => c.status === 'Trống').length
  const maintenance = classrooms.value.filter(c => c.status === 'Bảo trì').length
  
  return [
    { label: 'Tổng Phòng Học', value: total, icon: 'meeting_room', color: '#1d4ed8', bg: 'rgba(29,78,216,0.1)' },
    { label: 'Đang Sử Dụng', value: using, icon: 'cast_for_education', color: '#f59e0b', bg: 'rgba(245,158,11,0.1)' },
    { label: 'Đang Trống', value: empty, icon: 'check_circle', color: '#10b981', bg: 'rgba(16,185,129,0.1)' },
    { label: 'Đang Bảo Trì', value: maintenance, icon: 'handyman', color: '#ef4444', bg: 'rgba(239,68,68,0.1)' },
  ]
})

// --- Actions ---
function openAddModal() {
  isEditing.value = false
  currentClassroom.value = { ...initialClassroom, equipment: [] }
  isModalOpen.value = true
}

function openEditModal(room) {
  isEditing.value = true
  currentClassroom.value = JSON.parse(JSON.stringify(room)) // Deep clone for array
  isModalOpen.value = true
}

function closeModal() {
  isModalOpen.value = false
}

function saveClassroom() {
  if (isEditing.value) {
    const index = classrooms.value.findIndex(c => c.id === currentClassroom.value.id)
    if (index !== -1) {
      classrooms.value[index] = { ...currentClassroom.value }
    }
  } else {
    const newId = classrooms.value.length ? Math.max(...classrooms.value.map(c => c.id)) + 1 : 1
    classrooms.value.push({
      ...currentClassroom.value,
      id: newId
    })
  }
  closeModal()
}

function deleteClassroom(id) {
  if (confirm('Bạn có chắc chắn muốn xóa phòng học này?')) {
    classrooms.value = classrooms.value.filter(c => c.id !== id)
  }
}

function getStatusClass(status) {
  if (status === 'Trống') return 'badge-success'
  if (status === 'Đang dùng') return 'badge-warning'
  return 'badge-danger'
}
</script>

<template>
  <div class="page-container">
    <header class="page-header">
      <div>
        <h1 class="page-title">Quản lý Phòng học</h1>
        <p class="page-subtitle">Kiểm soát tình trạng cơ sở vật chất và phân phối hạ tầng đào tạo</p>
      </div>
      <button class="btn-primary" @click="openAddModal">
        <span class="material-symbols-outlined">add_business</span>
        Thêm Phòng học
      </button>
    </header>

    <!-- Stats -->
    <div class="stats-grid">
      <div v-for="stat in quickStats" :key="stat.label" class="stat-card">
        <div class="stat-icon" :style="{ color: stat.color, backgroundColor: stat.bg }">
          <span class="material-symbols-outlined">{{ stat.icon }}</span>
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
        <div class="filter-group">
          <div class="search-box">
            <span class="material-symbols-outlined search-icon">search</span>
            <input v-model="searchQuery" type="text" class="search-input" placeholder="Tìm mã phòng..." />
          </div>
          
          <div class="select-box">
            <select v-model="statusFilter" class="form-select">
              <option value="">Tất cả Trạng thái</option>
              <option value="Trống">Trống</option>
              <option value="Đang dùng">Đang sử dụng</option>
              <option value="Bảo trì">Bảo trì</option>
            </select>
            <span class="material-symbols-outlined select-icon">expand_more</span>
          </div>

          <div class="select-box">
            <select v-model="buildingFilter" class="form-select">
              <option value="">Tòa nhà</option>
              <option value="A">Tòa A</option>
              <option value="B">Tòa B</option>
              <option value="C">Tòa C</option>
            </select>
            <span class="material-symbols-outlined select-icon">expand_more</span>
          </div>
        </div>

        <div class="action-group">
          <button class="btn-secondary">
            <span class="material-symbols-outlined">map</span>
            Sơ đồ phòng
          </button>
        </div>
      </div>

      <!-- Table -->
      <div class="table-responsive">
        <table class="data-table">
          <thead>
            <tr>
              <th>MÃ PHÒNG</th>
              <th>KHU VỰC / TÒA NHÀ</th>
              <th>SỨC CHỨA</th>
              <th>TRANG THIẾT BỊ</th>
              <th>TRẠNG THÁI & LỚP HỌC</th>
              <th class="text-right">THAO TÁC</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="room in filteredClassrooms" :key="room.id">
              <td>
                <span class="room-code">
                  <span class="material-symbols-outlined icon-small">sensor_door</span>
                  {{ room.code }}
                </span>
              </td>
              <td><span class="building-text">{{ room.building }}</span></td>
              <td>
                <div class="capacity-badge">
                  <span class="material-symbols-outlined">groups</span>
                  {{ room.capacity }} HV
                </div>
              </td>
              <td>
                <div class="equipment-stack">
                  <span v-for="item in room.equipment" :key="item" class="equip-tag">{{ item }}</span>
                  <span v-if="!room.equipment.length" class="empty-text">Chưa có thiết bị</span>
                </div>
              </td>
              <td>
                <div class="status-info">
                  <span class="badge" :class="getStatusClass(room.status)">{{ room.status }}</span>
                  <span v-if="room.currentClass" class="current-class-label">
                    <span class="material-symbols-outlined">play_lesson</span>
                    {{ room.currentClass }}
                  </span>
                </div>
              </td>
              <td class="text-right">
                <button class="icon-btn" title="Sửa phòng" @click="openEditModal(room)">
                  <span class="material-symbols-outlined">edit_square</span>
                </button>
                <button class="icon-btn text-danger" title="Xóa phòng" @click="deleteClassroom(room.id)">
                  <span class="material-symbols-outlined">delete</span>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="pagination-footer">
        <span class="page-info">Hiển thị {{ filteredClassrooms.length }} trên {{ classrooms.length }} phòng học</span>
        <div class="pagination">
          <button class="page-btn"><span class="material-symbols-outlined">chevron_left</span></button>
          <button class="page-btn active">1</button>
          <button class="page-btn"><span class="material-symbols-outlined">chevron_right</span></button>
        </div>
      </div>
    </div>

    <!-- Classroom Modal -->
    <Transition name="fade">
      <div v-if="isModalOpen" class="modal-overlay" @click.self="closeModal">
        <div class="modal-content premium-modal">
          <div class="modal-header">
            <div class="header-info">
              <h2 class="modal-title">{{ isEditing ? 'Cập Nhật Phòng Học' : 'Thêm Phòng Học Mới' }}</h2>
              <p class="modal-subtitle">Quản lý không gian và tiện nghi để tối ưu hóa trải nghiệm giảng dạy.</p>
            </div>
            <button class="close-btn" @click="closeModal">
              <span class="material-symbols-outlined">close</span>
            </button>
          </div>

          <form @submit.prevent="saveClassroom">
            <div class="modal-body">
              <div class="form-row">
                <div class="form-group flex-1">
                  <label>MÃ PHÒNG / TÊN PHÒNG</label>
                  <div class="input-wrapper">
                    <span class="material-symbols-outlined input-icon">room</span>
                    <input v-model="currentClassroom.code" type="text" placeholder="Ví dụ: P.305" required />
                  </div>
                </div>
                <div class="form-group flex-1">
                  <label>KHU VỰC / TÒA NHÀ</label>
                  <div class="select-wrapper">
                    <select v-model="currentClassroom.building">
                      <option value="Tòa A">Tòa A - Khối Văn hóa</option>
                      <option value="Tòa B">Tòa B - Khối Ngoại ngữ</option>
                      <option value="Tòa C">Tòa C - Thực hành</option>
                    </select>
                    <span class="material-symbols-outlined expand-icon">expand_more</span>
                  </div>
                </div>
              </div>

              <div class="form-row">
                <div class="form-group flex-1">
                  <label>SỨC CHỨA (HỌC VIÊN)</label>
                  <div class="input-wrapper">
                    <span class="material-symbols-outlined input-icon">groups</span>
                    <input v-model.number="currentClassroom.capacity" type="number" required />
                  </div>
                </div>
                <div class="form-group flex-1">
                  <label>TRẠNG THÁI HIỆN TẠI</label>
                  <div class="select-wrapper">
                    <select v-model="currentClassroom.status">
                      <option value="Trống">Sẵn sàng (Trống)</option>
                      <option value="Đang dùng">Đang có lớp dạy</option>
                      <option value="Bảo trì">Đang bảo trì / Sửa chữa</option>
                    </select>
                    <span class="material-symbols-outlined expand-icon">expand_more</span>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label>TRANG THIẾT BỊ PHÒNG</label>
                <div class="checkbox-grid">
                  <label class="checkbox-item">
                    <input type="checkbox" value="Máy chiếu" v-model="currentClassroom.equipment" />
                    <span>Máy chiếu</span>
                  </label>
                  <label class="checkbox-item">
                    <input type="checkbox" value="Tivi" v-model="currentClassroom.equipment" />
                    <span>Tivi thông minh</span>
                  </label>
                  <label class="checkbox-item">
                    <input type="checkbox" value="Điều hòa" v-model="currentClassroom.equipment" />
                    <span>Điều hòa</span>
                  </label>
                  <label class="checkbox-item">
                    <input type="checkbox" value="Loa" v-model="currentClassroom.equipment" />
                    <span>Hệ thống loa</span>
                  </label>
                  <label class="checkbox-item">
                    <input type="checkbox" value="Máy tính" v-model="currentClassroom.equipment" />
                    <span>Hệ thống máy tính</span>
                  </label>
                </div>
              </div>

              <div class="form-group" v-if="currentClassroom.status === 'Đang dùng'">
                <label>LỚP ĐANG SỬ DỤNG (NẾU CÓ)</label>
                <div class="input-wrapper">
                  <span class="material-symbols-outlined input-icon">school</span>
                  <input v-model="currentClassroom.currentClass" type="text" placeholder="Ví dụ: ENG-202" />
                </div>
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn-cancel" @click="closeModal">Hủy bỏ</button>
              <button type="submit" class="btn-submit">{{ isEditing ? 'Cập nhật' : 'Thêm phòng' }}</button>
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

.stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; }
.stat-info { display: flex; flex-direction: column; }
.stat-label { font-size: 12px; font-weight: 700; color: var(--on-surface-variant); text-transform: uppercase; margin-bottom: 2px; }
.stat-value { font-family: var(--font-outfit); font-size: 22px; font-weight: 800; color: var(--on-surface); }

/* Main Card */
.content-card {
  background-color: white;
  border-radius: 24px;
  box-shadow: var(--shadow-md);
  border: 1px solid var(--surface-container);
  overflow: hidden;
}

.table-actions { display: flex; justify-content: space-between; padding: 20px 24px; border-bottom: 1px solid var(--surface-container); }
.filter-group { display: flex; gap: 16px; }
.search-box { position: relative; width: 280px; }
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
  background-color: var(--surface-container-lowest);
}

.select-box { position: relative; }
.form-select { appearance: none; border: 1.5px solid var(--outline-variant); padding: 10px 40px 10px 16px; border-radius: 14px; font-size: 14px; font-weight: 600; color: var(--on-surface-variant); background-color: white; cursor: pointer; }
.select-icon { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: var(--outline); pointer-events: none; }

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

.room-code { font-size: 16px; font-weight: 800; color: var(--on-surface); display: inline-flex; align-items: center; gap: 8px; }
.room-code .material-symbols-outlined { color: var(--primary); font-variation-settings: 'FILL' 1; }
.building-text { font-size: 14px; font-weight: 700; color: var(--on-surface-variant); }

.capacity-badge { display: inline-flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 700; color: #475569; background: var(--surface-container-low); padding: 6px 14px; border-radius: 10px; }
.capacity-badge .material-symbols-outlined { font-size: 18px; }

.equipment-stack { display: flex; flex-wrap: wrap; gap: 6px; max-width: 200px; }
.equip-tag { font-size: 10px; font-weight: 800; color: var(--outline); background: var(--surface-container); padding: 4px 8px; border-radius: 6px; text-transform: uppercase; }

.status-info { display: flex; flex-direction: column; gap: 6px; }
.current-class-label { display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 700; color: var(--primary); }
.current-class-label .material-symbols-outlined { font-size: 16px; }

.badge { padding: 6px 14px; border-radius: 999px; font-size: 12px; font-weight: 700; text-align: center; }
.badge-success { background-color: #dcfce7; color: #15803d; }
.badge-warning { background-color: #fef3c7; color: #b45309; }
.badge-danger { background-color: #fee2e2; color: #b91c1c; }

.icon-btn { background: transparent; border: none; cursor: pointer; color: var(--outline); padding: 10px; border-radius: 10px; transition: all 0.2s; display: inline-flex; }
.icon-btn:hover { background-color: var(--surface-container); color: var(--primary); }

/* Modals */
.modal-overlay { position: fixed; inset: 0; background-color: rgba(0, 0, 0, 0.5); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; z-index: 1000; }
.premium-modal { background: white; width: 100%; max-width: 580px; border-radius: 32px; box-shadow: var(--shadow-xl); overflow: hidden; animation: modalIn 0.4s cubic-bezier(0.16, 1, 0.3, 1); }

@keyframes modalIn { from { transform: scale(0.95) translateY(20px); opacity: 0; } to { transform: scale(1) translateY(0); opacity: 1; } }

.modal-header { padding: 32px 40px 16px 40px; display: flex; justify-content: space-between; align-items: flex-start; }
.modal-title { font-family: var(--font-outfit); font-size: 24px; font-weight: 800; color: var(--on-surface); margin: 0 0 8px 0; }
.modal-subtitle { font-size: 13px; color: var(--on-surface-variant); font-weight: 500; }
.close-btn { background: var(--surface-container); border: none; color: var(--outline); cursor: pointer; padding: 8px; border-radius: 12px; }

.modal-body { padding: 24px 40px 40px 40px; }
.form-group { margin-bottom: 24px; }
.form-group label { display: block; font-size: 11px; font-weight: 900; color: var(--outline); margin-bottom: 10px; letter-spacing: 0.1em; }
.form-row { display: flex; gap: 20px; }
.flex-1 { flex: 1; }

.input-wrapper { position: relative; display: flex; align-items: center; }
.input-icon { position: absolute; left: 16px; color: var(--outline); font-size: 20px; }
.input-wrapper input { width: 100%; background-color: var(--surface-container-lowest); border: 1.5px solid var(--outline-variant); padding: 14px 16px 14px 48px; border-radius: 16px; font-size: 15px; font-weight: 600; color: var(--on-surface); outline: none; }

.select-wrapper { position: relative; display: flex; align-items: center; }
.select-wrapper select { width: 100%; appearance: none; background-color: var(--surface-container-lowest); border: 1.5px solid var(--outline-variant); padding: 14px 40px 14px 16px; border-radius: 16px; font-size: 15px; font-weight: 600; color: var(--on-surface); outline: none; }
.expand-icon { position: absolute; right: 16px; color: var(--outline); pointer-events: none; }

.checkbox-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; background: var(--surface-container-lowest); padding: 16px; border-radius: 16px; border: 1.5px solid var(--outline-variant); }
.checkbox-item { display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 700; color: var(--on-surface-variant); cursor: pointer; }
.checkbox-item input { width: 18px; height: 18px; accent-color: var(--primary); }

.modal-footer { padding: 0 40px 40px 40px; display: flex; justify-content: flex-end; gap: 16px; }
.btn-submit { background-color: var(--primary); color: white; border: none; padding: 14px 32px; border-radius: 16px; font-weight: 800; font-size: 15px; cursor: pointer; box-shadow: 0 8px 16px rgba(0, 74, 198, 0.25); }

/* Transitions */
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

.pagination-footer { display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; }
.page-btn { min-width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border: 1.5px solid var(--outline-variant); background-color: white; border-radius: 10px; font-weight: 700; cursor: pointer; }
.page-btn.active { background-color: var(--primary); color: white; border-color: var(--primary); }
</style>

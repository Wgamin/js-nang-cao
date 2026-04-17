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
    { label: 'Tổng Phòng Học', value: total, icon: 'meeting_room', color: '#2563eb', bg: '#eff6ff' },
    { label: 'Đang Sử Dụng', value: using, icon: 'cast_for_education', color: '#f59e0b', bg: '#fffbeb' },
    { label: 'Đang Trống', value: empty, icon: 'check_circle', color: '#10b981', bg: '#ecfdf5' },
    { label: 'Đang Bảo Trì', value: maintenance, icon: 'handyman', color: '#ef4444', bg: '#fef2f2' },
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
  currentClassroom.value = JSON.parse(JSON.stringify(room)) // Deep clone 
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
  <div class="admin-page">
    <div class="headers">
      <div class="header-left">
        <h1 class="title">Quản lý Phòng học</h1>
        <p class="subtitle">Kiểm soát tình trạng cơ sở vật chất và phân phối hạ tầng đào tạo</p>
      </div>
      <button class="btn btn-primary" @click="openAddModal">
        <span class="material-symbols-outlined">add_business</span>
        Thêm Phòng học
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
          <input v-model="searchQuery" type="text" class="input-search" placeholder="Tìm theo mã phòng..." />
        </div>
        
        <div class="filters">
          <div class="select-wrapper">
            <select v-model="statusFilter" class="select-filter">
              <option value="">Tất cả trạng thái</option>
              <option value="Trống">Trống</option>
              <option value="Đang dùng">Đang sử dụng</option>
              <option value="Bảo trì">Bảo trì</option>
            </select>
            <span class="material-symbols-outlined select-arrow">expand_more</span>
          </div>

          <div class="select-wrapper">
            <select v-model="buildingFilter" class="select-filter">
              <option value="">Khu vực / Tòa nhà</option>
              <option value="Tòa A">Tòa A</option>
              <option value="Tòa B">Tòa B</option>
              <option value="Tòa C">Tòa C</option>
            </select>
            <span class="material-symbols-outlined select-arrow">expand_more</span>
          </div>
          
          <button class="btn btn-secondary">
            <span class="material-symbols-outlined">map</span>
            Sơ đồ phòng
          </button>
        </div>
      </div>

      <!-- Table Section -->
      <div class="table-container">
        <table class="user-table">
          <thead>
            <tr>
              <th style="width: 15%">Mã phòng</th>
              <th style="width: 15%">Khu vực</th>
              <th style="width: 15%">Sức chứa</th>
              <th style="width: 25%">Trang thiết bị</th>
              <th style="width: 20%">Trạng thái</th>
              <th style="width: 10%" class="text-right">Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="room in filteredClassrooms" :key="room.id" class="table-row">
              <td>
                <span class="room-code">
                  <span class="material-symbols-outlined">sensor_door</span>
                  {{ room.code }}
                </span>
              </td>
              <td>
                <span class="building-text">{{ room.building }}</span>
              </td>
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
                  <br>
                  <span v-if="room.currentClass" class="current-class-link">
                    <span class="material-symbols-outlined">play_lesson</span>
                    {{ room.currentClass }}
                  </span>
                </div>
              </td>
              <td class="text-right">
                <div class="actions">
                  <button class="action-btn" title="Chỉnh sửa" @click="openEditModal(room)">
                    <span class="material-symbols-outlined">edit</span>
                  </button>
                  <button class="action-btn btn-delete" title="Xóa" @click="deleteClassroom(room.id)">
                    <span class="material-symbols-outlined">delete</span>
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="filteredClassrooms.length === 0">
              <td colspan="6" class="empty-state">Không tìm thấy phòng học nào.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="table-footer">
        <p>Hiển thị <strong>{{ filteredClassrooms.length }}</strong> phòng học</p>
      </div>
    </div>

    <!-- Modal Form -->
    <Transition name="modal-fade">
      <div v-if="isModalOpen" class="modal-backdrop" @click.self="closeModal">
        <div class="modal-box">
          <div class="modal-header">
            <div class="modal-header-text">
              <h2 class="modal-title">{{ isEditing ? 'Cập Nhật Phòng Học' : 'Thêm Phòng Học Mới' }}</h2>
              <p class="modal-subtitle">Quản lý không gian và tiện nghi để tối ưu hóa trải nghiệm giảng dạy.</p>
            </div>
            <button class="btn-close-minimal" @click="closeModal" title="Đóng">
              <span class="material-symbols-outlined">close</span>
            </button>
          </div>

          <form @submit.prevent="saveClassroom">
            <div class="modal-body">
              <div class="form-grid">
                <div class="form-item span-1">
                  <label class="form-label">Mã phòng / Tên phòng</label>
                  <input v-model="currentClassroom.code" type="text" placeholder="Ví dụ: P.305" required class="form-input" />
                </div>
                
                <div class="form-item span-1">
                  <label class="form-label">Khu vực / Tòa nhà</label>
                  <div class="select-container">
                    <select v-model="currentClassroom.building" class="form-select">
                      <option value="Tòa A">Tòa A - Khối Văn hóa</option>
                      <option value="Tòa B">Tòa B - Khối Ngoại ngữ</option>
                      <option value="Tòa C">Tòa C - Thực hành</option>
                    </select>
                    <span class="material-symbols-outlined select-icon-minimal">expand_more</span>
                  </div>
                </div>

                <div class="form-item span-1">
                  <label class="form-label">Sức chứa (Học viên)</label>
                  <input v-model.number="currentClassroom.capacity" type="number" required class="form-input" />
                </div>

                <div class="form-item span-1">
                  <label class="form-label">Trạng thái hiện tại</label>
                  <div class="select-container">
                    <select v-model="currentClassroom.status" class="form-select">
                      <option value="Trống">Sẵn sàng (Trống)</option>
                      <option value="Đang dùng">Đang có lớp dạy</option>
                      <option value="Bảo trì">Đang bảo trì / Sửa chữa</option>
                    </select>
                    <span class="material-symbols-outlined select-icon-minimal">expand_more</span>
                  </div>
                </div>

                <div class="form-item span-2">
                  <label class="form-label">Trang thiết bị phòng</label>
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

                <div class="form-item span-2" v-if="currentClassroom.status === 'Đang dùng'">
                  <label class="form-label">Lớp đang sử dụng (Nếu có)</label>
                  <input v-model="currentClassroom.currentClass" type="text" placeholder="Ví dụ: ENG-202" class="form-input" />
                </div>
              </div>
            </div>

            <div class="modal-footer-minimal">
              <button type="button" class="btn btn-simple" @click="closeModal">Hủy bỏ</button>
              <button type="submit" class="btn btn-solid-primary">{{ isEditing ? 'Cập nhật' : 'Thêm phòng' }}</button>
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

/* Stats Cards */
.stats-container { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 28px; }
.stat-card { background-color: #ffffff; padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 16px; border: 1px solid #f1f5f9; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05); transition: transform 0.2s ease; }
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

/* Room specific */
.room-code { font-size: 14px; font-weight: 700; color: #1e293b; display: inline-flex; align-items: center; gap: 8px; }
.room-code .material-symbols-outlined { color: #2563eb; font-size: 20px; }
.building-text { font-size: 13px; font-weight: 600; color: #475569; }

.capacity-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 12px; font-weight: 600; color: #475569; background: #f8fafc; padding: 4px 10px; border-radius: 8px; border: 1px solid #e2e8f0; }
.capacity-badge .material-symbols-outlined { font-size: 16px; color: #64748b; }

.equipment-stack { display: flex; flex-wrap: wrap; gap: 6px; }
.equip-tag { font-size: 11px; font-weight: 600; color: #475569; background: #f1f5f9; padding: 4px 8px; border-radius: 6px; }
.empty-text { font-size: 12px; color: #94a3b8; font-style: italic; }

.current-class-link { display: inline-flex; align-items: center; gap: 4px; font-size: 12px; font-weight: 600; color: #2563eb; margin-top: 4px; }
.current-class-link .material-symbols-outlined { font-size: 14px; }

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
.span-1 { grid-column: span 1; }
.span-2 { grid-column: span 2; }
.form-label { display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 8px; }
.form-input, .form-select { width: 100%; padding: 10px 14px; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; color: #0f172a; outline: none; transition: all 0.2s; }
.form-input:focus, .form-select:focus { background-color: #ffffff; border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }

.select-container { position: relative; }
.select-icon-minimal { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; font-size: 20px; }
.form-select { appearance: none; padding-right: 40px; }

/* Checkboxes */
.checkbox-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; background: #f8fafc; padding: 16px; border-radius: 8px; border: 1px solid #e2e8f0; }
.checkbox-item { display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; color: #475569; cursor: pointer; }
.checkbox-item input[type="checkbox"] { width: 16px; height: 16px; accent-color: #2563eb; }

/* Modal Footer */
.modal-footer-minimal { padding: 20px 28px; background-color: #f8fafc; border-top: 1px solid #f1f5f9; border-radius: 0 0 16px 16px; display: flex; justify-content: flex-end; gap: 12px; }
.btn-simple { background-color: transparent; border: 1px solid #e2e8f0; color: #475569; font-weight: 600; padding: 10px 18px; border-radius: 8px; cursor: pointer;}
.btn-simple:hover { background-color: #f1f5f9; border-color: #cbd5e1; }
.btn-solid-primary { background-color: #2563eb; color: #ffffff; border: none; font-weight: 600; padding: 10px 18px; border-radius: 8px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); cursor: pointer;}
.btn-solid-primary:hover { background-color: #1d4ed8; }

.modal-fade-enter-active, .modal-fade-leave-active { transition: opacity 0.2s ease; }
.modal-fade-enter-from, .modal-fade-leave-to { opacity: 0; }
</style>

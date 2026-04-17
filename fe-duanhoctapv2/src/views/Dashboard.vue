<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useApi } from '../utils/api'
import AppLayout from '../components/AppLayout.vue'

const role = ref(localStorage.getItem('role') || '')
const schedules = ref<any[]>([])
const teacherClasses = ref<any[]>([])
const stats = ref<any>({})
const loading = ref(true)
const toast = ref<{ msg: string; type: string } | null>(null)

// Attendance modal state
const showModal = ref(false)
const selectedSchedule = ref<any>(null)
const attendanceStudents = ref<any[]>([])
const savingAttendance = ref(false)

const { fetchApi } = useApi()
const router = useRouter()

const isAdmin = computed(() => role.value === 'Admin')
const isTeacher = computed(() => role.value === 'Teacher')
const isStudent = computed(() => role.value === 'Student')

const showToast = (msg: string, type = 'success') => {
  toast.value = { msg, type }
  setTimeout(() => { toast.value = null }, 3500)
}

const formatDate = (dt: string) =>
  new Date(dt).toLocaleString('vi-VN', {
    weekday: 'short', day: '2-digit', month: '2-digit',
    year: 'numeric', hour: '2-digit', minute: '2-digit',
  })

const loadData = async () => {
  loading.value = true
  try {
    if (isAdmin.value) {
      const res = await fetchApi('/admin/stats')
      stats.value = res.data
    } else if (isTeacher.value) {
      const [s, c] = await Promise.all([
        fetchApi('/schedules'),
        fetchApi('/teacher/classes'),
      ])
      schedules.value = s.data
      teacherClasses.value = c.data
    } else {
      const res = await fetchApi('/schedules')
      schedules.value = res.data
    }
  } catch (e: any) {
    showToast(e.message, 'error')
  } finally {
    loading.value = false
  }
}

/** Mở modal điểm danh: tự động load học sinh từ class_user qua API */
const openAttendance = async (schedule: any) => {
  selectedSchedule.value = schedule
  attendanceStudents.value = []
  showModal.value = true
  try {
    const res = await fetchApi(`/schedules/${schedule.id}/attendance`)
    // API trả về { schedule, students: [{id, name, is_present, note}] }
    attendanceStudents.value = res.data.students.map((s: any) => ({ ...s }))
  } catch (e: any) {
    showToast('Không thể tải danh sách học sinh: ' + e.message, 'error')
  }
}

const saveAttendance = async () => {
  if (!selectedSchedule.value) return
  savingAttendance.value = true
  try {
    await fetchApi(`/schedules/${selectedSchedule.value.id}/attendance`, {
      method: 'POST',
      body: JSON.stringify({
        attendances: attendanceStudents.value.map((s: any) => ({
          student_id: s.id,
          is_present: s.is_present,
          note: s.note || null,
        })),
      }),
    })
    showToast('✅ Điểm danh thành công!')
    showModal.value = false
  } catch (e: any) {
    showToast(e.message, 'error')
  } finally {
    savingAttendance.value = false
  }
}

onMounted(loadData)
</script>

<template>
  <AppLayout>
    <div class="page">
      <!-- Loading -->
      <div v-if="loading" class="loading-state">
        <div class="big-spinner"></div>
        <p>Đang tải dữ liệu...</p>
      </div>

      <template v-else>
        <!-- ===== ADMIN ===== -->
        <div v-if="isAdmin" class="page-body">
          <div class="page-header">
            <h1 class="page-title">📊 Tổng Quan Hệ Thống</h1>
          </div>
          <div class="stats-grid">
            <router-link to="/admin/users" class="stat-card stat-card--link">
              <div class="stat-icon" style="background:#ede9fe;color:#7c3aed">👥</div>
              <div class="stat-value">{{ stats.total_students ?? 0 }}</div>
              <div class="stat-label">Học Sinh</div>
            </router-link>
            <router-link to="/admin/users" class="stat-card stat-card--link">
              <div class="stat-icon" style="background:#dcfce7;color:#16a34a">🧑‍🏫</div>
              <div class="stat-value">{{ stats.total_teachers ?? 0 }}</div>
              <div class="stat-label">Giáo Viên</div>
            </router-link>
            <router-link to="/admin/classes" class="stat-card stat-card--link">
              <div class="stat-icon" style="background:#dbeafe;color:#1d4ed8">🏫</div>
              <div class="stat-value">{{ stats.total_classes ?? 0 }}</div>
              <div class="stat-label">Lớp Học</div>
            </router-link>
            <router-link to="/admin/schedules" class="stat-card stat-card--link">
              <div class="stat-icon" style="background:#fef3c7;color:#d97706">📅</div>
              <div class="stat-value">{{ stats.total_schedules ?? 0 }}</div>
              <div class="stat-label">Buổi Học</div>
            </router-link>
          </div>
          <div class="quick-nav card mt-6">
            <h2 class="section-title">Truy cập nhanh</h2>
            <p class="text-muted" style="margin-top:8px;font-size:14px">Sử dụng menu bên trái để quản lý Users, Lớp học và Lịch học.</p>
          </div>
        </div>

        <!-- ===== TEACHER ===== -->
        <div v-else-if="isTeacher" class="page-body">
          <div class="page-header">
            <h1 class="page-title">📋 Lớp Học Của Tôi</h1>
          </div>
          <div v-if="!teacherClasses.length" class="empty-card card">Bạn chưa được phân công lớp nào.</div>
          <div v-for="cls in teacherClasses" :key="cls.id" class="card mt-4">
            <div class="class-header">
              <div>
                <h3 class="class-name">{{ cls.name }}</h3>
                <span class="status-badge" :class="cls.status === 'active' ? 'status-active' : 'status-inactive'">
                  {{ cls.status === 'active' ? 'Đang hoạt động' : 'Ngừng hoạt động' }}
                </span>
                <p v-if="cls.description" class="text-muted" style="font-size:13px;margin-top:4px">{{ cls.description }}</p>
              </div>
              <div class="student-count">{{ cls.students?.length ?? 0 }} học sinh</div>
            </div>

            <h4 class="section-title mt-4">Lịch Học</h4>
            <div v-if="!cls.schedules?.length" class="empty-state">Chưa có lịch học nào.</div>
            <div v-else class="table-wrapper mt-4">
              <table class="table">
                <thead>
                  <tr>
                    <th>Thời Gian Bắt Đầu</th>
                    <th>Thời Gian Kết Thúc</th>
                    <th>Phòng</th>
                    <th>Ghi Chú</th>
                    <th>Hành Động</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="sched in cls.schedules" :key="sched.id">
                    <td>{{ formatDate(sched.start_time) }}</td>
                    <td>{{ sched.end_time ? formatDate(sched.end_time) : '—' }}</td>
                    <td><span class="room-tag">{{ sched.room }}</span></td>
                    <td class="text-muted">{{ sched.note || '—' }}</td>
                    <td>
                      <button
                        class="btn btn-primary btn-sm"
                        :id="`attendance-btn-${sched.id}`"
                        @click="openAttendance({ ...sched, class_id: cls.id })"
                      >
                        ✏️ Điểm Danh
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- ===== STUDENT ===== -->
        <div v-else class="page-body">
          <div class="page-header">
            <h1 class="page-title">📅 Lịch Học Của Tôi</h1>
          </div>
          <div v-if="!schedules.length" class="empty-card card">Bạn chưa được đăng ký vào lớp nào.</div>
          <div v-else class="card">
            <div class="table-wrapper">
              <table class="table">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Lớp</th>
                    <th>Thời Gian Bắt Đầu</th>
                    <th>Thời Gian Kết Thúc</th>
                    <th>Phòng Học</th>
                    <th>Ghi Chú</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(sched, i) in schedules" :key="sched.id">
                    <td class="text-muted">{{ i + 1 }}</td>
                    <td class="fw-bold">{{ sched.study_class?.name ?? '—' }}</td>
                    <td>{{ formatDate(sched.start_time) }}</td>
                    <td>{{ sched.end_time ? formatDate(sched.end_time) : '—' }}</td>
                    <td><span class="room-tag">{{ sched.room }}</span></td>
                    <td class="text-muted">{{ sched.note || '—' }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </template>
    </div>

    <!-- ===== ATTENDANCE MODAL ===== -->
    <Teleport to="body">
      <div v-if="showModal" class="modal-backdrop" @click.self="showModal = false">
        <div class="modal" id="attendance-modal">
          <div class="modal-header">
            <div>
              <h3>✏️ Điểm Danh Buổi Học</h3>
              <p class="text-muted" style="font-size:13px;margin-top:2px">
                {{ selectedSchedule ? formatDate(selectedSchedule.start_time) : '' }}
                &nbsp;·&nbsp; 🚪 {{ selectedSchedule?.room }}
              </p>
            </div>
            <button class="modal-close" @click="showModal = false">✕</button>
          </div>
          <div class="modal-body">
            <div v-if="!attendanceStudents.length" class="empty-state">
              <div class="big-spinner" style="width:28px;height:28px"></div>
            </div>
            <div v-else>
              <div class="attendance-header-row">
                <span>Học Sinh</span>
                <span>Trạng Thái</span>
                <span>Ghi Chú</span>
              </div>
              <div class="student-list">
                <div v-for="student in attendanceStudents" :key="student.id" class="student-row">
                  <div class="student-name">
                    <div class="student-avatar">{{ student.name.charAt(0) }}</div>
                    <div>
                      <div style="font-weight:600;font-size:14px">{{ student.name }}</div>
                      <div class="text-muted" style="font-size:12px">{{ student.email }}</div>
                    </div>
                  </div>
                  <label class="toggle" :for="`s-${student.id}`">
                    <input type="checkbox" :id="`s-${student.id}`" v-model="student.is_present" />
                    <span class="toggle-track"><span class="toggle-thumb"></span></span>
                    <span :style="{ color: student.is_present ? 'var(--color-success)' : 'var(--color-danger)', fontWeight: 600, fontSize:'13px', minWidth:'60px' }">
                      {{ student.is_present ? '✅ Có mặt' : '❌ Vắng' }}
                    </span>
                  </label>
                  <input
                    class="form-input note-input"
                    type="text"
                    v-model="student.note"
                    placeholder="Ghi chú..."
                  />
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-outline" @click="showModal = false">Hủy</button>
            <button id="save-attendance-btn" class="btn btn-primary" :disabled="savingAttendance || !attendanceStudents.length" @click="saveAttendance">
              <span v-if="savingAttendance" class="spinner"></span>
              {{ savingAttendance ? 'Đang lưu...' : '💾 Lưu Điểm Danh' }}
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
.page { padding: 28px 32px; min-height: 100vh; }
.page-header { margin-bottom: 24px; }
.page-title { font-size: 24px; font-weight: 800; }
.page-body { max-width: 1100px; }

.loading-state { display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:60vh; gap:16px; color:var(--color-text-muted); }
.big-spinner { width:40px; height:40px; border:3px solid rgba(79,70,229,.2); border-top-color:#4f46e5; border-radius:50%; animation:spin .8s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

.stats-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:20px; }
.stat-card { background:#fff; border-radius:var(--radius-lg); border:1px solid var(--color-border); padding:24px; transition:transform .2s,box-shadow .2s; }
.stat-card--link { text-decoration:none; color:inherit; display:block; }
.stat-card--link:hover { transform:translateY(-3px); box-shadow:0 8px 24px rgba(0,0,0,.1); }
.stat-icon { width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px;margin-bottom:16px; }
.stat-value { font-size:36px;font-weight:800;line-height:1; }
.stat-label { font-size:14px;color:var(--color-text-muted);margin-top:6px;font-weight:500; }

.quick-nav h2 { font-size:16px;font-weight:700; }

.class-header { display:flex;align-items:flex-start;justify-content:space-between;gap:16px; }
.class-name { font-size:18px;font-weight:700;margin-bottom:4px; }
.student-count { background:#f0f9ff;color:#0369a1;padding:6px 14px;border-radius:999px;font-size:13px;font-weight:600;white-space:nowrap; }
.status-badge { display:inline-block;padding:2px 10px;border-radius:999px;font-size:12px;font-weight:600; }
.status-active { background:#dcfce7;color:#15803d; }
.status-inactive { background:#fee2e2;color:#b91c1c; }

.section-title { font-size:15px;font-weight:700; }
.empty-state { text-align:center;padding:32px;color:var(--color-text-muted);font-size:14px; }
.empty-card { text-align:center;padding:48px;color:var(--color-text-muted); }
.room-tag { background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0;padding:2px 10px;border-radius:999px;font-size:12px;font-weight:500; }

/* Modal */
.modal-backdrop { position:fixed;inset:0;background:rgba(15,23,42,.6);backdrop-filter:blur(4px);display:flex;align-items:center;justify-content:center;z-index:1000;padding:20px; }
.modal { background:#fff;border-radius:20px;width:100%;max-width:580px;max-height:88vh;display:flex;flex-direction:column;box-shadow:0 24px 60px rgba(0,0,0,.2);animation:modalIn .25s ease; }
@keyframes modalIn { from{opacity:0;transform:scale(.95) translateY(10px)} to{opacity:1;transform:scale(1) translateY(0)} }
.modal-header { display:flex;align-items:flex-start;justify-content:space-between;padding:20px 24px;border-bottom:1px solid var(--color-border); }
.modal-header h3 { font-size:17px;font-weight:700; }
.modal-close { background:none;border:none;cursor:pointer;color:var(--color-text-muted);font-size:18px;padding:4px 8px;border-radius:6px;line-height:1; }
.modal-close:hover { background:#f1f5f9; }
.modal-body { padding:20px 24px;overflow-y:auto;flex:1; }
.modal-footer { display:flex;gap:12px;justify-content:flex-end;padding:16px 24px;border-top:1px solid var(--color-border); }

.attendance-header-row { display:grid;grid-template-columns:1fr 120px 1fr;gap:12px;font-size:12px;font-weight:700;color:var(--color-text-muted);text-transform:uppercase;letter-spacing:.05em;padding:0 4px 8px;border-bottom:1px solid var(--color-border);margin-bottom:8px; }
.student-list { display:flex;flex-direction:column;gap:8px; }
.student-row { display:grid;grid-template-columns:1fr 120px 1fr;gap:12px;align-items:center;padding:12px;border:1px solid var(--color-border);border-radius:10px; }
.student-row:hover { background:#f8fafc; }
.student-name { display:flex;align-items:center;gap:10px; }
.student-avatar { width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#4f46e5,#06b6d4);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;flex-shrink:0; }
.note-input { padding:7px 10px;font-size:13px; }

.toggle { display:flex;align-items:center;gap:8px;cursor:pointer; }
.toggle input { display:none; }
.toggle-track { width:40px;height:22px;background:#e2e8f0;border-radius:999px;position:relative;transition:background .2s;flex-shrink:0; }
.toggle input:checked + .toggle-track { background:var(--color-success); }
.toggle-thumb { position:absolute;top:3px;left:3px;width:16px;height:16px;border-radius:50%;background:#fff;box-shadow:0 1px 3px rgba(0,0,0,.2);transition:left .2s; }
.toggle input:checked ~ .toggle-track .toggle-thumb { left:21px; }

@media (max-width:768px) {
  .page { padding:16px; }
  .stats-grid { grid-template-columns:repeat(2,1fr); }
  .student-row { grid-template-columns:1fr; }
  .attendance-header-row { display:none; }
}
</style>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useApi } from '../utils/api'
import AppLayout from '../components/AppLayout.vue'

const role = ref(localStorage.getItem('role') || '')
const { fetchApi } = useApi()

const isAdmin = computed(() => role.value === 'Admin')
const isTeacher = computed(() => role.value === 'Teacher')
const isStudent = computed(() => role.value === 'Student')
const isParent = computed(() => role.value === 'Parent')

// ---- Common state ----
const loading = ref(true)
const toast = ref<{ msg: string; type: string } | null>(null)
const showToast = (msg: string, type = 'success') => {
  toast.value = { msg, type }
  setTimeout(() => { toast.value = null }, 3500)
}
const fmt = (dt: string) => dt ? new Date(dt).toLocaleString('vi-VN', { day:'2-digit', month:'2-digit', year:'numeric', hour:'2-digit', minute:'2-digit' }) : '—'
const fmtMoney = (n: any) => Number(n).toLocaleString('vi-VN') + ' ₫'

// ---- Admin state ----
const stats = ref<any>({})

// ---- Teacher state ----
const teacherClasses = ref<any[]>([])
const teacherClassDetails = ref<Record<number, any>>({}) // { classId: fullDetailData }
const showModal = ref(false)
const selectedSchedule = ref<any>(null)
const attendanceStudents = ref<any[]>([])
const savingAttendance = ref(false)
const loadingClassId = ref<number | null>(null)

const openAttendance = async (sched: any) => {
  selectedSchedule.value = sched
  showModal.value = true
  attendanceStudents.value = []
  try {
    const res = await fetchApi(`/schedules/${sched.id}/attendance`)
    attendanceStudents.value = res.data.students.map((s: any) => ({ ...s, status: s.status || 'present' }))
  } catch (e: any) { showToast(e.message, 'error') }
}

const openAttendanceFromDetail = async (sched: any) => {
  selectedSchedule.value = sched
  showModal.value = true
  attendanceStudents.value = []
  try {
    const res = await fetchApi(`/schedules/${sched.id}/attendance`)
    attendanceStudents.value = res.data.students.map((s: any) => ({ ...s, status: s.status || 'present' }))
  } catch (e: any) { showToast(e.message, 'error') }
}

const loadClassDetails = async (classId: number, forceRefresh = false) => {
  if (teacherClassDetails.value[classId] && !forceRefresh) return // Đã load rồi (trừ khi force refresh)
  if (forceRefresh) delete teacherClassDetails.value[classId] // Xóa cache cũ
  loadingClassId.value = classId
  try {
    const res = await fetchApi(`/teacher/classes/${classId}/attendance`)
    teacherClassDetails.value[classId] = res.data
  } catch (e: any) {
    showToast(e.message, 'error')
  } finally {
    loadingClassId.value = null
  }
}

const saveAttendance = async () => {
  savingAttendance.value = true
  try {
    await fetchApi(`/schedules/${selectedSchedule.value.id}/attendance`, {
      method: 'POST',
      body: JSON.stringify({
        attendances: attendanceStudents.value.map((s: any) => ({
          student_id: s.id, status: s.status, note: s.note || null,
        })),
      }),
    })
    showToast('✅ Lưu điểm danh thành công!')
    showModal.value = false
  } catch (e: any) { showToast(e.message, 'error') }
  finally { savingAttendance.value = false }
}

const statusLabel: Record<string, string> = {
  present: '✅ Có mặt', absent: '❌ Vắng',
}
const statusColor: Record<string, string> = {
  present: '#16a34a', absent: '#dc2626',
}

// ---- Student state ----
const schedules = ref<any[]>([])
const attendanceHistory = ref<any[]>([])
const tuitionHistory = ref<any[]>([])
const studentTab = ref<'schedule' | 'attendance' | 'tuition'>('schedule')

// ---- Parent state ----
const parentTab = ref(0)
const childrenData = ref<any[]>([]) // [{ child, schedules, attendances, tuitions }]

const loadParentData = async () => {
  const [schedRes, histRes, tuitionRes] = await Promise.all([
    fetchApi('/schedules'),
    fetchApi('/attendance/history'),
    fetchApi('/tuitions/history'),
  ])
  const schedByChild: Record<string, any[]> = {}
  schedRes.data.forEach((s: any) => {
    const key = s.child_name || 'unknown'
    if (!schedByChild[key]) schedByChild[key] = []
    schedByChild[key].push(s)
  })

  // Map tuitions by child id for easier access
  const tuitionsByChildId: Record<number, any[]> = {}
  tuitionRes.data.forEach((c: any) => {
    tuitionsByChildId[c.child.id] = c.tuitions
  })

  childrenData.value = histRes.data.map((c: any) => ({
    ...c,
    schedules: schedByChild[c.child.name] || [],
    tuitions: tuitionsByChildId[c.child.id] || [],
  }))
}

const loadData = async () => {
  loading.value = true
  try {
    if (isAdmin.value) {
      const res = await fetchApi('/admin/stats')
      stats.value = res.data
    } else if (isTeacher.value) {
      const res = await fetchApi('/teacher/classes')
      teacherClasses.value = res.data
    } else if (isParent.value) {
      await loadParentData()
    } else {
      const [s, h, t] = await Promise.all([fetchApi('/schedules'), fetchApi('/attendance/history'), fetchApi('/tuitions/history')])
      schedules.value = s.data
      attendanceHistory.value = h.data
      tuitionHistory.value = t.data
    }
  } catch (e: any) { showToast(e.message, 'error') }
  finally { loading.value = false }
}

onMounted(loadData)
</script>

<template>
  <AppLayout>
    <div class="page">
      <div v-if="loading" class="loading-state">
        <div class="big-spinner"></div><p>Đang tải dữ liệu...</p>
      </div>

      <template v-else>
        <!-- ===== ADMIN ===== -->
        <div v-if="isAdmin">
          <div class="page-header"><h1 class="page-title">📊 Tổng Quan Hệ Thống</h1></div>
          <div class="stats-grid">
            <router-link to="/admin/users" class="stat-card stat-link">
              <div class="stat-icon" style="background:#ede9fe;color:#7c3aed">👥</div>
              <div class="stat-value">{{ stats.total_students ?? 0 }}</div><div class="stat-label">Học Sinh</div>
            </router-link>
            <router-link to="/admin/users" class="stat-card stat-link">
              <div class="stat-icon" style="background:#dcfce7;color:#16a34a">🧑‍🏫</div>
              <div class="stat-value">{{ stats.total_teachers ?? 0 }}</div><div class="stat-label">Giáo Viên</div>
            </router-link>
            <router-link to="/admin/users" class="stat-card stat-link">
              <div class="stat-icon" style="background:#fef3c7;color:#d97706">👨‍👩‍👧</div>
              <div class="stat-value">{{ stats.total_parents ?? 0 }}</div><div class="stat-label">Phụ Huynh</div>
            </router-link>
            <router-link to="/admin/classes" class="stat-card stat-link">
              <div class="stat-icon" style="background:#dbeafe;color:#1d4ed8">🏫</div>
              <div class="stat-value">{{ stats.total_classes ?? 0 }}</div><div class="stat-label">Lớp Học</div>
            </router-link>
            <router-link to="/admin/schedules" class="stat-card stat-link">
              <div class="stat-icon" style="background:#fce7f3;color:#be185d">📅</div>
              <div class="stat-value">{{ stats.total_schedules ?? 0 }}</div><div class="stat-label">Buổi Học</div>
            </router-link>
            <router-link to="/admin/tuitions" class="stat-card stat-link">
              <div class="stat-icon" style="background:#ecfdf5;color:#059669">💰</div>
              <div class="stat-value">→</div><div class="stat-label">Học Phí</div>
            </router-link>
          </div>
        </div>

        <!-- ===== TEACHER ===== -->
        <div v-else-if="isTeacher">
          <div class="page-header"><h1 class="page-title">📋 Lớp Học Của Tôi</h1></div>
          <div v-if="!teacherClasses.length" class="empty-card card">Bạn chưa được phân công lớp nào.</div>
          <div v-for="cls in teacherClasses" :key="cls.id" class="card mt-4">
            <div class="class-header">
              <div>
                <h3 class="class-name">{{ cls.name }}</h3>
                <span class="status-badge" :class="cls.status === 'active' ? 'status-active' : 'status-inactive'">
                  {{ cls.status === 'active' ? 'Đang hoạt động' : 'Ngừng' }}
                </span>
                <p v-if="cls.description" class="text-muted" style="font-size:13px;margin-top:4px">{{ cls.description }}</p>
              </div>
              <div class="student-count">{{ cls.students?.length ?? 0 }} học sinh</div>
            </div>

            <!-- Nút tải chi tiết lớp -->
            <button
              v-if="!teacherClassDetails[cls.id]"
              class="btn btn-outline btn-sm mt-3"
              :disabled="loadingClassId === cls.id"
              @click="loadClassDetails(cls.id)"
            >
              {{ loadingClassId === cls.id ? '⏳ Đang tải...' : '📋 Xem Tất Cả Buổi Học (' + (cls.schedules?.length || 0) + ' buổi)' }}
            </button>

            <!-- Chi tiết lớp với tất cả buổi học -->
            <div v-if="teacherClassDetails[cls.id]" class="mt-4">
              <div class="section-header">
                <h4 class="section-title">📅 Tất Cả Buổi Học ({{ teacherClassDetails[cls.id].schedules?.length || 0 }} buổi)</h4>
                <button class="btn btn-outline btn-sm" @click="loadClassDetails(cls.id, true)">🔄 Làm mới</button>
              </div>

              <div v-if="!teacherClassDetails[cls.id].schedules?.length" class="empty-state">Chưa có buổi học nào.</div>
              <div v-else class="table-wrapper mt-3">
                <table class="table">
                  <thead>
                    <tr>
                      <th>STT</th>
                      <!-- <th>Buổi</th> -->
                      <th>Ngày Học</th>
                      <th>Phòng</th>
                      <th>Trạng Thái</th>
                      <th>Thống Kê</th>
                      <th>Hành Động</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(sched, index) in teacherClassDetails[cls.id].schedules" :key="sched.id">
                      <td class="text-muted">{{ Number(index) + 1 }}</td>
                      <!-- <td><span class="session-badge">Buổi {{ sched.attendance_count }}</span></td> -->
                      <td>{{ fmt(sched.start_time) }}</td>
                      <td><span class="room-tag">{{ sched.room }}</span></td>
                      <td>
                        <span v-if="sched.is_attendanced" class="att-badge att-done">✅ Đã điểm danh</span>
                        <span v-else class="att-badge att-pending">⏳ Chưa điểm danh</span>
                      </td>
                      <td>
                        <div class="stats-mini">
                          <span class="stat-present">✅ {{ sched.stats?.present || 0 }}</span>
                          <span class="stat-absent">❌ {{ sched.stats?.absent || 0 }}</span>
                          <span class="text-muted">/ {{ teacherClassDetails[cls.id].class?.total_students || 0 }}</span>
                        </div>
                      </td>
                      <td>
                        <button class="btn btn-primary btn-sm" @click="openAttendanceFromDetail(sched)">
                          {{ sched.is_attendanced ? '✏️ Sửa' : '✏️ Điểm Danh' }}
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Lịch học cũ (nếu chưa load chi tiết) -->
            <div v-else-if="cls.schedules?.length" class="mt-4">
              <h4 class="section-title mt-4">Lịch Học Gần Đây</h4>
              <div class="table-wrapper mt-3">
                <table class="table">
                  <thead><tr><th>Buổi</th><th>Bắt Đầu</th><th>Kết Thúc</th><th>Phòng</th><th>Ghi Chú</th><th>Hành Động</th></tr></thead>
                  <tbody>
                    <tr v-for="sched in cls.schedules.slice(0, 5)" :key="sched.id">
                      <td><span class="session-badge">Buổi {{ sched.attendance_count || 1 }}</span></td>
                      <td>{{ fmt(sched.start_time) }}</td>
                      <td>{{ sched.end_time ? fmt(sched.end_time) : '—' }}</td>
                      <td><span class="room-tag">{{ sched.room }}</span></td>
                      <td class="text-muted">{{ sched.note || '—' }}</td>
                      <td>
                        <button class="btn btn-primary btn-sm" @click="openAttendance(sched)">
                          ✏️ Điểm Danh
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- ===== STUDENT ===== -->
        <div v-else-if="isStudent">
          <div class="page-header"><h1 class="page-title">📅 Thông Tin Học Tập</h1></div>
          <div class="tab-bar">
            <button class="tab-btn" :class="{ active: studentTab === 'schedule' }" @click="studentTab = 'schedule'">📅 Lịch Học</button>
            <button class="tab-btn" :class="{ active: studentTab === 'attendance' }" @click="studentTab = 'attendance'">📋 Điểm Danh</button>
            <button class="tab-btn" :class="{ active: studentTab === 'tuition' }" @click="studentTab = 'tuition'">💰 Học Phí</button>
          </div>
          <div v-if="studentTab === 'schedule'" class="card mt-4">
            <div v-if="!schedules.length" class="empty-state">Chưa có lịch học nào.</div>
            <div v-else class="table-wrapper">
              <table class="table">
                <thead><tr><th>#</th><th>Lớp</th><th>Bắt Đầu</th><th>Kết Thúc</th><th>Phòng</th><th>Ghi Chú</th></tr></thead>
                <tbody>
                  <tr v-for="(s, i) in schedules" :key="s.id">
                    <td class="text-muted">{{ i+1 }}</td>
                    <td class="fw-bold">{{ s.study_class?.name ?? s.class_name ?? '—' }}</td>
                    <td>{{ fmt(s.start_time) }}</td>
                    <td>{{ s.end_time ? fmt(s.end_time) : '—' }}</td>
                    <td><span class="room-tag">{{ s.room }}</span></td>
                    <td class="text-muted">{{ s.note || '—' }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div v-else-if="studentTab === 'attendance'" class="card mt-4">
            <div v-if="!attendanceHistory.length" class="empty-state">Chưa có dữ liệu điểm danh.</div>
            <div v-else class="table-wrapper">
              <table class="table">
                <thead><tr><th>#</th><th>Lớp</th><th>Buổi</th><th>Ngày Học</th><th>Trạng Thái</th><th>Ghi Chú</th></tr></thead>
                <tbody>
                  <tr v-for="(a, i) in attendanceHistory" :key="a.id">
                    <td class="text-muted">{{ i+1 }}</td>
                    <td class="fw-bold">{{ a.schedule.class_name }}</td>
                    <td><span class="session-badge">Buổi {{ a.schedule.attendance_count || 1 }}</span></td>
                    <td>{{ fmt(a.schedule.start_time) }}</td>
                    <td><span class="att-badge" :style="{ background: statusColor[a.status] + '20', color: statusColor[a.status] }">{{ statusLabel[a.status] }}</span></td>
                    <td class="text-muted">{{ a.note || '—' }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div v-else class="card mt-4">
             <div v-if="!tuitionHistory.length" class="empty-state">Chưa có dữ liệu học phí.</div>
             <div v-else class="table-wrapper">
                <table class="table">
                   <thead><tr><th>Lớp</th><th>Học phí</th><th>Đã đóng</th><th>Trạng thái</th><th>Ghi chú</th></tr></thead>
                   <tbody>
                      <tr v-for="t in tuitionHistory" :key="t.id">
                         <td class="fw-bold">{{ t.class_name }}</td>
                         <td>{{ fmtMoney(t.amount) }}</td>
                         <td :style="{ color: t.amount_paid > 0 ? '#16a34a' : 'inherit' }">{{ fmtMoney(t.amount_paid) }}</td>
                         <td>
                           <span class="status-badge" :style="{ background: t.status==='paid'?'#dcfce7':(t.status==='partially_paid'?'#fef3c7':'#fee2e2'), color: t.status==='paid'?'#16a34a':(t.status==='partially_paid'?'#d97706':'#dc2626') }">
                              {{ t.status === 'paid' ? 'Đã thanh toán' : (t.status === 'partially_paid' ? 'Đóng 1 phần' : 'Chưa đóng') }}
                           </span>
                         </td>
                         <td class="text-muted">{{ t.note || '—' }}</td>
                      </tr>
                   </tbody>
                </table>
             </div>
          </div>
        </div>

        <!-- ===== PARENT ===== -->
        <div v-else-if="isParent">
          <div class="page-header"><h1 class="page-title">👨‍👩‍👧 Thông Tin Con Em</h1></div>
          <div v-if="!childrenData.length" class="empty-card card">Chưa có thông tin con em được liên kết.</div>
          <template v-else>
            <!-- Tab chọn con -->
            <div class="tab-bar">
              <button v-for="(c, i) in childrenData" :key="i"
                class="tab-btn" :class="{ active: parentTab === i }"
                @click="parentTab = i">
                👤 {{ c.child.name }}
              </button>
            </div>
            <div v-if="childrenData[parentTab]" class="child-dashboard">
              <!-- Sub-tabs lịch học / điểm danh của từng con -->
              <div class="child-tabs">
                <div class="child-tab-content">
                  <!-- Lịch học -->
                  <div class="card">
                    <h3 class="section-title">📅 Lịch Học Sắp Tới</h3>
                    <div v-if="!childrenData[parentTab].schedules.length" class="empty-state">Không có lịch học nào.</div>
                    <div v-else class="table-wrapper mt-4">
                      <table class="table">
                        <thead><tr><th>Lớp</th><th>Bắt Đầu</th><th>Phòng</th></tr></thead>
                        <tbody>
                          <tr v-for="s in childrenData[parentTab].schedules" :key="s.id">
                            <td class="fw-bold">{{ s.class_name }}</td>
                            <td>{{ fmt(s.start_time) }}</td>
                            <td><span class="room-tag">{{ s.room }}</span></td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                  <!-- Điểm danh -->
                  <div class="card mt-4">
                    <h3 class="section-title">📋 Lịch Sử Điểm Danh</h3>
                    <div v-if="!childrenData[parentTab].attendances.length" class="empty-state">Chưa có dữ liệu điểm danh.</div>
                    <div v-else class="table-wrapper mt-4">
                      <table class="table">
                        <thead><tr><th>Lớp</th><th>Buổi</th><th>Ngày Học</th><th>Trạng Thái</th><th>Ghi Chú</th></tr></thead>
                        <tbody>
                          <tr v-for="a in childrenData[parentTab].attendances" :key="a.id">
                            <td class="fw-bold">{{ a.schedule.class_name }}</td>
                            <td><span class="session-badge">Buổi {{ a.schedule.attendance_count || 1 }}</span></td>
                            <td>{{ fmt(a.schedule.start_time) }}</td>
                            <td><span class="att-badge" :style="{ background: statusColor[a.status]+'20', color: statusColor[a.status] }">{{ statusLabel[a.status] }}</span></td>
                            <td class="text-muted">{{ a.note || '—' }}</td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                  <!-- Học phí -->
                  <div class="card mt-4">
                    <h3 class="section-title">💰 Tình Trạng Học Phí</h3>
                    <div v-if="!childrenData[parentTab]?.tuitions?.length" class="empty-state">Chưa có dữ liệu học phí.</div>
                    <div v-else class="table-wrapper mt-4">
                      <table class="table">
                        <thead><tr><th>Lớp</th><th>Học phí</th><th>Đã đóng</th><th>Trạng thái</th><th>Ngày thu</th></tr></thead>
                        <tbody>
                          <tr v-for="t in childrenData[parentTab].tuitions" :key="t.id">
                            <td class="fw-bold">{{ t.class_name }}</td>
                            <td>{{ fmtMoney(t.amount) }}</td>
                            <td :style="{ color: t.amount_paid > 0 ? '#16a34a' : 'inherit' }">{{ fmtMoney(t.amount_paid) }}</td>
                            <td>
                              <span class="status-badge" :style="{ background: t.status==='paid'?'#dcfce7':(t.status==='partially_paid'?'#fef3c7':'#fee2e2'), color: t.status==='paid'?'#16a34a':(t.status==='partially_paid'?'#d97706':'#dc2626') }">
                                {{ t.status === 'paid' ? 'Đã thanh toán' : (t.status === 'partially_paid' ? 'Đóng 1 phần' : 'Chưa đóng') }}
                              </span>
                            </td>
                            <td>{{ t.payment_date ? new Date(t.payment_date).toLocaleDateString() : '—' }}</td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </template>
        </div>
      </template>
    </div>

    <!-- ATTENDANCE MODAL (Teacher) -->
    <Teleport to="body">
      <div v-if="showModal" class="modal-backdrop" @click.self="showModal = false">
        <div class="modal" id="attendance-modal">
          <div class="modal-header">
            <div>
              <h3>✏️ Điểm Danh {{ selectedSchedule ? `Buổi ${selectedSchedule.attendance_count || 1}` : '' }}</h3>
              <p class="text-muted" style="font-size:13px">{{ selectedSchedule ? fmt(selectedSchedule.start_time) : '' }} | {{ selectedSchedule?.room }}</p>
            </div>
            <button class="modal-close" @click="showModal = false">✕</button>
          </div>
          <div class="modal-body">
            <div v-if="!attendanceStudents.length" class="loading-state" style="min-height:120px">
              <div class="big-spinner" style="width:28px;height:28px"></div>
            </div>
            <div v-else>
              <div class="att-list-header">
                <span>Học Sinh</span><span>Trạng Thái</span><span>Ghi Chú</span>
              </div>
              <div class="att-list">
                <div v-for="s in attendanceStudents" :key="s.id" class="att-row">
                  <div class="student-name">
                    <div class="student-avatar">{{ s.name.charAt(0) }}</div>
                    <div>
                      <div style="font-weight:600;font-size:14px">{{ s.name }}</div>
                      <div class="text-muted" style="font-size:11px">{{ s.email }}</div>
                    </div>
                  </div>
                  <!-- Status select: present / absent -->
                  <div class="status-select-group">
                    <label v-for="opt in ['present','absent']" :key="opt" class="status-radio" :class="`status-radio--${opt}`">
                      <input type="radio" :name="`status-${s.id}`" :value="opt" v-model="s.status" />
                      <span>{{ opt === 'present' ? '✅ Có mặt' : '❌ Vắng' }}</span>
                    </label>
                  </div>
                  <input class="form-input note-input" type="text" v-model="s.note" placeholder="Ghi chú..." />
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

.loading-state { display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:60vh;gap:16px;color:var(--color-text-muted); }
.big-spinner { width:40px;height:40px;border:3px solid rgba(79,70,229,.2);border-top-color:#4f46e5;border-radius:50%;animation:spin .8s linear infinite; }
@keyframes spin { to{transform:rotate(360deg)} }

.stats-grid { display:grid;grid-template-columns:repeat(3,1fr);gap:20px; }
.stat-card { background:#fff;border-radius:var(--radius-lg);border:1px solid var(--color-border);padding:24px;transition:transform .2s,box-shadow .2s; }
.stat-link { text-decoration:none;color:inherit;display:block; }
.stat-link:hover { transform:translateY(-3px);box-shadow:0 8px 24px rgba(0,0,0,.1); }
.stat-icon { width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px;margin-bottom:16px; }
.stat-value { font-size:36px;font-weight:800;line-height:1; }
.stat-label { font-size:14px;color:var(--color-text-muted);margin-top:6px;font-weight:500; }

.tab-bar { display:flex;gap:8px;margin-bottom:0; }
.tab-btn { padding:10px 20px;border-radius:10px 10px 0 0;border:1px solid var(--color-border);border-bottom:none;background:#f8fafc;font-size:14px;font-weight:600;cursor:pointer;color:var(--color-text-muted);transition:all .15s; }
.tab-btn.active { background:#fff;color:var(--color-primary);border-color:var(--color-primary); }

.class-header { display:flex;align-items:flex-start;justify-content:space-between;gap:16px; }
.class-name { font-size:18px;font-weight:700;margin-bottom:4px; }
.student-count { background:#f0f9ff;color:#0369a1;padding:6px 14px;border-radius:999px;font-size:13px;font-weight:600;white-space:nowrap; }
.status-badge { display:inline-block;padding:2px 10px;border-radius:999px;font-size:12px;font-weight:600; }
.status-active { background:#dcfce7;color:#15803d; }
.status-inactive { background:#fee2e2;color:#b91c1c; }
.section-title { font-size:15px;font-weight:700; }
.section-header { display:flex;align-items:center;justify-content:space-between;margin-bottom:8px; }
.empty-state { text-align:center;padding:32px;color:var(--color-text-muted);font-size:14px; }
.empty-card { text-align:center;padding:48px;color:var(--color-text-muted); }
.room-tag { background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0;padding:2px 10px;border-radius:999px;font-size:12px;font-weight:500; }
.session-badge { background:#e0e7ff;color:#3730a3;border:1px solid #c7d2fe;padding:3px 10px;border-radius:999px;font-size:12px;font-weight:700; }
.att-badge { display:inline-block;padding:3px 10px;border-radius:999px;font-size:12px;font-weight:600; }
.att-done { background:#dcfce7;color:#15803d; }
.att-pending { background:#fef3c7;color:#d97706; }
.stats-mini { display:flex;gap:8px;font-size:12px;align-items:center; }
.stat-present { color:#16a34a;font-weight:600; }
.stat-absent { color:#dc2626;font-weight:600; }
.mt-3 { margin-top: 12px; }

.child-dashboard { margin-top: 0; }
.child-tab-content { display:flex;flex-direction:column;gap:0; }

/* Attendance Modal */
.modal-backdrop { position:fixed;inset:0;background:rgba(15,23,42,.6);backdrop-filter:blur(4px);display:flex;align-items:center;justify-content:center;z-index:1000;padding:20px; }
.modal { background:#fff;border-radius:20px;width:100%;max-width:680px;max-height:88vh;display:flex;flex-direction:column;box-shadow:0 24px 60px rgba(0,0,0,.2);animation:modalIn .25s ease; }
@keyframes modalIn { from{opacity:0;transform:scale(.95)} to{opacity:1;transform:scale(1)} }
.modal-header { display:flex;align-items:flex-start;justify-content:space-between;padding:20px 24px;border-bottom:1px solid var(--color-border); }
.modal-header h3 { font-size:17px;font-weight:700; }
.modal-close { background:none;border:none;cursor:pointer;color:var(--color-text-muted);font-size:18px;padding:4px 8px;border-radius:6px;line-height:1; }
.modal-body { padding:20px 24px;overflow-y:auto;flex:1; }
.modal-footer { display:flex;gap:12px;justify-content:flex-end;padding:16px 24px;border-top:1px solid var(--color-border); }

.att-list-header { display:grid;grid-template-columns:1fr 220px 1fr;gap:12px;font-size:11px;font-weight:700;color:var(--color-text-muted);text-transform:uppercase;letter-spacing:.05em;padding:0 4px 8px;border-bottom:1px solid var(--color-border);margin-bottom:8px; }
.att-list { display:flex;flex-direction:column;gap:8px; }
.att-row { display:grid;grid-template-columns:1fr 220px 1fr;gap:12px;align-items:center;padding:10px 12px;border:1px solid var(--color-border);border-radius:10px; }
.att-row:hover { background:#f8fafc; }
.student-name { display:flex;align-items:center;gap:10px; }
.student-avatar { width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#4f46e5,#06b6d4);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;flex-shrink:0; }
.note-input { padding:7px 10px;font-size:13px; }

.status-select-group { display:flex;gap:6px;flex-wrap:wrap; }
.status-radio { display:flex;align-items:center;gap:4px;cursor:pointer; }
.status-radio input { display:none; }
.status-radio span { padding:5px 10px;border-radius:999px;font-size:12px;font-weight:600;border:1.5px solid #e2e8f0;color:#64748b;transition:all .15s; }
.status-radio input:checked + span { border-color:transparent; }
.status-radio--present input:checked + span { background:#dcfce7;color:#15803d; }
.status-radio--absent input:checked + span { background:#fee2e2;color:#dc2626; }
.status-radio--late input:checked + span { background:#fef3c7;color:#d97706; }
.status-radio:hover span { border-color:var(--color-primary); }

@media (max-width:768px) {
  .page { padding:16px; }
  .stats-grid { grid-template-columns:repeat(2,1fr); }
  .att-row,.att-list-header { grid-template-columns:1fr; }
  .att-list-header { display:none; }
  .status-select-group { flex-direction:row; }
}
</style>

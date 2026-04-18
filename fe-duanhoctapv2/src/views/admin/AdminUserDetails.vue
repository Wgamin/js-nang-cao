<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useApi } from '../../utils/api'
import AppLayout from '../../components/AppLayout.vue'

const route = useRoute()
const router = useRouter()
const { fetchApi } = useApi()

const user = ref<any>(null)
const loading = ref(true)

const loadParams = async () => {
  const id = route.params.id
  loading.value = true
  try {
    const res = await fetchApi(`/admin/users/${id}`)
    user.value = res.data
  } catch (e: any) {
    alert(e.message)
    router.push('/admin/users')
  } finally {
    loading.value = false
  }
}

// Watch for route changes to reload if admin navigates between users
import { watch } from 'vue'
watch(() => route.params.id, () => {
  if (route.name === 'admin-user-details') loadParams()
})

const formatDate = (dt: string) => {
  if (!dt) return '—'
  return new Date(dt).toLocaleString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' })
}

onMounted(loadParams)
</script>

<template>
  <AppLayout>
    <div class="page">
      <div class="page-header">
        <div>
          <button class="btn btn-outline btn-sm mb-4" @click="router.push('/admin/users')">← Quay lại danh sách</button>
          <h1 class="page-title">Chi Tiết Thông Tin User</h1>
        </div>
      </div>

      <div v-if="loading" class="loading-center"><div class="spinner-blue"></div></div>
      <div v-else-if="user" class="profile-layout">
        <!-- Sidebar profile -->
        <div class="profile-sidebar card">
          <div class="avatar-large">{{ user.name.charAt(0).toUpperCase() }}</div>
          <h2 class="profile-name">{{ user.name }}</h2>
          <div class="profile-role">
            <span class="role-badge" :class="`role-${user.roles[0]?.toLowerCase()}`">{{ user.roles[0] || 'User' }}</span>
          </div>

          <div class="profile-info-list mt-4">
            <div class="info-item">
              <span class="info-label">Email</span>
              <span class="info-value">{{ user.email }}</span>
            </div>
            <div class="info-item">
              <span class="info-label">Số điện thoại</span>
              <span class="info-value">{{ user.phone || '—' }}</span>
            </div>
            <div class="info-item">
              <span class="info-label">Địa chỉ</span>
              <span class="info-value">{{ user.address || '—' }}</span>
            </div>
            <div class="info-item">
              <span class="info-label">Ngày tạo</span>
              <span class="info-value">{{ formatDate(user.created_at) }}</span>
            </div>
          </div>
        </div>

        <!-- Main content based on role -->
        <div class="profile-main card">
          <template v-if="user.roles[0] === 'Student'">
            <div class="card-p mb-4">
              <h3 class="section-title">👨‍🏫 Thông Tin Học Sinh</h3>
              <div class="info-card bg-light mt-3">
                <div class="fw-bold">Phụ huynh liên kết:</div>
                <div v-if="user.parent" class="mt-2">
                  <p><strong>{{ user.parent.name }}</strong> ({{ user.parent.email }})</p>
                  <button class="btn btn-primary btn-sm mt-2" @click="router.push(`/admin/users/${user.parent.id}`); loadParams()">Xem Phụ Huynh</button>
                </div>
                <div v-else class="text-muted mt-2">Chưa có phụ huynh liên kết.</div>
              </div>
            </div>

            <div class="card-p mb-4">
              <h3 class="section-title">📋 Trạng Thái Học Phí</h3>
              <div class="table-wrapper">
                <table class="table">
                  <thead><tr><th>Lớp Học</th><th>Tổng</th><th>Đã Đóng</th><th>Trạng Thái</th></tr></thead>
                  <tbody>
                    <tr v-for="t in user.tuitions" :key="t.id">
                      <td>{{ t.study_class?.name }}</td>
                      <td>{{ Number(t.amount || 0).toLocaleString('vi-VN') }} ₫</td>
                      <td>{{ Number(t.amount_paid || 0).toLocaleString('vi-VN') }} ₫</td>
                      <td>
                        <span class="role-badge" :class="t.status==='paid'?'role-student':t.status==='unpaid'?'role-admin':'role-parent'">
                          {{ t.status==='paid'?'Đã Đóng':t.status==='unpaid'?'Chưa Đóng':'Một Phần' }}
                        </span>
                      </td>
                    </tr>
                    <tr v-if="!user.tuitions?.length"><td colspan="4" class="text-muted text-center">Không có hóa đơn</td></tr>
                  </tbody>
                </table>
              </div>
            </div>
            
            <div class="card-p mb-4">
              <h3 class="section-title">📅 Điểm Danh / Lịch Học</h3>
              <div class="table-wrapper">
                <table class="table">
                  <thead><tr><th>Lớp</th><th>Ngày Học</th><th>Phòng</th><th>Lịch Sử</th></tr></thead>
                  <tbody>
                    <tr v-for="a in user.attendances" :key="a.id">
                      <td>{{ a.schedule?.study_class?.name }}</td>
                      <td>{{ formatDate(a.schedule?.start_time) }}</td>
                      <td>{{ a.schedule?.room }}</td>
                      <td :style="a.status === 'present' ? 'color:#15803d' : 'color:#b91c1c'">
                        <strong>{{ a.status === 'present' ? '✅ Có mặt' : '❌ Vắng' }}</strong>
                      </td>
                    </tr>
                    <tr v-if="!user.attendances?.length"><td colspan="4" class="text-muted text-center">Không có bản ghi</td></tr>
                  </tbody>
                </table>
              </div>
            </div>
          </template>

          <template v-else-if="user.roles[0] === 'Teacher'">
            <div class="card-p mb-4">
              <h3 class="section-title">👩‍🏫 Các Lớp Quản Lý</h3>
              <div class="table-wrapper">
                <table class="table">
                  <thead><tr><th>Tên Lớp</th><th>Số Học Sinh</th><th>Lịch Trình</th></tr></thead>
                  <tbody>
                    <tr v-for="c in user.teaching_classes" :key="c.id">
                      <td class="fw-bold">{{ c.name }}</td>
                      <td>{{ c.students?.length || 0 }}</td>
                      <td>{{ c.schedules?.length || 0 }} buổi học</td>
                    </tr>
                    <tr v-if="!user.teaching_classes?.length"><td colspan="3" class="text-muted text-center">Chưa phân lớp</td></tr>
                  </tbody>
                </table>
              </div>
            </div>
          </template>

          <template v-else-if="user.roles[0] === 'Parent'">
            <div class="card-p mb-4">
              <h3 class="section-title">👨‍👩‍👧 Quản Lý Học Sinh (Các con)</h3>
              <div class="children-list">
                <div v-for="child in user.children" :key="child.id" class="child-item">
                  <div class="child-avatar">{{ child.name.charAt(0) }}</div>
                  <div style="flex:1">
                    <div class="fw-bold" style="font-size:14px">{{ child.name }}</div>
                    <div class="text-muted" style="font-size:12px">{{ child.email }}</div>
                  </div>
                  <button class="btn btn-outline btn-sm" @click="router.push(`/admin/users/${child.id}`); loadParams()">Xem Chi Tiết</button>
                </div>
                <div v-if="!user.children?.length" class="text-muted">Chưa có học sinh liên kết.</div>
              </div>
            </div>
          </template>

          <template v-else-if="user.roles[0] === 'Admin'">
            <h3 class="section-title">🛡️ Quản Trị Viên</h3>
            <p class="text-muted mt-2">Đầy đủ quyền hạn truy cập hệ thống.</p>
          </template>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
.page { padding: 28px 32px; }
.page-header { margin-bottom: 24px; }
.page-title { font-size: 24px; font-weight: 800; }
.mb-4 { margin-bottom: 16px; }
.mt-4 { margin-top: 16px; }
.mt-3 { margin-top: 12px; }
.mt-2 { margin-top: 8px; }

.loading-center { display: flex; justify-content: center; padding: 40px; }
.spinner-blue { width: 32px; height: 32px; border: 3px solid rgba(79,70,229,.2); border-top-color: #4f46e5; border-radius: 50%; animation: spin .8s linear infinite; }
@keyframes spin { to { transform: rotate(360deg) } }

.profile-layout { display: grid; grid-template-columns: 320px 1fr; gap: 24px; }

.profile-sidebar { padding: 32px 24px; text-align: center; }
.avatar-large { width: 90px; height: 90px; border-radius: 50%; background: linear-gradient(135deg, #4f46e5, #06b6d4); color: white; display: flex; align-items: center; justify-content: center; font-size: 36px; font-weight: 800; margin: 0 auto 16px; box-shadow: 0 8px 20px rgba(79,70,229,.3); }
.profile-name { font-size: 20px; font-weight: 800; margin-bottom: 8px; }
.role-badge { display: inline-block; padding: 4px 14px; border-radius: 999px; font-size: 12px; font-weight: 700; }
.role-admin { background: #fee2e2; color: #b91c1c; }
.role-teacher { background: #e0f2fe; color: #0369a1; }
.role-student { background: #dcfce7; color: #15803d; }
.role-parent { background: #fef3c7; color: #d97706; }

.profile-info-list { text-align: left; background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid var(--color-border); }
.info-item { display: flex; flex-direction: column; padding-bottom: 12px; margin-bottom: 12px; border-bottom: 1px dashed var(--color-border); gap: 4px; }
.info-item:last-child { margin-bottom: 0; padding-bottom: 0; border-bottom: none; }
.info-label { font-size: 12px; color: var(--color-text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
.info-value { font-size: 14px; font-weight: 500; word-break: break-all; margin-top: 4px; }

.profile-main { padding: 0; background: transparent; box-shadow: none; border: none; }
.card-p { background: #fff; padding: 24px; border-radius: 16px; border: 1px solid var(--color-border); }
.section-title { font-size: 18px; font-weight: 800; margin-bottom: 16px; border-bottom: 1px solid var(--color-border); padding-bottom: 12px; }
.info-card { padding: 20px; border-radius: 12px; border: 1px solid var(--color-border); }
.bg-light { background: #f8fafc; }

.children-list { display: flex; flex-direction: column; gap: 12px; }
.child-item { display: flex; align-items: center; gap: 12px; background: white; padding: 12px; border: 1px solid var(--color-border); border-radius: 10px; }
.child-avatar { width: 40px; height: 40px; border-radius: 8px; background: var(--color-primary-light); color: var(--color-primary); display: flex; align-items: center; justify-content: center; font-size: 16px; font-weight: 700; }

@media (max-width: 900px) {
  .profile-layout { grid-template-columns: 1fr; }
}
</style>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useApi } from '../utils/api'
import AppLayout from '../components/AppLayout.vue'

const { fetchApi } = useApi()
const user = ref<any>(null)
const roles = ref<string[]>([])
const loading = ref(true)

const loadProfile = async () => {
  try {
    const res = await fetchApi('/auth/me')
    user.value = res.data.user
    roles.value = res.data.roles
  } catch (e: any) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

const formatDate = (dt: string) => {
  if (!dt) return '—'
  return new Date(dt).toLocaleString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' })
}

onMounted(loadProfile)
</script>

<template>
  <AppLayout>
    <div class="page">
      <div class="page-header">
        <h1 class="page-title">Hồ Sơ Cá Nhân</h1>
        <p class="text-muted">Thông tin tài khoản của bạn</p>
      </div>

      <div v-if="loading" class="loading-center"><div class="spinner-blue"></div></div>
      <div v-else-if="user" class="profile-layout">
        <div class="profile-sidebar card">
          <div class="avatar-large">{{ user.name.charAt(0).toUpperCase() }}</div>
          <h2 class="profile-name">{{ user.name }}</h2>
          <div class="profile-role">
            <span class="role-badge" :class="`role-${roles[0]?.toLowerCase()}`">{{ roles[0] || 'User' }}</span>
          </div>
        </div>

        <div class="profile-main card">
          <h3 class="section-title">Thông tin chung</h3>
          <div class="form-grid mt-4">
            <div class="info-group">
              <label>Họ và tên</label>
              <div class="info-box">{{ user.name }}</div>
            </div>
            <div class="info-group">
              <label>Địa chỉ Email</label>
              <div class="info-box">{{ user.email }}</div>
            </div>
            <div class="info-group">
              <label>Số điện thoại</label>
              <div class="info-box">{{ user.phone || 'Chưa cập nhật' }}</div>
            </div>
            <div class="info-group">
              <label>Tham gia từ</label>
              <div class="info-box">{{ formatDate(user.created_at) }}</div>
            </div>
            <div class="info-group" style="grid-column:span 2">
              <label>Địa chỉ</label>
              <div class="info-box">{{ user.address || 'Chưa cập nhật' }}</div>
            </div>
          </div>
          <div class="mt-4 text-center">
            <p class="text-muted" style="font-size:13px">Để thay đổi thông tin, vui lòng liên hệ Ban quản trị.</p>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
.page { padding: 28px 32px; }
.page-header { margin-bottom: 24px; }
.page-title { font-size: 24px; font-weight: 800; margin-bottom: 4px; }
.mt-4 { margin-top: 20px; }

.loading-center { display: flex; justify-content: center; padding: 40px; }
.spinner-blue { width: 32px; height: 32px; border: 3px solid rgba(79,70,229,.2); border-top-color: #4f46e5; border-radius: 50%; animation: spin .8s linear infinite; }
@keyframes spin { to { transform: rotate(360deg) } }

.profile-layout { display: grid; grid-template-columns: 300px 1fr; gap: 24px; }

.profile-sidebar { padding: 40px 24px; text-align: center; }
.avatar-large { width: 100px; height: 100px; border-radius: 50%; background: linear-gradient(135deg, #4f46e5, #06b6d4); color: white; display: flex; align-items: center; justify-content: center; font-size: 42px; font-weight: 800; margin: 0 auto 16px; box-shadow: 0 10px 25px rgba(79,70,229,.3); }
.profile-name { font-size: 22px; font-weight: 800; margin-bottom: 10px; }
.role-badge { display: inline-block; padding: 5px 16px; border-radius: 999px; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
.role-admin { background: #fee2e2; color: #b91c1c; }
.role-teacher { background: #e0f2fe; color: #0369a1; }
.role-student { background: #dcfce7; color: #15803d; }
.role-parent { background: #fef3c7; color: #d97706; }

.profile-main { padding: 32px; }
.section-title { font-size: 18px; font-weight: 800; border-bottom: 1px solid var(--color-border); padding-bottom: 12px; margin-bottom: 24px; }

.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.info-group label { display: block; font-size: 13px; font-weight: 700; color: var(--color-text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
.info-box { background: #f8fafc; border: 1px solid var(--color-border); padding: 12px 16px; border-radius: 10px; font-size: 15px; font-weight: 500; color: var(--color-text); min-height: 46px; display: flex; align-items: center; }

@media (max-width: 900px) {
  .profile-layout { grid-template-columns: 1fr; }
}
</style>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useApi } from '../utils/api'

const email = ref('')
const password = ref('')
const loading = ref(false)
const errorMsg = ref('')

const router = useRouter()
const { fetchApi } = useApi()

const handleLogin = async () => {
  if (!email.value || !password.value) {
    errorMsg.value = 'Vui lòng nhập đầy đủ thông tin!'
    return
  }
  loading.value = true
  errorMsg.value = ''
  try {
    const response = await fetchApi('/login', {
      method: 'POST',
      body: JSON.stringify({ email: email.value, password: password.value }),
    })
    if (response.status === 'success') {
      localStorage.setItem('token', response.data.token)
      localStorage.setItem('role', response.data.roles[0] ?? '')
      localStorage.setItem('userName', response.data.user.name)
      await router.push('/')
    }
  } catch (e: any) {
    errorMsg.value = e.message || 'Đăng nhập thất bại!'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="login-page">
    <!-- Left decorative panel -->
    <div class="login-banner">
      <div class="banner-content">
        <div class="banner-icon">🎓</div>
        <h1 class="banner-title">Hệ Thống<br />Quản Lý Học Thêm</h1>
        <p class="banner-subtitle">Nền tảng quản lý lớp học, điểm danh và lịch học hiệu quả cho giáo viên và học sinh.</p>
        <div class="banner-features">
          <div class="feature-item">✅ Phân quyền 4 tầng: Admin · GV · HS · Phụ huynh</div>
          <div class="feature-item">✅ Điểm danh 3 trạng thái: Có mặt · Vắng · Muộn</div>
          <div class="feature-item">✅ Quản lý học phí & thu tiền trực tuyến</div>
          <div class="feature-item">✅ Phụ huynh theo dõi con em từ xa</div>
        </div>
      </div>
    </div>

    <!-- Right login form -->
    <div class="login-form-side">
      <div class="login-card">
        <div class="login-header">
          <h2 class="login-title">Đăng nhập</h2>
          <p class="login-desc">Vui lòng nhập email và mật khẩu để tiếp tục.</p>
        </div>

        <form @submit.prevent="handleLogin" class="login-form" id="login-form">
          <div class="form-group">
            <label class="form-label" for="email-input">Email</label>
            <input
              id="email-input"
              class="form-input"
              type="email"
              v-model="email"
              placeholder="admin@test.com"
              autocomplete="email"
            />
          </div>
          <div class="form-group">
            <label class="form-label" for="password-input">Mật khẩu</label>
            <input
              id="password-input"
              class="form-input"
              type="password"
              v-model="password"
              placeholder="••••••••"
              autocomplete="current-password"
            />
          </div>

          <div v-if="errorMsg" class="error-msg">⚠️ {{ errorMsg }}</div>

          <button id="login-btn" type="submit" class="btn btn-primary login-btn" :disabled="loading">
            <span v-if="loading" class="spinner"></span>
            <span>{{ loading ? 'Đang đăng nhập...' : 'Đăng nhập' }}</span>
          </button>
        </form>

        <div class="demo-accounts">
          <p class="demo-title">Tài khoản demo:</p>
          <div class="demo-list">
            <button class="demo-item" @click="email = 'admin@test.com'; password = 'password'">
              <span class="badge badge-admin">Admin</span> admin@test.com
            </button>
            <button class="demo-item" @click="email = 'teacher@test.com'; password = 'password'">
              <span class="badge badge-teacher">Giáo viên</span> teacher@test.com
            </button>
            <button class="demo-item" @click="email = 'student@test.com'; password = 'password'">
              <span class="badge badge-student">Học sinh</span> student@test.com
            </button>
            <button class="demo-item" @click="email = 'parent@test.com'; password = 'password'">
              <span class="badge badge-parent">Phụ Huynh</span> parent@test.com
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.login-page {
  display: flex;
  min-height: 100vh;
}

/* Banner */
.login-banner {
  flex: 1;
  background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #06b6d4 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 60px 48px;
  position: relative;
  overflow: hidden;
}
.login-banner::before {
  content: '';
  position: absolute;
  inset: 0;
  background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Ccircle cx='30' cy='30' r='20'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}
.banner-content { position: relative; z-index: 1; color: #fff; max-width: 420px; }
.banner-icon { font-size: 64px; margin-bottom: 20px; }
.banner-title {
  font-size: 36px;
  font-weight: 800;
  line-height: 1.2;
  margin-bottom: 16px;
}
.banner-subtitle {
  font-size: 16px;
  opacity: 0.85;
  line-height: 1.7;
  margin-bottom: 32px;
}
.banner-features { display: flex; flex-direction: column; gap: 12px; }
.feature-item { font-size: 14px; opacity: 0.9; }

/* Form side */
.login-form-side {
  width: 480px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 48px 36px;
  background: #fff;
}
.login-card { width: 100%; }
.login-header { margin-bottom: 32px; }
.login-title { font-size: 28px; font-weight: 800; color: #1e293b; margin-bottom: 8px; }
.login-desc { color: #64748b; font-size: 15px; }

.error-msg {
  background: #fef2f2;
  border: 1px solid #fecaca;
  color: #dc2626;
  padding: 10px 14px;
  border-radius: 8px;
  font-size: 14px;
  margin-bottom: 16px;
}

.login-btn { width: 100%; justify-content: center; padding: 14px; font-size: 16px; }

/* Demo accounts */
.demo-accounts {
  margin-top: 28px;
  padding-top: 20px;
  border-top: 1px solid #e2e8f0;
}
.demo-title { font-size: 13px; font-weight: 600; color: #94a3b8; margin-bottom: 10px; }
.demo-list { display: flex; flex-direction: column; gap: 8px; }
.demo-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 8px 12px;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  background: #f8fafc;
  cursor: pointer;
  font-size: 13px;
  color: #475569;
  transition: all 0.15s ease;
  text-align: left;
}
.demo-item:hover { border-color: #4f46e5; background: #eef2ff; color: #4f46e5; }
.badge-parent { background: #fef3c7; color: #d97706; }

@media (max-width: 900px) {
  .login-banner { display: none; }
  .login-form-side { width: 100%; padding: 32px 24px; }
}
</style>

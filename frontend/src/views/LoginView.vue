<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/utils/api'

const router = useRouter()
const username = ref('')
const password = ref('')
const showPassword = ref(false)
const rememberLogin = ref(false)
const isLoading = ref(false)
const errorMessage = ref('')

const handleLogin = async () => {
    if (!username.value || !password.value) {
        errorMessage.value = 'Vui lòng điền đầy đủ thông tin.'
        return;
    }

    isLoading.value = true;
    errorMessage.value = '';
    
    try {
        const response = await api.post('/login', {
            email: username.value,
            password: password.value
        });

        if (response && response.data && response.data.token) {
            // Lưu token và thông tin user
            localStorage.setItem('token', response.data.token);
            localStorage.setItem('user', JSON.stringify(response.data.user));
            
            // Chuyển hướng
            router.push('/admin/dashboard');
        } else {
            errorMessage.value = 'Đăng nhập thất bại. Vui lòng thử lại.';
        }
    } catch (error) {
        console.error('Login error:', error);
        errorMessage.value = error.message || 'Tài khoản hoặc mật khẩu không chính xác.';
    } finally {
        isLoading.value = false;
    }
}
</script>

<template>
  <div class="login-wrapper">
    <!-- Background Decoration -->
    <div class="background-decor">
      <div class="blur-circle circle-1"></div>
      <div class="blur-circle circle-2"></div>
    </div>

    <!-- Main Container -->
    <main class="login-container">
      <!-- Logo & Branding -->
      <div class="branding">
        <div class="logo-icon-box">
          <span class="material-symbols-outlined">school</span>
        </div>
        <h1 class="brand-title">EduManager</h1>
        <p class="brand-subtitle">Hệ thống quản lý trung tâm giáo dục</p>
      </div>

      <!-- Login Card -->
      <div class="login-card">
        <div class="card-header">
          <h2>Chào mừng trở lại</h2>
          <p>Vui lòng đăng nhập vào tài khoản của bạn</p>
        </div>

        <form class="login-form" @submit.prevent="handleLogin">
          <div v-if="errorMessage" class="error-alert">
            {{ errorMessage }}
          </div>

          <!-- Email Input -->
          <div class="form-group">
            <label for="username">Email</label>
            <div class="input-container">
              <span class="material-symbols-outlined input-icon">person</span>
              <input 
                id="username" 
                v-model="username"
                type="email" 
                placeholder="Nhập email" 
                required 
              />
            </div>
          </div>

          <!-- Password Input -->
          <div class="form-group">
            <div class="label-flex">
              <label for="password">Mật khẩu</label>
              <a href="#" class="forgot-link">Quên mật khẩu?</a>
            </div>
            <div class="input-container">
              <span class="material-symbols-outlined input-icon">lock</span>
              <input 
                id="password" 
                v-model="password"
                :type="showPassword ? 'text' : 'password'" 
                placeholder="••••••••" 
                required 
              />
              <button 
                type="button" 
                class="toggle-password" 
                @click="showPassword = !showPassword"
              >
                <span class="material-symbols-outlined">{{ showPassword ? 'visibility_off' : 'visibility' }}</span>
              </button>
            </div>
          </div>

          <!-- Remember Me -->
          <div class="remember-me">
            <input 
              id="remember" 
              v-model="rememberLogin"
              type="checkbox"
            />
            <label for="remember">Ghi nhớ đăng nhập</label>
          </div>

          <!-- Login Button -->
          <button 
            type="submit" 
            class="submit-btn"
            :disabled="isLoading"
          >
            <span v-if="isLoading" class="material-symbols-outlined spinner">progress_activity</span>
            {{ isLoading ? 'Đang đăng nhập...' : 'Đăng nhập' }}
          </button>
        </form>

        <!-- Bottom Action -->
        <div class="card-footer">
          <p>
            Chưa có tài khoản? 
            <a href="#">Liên hệ Quản trị viên</a>
          </p>
        </div>
      </div>

      <!-- Footer Meta -->
      <footer class="app-footer">
        <p class="copyright">© 2024 EDUMANAGER. ALL RIGHTS RESERVED.</p>
        <div class="footer-links">
          <a href="#">Hỗ trợ</a>
          <span class="divider">•</span>
          <a href="#">Điều khoản</a>
          <span class="divider">•</span>
          <a href="#">Bảo mật</a>
        </div>
      </footer>
    </main>
  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

.login-wrapper {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #f7f9fb;
  color: #191c1e;
  font-family: 'Inter', sans-serif;
  padding: 24px;
  position: relative;
  overflow: hidden;
  z-index: 0;
}

/* Background Decorations */
.background-decor {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: -1;
  pointer-events: none;
}

.blur-circle {
  position: absolute;
  border-radius: 50%;
  filter: blur(120px);
}

.circle-1 {
  top: -10%;
  left: -10%;
  width: 40%;
  height: 40%;
  background: rgba(0, 74, 198, 0.05); /* primary with 5% opacity */
}

.circle-2 {
  top: 60%;
  right: -5%;
  width: 30%;
  height: 30%;
  background: rgba(172, 191, 255, 0.1); /* secondary-container equivalent */
  filter: blur(100px);
}

/* Container */
.login-container {
  width: 100%;
  max-width: 400px;
  z-index: 10;
  animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Branding */
.branding {
  text-align: center;
  margin-bottom: 40px;
}

.logo-icon-box {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 64px;
  height: 64px;
  border-radius: 16px;
  background: linear-gradient(135deg, #004ac6, #2563eb);
  color: #ffffff;
  margin-bottom: 16px;
  box-shadow: 0 10px 20px rgba(0, 74, 198, 0.2);
}

.logo-icon-box .material-symbols-outlined {
  font-size: 32px;
}

.brand-title {
  font-size: 30px;
  font-weight: 800;
  letter-spacing: -0.02em;
  color: #191c1e;
  margin: 0;
}

.brand-subtitle {
  font-size: 15px;
  color: #434655;
  margin: 8px 0 0;
  font-weight: 500;
}

/* Login Card */
.login-card {
  background: #ffffff;
  border-radius: 20px;
  padding: 32px;
  border: 1px solid rgba(195, 198, 215, 0.3);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
}

.card-header {
  margin-bottom: 32px;
}

.card-header h2 {
  font-size: 20px;
  font-weight: 700;
  color: #191c1e;
  margin: 0 0 6px;
}

.card-header p {
  font-size: 14px;
  color: #434655;
  margin: 0;
}

/* Form Styles */
.login-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.error-alert {
  padding: 12px;
  background: rgba(186, 26, 26, 0.1);
  border: 1px solid rgba(186, 26, 26, 0.2);
  color: #ba1a1a;
  border-radius: 8px;
  font-size: 13px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-group label {
  font-size: 14px;
  font-weight: 600;
  color: #191c1e;
}

.label-flex {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.forgot-link {
  font-size: 13px;
  color: #004ac6;
  text-decoration: none;
  font-weight: 600;
}

.forgot-link:hover {
  text-decoration: underline;
}

.input-container {
  position: relative;
  display: flex;
  align-items: center;
}

.input-icon {
  position: absolute;
  left: 12px;
  font-size: 20px;
  color: #737686;
  pointer-events: none;
}

.input-container input {
  width: 100%;
  padding: 12px 12px 12px 40px;
  border-radius: 12px;
  border: 1px solid rgba(195, 198, 215, 0.5);
  font-size: 15px;
  background: #ffffff;
  color: #191c1e;
  transition: all 0.2s ease;
  outline: none;
}

.input-container input:focus {
  border-color: #004ac6;
  box-shadow: 0 0 0 4px rgba(0, 74, 198, 0.1);
}

.input-container input::placeholder {
  color: rgba(115, 118, 134, 0.4);
}

.toggle-password {
  position: absolute;
  right: 12px;
  background: none;
  border: none;
  padding: 4px;
  cursor: pointer;
  color: #737686;
  display: flex;
  align-items: center;
}

.toggle-password:hover {
  color: #191c1e;
}

.remember-me {
  display: flex;
  align-items: center;
  gap: 10px;
}

.remember-me input {
  width: 18px;
  height: 18px;
  accent-color: #004ac6;
  cursor: pointer;
}

.remember-me label {
  font-size: 14px;
  color: #434655;
  cursor: pointer;
}

.submit-btn {
  width: 100%;
  padding: 14px;
  background: linear-gradient(to right, #004ac6, #2563eb);
  color: #ffffff;
  border: none;
  border-radius: 12px;
  font-size: 16px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.submit-btn:hover {
  opacity: 0.9;
}

.submit-btn:active {
  transform: scale(0.98);
}

.submit-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.spinner {
  animation: spin 1s linear infinite;
  font-size: 20px;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.card-footer {
  margin-top: 24px;
  text-align: center;
  padding-top: 20px;
  border-top: 1px solid rgba(195, 198, 215, 0.2);
}

.card-footer p {
  font-size: 14px;
  color: #434655;
}

.card-footer a {
  color: #004ac6;
  font-weight: 700;
  text-decoration: none;
}

.card-footer a:hover {
  text-decoration: underline;
}

/* Footer Meta */
.app-footer {
  margin-top: 48px;
  text-align: center;
}

.copyright {
  font-size: 11px;
  font-weight: 600;
  color: #737686;
  letter-spacing: 0.05em;
  margin-bottom: 12px;
  text-transform: uppercase;
}

.footer-links {
  display: flex;
  justify-content: center;
  gap: 12px;
  align-items: center;
}

.footer-links a {
  font-size: 12px;
  color: #737686;
  text-decoration: none;
}

.footer-links a:hover {
  color: #004ac6;
}

.divider {
  color: rgba(195, 198, 215, 0.3);
  font-size: 10px;
}
</style>

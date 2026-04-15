<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const email = ref('')
const password = ref('')
const showPassword = ref(false)
const rememberLogin = ref(false)

const handleLogin = () => {
    // Basic mock navigation to admin dashboard
    if (email.value && password.value) {
        router.push('/admin/dashboard')
    }
}
</script>

<template>
  <div class="login-page">
    <!-- Background Decoration -->
    <div class="bg-decorations">
      <div class="decor-circle circle-1"></div>
      <div class="decor-circle circle-2"></div>
    </div>

    <main class="login-container">
      <!-- Logo & Branding -->
      <div class="brand-header">
        <div class="logo-box">
          <span class="material-symbols-outlined logo-icon">school</span>
        </div>
        <h1 class="brand-name">EduManager</h1>
        <p class="brand-slogan">Hệ thống quản lý trung tâm giáo dục</p>
      </div>

      <!-- Login Card -->
      <div class="login-card">
        <div class="card-header">
          <h2>Chào mừng trở lại</h2>
          <p>Vui lòng đăng nhập vào tài khoản của bạn</p>
        </div>

        <form class="login-form" @submit.prevent="handleLogin">
          <!-- Email -->
          <div class="form-group">
            <label for="email">Địa chỉ Email</label>
            <div class="input-wrapper">
              <span class="material-symbols-outlined input-icon">mail</span>
              <input 
                type="email" 
                id="email" 
                v-model="email" 
                placeholder="username@email.com" 
                required 
              />
            </div>
          </div>

          <!-- Password -->
          <div class="form-group">
            <div class="label-row">
              <label for="password">Mật khẩu</label>
              <a href="#" class="forgot-link">Quên mật khẩu?</a>
            </div>
            <div class="input-wrapper">
              <span class="material-symbols-outlined input-icon">lock</span>
              <input 
                :type="showPassword ? 'text' : 'password'" 
                id="password" 
                v-model="password" 
                placeholder="••••••••" 
                required 
              />
              <button 
                type="button" 
                class="btn-toggle-pass" 
                @click="showPassword = !showPassword"
              >
                <span class="material-symbols-outlined input-icon">{{ showPassword ? 'visibility_off' : 'visibility' }}</span>
              </button>
            </div>
          </div>

          <!-- Remember Me -->
          <div class="remember-row">
             <input type="checkbox" id="remember" v-model="rememberLogin" />
             <label for="remember">Ghi nhớ đăng nhập</label>
          </div>

          <!-- Submit -->
          <button type="submit" class="btn-submit">
             Đăng nhập
          </button>
        </form>

        <div class="card-footer">
           <p>Chưa có tài khoản? <a href="#">Liên hệ Quản trị viên</a></p>
        </div>
      </div>

      <!-- Footer Meta -->
      <footer class="login-footer">
        <p class="copyright">© 2024 EDUMANAGER. ALL RIGHTS RESERVED.</p>
        <div class="footer-links">
          <a href="#">Hỗ trợ</a>
          <span class="dot">•</span>
          <a href="#">Điều khoản</a>
          <span class="dot">•</span>
          <a href="#">Bảo mật</a>
        </div>
      </footer>
    </main>
  </div>
</template>

<style scoped>
.login-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #f7f9fb;
  color: #191c1e;
  font-family: 'Inter', sans-serif;
  position: relative;
  overflow: hidden;
  padding: 24px;
}

/* Background Blurs */
.bg-decorations {
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  pointer-events: none;
  z-index: 0;
}

.decor-circle {
  position: absolute;
  border-radius: 50%;
  filter: blur(120px);
}

.circle-1 {
  top: -10%;
  left: -10%;
  width: 40%;
  padding-bottom: 40%;
  background-color: rgba(0, 74, 198, 0.05); /* primary/5 */
}

.circle-2 {
  top: 60%;
  right: -5%;
  width: 30%;
  padding-bottom: 30%;
  background-color: rgba(172, 191, 255, 0.1); 
  filter: blur(100px);
}

.login-container {
  width: 100%;
  max-width: 440px;
  position: relative;
  z-index: 1;
  animation: slideUp 0.5s ease-out;
}

@keyframes slideUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

/* Header */
.brand-header {
  text-align: center;
  margin-bottom: 40px;
}

.logo-box {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 64px;
  height: 64px;
  border-radius: 16px;
  background: linear-gradient(135deg, #004ac6, #2563eb);
  color: #ffffff;
  box-shadow: 0 10px 25px -5px rgba(0, 74, 198, 0.3);
  margin-bottom: 16px;
}

.logo-icon {
  font-size: 32px;
}

.brand-name {
  font-size: 30px;
  font-weight: 800;
  letter-spacing: -0.025em;
  color: #191c1e;
  margin: 0;
}

.brand-slogan {
  font-size: 15px;
  font-weight: 500;
  color: #434655;
  margin: 8px 0 0 0;
}

/* Card */
.login-card {
  background-color: #ffffff;
  border-radius: 16px;
  padding: 40px 32px;
  box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -2px rgba(0,0,0,0.02);
}

.card-header {
  margin-bottom: 32px;
}

.card-header h2 {
  font-size: 20px;
  font-weight: 700;
  color: #191c1e;
  margin: 0 0 4px 0;
}

.card-header p {
  font-size: 14px;
  color: #434655;
  margin: 0;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group label {
  font-size: 14px;
  font-weight: 600;
  color: #191c1e;
  margin-bottom: 8px;
}

.label-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
}

.label-row label {
  margin-bottom: 0;
}

.forgot-link {
  font-size: 13px;
  font-weight: 600;
  color: #004ac6;
  text-decoration: none;
  transition: color 0.2s;
}

.forgot-link:hover {
  color: #003ea8;
}

.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.input-icon {
  position: absolute;
  left: 14px;
  font-size: 20px;
  color: #737686;
  pointer-events: none;
}

.input-wrapper input {
  width: 100%;
  padding: 14px 14px 14px 44px;
  background-color: #ffffff;
  border: 1px solid rgba(195, 198, 215, 0.5);
  border-radius: 12px;
  font-size: 15px;
  color: #191c1e;
  outline: none;
  transition: all 0.2s;
}

.input-wrapper input::placeholder {
  color: rgba(115, 118, 134, 0.5);
}

.input-wrapper input:focus {
  border-color: #004ac6;
  box-shadow: 0 0 0 4px rgba(0, 74, 198, 0.1);
}

.btn-toggle-pass {
  position: absolute;
  right: 14px;
  background: transparent;
  border: none;
  padding: 0;
  cursor: pointer;
  display: flex;
  color: #737686;
  transition: color 0.2s;
}

.btn-toggle-pass .input-icon {
  position: static;
  pointer-events: auto;
}

.btn-toggle-pass:hover {
  color: #191c1e;
}

.remember-row {
  display: flex;
  align-items: center;
  gap: 10px;
}

.remember-row input[type="checkbox"] {
  width: 18px;
  height: 18px;
  border-radius: 4px;
  border: 1px solid rgba(195, 198, 215, 0.8);
  accent-color: #004ac6;
  cursor: pointer;
}

.remember-row label {
  font-size: 14px;
  color: #434655;
  cursor: pointer;
  user-select: none;
}

.btn-submit {
  width: 100%;
  padding: 16px;
  background: linear-gradient(to right, #004ac6, #2563eb);
  color: #ffffff;
  border: none;
  border-radius: 12px;
  font-size: 16px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 0 2px 4px rgba(0, 74, 198, 0.1);
}

.btn-submit:hover {
  opacity: 0.9;
}

.btn-submit:active {
  transform: scale(0.98);
}

.card-footer {
  margin-top: 32px;
  padding-top: 24px;
  border-top: 1px solid rgba(195, 198, 215, 0.2);
  text-align: center;
}

.card-footer p {
  font-size: 14px;
  color: #434655;
  margin: 0;
}

.card-footer a {
  font-weight: 700;
  color: #004ac6;
  text-decoration: none;
  transition: color 0.2s;
}

.card-footer a:hover {
  color: #003ea8;
}

/* App Footer */
.login-footer {
  margin-top: 48px;
  text-align: center;
}

.copyright {
  font-size: 12px;
  font-weight: 600;
  letter-spacing: 0.05em;
  color: #737686;
  margin: 0 0 12px 0;
}

.footer-links {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 16px;
}

.footer-links a {
  font-size: 12px;
  color: #737686;
  text-decoration: none;
  transition: color 0.2s;
}

.footer-links a:hover {
  color: #004ac6;
}

.dot {
  color: rgba(195, 198, 215, 0.4);
  font-size: 12px;
}
</style>

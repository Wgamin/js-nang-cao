<template>
  <div class="home-page">
    <!-- NAVBAR -->
    <nav class="navbar" :class="{ 'navbar-scrolled': scrolled }">
      <div class="container navbar-container">
        <div class="logo">
          <div class="logo-icon">E</div>
          <span class="logo-text">Edu<span>Manager</span></span>
        </div>
        
        <div class="nav-links" :class="{ 'nav-active': mobileMenu }">
          <a href="#features" @click="mobileMenu = false">Tính năng</a>
          <a href="#stats" @click="mobileMenu = false">Thành tựu</a>
          <a href="#about" @click="mobileMenu = false">Về chúng tôi</a>
          <router-link to="/login" class="btn btn-primary login-btn">Đăng nhập</router-link>
        </div>

        <button class="mobile-toggle" @click="mobileMenu = !mobileMenu">
          <span class="material-symbols-outlined">{{ mobileMenu ? 'close' : 'menu' }}</span>
        </button>
      </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="hero-section">
      <div class="container hero-container">
        <div class="hero-content">
          <div class="badge">Hệ thống quản lý giáo dục 4.0</div>
          <h1 class="hero-title">
            Nâng tầm quản lý <br/>
            <span class="text-gradient">Trung tâm hiện đại</span>
          </h1>
          <p class="hero-desc">
            Giải pháp toàn diện giúp tối ưu hóa quy trình vận hành, quản lý học viên và lịch học một cách thông minh, chính xác và hiệu quả.
          </p>
          <div class="hero-actions">
            <router-link to="/login" class="btn btn-primary btn-lg shadow-hover">
              Bắt đầu ngay
              <span class="material-symbols-outlined">arrow_forward</span>
            </router-link>
            <a href="#features" class="btn btn-outline btn-lg">Tìm hiểu thêm</a>
          </div>
          
          <div class="hero-trust">
            <div class="trust-avatars">
              <img src="https://i.pravatar.cc/150?u=1" alt="User">
              <img src="https://i.pravatar.cc/150?u=2" alt="User">
              <img src="https://i.pravatar.cc/150?u=3" alt="User">
              <div class="trust-more">+500</div>
            </div>
            <p>trung tâm đã tin dùng EduManager</p>
          </div>
        </div>
        
        <div class="hero-visual">
          <div class="visual-wrapper">
            <div class="abstract-shape shape-1"></div>
            <div class="abstract-shape shape-2"></div>
            <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?q=80&w=1471&auto=format&fit=crop" alt="Students" class="main-image">
            
            <!-- Floating Elements -->
            <div class="floating-card card-1">
              <div class="icon-box"><span class="material-symbols-outlined">analytics</span></div>
              <div class="card-info">
                <span>Tăng hiệu suất</span>
                <strong>+45%</strong>
              </div>
            </div>
            <div class="floating-card card-2">
              <div class="icon-box"><span class="material-symbols-outlined">groups</span></div>
              <div class="card-info">
                <span>Học viên mới</span>
                <strong>+120</strong>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- STATS SECTION -->
    <section id="stats" class="stats-section">
      <div class="container">
        <div class="stats-grid">
          <div class="stat-item">
            <h2 class="stat-number">10k+</h2>
            <p class="stat-label">Học viên đang học</p>
          </div>
          <div class="stat-item">
            <h2 class="stat-number">500+</h2>
            <p class="stat-label">Trung tâm đối tác</p>
          </div>
          <div class="stat-item">
            <h2 class="stat-number">98%</h2>
            <p class="stat-label">Khách hàng hài lòng</p>
          </div>
          <div class="stat-item">
            <h2 class="stat-number">24/7</h2>
            <p class="stat-label">Hỗ trợ kỹ thuật</p>
          </div>
        </div>
      </div>
    </section>

    <!-- FEATURES SECTION -->
    <section id="features" class="section-padding">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">Tính năng ưu việt</h2>
          <p class="section-subtitle">Tất cả những gì bạn cần để vận hành trung tâm giáo dục một cách trơn tru nhất.</p>
        </div>

        <div class="features-grid">
          <div class="card-premium feature-card">
            <div class="f-icon-wrapper"><span class="material-symbols-outlined">person_search</span></div>
            <h3>Quản lý Học viên</h3>
            <p>Theo dõi tiến độ, điểm danh và kết quả học tập của từng học viên theo thời gian thực.</p>
          </div>
          <div class="card-premium feature-card">
            <div class="f-icon-wrapper"><span class="material-symbols-outlined">calendar_month</span></div>
            <h3>Sắp xếp Lịch học</h3>
            <p>Tự động hóa lịch dạy, tránh trùng lịch và quản lý phòng học một cách tối ưu.</p>
          </div>
          <div class="card-premium feature-card">
            <div class="f-icon-wrapper"><span class="material-symbols-outlined">payments</span></div>
            <h3>Học phí & Tài chính</h3>
            <p>Quản lý thu chi, hóa đơn và nhắc nhở học phí tự động qua email hoặc hệ thống.</p>
          </div>
          <div class="card-premium feature-card">
            <div class="f-icon-wrapper"><span class="material-symbols-outlined">monitoring</span></div>
            <h3>Báo cáo Phân tích</h3>
            <p>Hệ thống báo cáo trực quan giúp lãnh đạo đưa ra quyết định dựa trên dữ liệu chính xác.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA SECTION -->
    <section class="cta-section">
      <div class="container">
        <div class="cta-box card-premium">
          <div class="cta-content">
            <h2>Sẵn sàng bắt đầu hành trình mới?</h2>
            <p>Đăng ký ngay để trải nghiệm phiên bản hoàn thiện nhất của hệ thống EduManager.</p>
            <router-link to="/login" class="btn btn-primary btn-lg">
              Tham gia ngay
              <span class="material-symbols-outlined">bolt</span>
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer">
      <div class="container footer-grid">
        <div class="footer-brand">
          <div class="logo">
            <div class="logo-icon">E</div>
            <span class="logo-text">Edu<span>Manager</span></span>
          </div>
          <p class="footer-desc">Giải pháp quản lý trung tâm giáo dục hàng đầu Việt Nam.</p>
          <div class="social-links">
            <a href="#" class="social-icon"><span class="material-symbols-outlined">public</span></a>
            <a href="#" class="social-icon"><span class="material-symbols-outlined">alternate_email</span></a>
          </div>
        </div>
        
        <div class="footer-links">
          <h4>Sản phẩm</h4>
          <ul>
            <li><a href="#">Tính năng</a></li>
            <li><a href="#">Bảng giá</a></li>
            <li><a href="#">Ứng dụng di động</a></li>
          </ul>
        </div>
        
        <div class="footer-links">
          <h4>Hỗ trợ</h4>
          <ul>
            <li><a href="#">Tài liệu</a></li>
            <li><a href="#">Trung tâm trợ giúp</a></li>
            <li><a href="#">Liên hệ</a></li>
          </ul>
        </div>
      </div>
      
      <div class="container footer-bottom">
        <p>© 2026 EduManager. Design for Excellence.</p>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const scrolled = ref(false);
const mobileMenu = ref(false);

const handleScroll = () => {
  scrolled.value = window.scrollY > 50;
};

onMounted(() => {
  window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll);
});
</script>

<style scoped>
.home-page {
  background-color: var(--surface);
  color: var(--on-surface);
  overflow-x: hidden;
}

/* NAVBAR */
.navbar {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  height: 80px;
  display: flex;
  align-items: center;
  z-index: 1000;
  transition: all 0.3s ease;
}

.navbar-scrolled {
  background: rgba(255, 255, 255, 0.85);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  height: 70px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
}

.navbar-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.logo {
  display: flex;
  align-items: center;
  gap: 10px;
}

.logo-icon {
  width: 32px;
  height: 32px;
  background: var(--primary);
  color: white;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 800;
  font-size: 1.2rem;
}

.logo-text {
  font-size: 1.4rem;
  font-weight: 700;
  font-family: var(--font-outfit);
}

.logo-text span {
  color: var(--primary);
}

.nav-links {
  display: flex;
  align-items: center;
  gap: 2.5rem;
}

.nav-links a {
  text-decoration: none;
  color: var(--on-surface-variant);
  font-weight: 500;
  transition: color 0.3s;
}

.nav-links a:hover {
  color: var(--primary);
}

.mobile-toggle {
  display: none;
  background: none;
  border: none;
  color: var(--on-surface);
  cursor: pointer;
}

/* HERO SECTION */
.hero-section {
  padding-top: 160px;
  padding-bottom: 80px;
  position: relative;
}

.hero-container {
  display: grid;
  grid-template-columns: 1fr 1fr;
  align-items: center;
  gap: 4rem;
}

.badge {
  display: inline-block;
  padding: 6px 16px;
  background: var(--surface-container-low);
  color: var(--primary);
  border-radius: 100px;
  font-size: 0.85rem;
  font-weight: 600;
  margin-bottom: 1.5rem;
}

.hero-title {
  font-size: 4rem;
  line-height: 1.1;
  margin-bottom: 1.5rem;
}

.hero-desc {
  font-size: 1.15rem;
  color: var(--on-surface-variant);
  margin-bottom: 2.5rem;
  line-height: 1.6;
  max-width: 500px;
}

.hero-actions {
  display: flex;
  gap: 1.25rem;
  margin-bottom: 3.5rem;
}

.hero-trust {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.trust-avatars {
  display: flex;
}

.trust-avatars img {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  border: 2px solid white;
  margin-left: -10px;
}

.trust-avatars img:first-child {
  margin-left: 0;
}

.trust-more {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: var(--surface-container-highest);
  border: 2px solid white;
  margin-left: -10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.75rem;
  font-weight: 700;
}

.hero-trust p {
  font-size: 0.9rem;
  color: var(--on-surface-variant);
}

.hero-visual {
  position: relative;
}

.visual-wrapper {
  position: relative;
  z-index: 1;
}

.main-image {
  width: 100%;
  border-radius: 32px;
  box-shadow: var(--shadow-xl);
  object-fit: cover;
  aspect-ratio: 4/5;
}

.abstract-shape {
  position: absolute;
  filter: blur(80px);
  z-index: -1;
  opacity: 0.4;
}

.shape-1 {
  width: 300px;
  height: 300px;
  background: var(--primary);
  top: -50px;
  right: -50px;
}

.shape-2 {
  width: 250px;
  height: 250px;
  background: #c084fc;
  bottom: -50px;
  left: -50px;
}

.floating-card {
  position: absolute;
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(8px);
  padding: 1rem;
  border-radius: 16px;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
  animation: float 4s ease-in-out infinite;
}

.card-1 {
  top: 15%;
  left: -20px;
}

.card-2 {
  bottom: 15%;
  right: -20px;
  animation-delay: 2s;
}

.icon-box {
  width: 40px;
  height: 40px;
  background: var(--primary);
  color: white;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.card-info span {
  display: block;
  font-size: 0.75rem;
  color: var(--on-surface-variant);
}

.card-info strong {
  font-size: 1.1rem;
  font-weight: 700;
}

@keyframes float {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-10px); }
}

/* STATS SECTION */
.stats-section {
  padding: 60px 0;
  background: var(--surface-container-lowest);
  border-y: 1px solid var(--surface-container);
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 2rem;
  text-align: center;
}

.stat-number {
  font-size: 2.5rem;
  color: var(--primary);
  margin-bottom: 0.5rem;
}

.stat-label {
  color: var(--on-surface-variant);
  font-weight: 500;
}

/* FEATURES SECTION */
.section-header {
  text-align: center;
  max-width: 600px;
  margin: 0 auto 4rem;
}

.section-title {
  font-size: 2.5rem;
  margin-bottom: 1rem;
}

.section-subtitle {
  color: var(--on-surface-variant);
  font-size: 1.1rem;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 2rem;
}

.f-icon-wrapper {
  width: 56px;
  height: 56px;
  background: var(--surface-container-low);
  color: var(--primary);
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 1.5rem;
  font-size: 1.75rem;
}

.feature-card h3 {
  font-size: 1.25rem;
  margin-bottom: 1rem;
}

.feature-card p {
  color: var(--on-surface-variant);
  line-height: 1.5;
}

/* CTA */
.cta-section {
  padding-bottom: 100px;
}

.cta-box {
  background: linear-gradient(135deg, var(--on-surface) 0%, #1e1b4b 100%);
  color: white;
  text-align: center;
  padding: 4rem 2rem;
  border: none;
}

.cta-content h2 {
  color: white;
  font-size: 2.5rem;
  margin-bottom: 1rem;
}

.cta-content p {
  color: #94a3b8;
  margin-bottom: 2.5rem;
  font-size: 1.1rem;
}

/* FOOTER */
.footer {
  background: var(--surface-container-lowest);
  padding: 80px 0 40px;
  border-top: 1px solid var(--surface-container);
}

.footer-grid {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr;
  gap: 4rem;
  margin-bottom: 4rem;
}

.footer-brand .logo {
  margin-bottom: 1.5rem;
}

.footer-desc {
  color: var(--on-surface-variant);
  margin-bottom: 2rem;
  max-width: 300px;
}

.social-links {
  display: flex;
  gap: 1rem;
}

.social-icon {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  background: var(--surface-container-high);
  display: flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
  color: var(--on-surface);
  transition: all 0.3s;
}

.social-icon:hover {
  background: var(--primary);
  color: white;
}

.footer-links h4 {
  margin-bottom: 1.5rem;
  font-size: 1.1rem;
}

.footer-links ul {
  list-style: none;
}

.footer-links li {
  margin-bottom: 0.75rem;
}

.footer-links a {
  text-decoration: none;
  color: var(--on-surface-variant);
  transition: color 0.3s;
}

.footer-links a:hover {
  color: var(--primary);
}

.footer-bottom {
  padding-top: 2rem;
  border-top: 1px solid var(--surface-container);
  text-align: center;
  color: var(--outline-variant);
  font-size: 0.9rem;
}

/* RESPONSIVE */
@media (max-width: 1100px) {
  .hero-title { font-size: 3.2rem; }
  .hero-container { gap: 2rem; }
}

@media (max-width: 1024px) {
  .navbar-container { padding: 0 1.5rem; }
  
  .nav-links {
    position: fixed;
    top: 0;
    right: -100%;
    width: 300px;
    height: 100vh;
    background: white;
    flex-direction: column;
    padding: 100px 2rem;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: -10px 0 30px rgba(0,0,0,0.1);
    gap: 2rem;
    align-items: flex-start;
  }
  
  .nav-links.nav-active { right: 0; }
  
  .mobile-toggle { 
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .login-btn { width: 100%; }
  
  .hero-container {
    grid-template-columns: 1fr;
    text-align: center;
    padding-top: 40px;
  }
  
  .hero-content {
    display: flex;
    flex-direction: column;
    align-items: center;
  }
  
  .hero-desc { margin-inline: auto; }
  
  .hero-actions { 
    flex-direction: column; 
    width: 100%; 
    max-width: 400px;
  }
  
  .hero-visual { margin-top: 4rem; }
  
  .stats-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 640px) {
  .hero-title { font-size: 2.5rem; }
  .stats-grid { grid-template-columns: 1fr; }
  .section-title { font-size: 2rem; }
}
</style>

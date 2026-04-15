<template>
  <div class="home-view">
    <section class="hero">
      <div class="hero-content">
        <h1 class="hero-title">
          Smart Management for <span class="highlight">Modern Education</span>
        </h1>
        <p class="hero-subtitle">
          A comprehensive suite to manage your center, track students, and optimize your teaching workflow with ease and precision.
        </p>
        <div class="hero-actions">
          <router-link to="/login" class="btn btn-primary btn-lg">Get Started</router-link>
          <a href="#features" class="btn btn-outline btn-lg">Learn More</a>
        </div>
      </div>
      <div class="hero-image">
        <div class="abstract-shape">
          <div class="circle circle-1"></div>
          <div class="circle circle-2"></div>
        </div>
        <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?q=80&w=1470&auto=format&fit=crop" alt="Education" class="hero-img">
      </div>
    </section>

    <section id="features" class="features">
      <div class="section-header">
        <h2 class="section-title">Everything you need</h2>
        <p class="section-desc">Powerful features designed to simplify center operations.</p>
      </div>
      
      <div class="features-grid">
        <div class="feature-card">
          <div class="f-icon">📊</div>
          <h3>Student Tracking</h3>
          <p>Monitor progress, attendance, and performance with detailed analytics.</p>
        </div>
        <div class="feature-card">
          <div class="f-icon">📅</div>
          <h3>Smart Scheduling</h3>
          <p>Automated classes and teacher assignments without conflicts.</p>
        </div>
        <div class="feature-card">
          <div class="f-icon">💰</div>
          <h3>Finance & Tuition</h3>
          <p>Easily manage payments, invoices, and financial reports.</p>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
</script>

<style scoped>
.home-view {
  display: flex;
  flex-direction: column;
  gap: 6rem;
  padding-bottom: 4rem;
}

.hero {
  display: grid;
  grid-template-columns: 1.2fr 0.8fr;
  align-items: center;
  gap: 4rem;
  padding: 2rem 0;
}

.hero-title {
  font-size: 3.5rem;
  font-weight: 800;
  line-height: 1.1;
  margin-bottom: 1.5rem;
  color: var(--text-main);
  letter-spacing: -0.04em;
}

.highlight {
  color: var(--primary);
  display: block;
}

.hero-subtitle {
  font-size: 1.25rem;
  color: var(--text-muted);
  margin-bottom: 2.5rem;
  max-width: 540px;
}

.hero-actions {
  display: flex;
  gap: 1rem;
}

.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.75rem 1.5rem;
  border-radius: 10px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.2s;
  cursor: pointer;
}

.btn-lg {
  padding: 1rem 2rem;
  font-size: 1.1rem;
}

.btn-primary {
  background-color: var(--primary);
  color: white;
}

.btn-primary:hover {
  background-color: var(--primary-hover);
  transform: translateY(-2px);
}

.btn-outline {
  border: 2px solid #e2e8f0;
  color: var(--text-main);
}

.btn-outline:hover {
  background-color: #f1f5f9;
  border-color: #cbd5e1;
}

.hero-image {
  position: relative;
}

.hero-img {
  width: 100%;
  aspect-ratio: 1;
  object-fit: cover;
  border-radius: 24px;
  box-shadow: var(--shadow-lg);
}

.abstract-shape {
  position: absolute;
  top: -20px;
  right: -20px;
  z-index: -1;
}

.circle {
  position: absolute;
  border-radius: 50%;
  filter: blur(40px);
}

.circle-1 {
  width: 200px;
  height: 200px;
  background: rgba(79, 70, 229, 0.2);
  top: 0;
  right: 0;
}

.circle-2 {
  width: 150px;
  height: 150px;
  background: rgba(129, 140, 248, 0.2);
  bottom: -50px;
  left: -50px;
}

.section-header {
  text-align: center;
  margin-bottom: 3.5rem;
}

.section-title {
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 1rem;
}

.section-desc {
  color: var(--text-muted);
  font-size: 1.1rem;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
}

.feature-card {
  background: white;
  padding: 2.5rem;
  border-radius: 20px;
  border: 1px solid #f1f5f9;
  transition: all 0.3s;
}

.feature-card:hover {
  transform: translateY(-5px);
  box-shadow: var(--shadow-lg);
  border-color: var(--primary);
}

.f-icon {
  font-size: 2.5rem;
  margin-bottom: 1.5rem;
}

.feature-card h3 {
  font-size: 1.25rem;
  margin-bottom: 1rem;
  font-weight: 700;
}

.feature-card p {
  color: var(--text-muted);
}

@media (max-width: 1024px) {
  .hero {
    grid-template-columns: 1fr;
    text-align: center;
    gap: 3rem;
  }
  
  .hero-content {
    display: flex;
    flex-direction: column;
    align-items: center;
  }
  
  .hero-title {
    font-size: 2.75rem;
  }
  
  .hero-image {
    max-width: 500px;
    margin: 0 auto;
  }
}
</style>

<template>
  <div class="view-page">
    <header class="header-action">
      <div class="left">
        <h1>Quản lý Lớp học</h1>
        <p>Danh sách các lớp học hiện có trong trung tâm</p>
      </div>
      <button class="btn-primary">
        <span class="material-symbols-outlined">add</span>
        Thêm Lớp mới
      </button>
    </header>

    <section class="stats-bento">
      <div v-for="stat in stats" :key="stat.label" class="mini-card">
        <div class="card-info">
          <span class="label">{{ stat.label }}</span>
          <h2 :class="stat.accent">{{ stat.value }}</h2>
          <p class="meta" :class="stat.accent">{{ stat.meta }}</p>
        </div>
        <span class="material-symbols-outlined decor">{{ stat.icon }}</span>
      </div>
    </section>

    <section class="filter-bar">
      <div class="filter-left">
        <div class="select-wrapper">
          <span class="material-symbols-outlined">search</span>
          <select>
            <option>Tất cả môn học</option>
            <option>Tiếng Anh</option>
            <option>Lập trình</option>
          </select>
        </div>
        <div class="select-wrapper">
          <span class="material-symbols-outlined">filter_list</span>
          <select>
            <option>Tất cả trạng thái</option>
            <option>Đang mở</option>
            <option>Sắp mở</option>
          </select>
        </div>
      </div>
      <div class="filter-right">
        <span>Hiển thị 1 - 3 trên 42 lớp</span>
        <div class="nav-btns">
          <button><span class="material-symbols-outlined">chevron_left</span></button>
          <button><span class="material-symbols-outlined">chevron_right</span></button>
        </div>
      </div>
    </section>

    <div class="table-wrapper">
      <table class="data-table">
        <thead>
          <tr>
            <th>Tên Lớp</th>
            <th>Môn học</th>
            <th>Giáo viên</th>
            <th>Phòng</th>
            <th>Sĩ số</th>
            <th>Học phí</th>
            <th>Trạng thái</th>
            <th class="actions"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="cls in classes" :key="cls.id">
            <td>
              <div class="class-title">
                <strong>{{ cls.name }}</strong>
                <span>Mã: {{ cls.code }}</span>
              </div>
            </td>
            <td>
              <span class="badge-subject" :class="cls.subjectClass">{{ cls.subject }}</span>
            </td>
            <td>
              <div class="teacher-cell">
                <img :src="cls.teacherAvatar" :alt="cls.teacher" />
                <span>{{ cls.teacher }}</span>
              </div>
            </td>
            <td><span class="txt-room">{{ cls.room }}</span></td>
            <td>
              <div class="capacity-wrap">
                <div class="progress">
                  <div class="bar" :class="cls.progressClass" :style="{ width: cls.students/cls.maxStudents*100 + '%' }"></div>
                </div>
                <strong>{{ cls.students }}/{{ cls.maxStudents }}</strong>
              </div>
            </td>
            <td><span class="txt-price">{{ cls.fee }}đ</span></td>
            <td>
              <span class="badge-status" :class="cls.statusBadgeClass">
                <i :class="cls.statusDotClass"></i> {{ cls.status }}
              </span>
            </td>
            <td class="actions">
              <div class="btn-group">
                <button class="btn-edit"><span class="material-symbols-outlined">edit</span></button>
                <button class="btn-del"><span class="material-symbols-outlined">delete</span></button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
const stats = [
  { label: 'Tổng số lớp', value: '42', meta: '+5 trong tháng', accent: 'primary', icon: 'hub' },
  { label: 'Lớp đang mở', value: '28', meta: '66% quy mô', accent: 'neutral', icon: 'door_open' },
  { label: 'Sắp khai giảng', value: '14', meta: 'Cần giáo viên', accent: 'warning', icon: 'calendar_today' },
];

const classes = [
  { 
    id: 1, name: 'IELTS Foundation 01', code: 'SF-IELTS-001', subject: 'TIẾNG ANH', subjectClass: 'primary',
    teacher: 'Lê Thị Mai Anh', teacherAvatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuCA_1r8Sa0-4OkqV_dUdsQ1oZ2RLx-15mk_Tmmls1UFJks57XmSEjOKfN9g8Ku3CiaqmouatAD8l88mpqk8TMUD5Rk5387l7UmRZBlXGVvXuAbZAgkWASrv_vGXvqh6GyM_b2rNV8vIuOEhmQOVrYXcSG7n3usop461uvFzyXrpLoWUglgmxJwn3e-CHc8Dv_k0FUrAMHHCeZPBs5MV6K99e_Byd8lkvkCAvRDQrHJGJ2Bnnvwu3p5ZxcagaWFk56UG8pdE8MOT6BLE',
    room: 'Phòng A.302', students: 12, maxStudents: 15, fee: '3.500.000', status: 'Open', statusBadgeClass: 'success', statusDotClass: 'bg-green', progressClass: 'bg-blue'
  },
  { 
    id: 2, name: 'React.js Advanced', code: 'SF-DEV-202', subject: 'LẬP TRÌNH', subjectClass: 'tertiary',
    teacher: 'Nguyễn Tuấn Hải', teacherAvatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuBL7JldF5E1B-ZcAErI9JjJ36C4_ZXeyIpIJuJZrBoUOST4vnjGrUmdk3FZU9qEQ7lCUhSQnSibH1UK2Xe_2znrepMvgCyqPSvuITCmIOomMcBa9F9ChAHofbOFuhk22Wq4moUa-LihS-74387bMLO84d1u6ziUPUGHVrN9G8kWtkjxNnD_rfKwF2UdMzKgpLZm65a0ZYOUXA1n1wpIf4i-SWPRNIpBYd03GeoT1L5bWmgERvTik_p-dVNi0_hRO8IuedfSxR5KqzNd',
    room: 'Phòng Lab.01', students: 5, maxStudents: 20, fee: '5.200.000', status: 'Planned', statusBadgeClass: 'warning', statusDotClass: 'bg-orange', progressClass: 'bg-orange'
  },
  { 
    id: 3, name: 'Toán Tư Duy Tiểu Học', code: 'SF-MATH-101', subject: 'TOÁN HỌC', subjectClass: 'secondary',
    teacher: 'Trần Hoàng Nam', teacherAvatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDi5tGNh1Lb6tK59veXLiPMj-GFFzY0XX_c1Wew0Z__vNvxakduMwzG5do8-_T5-yVbxubK6fplIQsXYVkvhWH7InDz1dq2Fi8K_eYSldg5Gl4tH5AqqOaiXIlAkZDJudSVBmTa-Me1DyHkCh7CENohQnQQlbX8TXXKHDQP639sGmggYneDNT7iTyEqoZfPDXIRhuCxKX7EThi7_tmDtmH3Q1pV9CTqKjDLvVoONtA_zRGclUbjAgK9-F-x2NjhN-pHJ9K_TCSNtNBf',
    room: 'Phòng C.105', students: 15, maxStudents: 15, fee: '2.800.000', status: 'Closed', statusBadgeClass: 'neutral', statusDotClass: 'bg-slate', progressClass: 'bg-slate'
  },
];
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');

.view-page {
    font-family: 'Inter', sans-serif;
    color: #1a1c1e;
    animation: fadeIn 0.5s ease;
}

@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

/* ===== HEADER ===== */
.header-action {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
}

.header-action h1 { font-size: 28px; font-weight: 800; letter-spacing: -0.02em; }
.header-action p { color: #64748b; font-size: 14px; margin-top: 4px; }

.btn-primary {
    background: #004ac6;
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 12px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    box-shadow: 0 10px 20px rgba(0, 74, 198, 0.15);
    transition: 0.2s;
}

.btn-primary:hover {
    transform: translateY(-2px);
    background: #003ea8;
}

/* ===== BENTO STATS ===== */
.stats-bento {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
    margin-bottom: 32px;
}

.mini-card {
    background: #ffffff;
    padding: 24px;
    border-radius: 20px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    overflow: hidden;
    border: 1px solid #f1f5f9;
}

.mini-card .label { font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; }
.mini-card h2 { font-size: 32px; font-weight: 800; margin: 4px 0; }
.mini-card .meta { font-size: 12px; font-weight: 600; }

.mini-card h2.primary { color: #004ac6; }
.mini-card .meta.primary { color: #16a34a; }
.mini-card h2.warning { color: #943700; }
.mini-card .meta.warning { color: #943700; }

.mini-card .decor {
    position: absolute;
    right: -10px;
    bottom: -10px;
    font-size: 80px;
    opacity: 0.03;
    transform: rotate(15deg);
}

/* ===== FILTER BAR ===== */
.filter-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #ffffff;
    padding: 16px 24px;
    border-radius: 16px;
    margin-bottom: 24px;
    border: 1px solid #f1f5f9;
}

.filter-left { display: flex; gap: 12px; }

.select-wrapper {
    background: #f8fafc;
    border-radius: 10px;
    padding: 8px 12px;
    display: flex;
    align-items: center;
    gap: 8px;
    border: 1px solid #e2e8f0;
}

.select-wrapper select {
    background: transparent;
    border: none;
    font-size: 13px;
    font-weight: 600;
    color: #475569;
    outline: none;
    cursor: pointer;
}

.select-wrapper span { font-size: 18px; color: #94a3b8; }

.filter-right { display: flex; align-items: center; gap: 16px; font-size: 13px; color: #64748b; font-weight: 500; }

.nav-btns { display: flex; gap: 4px; }
.nav-btns button {
    width: 32px;
    height: 32px;
    border: none;
    background: #f1f5f9;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.nav-btns button:hover { background: #e2e8f0; }

/* ===== TABLE ===== */
.table-wrapper {
    background: #ffffff;
    border-radius: 20px;
    overflow: hidden;
    border: 1px solid #f1f5f9;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
}

.data-table { width: 100%; border-collapse: collapse; }
.data-table th {
    background: #f8fafc;
    text-align: left;
    padding: 16px 24px;
    font-size: 11px;
    text-transform: uppercase;
    font-weight: 800;
    color: #64748b;
    letter-spacing: 0.1em;
}

.data-table td { padding: 20px 24px; border-top: 1px solid #f1f5f9; }

.class-title strong { display: block; font-size: 14px; font-weight: 700; color: #1a1c1e; }
.class-title span { font-size: 10px; color: #94a3b8; font-weight: 700; text-transform: uppercase; }

.badge-subject {
    padding: 4px 10px;
    border-radius: 8px;
    font-size: 10px;
    font-weight: 800;
}
.badge-subject.primary { background: #eff6ff; color: #004ac6; }
.badge-subject.secondary { background: #f0fdf4; color: #166534; }
.badge-subject.tertiary { background: #fff7ed; color: #943700; }

.teacher-cell { display: flex; align-items: center; gap: 12px; }
.teacher-cell img { width: 36px; height: 36px; border-radius: 10px; object-fit: cover; }
.teacher-cell span { font-size: 13px; font-weight: 600; }

.txt-room { font-size: 13px; font-weight: 600; color: #64748b; }

.capacity-wrap { display: flex; align-items: center; gap: 12px; }
.capacity-wrap .progress { height: 6px; width: 60px; background: #f1f5f9; border-radius: 99px; overflow: hidden; }
.capacity-wrap .bar { height: 100%; border-radius: 99px; transition: 0.5s; }
.capacity-wrap .bar.bg-blue { background: #004ac6; }
.capacity-wrap .bar.bg-orange { background: #943700; }
.capacity-wrap .bar.bg-slate { background: #64748b; }
.capacity-wrap strong { font-size: 12px; min-width: 40px; }

.txt-price { font-size: 14px; font-weight: 700; color: #004ac6; }

.badge-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    border-radius: 99px;
    font-size: 11px;
    font-weight: 700;
    border: 1px solid transparent;
}
.badge-status.success { background: #dcfce7; color: #166534; }
.badge-status.warning { background: #fff7ed; color: #9a3412; }
.badge-status.neutral { background: #f1f5f9; color: #475569; }

.badge-status i { width: 6px; height: 6px; border-radius: 50%; }
.badge-status i.bg-green { background: #16a34a; }
.badge-status i.bg-orange { background: #ea580c; }
.badge-status i.bg-slate { background: #64748b; }

.btn-group { display: flex; gap: 8px; }
.btn-group button {
    width: 32px;
    height: 32px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    background: #f8fafc;
    color: #64748b;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: 0.2s;
}

.btn-edit:hover { background: #eff6ff; color: #004ac6; }
.btn-del:hover { background: #fee2e2; color: #dc2626; }

@media (max-width: 768px) {
    .stats-bento { grid-template-columns: 1fr; }
    .filter-bar { flex-direction: column; gap: 16px; align-items: flex-start; }
}
</style>

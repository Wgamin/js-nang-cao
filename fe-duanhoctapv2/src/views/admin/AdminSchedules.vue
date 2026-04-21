<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useApi } from '../../utils/api'
import AppLayout from '../../components/AppLayout.vue'

const { fetchApi } = useApi()
const router = useRouter()
const schedules = ref<any[]>([])
const classesList = ref<any[]>([])
const loading = ref(true)

const loadAll = async () => {
  loading.value = true
  try {
    const [s, c] = await Promise.all([
      fetchApi('/admin/schedules'),
      fetchApi('/admin/classes-list'),
    ])
    schedules.value = s.data
    classesList.value = c.data
  } catch (e: any) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

const classesOverview = computed(() => {
  const map = new Map()
  classesList.value.forEach(c => {
    map.set(c.id, {
      id: c.id,
      name: c.name,
      teacher_name: 'Chưa cập nhật',
      session_count: 0,
      next_session: null as any
    })
  })
  
  schedules.value.forEach(s => {
    const classId = s.class_id
    if (!map.has(classId)) {
      map.set(classId, {
        id: classId,
        name: s.study_class?.name || 'Không rõ lớp',
        teacher_name: s.study_class?.teacher?.name || 'Chưa phân công',
        session_count: 0,
        next_session: null
      })
    } else {
      if(s.study_class?.teacher?.name) {
          map.get(classId).teacher_name = s.study_class.teacher.name
      }
    }
    
    map.get(classId).session_count++
    
    // Find closest upcoming session
    const sTime = new Date(s.start_time).getTime()
    const now = new Date().getTime()
    if (sTime >= now) {
      if (!map.get(classId).next_session || sTime < new Date(map.get(classId).next_session).getTime()) {
        map.get(classId).next_session = s.start_time
      }
    }
  })
  
  return Array.from(map.values())
})

const formatDate = (dt: string) =>
  dt ? new Date(dt).toLocaleString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : '—'

const goToDetail = (classId: number) => {
    router.push(`/admin/schedules/${classId}`)
}

onMounted(loadAll)
</script>

<template>
  <AppLayout>
    <div class="page">
      <div class="page-header">
        <div>
          <h1 class="page-title">📅 Tổng Quan Lịch Học Theo Lớp</h1>
          <p class="text-muted">Chọn một lớp bên dưới để quản lý chi tiết từng buổi học tương ứng.</p>
        </div>
      </div>

      <div v-if="loading" class="loading-center"><div class="spinner-blue"></div></div>
      <div v-else-if="!classesOverview.length" class="empty-state">Chưa có dữ liệu khóa học.</div>
      
      <div class="classes-grid">
        <div v-for="cls in classesOverview" :key="cls.id" class="class-card">
          <div class="class-card-header">
            <h3 class="class-name">{{ cls.name }}</h3>
            <span class="badge badge-sessions">{{ cls.session_count }} Buổi</span>
          </div>
          <div class="class-card-body">
            <p><strong>👨‍🏫 Giảng viên:</strong> {{ cls.teacher_name }}</p>
            <p v-if="cls.next_session"><strong style="color:var(--color-primary)">📅 Ca tiếp theo:</strong> {{ formatDate(cls.next_session) }}</p>
            <p v-else class="text-muted" style="font-size: 13px">⚠ Lớp chưa có lịch học nào sắp tới.</p>
          </div>
          <button class="btn btn-primary mt-3 btn-full" @click="goToDetail(cls.id)">
            ⚙️ Quản lý chi tiết ca học
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
.page { padding: 28px 32px; }
.page-header { display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px;gap:16px; }
.page-title { font-size:24px;font-weight:800;margin-bottom:4px; }
.loading-center { display:flex;justify-content:center;padding:40px; }
.spinner-blue { width:32px;height:32px;border:3px solid rgba(79,70,229,.2);border-top-color:#4f46e5;border-radius:50%;animation:spin .8s linear infinite; }
@keyframes spin { to{transform:rotate(360deg)} }
.empty-state { text-align:center;padding:48px;color:var(--color-text-muted); }

.classes-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px; }
.class-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); transition: all 0.2s; display: flex; flex-direction: column; }
.class-card:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); border-color: #cbd5e1; }
.class-card-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 16px; border-bottom: 1px dashed #e2e8f0; padding-bottom: 12px; }
.class-name { font-size: 18px; font-weight: 700; color: #1e293b; margin: 0; }
.badge-sessions { background: #dbeafe; color: #1d4ed8; padding: 4px 10px; border-radius: 999px; font-size: 12px; font-weight: 600; white-space: nowrap; }
.class-card-body { flex: 1; display: flex; flex-direction: column; gap: 8px; font-size: 14px; color: #475569; }
.class-card-body p { margin: 0; }
.btn-full { width: 100%; justify-content: center; font-weight: 600; }
</style>

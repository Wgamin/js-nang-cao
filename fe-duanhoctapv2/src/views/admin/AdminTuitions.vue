<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useApi } from '../../utils/api'
import AppLayout from '../../components/AppLayout.vue'

const { fetchApi } = useApi()
const tuitions = ref<any[]>([])
const stats = ref<any>({})
const classesList = ref<any[]>([])
const studentsList = ref<any[]>([])
const loading = ref(true)
const toast = ref<{ msg: string; type: string } | null>(null)

// Modal tạo hóa đơn
const showCreateModal = ref(false)
const saving = ref(false)
const createForm = ref({ user_id: '' as any, study_class_id: '' as any, amount: '', note: '' })

// Modal xác nhận đóng tiền
const showPayModal = ref(false)
const payingTuition = ref<any>(null)
const payForm = ref({ amount_paid: '', payment_date: '', note: '' })
const paying = ref(false)

// Modal generate for class
const showGenModal = ref(false)
const generating = ref(false)
const genForm = ref({ study_class_id: '' as any, amount: '', note: '' })

// Filter
const filterStatus = ref('')
const filterClass = ref('')

const showToast = (msg: string, type = 'success') => {
  toast.value = { msg, type }
  setTimeout(() => { toast.value = null }, 3500)
}

const fmtMoney = (n: any) => Number(n || 0).toLocaleString('vi-VN') + ' ₫'
const fmtDate = (d: string) => d ? new Date(d).toLocaleDateString('vi-VN') : '—'

const statusLabel: Record<string, string> = {
  unpaid: '❌ Chưa đóng',
  partially_paid: '⚠️ Đóng một phần',
  paid: '✅ Đã đóng đủ',
}
const statusClass: Record<string, string> = {
  unpaid: 'status-unpaid',
  partially_paid: 'status-partial',
  paid: 'status-paid',
}

const filteredTuitions = computed(() => {
  return tuitions.value.filter(t => {
    const matchStatus = !filterStatus.value || t.status === filterStatus.value
    const matchClass = !filterClass.value || String(t.study_class_id) === filterClass.value
    return matchStatus && matchClass
  })
})

const loadAll = async () => {
  loading.value = true
  try {
    const [t, s_stats, cl, st] = await Promise.all([
      fetchApi('/admin/tuitions'),
      fetchApi('/admin/tuitions/stats'),
      fetchApi('/admin/classes-list'),
      fetchApi('/admin/students-list'),
    ])
    tuitions.value = t.data
    stats.value = s_stats.data
    classesList.value = cl.data
    studentsList.value = st.data
  } catch (e: any) {
    showToast(e.message, 'error')
  } finally {
    loading.value = false
  }
}

const createTuition = async () => {
  saving.value = true
  try {
    await fetchApi('/admin/tuitions', {
      method: 'POST',
      body: JSON.stringify({
        user_id: createForm.value.user_id,
        study_class_id: createForm.value.study_class_id,
        amount: createForm.value.amount,
        note: createForm.value.note,
      }),
    })
    showToast('✅ Tạo hóa đơn thành công!')
    showCreateModal.value = false
    await loadAll()
  } catch (e: any) {
    showToast(e.message, 'error')
  } finally {
    saving.value = false
  }
}

const openPayModal = (t: any) => {
  payingTuition.value = t
  payForm.value = {
    amount_paid: String(Number(t.amount_paid) || 0),
    payment_date: new Date().toISOString().slice(0, 10),
    note: t.note || '',
  }
  showPayModal.value = true
}

const confirmPayment = async () => {
  paying.value = true
  try {
    await fetchApi(`/admin/tuitions/${payingTuition.value.id}`, {
      method: 'PUT',
      body: JSON.stringify({
        amount_paid: Number(payForm.value.amount_paid),
        payment_date: payForm.value.payment_date,
        note: payForm.value.note,
      }),
    })
    showToast('✅ Xác nhận đóng tiền thành công!')
    showPayModal.value = false
    await loadAll()
  } catch (e: any) {
    showToast(e.message, 'error')
  } finally {
    paying.value = false
  }
}

const generateForClass = async () => {
  generating.value = true
  try {
    const res = await fetchApi('/admin/tuitions/generate-for-class', {
      method: 'POST',
      body: JSON.stringify({
        study_class_id: genForm.value.study_class_id,
        amount: Number(genForm.value.amount),
        note: genForm.value.note,
      }),
    })
    showToast('✅ ' + res.data)
    showGenModal.value = false
    await loadAll()
  } catch (e: any) {
    showToast(e.message, 'error')
  } finally {
    generating.value = false
  }
}

const deleteTuition = async (t: any) => {
  if (!confirm(`Xóa hóa đơn của "${t.student?.name}"?`)) return
  try {
    await fetchApi(`/admin/tuitions/${t.id}`, { method: 'DELETE' })
    showToast('🗑️ Đã xóa hóa đơn!')
    await loadAll()
  } catch (e: any) {
    showToast(e.message, 'error')
  }
}

onMounted(loadAll)
</script>

<template>
  <AppLayout>
    <div class="page">
      <!-- Header -->
      <div class="page-header">
        <div>
          <h1 class="page-title">💰 Quản Lý Học Phí</h1>
          <p class="text-muted">Theo dõi và xác nhận thu tiền học phí từ học sinh.</p>
        </div>
        <div style="display:flex;gap:10px">
          <button id="gen-class-btn" class="btn btn-outline" @click="showGenModal = true">⚡ Tạo Hàng Loạt</button>
          <button id="create-tuition-btn" class="btn btn-primary" @click="showCreateModal = true">+ Tạo Hóa Đơn</button>
        </div>
      </div>

      <!-- Stats -->
      <div class="stats-row">
        <div class="mini-stat">
          <span class="mini-stat-label">Tổng hóa đơn</span>
          <span class="mini-stat-value">{{ stats.total ?? 0 }}</span>
        </div>
        <div class="mini-stat mini-stat--danger">
          <span class="mini-stat-label">❌ Chưa đóng</span>
          <span class="mini-stat-value">{{ stats.unpaid ?? 0 }}</span>
        </div>
        <div class="mini-stat mini-stat--warning">
          <span class="mini-stat-label">⚠️ Đóng một phần</span>
          <span class="mini-stat-value">{{ stats.partially_paid ?? 0 }}</span>
        </div>
        <div class="mini-stat mini-stat--success">
          <span class="mini-stat-label">✅ Đã đóng đủ</span>
          <span class="mini-stat-value">{{ stats.paid ?? 0 }}</span>
        </div>
        <div class="mini-stat mini-stat--info">
          <span class="mini-stat-label">Đã thu được</span>
          <span class="mini-stat-value" style="font-size:16px">{{ fmtMoney(stats.total_collected) }}</span>
        </div>
        <div class="mini-stat">
          <span class="mini-stat-label">Tổng phải thu</span>
          <span class="mini-stat-value" style="font-size:16px">{{ fmtMoney(stats.total_amount) }}</span>
        </div>
      </div>

      <!-- Filter -->
      <div class="filter-bar card">
        <label class="form-label" style="margin:0">Lọc:</label>
        <select class="form-input" style="width:180px" v-model="filterStatus">
          <option value="">Tất cả trạng thái</option>
          <option value="unpaid">Chưa đóng</option>
          <option value="partially_paid">Đóng một phần</option>
          <option value="paid">Đã đóng đủ</option>
        </select>
        <select class="form-input" style="width:200px" v-model="filterClass">
          <option value="">Tất cả lớp</option>
          <option v-for="c in classesList" :key="c.id" :value="String(c.id)">{{ c.name }}</option>
        </select>
        <span class="result-count">{{ filteredTuitions.length }} kết quả</span>
      </div>

      <!-- Table -->
      <div class="card mt-4">
        <div v-if="loading" class="loading-center"><div class="spinner-blue"></div></div>
        <div v-else-if="!filteredTuitions.length" class="empty-state">Không có hóa đơn nào.</div>
        <div v-else class="table-wrapper">
          <table class="table">
            <thead>
              <tr>
                <th>Học Sinh</th>
                <th>Lớp Học</th>
                <th>Số Tiền</th>
                <th>Đã Đóng</th>
                <th>Còn Lại</th>
                <th>Trạng Thái</th>
                <th>Ngày Thu</th>
                <th>Ghi Chú</th>
                <th>Hành Động</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="t in filteredTuitions" :key="t.id">
                <td>
                  <div class="fw-bold">{{ t.student?.name }}</div>
                  <div class="text-muted" style="font-size:11px">{{ t.student?.phone }}</div>
                </td>
                <td>
                  <div class="fw-bold">{{ t.study_class?.name }}</div>
                  <div class="text-muted" style="font-size:11px">{{ t.study_class?.subject?.name || '—' }}</div>
                </td>
                <td>{{ fmtMoney(t.amount) }}</td>
                <td :style="{ color: Number(t.amount_paid) > 0 ? 'var(--color-success)' : 'inherit' }">
                  {{ fmtMoney(t.amount_paid) }}
                </td>
                <td :style="{ color: Number(t.amount) - Number(t.amount_paid) > 0 ? 'var(--color-danger)' : 'var(--color-success)', fontWeight:600 }">
                  {{ fmtMoney(Number(t.amount) - Number(t.amount_paid)) }}
                </td>
                <td>
                  <span class="status-pill" :class="statusClass[t.status]">{{ statusLabel[t.status] }}</span>
                </td>
                <td>{{ fmtDate(t.payment_date) }}</td>
                <td class="text-muted cell-truncate">{{ t.note || '—' }}</td>
                <td>
                  <div class="action-btns">
                    <button v-if="t.status !== 'paid'" class="btn btn-success btn-sm" @click="openPayModal(t)">
                      💵 Thu Tiền
                    </button>
                    <button class="btn btn-danger btn-sm" @click="deleteTuition(t)">🗑️</button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Create Modal -->
    <Teleport to="body">
      <div v-if="showCreateModal" class="modal-backdrop" @click.self="showCreateModal = false">
        <div class="modal" id="create-tuition-modal">
          <div class="modal-header">
            <h3>➕ Tạo Hóa Đơn Học Phí</h3>
            <button class="modal-close" @click="showCreateModal = false">✕</button>
          </div>
          <div class="modal-body">
            <div class="form-grid">
              <div class="form-group">
                <label class="form-label">Học sinh *</label>
                <select class="form-input" v-model="createForm.user_id">
                  <option disabled value="">-- Chọn học sinh --</option>
                  <option v-for="s in studentsList" :key="s.id" :value="s.id">{{ s.name }}</option>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Lớp học *</label>
                <select class="form-input" v-model="createForm.study_class_id">
                  <option disabled value="">-- Chọn lớp --</option>
                  <option v-for="c in classesList" :key="c.id" :value="c.id">{{ c.name }}</option>
                </select>
              </div>
              <div class="form-group" style="grid-column:span 2">
                <label class="form-label">Số tiền học phí (VND) *</label>
                <input class="form-input" type="number" v-model="createForm.amount" placeholder="800000" />
              </div>
              <div class="form-group" style="grid-column:span 2">
                <label class="form-label">Ghi chú</label>
                <input class="form-input" v-model="createForm.note" placeholder="Học phí tháng 4..." />
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-outline" @click="showCreateModal = false">Hủy</button>
            <button class="btn btn-primary" :disabled="saving" @click="createTuition">
              <span v-if="saving" class="spinner"></span>{{ saving ? 'Đang lưu...' : '💾 Tạo Hóa Đơn' }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Pay Modal -->
    <Teleport to="body">
      <div v-if="showPayModal" class="modal-backdrop" @click.self="showPayModal = false">
        <div class="modal" id="pay-tuition-modal">
          <div class="modal-header">
            <div>
              <h3>💵 Xác Nhận Thu Tiền</h3>
              <p class="text-muted" style="font-size:13px">
                {{ payingTuition?.student?.name }} — {{ payingTuition?.study_class?.name }} (Môn: {{ payingTuition?.study_class?.subject?.name || '—' }})
                &nbsp;·&nbsp; Cần thu: <strong>{{ fmtMoney(payingTuition?.amount) }}</strong>
              </p>
            </div>
            <button class="modal-close" @click="showPayModal = false">✕</button>
          </div>
          <div class="modal-body">
            <div class="form-grid">
              <div class="form-group">
                <label class="form-label">Số tiền đã thu (VND) *</label>
                <input class="form-input" type="number" v-model="payForm.amount_paid" :max="payingTuition?.amount" />
              </div>
              <div class="form-group">
                <label class="form-label">Ngày thu tiền *</label>
                <input class="form-input" type="date" v-model="payForm.payment_date" />
              </div>
              <div class="form-group" style="grid-column:span 2">
                <label class="form-label">Ghi chú</label>
                <input class="form-input" v-model="payForm.note" placeholder="Đóng tiền mặt, Chuyển khoản MB Bank..." />
              </div>
            </div>
            <!-- Progress bar -->
            <div class="pay-progress" v-if="payingTuition">
              <div class="pay-progress-bar">
                <div class="pay-progress-fill"
                  :style="{ width: Math.min(100, (Number(payForm.amount_paid) / Number(payingTuition.amount)) * 100) + '%' }">
                </div>
              </div>
              <span class="pay-progress-text">
                {{ Math.min(100, Math.round((Number(payForm.amount_paid) / Number(payingTuition.amount)) * 100)) }}% đã đóng
              </span>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-outline" @click="showPayModal = false">Hủy</button>
            <button id="confirm-pay-btn" class="btn btn-success" :disabled="paying" @click="confirmPayment">
              <span v-if="paying" class="spinner"></span>{{ paying ? 'Đang lưu...' : '✅ Xác Nhận Thu Tiền' }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Generate Modal -->
    <Teleport to="body">
      <div v-if="showGenModal" class="modal-backdrop" @click.self="showGenModal = false">
        <div class="modal" id="gen-tuition-modal">
          <div class="modal-header">
            <h3>⚡ Tạo Học Phí Hàng Loạt</h3>
            <button class="modal-close" @click="showGenModal = false">✕</button>
          </div>
          <div class="modal-body">
            <p class="text-muted mb-4" style="font-size:14px">Hệ thống sẽ tự động tạo hóa đơn cho tất cả học sinh trong lớp (bỏ qua những học sinh đã có hóa đơn).</p>
            <div class="form-grid">
              <div class="form-group" style="grid-column:span 2">
                <label class="form-label">Lớp học *</label>
                <select class="form-input" v-model="genForm.study_class_id">
                  <option disabled value="">-- Chọn lớp --</option>
                  <option v-for="c in classesList" :key="c.id" :value="c.id">{{ c.name }}</option>
                </select>
              </div>
              <div class="form-group" style="grid-column:span 2">
                <label class="form-label">Số tiền học phí (VND) *</label>
                <input class="form-input" type="number" v-model="genForm.amount" placeholder="800000" />
              </div>
              <div class="form-group" style="grid-column:span 2">
                <label class="form-label">Ghi chú</label>
                <input class="form-input" v-model="genForm.note" placeholder="Học phí tháng 5/2026" />
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-outline" @click="showGenModal = false">Hủy</button>
            <button class="btn btn-primary" :disabled="generating" @click="generateForClass">
              <span v-if="generating" class="spinner"></span>{{ generating ? 'Đang tạo...' : '⚡ Tạo Hàng Loạt' }}
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
.page { padding: 28px 32px; }
.page-header { display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px;gap:16px; }
.page-title { font-size:24px;font-weight:800;margin-bottom:4px; }

.stats-row { display:grid;grid-template-columns:repeat(6,1fr);gap:12px;margin-bottom:20px; }
.mini-stat { background:#fff;border:1px solid var(--color-border);border-radius:12px;padding:16px;display:flex;flex-direction:column;gap:6px; }
.mini-stat--danger { border-left:3px solid var(--color-danger); }
.mini-stat--warning { border-left:3px solid #f59e0b; }
.mini-stat--success { border-left:3px solid var(--color-success); }
.mini-stat--info { border-left:3px solid var(--color-primary); }
.mini-stat-label { font-size:11px;font-weight:600;color:var(--color-text-muted);text-transform:uppercase;letter-spacing:.04em; }
.mini-stat-value { font-size:22px;font-weight:800;line-height:1; }

.filter-bar { display:flex;align-items:center;gap:12px;padding:14px 20px; }
.result-count { margin-left:auto;color:var(--color-text-muted);font-size:13px; }
.loading-center { display:flex;justify-content:center;padding:40px; }
.spinner-blue { width:32px;height:32px;border:3px solid rgba(79,70,229,.2);border-top-color:#4f46e5;border-radius:50%;animation:spin .8s linear infinite; }
@keyframes spin { to{transform:rotate(360deg)} }
.empty-state { text-align:center;padding:48px;color:var(--color-text-muted); }
.cell-truncate { max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
.action-btns { display:flex;gap:6px; }

.status-pill { display:inline-block;padding:4px 10px;border-radius:999px;font-size:12px;font-weight:600; }
.status-unpaid { background:#fee2e2;color:#dc2626; }
.status-partial { background:#fef3c7;color:#d97706; }
.status-paid { background:#dcfce7;color:#15803d; }

.pay-progress { margin-top:20px; }
.pay-progress-bar { height:10px;border-radius:999px;background:#e2e8f0;overflow:hidden; }
.pay-progress-fill { height:100%;background:linear-gradient(90deg,#4f46e5,#06b6d4);border-radius:999px;transition:width .3s; }
.pay-progress-text { font-size:13px;font-weight:600;color:var(--color-primary);margin-top:6px;display:block;text-align:right; }

.form-grid { display:grid;grid-template-columns:1fr 1fr;gap:16px; }
.mb-4 { margin-bottom:16px; }
.modal-backdrop { position:fixed;inset:0;background:rgba(15,23,42,.6);backdrop-filter:blur(4px);display:flex;align-items:center;justify-content:center;z-index:1000;padding:20px; }
.modal { background:#fff;border-radius:20px;width:100%;max-width:560px;max-height:90vh;display:flex;flex-direction:column;box-shadow:0 24px 60px rgba(0,0,0,.2);animation:modalIn .25s ease; }
@keyframes modalIn { from{opacity:0;transform:scale(.95)} to{opacity:1;transform:scale(1)} }
.modal-header { display:flex;align-items:flex-start;justify-content:space-between;padding:20px 24px;border-bottom:1px solid var(--color-border); }
.modal-header h3 { font-size:17px;font-weight:700; }
.modal-close { background:none;border:none;cursor:pointer;color:var(--color-text-muted);font-size:18px;padding:4px 8px;border-radius:6px; }
.modal-body { padding:24px;overflow-y:auto;flex:1; }
.modal-footer { display:flex;gap:12px;justify-content:flex-end;padding:16px 24px;border-top:1px solid var(--color-border); }

@media (max-width:1200px) { .stats-row { grid-template-columns:repeat(3,1fr); } }
@media (max-width:768px) { .page { padding:16px; } .stats-row { grid-template-columns:repeat(2,1fr); } }
</style>

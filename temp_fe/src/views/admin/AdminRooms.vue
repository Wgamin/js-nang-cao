<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useApi } from '../../utils/api'
import AppLayout from '../../components/AppLayout.vue'

const { fetchApi } = useApi()
const data = ref<any[]>([])
const loading = ref(true)
const toast = ref<{ msg: string; type: string } | null>(null)

const showModal = ref(false)
const isEditing = ref(false)
const saving = ref(false)
const form = ref({ id: null, name: '', capacity: 30, location: '' })

const showToast = (msg: string, type = 'success') => {
  toast.value = { msg, type }
  setTimeout(() => { toast.value = null }, 3500)
}

const loadData = async () => {
  loading.value = true
  try {
    const res = await fetchApi('/admin/rooms')
    data.value = res.data
  } catch (e: any) { showToast(e.message, 'error') }
  finally { loading.value = false }
}

const openCreate = () => {
  isEditing.value = false
  form.value = { id: null, name: '', capacity: 30, location: '' }
  showModal.value = true
}

const openEdit = (item: any) => {
  isEditing.value = true
  form.value = { ...item }
  showModal.value = true
}

const save = async () => {
  saving.value = true
  try {
    if (isEditing.value) {
      await fetchApi(`/admin/rooms/${form.value.id}`, { method: 'PUT', body: JSON.stringify(form.value) })
      showToast('Cập nhật thành công!')
    } else {
      await fetchApi('/admin/rooms', { method: 'POST', body: JSON.stringify(form.value) })
      showToast('Tạo thành công!')
    }
    showModal.value = false
    loadData()
  } catch (e: any) { showToast(e.message, 'error') }
  finally { saving.value = false }
}

const destroy = async (item: any) => {
  if (!confirm('Xoá?')) return
  try {
    await fetchApi(`/admin/rooms/${item.id}`, { method: 'DELETE' })
    showToast('Đã xoá!')
    loadData()
  } catch (e: any) { showToast(e.message, 'error') }
}

onMounted(loadData)
</script>

<template>
  <AppLayout>
    <div class="page">
      <div class="page-header">
        <h1>🏫 Quản Lý Phòng Học</h1>
        <button class="btn btn-primary" @click="openCreate">+ Thêm Phòng</button>
      </div>

      <div class="card">
        <table class="table">
          <thead><tr><th>Tên Phòng</th><th>Sức chứa</th><th>Vị trí</th><th>Thao tác</th></tr></thead>
          <tbody>
            <tr v-for="item in data" :key="item.id">
              <td class="fw-bold">{{ item.name }}</td><td>{{ item.capacity }} người</td><td>{{ item.location || '—' }}</td>
              <td>
                <button class="btn btn-outline" @click="openEdit(item)">✏️</button>
                <button class="btn btn-danger" style="margin-left:8px" @click="destroy(item)">🗑️</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    
    <div v-if="showModal" class="modal-backdrop" @click.self="showModal=false">
      <div class="modal">
        <div class="modal-header"><h3>{{ isEditing ? 'Sửa' : 'Thêm' }}</h3></div>
        <div class="modal-body">
          <label>Tên</label><input class="form-input" v-model="form.name" /><br><br>
          <label>Sức chứa</label><input class="form-input" type="number" v-model="form.capacity" /><br><br>
          <label>Vị trí</label><input class="form-input" v-model="form.location" />
        </div>
        <div class="modal-footer">
          <button class="btn btn-primary" @click="save">Lưu</button>
        </div>
      </div>
    </div>
    <div v-if="toast" class="toast" :class="'toast-'+toast.type">{{ toast.msg }}</div>
  </AppLayout>
</template>

<style scoped>
.page { padding: 28px; }
.page-header { display: flex; justify-content: space-between; margin-bottom: 24px; }
.modal-backdrop { position:fixed;inset:0;background:rgba(0,0,0,0.5);display:flex;align-items:center;justify-content:center;z-index:999; }
.modal { background:#fff;padding:24px;border-radius:12px;width:400px; }
</style>

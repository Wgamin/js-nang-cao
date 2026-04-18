<template>
  <div class="space-y-8">
    <section class="flex flex-wrap items-center justify-between gap-4 bg-surface-container-lowest p-5 rounded-xl border border-outline-variant/10 shadow-sm">
      <div class="flex items-center gap-4">
        <h2 class="text-xl font-bold text-on-surface">Quản lý {{ title }}</h2>
        <div class="h-6 w-[1px] bg-outline-variant/30"></div>
        <div class="flex items-center gap-2 px-3 py-2 bg-surface-container rounded-lg">
          <span class="material-symbols-outlined text-sm text-on-surface-variant">search</span>
          <input class="bg-transparent border-none text-sm font-medium focus:ring-0 placeholder-on-surface-variant/50" :placeholder="`Tìm kiếm ${title}...`" type="text"/>
        </div>
      </div>
      <button class="bg-primary text-white px-5 py-2 rounded-lg text-sm font-semibold hover:bg-primary-container transition-all active:scale-95 shadow-sm">
        Thêm {{ title }} Mới
      </button>
    </section>

    <section class="bg-surface-container-lowest rounded-xl overflow-hidden shadow-sm border border-outline-variant/10">
      <table class="w-full text-left border-collapse">
        <thead class="bg-surface-container-low">
          <tr>
            <th v-for="col in columns" :key="col" class="px-6 py-4 text-[11px] uppercase tracking-widest font-bold text-on-surface-variant">{{ col }}</th>
            <th class="px-6 py-4 text-[11px] uppercase tracking-widest font-bold text-on-surface-variant text-right">Thao tác</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-outline-variant/10">
          <tr v-for="item in data" :key="item.id" class="hover:bg-surface-container-high/50 transition-colors group">
            <td v-for="(value, key) in item" v-show="key !== 'id' && key !== 'statusClass' && key !== 'avatar' && key !== 'statusDot'" :key="key" class="px-6 py-5 text-sm">
              <div v-if="key === 'name'" class="flex items-center gap-3">
                <div v-if="item.avatar" class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs">{{ item.name.charAt(0) }}</div>
                <span class="font-bold text-on-surface">{{ value }}</span>
              </div>
              <span v-else-if="key === 'status'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold" :class="item.statusClass">
                <span v-if="item.statusDot" class="w-1.5 h-1.5 rounded-full mr-1.5" :class="item.statusDot"></span> {{ value }}
              </span>
              <span v-else>{{ value }}</span>
            </td>
            <td class="px-6 py-5 text-right">
              <div class="flex items-center justify-end gap-1">
                <button class="p-2 text-on-surface-variant hover:text-primary transition-colors">
                  <span class="material-symbols-outlined text-lg">edit</span>
                </button>
                <button class="p-2 text-on-surface-variant hover:text-error transition-colors">
                  <span class="material-symbols-outlined text-lg">delete</span>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </section>
  </div>
</template>

<script setup>
defineProps({
  title: String,
  columns: Array,
  data: Array
});
</script>

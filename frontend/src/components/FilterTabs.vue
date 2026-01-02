<template>
  <div class="filter-tabs">
    <button
      v-for="filter in filters"
      :key="filter.id"
      class="filter-tab"
      :class="{ active: modelValue === filter.id }"
      @click="$emit('update:modelValue', filter.id)"
    >
      {{ filter.label }}
    </button>
  </div>
</template>

<script setup lang="ts">
interface Filter {
  id: string
  label: string
}

interface Props {
  filters: Filter[]
  modelValue: string
}

defineProps<Props>()

defineEmits<{
  'update:modelValue': [value: string]
}>()
</script>

<style scoped>
.filter-tabs {
  display: flex;
  gap: 0.75rem;
  padding: 0 2rem;
  margin-bottom: 1.5rem;
  overflow-x: auto;
  scrollbar-width: none;
  -ms-overflow-style: none;
}

.filter-tabs::-webkit-scrollbar {
  display: none;
}

.filter-tab {
  flex-shrink: 0;
  background: rgba(255, 255, 255, 0.05);
  color: rgba(255, 255, 255, 0.7);
  border: 1px solid rgba(255, 255, 255, 0.1);
  padding: 0.5rem 1.25rem;
  border-radius: 20px;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  white-space: nowrap;
}

.filter-tab:hover {
  background: rgba(255, 255, 255, 0.1);
  color: rgba(255, 255, 255, 0.9);
  border-color: rgba(255, 255, 255, 0.2);
}

.filter-tab.active {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: #fff;
  border-color: transparent;
}

@media (max-width: 768px) {
  .filter-tabs {
    padding: 0 1rem;
    gap: 0.5rem;
  }

  .filter-tab {
    padding: 0.4rem 1rem;
    font-size: 0.8rem;
  }
}
</style>

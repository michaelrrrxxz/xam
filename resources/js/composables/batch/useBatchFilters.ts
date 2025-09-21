// resources/js/composables/batch/useBatchFilters.ts
import { ref, computed, Ref } from 'vue'
import type { Batch } from './useBatch'

export function useBatchFilters(batch: Ref<Batch[]>) {
  const searchQuery = ref('')
  const sortKey = ref<'year' | 'name' | 'access_key'>('year')
  const sortOrder = ref<'asc' | 'desc'>('asc')
  const yearFilter = ref<string>('all')

  const availableYears = computed(() => {
    const years = batch.value.map((b) => new Date(b.created_at).getFullYear())
    return Array.from(new Set(years)).sort((a, b) => b - a)
  })

  const filteredBatch = computed<Batch[]>(() => {
    let data = batch.value

    if (searchQuery.value) {
      data = data.filter((e) =>
        e.name.toLowerCase().includes(searchQuery.value.toLowerCase())
      )
    }

    if (yearFilter.value !== 'all') {
      data = data.filter(
        (e) =>
          new Date(e.created_at).getFullYear().toString() === yearFilter.value
      )
    }

    return [...data].sort((a, b) => {
      const valA =
        sortKey.value === 'year'
          ? new Date(a.created_at).getFullYear()
          : a[sortKey.value]
      const valB =
        sortKey.value === 'year'
          ? new Date(b.created_at).getFullYear()
          : b[sortKey.value]

      if (valA < valB) return sortOrder.value === 'asc' ? -1 : 1
      if (valA > valB) return sortOrder.value === 'asc' ? 1 : -1
      return 0
    })
  })

  const groupedByYear = (batch: Batch[]) => {
    const groups: Record<number, Batch[]> = {}
    batch.forEach((b) => {
      const year = new Date(b.created_at).getFullYear()
      if (!groups[year]) groups[year] = []
      groups[year].push(b)
    })
    return groups
  }

  const toggleSort = (key: typeof sortKey.value) => {
    if (sortKey.value === key) {
      sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc'
    } else {
      sortKey.value = key
      sortOrder.value = 'asc'
    }
  }

  return {
    searchQuery,
    sortKey,
    sortOrder,
    yearFilter,
    availableYears,
    filteredBatch,
    groupedByYear,
    toggleSort,
  }
}

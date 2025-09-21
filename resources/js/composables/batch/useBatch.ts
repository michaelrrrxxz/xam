import { ref, reactive, onMounted } from 'vue'
import api from '@/Api/Axios'
import { toast } from 'vue-sonner'

export interface Batch {
  id: number
  name: string
  description: string | null
  access_key: number
  isLocked: boolean
  created_at: Date
}

export function useBatch() {
  const batch = ref<Batch[]>([])
  const isLoading = ref(true)
  const isSaving = ref(false)
  const errors = ref<{ name?: string }>({})
  const isOpen = ref(false)
  const isEditOpen = ref(false)

  const form = reactive<{ id: number | null; name: string; description: string }>({
    id: null,
    name: '',
    description: '',
  })

  const fetchBatch = async () => {
    isLoading.value = true
    try {
      const res = await api.get('admin/batch')
      batch.value = res.data
    } catch (err) {
      console.warn(err)
    } finally {
      isLoading.value = false
    }
  }

  const resetForm = () => {
    form.id = null
    form.name = ''
    form.description = ''
  }

  const openModal = () => {
    resetForm()
    isOpen.value = true
  }

  const openEditModal = async (id: number) => {
    const res = await api.get(`admin/batch/${id}`)
    const b = res.data
    if (b) {
      form.id = b.id
      form.name = b.name
      form.description = b.description || ''
      isEditOpen.value = true
    }
  }

  const saveBatch = async () => {
    errors.value = {}
    isSaving.value = true
    try {
      if (form.id) {
        await api.put(`admin/batch/${form.id}`, {
          name: form.name,
          description: form.description,
        })
        toast.success('Batch updated successfully!')
      } else {
        await api.post('admin/batch', {
          name: form.name,
          description: form.description,
        })
        toast.success('Batch added successfully!')
      }
      await fetchBatch()
      resetForm()
      isOpen.value = false
      isEditOpen.value = false
    } catch (err: any) {
      if (err.response?.status === 422) {
        const backendErrors = err.response.data.errors
        if (backendErrors) {
          errors.value = Object.keys(backendErrors).reduce((acc, key) => {
            acc[key] = backendErrors[key][0]
            return acc
          }, {} as { [key: string]: string })
        } else {
          toast.error(err.response.data?.message || 'Validation failed.')
        }
      } else {
        toast.error('Something went wrong.')
      }
    } finally {
      isSaving.value = false
    }
  }

  const deleteBatch = async (id: number) => {
    try {
      await api.delete(`admin/batch/${id}`)
      toast.success('Batch deleted successfully!')
      await fetchBatch()
    } catch {
      toast.error('Failed to delete batch.')
    }
  }

  const lockBatch = async (id: number) => {
    try {
      await api.put(`admin/batch/${id}/lock`)
      toast.success('Batch locked successfully!')
      await fetchBatch()
    } catch {
      toast.error('Failed to lock batch.')
    }
  }

  const activateBatch = async (id: number) => {
    try {
      await api.put(`admin/batch/${id}/activate`)
      toast.success('Batch activated successfully!')
      await fetchBatch()
    } catch (err: any) {
      if (err.response?.status === 422) {
        toast.error(err.response.data?.message || 'An active batch already exists.')
      } else {
        toast.error('Failed to activate batch.')
      }
    }
  }

  onMounted(fetchBatch)

  return {
    batch,
    isLoading,
    isSaving,
    errors,
    form,
    isOpen,
    isEditOpen,
    fetchBatch,
    openModal,
    openEditModal,
    saveBatch,
    deleteBatch,
    lockBatch,
    activateBatch,
    resetForm,
  }
}

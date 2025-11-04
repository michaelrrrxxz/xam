import { reactive, ref } from 'vue';
import api from '@/Api/Axios';
import { toast } from 'vue-sonner';
import type { User } from '@/types';
import { useUsers } from './useUsers';
import { useTempPasswordStore } from '@/stores/tempPassword'
  import { useRouter } from 'vue-router'



export function useUserForm() {


const router = useRouter()
  const { fetchUsers } = useUsers();

  const form = reactive<{
    id: number | null;
    name: string;
    email: string;
    processing: boolean;
  }>({
    id: null,
    name: '',
    email: '',
    processing: false,
  });

  const errors = ref<{ [key: string]: string }>({});
  const isSaving = ref(false);
  const isOpen = ref(false);
  const isEditOpen = ref(false);
  const isResetPasswordOpen = ref(false);

  const resetForm = () => {
    form.id = null;
    form.name = '';
    form.email = '';
    errors.value = {};
  };

  const openModal = () => {
    resetForm();
    isOpen.value = true;
  };

  const openEditModal = async (id: number) => {
    const { data } = await api.get(`admin/user/${id}`);
    if (data) {
      form.id = data.id;
      form.name = data.name;
      form.email = data.email || '';
      isEditOpen.value = true;
    }
  };






  const openResetPasswordModal = async (id: number) => {
    isResetPasswordOpen.value = true;

    try {
      const { data } = await api.get(`admin/user/${id}`);
      if (data) {
        form.id = data.id;
        form.name = data.name;
        form.email = data.email || '';
        isResetPasswordOpen.value = true;
      }
    } catch (e) {
      console.error('Failed to fetch user:', e);
    }
  };

  const saveUser = async () => {
    errors.value = {};
    isSaving.value = true;
    try {
      if (form.id) {
        await api.put(`admin/user/${form.id}`, {
          name: form.name,
          email: form.email,
        });
        toast.success('User updated successfully!');
      } else {
        await api.post('admin/user', {
          name: form.name,
          email: form.email,
        });
        toast.success('User added successfully!');
      }
      fetchUsers();
      resetForm();
      isOpen.value = false;
      isEditOpen.value = false;
    } catch (error: any) {
      if (error.response?.data?.errors) {
        const backendErrors = error.response.data.errors;
        errors.value = Object.keys(backendErrors).reduce(
          (acc, key) => {
            acc[key] = backendErrors[key][0];
            return acc;
          },
          {} as { [key: string]: string }
        );
      } else {
        toast.error('Something went wrong.');
      }
    } finally {
      isSaving.value = false;
    }
  };





const resetPassword = async () => {
  if (!form.id) return

  try {
    form.processing = true
    const { data } = await api.post(`admin/users/${form.id}/reset-password`)


    // save password in store
    const tempStore = useTempPasswordStore()
    tempStore.setTempPassword(data.user, data.temporary_password)

    // go to the temp password page
    router.push({ name: 'TempPassword', params: { id: data.user.id } })

    isResetPasswordOpen.value = false
    toast.success('Password reset successfully!')
  } catch (err: any) {
    toast.error(err.response?.data?.message || 'Failed to reset password.')
  } finally {
    form.processing = false
  }
}



  return {
    form,
    errors,
    isSaving,
    isOpen,
    isEditOpen,
    isResetPasswordOpen,
    openModal,
    openEditModal,
    openResetPasswordModal,
    saveUser,
    resetForm,
    resetPassword,
  };
}

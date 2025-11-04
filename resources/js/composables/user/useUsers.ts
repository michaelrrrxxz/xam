import { ref, reactive, computed, onMounted } from 'vue';
import api from '@/Api/Axios';
import { toast } from 'vue-sonner';
import type { User } from '@/types';

export function useUsers() {
  const users = ref<User[]>([]);
  const isLoading = ref(true);
  const searchQuery = ref('');

  // fetch all users
  const fetchUsers = async () => {
    isLoading.value = true;
    try {
      const { data } = await api.get('admin/user');
      users.value = data;
    } catch (error) {
      console.warn(error);
    } finally {
      isLoading.value = false;
    }
  };

  const deleteUser = async (id: number) => {
    toast('Are you sure?', {
      position: 'top-center',
      description: 'This will permanently delete the user.',
      action: {
        label: 'Confirm',
        onClick: async () => {
          try {
            await api.delete(`/user/${id}`);
            toast.success('User deleted successfully!');
            await fetchUsers();
          } catch {
            toast.error('Failed to delete user.');
          }
        },
      },
    });
  };

  const filteredUsers = computed(() => {
    if (!searchQuery.value) return users.value;
    return users.value.filter((u) =>
      u.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
  });

  onMounted(fetchUsers);

  return {
    users,
    isLoading,
    searchQuery,
    filteredUsers,
    fetchUsers,
    deleteUser,
  };
}

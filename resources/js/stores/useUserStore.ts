import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '@/Api/Axios';

export const useUserStore = defineStore('user', () => {
  const name = ref('');
  const email = ref('');
  const avatar = ref('/avatars/default.jpg');
  const loading = ref(false);

  async function fetchUser(force = false) {
    if (name.value && email.value && !force) {
      // Already loaded, skip
      return;
    }

    const token = localStorage.getItem('token');
    if (!token) return;

    loading.value = true;
    try {
      const response = await api.get('/user', {
        headers: { Authorization: `Bearer ${token}` },
      });

      name.value = response.data?.name ?? '';
      email.value = response.data?.email ?? '';
      avatar.value = '/avatars/default.jpg';
    } catch (error) {
      console.error('Failed to fetch user:', error);
    } finally {
      loading.value = false;
    }
  }

  async function logout() {
    const token = localStorage.getItem('token');
    if (!token) return;

    try {
      await api.post(
        '/logout',
        {},
        {
          headers: { Authorization: `Bearer ${token}` },
        }
      );

      localStorage.removeItem('token');
      name.value = '';
      email.value = '';
      avatar.value = '/avatars/default.jpg';
    } catch (error) {
      console.error('Logout failed:', error);
    }
  }

  return { name, email, avatar, loading, fetchUser, logout };
});

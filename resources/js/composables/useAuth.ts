import { ref } from 'vue';
import api from '@/Api/Axios';
export const user = ref<{ name: string; email: string; avatar?: string } | null>(null);

export const fetchUser = async () => {
  try {
    const token = localStorage.getItem('token');
    if (!token) return null;

    const response = await api.get('/user', {
      headers: { Authorization: `Bearer ${token}` },
    });

    user.value = {
      name: response.data.name,
      email: response.data.email,
      avatar: '/avatars/default.jpg', // or use response.data.avatar if exists
    };

    return user.value;
  } catch (error) {
    console.error('Failed to fetch user:', error);
    return null;
  }
};

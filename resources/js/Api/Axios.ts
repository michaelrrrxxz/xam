import axios from 'axios';
import { toast } from 'vue-sonner';
import { useRouter } from 'vue-router';
const router = useRouter();
const api = axios.create({
  baseURL: 'http://127.0.0.1:8000/api/v1',
  timeout: 10000,
});


api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response) {
      const status = error.response.status;

      if (status === 401) {
        toast.error('Session expired. Please log in again.');
        localStorage.removeItem('token');
        router.push('/login');
      }

      if (status === 403) {
        toast.error('You do not have permission for this action.');
      }

      if (status === 422) {
        const messages = Object.values(error.response.data.errors || {})
          .flat()
          .join('\n');
        toast.error(messages || 'Validation failed. Check your input.');
      }
    } else if (error.request) {
      toast.error('No response from server. Check your connection.');
    } else {
      toast.error('Unexpected error occurred.');
    }

    return Promise.reject(error);
  }
);

export default api;

import { defineStore } from 'pinia';
import api from '@/Api/Axios';

interface Settings {
  id: number;
  school_name: string;
  school_logo: string | null;
  setup_complete: boolean;
}

export const useSettings = defineStore('settings', {
  state: () => ({
    settings: null as Settings | null,
  }),
  actions: {
    async fetchSettings() {
      try {
        const res = await api.get('/settings');
        this.settings = res.data?.settings || null;
      } catch (error) {
        console.error('Failed to load settings:', error);
      }
    },
    async saveSettings(formData: FormData) {
      try {
        const res = await api.post('/admin/settings', formData, {
          headers: { 'Content-Type': 'multipart/form-data' },
        });
        this.settings = res.data.settings;
      } catch (error) {
        console.error('Failed to save settings:', error);
      }
    },
  },
});

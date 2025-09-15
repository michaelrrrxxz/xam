import { defineStore } from 'pinia';
import api from '@/Api/Axios';

interface StudentData {
  [key: string]: any;
}

interface Settings {
  setup_complete: boolean;
}

export const useAuthStore = defineStore('auth', {
  state: () => ({
    studentData: null as StudentData | null,
    settings: null as Settings | null,
  }),

  actions: {
    loadStudentData() {
      try {
        this.studentData = JSON.parse(localStorage.getItem('studentData') || 'null');
      } catch {
        this.studentData = null;
      }
    },

    hasStudentData(): boolean {
      return this.studentData !== null && Object.keys(this.studentData).length > 0;
    },

    async fetchSettings(token: string) {
      try {
        const res = await api.get('/settings', {
          headers: { Authorization: `Bearer ${token}` },
        });
        this.settings = res.data?.settings ?? null;
        return this.settings;
      } catch (e) {
        console.error('Failed to fetch settings:', e);
        this.settings = null;
        return null;
      }
    },
  },
});

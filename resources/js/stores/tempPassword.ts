import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useTempPasswordStore = defineStore('tempPassword', () => {
  const user = ref<any | null>(null)
  const password = ref<string | null>(null)

  function setTempPassword(u: any, pw: string) {
    user.value = u
    password.value = pw
  }

  function clearTempPassword() {
    user.value = null
    password.value = null
  }

  return { user, password, setTempPassword, clearTempPassword }
})

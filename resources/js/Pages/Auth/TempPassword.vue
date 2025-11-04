<template>
<AppLayout>
  <div class="max-w-xl mx-auto p-6">
    <h1 class="text-xl font-bold mb-2">Temporary Password</h1>

    <div v-if="user && password">
      <p class="mb-4">
        Password for <strong>{{ user.name }}</strong> has been reset.
        Share the password below:
      </p>

      <div class="bg-gray-100 p-4 rounded text-lg font-mono break-all">
        <span id="tempPassword">{{ password }}</span>
      </div>

      <div class="mt-4 flex gap-2">
        <Button @click="copyPassword">Copy</Button>
        <RouterLink to="/users">
          <Button variant="secondary" @click="clearTempPassword">Back to Users</Button>
        </RouterLink>
      </div>

      <p class="mt-3 text-sm text-red-600">
        <AlertTriangle/> This password will only be shown once. If you leave or refresh, it will disappear.
      </p>
    </div>

    <div v-else>
      <p class="text-red-600">Temporary password has expired.</p>
      <RouterLink to="/settings">
        <Button class="mt-4">Back to Users</Button>
      </RouterLink>
    </div>
  </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { AlertTriangle, LucideArrowDownRightFromCircle } from 'lucide-vue-next'
import AppLayout from '@/layouts/AppLayout.vue'
import { useTempPasswordStore } from '@/stores/tempPassword'
import {Button} from '@/components/ui/button'
import { toast } from 'vue-sonner'

const tempStore = useTempPasswordStore()
const user = tempStore.user
const password = tempStore.password

function copyPassword() {
  if (password) {
    navigator.clipboard.writeText(password)
    toast.success('Copied to clipboard')
  }
}

function clearTempPassword() {
  tempStore.clearTempPassword()
}
</script>

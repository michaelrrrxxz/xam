<script setup lang="ts">
import { useColorMode } from '@vueuse/core';
import Logo from '@/components/Logo.vue';
const mode = useColorMode();
</script>

<template>
  <div class="min-h-screen flex flex-col bg-background">
    <!-- Centered auth container -->
    <main class="flex flex-1 flex-col items-center justify-center px-4 py-8 gap-6">
      <!-- App logo OUTSIDE card -->
      <Logo />

      <!-- Auth card -->
      <div class="w-full max-w-md space-y-6 p-6 rounded-xl shadow-md border bg-card">
        <div class="text-center space-y-2">
          <h1 class="text-2xl font-bold tracking-tight">Welcome</h1>
          <p class="text-muted-foreground text-sm">Please fill out the credentials to continue</p>
        </div>

        <!-- Slot for page content -->
        <slot />
      </div>
    </main>

    <!-- Footer -->
    <footer class="py-4 text-center text-xs text-muted-foreground">
      &copy; {{ new Date().getFullYear() }} YourApp. All rights reserved.
    </footer>
  </div>
</template>

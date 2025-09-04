<script setup lang="ts">
import { useColorMode } from '@vueuse/core';

const mode = useColorMode();
</script>

<template>
  <div class="min-h-screen flex flex-col bg-background">
    <!-- Centered auth container -->
    <main class="flex flex-1 items-center justify-center px-4 py-8">
      <div class="w-full max-w-md space-y-6 p-6 rounded-xl shadow-md border bg-card">
        <!-- App logo / heading -->
        <!-- <div class="text-center space-y-2">
          <img
            v-if="mode === 'light'"
            src="/logo-light.svg"
            alt="App Logo"
            class="w-14 h-14 mx-auto"
          />
          <img
            v-else
            src="/logo-dark.svg"
            alt="App Logo"
            class="w-14 h-14 mx-auto"
          />
          <h1 class="text-2xl font-bold tracking-tight">Welcome</h1>
          <p class="text-muted-foreground text-sm">
            Please sign in to continue
          </p>
        </div> -->

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

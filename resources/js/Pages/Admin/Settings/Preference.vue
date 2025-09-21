<template>
  <TabsContent value="preference">
    <Card class="p-6 space-y-4">
      <h2 class="text-lg font-semibold">Preferences</h2>

      <div class="flex items-center justify-between">
        <span class="font-medium">Theme</span>
        <div class="flex items-center gap-2">
          <Button
            size="sm"
            variant="outline"
            :class="{ 'bg-gray-200 dark:bg-gray-700': mode === 'light' }"
            @click="mode = 'light'"
          >
            Light
          </Button>
          <Button
            size="sm"
            variant="outline"
            :class="{ 'bg-gray-200 dark:bg-gray-700': mode === 'dark' }"
            @click="mode = 'dark'"
          >
            Dark
          </Button>
          <Button
            size="sm"
            variant="outline"
            :class="{ 'bg-gray-200 dark:bg-gray-700': mode === 'auto' }"
            @click="mode = 'auto'"
          >
            System
          </Button>
        </div>
      </div>
    </Card>
  </TabsContent>
</template>

<script lang="ts" setup>
import { TabsContent } from '@/components/ui/tabs';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';

import { useColorMode } from '@vueuse/core';

const mode = useColorMode();
</script>

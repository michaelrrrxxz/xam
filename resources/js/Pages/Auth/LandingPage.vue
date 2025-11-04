<script setup lang="ts">
import { useColorMode } from '@vueuse/core';
import { Button } from '@/components/ui/button';
import { ArrowRight, LogIn } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import { useRouter } from 'vue-router';

import Logo from '@/components/Logo.vue';
const mode = useColorMode();
const router = useRouter();

const goToExam = () => {
  router.push({ name: 'exam-verify' });
};
const goToLogin = () => {
  router.push({ name: 'Login' });
};
localStorage.removeItem('studentData');
</script>

<template>
  <div class="min-h-screen flex flex-col bg-background">
    <!-- Hero Section -->
    <section class="container mx-auto py-20 md:py-32 px-6">
      <Logo :logo-size="32" />
      <div class="flex flex-col items-center text-center space-y-8">
        <!-- Badge -->
        <Badge variant="outline" class="text-sm py-2">
          <span class="mr-2 text-primary"><Badge>New</Badge></span>
          <span>Ability Exam Portal</span>
        </Badge>

        <!-- Heading -->
        <h1 class="text-5xl md:text-6xl font-bold max-w-3xl">
          Prepare, Practice, and
          <br />
          <span class="bg-gradient-to-r from-primary to-[#3fc40a] bg-clip-text text-transparent">
            Ace the Exam
          </span>
        </h1>

        <!-- Subtitle -->
        <p class="text-xl text-muted-foreground max-w-xl">
          Access your ability tests and practice exams online with a modern, responsive interface.
        </p>

        <!-- Call-to-action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 mt-6">
          <!-- Login Button with Router -->
          <Button @click="goToExam" class="font-bold w-60 group/arrow">
            Start Exam
            <ArrowRight class="size-5 ml-2 group-hover/arrow:translate-x-1 transition-transform" />
          </Button>

          <Button @click="goToLogin" variant="secondary" class="font-bold w-60 group/arrow">
            Login
            <LogIn class="size-5 ml-2 group-hover/arrow:translate-x-2 transition-transform" />
          </Button>
        </div>
      </div>
    </section>
  </div>
</template>

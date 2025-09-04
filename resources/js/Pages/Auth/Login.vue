<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/Api/Axios';
import {toast} from 'vue-sonner'
const router = useRouter();

// Reactive form state
const form = ref({
  email: '',
  password: '',
});

// Login handler
const handleLogin = async () => {


  try {
    const response = await api.post('/login', form.value);

    // Store token
    const token = response.data.token;
    localStorage.setItem('token', token);

    // Log user for debugging
    console.log('Login user:', response.data.user);

    toast.success('Login successful! Redirecting to dashboard...');

    setTimeout(() => {
      router.push('/dashboard');
    }, 2000);
  }catch(err){
    toast.error('Unsuccessful login! Try again...')
    console.log(err)
  }
}
</script>

<template>
  <AuthLayout>
    <form @submit.prevent="handleLogin" class="grid gap-4">
      <!-- Email -->
      <div class="grid gap-2">
        <Label>Email</Label>
        <Input type="email" v-model="form.email" placeholder="you@example.com" />
      </div>

      <!-- Password -->
      <div class="grid gap-2">
        <Label>Password</Label>
        <Input type="password" v-model="form.password" placeholder="********" />
      </div>

   <Input type="text"  />

      <!-- Submit -->
      <Button type="submit" class="w-full">Login</Button>
    </form>
  </AuthLayout>
</template>

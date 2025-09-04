<script lang="ts" setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog';
import api from '@/Api/Axios';
import { toast } from 'vue-sonner';
import 'vue-sonner/style.css';

const router = useRouter();

const idNumber = ref('');
const accessKey = ref('');
const isLoading = ref(false);

const errors = ref<{ id_number?: string; access_key?: string }>({});

// Admin modal state
const showAdminDialog = ref(false);
const adminEmail = ref('');
const adminPassword = ref('');
const isAdminLoading = ref(false);

onMounted(() => {
  const reason = localStorage.getItem('redirectReason');
  if (reason) {
    toast.error(reason);
    localStorage.removeItem('redirectReason');
  }
});

const verifyStudent = async (withAdmin = false) => {
  errors.value = {};
  isLoading.value = true;

  try {
    const payload: any = {
      id_number: idNumber.value,
      access_key: accessKey.value,
    };

    if (withAdmin) {
      payload.admin_email = adminEmail.value;
      payload.admin_password = adminPassword.value;
    }

    const response = await api.post('examinee/verify', payload);

    const { student, batch } = response.data;

    toast.success('Verification Successful!');
    localStorage.setItem(
      'studentData',
      JSON.stringify({
        id: student.id,
        id_number: student.id_number,
        name: `${student.first_name} ${student.middle_name ?? ''} ${student.last_name}`
          .replace(/\s+/g, ' ')
          .trim(),
        first_name: student.first_name,
        last_name: student.last_name,
        middle_name: student.middle_name,
        course: student.course,
        birth_day: student.birth_day,
        gender: student.gender,
        batch: batch,
      })
    );
    setTimeout(() => {
      router.push('/exam/information-form');
    }, 2000);
  } catch (err: any) {
    if (err.response?.data) {
      const data = err.response.data;

      if (data.errors) {
        errors.value = {
          id_number: data.errors.id_number?.[0],
          access_key: data.errors.access_key?.[0],
        };
        toast.error('Please correct the highlighted fields.');
      } else if (data.message) {
        if (data.message.includes('Admin credentials required')) {
          showAdminDialog.value = true; // Open modal
          toast.error('Admin approval required.');
        } else {
          toast.error(data.message);
        }
      } else {
        toast.error('Verification failed. Please try again.');
      }
    } else {
      toast.error('Network error. Please try again.');
    }
  } finally {
    isLoading.value = false;
  }
};

const submitAdminApproval = async () => {
  isAdminLoading.value = true;
  try {
    await verifyStudent(true);
    showAdminDialog.value = false;
  } finally {
    isAdminLoading.value = false;
  }
};
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-background px-4">
    <Card class="w-full max-w-md shadow-lg">
      <CardHeader>
        <CardTitle class="text-center text-xl font-bold"> Exam Verification </CardTitle>
      </CardHeader>
      <CardContent class="space-y-4">
        <!-- ID Number Input -->
        <div class="grid gap-2">
          <Label for="idNumber">ID Number</Label>
          <Input
            id="idNumber"
            v-model="idNumber"
            placeholder="Enter your ID number"
            :class="['w-full', errors.id_number ? 'border-red-500 focus:ring-red-500' : '']"
          />
          <p v-if="errors.id_number" class="text-red-500 text-sm">
            {{ errors.id_number }}
          </p>
        </div>

        <!-- Access Key Input -->
        <div class="grid gap-2">
          <Label for="accessKey">Access Key</Label>
          <Input
            id="accessKey"
            v-model="accessKey"
            placeholder="Enter your access key"
            type="password"
            :class="['w-full', errors.access_key ? 'border-red-500 focus:ring-red-500' : '']"
          />
          <p v-if="errors.access_key" class="text-red-500 text-sm">
            {{ errors.access_key }}
          </p>
        </div>

        <!-- Submit Button -->
        <Button
          class="w-full font-bold mt-4"
          :disabled="isLoading || !idNumber || !accessKey"
          @click="verifyStudent"
        >
          {{ isLoading ? 'Verifying...' : 'Verify and Continue' }}
        </Button>
      </CardContent>
    </Card>

    <!-- Admin Approval Modal -->
    <Dialog v-model:open="showAdminDialog">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Admin Approval Required</DialogTitle>
        </DialogHeader>
        <div class="space-y-4">
          <div>
            <Label for="adminEmail">Admin Email</Label>
            <Input id="adminEmail" v-model="adminEmail" placeholder="Enter admin email" />
          </div>
          <div>
            <Label for="adminPassword">Admin Password</Label>
            <Input
              id="adminPassword"
              v-model="adminPassword"
              placeholder="Enter admin password"
              type="password"
            />
          </div>
        </div>
        <DialogFooter>
          <Button variant="outline" @click="showAdminDialog = false">Cancel</Button>
          <Button :disabled="isAdminLoading" @click="submitAdminApproval">
            {{ isAdminLoading ? 'Verifying...' : 'Approve' }}
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </div>
</template>

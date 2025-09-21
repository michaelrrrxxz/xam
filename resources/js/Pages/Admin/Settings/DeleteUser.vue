<script setup lang="ts">
import { reactive, ref } from 'vue';

// shadcn components
import { Button } from '@/components/ui/button';
import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useRouter } from 'vue-router';
const router = useRouter();

// other utils
import { toast } from 'vue-sonner';
import api from '@/Api/Axios';

// props - pass user id from parent
const props = defineProps<{
  userId: number | null;
}>();

const passwordInput = ref<HTMLInputElement | null>(null);

const form = reactive({
  password: '',
  processing: false,
  error: '',
});

const deleteUser = async (e: Event) => {
  e.preventDefault();
  form.processing = true;
  form.error = '';

  try {
    await api.delete(`admin/user/${props.userId}`, {
      data: { password: form.password },
    });
    setTimeout(() => {
      toast.success('User deleted successfully!');
    }, 2000);
    setTimeout(() => {
      window.location.href = '/';
    }, 1000);
  } catch (err) {
    form.error = 'Failed to delete user.';
    toast.error(form.error);
  } finally {
    form.processing = false;
  }
};
</script>

<template>
  <div class="space-y-6">
    <div>
      <h3 class="text-lg font-semibold">Delete account</h3>
      <p class="text-sm text-muted-foreground">Delete your account and all of its resources</p>
    </div>

    <div
      class="space-y-4 rounded-lg border border-red-100 bg-red-50 p-4 dark:border-red-200/10 dark:bg-red-700/10"
    >
      <div class="space-y-0.5 text-red-600 dark:text-red-100">
        <p class="font-medium">Warning</p>
        <p class="text-sm">Please proceed with caution, this cannot be undone.</p>
      </div>

      <Dialog>
        <DialogTrigger as-child>
          <Button variant="destructive">Delete account</Button>
        </DialogTrigger>

        <DialogContent>
          <form class="space-y-6" @submit="deleteUser">
            <DialogHeader class="space-y-3">
              <DialogTitle>Are you sure you want to delete your account?</DialogTitle>
              <DialogDescription>
                Once your account is deleted, all of its resources and data will also be permanently
                deleted. Please enter your password to confirm.
              </DialogDescription>
            </DialogHeader>

            <div class="grid gap-2">
              <Label for="password">Password</Label>
              <Input
                id="password"
                type="password"
                ref="passwordInput"
                v-model="form.password"
                placeholder="Password"
              />
              <p v-if="form.error" class="text-sm text-red-500">
                {{ form.error }}
              </p>
            </div>

            <DialogFooter class="gap-2">
              <DialogClose as-child>
                <Button type="button" variant="secondary">Cancel</Button>
              </DialogClose>

              <Button type="submit" variant="destructive" :disabled="form.processing">
                Delete account
              </Button>
            </DialogFooter>
          </form>
        </DialogContent>
      </Dialog>
    </div>
  </div>
</template>

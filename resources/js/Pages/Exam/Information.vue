<template>
  <div class="min-h-screen flex items-center justify-center bg-background px-4">
    <Card class="w-full max-w-lg shadow-lg">
      <CardHeader>
        <CardTitle class="text-center text-2xl font-bold"> Student Information </CardTitle>
      </CardHeader>

      <CardContent class="space-y-4">
        <!-- Name -->
        <div class="grid gap-2">
          <Label for="name">Full Name</Label>
          <Input
            id="name"
            v-model="form.name"
            placeholder="Full Name"
            readonly
            class="w-full bg-muted cursor-not-allowed"
          />
        </div>

        <!-- ID Number (readonly) -->
        <div class="grid gap-2">
          <Label for="id_number">ID Number</Label>
          <Input
            id="id_number"
            v-model="form.id_number"
            readonly
            class="w-full bg-muted cursor-not-allowed"
          />
        </div>

        <!-- Course -->
        <div class="grid gap-2">
          <Label for="course">Course</Label>
          <Input
            id="course"
            v-model="form.course"
            placeholder="Enter your course"
            readonly
            class="w-full bg-muted cursor-not-allowed"
          />
        </div>

        <!-- Address Section -->
        <div class="space-y-4">
          <!-- Region -->
          <div class="grid gap-2 w-full">
            <Label>Region</Label>
            <Select v-model="selectedRegion" @update:modelValue="onRegionChange">
              <SelectTrigger class="w-full">
                <SelectValue placeholder="Select Region" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem v-for="(region, key) in addressData" :key="key" :value="key">
                  {{ region.region_name }}
                </SelectItem>
              </SelectContent>
            </Select>
          </div>

          <!-- Province -->
          <div class="grid gap-2 w-full">
            <Label>Province</Label>
            <Select
              v-model="selectedProvince"
              @update:modelValue="onProvinceChange"
              :disabled="!selectedRegion"
            >
              <SelectTrigger class="w-full">
                <SelectValue placeholder="Select Province" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem v-for="(province, key) in provinces" :key="key" :value="key">
                  {{ key }}
                </SelectItem>
              </SelectContent>
            </Select>
          </div>

          <!-- Municipality -->
          <div class="grid gap-2 w-full">
            <Label>Municipality</Label>
            <Select
              v-model="selectedMunicipality"
              @update:modelValue="updateAddress"
              :disabled="!selectedProvince"
            >
              <SelectTrigger class="w-full">
                <SelectValue placeholder="Select Municipality" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem v-for="(municipality, key) in municipalities" :key="key" :value="key">
                  {{ key }}
                </SelectItem>
              </SelectContent>
            </Select>
          </div>
        </div>

        <div class="grid gap-2">
          <Label>School </Label>
          <Input
            type="text"
            name="school"
            id="school"
            class="form-control"
            list="schoolList"
            v-model="form.school"
            placeholder="Enter Full School Name"
          />
          <datalist id="schoolList">
            <option v-for="school in schools" :key="school.id" :value="school.name"></option>
          </datalist>
        </div>

        <div class="grid gap-2 w-full">
          <Label>Group</Label>
          <Select v-model="form.group_abc">
            <SelectTrigger class="w-full">
              <SelectValue placeholder="Select Group" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="a">A</SelectItem>
              <SelectItem value="b">B</SelectItem>
              <SelectItem value="c">C</SelectItem>
              <SelectItem value="d">D</SelectItem>
              <SelectItem value="e">E</SelectItem>
            </SelectContent>
          </Select>
        </div>

        <!-- Submit Button -->
        <Button class="w-full font-bold mt-4" @click="submitInfo"> Save and Proceed </Button>
      </CardContent>
    </Card>
  </div>
</template>

<script lang="ts" setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { toast } from 'vue-sonner';
import api from '@/Api/Axios';
import {
  Select,
  SelectTrigger,
  SelectContent,
  SelectItem,
  SelectValue,
} from '@/components/ui/select';

// Import JSON address data
import addressJson from '@/assets/data.json';

const router = useRouter();

// Form state
const form = ref({
  id_number: '',
  name: '',
  course: '',
  address: '',
  enrolledstudent_id: null,
  school: '',
  group_abc: '',
});

interface School {
  id: number;
  name: string;
}
const schools = ref<School[]>([]);
const open = ref(false);

// Fetch schools from API
const fetchSchool = async () => {
  try {
    const response = await api.get('examinee/school');
    schools.value = response.data;
    console.log(schools.value);
  } catch (error) {
    console.error('Error fetching schools:', error);
  }
};

// Load student data from localStorage
onMounted(() => {
  fetchSchool();
  const data = localStorage.getItem('studentData');
  if (data) {
    form.value = { ...form.value, ...JSON.parse(data), enrolledstudent_id: JSON.parse(data).id };
  }
});

// Address state
const addressData = ref<any>(addressJson);
const selectedRegion = ref('');
const selectedProvince = ref('');
const selectedMunicipality = ref('');

// Computed values for dropdowns
const provinces = computed(() =>
  selectedRegion.value ? addressData.value[selectedRegion.value].province_list : {}
);

const municipalities = computed(() =>
  selectedProvince.value ? provinces.value[selectedProvince.value].municipality_list : {}
);

// Handlers for cascading dropdown
const onRegionChange = () => {
  selectedProvince.value = '';
  selectedMunicipality.value = '';
  form.value.address = '';
};

const onProvinceChange = () => {
  selectedMunicipality.value = '';
  form.value.address = '';
};

// Update final address dynamically
const updateAddress = () => {
  if (selectedRegion.value && selectedProvince.value && selectedMunicipality.value) {
    const regionName = addressData.value[selectedRegion.value].region_name;
    form.value.address = `${regionName}, ${selectedProvince.value}, ${selectedMunicipality.value}`;
  } else {
    form.value.address = '';
  }
};

const filteredSchools = computed(() => {
  const search = form.value.school.toLowerCase();
  return search
    ? schools.value.filter((s) => s.name.toLowerCase().includes(search))
    : schools.value;
});

// Handle selecting a school
const selectSchool = (school: string) => {
  form.value.school = school;
  open.value = false;
};

// Submit form
const submitInfo = async () => {
  if (!form.value.address) {
    toast.error('Please fill in your address before proceeding.');
    return;
  }

  try {
    const response = await api.post('examinee/information', form.value);
    console.log('Information saved:', response.data);

    localStorage.setItem(
      'schoolData',
      JSON.stringify({
        school: form.value.school,
      })
    );
    localStorage.setItem(
      'info',
      JSON.stringify({
        group_abc: form.value.group_abc,
      })
    );
  } catch (error) {
    toast.error('Failed to save information. Please try again.');
    return;
  }

  toast.success('Information saved! Redirecting...');
  setTimeout(() => {
    router.push('/exam/trial');
  }, 2000);
};
</script>

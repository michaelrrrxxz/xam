<template>
  <div class="p-6 max-w-3xl mx-auto">
    <!-- Page Title -->
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Category Management</h1>

    <!-- Add Category Form -->
    <div class="bg-white shadow rounded-xl p-6 mb-8">
      <h2 class="text-lg font-semibold mb-4 text-gray-700">Add New Category</h2>
      <form @submit.prevent="addCategory" class="space-y-4">
        <!-- Name Input -->
        <div>
          <label class="block text-sm font-medium text-gray-600 mb-1">Name</label>
          <input
            v-model="newCategory.name"
            type="text"
            placeholder="Enter category name"
            class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring focus:ring-blue-300"
            required
          />
        </div>

        <!-- Description Input -->
        <div>
          <label class="block text-sm font-medium text-gray-600 mb-1">Description</label>
          <input
            v-model="newCategory.description"
            type="text"
            placeholder="Enter description"
            class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring focus:ring-blue-300"
            required
          />
        </div>

        <!-- Submit Button -->
        <button
          type="submit"
          class="bg-blue-500 text-white px-5 py-2 rounded-lg shadow hover:bg-blue-600 transition"
        >
          Submit
        </button>
      </form>
    </div>

    <!-- Edit Category Form -->
    <div v-if="showCategory.id" class="bg-white shadow rounded-xl p-6 mb-8">
      <h2 class="text-lg font-semibold mb-4 text-gray-700">Edit Category</h2>
      <form @submit.prevent="updateCategory" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-600 mb-1">Name</label>
          <input
            v-model="showCategory.name"
            type="text"
            placeholder="Enter category name"
            class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring focus:ring-blue-300"
            required
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-600 mb-1">Description</label>
          <input
            v-model="showCategory.description"
            type="text"
            placeholder="Enter description"
            class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring focus:ring-blue-300"
            required
          />
        </div>

        <button
          type="submit"
          class="bg-green-500 text-white px-5 py-2 rounded-lg shadow hover:bg-green-600 transition"
        >
          Update
        </button>
      </form>
    </div>

    <!-- Category List -->
    <div class="bg-white shadow rounded-xl p-6">
      <h2 class="text-lg font-semibold mb-4 text-gray-700">Category List</h2>

      <div v-if="categories.length" class="overflow-x-auto">
        <table class="min-w-full border border-gray-200 rounded-lg">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-4 py-2 text-left text-gray-600 font-medium border-b">#</th>
              <th class="px-4 py-2 text-left text-gray-600 font-medium border-b">Name</th>
              <th class="px-4 py-2 text-left text-gray-600 font-medium border-b">Description</th>
              <th class="px-4 py-2 text-left text-gray-600 font-medium border-b">Options</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="category in categories"
              :key="category.id"
              class="hover:bg-gray-50 transition"
            >
              <td class="px-4 py-2 border-b text-gray-700">{{ category.id }}</td>
              <td class="px-4 py-2 border-b text-gray-800 font-medium">{{ category.name }}</td>
              <td class="px-4 py-2 border-b text-gray-600">{{ category.description }}</td>
              <td class="px-4 py-2 border-b text-gray-600 space-x-2">
                <button
                  @click="deleteCategory(category.id)"
                  class="bg-red-500 hover:bg-red-700 text-white px-3 py-1 rounded"
                >
                  Delete
                </button>
                <button
                  @click="editCategory(category.id)"
                  class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded"
                >
                  Edit
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <p v-else class="text-gray-500 text-sm italic">No categories found.</p>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { onMounted, ref } from 'vue';
import api from '@/Api/Axios';

interface Category {
  id: number;
  name: string;
  description: string;
}

const categories = ref<Category[]>([]);
const newCategory = ref<Partial<Category>>({ name: '', description: '' });
const showCategory = ref<Partial<Category>>({ id: undefined, name: '', description: '' });

const resetForm = () => {
  newCategory.value = { name: '', description: '' };
};

const fetchCategories = async () => {
  try {
    const response = await api.get('/categories');
    categories.value = response.data;
  } catch (error) {
    console.error('Error fetching categories:', error);
  }
};

const addCategory = async () => {
  try {
    await api.post('/categories', newCategory.value);
    await fetchCategories();
    resetForm();
  } catch (error) {
    console.error('Error adding category:', error);
  }
};

const editCategory = async (id: number) => {
  try {
    const response = await api.get(`/categories/${id}`);
    showCategory.value = response.data;
  } catch (error) {
    console.error('Error fetching category:', error);
  }
};

const updateCategory = async () => {
  try {
    if (!showCategory.value.id) return;
    await api.put(`/categories/${showCategory.value.id}`, showCategory.value);
    await fetchCategories();
    showCategory.value = { id: undefined, name: '', description: '' };
  } catch (error) {
    console.error('Error updating category:', error);
  }
};

const deleteCategory = async (id: number) => {
  try {
    await api.delete(`/categories/${id}`);
    categories.value = categories.value.filter((category) => category.id !== id);
  } catch (error) {
    console.error('Error deleting category:', error);
  }
};

onMounted(fetchCategories);
</script>

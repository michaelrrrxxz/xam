import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router';

// --- Page Imports --- //
import Login from '@/Pages/Auth/Login.vue';
import Register from '@/Pages/Auth/Register.vue';
import Landing from '@/Pages/Auth/LandingPage.vue';

import EnrolledStudent from '@/Pages/Admin/EnrolledStudent.vue';
import Batch from '@/Pages/Admin/Batch.vue';
import Questions from '@/Pages/Admin/Questions.vue';
import Dashboard from '@/Pages/Admin/Dashboard.vue';
import AdminResult from '@/Pages/Admin/Result.vue';

import Verify from '@/Pages/Exam/Verify.vue';
import Information from '@/Pages/Exam/Information.vue';
import Trial from '@/Pages/Exam/Trial.vue';
import Actual from '@/Pages/Exam/Actual.vue';
import Result from '@/Pages/Exam/Result.vue';

// --- Extend Vue Router Meta --- //
declare module 'vue-router' {
  interface RouteMeta {
    title?: string;
    requiresAuth?: boolean; // flag for protected routes
  }
}

const appName = 'Exam';

const routes: RouteRecordRaw[] = [
  // --- Authentication Routes --- //
  {
    path: '/register',
    name: 'Register',
    component: Register,
    meta: { title: appName },
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { title: appName },
  },
  {
    path: '/',
    name: 'Landing',
    component: Landing,
    meta: { title: appName },
  },

  // --- Exam Routes --- //
  {
    path: '/exam-verify',
    name: 'exam-verify',
    component: Verify,
    meta: { title: `Exam: ${appName}` },
  },
  {
    path: '/exam/information-form',
    name: 'exam-information-form',
    component: Information,
    meta: { title: `Exam: ${appName}` },
  },
  {
    path: '/exam/trial',
    name: 'exam-trial',
    component: Trial,
    meta: { title: `Exam: ${appName}` },
  },
  {
    path: '/exam',
    name: 'exam',
    component: Actual,
    meta: { title: `Exam: ${appName}` },
  },
  {
    path: '/exam/result',
    name: 'exam-result',
    component: Result,
    meta: { title: `Result ${appName}` },
  },

  // --- Admin/Management Routes --- //
  {
    path: '/questions',
    name: 'Questions',
    component: Questions,
    meta: { title: `Questions: ${appName}`, requiresAuth: true },
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: Dashboard,
    meta: { title: `Dashboard: ${appName}`, requiresAuth: true },
  },
  {
    path: '/batch',
    name: 'Batch',
    component: Batch,
    meta: { title: `Batch: ${appName}`, requiresAuth: true },
    props: true,
  },
  {
    path: '/admin/results',
    name: 'Result',
    component: AdminResult,
    meta: { title: `Result: ${appName}`, requiresAuth: true },
    props: true,
  },
  {
    path: '/enrolled-students',
    name: 'Enrolled-students',
    component: EnrolledStudent,
    meta: { title: `Enrolled Students: ${appName}`, requiresAuth: true },
    props: true,
  },

  // --- Optional 404 Page --- //
  // {
  //   path: '/:pathMatch(.*)*',
  //   name: 'NotFound',
  //   component: NotFound,
  //   meta: { title: `404 Not Found: ${appName}` }
  // }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// --- Navigation Guards --- //
router.beforeEach((to, from, next) => {
  const routeName = to.name?.toString() || '';

  // --- Exam Access Guard --- //
  const examProtectedRoutes = ['exam-information-form', 'exam-trial', 'exam'];
  if (examProtectedRoutes.includes(routeName)) {
    let studentData: Record<string, any> | null = null;

    try {
      studentData = JSON.parse(localStorage.getItem('studentData') || 'null');
    } catch (error) {
      studentData = null;
    }

    if (!studentData || Object.keys(studentData).length === 0) {
      localStorage.setItem('redirectReason', 'Please verify first.');
      return next({ name: 'exam-verify' });
    }
  }

  // --- Admin Access Guard --- //
  if (to.meta.requiresAuth) {
    const token = localStorage.getItem('token');

    if (!token) {
      // Redirect to login if token doesn't exist
      return next({ name: 'Login' });
    }
  }

  next();
});

// --- Optional: Update Document Title --- //
router.afterEach((to) => {
  document.title = to.meta.title || appName;
});

export default router;

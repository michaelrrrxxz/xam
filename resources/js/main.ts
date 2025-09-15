import { createApp } from 'vue';
import App from './App.vue';
import router from './router/index';
import { Toaster } from './components/ui/sonner';
import { createPinia } from 'pinia';
import 'tippy.js/dist/tippy.css';

const app = createApp(App);
const pinia = createPinia();

router.beforeEach((to, from, next) => {
  document.title = to.meta.title || 'Default Title';
  next();
});

app.use(router);
app.use(pinia);
// eslint-disable-next-line vue/multi-word-component-names
app.component('Toaster', Toaster);
app.mount('#app');

import './bootstrap';
import { createApp } from 'vue';
import router from './router';
import App from './components/App.vue';

// Create axios instance with default config
window.axios = axios;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.withCredentials = true;

// Create Vue app
const app = createApp(App);

// Use router
app.use(router);

// Mount app
app.mount('#app');
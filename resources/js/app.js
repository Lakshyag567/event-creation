import {createApp} from 'vue';
import Main from './layouts/Main.vue';
import router from "./router.js";

const app= createApp(Main);
app.use(router);
app.mount('#app');
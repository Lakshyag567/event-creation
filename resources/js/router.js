import Home from "./pages/Home.vue";
import {createRouter, createWebHistory} from "vue-router";
import BulkUpload from "./pages/BulkUpload.vue";

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/',
            component: Home,
            name:'home'
        },
        {
            path: '/bulk-upload',
            component: BulkUpload,
            name:'bulk-upload'
        }
    ],
})

export default router;
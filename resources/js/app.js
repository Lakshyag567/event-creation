import {createApp} from 'vue';
import Main from './layouts/Main.vue';
import router from "./router.js";
import { DateMixin } from './mixin/datetime.js';
import TopBar from "@/components/TopBar.vue";
import Loading from "@/components/Loading.vue";
import ImageModal from "@/components/ImageModal.vue";
import Unicon from 'vue-unicons'
import {uniUpload, uniCalendarAlt, uniEdit, uniTrashAlt, uniTimes, uniSearch, uniAngleDown, uniSync } from 'vue-unicons/dist/icons'
Unicon.add([uniUpload, uniCalendarAlt, uniEdit, uniTrashAlt, uniTimes, uniSearch, uniAngleDown, uniSync ])

const app= createApp(Main);

app.component("Topbar", TopBar);
app.component("Loading", Loading);
app.component("ImageModal", ImageModal);

app.mixin(DateMixin)
app.use(Unicon)
app.use(router);
app.mount('#app');
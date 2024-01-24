import {createApp} from 'vue';
import Main from './layouts/Main.vue';
import router from "./router.js";
import { DateMixin } from './mixin/datetime.js';
import TopBar from "@/components/TopBar.vue";
import Loading from "@/components/Loading.vue";
import Unicon from 'vue-unicons'
import { uniExport, uniUpload, uniCalendarAlt } from 'vue-unicons/dist/icons'
Unicon.add([uniExport, uniUpload, uniCalendarAlt])

const app= createApp(Main);

app.component("Topbar", TopBar);
app.component("Loading", Loading);

app.use(Unicon)
app.mixin(DateMixin)
app.use(router);
app.mount('#app');
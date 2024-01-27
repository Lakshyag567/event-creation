import {createApp} from 'vue';
import "./axios.js";
import Unicon from 'vue-unicons'
import Pusher from 'pusher-js';
import config from './config.js';
import 'vue-toast-notification/dist/theme-sugar.css';
import router from "./router.js";
import Main from './layouts/Main.vue';
import DateMixin from './mixin/datetime.js';
import TopBar from "@/components/TopBar.vue";
import Loading from "@/components/Loading.vue";
import ImageModal from "@/components/ImageModal.vue";

const app = createApp(Main);

//Icons
import {uniUpload, uniCalendarAlt, uniEdit, uniTrashAlt, uniTimes, uniSearch, uniAngleDown, uniArrowLeft, uniArrowRight, uniSync} from 'vue-unicons/dist/icons'
Unicon.add([uniUpload, uniCalendarAlt, uniEdit, uniTrashAlt, uniTimes, uniSearch, uniAngleDown, uniArrowLeft, uniArrowRight, uniSync])

//Common Components
app.component("Topbar", TopBar);
app.component("Loading", Loading);
app.component("ImageModal", ImageModal);

app.mixin(DateMixin)
app.use(Unicon)
app.use(router);

//Pusher Config
window.Pusher = new Pusher(config.PUSHER_APP_KEY, {
    cluster: config.PUSHER_APP_CLUSTER,
    encrypted: true
});

app.mount('#app');
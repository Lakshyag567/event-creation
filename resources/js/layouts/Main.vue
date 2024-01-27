<template>
    <div>
        <Topbar/>
        <div class="mt-16 xl:mt-20 mb-6">
            <div class="w-fit relative mx-auto my-4 py-2 transition-all border-none" v-if="notification">
                <div :class="`text-white px-10 py-8 text-xl text-justify rounded-lg ${colorClass}`">
                    <p class="text-center text-2xl font-semibold mb-2">{{ notification?.msg }} !!</p>
                    <span class="my-2" v-html="notification?.data"></span>
                </div>
                <button aria-label="close" title="close" @click="notification = null" class="absolute top-2 -right-2 text-3xl text-white px-2 pt-0.5 cursor-pointer ">
                    <unicon name="times" class="fill-white w-8 h-8 m-1 p-0 border-2 rounded-full"></unicon>
                </button>
            </div>
            <router-view/>
        </div>
    </div>
</template>
<script setup>
    import {ref, onMounted, computed} from 'vue';
    import {useToast} from "vue-toast-notification";

    const notification = ref(null);
    const toast = useToast();

    const colorClass = computed(() => {
        if (notification.value?.status === 'success') {
            return 'bg-green-500';
        } else if (notification.value?.status === 'error') {
            return 'bg-red-500';
        }
    })

    onMounted(() => {
        //Receiving notifications
        Pusher
            .subscribe('file-upload-channel')
            .bind('file-upload-event', (data) => {
                notification.value = data.data;
                document.body.scrollTop = document.documentElement.scrollTop = 0;
            })
    });
</script>

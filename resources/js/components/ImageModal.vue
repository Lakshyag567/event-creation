<template>
    <Transition>
        <div v-if="show"
             class="fixed right-0 left-0 top-0 bottom-0 z-50 items-center justify-center bg-gray-900 bg-opacity-80 px-4 w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-full">
            <div class="w-[95%] mx-auto relative flex justify-center h-full items-center">
                <button aria-label="close" title="close" @click="hide()" class="absolute top-0 -right-4 text-4xl text-white bg-primary-500 px-2 pt-0.5 rounded-sm">
                    <unicon name="times" class="fill-white w-8 h-8 m-0 p-0"></unicon>
                </button>
                <!-- Modal content -->
                <img :src="img" class="mx-12 border border-gray-500 rounded shadow-lg p-2.5 bg-white" alt="image"/>
            </div>
        </div>
    </Transition>
</template>
<script setup>
    import {ref, watch} from 'vue'

    const props = defineProps({
        show: {type: Boolean, required: true},
        hide: {type: Function, required: true},
        img: {type: String, required: true},
    })

    // Create a ref to store the value of the "show"
    const myShow = ref(props.show);

    // Watch for changes in "show"
    watch(() => props.show, (newValue) => {
        myShow.value = newValue;
    });
</script>

<style scoped>
img {
    max-height: calc(100vh - 10vh);
    max-width: 100%;
}
</style>

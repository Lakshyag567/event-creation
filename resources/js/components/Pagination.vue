<template>
    <div>
        <p class="text-base text-gray-700">
            Showing <span class="font-medium">{{ pagination.from || '0' }}</span>
            to <span class="font-medium">{{ pagination.to || '0' }}</span>
            of <span class="font-medium">{{ pagination.total || '0' }}</span>
            results
        </p>
    </div>
    <div class="my-4 px-2 flex justify-end">
        <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
            <!-- Previous btn  -->
            <button class="prev-btn" type="button" :disabled="isPrevDisabled" @click="fetchNewData(pagination.prev_page_url)">
                <unicon name="arrow-left" class="text-xl gray text-center h-6 w-6"></unicon>
            </button>

            <!-- Page Links 1,2,3 .... 10 -->
            <template v-if="pagination.links">
                <template v-for="(link,index) in pagination.links">
                    <button class="active-link" aria-current="page" v-if="link.active">
                        {{ link.label || '0' }}
                    </button>
                    <button class="default-link" @click="link.url ? props.fetchNewData(link.url) : false" v-else>
                        {{ link.label || '0' }}
                    </button>
                </template>
            </template>

            <!-- Next btn  -->
            <button type="button" :disabled="isNextDisabled" @click="props.fetchNewData(pagination.next_page_url)" class="next-btn">
                <unicon name="arrow-right" class="text-xl gray text-center h-6 w-6"></unicon>
            </button>
        </nav>
    </div>
</template>
<script setup>
    import {computed} from 'vue'

    const props = defineProps({
        pagination: {type: Object, required: true},
        fetchNewData: {type: Function, required: true},
    })

    const isPrevDisabled = computed(() => {
        return !props.pagination?.prev_page_url;
    })
    const isNextDisabled = computed(() => {
        return !props.pagination?.next_page_url;
    })
</script>

<template>
    <div class="container mx-auto my-2 px-4">
        <div class="flex flex-wrap justify-between items-center">
            <div class="flex gap-2 items-center text-3xl text-primary-500 font-semibold">
                <unicon name="calendar-alt" class="fill-primary-500 w-8 h-8"></unicon>
                <h3 class="text-start my-4">Events</h3>
            </div>
            <div>
                <router-link :to="{ name: 'bulk-upload'}" class="upload-btn">
                    Bulk Upload
                    <unicon name="upload" fill="white"></unicon>
                </router-link>
            </div>
        </div>

        <div class="bg-white p-4 overflow-x-auto shadow-md rounded-lg my-2">
            <div class="block">
                <form @submit.prevent="createOrEditEvent()">
                    <div class="flex flex-col md:flex-row gap-4 my-2 py-1">
                        <div class="md:w-1/2 w-full">
                            <label for="title" class="block mb-2 text-sm font-bold text-gray-900">Title <span class="text-red-600">*</span></label>
                            <input type="text" v-model="title" id="title" class="form-input" placeholder="AI Workshop" required>
                        </div>
                        <div class="md:w-1/2 w-full">
                            <label for="image" class="block mb-2 text-sm font-bold text-gray-900">Image <span class="text-red-600" v-if="!editId">*</span></label>
                            <input type="file" @change="handleImage($event)" id="image" class="form-input" accept="image/*" :required="!editId">
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row gap-4 my-2 py-1">
                        <div class="md:w-1/2 w-full">
                            <label for="start_date" class="block mb-2 text-sm font-bold text-gray-900">Start Date <span class="text-red-600">*</span></label>
                            <input type="date" v-model="start_date" id="start_date" class="form-input" required>
                        </div>
                        <div class="md:w-1/2 w-full">
                            <label for="end_date" class="block mb-2 text-sm font-bold text-gray-900">End Date <span class="text-red-600">*</span></label>
                            <input type="date" v-model="end_date" id="end_date" class="form-input" required>
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row gap-4 my-2 py-1">
                        <div class="w-full">
                            <label for="description" class="block mb-2 text-sm font-bold text-gray-900">Description <span class="text-red-600">*</span></label>
                            <textarea type="text" rows="4" v-model="description" id="description" class="form-input" required></textarea>
                        </div>
                    </div>
                    <div class="text-center my-6">
                        <button type="submit" class="submit-btn">
                            {{ editId ? 'Update' : 'Create' }}
                        </button>
                        <button type="button" @click="clear()" class="clear-btn">
                            Clear
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="bg-white p-4 overflow-x-auto shadow-md rounded-lg my-4">
            <div class="block">
                <div class="flex flex-wrap justify-between items-center gap-2 py-4">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-2 cursor-pointer" v-if="keyword" @click="keyword = ''; fetchEvent();">
                            <unicon name="times" class=""></unicon>
                        </div>
                        <div class="absolute inset-y-0 left-0 flex items-center pl-2 pointer-events-none" v-else>
                            <unicon name="search" class=""></unicon>
                        </div>
                        <input type="text" v-model="keyword" title="Search" class="search" placeholder="Search" @change="fetchEvent()">
                    </div>
                    <div class="flex">
                        <div class="flex border border-gray-600 rounded-lg bg-white">
                            <button class="px-2 pt-1 m-[2px] hover:bg-gray-100 border-r border-solid cursor-pointer" @click="fetchEvent()">
                                <unicon name="sync" class=""></unicon>
                            </button>
                            <div class="relative flex items-center">
                                <select title="Filter" v-model="filter" @change="fetchEvent()" class="filter-dropdown">
                                    <option class="bg-gray-100" value="">All Events</option>
                                    <option class="bg-gray-100" value="FINISHED">Finished Events</option>
                                    <option class="bg-gray-100" value="UPCOMING">Upcoming Events</option>
                                    <option class="bg-gray-100" value="UPCOMING7">Upcoming in 7 days</option>
                                    <option class="bg-gray-100" value="FINISHED7">Finished in last 7 days</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <unicon name="angle-down" class=""></unicon>
                                </div>
                            </div>
                        </div>
                        <div class="relative flex border border-gray-600 rounded-lg bg-white ml-2">
                            <select class="!w-16 !h-auto filter-dropdown" style="appearance: unset;" @change="fetchEvent()" v-model="rows">
                                <option value="25" class="bg-white" selected>25</option>
                                <option value="50" class="bg-white">50</option>
                                <option value="100" class="bg-white">100</option>
                                <option value="250" class="bg-white">250</option>
                                <option value="500" class="bg-white">500</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                <unicon name="angle-down" class=""></unicon>
                            </div>
                        </div>
                    </div>
                </div>
                <template v-if="loading">
                    <Loading/>
                </template>
                <template v-else-if="events && events.length > 0">
                    <div class="clear-right overflow-x-auto">
                        <div class="table border-solid border border-gray-500 w-full">
                            <div class="table-row table-head">
                                <div class="table-cell border-gray-500 text-center font-semibold uppercase w-10 p-1">S.No.</div>
                                <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1 min-w-24">Image</div>
                                <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1 min-w-24">Title</div>
                                <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1 min-w-36">Description</div>
                                <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1 min-w-28">Start Date</div>
                                <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1 min-w-24">End Date</div>
                                <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1 min-w-32">Creation On</div>
                                <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1 min-w-24">Actions</div>
                            </div>
                            <div v-for="(event, index) in events" v-bind:key="event.id" class="table-row table-body hover:bg-primary-100" :class="{ 'bg-primary-200': event.id === editId }">
                                <div class="table-cell border-t border-gray-500 text-sm text-center w-10 p-1">
                                    {{ pagination.from + index }}
                                </div>
                                <div class="table-cell border-t border-l border-gray-500 text-sm p-1 text-center !align-middle">
                                    <img class="w-20 h-20 border border-gray-400 mx-auto p-1 cursor-pointer rounded-[50%]"
                                         v-if="event.image"
                                         @click="openImageModal(storage_url + event.image)"
                                         :src="storage_url + event.image" :alt="event.title"
                                    >
                                    <p class="text-center text-gray-800" v-else>--No Image--</p>
                                </div>
                                <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center">{{ event.title }}</div>
                                <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center">{{ event.description }}</div>
                                <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1">
                                    <div class="font-normal text-gray-900" v-html="formatDate(event.start_date)"></div>
                                </div>
                                <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1">
                                    <div class="font-normal text-gray-900" v-html="formatDate(event.end_date)"></div>
                                </div>
                                <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1">
                                    <div class="font-normal text-gray-900" v-html="formatDateTime(event.created_at)"></div>
                                </div>
                                <div class="table-cell border-t border-l border-gray-500 text-sm align-[middle!important] text-center">
                                    <div class="flex gap-4 items-center justify-center">
                                        <a href="javascript:void(0)" @click="editEvent(event.id)" type="button" class="font-medium cursor-pointer text-yellow-500">
                                            <unicon name="edit" class="fill-primary-500"></unicon>
                                            <i class="fi fi-rr-pencil w-5 h-5 text-xl"></i>
                                        </a>
                                        <a href="javascript:void(0)" @click="deleteEvent(event.id)" type="button" class="font-medium cursor-pointer text-red-500">
                                            <unicon name="trash-alt" class="fill-red-500"></unicon>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between py-4">
                            <Pagination :pagination="pagination" :fetchNewData="fetchEvent"/>
                        </div>
                    </div>
                </template>
                <template v-else>
                    <div>
                        <p class="text-center text-2xl">No Events Found !</p>
                    </div>
                </template>
            </div>
        </div>
        <ImageModal :show="showModal" :hide="closeImageModal" :img="imgModal"/>
    </div>
</template>

<script setup>
    import {ref, defineComponent, onMounted, watch} from 'vue';
    import {useToast} from 'vue-toast-notification';
    import Pagination from "@/components/Pagination.vue";

    const storage_url = window.location.origin + "/storage/"
    const loading = ref(true);
    const editId = ref('');
    const events = ref([]);
    const pagination = ref({});
    //form-fields
    const title = ref('');
    const description = ref('');
    const start_date = ref('');
    const end_date = ref('');
    const image = ref('');
    //filters
    const keyword = ref('');
    const filter = ref('');
    const rows = ref('25');
    //Image modal
    const showModal = ref(false);
    const imgModal = ref('');
    //Toast
    const toast = useToast();

    /* Watcher on search */
    watch(keyword, () => {
        fetchEvent();
    })

    /* Methods */
    const openImageModal = (img) => {
        showModal.value = true;
        imgModal.value = img;
    };
    const closeImageModal = () => {
        showModal.value = false;
    };

    const handleImage = (e) => {
        if (e?.target?.files.length > 0) {
            const file = e.target.files[0];
            if (file.type.match('image.*')) {
                image.value = file;
            } else {
                document.getElementById('image').value = '';
                alert("Please choose only image!");
            }
        }
    }
    const clear = () => {
        editId.value = '';
        title.value = '';
        description.value = '';
        start_date.value = '';
        end_date.value = '';
        image.value = '';
        document.getElementById('image').value = ''
    }


    /*Creating new event and edit event */
    const createOrEditEvent = () => {
        let url = '/event';
        let formData = new FormData();
        //Basic fields
        formData.append('title', title.value);
        formData.append('description', description.value);
        formData.append('start_date', start_date.value);
        formData.append('end_date', end_date.value);

        //If image exists
        if (image.value) {
            formData.append('image', image.value);
        }
        //If the action is edit
        if (editId.value) {
            url += '/' + editId.value;
            formData.append('_method', 'PUT');
            formData.append('id', editId.value);
        }
        axios.post(url, formData)
            .then(res => {
                clear(); //clear the form fields
                fetchEvent(); //fetching events with new data
                toast[res.data.status](res.data.msg); //display success toast msg
            })
            .catch(err => {
                err.handleGlobally && err.handleGlobally();
            })
    }
    const fetchEvent = (url = 'event') => {
        loading.value = true;
        axios.get(url, {
            params: {
                filter: filter.value,
                keyword: keyword.value,
                rows: rows.value,
            }
        })
            .then(res => {
                loading.value = false;
                let {data, ...paginate} = res.data;
                //updating the "events" ref
                events.value = data || [];
                //Handling pagination links data
                paginate.links.pop();
                paginate.links.shift();
                pagination.value = paginate;
            })
            .catch(err => {
                loading.value = false;
                err.handleGlobally && err.handleGlobally();
            })
    }
    /* Fetch the particular event by <id> to edit */
    const editEvent = (id) => {
        axios
            .get('event/' + id)
            .then(res => {
                editId.value = res.data.id;
                title.value = res.data.title;
                description.value = res.data.description;
                start_date.value = res.data.start_date;
                end_date.value = res.data.end_date;
                //scroll top for edit the form
                document.body.scrollTop = document.documentElement.scrollTop = 0;
            })
            .catch(err => {
                err.handleGlobally && err.handleGlobally();
            })
    }
    /* Deleting the particular event */
    const deleteEvent = (id) => {
        if (!confirm("Are you sure want to delete ?")) {
            return;
        }
        axios.delete('event/' + id)
            .then(res => {
                fetchEvent(); //fetching events after deletion
                toast[res.data.status](res.data.msg); //display success toast msg
            })
            .catch(err => {
                err.handleGlobally && err.handleGlobally();
                fetchEvent();
            })
    }

    //Fetch data when component mounts
    onMounted(() => {
        fetchEvent();
    });

</script>

<style lang="scss" scoped>

</style>
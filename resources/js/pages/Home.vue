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
        <form @submit.prevent="editOrCreateEvent()">
          <div class="flex flex-col md:flex-row gap-4 my-2 py-1">
            <div class="md:w-1/2 w-full">
              <label for="title" class="block mb-2 text-sm font-bold text-gray-900">Title <span class="text-red-600">*</span></label>
              <input type="text" v-model="title" id="title" class="form-input" placeholder="AI Workshop" required>
            </div>
            <div class="md:w-1/2 w-full">
              <label for="image" class="block mb-2 text-sm font-bold text-gray-900">Image <span class="text-red-600">*</span></label>
              <input type="file" @change="handleImage($event)" id="image" class="form-input" accept="image/*" required>
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

    <div class="bg-white p-4 overflow-x-auto shadow-md rounded-lg my-2">
      <div class="block">
        <template v-if="loading">
          <Loading/>
        </template>
        <template v-else-if="events && events.length > 0">
          <div class="clear-right overflow-x-auto">
            <div class="table border-solid border border-gray-500 w-full">
              <div class="table-row table-head">
                <div class="table-cell border-gray-500 text-center font-semibold uppercase w-10 p-1">S.No.</div>
                <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">Title</div>
                <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">Description</div>
                <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">Start Date</div>
                <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">End Date</div>
                <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">Actions</div>
              </div>
              <div v-for="(event, index) in events" v-bind:key="event.id" class="table-row table-body hover:bg-primary-100" :class="{ 'bg-primary-200': event.id === editId }">
                <div class="table-cell border-t border-gray-500 text-sm text-center w-10 p-1">
                  {{ index + 1 }}
                </div>
                <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center">{{ event.title }}</div>
                <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center">{{ event.description }}</div>
                <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1 !align-middle">
                  <div class="font-normal text-gray-900" v-html="formDateTime(event.start_date)"></div>
                </div>
                <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1 !align-middle">
                  <div class="font-normal text-gray-900" v-html="formDateTime(event.end_date)"></div>
                </div>
                <div class="table-cell border-t border-l border-gray-500 text-sm align-[middle!important] text-center">
                  <div class="flex gap-4 items-center justify-center">
                    <a href="javascript:void(0)" @click="editEvent(event.id)" type="button"
                       class="font-medium cursor-pointer text-yellow-500">
                      <i class="fi fi-rr-pencil w-5 h-5 text-xl"></i>
                    </a>
                    <a href="javascript:void(0)" @click="deleteEvent(event.id)" type="button"
                       class="font-medium cursor-pointer text-red-500">
                      <i class="fi fi-rr-trash w-5 h-5 text-xl"></i>
                    </a>
                  </div>
                </div>
              </div>
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
  </div>
</template>

<script>
import {ref, defineComponent, onMounted} from 'vue';
import axios from "axios";

export default defineComponent({
  name: "Home",
  setup() {
    const loading = ref(true);
    const editId = ref('');
    const events = ref([{}]);
    //form-fields
    const title = ref('');
    const description = ref('');
    const start_date = ref('');
    const end_date = ref('');
    const image = ref('');
    //filters
    const filter = ref(25);

    /* Methods */
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
      title.value = '';
      description.value = '';
      start_date.value = '';
      end_date.value = '';
      editId.value = '';
      document.getElementById('image').value = ''
    }

    const fetchEvent = () => {
      loading.value = true;
      axios.get('/admin/event', {
        params: {
          filter: filter,
        }
      })
          .then(res => {
            loading.value = false;
            events.value = res.data.data || [];
          })
          .catch(err => {
            loading.value = false;
            err.handleGlobally && err.handleGlobally();
          })
    }
    const editEvent = (id) => {
      axios.get('/admin/event/' + id)
          .then(res => {
            editId.value = res.data.data.id;
            title.value = res.data.data.title;
            description.value = res.data.data.description;
            start_date.value = res.data.data.start_date;
            end_date.value = res.data.data.end_date;
          })
          .catch(err => {
            err.handleGlobally && err.handleGlobally();
          })
    }
    const deleteEvent = (id) => {
      axios.delete('/admin/event/' + id)
          .then(res => {
            //show_toast(res.data.status, res.data.msg);
            fetchEvent();
          })
          .catch(err => {
            err.handleGlobally && err.handleGlobally();
            fetchEvent();
          })
    }
    const editOrCreateEvent = () => {
      let url = '/admin/event';
      let formData = new FormData();
      formData.append('title', title);
      formData.append('description', description);
      formData.append('start_date', start_date);
      formData.append('end_date', end_date);

      if (editId) {
        //If the action is edit
        url += '/' + editId;
        formData.append('_method', 'PUT');
        formData.append('id', editId);
      }
      axios.post(url, formData)
          .then(res => {
            //show_toast(res.data.status, res.data.msg);
            clear();
            fetchEvent();
          })
          .catch(err => {
            err.handleGlobally && err.handleGlobally();
            fetchEvent();
          })
    }

    return {
      loading,
      editId,
      events,
      title,
      description,
      start_date,
      end_date,
      image,
      filter,
      handleImage, clear,
      fetchEvent, editOrCreateEvent, editEvent, deleteEvent
    }
  },
})
</script>

<style lang="scss" scoped>

</style>
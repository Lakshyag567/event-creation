<template>
    <div class="container mx-auto my-2 px-4">
        <div class="flex flex-wrap justify-between items-center">
            <div>
                <a @click="$router.go(-1)"
                   class="inline-flex items-center gap-2 px-4 py-2 text-base font-bold text-center text-white align-middle transition-all rounded-lg cursor-pointer bg-gray-800 hover:bg-black hover:text-white">
                    <unicon name="arrow-left" fill="white"></unicon>
                    Back
                </a>
            </div>
        </div>
        <div class="flex gap-2 items-center text-3xl text-primary-500 font-semibold">
            <unicon name="upload" class="fill-primary-500 font-bold w-8 h-8"></unicon>
            <h3 class="text-start my-8">Upload</h3>
        </div>

        <div class="bg-white p-4 mx-auto my-2 overflow-x-auto shadow-md rounded-lg">
            <div class="block">
                <form @submit.prevent="uploadFile()">
                    <div class="flex flex-col md:flex-row gap-4 my-2 py-1">
                        <div class="w-full">
                            <label for="file" class="block mb-2 text-sm font-bold text-gray-900">Excel File <span class="text-red-600">*</span></label>
                            <input type="file" @change="handleFileChange($event)" id="file" class="form-input" accept=".csv,.xls,.xlsx" required>
                        </div>
                    </div>
                    <div class="w-full">
                        <button type="submit" class="submit-btn" :disabled="uploading">
                            {{ progress ? progress : '' }}
                            {{ uploading ? 'Uploading...' : 'Upload' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="bg-white p-4 mx-auto my-2 overflow-x-auto shadow-md rounded-lg" v-if="responseData">
            <h1 class="text-3xl mb-4">Response :- </h1>
            <div class="block">
                <p v-html="responseData" class="text-base"></p>
            </div>
        </div>
    </div>
</template>

<script setup>
    import {ref} from "vue";
    import {useToast} from "vue-toast-notification";

    const uploading = ref(false);
    const file = ref('');
    const toast = useToast();
    const responseData = ref('');
    const progress = ref('');

    const handleFileChange = (e) => {
        if (e?.target?.files.length > 0) {
            const targetFile = e.target.files[0];
            if (targetFile) {
                const fileName = targetFile.name.toLowerCase();
                if (fileName.endsWith('.xls') || fileName.endsWith('.xlsx') || fileName.endsWith('.csv')) {
                    file.value = targetFile //updating ref value
                } else {
                    document.getElementById('file').value = '';
                    alert("Please choose valid file!");
                }
            }
        }
    }

    const uploadFile = () => {
        if (!file.value) return;

        //uploading progress
        const config = {
            onUploadProgress: (progressEvent) => {
                const {loaded, total} = progressEvent;
                let percent = Math.floor((loaded * 100) / total)

                if (percent <= 100) {
                    progress.value = percent + '%'
                }
            },
        }
        let formData = new FormData();
        formData.append('file', file.value);

        uploading.value = true;
        responseData.value = '';

        axios.post('/event/bulk-upload', formData, config)
            .then(res => {
                toast[res.data.status](res.data.msg);
                uploading.value = false;
                progress.value = '';
                responseData.value = (res.data.data);
            })
            .catch(err => {
                err.handleGlobally && err.handleGlobally();
                uploading.value = false;
            })
    }
</script>
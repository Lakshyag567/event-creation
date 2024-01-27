import axios from 'axios';
import {useToast} from 'vue-toast-notification';

const toast = useToast();

const errorComposer = (err) => {
    return () => {
        let errors = err?.response?.data?.errors ?? {};
        let message = err?.response?.data?.message ?? "";

        if (errors && Object.keys(errors).length > 0) {
            //validation errors
            let errorMsg = `<ul>`
            Object.keys(errors).forEach((key, index) => {
                errorMsg += `<li>${errors[key][0]}</li>`
            })
            errorMsg += `</ul>`
            toast.error(errorMsg); //multiple errors

        } else if (message) {
            toast.error(message); //single error msg
        } else {
            toast.error(err.toString()) //req. failed msg
        }
    }
}


axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.baseURL = window.location.origin + "/api";
axios.interceptors.response.use(undefined, function (error) {
    // errorComposer will compose a handleGlobally function
    error.handleGlobally = errorComposer(error);
    return Promise.reject(error);
})
window.axios = axios;
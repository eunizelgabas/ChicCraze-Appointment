<script setup>
import Sidebar from '@/Layouts/Sidebar.vue'
import Modal from  '@/Components/Modal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
// import Pagination from '@/Components/Pagination.vue'
import Card from '@/Components/Card.vue'
import { ref, watch, onMounted, computed } from 'vue';
import { useForm, Link, Head,router } from '@inertiajs/vue3'
import PrimaryButton from '@/Components/PrimaryButton.vue';
 import { inject } from 'vue';
const toggleDarkMode = inject('isDarkMode');

let showConfirm = ref(false)
let showEdit = ref(false)
let showAdd = ref(false)
let selectedServiceForDelete = null
let selectedService = null
let deleteForm = useForm({});

let form = useForm({
    name: '',
    image: '',
    description: '',
})

let props = defineProps({
    services: Array,
    filters: Object,
    serviceCount: Number
})

const servicePic = ref(props.services);

        // Method to get the image URL
        const getPicUrl = (image) => `/services/${image}`;

        // On mounted, update the ref if the prop changes
        onMounted(() => {
            servicePic.value = props.services;
        });


        function extractFilename(storageLink) {
      const parts = storageLink.split('/');
      const filenameWithExtension = parts[parts.length - 1];
      const filenameParts = filenameWithExtension.split('.');
      return filenameParts[0]; // Only get the filename without extension
    }
function add() {
    showAdd.value = true;
    form.name = '';
    form.image = '';
    form.description = ''
}

function edit(serv) {
    selectedService = serv
    showEdit.value = true;
    form.name = serv.name
    form.image = serv.image
    form.description = serv.description
}

function closeModal(){
    showConfirm.value = false;
}

function close(){
    showEdit.value = false;
}

function close2(){
    showAdd.value = false;
}

function remove(serv) {
    selectedServiceForDelete = serv
    showConfirm.value = true;
}

function deleteServ(){
    deleteForm.delete('/service/' + selectedServiceForDelete.id)
    showConfirm.value = false;
}

const submit = () =>{
    form.post('/service')
    form.name = "";
    form.image = '';
    form.description = "";
    close2();
}

const update = () =>{
    form.put('/service/' + selectedService.id)
    form.name = "";
    form.image = " ";
    form.description = "";
     console.log(form.image);
    close();
}

let search = ref(props.filters.search);
watch(search, (value) => {
    router.get(
        "/service",
        { search: value },
        {
            preserveState: true,
            replace: true,
        }
    );
});

 const image = ref(null);
    const fileError = ref(null);

    const handleFileChange = (event) => {
      const file = event.target.files[0];
      form.image = file;
      const reader = new FileReader();
      reader.onload = (e) => (image.value = e.target.result);
      reader.readAsDataURL(file);
    }
    const fileHandler = (event) => {
      const file = event.target.files[0];
      image.value = URL.createObjectURL(file);

      // Perform your custom validation here
      const allowedExtensions = ["png", "jpg", "jpeg", "pdf"];
      const maxFileSize = 10 * 1024 * 1024; // 10MB

      if (!allowedExtensions.includes(file.name.split('.').pop().toLowerCase())) {
        fileError.value = "Invalid file format. Please choose a PNG, JPG, or PDF file.";
      } else if (file.size > maxFileSize) {
        fileError.value = "File size exceeds the maximum limit of 10MB.";
      } else {
        fileError.value = null;
      }
    };

onMounted(() => {
// Set a timeout to hide the success flash message after 3 seconds
const successFlashMessage = document.getElementById('flash-success-message');
if (successFlashMessage) {
setTimeout(() => {
    successFlashMessage.style.display = 'none';
}, 3000);
}

// Set a timeout to hide the error flash message after 3 seconds
const errorFlashMessage = document.getElementById('flash-error-message');
if (errorFlashMessage) {
setTimeout(() => {
    errorFlashMessage.style.display = 'none';
}, 3000);
}
});
</script>

<template>
    <Sidebar>
        <Head title="Medical Service"/>

        <section class="container px-4 py-6 mx-auto">
            <div v-if="$page.props.flash.success" id="flash-success-message" class="absolute top-20 right-1 p-4 bg-green-300 border border-gray-300 rounded-md shadow-md">
                {{ $page.props.flash.success }}
                <div class="progress-bar success"></div>
            </div>
            <div v-if="$page.props.flash.error" id="flash-error-message" class=" absolute top-20 right-1 p-4 bg-red-300 border border-gray-300 rounded-md shadow-md">
                {{ $page.props.flash.error }}
                <div class="progress-bar error"></div>
            </div>
            <div class="sm:flex sm:items-center sm:justify-between" :class="themeMode">
                <div>
                    <div class="flex items-center gap-x-3">
                        <h2 class="text-3xl font-bold ">ChicCraze Service</h2>

                        <span class="px-3 py-1 text-xs text-blue-600 bg-blue-100 rounded-full">{{ serviceCount }} service</span>
                    </div>
                </div>
            </div>


            <div class="mt-6 md:flex md:items-center md:justify-between md:w-full">
                <div class="inline-flex overflow-hidden">
                    <div class="py-3 px-4">
                        <div class="relative max-w-xs">
                            <label for="hs-table-search" class="sr-only">Search</label>
                            <input type="search" v-model="search"  name="hs-table-search" id="hs-table-search" class="py-2 px-3 ps-9 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:z-10 focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none " placeholder="Search service">
                            <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-3">
                                <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative flex items-center  md:mt-0" >

                    <div class="flex items-center  gap-x-3">
                        <button @click="add()" v-if="$page.props.auth.permissions.includes('manage-service')" class="flex items-center justify-center w-1/2 px-5 py-2 text-sm tracking-wide text-white transition-colors duration-200 bg-blue-500 rounded-lg shrink-0 sm:w-auto gap-x-2 hover:bg-blue-600 dark:hover:bg-blue-500 dark:bg-blue-600">
                            <i class="fa-solid fa-plus"></i>
                            <span class="">Add Medical Service</span>
                        </button>
                        <Modal :show="showAdd" @close="close2">
                            <div class="p-4 sm:p-10 overflow-y-auto">
                                <div class="flex items-center justify-center ">
                                    <div class="mx-auto w-full max-w-[550px] bg-white">
                                        <form @submit.prevent = "submit">
                                            <div class="mb-5 pt-3">
                                                <label class="mb-5 block text-base font-semibold text-[#07074D] sm:text-xl">
                                                    Add Medical Service Details
                                                </label>
                                                <div class="-mx-3 flex flex-wrap">
                                                    <div class="w-full px-3">
                                                        <div class="mb-3">
                                                            <label for="name" class="text-left">Name</label>
                                                            <input type="text" v-model="form.name" name="name" id="name" placeholder=""
                                                                class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                                                        </div>
                                                        <label for="name" class="text-left">Name</label>
                                                        <div class="w-full relative border-2 mb-2 border-gray-300 border-dashed rounded-lg p-6" id="dropzone">
                                                            <input type="file" @change="handleFileChange" class="absolute inset-0 w-full h-full opacity-0 z-50" />
                                                                <div class="text-center">

                                                                    <img v-if="image" id="image" class=" mx-auto w-[100px] h-[100px] " :src="image" />

                                                                    <img v-else class="mx-auto h-12 w-12" src="https://www.svgrepo.com/show/357902/image-upload.svg" alt="">
                                                                    <h3  class="mt-2 text-sm font-medium text-gray-900">
                                                                        <label for="file-upload" class="relative cursor-pointer">
                                                                            <span>Drag and drop</span>
                                                                            <span class="text-indigo-600"> or browse </span>
                                                                            <span>to upload</span>
                                                                            <input id="file-upload" @change="handleFileChange"  name="file-upload" type="file" class="sr-only">
                                                                        </label>
                                                                    </h3>
                                                                    <p  class="mt-1 text-xs text-gray-500">
                                                                        PNG, JPG, PDF up to 10MB
                                                                    </p>
                                                                </div>
                                                                <p v-if="form.errors.image" class="text-red-500 mt-2">{{ form.errors.image }}</p>
                                                        </div>
                                                        <div class="mb-5">
                                                            <label for="description" class="text-left">Description</label>
                                                            <input type="text" v-model="form.description" name="description" id="description" placeholder=""
                                                                class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-6 flex justify-end gap-x-4">
                                                <SecondaryButton @click="close2">Cancel</SecondaryButton>
                                                <PrimaryButton type="submit" @click="submit">Save</PrimaryButton>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </Modal>
                    </div>
                </div>
            </div>

            <div class="w-full px-2 mt-3" :class="themeMode">
                <div class="h-12">
                    <div class=" overflow-hidden shadow-sm sm:rounded-lg">
                            <Card>

                            <template #head>
                                <div class="flex flex-wrap justify-center -mx-4">
                            <div class="w-full sm:w-1/2 md:w-1/3 lg:w-1/4 xl:w-1/4 px-4 mb-8  mr-5 ml-5 border border-gray-300 rounded-md" v-for="serv in services.data" :key="serv.id">

                            <div class="p-6" >
                               <div class="relative mx-4 mt-4 overflow-hidden rounded-xl object-cover h-[200px] bg-blue-gray-500 bg-clip-border text-white shadow-lg shadow-blue-gray-500/40">
                                    <img
                                        :src="getPicUrl(serv.image)"
                                        alt="service image"
                                        class="w-full h-full object-cover"
                                    />
                                    <div class="to-bg-black-10 absolute inset-0 h-full w-full bg-gradient-to-tr from-transparent via-transparent to-black/60"></div>
                                    <button
                                        class="!absolute top-4 right-4 h-8 max-h-[32px] w-8 max-w-[32px] select-none rounded-full text-center align-middle font-sans text-xs font-medium uppercase text-red-500 transition-all hover:bg-red-500/10 active:bg-red-500/30 disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                                        type="button"
                                        data-ripple-dark="true"
                                    >
                                        <span class="absolute top-1/2 left-1/2 -translate-y-1/2 -translate-x-1/2 transform">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 24 24"
                                                fill="currentColor"
                                                aria-hidden="true"
                                                class="h-6 w-6"
                                            >
                                                <path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z"></path>
                                            </svg>
                                        </span>
                                    </button>
                                </div>

                                <div class="mb-3 flex items-center justify-between mt-5">
                                <h5 class="block font-sans text-xl font-medium leading-snug tracking-normal  antialiased">
                                    {{ serv.name }}
                                </h5>
                                <p class="flex items-center gap-1.5 font-sans text-base font-normal leading-relaxed  antialiased">
                                    <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="currentColor"
                                    aria-hidden="true"
                                    class="-mt-0.5 h-5 w-5 text-yellow-700"
                                    >
                                    <path
                                        fill-rule="evenodd"
                                        d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"
                                        clip-rule="evenodd"
                                    ></path>
                                    </svg>
                                    5.0
                                </p>
                                </div>
                                <p class="block font-sans text-base font-light leading-relaxed  antialiased">
                                  {{ serv.description }}
                                </p>
                                <div class="group mt-8 flex flex-wrap items-center justify-center gap-3" v-if="isAdmin">

                                <a href="#"  @click="edit(serv)" title="Edit Service"
                                    data-tooltip-target="wifi"
                                    class="btn cursor-pointer rounded-full border border-[#EADDDD] bg-[#FFD6A7] p-3 text-[#8C5454] transition-colors hover:border-[#5B3929] hover:bg-[#F7CEA1] hover:!opacity-100 group-hover:opacity-70"
                                >
                                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>

                                </a>
                                <Modal :show="showEdit" @close="close">
                                    <div class="p-4 sm:p-10 overflow-y-auto">
                                        <div class="flex items-center justify-center ">
                                            <div class="mx-auto w-full max-w-[550px] bg-white">
                                                <form @submit.prevent = "update">
                                                    <div class="mb-5 pt-3">
                                                        <label class="mb-5 block text-base font-semibold text-[#07074D] sm:text-xl">
                                                        Edit  Medical Service Details
                                                        </label>
                                                        <div class="-mx-3 flex flex-wrap">
                                                            <div class="w-full px-3">
                                                                <div class="mb-5">
                                                                    <label for="name" class="text-left">Name</label>
                                                                    <input type="text" v-model="form.name" name="name" id="name" placeholder=""
                                                                        class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                                                                </div>
                                                                <label for="name" class="text-left">Name</label>
                                                                <div class="w-full relative border-2 mb-2 border-gray-300 border-dashed rounded-lg p-6" id="dropzone">
                                                                    <input type="file" @change="handleFileChange" class="absolute inset-0 w-full h-full opacity-0 z-50" />
                                                                        <div class="text-center">

                                                                            <img v-if="image" id="image" class=" mx-auto w-[100px] h-[100px] " :src="image" />

                                                                            <img v-else class="mx-auto h-12 w-12" src="https://www.svgrepo.com/show/357902/image-upload.svg" alt="">
                                                                            <h3  class="mt-2 text-sm font-medium text-gray-900">
                                                                                <label for="file-upload" class="relative cursor-pointer">
                                                                                    <span>Drag and drop</span>
                                                                                    <span class="text-indigo-600"> or browse </span>
                                                                                    <span>to upload</span>
                                                                                    <input id="file-upload" @change="handleFileChange"  name="file-upload" type="file" class="sr-only">
                                                                                </label>
                                                                            </h3>
                                                                            <p  class="mt-1 text-xs text-gray-500">
                                                                                PNG, JPG, PDF up to 10MB
                                                                            </p>
                                                                        </div>
                                                                        <p v-if="form.errors.image" class="text-red-500 mt-2">{{ form.errors.image }}</p>
                                                                </div>
                                                                <div class="mb-5">
                                                                    <label for="description" class="text-left">Description</label>
                                                                    <input type="text" v-model="form.description" name="description" id="description" placeholder=""
                                                                        class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mt-6 flex justify-end gap-x-4">
                                                        <SecondaryButton @click="close">Cancel</SecondaryButton>
                                                        <PrimaryButton type="submit" @click="update">Save</PrimaryButton>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </Modal>

                                <a  href="#" @click="remove(serv)"  title="Delete Service"
                                    data-tooltip-target="tv"
                                     class="btn cursor-pointer rounded-full border border-[#EADDDD] bg-[#FFD6A7] p-3 text-[#8C5454] transition-colors hover:border-[#5B3929] hover:bg-[#F7CEA1] hover:!opacity-100 group-hover:opacity-70"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>

                                </a>
                                <Modal :show="showConfirm" @close="closeModal">
                                    <div class="p-4 sm:p-10 text-center overflow-y-auto">

                                        <span class="mb-4 inline-flex justify-center items-center w-[62px] h-[62px] rounded-full border-4 border-red-50 bg-red-100 text-red-500">
                                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"></path>
                                            </svg>
                                        </span>


                                        <h3 class="mb-2 text-2xl font-bold text-gray-800">
                                            Delete Medical Service
                                        </h3>
                                        <p class="text-gray-500">
                                            Are you sure you want like to delete this Medical Service?
                                        </p>

                                        <div class="mt-6 flex justify-center gap-x-4">
                                            <SecondaryButton @click="closeModal">Cancel</SecondaryButton>
                                            <DangerButton @click="deleteServ()">Delete</DangerButton>
                                        </div>
                                    </div>
                                </Modal>


                                </div>
                            </div>
                            <div class="p-6 pt-3">
                                <Link :href="('/appointment/create/' + serv.id)"
                                class="block w-full select-none rounded-lg bg-[#8C5454] py-3.5 px-7 text-center align-middle font-sans text-sm font-bold uppercase text-white shadow-md shadow-[#FFD6A7] transition-all hover:shadow-lg hover:shadow-[#F7CEA1] focus:opacity-[0.85] focus:shadow-none active:opacity-[0.85] active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                                type="button"
                                data-ripple-light="true"
                                >
                                Book Appointment
                                </Link>
                            </div>
                            </div>
                            </div>
                            </template>
                        </Card>
                    </div>

                </div>
            </div>

        </section>

    </Sidebar>
</template>

<style scoped>

#flash-success-message {
    animation: fadeOut 6s ease-in-out forwards;
}

.progress-bar {
    height: 5px;
    width: 100%;
    background-color: #4CAF50; /* Green color */
    animation: progressBar 3s linear;
}
#flash-error-message {
    animation: fadeOut 7s ease-in-out forwards;
}

.success .progress-bar {

    animation: progressBar 5s linear;
}
.error .progress-bar {
    background-color: #FF5733; /* Red color */
    animation: progressBar 5s linear;
}

@keyframes fadeOut {
    from {
        opacity: 1;
    }
    to {
        opacity: 0;
    }
}

@keyframes progressBar {
    0% {
        width: 100%;
    }
    100% {
        width: 0;
    }
}
</style>

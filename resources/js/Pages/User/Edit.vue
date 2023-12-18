<script setup>
import Sidebar from '@/Layouts/Sidebar.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
// import Pagination from '@/Components/Pagination.vue';
import { Link,  Head, useForm, router } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import { ref, computed } from 'vue';
import { watch } from 'vue';
import { onMounted } from 'vue';


let props = defineProps({
    user:Object,
    roles:Object,

    currentRole: Object,

    })
 let form = useForm({
        name: props.user.name,
        email:props.user.email,
        type: props.user.type,
        role: props.currentRole,
        password: props.user.password,
        password_confirmation:props.user.password,

    })
const submit = () =>{
        form.post('/users')
    }
</script>

<template>
    <Sidebar>
        <Head title="Users"/>

        <section class="container px-4 py-6 mx-auto">
            <div v-if="$page.props.flash.success" id="flash-success-message" class="absolute top-20 right-1 p-4 bg-green-300 border border-gray-300 rounded-md shadow-md">
                {{ $page.props.flash.success }}
                <div class="progress-bar success"></div>
            </div>
            <div v-if="$page.props.flash.error" id="flash-error-message" class=" absolute top-20 right-1 p-4 bg-red-300 border border-gray-300 rounded-md shadow-md">
                {{ $page.props.flash.error }}
                <div class="progress-bar error"></div>
            </div>

            <!-- component -->
<!-- This is an example component -->
<div class="max-w-2xl mx-auto bg-white p-16">
    <div class="block  font-semibold text-xl self-start text-gray-700 mb-5">
        <h1 class="leading-relaxed">User Details Form</h1>
        <hr>
    </div>
	<form @submit.prevent="submit">


    <div class="mb-6">
        <label for="name" class="block mb-2 text-sm font-medium text-gray-900 ">Name</label>
        <input type="text" id="name" v-model="form.name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 " placeholder="John Doe" required>
    </div>
     <div class="mb-6">
        <label for="email" class="block mb-2 text-sm font-medium text-gray-900 ">Email address</label>
        <input type="email" id="email" v-model="form.email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 " placeholder="john.doe@company.com" required>
    </div>
    <div class="mb-6">
        <label for="roles" class="block mb-2 text-sm font-medium text-gray-900 ">Roles</label>
        <div class="mt-2">
        <select id="role" v-model="form.role" name="role" autocomplete="role" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            <option selected disabled   >Select Role</option>
            <option v-for="role in roles" :key="role.id" :value="role.name">{{ role.name }}</option>
            <!-- <option value="Role">Patient</option> -->
        </select>
        <div class="text-sm text-red-500 italic" v-if="form.errors.role">{{ form.errors.role }}</div>
        </div>
    </div>
    <div class="mb-6">
        <label for="roles" class="block mb-2 text-sm font-medium text-gray-900 ">Type</label>
        <div class="mt-2">
        <select id="role" v-model="form.type" name="role" autocomplete="role" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            <option selected disabled>Select Type</option>
           <option value="Admin">Admin</option>
            <option value="Standard">Standard</option>
        </select>
        <div class="text-sm text-red-500 italic" v-if="form.errors.role">{{ form.errors.role }}</div>
        </div>
    </div>
    <div class="mb-6">
        <label for="password" class="block mb-2 text-sm font-medium text-gray-900 ">Password</label>
        <input type="password" id="password" v-model="form.password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 " placeholder="•••••••••">
    </div>
    <div class="mb-6">
        <label for="confirm_password" class="block mb-2 text-sm font-medium text-gray-900 ">Confirm password</label>
        <input type="password" id="confirm_password" v-model="form.password_confirmation" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 " placeholder="•••••••••">
    </div>


    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center ">Submit</button>
</form>


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

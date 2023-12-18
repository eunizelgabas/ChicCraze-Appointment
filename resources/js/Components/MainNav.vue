<script setup>
import { ref, defineProps, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

    defineProps({
        collapse:Boolean,

    })
    let show = ref(false);

    const isOpen = () => {show.value = !show.value};

    const closeDropdown = () => {
        show.value = true;
    };

    let show2 = ref(false);

    const isOpen2 = () => {show2.value = !show2.value};

    const closeDropdown2 = () => {
        show2.value = true;
    };

</script>
<template>
    <div>
        <div class="overflow-y-auto overflow-x-hidden flex-grow">
            <ul class="flex flex-col py-2 space-y-1">
                <li class="px-5">
                    <div class="flex flex-row items-center h-6">
                        <div class="text-m font-light tracking-wide text-white" v-show="!collapse">
                            <p class="font-semibold tracking-wide text-m text-white leading-tight">
                                <span v-if="$page.props.auth.user.type  === 'Admin'">Administrator</span>
                                <span v-else-if="$page.props.auth.user.type === 'Doctor'">Standard User</span>

                            </p>
                        </div>
                    </div>
                </li>
                <hr class="text-white mt-5">
                <li :class="{ 'bg-blue-100 font-bold text-gray-800  border-transparent ': route().current('dashboard') }">
                    <Link href="/dashboard" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-blue-100  hover:text-gray-800 border-l-4 border-transparent hover:border-indigo-900 pr-6">
                        <span class="inline-flex justify-center items-center ml-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        </span>
                        <span class="ml-2 text-m tracking-wide truncate" v-show="!collapse">Dashboard</span>
                    </Link>
                </li>
                <li :class="{'bg-blue-100 text-gray-800 font-bold border-transparent border-indigo-900': route().current('service.index') } " v-if="$page.props.auth.permissions.includes('manage-service')" >
                    <Link :href="route('service.index')" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-blue-100  hover:text-gray-800 border-l-4 border-transparent hover:border-indigo-900 pr-6">
                        <span class="inline-flex justify-center items-center ml-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        </span>
                        <span class="ml-2 text-m tracking-wide truncate" v-show="!collapse">Service</span>
                    </Link>
                </li>
                <li :class="{'bg-blue-100 text-gray-800 font-bold border-transparent border-indigo-900': route().current('appointment.index') } "  v-if="$page.props.auth.permissions.includes('manage-appointment')">
                    <Link :href="route('appointment.index')" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-blue-100  hover:text-gray-800 border-l-4 border-transparent hover:border-indigo-900 pr-6">
                        <span class="inline-flex justify-center items-center ml-4">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" />
                            </svg>

                        </span>
                        <span class="ml-2 text-m tracking-wide truncate" v-show="!collapse">Appointment</span>
                    </Link>
                </li>

                 <li  :class="{'bg-blue-100 text-gray-800 font-bold border-transparent border-indigo-900': route().current('') } " v-if="$page.props.auth.permissions.includes('')">
                    <Link  href="#" class=" relative flex flex-row items-center h-11 focus:outline-none hover:bg-blue-100  hover:text-gray-800 border-l-4 border-transparent hover:border-indigo-900 pr-6">
                        <span class="inline-flex justify-center items-center ml-4">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                             <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </span>
                        <span class="ml-2 text-m tracking-wide truncate" v-show="!collapse">Calendar</span>
                    </Link>
                </li>
                <li :class="{'bg-blue-100 font-bold text-gray-800  border-transparent border-indigo-900': route().current('user.index') }" v-if="$page.props.auth.permissions.includes('manage-user')">
                    <Link :href="route('user.index')" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-blue-100  hover:text-gray-800 border-l-4 border-transparent hover:border-indigo-900 pr-6">
                        <span class="inline-flex justify-center items-center ml-4">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                            </svg>
                        </span>
                        <span class="ml-2 text-m tracking-wide truncate" v-show="!collapse">Manage User</span>
                    </Link>
                </li>
            </ul>
        </div>
    </div>
</template>

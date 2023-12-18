<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Sidebar from '@/Layouts/Sidebar.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import moment from 'moment';
import { watch } from 'vue';
import { ref, onMounted, computed } from 'vue';
import { Calendar, DatePicker } from 'v-calendar';
import 'v-calendar/style.css';


let props = defineProps({
    appointments: Array,
    user: Object,
    filters:Object,
    totalAppointments:Number,
    percentageChange:Number,
    cancelledAppointmentsByMonth: Number,
    upcomingAppointmentsByMonth: Number,
})

    function formattedDate(date){
        return moment(date).format('MMMM   D, YYYY');
    }

    const formatTimeToAMPM = (time) => {
        const [hours, minutes] = time.split(':').map(Number);
        const period = hours >= 12 ? 'PM' : 'AM';
        const formattedHours = hours % 12 || 12;
        const formattedMinutes = minutes.toString().padStart(2, '0');
        return `${formattedHours}:${formattedMinutes} ${period}`;
    };

    const currentTime = ref('');

    onMounted(() => {
    // Get the current time
    const now = new Date();
    const hours = now.getHours();
    const minutes = now.getMinutes();
    currentTime.value = `${hours}:${minutes < 10 ? '0' : ''}${minutes}`;
    });

    const greeting = computed(() => {
    const hour = currentTime.value ? parseInt(currentTime.value.split(':')[0]) : 0;

    if (hour >= 5 && hour < 12) {
        return 'Good Morning';
    } else if (hour >= 12 && hour < 17) {
        return 'Good Afternoon';
    } else {
        return 'Good Evening';
    }
    });

    let search = ref(props.filters.search);
    watch(search, (value) => {
        router.get(
            "/dashboard",
            { search: value },
            {
                preserveState: true,
                replace: true,
            }
        );
    });

const attrs = ref([
  ...props.appointments.map(appointment => {
    const appointmentDate = new Date(appointment.date);
    console.log('Raw appointment time:', appointment.time);

    const appointmentTime = appointment.time.split(':');
    const formattedTime = new Date(appointmentDate.getFullYear(), appointmentDate.getMonth(), appointmentDate.getDate(), parseInt(appointmentTime[0], 10), parseInt(appointmentTime[1], 10)).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

    console.log('Popover label:', appointment.popoverLabel);

    let highlightColor = '';
    switch (appointment.status) {
      case 'Pending':
        highlightColor = 'blue';
        break;
      case 'Accepted':
        highlightColor = 'green';
        break;
      case 'Cancelled':
        highlightColor = 'red';
        break;
      default:
        highlightColor = 'gray'; // You can set a default color for other statuses
    }


    return {
    key: `appointment-${appointment.id}`,
      highlight: highlightColor,
      style: 'background-color: red; color: white;',
      dates: appointmentDate,
      popover: {
        // label: `${appointment.patient.firstname} ${appointment.patient.lastname}\n${formattedTime}`, // Display patient's name and formatted time in the popover
        label: appointment.popoverLabel || '',
        visibility: 'hover', // Show popover on hover
      },
    };
  }),
]);



    const formattedAppointments = computed(() => {
      return props.appointments.map(appointment => {
        return {
          start: (appointment.date, appointment.time),
          title: `${appointment.patient.firstname} - ${appointment.doctor}`,
          // Add other properties as needed based on your appointment structure
        };
      });
    });

</script>

<template>
    <Head title="Dashboard" />

    <Sidebar>
        <div class="flex flex-col flex-1 w-full overflow-y-auto">

          <main class="relative z-0 flex-1 pb-8 px-6 ">
              <div class="grid pb-10  mt-4 ">
                    <div class="grid grid-cols-2 gap-2 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-2 mt-3">

                        <div class="col-span-1">
                            <div class="relative w-full h-52 bg-cover bg-center group rounded-lg overflow-hidden shadow-lg transition duration-300 ease-in-out"
                                style="background-image: url('https://images-wixmp-ed30a86b8c4ca887773594c2.wixmp.com/f/f868ecef-4b4a-4ddf-8239-83b2568b3a6b/de7hhu3-3eae646a-9b2e-4e42-84a4-532bff43f397.jpg?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJ1cm46YXBwOjdlMGQxODg5ODIyNjQzNzNhNWYwZDQxNWVhMGQyNmUwIiwiaXNzIjoidXJuOmFwcDo3ZTBkMTg4OTgyMjY0MzczYTVmMGQ0MTVlYTBkMjZlMCIsIm9iaiI6W1t7InBhdGgiOiJcL2ZcL2Y4NjhlY2VmLTRiNGEtNGRkZi04MjM5LTgzYjI1NjhiM2E2YlwvZGU3aGh1My0zZWFlNjQ2YS05YjJlLTRlNDItODRhNC01MzJiZmY0M2YzOTcuanBnIn1dXSwiYXVkIjpbInVybjpzZXJ2aWNlOmZpbGUuZG93bmxvYWQiXX0.R0h-BS0osJSrsb1iws4-KE43bUXHMFvu5PvNfoaoi8o');">
                                <div class="absolute inset-0 bg-blue-500 bg-opacity-75 transition duration-300 ease-in-out"></div>
                                    <div class="relative w-full h-full px-4 sm:px-6 lg:px-4 flex items-center">
                                    <div>
                                        <div class="text-white text-lg flex space-x-2 items-center">
                                            <div class="bg-white rounded-md p-2 flex items-center">
                                                <i class="fas fa-clipboard-check fa-sm text-blue-800"></i>
                                            </div>
                                            <p class="font-bold">Total Appointment</p>
                                        </div>
                                        <h3 class="text-white text-3xl mt-2 font-bold">
                                            {{ totalAppointments }}
                                        </h3>
                                        <h3 class="text-white text-lg mt-2 ">
                                           {{ percentageChange }}% <span class='font-semibold text-blue-200'>vs last month</span>
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-span-1">
                            <div class="relative w-full h-52 bg-cover bg-center group rounded-lg overflow-hidden shadow-lg transition duration-300 ease-in-out"
                            >

                                    <div class="absolute inset-0 bg-white bg-opacity-20 transition duration-300 ease-in-out"></div>
                                    <div class="relative w-full h-full px-4 sm:px-6 lg:px-4 flex items-center">
                                    <div>
                                        <div class="text-black text-lg flex space-x-2 items-center">
                                        <div class="bg-white rounded-md p-2 flex items-center">
                                            <i class="fas fa-toggle-off fa-sm text-blue-300"></i>
                                        </div>
                                        <p class="font-bold">Appointment Cancelled</p>
                                        </div>
                                        <h3 class="text-black text-3xl mt-2 font-bold">
                                           {{cancelledAppointmentsByMonth}}
                                        </h3>
                                        <h3 class="text-lg mt-2 text-black font-semibold ">
                                           {{ upcomingAppointmentsByMonth }} not confirmed
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
              </div>
              <div class="pt-8 px-2" >
                <div class="w-full grid grid-cols-2 xl:grid-cols-3 2xl:grid-cols-3 gap-4">
                   <div class="bg-white shadow rounded-lg p-4 sm:p-6 xl:p-8 xl:col-span-2 2xl:col-span-2">
                        <div class="flex justify-between ">
                            <h3 class="flex items-start justify-center m-2 ml-0 font-medium text-xl/tight text-dark">
                                <span class="mr-3 text-dark font-bold">Recent Appointments</span>
                            </h3>
                            <Link :href="route('appointment.index')" class=" inline-block text-[.925rem] font-medium leading-normal text-right align-right cursor-pointer rounded-2xl transition-colors duration-150 ease-in-out text-blue-400 bg-light-dark border-light shadow-none border-0 py-2 px-5 sm:self-center">
                                See More
                            </Link>
                        </div>
                        <div class="flex flex-col">
                            <div class="-m-1.5 overflow-x-auto">
                                <div class="p-1.5 min-w-full inline-block align-middle">
                                <div class=" rounded-lg divide-y divide-gray-200 ">
                                    <!-- <div class="py-3 px-4">
                                        <div class="relative max-w-xs">
                                            <label for="hs-table-search" class="sr-only">Search</label>
                                            <input type="search"  v-model="search" name="hs-table-search" id="hs-table-search" class="py-2 px-3 ps-9 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:z-10 focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none " placeholder="Search for appointment">
                                            <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-3">
                                            <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                                            </div>
                                        </div>
                                    </div> -->
                                    <div class="overflow-hidden">
                                    <table class="min-w-full divide-y divide-gray-200 ">
                                        <thead class="bg-gray-50 ">
                                            <tr>

                                            <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Client</th>
                                            <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Date and Time</th>
                                            <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Service</th>
                                            <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Status</th>
                                            <!-- <th scope="col" class="px-6 py-3 text-end text-xs font-medium text-gray-500 uppercase">Action</th> -->
                                            </tr>
                                        </thead>
                                            <tbody class="divide-y divide-gray-200">

                                                <tr v-for="app in appointments" :key="app.id">
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 ">{{ app.user.name }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 ">{{ formattedDate(app.date) }} at {{ formatTimeToAMPM(app.time) }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 ">{{ app.service.name }}</td>
                                                    <td class=" text-xs whitespace-nowrap  uppercase px-6 py-4  text-center font-bold " :class="{
                                                                        'text-blue-600': app.status == 'Pending',
                                                                        'text-red-600':app.status == 'Cancelled',
                                                                        'text-green-600': app.status =='Accepted',
                                                                    }">{{ app.status}}</td>
                                                    <!-- <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                                    <button type="button" class="inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent text-blue-600 hover:text-blue-800 disabled:opacity-50 disabled:pointer-events-none dark:text-blue-500 dark:hover:text-blue-400 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">Delete</button>
                                                    </td> -->
                                                </tr>
                                             </tbody>
                                        </table>
                                    </div>
                                </div>
                                </div>
                            </div>
                        </div>

                    </div>

                        <Calendar :attributes="attrs" class="my-custom-calendar py-10 " expanded />


                    </div>


            </div>
            <!-- <div class="pt-8 px-2" >
                <div class="w-full grid grid-cols-1 xl:grid-cols-1 2xl:grid-cols-3 gap-4">
                   <div class="bg-white shadow rounded-lg p-4 sm:p-6 xl:p-8 2xl:col-span-2">

                   </div>
                    <div class="bg-white shadow rounded-lg p-4 sm:p-6 xl:p-8 2xl:col-span-1">

                    </div>
                </div>
            </div> -->
          </main>

        </div>


    </Sidebar>
</template>
<style scoped>
.my-custom-calendar {
    width: 100% ;
    height: 100%;

}

/* #app {
  background: #fff;
  border-radius: 4px;
  padding: 20px;
  transition: all 0.2s;
  text-align: center;
} */
</style>

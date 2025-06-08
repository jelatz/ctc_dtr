<template>
    <Head title=" - Home" />
    <div :class="['flex h-full w-full items-center justify-center']">
        <!-- Form -->
        <div
            class="z-50 w-96 xl:absolute xl:top-1/2 xl:left-14 xl:-mt-5 xl:ml-[5%] xl:inline-block xl:-translate-y-1/2 2xl:left-24"
            :data-aos="'fade-right'"
        >
            <p class="clock mb-2 text-center text-4xl">{{ currentTime }}</p>
            <p class="mb-10 text-center">{{ currentDateTime }}</p>
            <form @submit.prevent="submitForm">
                <label for="employeeID" class="mb-1 block">DTR ID:</label>
                <input
                    v-model.trim="formData.employeeID"
                    type="text"
                    id="employeeID"
                    placeholder="Enter Employee ID"
                    :class="[
                        'h-10 w-full rounded-lg border bg-slate-50 px-5',
                        showError ? 'border-red-600' : 'border-gray-300',
                    ]"
                />
                <small v-if="showError" class="text-red-600">{{
                    errorMessage
                }}</small>
                <button
                    class="mx-auto mt-5 w-full cursor-pointer bg-[#fbc04a] py-1 hover:bg-[#fbc04ad4]"
                    :disabled="formData.processing"
                >
                    Submit
                </button>
            </form>
        </div>
    </div>
    <!-- Left Side (Swiper) -->
    <div
        class="absolute top-[40%] right-0 z-50 hidden w-1/2 xl:block"
        :data-aos="'fade-left'"
    >
        <Swiper
            :modules="[Autoplay, Navigation, Pagination]"
            :slides-per-view="1"
            :space-between="50"
            :loop="true"
            :autoplay="{
                delay: 3000,
                disableOnInteraction: false,
            }"
            :pagination="{
                clickable: true,
            }"
            :navigation="true"
            class="mySwiper"
        >
            <SwiperSlide v-for="(item, index) in items" :key="index">
                <div class="relative mx-auto w-[70%]">
                    <img
                        :src="item.image"
                        :alt="'Slide ' + (index + 1)"
                        class="h-20 w-20 rounded-full object-cover"
                    />
                    <h3 class="text-xl font-bold">{{ item.title }}</h3>
                    <p>{{ item.description }}</p>
                </div>
            </SwiperSlide>
        </Swiper>
    </div>

    <!-- Modal Component -->
    <Modal :show="showModal" @close="showModal = false" title="title">
        <div class="flex w-[30rem] flex-col items-center justify-center p-5">
            <img
                src=""
                alt="Profile Picture"
                class="h-64 w-full rounded-md bg-slate-600"
            />
            <p class="mt-3 w-full text-left">
                Employee ID:
                <span>{{ employeeData?.employee_id }}</span>
            </p>
            <p class="mt-2 w-full text-left">Position</p>
        </div>
        <div class="w-full p-5">
            <small class="mb-2">Showing 5 history </small>
            <table class="w-full">
                <thead class="bg-gray-200">
                    <tr class="py-2">
                        <th class="w-96 py-2">Schedule</th>
                        <th class="py-2">Login</th>
                        <th class="py-2">Logout</th>
                    </tr>
                </thead>
                <tbody class="nth-[0]-child:bg-gray-100">
                    <tr
                        class="text-center"
                        v-for="(schedule, index) in employeeSched"
                        :key="index"
                    >
                        <td class="py-3">
                            {{ schedule?.sched_start }} -
                            {{ schedule?.sched_end }}
                        </td>
                        <td class="py-3">{{ employeeData?.time_in }}</td>
                        <td class="py-3">{{ employeeData?.time_out }}</td>
                    </tr>
                </tbody>
            </table>
            <form @submit.prevent="confirmDtrSubmit">
                <button
                    type="submit"
                    class="float-end mt-5 w-fit cursor-pointer bg-[#fbc04a] px-10 py-1 hover:bg-[#fbc04ad4]"
                >
                    Confirm DTR
                </button>
            </form>
        </div>
    </Modal>
</template>

<script setup>
import { Swiper, SwiperSlide } from "swiper/vue";
import "swiper/swiper-bundle.css";
import { Autoplay, Navigation, Pagination } from "swiper/modules";
import { ref, onMounted, onBeforeUnmount, computed } from "vue";
import AOS from "aos";
import { useForm, usePage } from "@inertiajs/vue3";
import "aos/dist/aos.css";
import Modal from "@/Components/Modal.vue";
import axios from "axios";

// Employee ID and error handling
const showError = ref(false);
const errorMessage = ref("");
// Passing button value to modal
const showModal = ref(false);

// EmployeeID form
const formData = useForm({
    employeeID: "",
    processing: false,
});

const employeeData = ref({});
const employeeSched = ref([]);

const submitForm = async () => {
    if (!formData.employeeID) {
        showError.value = true;
        errorMessage.value = "Employee ID is required.";
        return;
    }

    showError.value = false;
    errorMessage.value = "";
    formData.processing = true;

    try {
        const response = await axios.post(route("check-employee"), {
            employeeID: formData.employeeID,
        });

        if (response.data.success) {
            employeeData.value = response.data.employeeData.employee;
            employeeSched.value = response.data.employeeData.schedules || [];
            showModal.value = true;
        } else {
            showError.value = true;
            errorMessage.value = response.data.message || "Unknown error.";
        }
    } catch (error) {
        showError.value = true;
        errorMessage.value =
            error.response?.data?.message || "An unexpected error occurred.";
    } finally {
        formData.processing = false;
    }
};

// Current time and date
const currentTime = ref(
    new Date(new Date().getTime() + 5 * 60000).toLocaleTimeString(),
);
const currentDateTime = ref("");

const formatDate = () => {
    const date = new Date(new Date().getTime() + 5 * 60000);
    const days = [
        "Sunday",
        "Monday",
        "Tuesday",
        "Wednesday",
        "Thursday",
        "Friday",
        "Saturday",
    ];
    const months = [
        "January",
        "February",
        "March",
        "April",
        "May",
        "June",
        "July",
        "August",
        "September",
        "October",
        "November",
        "December",
    ];

    const dayName = days[date.getDay()];
    const monthName = months[date.getMonth()];
    const day = date.getDate();
    const year = date.getFullYear();

    currentDateTime.value = `${dayName} - ${monthName} ${day}, ${year}`;
};

// Initialize the date
formatDate();
let clockInterval = null;

onMounted(() => {
    AOS.init({
        duration: 1000, // Animation duration
        once: false, // Animate multiple times on changes
        offset: 100, // Offset before animation starts
    });

    clockInterval = setInterval(() => {
        currentTime.value = new Date(
            new Date().getTime() + 5 * 60000,
        ).toLocaleTimeString();
    }, 1000);
});

onBeforeUnmount(() => {
    if (clockInterval) clearInterval(clockInterval);
});

// Swiper items
const items = [
    {
        image: "/images/slide1.jpg",
        title: "Slide 1 Title",
        description: "Short description for slide 1",
    },
    {
        image: "/images/slide2.jpg",
        title: "Slide 2 Title",
        description: "Short description for slide 2",
    },
    {
        image: "/images/slide3.jpg",
        title: "Slide 3 Title",
        description: "Short description for slide 3",
    },
];
</script>
<style>
/* Carousel style */
.mySwiper {
    width: 40rem;
    height: 300px;
}

/* Navigation buttons and pagination */
:deep(.swiper-button-next),
:deep(.swiper-button-prev),
:deep(.swiper-pagination-bullet-active) {
    color: #000;
}
.swiper-button-prev:after,
.swiper-button-next:after {
    font-size: 1.6rem !important;
    color: #000000 !important;
    font-weight: 600 !important;
}

:deep(.swiper-pagination-bullet-active) {
    background: #000;
}

/* Clock styling */
.clock {
    text-shadow: 0 15px 3px rgba(54, 54, 54, 0.08);
}

/* Modal style fix */
:deep(.modal-overlay) {
    z-index: 9999;
}
</style>

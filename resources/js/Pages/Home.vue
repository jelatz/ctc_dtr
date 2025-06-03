<template>

    <Head title=" - Home" />
    <div :class="[
        'z-50 flex h-screen justify-between pt-80'
    ]">
        <!-- Form -->
        <div class="z-50 w-96 ml-20 mt-5" :data-aos="'fade-right'">
            <p class="clock mb-10 text-center text-4xl">{{ currentTime }}</p>
            <form @submit.prevent="validateForm">
                <label for="employeeID" class="mb-1 block">DTR ID:</label>
                <input v-model.trim="employeeID" type="text" id="employeeID" placeholder="Enter Employee ID" :class="[
                    'h-10 w-full rounded-lg border bg-slate-50 px-5',
                    showError ? 'border-red-600' : 'border-gray-300',
                ]" />
                <small v-if="showError" class="text-red-600">Please enter your Employee ID</small>
                <button type="button"
                    class="mt-5 mx-auto w-full bg-[#fbc04a] py-1 cursor-pointer hover:bg-[#fbc04ad4]">Submit</button>
            </form>
        </div>

        <!-- Left Side (Swiper) -->
        <div class="z-50" :data-aos="'fade-left'">
            <Swiper :modules="[Autoplay, Navigation, Pagination]" :slides-per-view="1" :space-between="50" :loop="true"
                :autoplay="{
                    delay: 3000,
                    disableOnInteraction: false,
                }" :pagination="{
                    clickable: true,
                }" :navigation="true" class="mySwiper">
                <SwiperSlide v-for="(item, index) in items" :key="index">
                    <div class="relative mx-auto w-[70%]">
                        <img :src="item.image" :alt="'Slide ' + (index + 1)"
                            class="h-20 w-20 rounded-full object-cover" />
                        <h3 class="text-xl font-bold">{{ item.title }}</h3>
                        <p>{{ item.description }}</p>
                    </div>
                </SwiperSlide>
            </Swiper>
        </div>
    </div>

    <!-- Modal Component -->
    <Modal :show="showModal" :title="buttonValue" :confirmText="confirmText" @close="showModal = false"
        @confirm="handleConfirm">
        <div class="flex w-[30rem] flex-col items-center justify-center p-5">
            <img src="" alt="Profile Picture" class="h-64 w-full rounded-md bg-slate-600" />
            <p class="mt-5">
                Employee ID: <span>{{ employeeID }}</span>
            </p>
            <p class="mt-5">Position</p>
        </div>
        <div class="w-full p-5">
            <table class="w-full">
                <thead class="bg-gray-200">
                    <tr class="py-2">
                        <th class="py-2">Date</th>
                        <th class="py-2">Login</th>
                        <th class="py-2">Logout</th>
                    </tr>
                </thead>
                <tbody class="nth-[0]-child:bg-gray-100">
                    <tr class="text-center">
                        <td class="py-2">data</td>
                        <td class="py-2">data</td>
                        <td class="py-2">data</td>
                    </tr>
                    <tr class="bg-gray-200 text-center">
                        <td class="py-2">data</td>
                        <td class="py-2">data</td>
                        <td class="py-2">data</td>
                    </tr>
                    <tr class="text-center">
                        <td class="py-2">data</td>
                        <td class="py-2">data</td>
                        <td class="py-2">data</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </Modal>
</template>

<script setup>
import { Swiper, SwiperSlide } from "swiper/vue";
import "swiper/swiper-bundle.css";
import { Autoplay, Navigation, Pagination } from "swiper/modules";
import { ref, onMounted, onUnmounted, onBeforeUnmount, nextTick } from "vue";
import AOS from "aos";
import "aos/dist/aos.css";
import Modal from "@/Components/Modal.vue";

// Employee ID and error handling
const employeeID = ref("");
const showError = ref(false);

// Passing button value to modal
const buttonValue = ref("");
const showModal = ref(false);
const confirmText = ref("");

// Form validation and modal
const openModal = (value) => {
    buttonValue.value = value;
    showModal.value = true;
    confirmText.value = value === "Login" ? "Confirm Login" : "Confirm Logout";
    employeeID.value = employeeID.value;
};
// Form validation(ensure that employeeID is not empty)
const validateForm = () => {
    if (!employeeID.value) {
        showError.value = true;
    } else {
        showError.value = false;
        openModal(showYellow.value ? "Login" : "Logout");
    }
};

const handleConfirm = () => {
    alert(`Employee ID: ${employeeID.value} confirmed!`);
    showModal.value = false;
};

// Toggle login/logout using F1/F2 keys
const currentTime = ref(
    new Date(new Date().getTime() + 5 * 60000).toLocaleTimeString(),
);
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
<style scoped>
/* Carousel style */
.mySwiper {
    width: 50rem;
    height: 300px;
}

/* Navigation buttons and pagination */
:deep(.swiper-button-next),
:deep(.swiper-button-prev),
:deep(.swiper-pagination-bullet-active) {
    color: #000;
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

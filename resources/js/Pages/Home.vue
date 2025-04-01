<template>
    <Head title=" - Home" />
    <div
        :key="showYellow"
        :class="[
            'z-50 flex h-screen justify-stretch pt-80',
            showYellow
                ? 'flex-row-reverse space-x-[30rem] space-x-reverse'
                : 'flex-row space-x-[30rem]',
        ]"
    >
        <!-- Left Side (Swiper) -->
        <div
            class="z-50 pl-5"
            :data-aos="showYellow ? 'fade-left' : 'fade-right'"
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

        <!-- Right Side (Form) -->
        <div
            class="z-50 w-96"
            :data-aos="showYellow ? 'fade-right' : 'fade-left'"
        >
            <p class="clock mb-10 text-center text-4xl">{{ currentTime }}</p>
            <form @submit.prevent="validateForm">
                <label for="employeeID" class="mb-1 block">DTR ID:</label>
                <input
                    v-model.trim="employeeID"
                    type="text"
                    id="employeeID"
                    :class="[
                        'h-10 w-full rounded-lg border bg-slate-50 px-5',
                        showError ? 'border-red-600' : 'border-gray-300',
                    ]"
                />
                <small v-if="showError" class="text-red-600"
                    >Please enter your Employee ID</small
                >
                <button
                    type="submit"
                    :class="[
                        'mt-5 w-full rounded-md py-2 transition-all duration-300',
                        showYellow
                            ? 'bg-[#fbc04a] hover:bg-[#e8a233]'
                            : 'bg-[#fc8e8e] hover:bg-[#f67373]',
                    ]"
                    :key="buttonValue"
                >
                    {{ showYellow ? "Login" : "Logout" }}
                </button>
            </form>

            <div class="mt-36">
                <button
                    class="w-full rounded-md py-2 text-xl text-red-600"
                    @click="toggleLoginLogout"
                >
                    {{ showYellow ? "F2: Logout" : "F1: Login" }}
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Component -->
    <Modal
        :show="showModal"
        :title="buttonValue"
        :confirmText="confirmText"
        @close="showModal = false"
        @confirm="handleConfirm"
    >
        <div class="flex w-[30rem] flex-col items-center justify-center p-5">
            <img
                src=""
                alt="Profile Picture"
                class="h-64 w-full rounded-md bg-slate-600"
            />
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
const showYellow = ref(true);
const currentTime = ref(
    new Date(new Date().getTime() + 5 * 60000).toLocaleTimeString(),
);
let clockInterval = null;

// Toggle login/logout button text
const toggleLoginLogout = () => {
    showYellow.value = !showYellow.value;
    reinitializeAOS();
};

// Listen F1 and F2 keypress
const handleKeyDown = async (e) => {
    if (e.key === "F1") {
        e.preventDefault();
        showYellow.value = true;
        showError.value = false;
        employeeID.value = "";
        showModal.value = false;
    } else if (e.key === "F2") {
        e.preventDefault();
        showYellow.value = false;
        showError.value = false;
        employeeID.value = "";
        showModal.value = false;
    }
    await reinitializeAOS();
};

// Reinitialize AOS after DOM update
const reinitializeAOS = async () => {
    await nextTick();
    AOS.refresh();
};

onMounted(() => {
    window.addEventListener("keydown", handleKeyDown);
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

onUnmounted(() => {
    window.removeEventListener("keydown", handleKeyDown);
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

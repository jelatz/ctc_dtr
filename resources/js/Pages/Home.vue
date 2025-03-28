<template>
    <Head title=" - Home" />
    <div
        :key="showYellow"
        :class="[
            'z-50 flex h-screen justify-stretch pt-96',
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
            <form action="">
                <label for="" class="block">DTR ID:</label>
                <input
                    type="text"
                    class="h-10 w-full rounded-lg border bg-slate-50 px-5"
                />
                <button
                    @click="showModal = true"
                    type="button"
                    :class="[
                        'mt-5 w-full rounded-md py-2',
                        showYellow ? 'bg-[#fbc04a]' : 'bg-[#fc8e8e]',
                    ]"
                >
                    {{ showYellow ? "Login" : "Logout" }}
                </button>
            </form>

            <div class="mt-36">
                <button class="w-full rounded-md py-2 text-xl text-red-600">
                    {{ showYellow ? "F2: Logout" : "F1: Login" }}
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Component (moved outside the flex container) -->
    <Modal
        :show="showModal"
        title="Confirmation"
        confirmText="Save"
        @close="showModal = false"
        @confirm="handleConfirm"
    >
        <p>
            This is a sample modal content. Are you sure you want to continue?
        </p>
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

const showYellow = ref(true);
const currentTime = ref(
    new Date(new Date().getTime() + 5 * 60000).toLocaleTimeString(),
);
let clockInterval = null;

const showModal = ref(false);
const handleConfirm = () => {
    alert("Confirmed!");
    showModal.value = false;
};
onMounted(() => {
    window.addEventListener("keydown", handleKeyDown);

    // Initialize AOS on component load
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

// ✅ Handle key press and reinitialize AOS
const handleKeyDown = async (e) => {
    if (e.key === "F1") {
        e.preventDefault();
        showYellow.value = true;
    } else if (e.key === "F2") {
        e.preventDefault();
        showYellow.value = false;
    }

    // ✅ Wait for Vue to update DOM and reinitialize AOS
    await nextTick();
    AOS.refresh();
};

// Updated data with both images and text
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
</style>

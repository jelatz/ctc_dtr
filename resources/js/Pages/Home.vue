<template>
    <Head title=" - Home" />
    <div class="flex -translate-x-[50vw] duration-500 ease-in-out *:w-[50vw]" id="container">
        <!-- Left Carousel -->
        <div class="flex items-start justify-center">
            <Carousel :slides="images" autoplay :interval="8000" />
        </div>
        
        <!-- Center Form -->
        <div class="flex flex-col items-center justify-start mt-24 relative overflow-hidden" data-aos="fade-right">
            <div class="w-full max-w-md relative z-10">
                <!-- Logo & Clock -->
                <div class="text-center mb-8">
                    <img :src="basePath + logo" alt="CallTek Logo" class="lg:w-full md:h-full w-96 object-contain p-2" />
                    <p class="text-gray-600 font-extrabold text-xl">Daily Time Record</p>
                </div>

                <ClockDisplay :currentTime="currentTime" :currentDateTime="currentDateTime" :timezone="timezone" />

                <!-- Form -->
                <form @submit.prevent="submitForm" class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-8 border border-gray-100">
                    <label for="employeeID" class="block mb-2 text-sm font-semibold text-gray-700">Employee ID</label>
                    <input 
                        ref="employeeIDInput" 
                        v-model.trim="formData.employeeID" 
                        type="text" 
                        id="employeeID"
                        placeholder="Enter your Employee ID" 
                        :class="inputClasses"
                    />
                    <small v-if="showError" class="block mt-2 text-red-600 font-medium">
                        {{ formData.errors.employeeID || errorMessage }}
                    </small>
                    
                    <button type="submit" :class="buttonClasses" :disabled="formData.processing">
                        {{ isLogin ? "Login" : "Logout" }}
                    </button>
                </form>

                <!-- F1/F2 Instructions -->
                <div id="loginLogout" :class="instructionClasses">
                    <p :class="['text-2xl font-bold', isLogin ? 'text-red-600' : 'text-blue-600']">
                        {{ isLogin ? "Press F2 to Logout" : "Press F1 to Login" }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Carousel -->
        <div class="flex items-start justify-center">
            <Carousel :slides="images" autoplay :interval="8000" />
        </div>
    </div>

    <!-- Modal -->
    <EmployeeModal 
        :show="showModal" 
        :employeeData="employeeData" 
        :scheduleData="scheduleData"
        :isLogin="isLogin"
        @close="handleModalClose"
        @confirm="confirmDtrSubmit"
    />
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import Carousel from "@/Components/Carousel.vue";
import EmployeeModal from "@/Components/EmployeeModal.vue";
import ClockDisplay from "@/Components/ClockDisplay.vue";
import Swal from "sweetalert2";
import { time, convertToLocalDate } from "@/utils/date";
import getBasePath from "@/utils/basePath";
import AOS from "aos";
import "aos/dist/aos.css";

// Constants
const basePath = getBasePath();
const logo = "images/ctc_logo.png";
const timezoneOffset = new Date().getTimezoneOffset();
const timezone = `${Intl.DateTimeFormat().resolvedOptions().timeZone} (UTC ${timezoneOffset > 0 ? '-' : '+'}${String(Math.abs(timezoneOffset) / 60).padStart(2, '0')}:00)`;

defineProps({
  employeeData: {
    type: Object,
    default: null,
  },
  schedules: {
    type: Array,
    default: () => [],
  },
  success: {
    type: String,
    default: null,
  },
})

const images = [
    { src: `${basePath}images/tony_espinoza.jpg`, caption: "Tony Espinoza", description: "Sir Tony has dedicated his professional life to creating technology solutions that help businesses turn obstacles into opportunities. His passion and commitment have turned CallTek into a global enterprise that touches billions of consumers each day through the myriad of technology operators and service providers who depend on the company for support.", position: "CEO" },
    { src: `${basePath}images/danny_wu.jpg`, caption: "Danny Wu", description: "Sir Danny’s involvement with global IT operations, vendor management, and data center operations gives him a high level of expertise in overall operations. Sir Danny Wu serves as the Vice President of Global Operations and has been an integral component of CallTek’s success and growth for more than 13 years.", position: "VP Global Operations" },
    { src: `${basePath}images/cher.jpg`, caption: "Cher", description: "As the head of HR/Admin, Cher is good at strategy and her ability to organize makes her one of our strongest and successful leaders. She is always ready to engage any challenge to support our CTC BPO Family.", position: "HR Admin" },
    { src: `${basePath}images/lizzie.jpg`, caption: "Lizzie", description: "Lizzie is headstrong, strong-willed, and practically fearless which is a vital quality as an operations Manager. She is always ready to take on new challenges and too stubborn to back away from them.", position: "Operations Manager" },
    { src: `${basePath}images/cathleen.jpg`, caption: "Cathleen", description: "Smart, tough, and hard-working, these qualities make Cathleen an effective operations Manager. Her dedication continues to serve as a pillar of leadership at CallTek.", position: "Operations Manager" },
    { src: `${basePath}images/jane.jpg`, caption: "Jane", description: "Jane is an independent thinker and is responsible for helping manage our new global workforce. She has a deep understanding of our heritage and culture as she is one of our first family members in CallTek.", position: "Operations Manager" },
    { src: `${basePath}images/yanni.jpg`, caption: "Yanni", description: "Yanni is resilient, optimistic, and extremely dedicated to her craft. She is able to overcome obstacles that come her way. She is not scared to invest emotionally, which enables her to foster connections and relationships.", position: "Operations Manager" },
];

// State
const showError = ref(false);
const errorMessage = ref("");
const employeeIDInput = ref(null);
const showModal = ref(false);
const isLogin = ref(true);
const currentTime = ref(new Date().toLocaleTimeString());
const currentDateTime = ref("");
const employeeData = ref();
const scheduleData = ref([]);

const page = usePage();
const formData = useForm({ employeeID: "" });
const confirmDtrSubmitForm = useForm({
    employee_id: "",
    timezone: "",
    dtrDate: "",
    type: "",
});

// Computed Classes
const inputClasses = computed(() => [
    'h-12 w-full rounded-xl border px-4 text-lg font-medium transition-all duration-200 focus:outline-none focus:ring-2',
    showError.value 
        ? 'border-red-400 bg-red-50 focus:ring-red-500' 
        : 'border-gray-300 bg-white focus:border-blue-500 focus:ring-blue-500',
]);

const buttonClasses = computed(() => [
    'mt-6 w-full h-12 rounded-xl font-bold text-lg transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98] shadow-lg',
    isLogin.value
        ? 'bg-gradient-to-r from-blue-600 to-blue-950 text-white hover:from-blue-700 hover:to-blue-800 shadow-blue-500/30'
        : 'bg-gradient-to-r from-red-600 to-red-950 text-white hover:from-red-700 hover:to-red-800 shadow-red-500/30',
]);

const instructionClasses = computed(() => [
    'mt-8 bg-white/60 backdrop-blur-sm rounded-2xl px-6 py-4 text-center border-2 transition-all duration-300',
    isLogin.value 
        ? 'border-red-300 shadow-lg shadow-red-500/20' 
        : 'border-blue-300 shadow-lg shadow-blue-500/20',
]);

// Methods
const formatDate = () => {
    const date = new Date();
    const days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
    const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    currentDateTime.value = `${days[date.getDay()]} - ${months[date.getMonth()]} ${date.getDate()}, ${date.getFullYear()}`;
};

const updateClock = () => {
    currentTime.value = new Date().toLocaleTimeString();
    formatDate();
};

const focusInput = () => setTimeout(() => employeeIDInput.value?.focus(), 300);

const handleModalClose = () => {
    showModal.value = false;
    // Reset URL to home route when modal closes
    window.history.pushState({}, '', route('home'));
    focusInput();
};

const submitForm = () => {
    if (!formData.employeeID) {
        showError.value = true;
        errorMessage.value = "Employee ID is required.";
        formData.clearErrors("employeeID");
        return;
    }
    
    formData.post(route("get-schedules"), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        preserveUrl: true,
        onError: () => {
            showError.value = true;
            errorMessage.value = formData.errors.employeeID || "Unexpected error";
        },
        onSuccess: () => {
            employeeData.value = page.props.employeeData || {};
            scheduleData.value = page.props.schedules || [];
            showModal.value = true;
            showError.value = false;
            errorMessage.value = "";
            formData.reset();
            setTimeout(() => document.getElementById("confirmDtrButton")?.focus(), 0);
        },
    });
};

const confirmDtrSubmit = () => {
    Object.assign(confirmDtrSubmitForm, {
        employee_id: employeeData.value?.employee_id,
        dtrDate: scheduleData.value[0]?.sched_date,
        type: isLogin.value ? "login" : "logout",
    });

    confirmDtrSubmitForm.post(route("confirm-dtr"), {
        onSuccess: (data) => {
            Swal.fire({
                title: "Success",
                text: data.props.success,
                icon: "success",
                confirmButtonText: "OK",
                confirmButtonColor: "#f97316",
            }).then(handleModalClose);
        },
        onError: (errors) => {
            Swal.fire({
                title: "Error",
                text: errors.employeeID || "Unexpected error",
                icon: "error",
                confirmButtonText: "OK",
                confirmButtonColor: "#2563eb",
            }).then(handleModalClose);
        },
    });
};

const toggleLoginMode = (mode) => {
    const container = document.getElementById("container");
    if (container) {
        container.classList[mode ? 'add' : 'remove']("-translate-x-[50vw]");
        isLogin.value = mode;
        showError.value = false;
        errorMessage.value = "";
    }
};

// Event Handlers
const handleKeyPress = (e) => {
    if (e.key === "Escape" && showModal.value) handleModalClose();
    if (e.key === "F1") { e.preventDefault(); toggleLoginMode(true); }
    if (e.key === "F2") toggleLoginMode(false);
};

// Lifecycle
let clockInterval;

onMounted(() => {
    AOS.init({ duration: 1000, once: false, offset: 100 });
    focusInput();
    formatDate();
    clockInterval = setInterval(updateClock, 1000);
    window.addEventListener("keydown", handleKeyPress);
    
    if (page.props.employeeData && page.props.schedules) {
        employeeData.value = page.props.employeeData;
        scheduleData.value = page.props.schedules;
        showModal.value = true;
    }
});

onBeforeUnmount(() => {
    clearInterval(clockInterval);
    window.removeEventListener("keydown", handleKeyPress);
});

watch(showModal, (isOpen) => !isOpen && focusInput());
</script>

<style scoped>
:deep(.modal-overlay) { z-index: 9999; }
</style>
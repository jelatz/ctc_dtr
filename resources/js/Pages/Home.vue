<template>
    <Head title=" - Home" />
    <div class="flex -translate-x-[50vw] duration-500 ease-in-out *:w-[50vw]" id="container">
        <!-- Left Carousel -->
        <div class="flex items-start justify-center">
            <Carousel :slides="images" autoplay :interval="8000" />
        </div>
        
        <!-- Center Form -->
        <div class="flex flex-col items-center justify-center p-8 relative overflow-hidden" :data-aos="'fade-right'">
            <!-- Background decoration -->
            <!-- <div class="absolute inset-0 bg-[linear-gradient(to_right,#8080800a_1px,transparent_1px),linear-gradient(to_bottom,#8080800a_1px,transparent_1px)] bg-[size:24px_24px] opacity-30"></div> -->
            
            <div class="w-full max-w-md relative z-10">
                <!-- CallTek Logo Area -->
                <div class="text-center mb-8">
                
                    <img :src="getBasePath()+logo" alt="CallTek Logo" class="w-full h-full object-contain p-2" />
                        <!-- <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg> -->
                    <!-- <h1 class="text-4xl font-bold mb-2">
                        <span class="text-orange-600">Call</span><span class="text-blue-600">Tek</span>
                    </h1> -->
                    <p class="text-gray-600 font-extrabold text-xl">Daily Time Record</p>
                </div>

                <!-- Clock Display -->
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 mb-6 border border-gray-100">
                    <p class="text-center text-6xl font-bold text-gray-900 mb-2 tracking-tight" style="text-shadow: 0 2px 8px rgba(0,0,0,0.06)">
                        {{ currentTime }}
                    </p>
                    <p class="text-center text-lg text-gray-700 font-medium mb-1">
                        {{ currentDateTime }}
                    </p>
                    <p class="text-center text-sm text-gray-500">
                        {{ timezone }}
                    </p>
                </div>

                <!-- Form Card -->
                <form @submit.prevent="submitForm" class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-8 border border-gray-100">
                    <label for="employeeID" class="block mb-2 text-sm font-semibold text-gray-700">
                        Employee ID
                    </label>
                    <input 
                        ref="employeeIDInput" 
                        v-model.trim="formData.employeeID" 
                        type="text" 
                        id="employeeID"
                        placeholder="Enter your Employee ID" 
                        :class="[
                            'h-12 w-full rounded-xl border px-4 text-lg font-medium transition-all duration-200 focus:outline-none focus:ring-2',
                            showError 
                                ? 'border-red-400 bg-red-50 focus:ring-red-500' 
                                : 'border-gray-300 bg-white focus:border-blue-500 focus:ring-blue-500',
                        ]" 
                    />
                    <small v-if="showError" class="block mt-2 text-red-600 font-medium">
                        {{ formData.errors.employeeID || errorMessage }}
                    </small>
                    
                    <button 
                        type="submit"
                        :class="[
                            'mt-6 w-full h-12 rounded-xl font-bold text-lg transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98] shadow-lg',
                            isLogin
                                ? 'bg-gradient-to-r from-blue-600 to-blue-950 text-white hover:from-blue-700 hover:to-blue-800 shadow-blue-500/30'
                                : 'bg-gradient-to-r from-red-600 to-red-950 text-white hover:from-red-700 hover:to-red-800 shadow-red-500/30',
                        ]" 
                        :disabled="formData.processing"
                    >
                        {{ isLogin ? "Login" : "Logout" }}
                    </button>
                </form>

                <!-- F1/F2 Instruction -->
                <div 
                    id="loginLogout" 
                    :class="[
                        'mt-8 bg-white/60 backdrop-blur-sm rounded-2xl px-6 py-4 text-center border-2 transition-all duration-300',
                        isLogin 
                            ? 'border-red-300 shadow-lg shadow-red-500/20' 
                            : 'border-blue-300 shadow-lg shadow-blue-500/20',
                    ]"
                >
                    <p class="text-sm font-semibold text-gray-600 mb-1">Quick Access</p>
                    <p :class="[
                        'text-2xl font-bold',
                        isLogin ? 'text-red-600' : 'text-blue-600',
                    ]">
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

    <!-- Modern Modal -->
    <Modal :show="showModal" @close="showModal = false" modalTitle="Employee Information">
        <div class="p-6">
            <!-- Employee Info Section -->
            <div class="bg-gradient-to-br from-orange-50 to-blue-50 rounded-2xl p-6 mb-6 border border-gray-200">
                <div class="flex items-center space-x-4">
                    <div class="w-24 h-24 rounded-xl bg-gradient-to-br from-orange-500 to-blue-600 flex items-center justify-center text-white text-3xl font-bold shadow-lg">
                        {{ employeeData?.employee_id?.substring(0, 2).toUpperCase() }}
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-600 mb-1">Employee ID</p>
                        <p class="text-2xl font-bold text-gray-900">{{ employeeData?.employee_id }}</p>
                        <p class="text-sm text-gray-600 mt-2">Position: <span class="font-semibold">Staff</span></p>
                    </div>
                </div>
            </div>

            <!-- History Table -->
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-3 border-b border-gray-200">
                    <p class="text-sm font-semibold text-gray-700">Recent DTR History (Last 5 Records)</p>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gradient-to-r from-blue-600 to-blue-700 text-white">
                            <tr>
                                <th class="py-3 px-4 text-left text-sm font-semibold">Date</th>
                                <th class="py-3 px-4 text-left text-sm font-semibold">Schedule</th>
                                <th class="py-3 px-4 text-left text-sm font-semibold">Login</th>
                                <th class="py-3 px-4 text-left text-sm font-semibold">Logout</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr 
                                v-for="(schedule, index) in scheduleData" 
                                :key="index"
                                class="border-b border-gray-100 hover:bg-orange-50/50 transition-colors"
                                :class="index % 2 === 0 ? 'bg-white' : 'bg-gray-50'"
                            >
                                <td class="py-3 px-4 text-sm font-medium text-gray-900">
                                    {{ convertToLocalDate(schedule?.sched_date) }}
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-700">
                                    {{ time(schedule?.sched_start) }} - {{ time(schedule?.sched_end) }}
                                </td>
                                <td class="py-3 px-4 text-sm">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ time(schedule?.dtr?.time_in) || '-' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-sm">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        {{ time(schedule?.dtr?.time_out) || '-' }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Confirm Button -->
            <form @submit.prevent="confirmDtrSubmit" class="mt-6">
                <button 
                    type="submit"
                    id="confirmDtrButton"
                    :class="[
                        'w-full h-12 rounded-xl font-bold text-lg transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98] shadow-lg',
                        isLogin
                            ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white hover:from-blue-700 hover:to-blue-800 shadow-blue-500/30'
                            : 'bg-gradient-to-r from-red-600 to-red-700 text-white hover:from-red-700 hover:to-red-800 shadow-red-500/30',
                    ]"
                >
                    {{ isLogin ? "Confirm Login" : "Confirm Logout" }}
                </button>
            </form>
        </div>
    </Modal>
</template>

<script setup>
import Carousel from "@/Components/Carousel.vue";
import "swiper/swiper-bundle.css";
import { ref, onMounted, onBeforeUnmount, watch } from "vue";
import AOS from "aos";
import { useForm, usePage, router } from "@inertiajs/vue3";
import "aos/dist/aos.css";
import Modal from "@/Components/Modal.vue";
import Swal from "sweetalert2";
import { time, convertToLocalDate } from "@/utils/date";
import getBasePath from "@/utils/basePath";



// States
const showError = ref(false);
const errorMessage = ref("");
const employeeIDInput = ref(null);
const showModal = ref(false);
const isLogin = ref(true);
const timezoneOffset = (new Date()).getTimezoneOffset()
const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone + ` (UTC ${timezoneOffset > 0 ? '-' : '+'}${String(Math.abs(timezoneOffset) / 60).padStart(2, '0')}:00)`;
const logo = "/images/ctc_logo.png";

// Images for the carousel
const images = [
    { src: getBasePath() + "/images/tony_espinoza.jpg", caption: "Tony Espinoza", description: "Sir Tony has dedicated his professional life to creating technology solutions that help businesses turn obstacles into opportunities. His passion and commitment have turned CallTek into a global enterprise that touches billions of consumers each day through the myriad of technology operators and service providers who depend on the company for support.",position: "CEO" },
    { src: getBasePath() + "/images/danny_wu.jpg", caption: "Danny Wu", description: "Sir Danny's involvement with global IT operations, vendor management, and data center operations gives him a high level of expertise in overall operations. Sir Danny Wu serves as the Vice President of Global Operations and has been an integral component of CallTek's success and growth for more than 13 years.",position: "VP Global Operations" },
    { src: getBasePath() + "/images/cher.jpg", caption: "Cher", description: "As the head of HR/Admin, Cher is good at strategy and her ability to organize makes her one of our strongest and successful leaders. She is always ready to engage any challenge to support our CTC BPO Family.",position: "HR Admin" },
    { src: getBasePath() + "/images/lizzie.jpg", caption: "Lizzie", description: "Lizzie is headstrong, strong-willed, and practically fearless which is a vital quality as an operations Manager. She is always ready to take on new challenges and too stubborn to back away from them.",position: "Operations Manager" },
    { src: getBasePath() + "/images/cathleen.jpg", caption: "Cathleen", description: "Smart, tough, and hard-working, these qualities make Cathleen an effective operations Manager. Her dedication continues to serve as a pillar of leadership at CallTek.",position: "Operations Manager" },
    { src: getBasePath() + "/images/jane.jpg", caption: "Jane", description: "Jane is an independent thinker and is responsible for helping manage our new global workforce. She has a deep understanding of our heritage and culture as she is one of our first family members in CallTek.",position: "Operations Manager" },
    { src: getBasePath() + "/images/yanni.jpg", caption: "Yanni", description: "Yanni is resilient, optimistic, and extremely dedicated to her craft. She is able to overcome obstacles that come her way. She is not scared to invest emotionally, which enables her to foster connections and relationships.",position: "Operations Manager" },
];

onMounted(() => {
    setTimeout(() => {
        employeeIDInput.value?.focus();
    }, 300);
});

watch(showModal, (isOpen) => {
    if (!isOpen) {
        setTimeout(() => {
            employeeIDInput.value?.focus();
        }, 300);
    }
});

// Close modal with ESC key
const handleEscKey = (e) => {
    if (e.key === "Escape" && showModal.value) {
        showModal.value = false;
    }
};

onMounted(() => {
    window.addEventListener("keydown", handleEscKey);
});

onBeforeUnmount(() => {
    window.removeEventListener("keydown", handleEscKey);
});

onMounted(() => {
    if (page.props.employeeData && page.props.schedules) {
        employeeData.value = page.props.employeeData;
        scheduleData.value = page.props.schedules;
        showModal.value = true;
    }
});

// EmployeeID form
const formData = useForm({
    employeeID: "",
});

const employeeData = ref();
const scheduleData = ref([]);

const page = usePage();

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
        onError: (errors) => {
            showError.value = true;
            errorMessage.value = formData.errors.employeeID || "Unexpected error";
        },
        onSuccess: () => {
            employeeData.value = page.props.employeeData || {};
            scheduleData.value = usePage().props.schedules || [];
            showModal.value = true;
            showError.value = false;
            errorMessage.value = "";
            setTimeout(() => {
                const btn = document.getElementById("confirmDtrButton");
                if (btn) btn.focus();
            }, 0);
            formData.reset();
        },
    });
};

// Confirm DTR submission
const confirmDtrSubmitForm = useForm({
    employee_id: "",
    timezone: "",
    dtrDate: "",
    type: isLogin.value,
});

const confirmDtrSubmit = () => {
    confirmDtrSubmitForm.employee_id = employeeData.value?.employee_id;
    confirmDtrSubmitForm.dtrDate = scheduleData.value[0]?.sched_date;
    confirmDtrSubmitForm.type = isLogin.value ? "login" : "logout";

    confirmDtrSubmitForm.post(route("confirm-dtr"), {
        onSuccess: (response) => {
            const flash = usePage().props.flash;
            Swal.fire({
                title: "Success",
                text: flash.success,
                icon: "success",
                confirmButtonText: "OK",
                confirmButtonColor: "#f97316",
            }).then(() => {
                showModal.value = false;
                formData.reset();
                employeeIDInput.value?.focus();
            });
        },
        onError: (errors) => {
            Swal.fire({
                title: "Error",
                text: errors.employeeID || "Unexpected error",
                icon: "error",
                confirmButtonText: "OK",
                confirmButtonColor: "#2563eb",
            }).then(() => {
                showModal.value = false;
                formData.reset();
                employeeIDInput.value?.focus();
            });
        },
    });
};

// Current time and date
const currentTime = ref(
    new Date(new Date().getTime() + 5 * 60000).toLocaleTimeString(),
);

const currentDateTime = ref("");

const formatDate = () => {
    const date = new Date(new Date().getTime() + 5 * 60000);
    const days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
    const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

    const dayName = days[date.getDay()];
    const monthName = months[date.getMonth()];
    const day = date.getDate();
    const year = date.getFullYear();

    currentDateTime.value = `${dayName} - ${monthName} ${day}, ${year}`;
};

let clockInterval = null;
formatDate();

onMounted(() => {
    AOS.init({
        duration: 1000,
        once: false,
        offset: 100,
    });

    clockInterval = setInterval(() => {
        currentTime.value = new Date(new Date().getTime() + 5 * 60000).toLocaleTimeString();
        formatDate();
    }, 1000);

    window.addEventListener("keydown", handleF2Key);
    window.addEventListener("keydown", handleF1Key);
});

onBeforeUnmount(() => {
    if (clockInterval) clearInterval(clockInterval);
    window.removeEventListener("keydown", handleF2Key);
    window.removeEventListener("keydown", handleF1Key);
});

const handleF2Key = (event) => {
    if (event.key === "F2") {
        const container = document.getElementById("container");
        showError.value = false;
        errorMessage.value = "";

        if (container) {
            container.classList.remove("-translate-x-[50vw]");
            isLogin.value = false;
        }
    }
};

const handleF1Key = (event) => {
    if (event.key === "F1") {
        event.preventDefault();
        const container = document.getElementById("container");

        if (container) {
            container.classList.add("-translate-x-[50vw]");
            isLogin.value = true;
        }
    }
};
</script>

<style scoped>
:deep(.modal-overlay) {
    z-index: 9999;
}
</style>
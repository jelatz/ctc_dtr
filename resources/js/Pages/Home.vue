<template>

    <Head title=" - Home" />
    <div class="flex w-full" id="container">
        <!-- Form (left) -->
        <div class="w-1/2 flex flex-col items-center justify-center p-4" :data-aos="'fade-right'">
            <div class="w-full max-w-md">
                <p class="clock mb-2 text-center text-4xl">{{ currentTime }}</p>
                <p class="mb-10 text-center">{{ currentDateTime }}</p>
                <form @submit.prevent="submitForm" class="shadow-xl p-8">
                    <label for="employeeID" class="mb-1 block font-bold">DTR ID:</label>
                    <input ref="employeeIDInput" v-model.trim="formData.employeeID" type="text" id="employeeID"
                        placeholder="Enter Employee ID" :class="[
                            'h-10 w-full rounded-lg border bg-slate-50 px-5',
                            showError ? 'border-red-600' : 'border-gray-300',
                        ]" />
                    <small v-if="showError" class="text-red-600">
                        {{ formData.errors.employeeID || errorMessage }}
                    </small>
                    <button
                        class="mx-auto mt-5 w-full cursor-pointer bg-blue-800 text-white rounded-lg py-1 hover:bg-blue-700"
                        :disabled="formData.processing">
                        Submit
                    </button>
                </form>
                <p id="loginLogout"
                    class="block w-36 mx-auto mt-20 px-4 py-2 rounded-lg text-3xl animate-bounce cursor-pointer text-center">
                    F2 <span class="text-lg">Logout</span>
                </p>
            </div>
        </div>

        <!-- Carousel (right) -->
        <div class="w-1/2 flex items-start justify-center bg-blue-400 p-4">
            <h1 class="text-white text-2xl">Carousel of Calltek Images</h1>
        </div>
    </div>


    <!-- Modal Component -->
    <Modal :show="showModal" @close="showModal = false" modalTitle="modalTitle">
        <div class="flex w-[30rem] flex-col items-center justify-center p-5">
            <img src="" alt="Profile Picture" class="h-64 w-full rounded-md bg-slate-600" />
            <p class="mt-3 w-full text-left">
                Employee ID:
                <span>{{ employeeData?.employee_id }}</span>
            </p>
            <p class="mt-2 w-full text-left">Position</p>
        </div>
        <div class="w-full p-5">
            <small class="mb-2">Showing 5 history </small>
            <table class="w-full">
                <thead class="bg-gray-600 text-white">
                    <tr class="py-2">
                        <th class="w-32 py-2">Date</th>
                        <th class="w-96 py-2">Schedule</th>
                        <th class="py-2">Login</th>
                        <th class="py-2">Logout</th>
                    </tr>
                </thead>
                <tbody class="nth-[0]-child:bg-gray-100">
                    <tr class="text-center even:bg-gray-200" v-for="(schedule, index) in scheduleData" :key="index">
                        <td class="py-3">
                            {{ convertToLocalDate(schedule?.sched_date) }}
                        </td>
                        <td class="py-3">
                            {{ time(schedule?.sched_start) }} -
                            {{ time(schedule?.sched_end) }}
                        </td>
                        <td class="py-3">{{ time(schedule?.dtr?.time_in) }}</td>
                        <td class="py-3">
                            {{ time(schedule?.dtr?.time_out) }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <form @submit.prevent="confirmDtrSubmit">
                <button type="submit"
                    class="float-end mt-5 w-fit cursor-pointer bg-[#fbc04a] px-10 py-1 hover:bg-[#fbc04ad4]"
                    id="confirmDtrButton">
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
import { ref, onMounted, onBeforeUnmount, watch } from "vue";
import AOS from "aos";
import { useForm, usePage, router } from "@inertiajs/vue3";
import "aos/dist/aos.css";
import Modal from "@/Components/Modal.vue";
import Swal from "sweetalert2";
import { time, convertToLocalDate } from "@/utils/date";

// States
const showError = ref(false);
const errorMessage = ref("");
const employeeIDInput = ref(null);
const showModal = ref(false);

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
        // router.visit(route("home"), {
        //     method: "get",
        //     preserveState: false,
        //     preserveScroll: false,
        // });
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
        replace: true, // prevents pushing /get-schedules to browser history
        onError: (errors) => {
            showError.value = true;
            errorMessage.value =
                formData.errors.employeeID || "Unexpected error";
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
});
const confirmDtrSubmit = () => {
    confirmDtrSubmitForm.employee_id = employeeData.value?.employee_id;
    confirmDtrSubmitForm.dtrDate = scheduleData.value[0]?.sched_date;
    confirmDtrSubmitForm.post(route("confirm-dtr"), {
        onSuccess: (response) => {
            const flash = usePage().props.flash;
            Swal.fire({
                title: "Success",
                text: flash.success,
                icon: "success",
                confirmButtonText: "OK",
            }).then(() => {
                showModal.value = false;
                formData.reset();
                employeeIDInput.value?.focus();
                employeeIDInput.value?.style.setProperty(
                    "border",
                    "2px solid #fbc04a",
                );
            });
        },
        onError: (errors) => {
            Swal.fire({
                title: "Error",
                text: errors.employeeID || "Unexpected error",
                icon: "error",
                confirmButtonText: "OK",
            }).then(() => {
                showModal.value = false;
                formData.reset();
                employeeIDInput.value?.focus();
                employeeIDInput.value?.style.setProperty(
                    "border",
                    "2px solid #fbc04a",
                );
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
let clockInterval = null;
formatDate();

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
        const loginLogoutButton = document.getElementById("loginLogout");

        if (container && loginLogoutButton) {
            container.classList.add("flipped");
            loginLogoutButton.innerHTML = `
        <span class="text-blue-600">F1</span>
        <span class="text-lg"> Login</span>
      `;
        }
    }
};

const handleF1Key = (event) => {
    if (event.key === "F1") {
        event.preventDefault();
        const container = document.getElementById("container");
        const loginLogoutButton = document.getElementById("loginLogout");

        if (container && loginLogoutButton) {
            container.classList.remove("flipped");
            loginLogoutButton.innerHTML = `
        <span class="text-red-600">F2</span>
        <span class="text-lg"> Logout</span>
      `;
        }
    }
};


</script>
<style scoped>
/* Clock styling */
.clock {
    text-shadow: 0 15px 3px rgba(54, 54, 54, 0.08);
}

/* Modal style fix */
:deep(.modal-overlay) {
    z-index: 9999;
}

#container {
    transition: transform 0.5s ease-in-out;
}

#container.flipped,#container.flipped>* {
  transform: scaleX(-1);
}

</style>

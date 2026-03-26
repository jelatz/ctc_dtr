<template>
    <Head :title="` - ${isLogin ? 'Login' : 'Logout'}`" />

    <!-- <div
        id="container"
        :class="[
            'flex duration-500 ease-in-out *:w-[50vw]',
            isLogin ? '-translate-x-[50vw]' : 'translate-x-0',
        ]"
    > -->

    <div
        id="container"
        class="flex transform transition-transform duration-500 ease-in-out *:w-[50vw] items-center"
        :style="{ transform: isLogin ? 'translateX(-50vw)' : 'translateX(0)' }"
    >

      <div class="flex flex-col items-center justify-center gap-6 space-y-10 py-6 px-4">
            <img
                :src="basePath + logo"
                alt="Logo"
                class="w-48 md:w-800 lg:w-140 object-contain"
            />
            <!-- World Analog Clocks -->
            <div class="flex flex-wrap justify-center gap-5 md:gap-14">
                <AnalogClock
                    v-for="tz in worldClockZones"
                    :key="tz.zone"
                    :serverTimestamp="serverTimestamp"
                    :timezone="tz.zone"
                    :label="tz.label"
                    :size="90"
                />
            </div>
        </div>

        <div
            class="relative flex flex-col items-center overflow-hidden w-full px-4 md:px-0"
            data-aos="fade-right"
        >
            <div class="relative z-10 w-full max-w-md">
                <div class="mb-8 text-center">
                    <!-- <img
                        :src="basePath + logo"
                        alt="Logo"
                        class="w-64 md:w-96 object-contain p-2 md:h-full lg:w-3/4 mx-auto"
                    /> -->
                    <p class="text-xl md:text-4xl font-extrabold text-gray-600">
                        Daily Time Record
                    </p>
                </div>

                <ClockDisplay
                    :currentTime="displayTime"
                    :currentDateTime="currentDateTime"
                    :timezone="timezone"
                    :serverTimestamp="serverTimestamp"
                />

                <form
                    @submit.prevent="submitForm"
                    class="rounded-2xl border border-gray-100 bg-white/80 p-6 md:p-8 shadow-md md:shadow-lg backdrop-blur-sm"
                >
                    <label
                        for="employeeID"
                        class="mb-1 md:mb-2 block text-sm md:text-sm font-semibold text-gray-700"
                        >Employee ID :</label
                    >
                    <input
                        ref="employeeIDInput"
                        v-model.trim="formData.employeeID"
                        type="text"
                        id="employeeID"
                        placeholder="Enter your Employee ID"
                        :class="inputClasses"
                    />
                    <small
                        v-if="showError"
                        class="mt-2 block font-medium text-red-600"
                    >
                        {{ formData.errors.employeeID || errorMessage }}
                    </small>

                    <button
                        type="submit"
                        :class="buttonClasses"
                        :disabled="formData.processing"
                    >
                        {{ isLogin ? "Login" : "Logout" }}
                    </button>
                </form>

                <div :class="instructionClasses">
                    <button
                        type="button"
                        :class="[
                            'group inline-flex items-center gap-2 bg-transparent border-none p-0 cursor-pointer select-none',
                            'text-base md:text-xl font-semibold tracking-wide',
                            'transition-all duration-200 ease-in-out',
                            'focus:outline-none',
                            isLogin ? 'text-red-600 hover:text-red-700' : 'text-blue-600 hover:text-blue-700',
                        ]"
                        @click="() => { isLogin = !isLogin; showError = false; focusInput(); }"
                    >
                        <span
                            :class="[
                                'inline-flex items-center justify-center w-7 h-7 rounded-full text-white flex-none',
                                'transition-transform duration-200 group-hover:scale-110 group-active:scale-95',
                                isLogin ? 'bg-red-500 shadow-md shadow-red-300' : 'bg-blue-500 shadow-md shadow-blue-300',
                            ]"
                        >
                            <MoveRight v-if="isLogin" :size="16" />
                            <MoveLeft v-else :size="16" />
                        </span>
                        <span
                            :class="[
                                'underline-offset-4 decoration-2',
                                'group-hover:underline group-active:opacity-70',
                                'transition-all duration-200',
                            ]"
                        >
                            {{ isLogin ? "Switch to Logout" : "Switch to Login" }}
                        </span>
                        <span class="text-md font-bold opacity-50 hidden md:inline">
                            {{ isLogin ? "(or press F2)" : "(or press F1)" }}
                        </span>
                    </button>
                </div>
            </div>
        </div>

     <div class="flex flex-col items-center justify-center gap-6 space-y-10 py-6 px-4">
            <img
                :src="basePath + logo"
                alt="Logo"
                class="w-48 md:w-800 lg:w-140 object-contain"
            />
            <!-- World Analog Clocks -->
            <div class="flex flex-wrap justify-center gap-5 md:gap-14">
                <AnalogClock
                    v-for="tz in worldClockZones"
                    :key="tz.zone"
                    :serverTimestamp="serverTimestamp"
                    :timezone="tz.zone"
                    :label="tz.label"
                    :size="90"
                />
            </div>
        </div>
    </div>

    <EmployeeModal
        :show="showModal"
        :employeeData="employeeData"
        :scheduleData="scheduleData"
        :currentTime="displayTime"
        :highlightedIndex="highlightedIndex"
        :isLogin="isLogin"
        @close="handleModalClose"
        @confirm="confirmDtrSubmit"
    />
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import Carousel from "@/Components/Carousel.vue";
import EmployeeModal from "@/Components/EmployeeModal.vue";
import ClockDisplay from "@/Components/ClockDisplay.vue";
import AnalogClock from "@/Components/AnalogClock.vue";
import Swal from "sweetalert2";
import getBasePath from "@/utils/basePath";
import AOS from "aos";
import "aos/dist/aos.css";
import { MoveRight, MoveLeft } from "lucide-vue-next";
const props = defineProps({
    employeeData: Object,
    schedules: Array,
});

const page = usePage();
const basePath = getBasePath();
const logo = "images/ctc_logo.png";

// Time Logic
const serverTimestamp = ref(page.props.server_time.now);
const timezone = page.props.server_time.timezone;

const worldClockZones = [
    { label: "ET", zone: "America/New_York" },
    { label: "CT", zone: "America/Chicago" },
    { label: "MT", zone: "America/Denver" },
    { label: "PT", zone: "America/Los_Angeles" },
];
let timer = null;
let lastTickAt = null; // wall-clock ms when we last incremented serverTimestamp

const displayTime = computed(() =>
    new Date(serverTimestamp.value).toLocaleTimeString("en-US", {
        timeZone: timezone,
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
        hour12: true,
    }),
);

const currentDateTime = computed(() => {
    return new Date(serverTimestamp.value)
        .toLocaleDateString("en-US", {
            timeZone: timezone,
            weekday: "long",
            year: "numeric",
            month: "long",
            day: "numeric",
        })
        .replace(/,/g, (m, i) => (i === 10 ? " -" : m)); // Match "Friday - February 26, 2026"
});

// State
const isLogin = ref(true);
const showModal = ref(false);
const showError = ref(false);
const errorMessage = ref("");
const employeeIDInput = ref(null);
const employeeData = ref(props.employeeData || {});
const scheduleData = ref(props.schedules || []);
const employeeID = ref("");
let modalTimeout = null; // Store timeout reference
const highlightedIndex = ref(null);

const formData = useForm({ employeeID: "" });
const dtrForm = useForm({ employee_id: "", dtrDate: "", type: "", timestamp: null });

// Computed Styles
const inputClasses = computed(() => [
    "h-10 md:h-12 w-full rounded-lg md:rounded-xl border px-3 md:px-4 text-base md:text-lg font-medium transition-all duration-200 focus:outline-none focus:ring-2",
    showError.value
        ? "border-red-400 bg-red-50 focus:ring-red-500"
        : "border-gray-300 bg-white focus:border-blue-500 focus:ring-blue-500",
]);

const buttonClasses = computed(() => [
    "mt-4 md:mt-6 w-full h-10 md:h-12 rounded-lg md:rounded-xl font-bold text-base md:text-lg transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98] shadow-md md:shadow-lg text-white",
    isLogin.value
        ? "bg-gradient-to-r from-blue-600 to-blue-950 shadow-blue-500/30"
        : "bg-gradient-to-r from-red-600 to-red-950 shadow-red-500/20",
]);

const instructionClasses = computed(() => [
    "mt-5 md:mt-8 bg-white/60 backdrop-blur-sm rounded-xl md:rounded-2xl px-4 md:px-6 py-3 md:py-4 text-center border-2 transition-all duration-300",
    isLogin.value
        ? "border-red-300 shadow-red-500/10"
        : "border-blue-300 shadow-blue-500/10",
]);

// Methods
const showToast = (message, type = "success") => {
    const isError = type === "error";
    const bgColor = isError ? "bg-red-50" : "bg-green-50";
    const iconBgColor = isError ? "bg-red-100" : "bg-green-100";
    const textColor = isError ? "text-red-700" : "text-green-700";
    
    // Add distinct colored borders to easily differentiate success and failure
    const borderColor = isError ? "border-red-400 border-l-4 border-l-red-500" : "border-green-400 border-l-4 border-l-green-500";
    
    const iconHtml = isError 
        ? `<svg class="w-4 h-4 md:w-5 md:h-5 ${textColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>`
        : `<svg class="w-4 h-4 md:w-5 md:h-5 ${textColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>`;

    return Swal.fire({
        position: "top",
        showConfirmButton: false,
        timer: 3000,
        backdrop: true,
        customClass: {
            popup: `!p-3 md:!p-4 !rounded-lg md:!rounded-xl shadow-md md:shadow-xl mt-4 md:mt-6 border ${borderColor} ${bgColor}`,
            htmlContainer: "!m-0"
        },
        width: "auto",
        html: `
            <div class="flex items-center space-x-2 md:space-x-3">
                <div class="flex items-center justify-center w-6 h-6 md:w-8 md:h-8 rounded-full ${iconBgColor} flex-none border border-white/50 shadow-sm">
                    ${iconHtml}
                </div>
                <span class="text-sm md:text-base font-bold ${textColor} whitespace-nowrap pr-1 md:pr-2 pl-2 md:pl-3">${message}</span>
            </div>
        `
    });
};

const focusInput = () => setTimeout(() => employeeIDInput.value?.focus(), 100);

const handleModalClose = () => {
    showModal.value = false;
    window.history.pushState({}, "", route("home"));
    focusInput();
    
    if (modalTimeout) {
        clearTimeout(modalTimeout);
        modalTimeout = null;
    }
};

const submitForm = () => {
    if (!navigator.onLine) {
        showToast("No internet connection. Please try again.", "error");
        return;
    }

    if (!formData.employeeID) {
        showError.value = true;
        errorMessage.value = "Employee ID is required.";
        formData.clearErrors();
        return;
    }

    formData.post(route("get-schedules"), {
        preserveState: true,
        onSuccess: () => {
            employeeData.value = page.props.employeeData;
            scheduleData.value = page.props.schedules;
            showModal.value = true;
            showError.value = false;
            formData.reset();

            // Set auto-close timeout (1 minute = 60000 ms)
            if (page.props.server_time && page.props.server_time.now) {
                serverTimestamp.value = page.props.server_time.now;
                lastTickAt = Date.now();
            }

            if (modalTimeout) clearTimeout(modalTimeout);
            modalTimeout = setTimeout(() => {
                if (showModal.value) {
                    handleModalClose();
                    // Optionally inform the user they were redirected due to inactivity
                    // showToast("Redirected to home due to inactivity.", "error"); 
                }
            }, 60000);
        },
        onError: (err) => {
            showError.value = true;
            errorMessage.value =
                err.employeeID || "Error retrieving employee data.";
        },
    });
};

// Builds inline animation HTML for embedding in the Swal success popup
const buildDtrAnimationHtml = (isLoginVal) => {
    if (isLoginVal) {
        // Entry: blue, person walks right→left into LEFT door
        return `
        <style>
            @keyframes swalWalkEntry{0%{transform:scaleX(-1) translateX(0);opacity:1}85%{transform:scaleX(-1) translateX(152px);opacity:1}95%{transform:scaleX(-1) translateX(162px);opacity:0}100%{transform:scaleX(-1) translateX(162px);opacity:0}}
            @keyframes swalStrideE{0%{transform:rotate(-25deg)}100%{transform:rotate(25deg)}}
            @keyframes swalOpenDoor{0%{transform:perspective(100px) rotateY(70deg)}100%{transform:perspective(100px) rotateY(0deg)}}
            @keyframes swalCloseDoorE{0%{transform:perspective(100px) rotateY(0deg)}100%{transform:perspective(100px) rotateY(70deg)}}
        </style>
        <div style="display:flex;justify-content:center;">
            <div style="position:relative;width:220px;height:60px;border-bottom:2px solid rgba(59,130,246,0.35);">
                <div style="position:absolute;left:8px;bottom:0;display:flex;flex-direction:column;align-items:center;z-index:3;">
                    <div style="font-size:7px;font-weight:bold;color:#3b82f6;border:1px solid #3b82f6;padding:1px 3px;margin-bottom:2px;letter-spacing:1px;border-radius:1px;">ENTRY</div>
                    <div style="width:22px;height:30px;border:1.5px solid #3b82f6;border-bottom:none;background:rgba(219,234,254,0.4);position:relative;">
                        <div style="width:100%;height:100%;background:rgba(147,197,253,0.5);transform-origin:right;transform:perspective(100px) rotateY(70deg);animation:swalOpenDoor 0.4s 1.2s forwards,swalCloseDoorE 0.5s 1.5s forwards;"></div>
                    </div>
                </div>
                <div style="position:absolute;right:0;bottom:0;z-index:2;transform:scaleX(-1) translateX(0);animation:swalWalkEntry 1.5s linear forwards;">
                    <div style="width:8px;height:8px;background:#3b82f6;border-radius:50%;margin:0 auto 1px;"></div>
                    <div style="width:6px;height:13px;background:#3b82f6;border-radius:2px;margin:0 auto;"></div>
                    <div style="display:flex;justify-content:center;gap:1px;margin-top:-1px;">
                        <div style="width:3px;height:8px;background:#3b82f6;border-radius:1px;transform-origin:top;animation:swalStrideE 0.3s infinite alternate;"></div>
                        <div style="width:3px;height:8px;background:#3b82f6;border-radius:1px;transform-origin:top;animation:swalStrideE 0.3s infinite alternate-reverse;"></div>
                    </div>
                </div>
            </div>
        </div>`;
    } else {
        // Exit: red, person walks left→right into RIGHT door
        return `
        <style>
            @keyframes swalWalkExit{0%{transform:translateX(8px);opacity:1}85%{transform:translateX(152px);opacity:1}95%{transform:translateX(162px);opacity:0}100%{transform:translateX(162px);opacity:0}}
            @keyframes swalStrideX{0%{transform:rotate(-25deg)}100%{transform:rotate(25deg)}}
            @keyframes swalCloseDoorX{0%{transform:perspective(100px) rotateY(-70deg)}100%{transform:perspective(100px) rotateY(0deg)}}
        </style>
        <div style="display:flex;justify-content:center;">
            <div style="position:relative;width:220px;height:60px;border-bottom:2px solid rgba(239,68,68,0.35);">
                <div style="position:absolute;right:8px;bottom:0;display:flex;flex-direction:column;align-items:center;z-index:3;">
                    <div style="font-size:7px;font-weight:bold;color:#ef4444;border:1px solid #ef4444;padding:1px 3px;margin-bottom:2px;letter-spacing:1px;border-radius:1px;">EXIT</div>
                    <div style="width:22px;height:30px;border:1.5px solid #ef4444;border-bottom:none;background:rgba(254,226,226,0.4);position:relative;">
                        <div style="width:100%;height:100%;background:rgba(252,165,165,0.5);transform-origin:left;transform:perspective(100px) rotateY(-70deg);animation:swalCloseDoorX 0.5s 1.5s forwards;"></div>
                    </div>
                </div>
                <div style="position:absolute;left:0;bottom:0;z-index:2;animation:swalWalkExit 1.5s linear forwards;">
                    <div style="width:8px;height:8px;background:#ef4444;border-radius:50%;margin:0 auto 1px;"></div>
                    <div style="width:6px;height:13px;background:#ef4444;border-radius:2px;margin:0 auto;"></div>
                    <div style="display:flex;justify-content:center;gap:1px;margin-top:-1px;">
                        <div style="width:3px;height:8px;background:#ef4444;border-radius:1px;transform-origin:top;animation:swalStrideX 0.3s infinite alternate;"></div>
                        <div style="width:3px;height:8px;background:#ef4444;border-radius:1px;transform-origin:top;animation:swalStrideX 0.3s infinite alternate-reverse;"></div>
                    </div>
                </div>
            </div>
        </div>`;
    }
};

const confirmDtrSubmit = () => {
    if (!navigator.onLine) {
        showToast("No internet connection. Please try again.", "error");
        return;
    }

    dtrForm.employee_id = employeeData.value.employee_id;
    dtrForm.dtrDate = scheduleData.value[0]?.sched_date;
    dtrForm.type = isLogin.value ? "login" : "logout";

    // Force strict catch-up if the OS just woke from sleep and setInterval hasn't fired yet
    if (lastTickAt !== null) {
        const now = Date.now();
        const elapsed = now - lastTickAt;
        if (elapsed > 0) {
            serverTimestamp.value += elapsed;
            lastTickAt = now;
        }
    }

    dtrForm.timestamp = serverTimestamp.value;

    const clickTime = serverTimestamp.value;

    dtrForm.post(route("confirm-dtr"), {
        preserveUrl: true,
        preserveState: true,
        onSuccess: (res) => {
            // Immediately reflect the successful login/logout in the modal's history
            const targetSchedule = scheduleData.value[0];
            if (targetSchedule) {
                if (!targetSchedule.dtr) {
                    targetSchedule.dtr = { time_in: null, time_out: null };
                }
                const nowString = new Date(clickTime).toLocaleTimeString("en-US", {
                    timeZone: timezone,
                    hour12: false,
                    hour: "2-digit",
                    minute: "2-digit",
                    second: "2-digit"
                });

                if (isLogin.value) {
                    targetSchedule.dtr.time_in = `${targetSchedule.sched_date} ${nowString}`;
                } else {
                    targetSchedule.dtr.time_out = `${targetSchedule.sched_date} ${nowString}`;
                }
                // Highlight the updated row with a blink animation
                highlightedIndex.value = 0;
                setTimeout(() => { highlightedIndex.value = null; }, 5000);
            }

            // Show success message + walking animation inside the Swal popup
            const isLoginVal = isLogin.value;
            const textColor = isLoginVal ? "text-green-700" : "text-green-700";
            const iconHtml = `<svg class="w-4 h-4 md:w-5 md:h-5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>`;

            Swal.fire({
                position: "top",
                showConfirmButton: false,
                timer: 5000,
                backdrop: true,
                customClass: {
                    popup: `!p-3 md:!p-4 !rounded-lg md:!rounded-xl shadow-md md:shadow-xl mt-4 md:mt-6 border border-green-400 border-l-4 border-l-green-500 bg-green-50`,
                    htmlContainer: "!m-0"
                },
                width: "auto",
                html: `
                    <div class="flex items-center space-x-2 md:space-x-3">
                        <div class="flex items-center justify-center w-6 h-6 md:w-8 md:h-8 rounded-full bg-green-100 flex-none border border-white/50 shadow-sm">
                            ${iconHtml}
                        </div>
                        <span class="text-lg md:text-xl font-bold text-green-700 whitespace-nowrap pr-1 md:pr-2 pl-2 md:pl-3">${res.props.success}</span>
                    </div>
                    ${buildDtrAnimationHtml(isLoginVal)}
                `
            }).then(handleModalClose);
        },
        onError: (err) => {
            if (page.props.schedules) {
                scheduleData.value = page.props.schedules;
            }
            showToast(err.employee_id || "Failed to record DTR", "error").then(handleModalClose);
        },
    });
};

const handleKeyPress = (e) => {
    if (e.key === "F1") {
        e.preventDefault();
        if (!showModal.value) {
            isLogin.value = true;
            showError.value = false;
            focusInput();
        }
    }
    if (e.key === "F2") {
        e.preventDefault();
        if (!showModal.value) {
            isLogin.value = false;
            showError.value = false;
            focusInput();
        }
    }
    if (e.key === "Escape" && showModal.value) handleModalClose();
};

const handleBeforeUnload = () => {
    if (showModal.value) {
        // Change the URL back to home so the refresh loads the home page directly
        window.history.replaceState({}, "", route("home"));
    }
};

// Resync clock when the tab becomes visible again after being throttled
const handleVisibilityChange = () => {
    if (document.visibilityState === 'visible' && lastTickAt !== null) {
        const elapsed = Date.now() - lastTickAt;
        serverTimestamp.value += elapsed;
        lastTickAt = Date.now();
    }
};

// Lifecycle
onMounted(() => {
    AOS.init({ duration: 800, once: true });
    lastTickAt = Date.now();
    timer = setInterval(() => {
        const now = Date.now();
        const elapsed = now - lastTickAt;
        serverTimestamp.value += elapsed;
        lastTickAt = now;
    }, 1000);
    document.addEventListener('visibilitychange', handleVisibilityChange);
    window.addEventListener("keydown", handleKeyPress);
    window.addEventListener("beforeunload", handleBeforeUnload);
    focusInput();
});

onUnmounted(() => {
    clearInterval(timer);
    if (modalTimeout) clearTimeout(modalTimeout);
    document.removeEventListener('visibilitychange', handleVisibilityChange);
    window.removeEventListener("keydown", handleKeyPress);
    window.removeEventListener("beforeunload", handleBeforeUnload);
});

watch(showModal, (val) => !val && focusInput());

// const images = [
//     {
//         src: `${basePath}images/tony_espinoza.jpg`,
//         caption: "Tony Espinoza",
//         description:
//             "Sir Tony has dedicated his professional life to creating technology solutions that help businesses turn obstacles into opportunities. His passion and commitment have turned CallTek into a global enterprise that touches billions of consumers each day through the myriad of technology operators and service providers who depend on the company for support.",
//         position: "CEO",
//     },
//     {
//         src: `${basePath}images/danny_wu.jpg`,
//         caption: "Danny Wu",
//         description:
//             "Sir Danny’s involvement with global IT operations, vendor management, and data center operations gives him a high level of expertise in overall operations. Sir Danny Wu serves as the Vice President of Global Operations and has been an integral component of CallTek’s success and growth for more than 13 years.",
//         position: "VP Global Operations",
//     },
//     {
//         src: `${basePath}images/cher.jpg`,
//         caption: "Cher",
//         description:
//             "As the head of HR/Admin, Cher is good at strategy and her ability to organize makes her one of our strongest and successful leaders. She is always ready to engage any challenge to support our CTC BPO Family.",
//         position: "HR Admin",
//     },
//     {
//         src: `${basePath}images/lizzie.jpg`,
//         caption: "Lizzie",
//         description:
//             "Lizzie is headstrong, strong-willed, and practically fearless which is a vital quality as an operations Manager. She is always ready to take on new challenges and too stubborn to back away from them.",
//         position: "Operations Manager",
//     },
//     {
//         src: `${basePath}images/cathleen.jpg`,
//         caption: "Cathleen",
//         description:
//             "Smart, tough, and hard-working, these qualities make Cathleen an effective operations Manager. Her dedication continues to serve as a pillar of leadership at CallTek.",
//         position: "Operations Manager",
//     },
//     {
//         src: `${basePath}images/jane.jpg`,
//         caption: "Jane",
//         description:
//             "Jane is an independent thinker and is responsible for helping manage our new global workforce. She has a deep understanding of our heritage and culture as she is one of our first family members in CallTek.",
//         position: "Operations Manager",
//     },
//     {
//         src: `${basePath}images/yanni.jpg`,
//         caption: "Yanni",
//         description:
//             "Yanni is resilient, optimistic, and extremely dedicated to her craft. She is able to overcome obstacles that come her way. She is not scared to invest emotionally, which enables her to foster connections and relationships.",
//         position: "Operations Manager",
//     },
// ];
</script>

<style scoped>
@media (max-width: 768px) {
    #container {
        flex-direction: column !important;
        transform: none !important;
    }
    #container > div {
        width: 100vw !important;
    }
}
</style>

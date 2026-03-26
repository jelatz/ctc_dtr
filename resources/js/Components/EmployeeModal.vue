<template>
    <Modal :show="show" @close="$emit('close')" modalTitle="Employee Information">
        <div class="p-4 md:p-6 w-full">
            <!-- Employee Info -->
            <div class="bg-gradient-to-br from-orange-50 to-blue-50 rounded-2xl p-4 md:p-6 mb-4 md:mb-6 border border-gray-200">
                <div class="flex items-start space-x-3 md:space-x-4">
                    <div class="w-20 h-20 md:w-24 md:h-24 rounded-xl bg-gradient-to-br from-orange-500 to-blue-600 flex items-center justify-center text-white text-2xl md:text-3xl font-bold shadow-lg">
                        <img :src="employeeData?.photo" alt="Employee Photo" class="w-full h-full object-cover rounded-xl" />
                    </div>
                    <div class="flex-1">
                        <p class="text-xs md:text-sm font-semibold text-gray-600 mb-1">Employee ID</p>
                        <p class="text-lg md:text-2xl font-bold text-gray-900">{{ employeeData?.employee_id }}</p>
                        <p class="text-base md:text-xl font-bold text-gray-900">{{ employeeData?.name }}</p>
                    </div>
                    <div>
                        <p>{{currentTime}}</p>
                    </div>
                </div>
            </div>

            <!-- History Table -->
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-4 md:px-6 py-2 md:py-3 border-b border-gray-200">
                    <p class="text-xs md:text-sm font-semibold text-gray-700">Recent DTR History (Last 5 Records)</p>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gradient-to-r from-blue-600 to-blue-700 text-white">
                            <tr>
                                <th class="py-2 px-3 md:py-3 md:px-4 text-left text-xs md:text-sm font-semibold">Date</th>
                                <th class="py-2 px-3 md:py-3 md:px-4 text-left text-xs md:text-sm font-semibold">Schedule</th>
                                <th class="py-2 px-3 md:py-3 md:px-4 text-left text-xs md:text-sm font-semibold">Login</th>
                                <th class="py-2 px-3 md:py-3 md:px-4 text-left text-xs md:text-sm font-semibold">Logout</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr 
                                v-for="(schedule, index) in scheduleData" 
                                :key="index"
                                class="border-b border-gray-100 hover:bg-orange-50/50 transition-colors"
                                :class="[
                                index % 2 === 0 ? 'bg-white' : 'bg-gray-50',
                                index === highlightedIndex ? 'row-blink' : ''
                            ]"
                            >
                                <td class="py-2 px-3 md:py-3 md:px-4 text-xs md:text-sm font-medium text-gray-900">
                                    {{ convertToLocalDate(schedule?.sched_date) }}
                                    <p v-if="schedule?.day_type === 'dayoff'" class="text-red-500 font-bold flex items-center gap-2"><TreePalm class="w-4 h-4 md:w-5 md:h-5 text-green-500" /> {{ schedule?.day_type }}</p>
                                </td>
                                <td class="py-2 px-3 md:py-3 md:px-4 text-xs md:text-sm text-gray-700">
                                    {{ time(schedule?.sched_start) }} - {{ time(schedule?.sched_end) }}
                                </td>
                                <td class="py-2 px-3 md:py-3 md:px-4 text-xs md:text-sm">
                                    <span class="inline-flex items-center px-2 py-0.5 md:px-2.5 md:py-1 rounded-full text-[10px] md:text-xs font-medium bg-green-100 text-green-800 whitespace-nowrap">
                                        {{ time(schedule?.dtr?.time_in) || '-' }}
                                    </span>
                                </td>
                                <td class="py-2 px-3 md:py-3 md:px-4 text-xs md:text-sm">
                                    <span class="inline-flex items-center px-2 py-0.5 md:px-2.5 md:py-1 rounded-full text-[10px] md:text-xs font-medium bg-red-100 text-red-800 whitespace-nowrap">
                                        {{ time(schedule?.dtr?.time_out) || '-' }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Confirm Button -->
            <form @submit.prevent="handleConfirm" class="mt-4 md:mt-6">
                <button
                    ref="confirmButton"
                    type="submit"
                    id="confirmDtrButton"
                    :class="buttonClasses"
                    :disabled="isAnimating"
                >
                    {{ isLogin ? "Confirm Login" : "Confirm Logout" }}
                </button>
            </form>
        </div>
    </Modal>
</template>

<script setup>
import { computed, ref, watch, nextTick } from 'vue';
import Modal from "@/Components/Modal.vue";
import { time, convertToLocalDate } from "@/utils/date";
import { TreePalm } from 'lucide-vue-next';

const props = defineProps({
    show: Boolean,
    employeeData: Object,
    scheduleData: Array,
    isLogin: Boolean,
    currentTime: String,
    highlightedIndex: { type: Number, default: null },
});

const emit = defineEmits(['close', 'confirm']);

const isAnimating = ref(false);

const handleConfirm = () => {
    // Start animation and fire submission simultaneously
    isAnimating.value = true;
    emit('confirm');
};

const confirmButton = ref(null);

watch(() => props.show, async (newVal) => {
    if (newVal) {
        await nextTick();
        setTimeout(() => {
            // Focus the confirm button when modal opens for easy Enter key submission
            confirmButton.value?.focus();
        }, 150); // Slightly longer delay to ensure Modal transition completes
    }
});

const buttonClasses = computed(() => [
    'w-full h-10 md:h-12 rounded-xl font-bold text-base md:text-lg transition-all duration-300 shadow-lg',
    props.isLogin
        ? isAnimating.value
            ? 'bg-gradient-to-r from-emerald-400 to-green-500 text-white opacity-60 cursor-not-allowed'
            : 'bg-gradient-to-r from-emerald-500 to-green-600 text-white hover:from-emerald-600 hover:to-green-700 shadow-emerald-500/30 transform hover:scale-[1.02] active:scale-[0.98]'
        : isAnimating.value
            ? 'bg-gradient-to-r from-red-400 to-red-500 text-white opacity-60 cursor-not-allowed'
            : 'bg-gradient-to-r from-red-600 to-red-700 text-white hover:from-red-700 hover:to-red-800 shadow-red-500/30 transform hover:scale-[1.02] active:scale-[0.98]',
]);

// Reset animation state when modal closes
watch(() => props.show, (newVal) => {
    if (!newVal) {
        isAnimating.value = false;
    }
});
</script>

<style scoped>
@keyframes rowBlink {
    0%, 100% { background-color: inherit; }
    50%       { background-color: #42b569; } /* soft yellow flash */
}

.row-blink {
    animation: rowBlink 1.2s ease-in-out infinite;
}
</style>
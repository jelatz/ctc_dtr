<template>
    <Modal :show="show" @close="$emit('close')" modalTitle="Employee Information">
        <div class="p-6">
            <!-- Employee Info -->
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
            <form @submit.prevent="$emit('confirm')">
                <button 
                    type="submit"
                    id="confirmDtrButton"
                    :class="buttonClasses"
                >
                    {{ isLogin ? "Confirm Login" : "Confirm Logout" }}
                </button>
            </form>
        </div>
    </Modal>
</template>

<script setup>
import { computed } from 'vue';
import Modal from "@/Components/Modal.vue";
import { time, convertToLocalDate } from "@/utils/date";

const props = defineProps({
    show: Boolean,
    employeeData: Object,
    scheduleData: Array,
    isLogin: Boolean,
});

defineEmits(['close', 'confirm']);

const buttonClasses = computed(() => [
    'mt-6 w-full h-12 rounded-xl font-bold text-lg transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98] shadow-lg',
    props.isLogin
        ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white hover:from-blue-700 hover:to-blue-800 shadow-blue-500/30'
        : 'bg-gradient-to-r from-red-600 to-red-700 text-white hover:from-red-700 hover:to-red-800 shadow-red-500/30',
]);
</script>
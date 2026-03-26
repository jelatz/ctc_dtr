<template>
    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-4 md:p-6 mb-4 md:mb-6 border border-gray-100">
        <p class="text-center text-3xl md:text-4xl font-bold text-gray-900 mb-1 md:mb-2 tracking-tight" style="text-shadow: 0 2px 8px rgba(0,0,0,0.06)">
            {{ currentTime }}
        </p>
        <p class="text-center text-base md:text-lg text-gray-700 font-medium mb-1">{{ currentDateTime }}</p>
        <p class="text-center text-sm md:text-md text-gray-500">{{ timezone }}</p>
    </div>
</template>

<script setup>
defineProps({
    currentTime: String,
    currentDateTime: String,
    timezone: String,
    serverTimestamp: Number,
});
</script>
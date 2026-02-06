<template>
    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 mb-6 border border-gray-100">
        <p class="text-center text-4xl font-bold text-gray-900 mb-2 tracking-tight" style="text-shadow: 0 2px 8px rgba(0,0,0,0.06)">
            {{ currentTime }}
        </p>
        <p class="text-center text-lg text-gray-700 font-medium mb-1">{{ currentDateTime }}</p>
        <p class="text-center text-md text-gray-500">{{ timezone }}</p>
    </div>
</template>

<script setup>
defineProps({
    currentTime: String,
    currentDateTime: String,
    timezone: String,
});
</script>
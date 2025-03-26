<template>
    <div class="relative overflow-hidden h-screen z-0">
        <div class="transition-wrapper">
            <!-- Login -->
            <div :class="[
                'w-[1200px] h-[1200px] bg-[#fbc04a] rounded-[100px] rotate-[45deg] absolute z-10 top-1/2 -translate-y-1/2',
                showYellow ? '-left-[30rem]' : '-left-[100rem]',
                'transition-all duration-700 ease-in-out'
            ]">
            </div>
            <div :class="[
                'w-[700px] h-[700px] bg-[#fbc04a8c] rounded-[100px] rotate-[45deg] absolute',
                showYellow ? 'left-[8rem] -top-[9rem]' : '-left-[52rem] -top-[9rem]',
                'transition-all duration-700 ease-in-out'
            ]">
            </div>
            <div :class="[
                'absolute z-50 top-[4rem]',
                showYellow ? 'left-[36rem] opacity-100' : '-left-[10rem] opacity-0',
                'transition-all duration-700 ease-in-out'
            ]">
                <i class="fa-solid fa-quote-right text-[12rem] text-[#7c7b788c]"></i>
            </div>
        </div>

        <slot />
        <!-- Logout -->
        <div class="transition-wrapper">
            <div :class="[
                'w-[1200px] h-[1200px] bg-[#fc8e8e] rounded-[100px] rotate-[45deg] absolute z-10 top-1/2 -translate-y-1/2',
                !showYellow ? '-right-[30rem]' : '-right-[100rem]',
                'transition-all duration-700 ease-in-out'
            ]">
            </div>
            <div :class="[
                'w-[700px] h-[700px] bg-[#ffc5c5] rounded-[100px] rotate-[45deg] absolute',
                !showYellow ? 'right-[8rem] -top-[9rem]' : 'right-[-52rem] -top-[9rem]',
                'transition-all duration-700 ease-in-out'
            ]">
            </div>
            <div :class="[
                'absolute z-50 top-[4rem]',
                !showYellow ? 'right-[36rem] opacity-100' : 'right-[-10rem] opacity-0',
                'transition-all duration-700 ease-in-out'
            ]">
                <i class="fa-solid fa-quote-right text-[12rem] text-[#7c7b788c]"></i>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const showYellow = ref(true)

const handleKeyDown = (e) => {
    if (e.key === 'F1') {
        e.preventDefault()
        showYellow.value = true
    } else if (e.key === 'F2') {
        e.preventDefault()
        showYellow.value = false
    }
}

onMounted(() => {
    window.addEventListener('keydown', handleKeyDown)
})

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeyDown)
})
</script>

<style scoped>
.transition-wrapper {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
}
</style>

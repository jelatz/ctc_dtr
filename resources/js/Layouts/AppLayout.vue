<template>
    <div class="relative z-0 h-screen overflow-hidden">
        <div class="transition-wrapper">
            <!-- Login -->
            <div
                :class="[
                    'absolute top-1/2 z-10 h-[1200px] w-[1200px] -translate-y-1/2 rotate-[45deg] rounded-[100px] bg-[#fbc04a]',
                    showYellow ? '-left-[30rem]' : '-left-[100rem]',
                    'transition-all duration-700 ease-in-out',
                ]"
            ></div>
            <div
                :class="[
                    'absolute h-[700px] w-[700px] rotate-[45deg] rounded-[100px] bg-[#fbc04a8c]',
                    showYellow
                        ? '-top-[9rem] left-[8rem]'
                        : '-top-[9rem] -left-[52rem]',
                    'transition-all duration-700 ease-in-out',
                ]"
            ></div>
            <div
                :class="[
                    'absolute top-[4rem] z-50',
                    showYellow
                        ? 'left-[36rem] opacity-100'
                        : '-left-[10rem] opacity-0',
                    'transition-all duration-700 ease-in-out',
                ]"
            >
                <i
                    class="fa-solid fa-quote-right text-[12rem] text-[#7c7b788c]"
                ></i>
            </div>
        </div>

        <slot />
        <!-- Logout -->
        <div class="transition-wrapper">
            <div
                :class="[
                    'absolute top-1/2 z-10 h-[1200px] w-[1200px] -translate-y-1/2 rotate-[45deg] rounded-[100px] bg-[#fc8e8e]',
                    !showYellow ? '-right-[30rem]' : '-right-[100rem]',
                    'transition-all duration-700 ease-in-out',
                ]"
            ></div>
            <div
                :class="[
                    'absolute h-[700px] w-[700px] rotate-[45deg] rounded-[100px] bg-[#ffc5c5]',
                    !showYellow
                        ? '-top-[9rem] right-[8rem]'
                        : '-top-[9rem] right-[-52rem]',
                    'transition-all duration-700 ease-in-out',
                ]"
            ></div>
            <div
                :class="[
                    'absolute top-[4rem] z-50',
                    !showYellow
                        ? 'right-[36rem] opacity-100'
                        : 'right-[-10rem] opacity-0',
                    'transition-all duration-700 ease-in-out',
                ]"
            >
                <i
                    class="fa-solid fa-quote-right text-[12rem] text-[#7c7b788c]"
                ></i>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from "vue";

const showYellow = ref(true);

const handleKeyDown = (e) => {
    if (e.key === "F1") {
        e.preventDefault();
        showYellow.value = true;
    } else if (e.key === "F2") {
        e.preventDefault();
        showYellow.value = false;
    }
};

onMounted(() => {
    window.addEventListener("keydown", handleKeyDown);
});

onUnmounted(() => {
    window.removeEventListener("keydown", handleKeyDown);
});
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

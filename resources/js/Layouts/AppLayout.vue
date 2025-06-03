<template>
    <div class="relative z-0 h-screen overflow-hidden">
        <div class="transition-wrapper">
            <!-- Login -->
            <div :class="[
                'absolute top-1/2 z-10 h-[1200px] w-[1200px] -translate-y-1/2 rotate-[45deg] rounded-[100px] bg-[#fbc04a] -right-[30rem]'
            ]"></div>
            <div :class="[
                'absolute h-[700px] w-[700px] rotate-[45deg] rounded-[100px] bg-[#fbc04a8c] -top-[9rem] right-[8rem]'
            ]"></div>
            <div :class="[
                'absolute top-[4rem] z-40 right-[36rem] opacity-100'
            ]">
                <i class="fa-solid fa-quote-right text-[12rem] text-[#7c7b788c]"></i>
            </div>
        </div>

        <slot />
    </div>
</template>

<script setup>

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

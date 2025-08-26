<template>
    <div class="relative z-0 min-h-screen w-full overflow-hidden">
        <!-- Login -->
        <div :class="[
            'top-[40rem] z-10 hidden h-[1200px] w-[1200px] left-12/12 -translate-y-1/2 -translate-x-1/2 rotate-[45deg] rounded-[140px] bg-[#fbc04a]  xl:block fixed'
        ]"></div>
        <div :class="[
            'fixed -top-[5rem] hidden h-[700px] w-[700px] rotate-[45deg] rounded-[100px] bg-[#fbc04a8c] xl:-top-[10rem] xl:-right-[6rem] xl:block',
        ]"></div>
        <div :class="[
            'fixed top-[6rem] z-40 hidden rotate-[180deg] opacity-100 xl:top-[10rem] xl:right-[36rem] xl:block 2xl:right-[46rem]',
        ]">
            <i class="fa-solid fa-quote-right text-[#7c7b788c] xl:text-[10rem]"></i>
        </div>

        <slot />
    </div>
</template>

<script setup></script>

<style scoped></style>

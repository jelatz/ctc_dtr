<template>
    <div class="relative z-0 h-dvh w-full overflow-hidden">
        <!-- Login -->
        <div
            :class="[
                'fixed top-96 z-10 hidden h-[1200px] w-[1200px] -translate-y-1/2 rotate-[45deg] rounded-[100px] bg-[#fbc04a] xl:-right-[40rem] xl:block 2xl:-right-[25rem]',
            ]"
        ></div>
        <div
            :class="[
                'fixed -top-[2rem] hidden h-[700px] w-[700px] rotate-[45deg] rounded-[100px] bg-[#fbc04a8c] xl:-top-[6rem] xl:-right-[6rem] xl:block 2xl:right-[8rem]',
            ]"
        ></div>
        <div
            :class="[
                'fixed top-[6rem] z-40 hidden rotate-[180deg] opacity-100 xl:top-[10rem] xl:right-[36rem] xl:block 2xl:right-[50rem]',
            ]"
        >
            <i
                class="fa-solid fa-quote-right text-[#7c7b788c] xl:text-[8rem]"
            ></i>
        </div>

        <slot />
    </div>
</template>

<script setup></script>

<style scoped></style>

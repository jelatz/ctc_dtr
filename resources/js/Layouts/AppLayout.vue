<template>
    <div class="relative z-0 h-dvh w-full overflow-hidden">
        <!-- Login -->
        <div
            :class="[
                'absolute top-1/2 z-10 hidden h-[1200px] w-[1200px] -translate-y-1/2 rotate-[45deg] rounded-[100px] bg-[#fbc04a] xl:-right-[40rem] xl:block',
            ]"
        ></div>
        <div
            :class="[
                'absolute -top-[9rem] -right-[6rem] hidden h-[700px] w-[700px] rotate-[45deg] rounded-[100px] bg-[#fbc04a8c] xl:block',
            ]"
        ></div>
        <div
            :class="[
                'absolute top-[6rem] right-[38rem] z-40 hidden opacity-100 xl:top-[8rem] xl:right-[36rem] xl:block',
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

<template>
    <div class="relative overflow-hidden h-screen">
        <div
            class="w-[1200px] h-[1300px] bg-[#fbc04a] rounded-[100px] rotate-[45deg] absolute z-50 -top-40 -left-[30rem]">
        </div>
        <div class="w-[700px] h-[700px] bg-[#fbc04a8c] rounded-[100px] rotate-[45deg] absolute left-[8rem] -top-[9rem]">
        </div>
        <div class="absolute left-[36rem] z-50 top-[4rem]">
            <i class="fa-solid fa-quote-right text-[12rem] text-[#7c7b788c]"></i>
        </div>
        <slot />
    </div>
</template>

<script setup></script>

<style lang="scss" scoped></style>

<template>
    <div
        v-if="show"
        class="bg-opacity-50 fixed inset-0 z-50 flex items-center justify-center bg-[rgba(0,0,0,0.5)]"
        @click.self="closeModal"
    >
        <div
            class="relative w-[90%] rounded-lg bg-white p-5 shadow-lg md:w-[80rem]"
            data-aos="fade-up"
        >
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b pb-3">
                <h2 class="text-lg font-bold">{{ title }}</h2>
                <button
                    @click="closeModal"
                    class="text-red-500 hover:text-red-700"
                >
                    ✕
                </button>
            </div>

            <!-- Modal Body -->
            <div class="mt-4 flex justify-stretch">
                <slot />
            </div>

            <!-- Modal Footer -->
            <div class="mt-4 flex justify-center">
                <button
                    v-if="confirmText"
                    @click="$emit('confirm')"
                    class="rounded bg-blue-950 px-4 py-2 text-xl text-white hover:bg-blue-900"
                >
                    {{ confirmText }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { defineProps, defineEmits } from "vue";

const props = defineProps({
    show: Boolean,
    title: String,
    confirmText: {
        type: String,
        default: "",
    },
});

const emit = defineEmits(["close", "confirm"]);

const closeModal = () => {
    emit("close");
};
</script>

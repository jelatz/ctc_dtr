<template>
  <div class="relative w-screen h-screen overflow-hidden flipped">
    <!-- Slides -->
    <transition name="fade-zoom" mode="out-in" appear>
      <img v-if="slides.length > 0" :src="slides[current]" :alt="'Slide ' + (current + 1)"
        class="absolute inset-0 w-full h-full object-cover" />
    </transition>

    <!-- Prev -->
    <!-- <button
      @click="prev"
      class="absolute left-4 top-1/2 -translate-y-1/2 bg-black/40 text-white p-3 rounded-full hover:bg-black/60"
    >
      &#10094;
    </button> -->

    <!-- Next -->
    <!-- <button
      @click="next"
      class="absolute right-4 top-1/2 -translate-y-1/2 bg-black/40 text-white p-3 rounded-full hover:bg-black/60"
    >
      &#10095;
    </button> -->

    <!-- Dots -->
    <div class="absolute bottom-6 w-full flex justify-center space-x-3">
      <button v-for="(slide, index) in slides" :key="'dot-' + index" @click="goTo(index)" class="w-3 h-3 rounded-full"
        :class="current === index ? 'bg-white' : 'bg-gray-400/60'"></button>
    </div>
  </div>
</template>

<script setup>
import { ref, defineProps, onMounted, onUnmounted } from "vue";

const props = defineProps({
  slides: {
    type: Array,
    required: true,
  },
  autoplay: {
    type: Boolean,
    default: false,
  },
  interval: {
    type: Number,
    default: 5000,
  },
});

const current = ref(0);

const next = () => {
  current.value = (current.value + 1) % props.slides.length;
};

const prev = () => {
  current.value =
    (current.value - 1 + props.slides.length) % props.slides.length;
};

const goTo = (index) => {
  current.value = index;
};

// 🔄 Autoplay
let timer;
onMounted(() => {
  if (props.autoplay) {
    timer = setInterval(next, props.interval);
  }
});
onUnmounted(() => clearInterval(timer));
</script>

<style>
.fade-zoom-enter-active,
.fade-zoom-leave-active {
  transition: all 0.7s ease;
}

.fade-zoom-enter-from {
  opacity: 0;
  transform: scale(1.05);
}

.fade-zoom-leave-to {
  opacity: 0;
  transform: scale(0.95);
}
</style>

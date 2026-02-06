<template>
  <div class="relative w-full h-full bg-gradient-to-br from-slate-50 via-blue-50 to-orange-50 overflow-hidden">
    <!-- Subtle background patterns -->
    <div class="absolute inset-0 overflow-hidden opacity-20">
      <div class="absolute top-20 right-20 w-96 h-96 bg-blue-500/30 rounded-full blur-3xl"></div>
      <div class="absolute bottom-20 left-20 w-96 h-96 bg-orange-500/30 rounded-full blur-3xl"></div>
    </div>

    <!-- Grid pattern -->
    <div class="absolute inset-0 bg-[linear-gradient(to_right,#8080800a_1px,transparent_1px),linear-gradient(to_bottom,#8080800a_1px,transparent_1px)] bg-[size:24px_24px]"></div>

    <!-- Content Container -->
    <div class="relative h-full flex flex-col items-center justify-center px-8 py-12">
      <!-- Slides -->
      <transition name="fade-slide" mode="out-in" appear>
        <div v-if="slides.length > 0" :key="current" class="flex flex-col items-center max-w-2xl w-full">
          
          <!-- Professional Card -->
          <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl p-10 border border-gray-100">
            <!-- Image Section -->
            <div class="relative mx-auto w-fit mb-8">
              <!-- Subtle glow ring -->
              <div class="absolute -inset-3 bg-gradient-to-r from-orange-400/30 via-blue-500/30 to-orange-400/30 rounded-full blur-xl"></div>
              
              <!-- Image container -->
              <div class="relative">
                <img
                  :src="slides[current].src"
                  :alt="'Slide ' + (current + 1)"
                  class="relative w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg ring-2 ring-blue-100/50"
                />
                
                <!-- Position badge -->
                <div class="absolute -bottom-3 left-1/2 -translate-x-1/2 bg-gradient-to-r from-orange-500 to-orange-600 text-white text-xs font-semibold px-4 py-1.5 rounded-full shadow-md whitespace-nowrap">
                  {{ current + 1 }} / {{ slides.length }}
                </div>
              </div>
            </div>

            <!-- Caption -->
            <h2 v-if="slides[current].caption" class="text-center text-gray-900 font-bold text-2xl md:text-3xl tracking-tight mb-3">
              {{ slides[current].caption }}
            </h2>

            <!-- Description -->
            <p v-if="slides[current].description" class="text-center text-gray-600 text-base md:text-lg max-w-xl mx-auto leading-relaxed">
              {{ slides[current].description }}
            </p>
          </div>
        </div>
      </transition>

      <!-- Navigation Dots -->
      <div class="mt-8 flex items-center space-x-2">
        <button
          v-for="(slide, index) in slides"
          :key="'dot-' + index"
          @click="goTo(index)"
          class="group relative transition-all duration-300 focus:outline-none"
          :class="current === index ? 'w-8' : 'w-2.5'"
          :aria-label="'Go to slide ' + (index + 1)"
        >
          <div 
            class="h-2.5 rounded-full transition-all duration-300"
            :class="current === index 
              ? 'bg-gradient-to-r from-orange-500 to-blue-600 shadow-md' 
              : 'bg-gray-300 group-hover:bg-gray-400'"
          ></div>
        </button>
      </div>

      <!-- Progress bar -->
      <div v-if="autoplay" class="mt-6 w-64 max-w-[90vw] h-1 bg-gray-200 rounded-full overflow-hidden">
        <div 
          class="h-full bg-gradient-to-r from-orange-500 via-orange-600 to-blue-600 transition-all duration-300 rounded-full"
          :style="{ width: ((current + 1) / slides.length * 100) + '%' }"
        ></div>
      </div>
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

const goTo = (index) => {
  current.value = index;
};

let timer;
onMounted(() => {
  if (props.autoplay) timer = setInterval(next, props.interval);
});
onUnmounted(() => clearInterval(timer));
</script>

<style scoped>
.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.fade-slide-enter-from {
  opacity: 0;
  transform: translateY(15px) scale(0.98);
}

.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(-15px) scale(0.98);
}
</style>
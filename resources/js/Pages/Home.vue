<template>

  <Head title=" - Home" />
  <div class="flex h-screen pt-96 justify-stretch space-x-[30rem] z-50">
    <div class="z-50 pl-5">
      <Swiper :modules="[Autoplay, Navigation, Pagination]" :slides-per-view="1" :space-between="50" :loop="true"
        :autoplay="{
          delay: 3000,
          disableOnInteraction: false
        }" :pagination="{
          clickable: true
        }" :navigation="true" class="mySwiper">
        <SwiperSlide v-for="(item, index) in items" :key="index">
          <div class="relative w-[70%] mx-auto">
            <img :src="item.image" :alt="'Slide ' + (index + 1)" class="w-20 h-20 rounded-full object-cover" />
            <h3 class="text-xl font-bold">{{ item.title }}</h3>
            <p>{{ item.description }}</p>
          </div>
        </SwiperSlide>
      </Swiper>
    </div>
    <!-- Right side -->
    <div class="w-96">
      <p class="clock text-4xl mb-10 text-center">{{ currentTime }}</p>
      <form action="">
        <label for="" class="block">DTR ID : </label>
        <input type="text" class="border rounded-lg bg-slate-50 w-full h-10 px-5" />
        <button type="submit" class="bg-[#fbc04a] w-full rounded-md mt-5 py-2">Login</button>
      </form>

      <div class="mt-20">
        <button class="w-full rounded-md py-2 text-red-600 text-xl">F2: Logout</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Swiper, SwiperSlide } from 'swiper/vue'
import 'swiper/swiper-bundle.css'
import { Autoplay, Navigation, Pagination } from 'swiper/modules'

// Updated data with both images and text
const items = [
  {
    image: '/images/slide1.jpg',
    title: 'Slide 1 Title',
    description: 'Short description for slide 1'
  },
  {
    image: '/images/slide2.jpg',
    title: 'Slide 2 Title',
    description: 'Short description for slide 2'
  },
  {
    image: '/images/slide3.jpg',
    title: 'Slide 3 Title',
    description: 'Short description for slide 3'
  }
]

import { ref, onMounted, onBeforeUnmount } from 'vue'

const currentTime = ref(new Date(new Date().getTime() + 5 * 60000).toLocaleTimeString())


let clockInterval = null

onMounted(() => {
  // Update the time every second
  clockInterval = setInterval(() => {
    currentTime.value = new Date(new Date().getTime() + 5 * 60000).toLocaleTimeString()
  }, 1000)
})

onBeforeUnmount(() => {
  // Clean up the interval when component is unmounted
  if (clockInterval) clearInterval(clockInterval)
})
</script>

<style scoped>
.mySwiper {
  width: 50rem;
  height: 300px;
}

/* Change color of next button */
:deep(.swiper-button-next) {
  color: #000;
}

/* Change color of prev button */
:deep(.swiper-button-prev) {
  color: #000;
}

:deep(.swiper-pagination-bullet-active) {
  background: #000;
}
.clock {
  text-shadow: 0 15px 3px #36363615
}
</style>
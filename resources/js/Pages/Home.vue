<template>

  <Head title=" - Home" />
  <div class="absolute z-50 top-[35rem] -translate-y-1/2 left-[3rem]">
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
    <div class="w-[30rem] flex justify-between items-center mt-20">
      <button class="text-left"><strong>F1</strong><span class="block">Login</span></button>
      <button class="text-left"><strong>F2</strong><span class="block">Logout</span></button>
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
</style>
<template>
    <div class="flex flex-col items-center gap-1.5">
        <svg :width="size" :height="size" :viewBox="`0 0 ${size} ${size}`">
            <defs>
                <filter :id="`aCkShadow_${uid}`">
                    <feDropShadow dx="0" dy="1" stdDeviation="2" flood-color="rgba(0,0,0,0.12)" />
                </filter>
                <filter :id="`aCkHandShadow_${uid}`">
                    <feDropShadow dx="0" dy="1" stdDeviation="1" flood-color="rgba(0,0,0,0.10)" />
                </filter>
            </defs>

            <!-- Outer ring (light border) -->
            <circle :cx="cx" :cy="cy" :r="r + 4" fill="#e2e8f0" :filter="`url(#aCkShadow_${uid})`" />
            <circle :cx="cx" :cy="cy" :r="r + 2" fill="#f1f5f9" />

            <!-- Face (white) -->
            <circle :cx="cx" :cy="cy" :r="r" fill="#ffffff" />

            <!-- Hour ticks -->
            <g v-for="i in 12" :key="`h${i}`">
                <line
                    :x1="cx + (r - 2) * Math.cos(toRad(i * 30 - 90))"
                    :y1="cy + (r - 2) * Math.sin(toRad(i * 30 - 90))"
                    :x2="cx + (r - 7) * Math.cos(toRad(i * 30 - 90))"
                    :y2="cy + (r - 7) * Math.sin(toRad(i * 30 - 90))"
                    stroke="#64748b"
                    :stroke-width="size * 0.022"
                    stroke-linecap="round"
                />
            </g>

            <!-- Minute ticks -->
            <g v-for="i in 60" :key="`m${i}`" v-if="i % 5 !== 0">
                <line
                    :x1="cx + (r - 2) * Math.cos(toRad(i * 6 - 90))"
                    :y1="cy + (r - 2) * Math.sin(toRad(i * 6 - 90))"
                    :x2="cx + (r - 5) * Math.cos(toRad(i * 6 - 90))"
                    :y2="cy + (r - 5) * Math.sin(toRad(i * 6 - 90))"
                    stroke="#cbd5e1"
                    stroke-width="0.8"
                />
            </g>

            <!-- Hour hand (dark blue — matches button gradient) -->
            <line
                :x1="cx - hourLen * 0.18 * Math.cos(hourAngle)"
                :y1="cy - hourLen * 0.18 * Math.sin(hourAngle)"
                :x2="cx + hourLen * Math.cos(hourAngle)"
                :y2="cy + hourLen * Math.sin(hourAngle)"
                stroke="#1e3a5f"
                :stroke-width="size * 0.042"
                stroke-linecap="round"
                :filter="`url(#aCkHandShadow_${uid})`"
            />

            <!-- Minute hand (slate) -->
            <line
                :x1="cx - minuteLen * 0.14 * Math.cos(minuteAngle)"
                :y1="cy - minuteLen * 0.14 * Math.sin(minuteAngle)"
                :x2="cx + minuteLen * Math.cos(minuteAngle)"
                :y2="cy + minuteLen * Math.sin(minuteAngle)"
                stroke="#475569"
                :stroke-width="size * 0.028"
                stroke-linecap="round"
                :filter="`url(#aCkHandShadow_${uid})`"
            />

            <!-- Second hand (orange — matches warm gradient accent) -->
            <line
                :x1="cx - secondLen * 0.25 * Math.cos(secondAngle)"
                :y1="cy - secondLen * 0.25 * Math.sin(secondAngle)"
                :x2="cx + secondLen * Math.cos(secondAngle)"
                :y2="cy + secondLen * Math.sin(secondAngle)"
                stroke="#ea580c"
                :stroke-width="size * 0.013"
                stroke-linecap="round"
            />

            <!-- Center dot -->
            <circle :cx="cx" :cy="cy" :r="size * 0.04" fill="#1e3a5f" />
            <circle :cx="cx" :cy="cy" :r="size * 0.016" fill="#ea580c" />
        </svg>

        <!-- Label -->
        <div class="text-center leading-tight">
            <p class="text-[14px] font-bold text-black tracking-widest uppercase">{{ label }}</p>
            <p v-if="fullTimezoneName" class="text-[14px] font-semibold text-black">{{ fullTimezoneName }}</p>
            <p class="text-[14px] font-medium text-black tabular-nums">{{ digitalTime }}</p>
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    serverTimestamp: { type: Number, required: true },
    timezone:        { type: String, required: true },
    label:           { type: String, required: true },
    size:            { type: Number, default: 75 },
});

// Unique ID per instance to avoid SVG filter collisions
const uid = Math.random().toString(36).slice(2, 8);

const toRad = (deg) => (deg * Math.PI) / 180;

const cx = computed(() => props.size / 2);
const cy = computed(() => props.size / 2);
const r  = computed(() => props.size / 2 - 7);

const hourLen   = computed(() => r.value * 0.50);
const minuteLen = computed(() => r.value * 0.72);
const secondLen = computed(() => r.value * 0.78);

const timeParts = computed(() => {
    if (!props.serverTimestamp) return { h: 0, m: 0, s: 0 };
    const d = new Date(props.serverTimestamp);
    const parts = new Intl.DateTimeFormat("en-US", {
        timeZone: props.timezone,
        hour: "numeric",
        minute: "2-digit",
        second: "2-digit",
        hour12: false,
    }).formatToParts(d);
    const get = (type) => parseInt(parts.find((p) => p.type === type)?.value ?? "0", 10);
    return { h: get("hour") % 12, m: get("minute"), s: get("second") };
});

const hourAngle   = computed(() => toRad(timeParts.value.h * 30 + timeParts.value.m * 0.5 - 90));
const minuteAngle = computed(() => toRad(timeParts.value.m * 6  + timeParts.value.s * 0.1 - 90));
const secondAngle = computed(() => toRad(timeParts.value.s * 6  - 90));

const digitalTime = computed(() => {
    if (!props.serverTimestamp) return "--:--";
    return new Date(props.serverTimestamp).toLocaleTimeString("en-US", {
        timeZone: props.timezone,
        hour: "2-digit",
        minute: "2-digit",
        hour12: true,
    });
});

const timezoneFullNames = {
    ET: "Eastern Time",
    CT: "Central Time",
    MT: "Mountain Time",
    PT: "Pacific Time",
};

const fullTimezoneName = computed(() => timezoneFullNames[props.label?.toUpperCase()] ?? null);
</script>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? 'flex items-center w-full text-sm font-semibold p-2 bg-gray-900 text-white rounded-md transition-colors duration-200'
        : 'flex items-center w-full text-sm font-medium p-2 text-gray-300 rounded-md hover:bg-gray-700 hover:text-white transition-colors duration-200'
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
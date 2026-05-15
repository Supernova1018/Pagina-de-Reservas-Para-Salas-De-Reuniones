<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    active: Boolean,
    href: String,
    as: String,
});

const classes = computed(() => {
    return props.active
    ? 'block w-full rounded-xl border-l-4 border-violet-400 bg-violet-500/10 px-3 py-2 text-start text-base font-medium text-violet-100 transition duration-150 ease-in-out focus:bg-violet-500/15 focus:text-white focus:outline-none'
    : 'block w-full rounded-xl border-l-4 border-transparent px-3 py-2 text-start text-base font-medium text-slate-300 transition duration-150 ease-in-out hover:border-violet-400/30 hover:bg-white/5 hover:text-white focus:border-violet-400/30 focus:bg-white/5 focus:text-white focus:outline-none';
});
</script>

<template>
    <div>
        <button v-if="as == 'button'" :class="classes" class="w-full text-start">
            <slot />
        </button>

        <a v-else-if="as == 'a'" :class="classes" class="w-full text-start" :href="href">
            <slot />
        </a>

        <Link v-else :href="href" :class="classes">
            <slot />
        </Link>
    </div>
</template>

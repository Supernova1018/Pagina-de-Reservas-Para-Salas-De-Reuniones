<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: String,
    as: String,
});
</script>

<template>
    <div>
        <button v-if="as == 'button'" type="submit" class="block w-full px-4 py-2 text-start text-sm leading-5 text-slate-200 transition duration-150 ease-in-out hover:bg-white/5 focus:bg-white/5">
            <slot />
        </button>

        <a v-else-if="as =='a'" :href="href" class="block px-4 py-2 text-sm leading-5 text-slate-200 transition duration-150 ease-in-out hover:bg-white/5 focus:bg-white/5">
            <slot />
        </a>

        <Link v-else :href="href" class="block px-4 py-2 text-sm leading-5 text-slate-200 transition duration-150 ease-in-out hover:bg-white/5 focus:bg-white/5">
            <slot />
        </Link>
    </div>
</template>

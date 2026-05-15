<script setup>
import { computed, ref } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';

const props = defineProps({
    spaces: Array,
    filters: Object,
});

const typeFilter     = ref(props.filters?.type || '');
const capacityFilter = ref(props.filters?.capacity || '');

function applyFilters() {
    router.get('/', {
        type:     typeFilter.value     || undefined,
        capacity: capacityFilter.value || undefined,
    }, { preserveState: true });
}

function clearFilters() {
    typeFilter.value     = '';
    capacityFilter.value = '';
    router.get('/');
}

function formatPrice(price) {
    if (!price || parseFloat(price) === 0) return 'Gratuita';
    return '$' + Number(price).toLocaleString('es-CO') + '/hora';
}

const typeOptions = [
    { value: '', label: 'Todos los tipos' },
    { value: 'university', label: 'Universitaria' },
    { value: 'corporate',  label: 'Corporativa'   },
];

const page = usePage();

const currentUser = computed(() => page.props.auth?.user ?? null);
const isAdmin = computed(() => currentUser.value?.is_admin === true);

function logout() {
    router.post(route('logout'));
}
</script>

<template>
    <Head title="Salas disponibles" />

    <div class="min-h-screen bg-slate-900 text-slate-200">

        <!-- Hero -->
        <header class="border-b border-violet-700/20 bg-slate-900/40 shadow-sm backdrop-blur">
            <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-6 sm:px-6 lg:px-8 md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-white">Reserva tu sala</h1>
                    <p class="mt-1 text-sm text-slate-400">Espacios universitarios y corporativos disponibles</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <div v-if="currentUser" class="inline-flex items-center gap-2 rounded-full border border-violet-400/20 bg-violet-500/10 px-3 py-2 text-sm text-violet-100">
                        <span class="text-xs font-semibold uppercase tracking-[0.2em] text-violet-200/80">Sesión</span>
                        <span class="max-w-[180px] truncate font-semibold">{{ currentUser.name }}</span>
                    </div>

                    <Link
                        v-if="currentUser && isAdmin"
                        :href="route('admin.dashboard')"
                        class="inline-flex items-center gap-2 rounded-lg border border-white/10 px-3 py-2 text-sm text-slate-200 transition hover:border-violet-400/30 hover:text-white"
                    >
                        Panel
                    </Link>
                    <Link
                        v-else-if="currentUser"
                        :href="route('public.reservations.mine')"
                        class="inline-flex items-center gap-2 rounded-lg border border-white/10 px-3 py-2 text-sm text-slate-200 transition hover:border-violet-400/30 hover:text-white"
                    >
                        Mis reservas
                    </Link>
                    <Link
                        v-if="!currentUser"
                        :href="route('login')"
                        class="inline-flex items-center gap-2 rounded-lg border border-white/10 bg-violet-600 px-3 py-2 text-sm font-semibold text-white transition hover:bg-violet-700"
                    >
                        Iniciar sesión
                    </Link>
                    <Link
                        v-if="!currentUser"
                        :href="route('register')"
                        class="inline-flex items-center gap-2 rounded-lg border border-white/10 px-3 py-2 text-sm text-slate-200 transition hover:border-violet-400/30 hover:text-white"
                    >
                        Crear cuenta
                    </Link>
                    <button
                        v-if="currentUser"
                        @click="logout"
                        class="inline-flex items-center gap-2 rounded-lg border border-white/10 px-3 py-2 text-sm text-slate-200 transition hover:border-rose-400/30 hover:text-rose-100"
                    >
                        Cerrar sesión
                    </button>
                </div>
            </div>
        </header>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

            <!-- Filters -->
            <div class="mb-8 grid grid-cols-1 gap-4 rounded-3xl border border-white/10 bg-slate-900/85 p-4 shadow-2xl shadow-violet-950/20 ring-1 ring-white/5 sm:grid-cols-2 xl:grid-cols-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-400">Tipo de sala</label>
                    <select
                        v-model="typeFilter"
                        class="rounded-lg border border-white/10 bg-slate-800/70 px-3 py-2 text-sm text-slate-100 focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20"
                    >
                        <option v-for="opt in typeOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-400">Capacidad mínima</label>
                    <input
                        v-model="capacityFilter"
                        type="number"
                        min="1"
                        placeholder="Ej: 10"
                        class="w-32 rounded-lg border border-white/10 bg-slate-800/70 px-3 py-2 text-sm text-slate-100 placeholder-slate-500 focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20"
                    />
                </div>
                <button
                    @click="applyFilters"
                    class="rounded-lg bg-violet-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-violet-700 sm:self-end"
                >
                    Filtrar
                </button>
                <button
                    v-if="typeFilter || capacityFilter"
                    @click="clearFilters"
                    class="rounded-lg border border-white/10 px-3 py-2 text-sm text-slate-300 transition hover:border-violet-400/30 hover:text-white sm:self-end"
                >
                    Limpiar
                </button>
            </div>

            <!-- Spaces grid -->
            <div v-if="spaces.length === 0" class="py-20 text-center text-slate-400">
                <p class="text-lg font-medium">No hay salas que coincidan con los filtros.</p>
                <button @click="clearFilters" class="mt-4 text-sm text-violet-200 hover:text-white hover:underline">Ver todas</button>
            </div>

            <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                <article
                    v-for="space in spaces"
                    :key="space.id"
                    class="overflow-hidden rounded-3xl border border-white/10 bg-slate-900/85 shadow-2xl shadow-violet-950/20 transition hover:border-violet-400/30 hover:shadow-violet-950/30 group"
                >
                    <div class="h-44 bg-cover bg-center" :style="space.image ? { backgroundImage: `url(${space.image})` } : undefined"></div>

                    <div class="p-5">
                        <!-- Header -->
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <span
                                    class="inline-block text-xs font-bold px-2 py-0.5 rounded-full mb-2"
                                    :class="space.type === 'university'
                                        ? 'bg-violet-500/15 text-violet-200'
                                        : 'bg-emerald-500/15 text-emerald-200'"
                                >
                                    {{ space.type === 'university' ? 'Universitaria' : 'Corporativa' }}
                                </span>
                                <h2 class="text-lg font-bold leading-tight text-white">{{ space.name }}</h2>
                            </div>
                            <span class="ml-2 whitespace-nowrap rounded-lg bg-violet-500/15 px-2 py-1 text-sm font-semibold text-violet-100">
                                {{ formatPrice(space.price_per_hour) }}
                            </span>
                        </div>

                        <div class="mb-3 flex gap-4 text-sm text-slate-400">
                            <span>{{ space.capacity }} personas</span>
                            <span v-if="space.location" class="truncate">{{ space.location }}</span>
                        </div>

                        <p class="mb-4 line-clamp-3 text-sm leading-relaxed text-slate-300">
                            {{ space.description }}
                        </p>

                        <!-- Action -->
                        <Link
                            :href="'/spaces/' + space.slug"
                            class="block w-full rounded-lg bg-violet-600 px-4 py-2.5 text-center text-sm font-semibold text-white transition hover:bg-violet-700"
                        >
                            Ver disponibilidad
                        </Link>
                    </div>
                </article>
            </div>
        </div>
    </div>
</template>
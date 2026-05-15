<script setup>
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const page = usePage();
const currentUser = computed(() => page.props.auth?.user ?? null);

const quickActions = [
    {
        title: 'Explorar salas',
        description: 'Busca la sala que mejor se ajuste al tamaño y al horario que necesitas.',
        href: route('home'),
        tone: 'from-violet-500/20 to-fuchsia-500/20 text-violet-100',
    },
    {
        title: 'Nueva reserva',
        description: 'Abre el formulario y solicita tu espacio en minutos.',
        href: route('public.reservations.create'),
        tone: 'from-cyan-500/20 to-sky-500/20 text-cyan-100',
    },
    {
        title: 'Mis reservas',
        description: 'Consulta tus reservas activas y cancela las que ya no necesites.',
        href: route('public.reservations.mine'),
        tone: 'from-emerald-500/20 to-teal-500/20 text-emerald-100',
    },
];

const highlights = computed(() => [
    {
        title: 'Sesión activa',
        value: currentUser.value?.name ?? 'Invitado',
        hint: 'Tu acceso se mantiene entre páginas.',
        tone: 'from-violet-500/20 to-fuchsia-500/20 text-violet-100',
    },
    {
        title: 'Reservas',
        value: 'Confirmadas',
        hint: 'El flujo es directo y sin aprobación manual.',
        tone: 'from-cyan-500/20 to-sky-500/20 text-cyan-100',
    },
    {
        title: 'Acceso rápido',
        value: 'Salas',
        hint: 'Todo el catálogo está a un clic.',
        tone: 'from-emerald-500/20 to-teal-500/20 text-emerald-100',
    },
]);
</script>

<template>
    <Head title="Inicio" />
    <AppLayout title="Inicio">
        <template #header>
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-violet-300">Inicio</p>
                    <h2 class="mt-2 text-3xl font-bold text-slate-50">Bienvenido, {{ currentUser?.name || 'usuario' }}</h2>
                    <p class="mt-2 max-w-2xl text-sm text-slate-400">Desde aquí puedes revisar salas disponibles, crear una reserva y consultar tu historial sin ver el panel administrativo.</p>
                </div>

                <Link href="/" class="rounded-2xl border border-violet-400/30 bg-violet-500/10 px-4 py-2.5 text-sm font-semibold text-violet-100 transition hover:bg-violet-500/20">
                    Ver salas disponibles
                </Link>
            </div>
        </template>

        <div class="px-4 py-8 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl space-y-8">
                <div class="grid gap-4 md:grid-cols-3">
                    <div v-for="item in highlights" :key="item.title" class="rounded-3xl border border-white/10 bg-slate-900/80 p-5 shadow-2xl shadow-violet-950/20 ring-1 ring-white/5">
                        <div class="inline-flex rounded-full bg-gradient-to-r px-3 py-1 text-xs font-semibold" :class="item.tone">{{ item.title }}</div>
                        <div class="mt-4 text-3xl font-bold text-white">{{ item.value }}</div>
                        <div class="mt-2 text-sm text-slate-400">{{ item.hint }}</div>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-3">
                    <Link v-for="action in quickActions" :key="action.title" :href="action.href" class="rounded-3xl border border-white/10 bg-slate-900/85 p-6 shadow-2xl shadow-violet-950/20 ring-1 ring-white/5 transition hover:border-violet-400/30 hover:bg-slate-900">
                        <div class="inline-flex rounded-full bg-gradient-to-r px-3 py-1 text-xs font-semibold" :class="action.tone">{{ action.title }}</div>
                        <p class="mt-4 text-sm leading-6 text-slate-300">{{ action.description }}</p>
                        <div class="mt-5 text-sm font-semibold text-violet-200">Abrir</div>
                    </Link>
                </div>

                <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-8 shadow-2xl shadow-violet-950/20 ring-1 ring-white/5">
                    <div class="grid gap-4 md:grid-cols-3">
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-5 text-slate-200">Tu sesión ya está guardada entre páginas y puedes cerrar sesión cuando quieras.</div>
                        <div class="rounded-2xl border border-violet-400/20 bg-violet-500/10 p-5 text-violet-100">Revisa salas y reservas desde el mismo flujo, sin panel de administrador.</div>
                        <div class="rounded-2xl border border-fuchsia-400/20 bg-fuchsia-500/10 p-5 text-fuchsia-100">Si cambias de cuenta, usa cerrar sesión antes de iniciar con otra credencial.</div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

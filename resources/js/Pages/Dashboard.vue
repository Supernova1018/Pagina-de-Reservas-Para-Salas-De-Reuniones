<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-violet-300">Inicio</p>
                <h2 class="mt-2 text-3xl font-bold text-slate-50">Bienvenido al panel</h2>
                <p class="mt-2 text-sm text-slate-400">Acceso rápido a reservas, salas y disponibilidad.</p>
            </div>
        </template>

        <div class="px-4 py-8 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-8 shadow-2xl shadow-violet-950/20 ring-1 ring-white/5">
                    <div class="grid gap-4 md:grid-cols-3">
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-5 text-slate-200">La estructura visual ahora coincide con el panel administrativo oscuro.</div>
                        <div class="rounded-2xl border border-violet-400/20 bg-violet-500/10 p-5 text-violet-100">Revisa el calendario para ver la disponibilidad por sala.</div>
                        <div class="rounded-2xl border border-fuchsia-400/20 bg-fuchsia-500/10 p-5 text-fuchsia-100">Puedes usar esta base para nuevas métricas o alertas.</div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    reservation: Object,
    feedback: Object,
    message: String,
});

const form = useForm({
    service_satisfactory: props.feedback?.service_satisfactory ?? true,
    space_met_expectations: props.feedback?.space_met_expectations ?? true,
    overall_score: props.feedback?.overall_score ?? 5,
    comment: props.feedback?.comment ?? '',
});

const completed = computed(() => props.reservation.status === 'completed' || new Date(props.reservation.end_time) <= new Date());

const scoreLabels = {
    1: 'Muy baja',
    2: 'Baja',
    3: 'Aceptable',
    4: 'Buena',
    5: 'Excelente',
};

function submit() {
    form.post(route('public.reservations.feedback.store', props.reservation.slug));
}
</script>

<template>
    <Head title="Calificar reserva" />

    <div class="min-h-screen bg-slate-950 text-slate-100">
        <div class="absolute inset-0 -z-10 overflow-hidden pointer-events-none">
            <div class="absolute -top-24 right-0 h-80 w-80 rounded-full bg-violet-600/15 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 h-96 w-96 rounded-full bg-rose-500/10 blur-3xl"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(168,85,247,0.12),transparent_26%),radial-gradient(circle_at_top_right,rgba(244,114,182,0.08),transparent_22%),linear-gradient(to_bottom,rgba(15,23,42,1),rgba(2,6,23,0.98))]"></div>
        </div>

        <div class="mx-auto flex min-h-screen max-w-5xl items-center px-4 py-10 sm:px-6 lg:px-8">
            <div class="grid w-full gap-6 lg:grid-cols-[1.1fr_0.9fr]">
                <section class="rounded-[2rem] border border-violet-400/15 bg-slate-900/80 p-6 shadow-2xl shadow-violet-950/20 backdrop-blur-xl sm:p-8">
                    <div class="inline-flex rounded-full border border-violet-400/20 bg-violet-500/10 px-3 py-1 text-xs font-semibold text-violet-100">
                        Valora tu experiencia
                    </div>
                    <h1 class="mt-4 text-3xl font-bold text-white sm:text-4xl">Cuéntanos cómo salió la reserva</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300">
                        Tu opinión ayuda a mejorar la atención, el proceso de reserva y la experiencia real en la sala.
                    </p>

                    <div v-if="message" class="mt-6 rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100">
                        {{ message }}
                    </div>

                    <div class="mt-6 rounded-2xl border border-white/10 bg-white/5 p-4 text-sm text-slate-200">
                        <div class="font-semibold text-violet-100">Reserva</div>
                        <div class="mt-1">{{ reservation.space.name }}</div>
                        <div class="mt-1 text-slate-400">{{ reservation.start_time }} → {{ reservation.end_time }}</div>
                    </div>

                    <form class="mt-6 space-y-5" @submit.prevent="submit">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <button
                                type="button"
                                @click="form.service_satisfactory = true"
                                class="rounded-2xl border px-4 py-4 text-left transition"
                                :class="form.service_satisfactory ? 'border-violet-400/40 bg-violet-500/15 text-white' : 'border-white/10 bg-white/5 text-slate-300 hover:bg-white/10'"
                            >
                                <div class="text-sm font-semibold">¿El servicio de reserva fue satisfactorio?</div>
                                <div class="mt-1 text-xs text-slate-400">Respuesta positiva</div>
                            </button>
                            <button
                                type="button"
                                @click="form.service_satisfactory = false"
                                class="rounded-2xl border px-4 py-4 text-left transition"
                                :class="!form.service_satisfactory ? 'border-rose-400/40 bg-rose-500/15 text-white' : 'border-white/10 bg-white/5 text-slate-300 hover:bg-white/10'"
                            >
                                <div class="text-sm font-semibold">¿Hubo algo por mejorar?</div>
                                <div class="mt-1 text-xs text-slate-400">Respuesta negativa</div>
                            </button>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <button
                                type="button"
                                @click="form.space_met_expectations = true"
                                class="rounded-2xl border px-4 py-4 text-left transition"
                                :class="form.space_met_expectations ? 'border-violet-400/40 bg-violet-500/15 text-white' : 'border-white/10 bg-white/5 text-slate-300 hover:bg-white/10'"
                            >
                                <div class="text-sm font-semibold">¿La sala cumplió tus expectativas?</div>
                                <div class="mt-1 text-xs text-slate-400">Sí, la experiencia fue correcta</div>
                            </button>
                            <button
                                type="button"
                                @click="form.space_met_expectations = false"
                                class="rounded-2xl border px-4 py-4 text-left transition"
                                :class="!form.space_met_expectations ? 'border-rose-400/40 bg-rose-500/15 text-white' : 'border-white/10 bg-white/5 text-slate-300 hover:bg-white/10'"
                            >
                                <div class="text-sm font-semibold">¿La sala quedó por debajo de lo esperado?</div>
                                <div class="mt-1 text-xs text-slate-400">No totalmente</div>
                            </button>
                        </div>

                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <div class="text-sm font-semibold text-white">Calificación general</div>
                                    <div class="text-xs text-slate-400">{{ scoreLabels[form.overall_score] }}</div>
                                </div>
                                <div class="text-right text-sm text-violet-100">{{ form.overall_score }}/5</div>
                            </div>
                            <div class="mt-4 grid grid-cols-5 gap-2">
                                <button v-for="score in 5" :key="score" type="button" @click="form.overall_score = score" class="rounded-xl border px-3 py-3 text-sm font-semibold transition" :class="form.overall_score === score ? 'border-violet-400/40 bg-violet-500/15 text-white' : 'border-white/10 bg-slate-950/60 text-slate-300 hover:bg-white/10'">
                                    {{ score }}
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-200">Comentarios opcionales</label>
                            <textarea v-model="form.comment" rows="4" class="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-sm text-slate-100 placeholder:text-slate-500 focus:border-violet-400 focus:ring-2 focus:ring-violet-500/20" placeholder="¿Qué fue lo mejor? ¿Qué mejorarías?"></textarea>
                            <p v-if="form.errors.comment" class="mt-1 text-xs text-rose-300">{{ form.errors.comment }}</p>
                        </div>

                        <div class="flex flex-col gap-3 sm:flex-row">
                            <button type="submit" :disabled="form.processing || !completed" class="inline-flex flex-1 items-center justify-center rounded-2xl bg-violet-500 px-5 py-3 text-sm font-semibold text-white transition hover:bg-violet-400 disabled:cursor-not-allowed disabled:opacity-50">
                                {{ form.processing ? 'Enviando...' : 'Enviar valoración' }}
                            </button>
                            <Link :href="route('public.reservations.confirmation', reservation.slug)" class="inline-flex items-center justify-center rounded-2xl border border-white/10 bg-white/5 px-5 py-3 text-sm font-medium text-slate-200 transition hover:bg-white/10">
                                Volver
                            </Link>
                        </div>

                        <p v-if="!completed" class="rounded-2xl border border-amber-400/20 bg-amber-500/10 px-4 py-3 text-sm text-amber-100">
                            Puedes completar esta encuesta cuando termine el horario reservado.
                        </p>
                    </form>
                </section>

                <aside class="space-y-4 rounded-[2rem] border border-white/10 bg-slate-900/70 p-6 shadow-2xl shadow-violet-950/20 backdrop-blur-xl sm:p-8">
                    <h2 class="text-lg font-bold text-white">Lo que vamos a medir</h2>
                    <div class="space-y-3 text-sm text-slate-300">
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">Satisfacción del servicio de reserva.</div>
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">Si la sala cumplió tus expectativas.</div>
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">Una calificación general para detectar mejoras.</div>
                    </div>
                    <div class="rounded-2xl border border-rose-400/20 bg-rose-500/10 p-4 text-sm text-rose-100">
                        Los comentarios se guardan junto a la reserva y quedan visibles en administración.
                    </div>
                </aside>
            </div>
        </div>
    </div>
</template>
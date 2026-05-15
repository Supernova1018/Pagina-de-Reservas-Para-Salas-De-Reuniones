<script setup>
defineProps({
    feedback: Object,
});
</script>

<template>
    <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-6 shadow-2xl shadow-violet-950/20 ring-1 ring-white/5">
        <h3 class="text-lg font-bold text-white">Valoración del usuario</h3>
        <div class="mt-4 grid gap-3 sm:grid-cols-2">
            <div class="rounded-2xl border border-white/10 bg-white/5 p-4 text-sm text-slate-200">
                <div class="text-xs uppercase tracking-wide text-slate-400">Servicio</div>
                <div class="mt-1 font-semibold text-violet-100">{{ feedback.service_satisfactory ? 'Satisfactorio' : 'A mejorar' }}</div>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/5 p-4 text-sm text-slate-200">
                <div class="text-xs uppercase tracking-wide text-slate-400">Sala</div>
                <div class="mt-1 font-semibold text-violet-100">{{ feedback.space_met_expectations ? 'Cumplió expectativas' : 'No cumplió del todo' }}</div>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/5 p-4 text-sm text-slate-200 sm:col-span-2">
                <div class="text-xs uppercase tracking-wide text-slate-400">Puntuación general</div>
                <div class="mt-1 text-xl font-bold text-white">{{ feedback.overall_score }}/5</div>
            </div>
            <div v-if="feedback.comment" class="rounded-2xl border border-white/10 bg-white/5 p-4 text-sm text-slate-200 sm:col-span-2">
                <div class="text-xs uppercase tracking-wide text-slate-400">Comentario</div>
                <div class="mt-1 whitespace-pre-line text-slate-300">{{ feedback.comment }}</div>
            </div>
        </div>
    </div>
</template>
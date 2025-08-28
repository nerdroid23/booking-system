<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Service } from '@/types/generated';

defineProps<{ service: Service }>();
</script>

<template>
  <Link
    class="flex flex-col items-center justify-center space-y-1 rounded-lg border border-slate-200 px-4 py-8 text-center shadow-sm hover:bg-gray-50/75"
  >
    <p class="text-sm font-medium text-slate-600">{{ service.title }}</p>
    <p class="text-sm font-medium text-slate-400">{{ service.duration }} minutes</p>
    <p class="mt-2 rounded-lg bg-slate-200 p-1.5 text-xs font-medium text-slate-600">{{ service.price }}</p>
  </Link>
</template>

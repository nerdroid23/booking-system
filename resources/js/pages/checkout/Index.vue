<script setup lang="ts">
import Layout from '@/layouts/HomeLayout.vue';
import { Availability, Employee, Service } from '@/types/generated';
import { easepick, LockPlugin } from '@easepick/bundle';
import styles from '@easepick/bundle/dist/index.css?url';
import { type Core } from '@easepick/core/dist/core';
import { Head, Link, router } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

defineOptions({ layout: Layout });

const props = defineProps<{
  // TODO: use deferred prop
  availability: Array<Availability>;
  employee: Employee | null;
  service: Service;
  date: string | null;
  start: string | null;
}>();

let picker: Core | null = null;
const pickerRef = ref<HTMLElement | null>(null);

onMounted(() => {
  createPicker();

  picker?.on('render', (event: CustomEvent) => {
    if (event.detail.view === 'Container' && event.detail.date.format('YYYY-MM-DD').toString() !== props.start) {
      router.reload({
        data: { start: event.detail.date.format('YYYY-MM-DD') },
        only: ['availability', 'start', 'date'],
        onSuccess() {
          picker?.renderAll();
        },
      });
    }
  });
});

const createPicker = () => {
  picker = new easepick.create({
    css: [styles],
    element: pickerRef?.value as HTMLElement,
    readonly: true,
    zIndex: 50,
    date: props.date ?? new Date(),
    plugins: [LockPlugin],
    LockPlugin: {
      minDate: new Date(),
      filter(date) {
        if (Array.isArray(date)) {
          return true;
        }

        return !props.availability?.find((a) => a.date === date.format('YYYY-MM-DD'));
      },
    },
  });
};
</script>

<template>
  <Head title="Checkout" />

  <Link
    :href="route('home')"
    class="text-xs text-blue-500"
  >
    &larr; Go back
  </Link>

  <form class="mt-6 space-y-10">
    <div>
      <h2 class="text-2xl font-medium">Here's what you're booking</h2>
      <div class="mt-6 flex space-x-3 bg-slate-100 p-4">
        <img
          v-if="employee"
          :src="employee.avatar_url"
          class="size-12 rounded-lg"
          alt=""
        />
        <div
          v-else
          class="size-12 rounded-lg bg-slate-200"
        ></div>
        <div class="flex w-full justify-between">
          <div>
            <div class="font-semibold">{{ service.title }} ({{ service.duration }} mins)</div>
            <div>{{ employee?.name ?? 'Any employee' }}</div>
          </div>
          <div>{{ service.price }}</div>
        </div>
      </div>
    </div>

    <div>
      <h2 class="text-xl font-medium">1. Choose a date</h2>
      <input
        ref="pickerRef"
        class="mt-6 w-full border-0 bg-slate-100 p-4 px-6 py-4 text-sm"
        placeholder="When for?"
      />
    </div>

    <div>
      <h2 class="text-xl font-medium">2. Choose a time</h2>
      <div class="mt-6 p-4">Slots</div>
    </div>

    <div>
      <h2 class="text-xl font-medium">3. Enter your details</h2>
      <div class="mt-6 p-4">Slots</div>
    </div>
  </form>
</template>

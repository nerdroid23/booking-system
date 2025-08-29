<script setup lang="ts">
import { Availability } from '@/types/generated';
import { easepick, LockPlugin } from '@easepick/bundle';
import styles from '@easepick/bundle/dist/index.css?url';
import { type Core } from '@easepick/core/dist/core';
import { router } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import customStyles from '../../css/vendor/easepick.css?url';

const props = defineProps<{
  availability: Array<Availability>;
  date: string | null;
  start: string | null;
}>();

const $emit = defineEmits<{ (e: 'updateSlots', date: string | null): void }>();

let picker: Core | null = null;
const pickerRef = ref<HTMLElement | null>(null);

onMounted(() => {
  createPicker();

  picker?.on('select', (event: CustomEvent) => {
    const date = event.detail.date?.format('YYYY-MM-DD') ?? null;
    $emit('updateSlots', date);
  });

  picker?.on('render', (event: CustomEvent) => {
    if (event.detail.view === 'Container' && event.detail.date.format('YYYY-MM-DD').toString() !== props.start) {
      router.reload({
        data: { start: event.detail.date.format('YYYY-MM-DD') },
        only: ['availability', 'start', 'date'],
        onSuccess: () => picker?.renderAll(),
      });
    }
  });
});

const createPicker = () => {
  picker = new easepick.create({
    css: [styles, customStyles],
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
    setup(picker) {
      picker.on('view', (e: CustomEvent) => {
        const { view, date, target } = e.detail;
        const dateString = date?.format('YYYY-MM-DD') ?? null;
        const availability = props.availability?.find((a) => a.date === dateString);

        if (view === 'CalendarDay' && availability) {
          const span = target.querySelector('.day-slots') || document.createElement('span');
          span.className = 'day-slots';
          const numSlots = Object.keys(availability.slots).length;
          span.textContent = `${numSlots} slot${numSlots !== 1 ? 's' : ''}`;

          target.append(span);
        }
      });
    },
  });
};
</script>

<template>
  <input
    ref="pickerRef"
    class="mt-6 w-full border-0 bg-slate-100 p-4 px-6 py-4 text-sm"
    placeholder="When for?"
  />
</template>

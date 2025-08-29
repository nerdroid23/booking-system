<script setup lang="ts">
import EasepickCalendar from '@/components/EasepickCalendar.vue';
import Layout from '@/layouts/HomeLayout.vue';
import { Availability, Employee, Service, Slot } from '@/types/generated';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { onMounted, ref, watch } from 'vue';

defineOptions({ layout: Layout });

const props = defineProps<{
  // TODO: use deferred prop
  availability: Array<Availability>;
  employee: Employee | null;
  service: Service;
  date: string | null;
  start: string | null;
}>();

onMounted(() => setSlots(props.date));

const slots = ref<Slot[]>([]);

const setSlots = (date: string | null) => {
  if (!date) {
    slots.value = [];
    return;
  }

  const availability = props.availability?.find((a) => a.date === date);
  slots.value = availability ? Object.values(availability.slots) : [];
};

const form = useForm<{
  service_id: number;
  employee_id: number | null;
  datetime: string | null;
  name: string | null;
  email: string | null;
}>({
  service_id: props.service.id,
  employee_id: props.employee?.id ?? null,
  datetime: null,
  name: null,
  email: null,
});

watch(
  () => form.datetime,
  (datetime) => {
    if (form.employee_id) {
      return;
    }

    const employee = Object.values(slots.value).find((s) => s.datetime === datetime)?.employees[0];

    router.get(route('checkout', [props.service, employee]), {}, { preserveState: true, preserveScroll: true, onSuccess() {} });
  },
);
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
      <EasepickCalendar
        :availability="availability"
        :date="date"
        :start="start"
        @updateSlots="setSlots"
      />
    </div>

    <div>
      <h2 class="text-xl font-medium">2. Choose a time</h2>
      <div class="mt-6 p-4">
        <div class="grid-col-3 grid gap-8 md:grid-cols-5">
          <button
            @click="form.datetime = slot.datetime"
            v-for="slot in slots"
            :key="slot.datetime"
            :class="{ 'border-slate-200 bg-slate-100 font-semibold': form.datetime === slot.datetime }"
            class="cursor-pointer border px-4 py-3 text-center hover:border-slate-200 hover:bg-slate-100 hover:font-semibold"
            type="button"
          >
            {{ slot.time }}
          </button>
        </div>
      </div>
    </div>

    <div>
      <h2 class="text-xl font-medium">3. Enter your details</h2>
      <div class="mt-6 p-4">Slots</div>
    </div>
  </form>
</template>

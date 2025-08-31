<script setup lang="ts">
import { Button } from '@/components/ui/button';
import Layout from '@/layouts/HomeLayout.vue';
import { Appointment } from '@/types/generated';
import { Head, Link, router } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { ref } from 'vue';

defineOptions({ layout: Layout });

const props = defineProps<{
  appointment: Appointment;
}>();

const submitting = ref(false);

const cancelAppointment = () => {
  router.delete(route('appointments.destroy', props.appointment.uuid), {
    onBefore: () => confirm('Are you sure you want to cancel this appointment?'),
    onStart: () => (submitting.value = true),
    onFinish: () => (submitting.value = false),
  });
};
</script>

<template>
  <Head title="Appointment" />

  <Link
    :href="route('home')"
    class="text-xs text-blue-500"
  >
    &larr; Go back
  </Link>

  <div class="mt-6 space-y-10">
    <div>
      <h2 class="text-2xl font-medium">{{ appointment.canceled ? 'Canceled' : 'Thanks for booking our services!' }}</h2>
      <div class="mt-6 flex space-x-3 bg-slate-100 px-6 py-4">
        <img
          :src="appointment.employee.avatar_url"
          class="size-12 rounded-lg"
          alt=""
        />

        <div class="flex w-full justify-between">
          <div>
            <div class="font-semibold">{{ appointment.service.title }} ({{ appointment.service.duration }} mins)</div>
            <div>{{ appointment.employee.name }}</div>
          </div>
          <div>{{ appointment.service.price }}</div>
        </div>
      </div>
    </div>

    <div>
      <h2 class="text-xl font-medium">When</h2>
      <div class="mt-6 space-x-3 bg-slate-100 px-6 py-4">
        <span>
          <time datetime="{{ appointment.date }}">{{ appointment.date }}</time> at
          <time datetime="{{ appointment.starts_at }}">{{ appointment.starts_at }}</time> until
          <time datetime="{{ appointment.ends_at }}">{{ appointment.ends_at }}</time>
        </span>
      </div>
    </div>

    <div v-if="!appointment.canceled">
      <Button
        @click="cancelAppointment"
        :disabled="submitting"
        class="mt-4 h-12 w-full rounded-none text-red-500"
        size="lg"
        variant="ghost"
        type="button"
      >
        <LoaderCircle
          v-if="submitting"
          class="h-4 w-4 animate-spin"
        />
        Cancel appointment
      </Button>
    </div>
  </div>
</template>

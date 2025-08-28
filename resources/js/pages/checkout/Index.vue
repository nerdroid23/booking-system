<script setup lang="ts">
import Layout from '@/layouts/HomeLayout.vue';
import { Employee, Service } from '@/types/generated';
import { Head, Link } from '@inertiajs/vue3';

defineOptions({ layout: Layout });
defineProps<{
  employee: Employee;
  service: Service;
}>();
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
          :src="employee.avatar_url"
          class="size-12 rounded-lg"
          alt=""
        />
        <div class="flex w-full justify-between">
          <div>
            <div class="font-semibold">{{ service.title }} ({{ service.duration }} mins)</div>
            <div>{{ employee.name }}</div>
          </div>
          <div>{{ service.price }}</div>
        </div>
      </div>
    </div>

    <div>
      <h2 class="text-xl font-medium">1. Choose a date</h2>
      <div class="mt-6 p-4">Picker</div>
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

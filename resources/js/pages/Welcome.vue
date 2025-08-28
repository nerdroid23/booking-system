<script setup lang="ts">
import ServiceCard from '@/components/ServiceCard.vue';
import HomeLayout from '@/layouts/HomeLayout.vue';
import { Employee, Service } from '@/types/generated';
import { Link } from '@inertiajs/vue3';

defineOptions({
  layout: HomeLayout,
});

defineProps({
  employees: Array<Employee>,
  services: Array<Service>,
});
</script>

<template>
  <div class="space-y-10">
    <div>
      <h2 class="text-xl font-medium">Choose a professional</h2>
      <div class="mt-6 grid grid-cols-2 gap-8 md:grid-cols-5">
        <Link
          v-for="employee in employees"
          :key="employee.id"
          :href="route('employees.show', employee)"
          class="flex flex-col items-center justify-center rounded-lg border border-slate-200 px-4 py-8 text-center shadow-sm hover:bg-gray-50/75"
        >
          <img
            :src="employee.avatar_url"
            alt="professional avatar"
            class="mb-4 size-16 rounded-full object-cover"
          />
          <p class="text-sm font-medium text-slate-600">{{ employee.name }}</p>
        </Link>
      </div>
    </div>
    <div>
      <h2 class="text-xl font-medium">Or, choose a service first</h2>
      <div class="mt-6 grid grid-cols-2 gap-8 md:grid-cols-5">
        <ServiceCard
          :service="service"
          v-for="service in services"
          :key="service.id"
        />
      </div>
    </div>
  </div>
</template>

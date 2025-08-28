<script setup lang="ts">
import HomeLayout from '@/layouts/HomeLayout.vue';
import { Employee, Service } from '@/types/generated';
import { Link } from '@inertiajs/vue3';
import ServiceCard from '@/components/ServiceCard.vue';

defineOptions({ layout: HomeLayout });
defineProps<{
  employee: Employee;
  services: Service[];
}>();
</script>

<template>
  <div>
    <Link
      :href="route('home')"
      class="text-xs text-blue-500"
    >
      &larr; Go back
    </Link>

    <div class="mt-6 space-y-3">
      <h2 class="text-xl font-medium">Now, choose a service from {{ employee.name }}</h2>

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

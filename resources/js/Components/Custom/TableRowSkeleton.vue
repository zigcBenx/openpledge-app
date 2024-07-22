<template>
  <tr class="flex justify-between items-center">
    <td>
      <div class="flex gap-3 items-center">
        <SkeletonLoader class="w-1 h-20" />
        <SkeletonLoader class="w-11 h-5" />
      </div>
    </td>
    <td>
      <SkeletonLoader class="lg:w-52 w-32 h-5 mb-1.5" />
      <SkeletonLoader class="lg:w-52 w-32 h-3" />
    </td>
    <td>
      <SkeletonLoader class="lg:w-52 w-32 h-5" />
    </td>
    <td>
      <SkeletonLoader class="lg:w-24 w-16 h-5" />
    </td>
    <td>
      <SkeletonLoader class="lg:w-24 w-16 h-5" />
    </td>
    <td>
      <SkeletonLoader class="lg:w-20 w-12 h-5" />
    </td>
    <td>
      <SkeletonLoader class="w-6 h-6" type="circle" />
    </td>
  </tr>
</template>
<script setup>
  import SkeletonLoader from '@/Components/SkeletonLoader.vue';
</script>
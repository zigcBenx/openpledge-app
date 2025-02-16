<script setup>
defineProps({ links: Array });
</script>

<template>
    <nav class="mt-4">
        <ul class="flex space-x-2">
            <li v-for="link in links" :key="link.label">
                <component
                    :is="link.url ? 'a' : 'span'"
                    v-html="link.label"
                    :href="link.url"
                    class="p-2 border rounded"
                    :class="{ 'bg-gray-300': link.active }"
                />
            </li>
        </ul>
    </nav>
</template>

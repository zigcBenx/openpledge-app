<template>
    <ol class="flex items-center w-full">
        <li v-for="(step, index) in totalSteps" :key="index"
            :class="index !== totalSteps - 1 && 'flex w-full items-center'">
            <span
                class="flex items-center justify-center w-10 h-10 lg:h-12 lg:w-12 rounded-full shrink-0 transition duration-500 ease-in-out"
                :class="{
                    'bg-turquoise dark:bg-dark-green text-white': index <= currentStep,
                    'bg-gray-100 dark:bg-gray-700 text-gray-500': index > currentStep,
                }">
                <svg v-if="index < currentStep" class="w-4 h-4 lg:w-5 lg:h-5" fill="currentColor" viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                        clip-rule="evenodd" />
                </svg>
                <span v-else class="font-bold text-lg">{{ step }}</span>
            </span>

            <div v-if="index < totalSteps - 1" class="mx-2 flex-grow h-1" :class="{
                'bg-turquoise dark:bg-dark-green': index < currentStep,
                'bg-gray-100 dark:bg-gray-700': index >= currentStep,
            }"></div>
        </li>
    </ol>
</template>

<script>
export default {
    props: {
        totalSteps: {
            type: Number,
            required: true
        },
        currentStep: {
            type: Number,
            required: true
        },
    },
};
</script>
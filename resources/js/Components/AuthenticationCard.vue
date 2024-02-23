<template>
    <div class="min-h-screen flex flex-col md:justify-center items-center pt-6 md:pt-0 bg-gray-100 dark:bg-gray-900">
        <div>
            <slot name="logo" />
        </div>

        <div class="w-full md:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden md:rounded-lg">
            <slot />
        </div>
    </div>
</template>

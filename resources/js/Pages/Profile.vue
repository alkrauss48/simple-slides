<script setup lang="ts">
import { ref, watch } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import type { PublicUser, PresentationListItem, PaginatedData } from '@/types'
import AppHead from '@/Components/AppHead.vue'

interface Props {
    user: PublicUser
    presentations: PaginatedData<PresentationListItem>
    search: string
    auth?: any
}

const props = defineProps<Props>()

const searchQuery = ref(props.search)
let searchTimeout: ReturnType<typeof setTimeout> | null = null

// Watch for search changes and debounce the search
watch(searchQuery, (newValue) => {
    if (searchTimeout) {
        clearTimeout(searchTimeout)
    }

    searchTimeout = setTimeout(() => {
        router.get(
            route('profile.show', { user: props.user.username }),
            { search: newValue },
            {
                preserveState: true,
                preserveScroll: true,
                only: ['presentations', 'search']
            }
        )
    }, 300)
})

const goToPresentation = (presentation: PresentationListItem) => {
    router.visit(route('presentations.show', {
        user: props.user.username,
        slug: presentation.slug
    }))
}
</script>

<template>
    <main class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <Head :title="`${user.name}'s Presentations`" />
        <AppHead />

        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                            {{ user.name }}'s Presentations
                        </h1>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            @{{ user.username }}
                        </p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <Link
                            href="/"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white"
                        >
                            Home
                        </Link>
                        <a
                            href="/admin"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white"
                        >
                            {{ auth?.user ? 'Dashboard' : 'Login' }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Search Bar -->
            <div class="mb-8">
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Search Presentations
                </label>
                <input
                    id="search"
                    v-model="searchQuery"
                    type="text"
                    placeholder="Search by title or description..."
                    class="w-full max-w-xl px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-800 dark:text-white"
                />
            </div>

            <!-- Presentations Grid -->
            <div v-if="presentations.data.length > 0" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <button
                        v-for="presentation in presentations.data"
                        :key="presentation.id"
                        @click="goToPresentation(presentation)"
                        class="bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-shadow p-6 text-left border border-gray-200 dark:border-gray-700 hover:border-blue-500 dark:hover:border-blue-400 flex flex-col"
                    >
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2 min-h-[3.5rem]">
                            {{ presentation.title }}
                        </h2>
                        <p
                            v-if="presentation.description"
                            class="text-gray-600 dark:text-gray-400 text-sm mb-4 line-clamp-3 flex-grow"
                        >
                            {{ presentation.description }}
                        </p>
                        <div
                            v-else
                            class="flex-grow"
                        ></div>
                        <div class="text-xs text-gray-500 dark:text-gray-500 mt-auto">
                            Updated {{ presentation.updated_at }}
                        </div>
                    </button>
                </div>

                <!-- Pagination -->
                <div v-if="presentations.last_page > 1" class="flex justify-center items-center space-x-2 mt-8">
                    <Link
                        v-for="link in presentations.links"
                        :key="link.label"
                        :href="link.url || '#'"
                        :class="[
                            'px-4 py-2 text-sm font-medium rounded-lg',
                            link.active
                                ? 'bg-blue-600 text-white'
                                : link.url
                                    ? 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-300 dark:border-gray-600'
                                    : 'bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-600 cursor-not-allowed'
                        ]"
                        :preserve-state="true"
                        :preserve-scroll="true"
                        v-html="link.label"
                    />
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-12">
                <svg
                    class="mx-auto h-12 w-12 text-gray-400"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    aria-hidden="true"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                    />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                    {{ search ? 'No presentations found' : 'No presentations yet' }}
                </h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ search ? 'Try adjusting your search terms.' : 'This user hasn\'t published any presentations yet.' }}
                </p>
            </div>
        </div>
    </main>
</template>

<style scoped>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>


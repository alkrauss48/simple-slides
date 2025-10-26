<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'

import CogIcon from '@/Components/icons/CogIcon.vue';
import MoonFillIcon from '@/Components/icons/MoonFillIcon.vue';
import MoonStrokeIcon from '@/Components/icons/MoonStrokeIcon.vue';
import { VisualMode, isDarkMode } from '@/enums/visualMode.ts';
import { getVisualMode, setVisualMode } from '@/utils/handleVisualMode.ts';
import slideStore from '@/store/slideStore.ts'

const props = defineProps<{
    auth?: any,
}>();

const isOpen = ref(false);
const visualMode = getVisualMode();
const darkMode = ref<boolean>(isDarkMode(visualMode));
const loopInterval = ref<number>(slideStore.loop);

const isAuthenticated = computed(() => !!props.auth?.user);

// Sync loopInterval with slideStore.loop when it changes externally (e.g., from query params)
watch(() => slideStore.loop, (newValue) => {
    loopInterval.value = newValue;
});

const toggleMenu = () => {
    isOpen.value = !isOpen.value;
};

const closeMenu = () => {
    isOpen.value = false;
};

const onBeforeSettingsVisit = () => {
    const fullPath = location.pathname + location.search;
    localStorage.setItem('appCurrentUrl', fullPath);
    closeMenu();
};

watch(darkMode, async (newValue: boolean) => {
    const newMode = newValue ? VisualMode.Dark : VisualMode.Light;
    setVisualMode(newMode);
});

watch(loopInterval, (newValue: number) => {
    // Update the current URL with the new loop parameter
    const url = new URL(window.location.href);
    if (newValue >= 2) {
        url.searchParams.set('loop', newValue.toString());
        slideStore.loop = newValue;
    } else {
        url.searchParams.delete('loop');
        slideStore.loop = 0;
    }

    // Update URL without reloading the page
    window.history.replaceState({}, '', url.toString());
});

// Close menu when clicking outside
const menuRef = ref<HTMLElement | null>(null);

const handleClickOutside = (event: MouseEvent) => {
    if (menuRef.value && !menuRef.value.contains(event.target as Node)) {
        closeMenu();
    }
};

const handleEscapeKey = (event: KeyboardEvent) => {
    if (event.key === 'Escape') {
        closeMenu();
    }
};

// Add event listeners when menu opens, remove when it closes
watch(isOpen, (newValue) => {
    if (newValue) {
        setTimeout(() => {
            document.addEventListener('click', handleClickOutside);
            document.addEventListener('keydown', handleEscapeKey);
        }, 0);
    } else {
        document.removeEventListener('click', handleClickOutside);
        document.removeEventListener('keydown', handleEscapeKey);
    }
});
</script>

<template>
    <div ref="menuRef" class="browsershot-hide fixed top-6 right-8 z-10">
        <button
            @click="toggleMenu"
            class="
                text-5xl text-gray-300/50
                hover:text-gray-300 focus:text-gray-300
                transition-colors
            "
            aria-label="Settings Menu"
        >
            <CogIcon />
        </button>

        <!-- Popup Menu -->
        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div
                v-if="isOpen"
                class="
                    absolute right-0 mt-4 w-80
                    bg-white dark:bg-gray-800
                    border border-gray-200 dark:border-gray-700
                    rounded-lg shadow-xl
                    overflow-hidden
                "
            >
                <div class="p-6 space-y-4">
                    <!-- Navigation Links -->
                    <div class="space-y-2">
                        <Link
                            href="/"
                            @click="closeMenu"
                            class="
                                block px-4 py-2 rounded
                                text-gray-900 dark:text-gray-100
                                hover:bg-gray-100 dark:hover:bg-gray-700
                                focus:bg-gray-100 dark:focus:bg-gray-700
                                font-medium transition-colors
                            "
                        >
                            Home
                        </Link>

                        <a
                            v-if="isAuthenticated"
                            href="/admin"
                            @click="closeMenu"
                            class="
                                block px-4 py-2 rounded
                                text-gray-900 dark:text-gray-100
                                hover:bg-gray-100 dark:hover:bg-gray-700
                                focus:bg-gray-100 dark:focus:bg-gray-700
                                font-medium transition-colors
                            "
                        >
                            Dashboard
                        </a>

                        <template v-else>
                            <a
                                href="/admin"
                                @click="closeMenu"
                                class="
                                    block px-4 py-2 rounded
                                    text-gray-900 dark:text-gray-100
                                    hover:bg-gray-100 dark:hover:bg-gray-700
                                    focus:bg-gray-100 dark:focus:bg-gray-700
                                    font-medium transition-colors
                                "
                            >
                                Login
                            </a>

                            <a
                                href="/admin/register"
                                @click="closeMenu"
                                class="
                                    block px-4 py-2 rounded
                                    text-gray-900 dark:text-gray-100
                                    hover:bg-gray-100 dark:hover:bg-gray-700
                                    focus:bg-gray-100 dark:focus:bg-gray-700
                                    font-medium transition-colors
                                "
                            >
                                Sign Up
                            </a>
                        </template>
                    </div>

                    <hr class="border-gray-200 dark:border-gray-700" />

                    <!-- Dark Mode Toggle -->
                    <div class="flex items-center justify-between px-4 py-2">
                        <span class="text-sm text-gray-600 dark:text-gray-400">
                            Dark Mode
                        </span>
                        <button
                            @click.stop="darkMode = !darkMode"
                            class="
                                text-3xl
                                text-gray-600 dark:text-gray-300
                                hover:text-gray-900 dark:hover:text-gray-100
                                focus:text-gray-900 dark:focus:text-gray-100
                                transition-colors
                            "
                            :aria-label="darkMode ? 'Disable Dark Mode' : 'Enable Dark Mode'"
                        >
                            <MoonStrokeIcon v-if="!darkMode" />
                            <MoonFillIcon v-else />
                        </button>
                    </div>

                    <hr class="border-gray-200 dark:border-gray-700" />

                    <!-- Loop Interval -->
                    <div class="px-4 py-2 space-y-2">
                        <label
                            for="loop-interval"
                            class="block font-medium text-gray-900 dark:text-gray-100"
                        >
                            Auto-Loop Interval (seconds)
                        </label>
                        <input
                            id="loop-interval"
                            v-model.number="loopInterval"
                            type="number"
                            min="0"
                            step="1"
                            placeholder="0 = disabled"
                            class="
                                w-full px-3 py-2 rounded
                                bg-gray-50 dark:bg-gray-900
                                border border-gray-300 dark:border-gray-600
                                text-gray-900 dark:text-gray-100
                                focus:outline-none focus:ring-2 focus:ring-blue-500
                            "
                        />
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Minimum 2 seconds to enable. Set to 0 to disable.
                        </p>
                    </div>

                    <hr class="border-gray-200 dark:border-gray-700" />

                    <!-- Other Settings Link -->
                    <Link
                        href="/settings"
                        :onBefore="onBeforeSettingsVisit"
                        class="
                            block px-4 py-2 rounded
                            text-center font-bold
                            text-gray-900 dark:text-gray-100
                            bg-gray-100 dark:bg-gray-700
                            hover:bg-gray-200 dark:hover:bg-gray-600
                            focus:bg-gray-200 dark:focus:bg-gray-600
                            transition-colors
                        "
                    >
                        Other Settings
                    </Link>
                </div>
            </div>
        </Transition>
    </div>
</template>


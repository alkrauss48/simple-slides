<script setup lang="ts">
import { ref, computed, useId } from 'vue'

import InfoIcon from '@/Components/icons/InfoIcon.vue';

const props = withDefaults(defineProps<{
    text: string,
    label?: string,
}>(), {
    label: 'More information',
});

const panelId = `tooltip-${useId()}`;

// Pinned (click/tap) is tracked separately from hover and focus so that a tap,
// which also fires mouseenter on most mobile browsers, does not immediately
// re-close the panel when the finger lifts.
const isPinned = ref(false);
const isHovered = ref(false);
const isFocused = ref(false);

const isVisible = computed(() => isPinned.value || isHovered.value || isFocused.value);

const togglePinned = () => {
    isPinned.value = !isPinned.value;
};

const onBlur = () => {
    isFocused.value = false;
    isPinned.value = false;
};

const hideTooltip = () => {
    isPinned.value = false;
    isHovered.value = false;
    isFocused.value = false;
};
</script>

<!--
    The panel is absolutely positioned against the nearest positioned ancestor,
    NOT against the trigger, so it can span a full form row instead of starting
    at the icon and overflowing its container. Give the row you want it to span
    a `relative` class.
-->
<template>
    <span class="inline-flex">
        <button
            type="button"
            :aria-label="props.label"
            :aria-expanded="isVisible"
            :aria-describedby="isVisible ? panelId : undefined"
            @click.stop="togglePinned"
            @mouseenter="isHovered = true"
            @mouseleave="isHovered = false"
            @focus="isFocused = true"
            @blur="onBlur"
            @keydown.esc.stop="hideTooltip"
            class="
                inline-flex items-center
                text-gray-500 dark:text-gray-400
                hover:text-gray-900 dark:hover:text-gray-100
                focus:text-gray-900 dark:focus:text-gray-100
                focus:outline-hidden focus:ring-2 focus:ring-blue-500 rounded-full
                transition-colors
            "
        >
            <slot>
                <InfoIcon />
            </slot>
        </button>

        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <span
                v-if="isVisible"
                :id="panelId"
                role="tooltip"
                class="
                    absolute left-0 right-0 top-full z-20 mt-1
                    pointer-events-none
                    px-3 py-2 rounded
                    bg-gray-900 dark:bg-gray-700
                    text-sm text-white
                    shadow-lg
                "
            >
                {{ props.text }}
            </span>
        </Transition>
    </span>
</template>

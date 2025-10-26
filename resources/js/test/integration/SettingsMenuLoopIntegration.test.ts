import { mount } from '@vue/test-utils'
import { nextTick } from 'vue'

import SlideView from '@/Components/SlideView.vue'
import dataStore from '@/store/dataStore.ts'
import slideStore from '@/store/slideStore.ts'
import ProgressType from '@/enums/progressType.ts'

beforeEach(() => {
    vi.useFakeTimers();
});

afterEach(() => {
    slideStore.reset();
    vi.clearAllTimers();
    vi.useRealTimers();
});

const mountSlideView = (loopValue: number = 0) => {
    dataStore.data = ['slide1', 'slide2', 'slide3', 'slide4', 'slide5'];
    slideStore.index = 0;
    slideStore.loop = loopValue;
    slideStore.progress = ProgressType.Bar;

    return mount(SlideView);
};

describe('SettingsMenu Loop Integration with SlideView', () => {
    test('loop interval starts when value is >= 2', async () => {
        // Start with valid loop value
        const wrapper = mountSlideView(5);
        await nextTick();

        // Interval should be set
        expect(wrapper.vm.loopInterval).not.toBe(null);
    });

    test('loop interval does not start when value is < 2', async () => {
        // Start with invalid loop value
        const wrapper = mountSlideView(1);
        await nextTick();

        // Interval should NOT be set
        expect(wrapper.vm.loopInterval).toBe(null);
    });

    test('enabling loop from settings with valid value starts interval', async () => {
        // Start with loop disabled
        const wrapper = mountSlideView(0);
        await nextTick();

        expect(wrapper.vm.loopInterval).toBe(null);

        // Enable with valid value
        slideStore.loop = 3;
        await nextTick();

        // Interval should now be set
        expect(wrapper.vm.loopInterval).not.toBe(null);
    });

    test('disabling loop from settings clears the interval', async () => {
        // Start with loop enabled
        const wrapper = mountSlideView(5);
        await nextTick();

        expect(wrapper.vm.loopInterval).not.toBe(null);

        // Disable loop (set to 0)
        slideStore.loop = 0;
        await nextTick();

        // Interval should be cleared
        expect(wrapper.vm.loopInterval).toBe(null);
    });

    test('changing loop interval clears old interval before starting new one', async () => {
        const clearIntervalSpy = vi.spyOn(global, 'clearInterval');

        // Start with loop of 5 seconds
        const wrapper = mountSlideView(5);
        await nextTick();

        const firstIntervalId = wrapper.vm.loopInterval;
        expect(firstIntervalId).not.toBe(null);

        // Change loop interval
        slideStore.loop = 3;
        await nextTick();

        // Should have cleared the old interval
        expect(clearIntervalSpy).toHaveBeenCalledWith(Number(firstIntervalId));

        // Should have a new interval
        const secondIntervalId = wrapper.vm.loopInterval;
        expect(secondIntervalId).not.toBe(null);
        expect(secondIntervalId).not.toBe(firstIntervalId);
    });
});

describe('SlideView - Keyboard Input Protection', () => {
    test('typing in input field does not trigger slide navigation', async () => {
        const wrapper = mountSlideView(0);
        await nextTick();

        // Create a fake input element
        const input = document.createElement('input');
        document.body.appendChild(input);
        input.focus();

        expect(slideStore.index).toBe(0);

        // Try to trigger slide navigation with right arrow while input is focused
        const event = new KeyboardEvent('keydown', { key: 'ArrowRight' });
        if (wrapper.vm && wrapper.vm.bindKeyDown) {
            wrapper.vm.bindKeyDown(event);
        }
        await nextTick();

        // Slide should NOT have changed
        expect(slideStore.index).toBe(0);

        // Cleanup
        document.body.removeChild(input);
    });

    test('typing in textarea does not trigger slide navigation', async () => {
        const wrapper = mountSlideView(0);
        await nextTick();

        // Create a fake textarea element
        const textarea = document.createElement('textarea');
        document.body.appendChild(textarea);
        textarea.focus();

        expect(slideStore.index).toBe(0);

        // Try to trigger slide navigation
        const event = new KeyboardEvent('keydown', { key: 'ArrowRight' });
        if (wrapper.vm && wrapper.vm.bindKeyDown) {
            wrapper.vm.bindKeyDown(event);
        }
        await nextTick();

        // Slide should NOT have changed
        expect(slideStore.index).toBe(0);

        // Cleanup
        document.body.removeChild(textarea);
    });

    test('keyboard navigation works when no input is focused', async () => {
        const wrapper = mountSlideView(0);
        await nextTick();

        expect(slideStore.index).toBe(0);

        // Trigger slide navigation with right arrow (no input focused)
        const event = new KeyboardEvent('keydown', { key: 'ArrowRight' });
        if (wrapper.vm && wrapper.vm.bindKeyDown) {
            wrapper.vm.bindKeyDown(event);
        }
        await nextTick();

        // Slide SHOULD have changed
        expect(slideStore.index).toBe(1);
    });

    test('backspace in input field does not navigate slides backward', async () => {
        const wrapper = mountSlideView(0);
        await nextTick();

        slideStore.index = 2;

        // Create a fake input element
        const input = document.createElement('input');
        document.body.appendChild(input);
        input.focus();

        expect(slideStore.index).toBe(2);

        // Try to trigger backward navigation with backspace while input is focused
        const event = new KeyboardEvent('keydown', { key: 'Backspace' });
        if (wrapper.vm && wrapper.vm.bindKeyDown) {
            wrapper.vm.bindKeyDown(event);
        }
        await nextTick();

        // Slide should NOT have changed
        expect(slideStore.index).toBe(2);

        // Cleanup
        document.body.removeChild(input);
    });
});


import { mount, VueWrapper } from '@vue/test-utils'

import Tooltip from '@/Components/Tooltip.vue'

const TEXT = 'Minimum 2 seconds to enable.';

const mountWrapper = (props = {}) : VueWrapper<any> => {
    return mount(Tooltip, {
        props: {
            text: TEXT,
            ...props
        }
    });
};

describe('Tooltip - Visibility', () => {
    test('panel is hidden by default', () => {
        const wrapper = mountWrapper();

        expect(wrapper.find('[role="tooltip"]').exists()).toBe(false);
        expect(wrapper.text()).not.toContain(TEXT);
    });

    test('hovering the trigger shows the panel and leaving hides it', async () => {
        const wrapper = mountWrapper();
        const trigger = wrapper.find('button');

        await trigger.trigger('mouseenter');
        expect(wrapper.find('[role="tooltip"]').text()).toContain(TEXT);

        await trigger.trigger('mouseleave');
        expect(wrapper.find('[role="tooltip"]').exists()).toBe(false);
    });

    test('focusing the trigger shows the panel and blurring hides it', async () => {
        const wrapper = mountWrapper();
        const trigger = wrapper.find('button');

        await trigger.trigger('focus');
        expect(wrapper.find('[role="tooltip"]').text()).toContain(TEXT);

        await trigger.trigger('blur');
        expect(wrapper.find('[role="tooltip"]').exists()).toBe(false);
    });

    test('clicking the trigger toggles the panel', async () => {
        const wrapper = mountWrapper();
        const trigger = wrapper.find('button');

        await trigger.trigger('click');
        expect(wrapper.find('[role="tooltip"]').exists()).toBe(true);

        await trigger.trigger('click');
        expect(wrapper.find('[role="tooltip"]').exists()).toBe(false);
    });

    test('a tap does not immediately close the panel when the pointer leaves', async () => {
        const wrapper = mountWrapper();
        const trigger = wrapper.find('button');

        // Touch browsers fire mouseenter alongside the tap, then mouseleave
        // once the finger lifts. The panel must survive that sequence.
        await trigger.trigger('mouseenter');
        await trigger.trigger('click');
        await trigger.trigger('mouseleave');

        expect(wrapper.find('[role="tooltip"]').exists()).toBe(true);
    });

    test('pressing Escape on the trigger hides the panel', async () => {
        const wrapper = mountWrapper();
        const trigger = wrapper.find('button');

        await trigger.trigger('click');
        expect(wrapper.find('[role="tooltip"]').exists()).toBe(true);

        await trigger.trigger('keydown', { key: 'Escape' });
        expect(wrapper.find('[role="tooltip"]').exists()).toBe(false);
    });
});

describe('Tooltip - Accessibility', () => {
    test('trigger describes the panel only while it is visible', async () => {
        const wrapper = mountWrapper();
        const trigger = wrapper.find('button');

        expect(trigger.attributes('aria-describedby')).toBeUndefined();
        expect(trigger.attributes('aria-expanded')).toBe('false');

        await trigger.trigger('focus');

        const panelId = wrapper.find('[role="tooltip"]').attributes('id');
        expect(panelId).toBeTruthy();
        expect(trigger.attributes('aria-describedby')).toBe(panelId);
        expect(trigger.attributes('aria-expanded')).toBe('true');
    });

    test('trigger uses the provided label', () => {
        const wrapper = mountWrapper({ label: 'About looping' });

        expect(wrapper.find('button').attributes('aria-label')).toBe('About looping');
    });

    test('trigger falls back to a default label', () => {
        const wrapper = mountWrapper();

        expect(wrapper.find('button').attributes('aria-label')).toBe('More information');
    });
});

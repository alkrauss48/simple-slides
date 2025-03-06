import { shallowMount, VueWrapper } from '@vue/test-utils'

import DraftBanner from '@/Components/DraftBanner.vue'
import { DRAFT_MESSAGE } from '@/constants/general.ts';

const mountWrapper = () : VueWrapper<any> => {
    return shallowMount(DraftBanner);
};

test('shows message', () => {
    const wrapper = mountWrapper();

    expect(wrapper.find('#draft-message').text()).toBe(DRAFT_MESSAGE);
});

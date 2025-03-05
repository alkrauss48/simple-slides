import { shallowMount, VueWrapper } from '@vue/test-utils'

import PrivacyPage from '@/Pages/Privacy.vue'

const mountWrapper = () : VueWrapper<any> => {
    return shallowMount(PrivacyPage);
};

test('loads the privacy policy page', async () => {
    const wrapper = mountWrapper();

    expect(wrapper.find('h1').text()).toBe('Privacy Policy');
});

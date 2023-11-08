import { shallowMount } from '@vue/test-utils'

import dataStore from '../../store/dataStore.ts'
import PreloadContent from '../../components/PreloadContent.vue'

test('shows the data as text', () => {
  dataStore.data = ['foo', 'bar', 'baz'];

  const wrapper = shallowMount(PreloadContent);

  expect(wrapper.text()).toContain('foo');
  expect(wrapper.text()).toContain('bar');
  expect(wrapper.text()).toContain('baz');
});

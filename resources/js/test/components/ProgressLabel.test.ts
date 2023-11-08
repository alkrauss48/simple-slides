import { shallowMount, VueWrapper } from '@vue/test-utils'

import ProgressLabel from '../../components/ProgressLabel.vue'
import dataStore from '../../store/dataStore.ts'
import slideStore from '../../store/slideStore.ts'

test('gets label', () => {
  dataStore.data = Array(16).fill('');
  slideStore.index = 4;

  const wrapper: VueWrapper<any> = shallowMount(ProgressLabel);

  expect(wrapper.vm.label).toBe('5 / 16');
});

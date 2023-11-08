import { shallowMount, VueWrapper } from '@vue/test-utils'

import ProgressBar from '../../components/ProgressBar.vue'
import dataStore from '../../store/dataStore.ts'
import slideStore from '../../store/slideStore.ts'

const mountWrapper = () : VueWrapper<any> => {
  dataStore.data = Array(17).fill('');
  slideStore.index = 4;

  return shallowMount(ProgressBar);
};

test('gets percentage', () => {
  const wrapper = mountWrapper();

  expect(wrapper.vm.percentage).toBe(25);
});

test('gets 0 percentage for 0 total', () => {
  dataStore.data = [];
  slideStore.index = 0;

  const wrapper: VueWrapper<any> = shallowMount(ProgressBar);

  expect(wrapper.vm.percentage).toBe(0);
});

test('gets 0 percentage for 1 total on last slide', () => {
  dataStore.data = [''];
  slideStore.index = 0;

  const wrapper: VueWrapper<any> = shallowMount(ProgressBar);

  expect(wrapper.vm.percentage).toBe(0);
});

test('gets 100 percentage for 2 total on last slide', () => {
  dataStore.data = ['', ''];
  slideStore.index = 1;

  const wrapper: VueWrapper<any> = shallowMount(ProgressBar);

  expect(wrapper.vm.percentage).toBe(100);
});

test('gets percentage label', () => {
  const wrapper = mountWrapper();

  expect(wrapper.vm.percentageLabel).toBe('25%');
});

test('gets style', () => {
  const wrapper = mountWrapper();

  expect(wrapper.vm.style).toStrictEqual({
    width: '25%',
  });
});

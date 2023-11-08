import { shallowMount, VueWrapper } from '@vue/test-utils'
import { useRouter } from 'vue-router'

import SlideView from '../../components/SlideView.vue'
import ProgressType from '../../enums/progressType.ts'
import router from '../../router/index.ts'
import dataStore from '../../store/dataStore.ts'
import slideStore from '../../store/slideStore.ts'

vi.mock('vue-router');

vi.mocked(useRouter).mockReturnValue({
  ...router,
  replace: vi.fn(),
})

beforeEach(() => {
  vi.mocked(useRouter().replace).mockReset()
})

const mountWrapper = () : VueWrapper<any> => {
  dataStore.data = ['foo', 'bar', 'baz'];
  slideStore.index = 1;
  slideStore.progress = ProgressType.Bar;

  return shallowMount(SlideView);
};

test('gets content', () => {
  const wrapper = mountWrapper();

  expect(wrapper.vm.content).toBe('bar');
});

test('returns showProgressLabel as true', () => {
  const wrapper = mountWrapper();

  slideStore.progress = ProgressType.Label;

  expect(wrapper.vm.showProgressLabel).toBeTruthy();
});

test('returns showProgressLabel as false', () => {
  const wrapper = mountWrapper();

  expect(wrapper.vm.showProgressLabel).toBeFalsy();
});

test('incrementCount increments index and calls router replace', () => {
  const wrapper = mountWrapper();
  wrapper.vm.incrementContent(1);

  expect(slideStore.index).toBe(2);
  expect(useRouter().replace).toHaveBeenCalled();
});

test('incrementCount increments index only to max, and calls router replace', () => {
  const wrapper = mountWrapper();
  wrapper.vm.incrementContent(5);

  expect(slideStore.index).toBe(2);
  expect(useRouter().replace).toHaveBeenCalled();
});

test('incrementCount decrements index and calls router replace', () => {
  const wrapper = mountWrapper();
  wrapper.vm.incrementContent(-1);

  expect(slideStore.index).toBe(0);
  expect(useRouter().replace).toHaveBeenCalled();
});

test('incrementCount decrements index only to min, and calls router replace', () => {
  const wrapper = mountWrapper();
  wrapper.vm.incrementContent(-5);

  expect(slideStore.index).toBe(0);
  expect(useRouter().replace).toHaveBeenCalled();
});

test('buildQueryParams includes index', () => {
  const wrapper = mountWrapper();
  const query = wrapper.vm.buildQueryParams();

  expect(query.index).toBe(1);
  expect(query.progress).toBeFalsy();
});

test('buildQueryParams includes progress', () => {
  const wrapper = mountWrapper();

  slideStore.progress = ProgressType.Label;

  const query = wrapper.vm.buildQueryParams();

  expect(query.progress).toBe(ProgressType.Label);
});

import { shallowMount, VueWrapper } from '@vue/test-utils'

import SlideArrows from '@/Components/SlideArrows.vue'

const mountWrapper = () : VueWrapper<any> => {
  return shallowMount(SlideArrows, {
    props: {
      isFirstSlide: false,
      isLastSlide: false,
    }
  });
};

test('emits "next" event', async () => {
  const wrapper = mountWrapper();

  wrapper.vm.$emit('next');

  // Wait until $emits have been handled
  await wrapper.vm.$nextTick();

  // assert event has been emitted
  expect(wrapper.emitted('next')).toBeTruthy();

  // assert event count
  expect(wrapper.emitted('next')?.length).toBe(1);
});

test('emits "previous" event', async () => {
  const wrapper = mountWrapper();

  wrapper.vm.$emit('previous');

  // Wait until $emits have been handled
  await wrapper.vm.$nextTick();

  // assert event has been emitted
  expect(wrapper.emitted('previous')).toBeTruthy();

  // assert event count
  expect(wrapper.emitted('previous')?.length).toBe(1);
});

test('emits next when next button is clicked', () => {
  const wrapper = mountWrapper();

  wrapper.find('#next').trigger('click')

  expect(wrapper.emitted()).toHaveProperty('next')
  expect(wrapper.emitted()).not.toHaveProperty('previous')
});

test('emits previous when previous button is clicked', () => {
  const wrapper = mountWrapper();

  wrapper.find('#previous').trigger('click')

  expect(wrapper.emitted()).toHaveProperty('previous')
  expect(wrapper.emitted()).not.toHaveProperty('next')
});

test('previous button is not visible if isFirstSlide prop is true', () => {
  const wrapper = shallowMount(SlideArrows, {
    props: {
      isFirstSlide: true,
      isLastSlide: false,
    }
  });

  expect(wrapper.find('#previous').exists()).toBe(false)
});

test('previous button is visible if isFirstSlide prop is false', () => {
  const wrapper = shallowMount(SlideArrows, {
    props: {
      isFirstSlide: false,
      isLastSlide: false,
    }
  });

  expect(wrapper.find('#previous').exists()).toBe(true)
});

test('next button is not visible if isLastSlide prop is true', () => {
  const wrapper = shallowMount(SlideArrows, {
    props: {
      isFirstSlide: false,
      isLastSlide: true,
    }
  });

  expect(wrapper.find('#next').exists()).toBe(false)
});

test('next button is visible if isLastSlide prop is false', () => {
  const wrapper = shallowMount(SlideArrows, {
    props: {
      isFirstSlide: false,
      isLastSlide: false,
    }
  });

  expect(wrapper.find('#next').exists()).toBe(true)
});

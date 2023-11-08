import { shallowMount } from '@vue/test-utils'

import SlideArrows from '../../components/SlideArrows.vue'

test('emits "next" event', async () => {
  const wrapper = shallowMount(SlideArrows);

  wrapper.vm.$emit('next');

  // Wait until $emits have been handled
  await wrapper.vm.$nextTick();

  // assert event has been emitted
  expect(wrapper.emitted('next')).toBeTruthy();

  // assert event count
  expect(wrapper.emitted('next')?.length).toBe(1);
});

test('emits "previous" event', async () => {
  const wrapper = shallowMount(SlideArrows);

  wrapper.vm.$emit('previous');

  // Wait until $emits have been handled
  await wrapper.vm.$nextTick();

  // assert event has been emitted
  expect(wrapper.emitted('previous')).toBeTruthy();

  // assert event count
  expect(wrapper.emitted('previous')?.length).toBe(1);
});

test('emits next when next button is clicked', () => {
  const wrapper = shallowMount(SlideArrows);

  wrapper.find('#next').trigger('click')

  expect(wrapper.emitted()).toHaveProperty('next')
  expect(wrapper.emitted()).not.toHaveProperty('previous')
})

test('emits previous when previous button is clicked', () => {
  const wrapper = shallowMount(SlideArrows);

  wrapper.find('#previous').trigger('click')

  expect(wrapper.emitted()).toHaveProperty('previous')
  expect(wrapper.emitted()).not.toHaveProperty('next')
})

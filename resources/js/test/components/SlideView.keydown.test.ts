import { shallowMount, VueWrapper } from '@vue/test-utils'

import SlideView from '@/Components/SlideView.vue'
import ProgressType from '@/enums/progressType.ts'
import dataStore from '@/store/dataStore.ts'
import slideStore from '@/store/slideStore.ts'

// Incrementors

const mountWrapper = () : VueWrapper<any> => {
  dataStore.data = ['foo', 'bar', 'baz', 'foo', 'bar', 'baz', 'foo', 'bar'];
  slideStore.index = 1;
  slideStore.progress = ProgressType.Bar;

  return shallowMount(SlideView);
};

test('pressing j increments the slide', async () => {
  const wrapper = mountWrapper();
  wrapper.vm.bindKeyDown({ key: 'j' });

  expect(slideStore.index).toBe(2);
});

test('pressing J increments the slide', async () => {
  const wrapper = mountWrapper();
  wrapper.vm.bindKeyDown({ key: 'J' });

  expect(slideStore.index).toBe(2);
});

test('pressing l increments the slide', async () => {
  const wrapper = mountWrapper();
  wrapper.vm.bindKeyDown({ key: 'l' });

  expect(slideStore.index).toBe(2);
});

test('pressing L increments the slide', async () => {
  const wrapper = mountWrapper();
  wrapper.vm.bindKeyDown({ key: 'L' });

  expect(slideStore.index).toBe(2);
});

test('pressing PageDown increments the slide', async () => {
  const wrapper = mountWrapper();
  wrapper.vm.bindKeyDown({ key: 'PageDown' });

  expect(slideStore.index).toBe(2);
});

test('pressing n increments the slide', async () => {
  const wrapper = mountWrapper();
  wrapper.vm.bindKeyDown({ key: 'n' });

  expect(slideStore.index).toBe(2);
});

test('pressing N increments the slide', async () => {
  const wrapper = mountWrapper();
  wrapper.vm.bindKeyDown({ key: 'N' });

  expect(slideStore.index).toBe(2);
});

test('pressing ArrowDown increments the slide', async () => {
  const wrapper = mountWrapper();
  wrapper.vm.bindKeyDown({ key: 'ArrowDown' });

  expect(slideStore.index).toBe(2);
});

test('pressing ArrowRight increments the slide', async () => {
  const wrapper = mountWrapper();
  wrapper.vm.bindKeyDown({ key: 'ArrowRight' });

  expect(slideStore.index).toBe(2);
});

test('pressing Space increments the slide', async () => {
  const wrapper = mountWrapper();
  wrapper.vm.bindKeyDown({ key: ' ' });

  expect(slideStore.index).toBe(2);
});

test('pressing Enter increments the slide', async () => {
  const wrapper = mountWrapper();
  wrapper.vm.bindKeyDown({ key: 'Enter' });

  expect(slideStore.index).toBe(2);
});

// Decrementors

test('pressing k decrements the slide', async () => {
  const wrapper = mountWrapper();
  wrapper.vm.bindKeyDown({ key: 'k' });

  expect(slideStore.index).toBe(0);
});

test('pressing K decrements the slide', async () => {
  const wrapper = mountWrapper();
  wrapper.vm.bindKeyDown({ key: 'K' });

  expect(slideStore.index).toBe(0);
});

test('pressing h decrements the slide', async () => {
  const wrapper = mountWrapper();
  wrapper.vm.bindKeyDown({ key: 'h' });

  expect(slideStore.index).toBe(0);
});

test('pressing H decrements the slide', async () => {
  const wrapper = mountWrapper();
  wrapper.vm.bindKeyDown({ key: 'H' });

  expect(slideStore.index).toBe(0);
});

test('pressing PageUp decrements the slide', async () => {
  const wrapper = mountWrapper();
  wrapper.vm.bindKeyDown({ key: 'PageUp' });

  expect(slideStore.index).toBe(0);
});

test('pressing p decrements the slide', async () => {
  const wrapper = mountWrapper();
  wrapper.vm.bindKeyDown({ key: 'p' });

  expect(slideStore.index).toBe(0);
});

test('pressing P decrements the slide', async () => {
  const wrapper = mountWrapper();
  wrapper.vm.bindKeyDown({ key: 'P' });

  expect(slideStore.index).toBe(0);
});

test('pressing Backspace decrements the slide', async () => {
  const wrapper = mountWrapper();
  wrapper.vm.bindKeyDown({ key: 'Backspace' });

  expect(slideStore.index).toBe(0);
});

test('pressing ArrowUp decrements the slide', async () => {
  const wrapper = mountWrapper();
  wrapper.vm.bindKeyDown({ key: 'ArrowUp' });

  expect(slideStore.index).toBe(0);
});

test('pressing ArrowLeft decrements the slide', async () => {
  const wrapper = mountWrapper();
  wrapper.vm.bindKeyDown({ key: 'ArrowLeft' });

  expect(slideStore.index).toBe(0);
});

// Focus

test('pressing enter while focused on next does not increment the slide', async () => {
  const wrapper = mountWrapper();

  document.getElementById('next')?.remove();

  const next = document.createElement("button");
  next.id = 'next';
  document.body.appendChild(next);
  next.focus();

  wrapper.vm.bindKeyDown({ key: 'Enter' });

  expect(slideStore.index).toBe(1);
});

test('pressing enter while focused on previous does not increment the slide', async () => {
  const wrapper = mountWrapper();

  document.getElementById('previous')?.remove();

  const previous = document.createElement("button");
  previous.id = 'previous';
  document.body.appendChild(previous);
  previous.focus();

  wrapper.vm.bindKeyDown({ key: 'Enter' });

  expect(slideStore.index).toBe(1);
});

test('pressing space while focused on next does not increment the slide', async () => {
  const wrapper = mountWrapper();

  document.getElementById('next')?.remove();

  const next = document.createElement("button");
  next.id = 'next';
  document.body.appendChild(next);
  next.focus();

  wrapper.vm.bindKeyDown({ key: ' ' });

  expect(slideStore.index).toBe(1);
});

test('pressing space while focused on previous does not increment the slide', async () => {
  const wrapper = mountWrapper();

  document.getElementById('previous')?.remove();

  const previous = document.createElement("button");
  previous.id = 'previous';
  document.body.appendChild(previous);
  previous.focus();

  wrapper.vm.bindKeyDown({ key: ' ' });

  expect(slideStore.index).toBe(1);
});

// Large Incrementors

test('pressing f large increments the slide', async () => {
  const wrapper = mountWrapper();
  wrapper.vm.bindKeyDown({ key: 'f' });

  expect(slideStore.index).toBe(6);
});

test('pressing F large increments the slide', async () => {
  const wrapper = mountWrapper();
  wrapper.vm.bindKeyDown({ key: 'F' });

  expect(slideStore.index).toBe(6);
});

// Large Decrementors

test('pressing b large increments the slide', async () => {
  const wrapper = mountWrapper();
  slideStore.index = 6;
  wrapper.vm.bindKeyDown({ key: 'b' });

  expect(slideStore.index).toBe(1);
});

test('pressing B large increments the slide', async () => {
  const wrapper = mountWrapper();
  slideStore.index = 6;
  wrapper.vm.bindKeyDown({ key: 'B' });

  expect(slideStore.index).toBe(1);
});

// Custom
test('pressing 0 takes you to the first slide', async () => {
  const wrapper = mountWrapper();
  slideStore.index = 6;
  wrapper.vm.bindKeyDown({ key: '0' });

  expect(slideStore.index).toBe(0);
});

test('pressing $ takes you to the first slide', async () => {
  const wrapper = mountWrapper();
  wrapper.vm.bindKeyDown({ key: '$' });

  expect(slideStore.index).toBe(dataStore.data.length - 1);
});

// @ts-nocheck

import { shallowMount, VueWrapper } from '@vue/test-utils'

import SettingsPage from '@/Pages/Settings.vue'
import { VisualMode } from '@/enums/visualMode.ts';
import { getVisualMode } from '@/utils/handleVisualMode.ts'

afterEach(() => {
  localStorage.clear()
});

const mountWrapper = () : VueWrapper<any> => {
  return shallowMount(SettingsPage);
};

test('sets the slidesUrl value', async () => {
  const wrapper = mountWrapper();

  const textarea = wrapper.find('#slidesUrl');

  await textarea.setValue('https://foo.com');

  expect(textarea.element.value).toBe('https://foo.com');
});

// TODO: Handle mocking inertia visit function.
// test('successfully submits the form', async () => {
//   const wrapper = mountWrapper();
//
//   await wrapper.find('#slidesUrl').setValue('https://foo.com');
//
//   await wrapper.find('form').trigger('submit');
// });

test('fails to submit the form if invalid', async () => {
  const wrapper = mountWrapper();

  await wrapper.find('form').trigger('submit');
});

test('sets the darkMode value', async () => {
  const wrapper = mountWrapper();

  const checkbox = wrapper.find('#darkMode');

  await checkbox.setChecked();

  expect(checkbox.element.checked).toBeTruthy();
});

test('setting darkMode to true sets dark mode', async () => {
  const wrapper = mountWrapper();

  const checkbox = wrapper.find('#darkMode');

  await checkbox.setValue(true);

  expect(getVisualMode()).toBe(VisualMode.Dark);
});

test('setting darkMode back to false sets light mode', async () => {
  const wrapper = mountWrapper();

  const checkbox = wrapper.find('#darkMode');

  await checkbox.setValue(true);

  expect(getVisualMode()).toBe(VisualMode.Dark);

  await checkbox.setValue(false);

  expect(getVisualMode()).toBe(VisualMode.Light);
});

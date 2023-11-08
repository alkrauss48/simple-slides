// @ts-nocheck

import { shallowMount, VueWrapper } from '@vue/test-utils'
import { useRouter } from 'vue-router'

import SettingsPage from '../../components/SettingsPage.vue'
import { VisualMode } from '../../enums/visualMode.ts';
import router from '../../router/index.ts'
import { getVisualMode } from '../../utils/handleVisualMode.ts'

vi.mock('vue-router');

vi.mocked(useRouter).mockReturnValue({
  ...router,
  push: vi.fn(),
  back: vi.fn(),
});

beforeEach(() => {
  vi.mocked(useRouter().push).mockReset()
  vi.mocked(useRouter().back).mockReset()
});

afterEach(() => {
  localStorage.clear()
});

const mountWrapper = () : VueWrapper<any> => {
  return shallowMount(SettingsPage, {
    global: {
      stubs: ['router-link'],
    }
  });
};

test('sets the slidesUrl value', async () => {
  const wrapper = mountWrapper();

  const textarea = wrapper.find('#slidesUrl');

  await textarea.setValue('https://foo.com');

  expect(textarea.element.value).toBe('https://foo.com');
});

test('successfully submits the form', async () => {
  const push = vi.fn()
  useRouter.mockImplementationOnce(() => ({
    push
  }))

  const wrapper = mountWrapper();

  await wrapper.find('#slidesUrl').setValue('https://foo.com');

  await wrapper.find('form').trigger('submit');

  expect(push).toHaveBeenCalledTimes(1);
  expect(push).toHaveBeenCalledWith(`/${btoa('https://foo.com')}`);
});

test('fails to submit the form if invalid', async () => {
  const push = vi.fn()
  useRouter.mockImplementationOnce(() => ({
    push
  }))

  const wrapper = mountWrapper();

  await wrapper.find('form').trigger('submit');

  expect(push).toHaveBeenCalledTimes(0);
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

test('successfully submits the form', async () => {
  const back = vi.fn()
  useRouter.mockImplementationOnce(() => ({
    back
  }))

  const wrapper = mountWrapper();

  await wrapper.find('#close').trigger('click');

  expect(back).toHaveBeenCalledTimes(1);
});

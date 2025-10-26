import { mount, VueWrapper } from '@vue/test-utils'

import SlideView from '@/Components/SlideView.vue'
import Keys from '@/constants/keys.ts';
import ProgressType from '@/enums/progressType.ts'
import dataStore from '@/store/dataStore.ts'
import slideStore from '@/store/slideStore.ts'

// Incrementors

const mountWrapper = () : VueWrapper<any> => {
    dataStore.data = ['foo', 'bar', 'baz', 'foo', 'bar', 'baz', 'foo', 'bar'];
    slideStore.index = 1;
    slideStore.loop = 5;
    slideStore.progress = ProgressType.Bar;

    return mount(SlideView);
};

test('loopInterval is not null with valid loop set', async () => {
    const wrapper = mountWrapper();

    expect(wrapper.vm.loopInterval).not.toBe(null);
});

test('loopInterval is cleared with valid loop set', async () => {
    const wrapper = mountWrapper();
    const spy = vi.spyOn(global, 'clearInterval');
    const intervalId = wrapper.vm.loopInterval;

    wrapper.vm.checkAndClearLoopInterval();

    expect(spy).toHaveBeenCalledTimes(1);
    expect(spy.mock.lastCall?.[0]).toBe(Number(intervalId));
});

test('loopInterval is cleared on valid key press', async () => {
    const wrapper = mountWrapper();
    const spy = vi.spyOn(global, 'clearInterval');
    const intervalId = wrapper.vm.loopInterval;

    wrapper.vm.bindKeyDown({ key: Keys.ENTER });

    expect(spy).toHaveBeenCalledTimes(1);
    expect(spy.mock.lastCall?.[0]).toBe(Number(intervalId));
});

test('loopInterval is not cleared on invalid key press', async () => {
    const wrapper = mountWrapper();
    const spy = vi.spyOn(global, 'clearInterval');

    wrapper.vm.bindKeyDown({ key: 'x' });

    expect(spy).toHaveBeenCalledTimes(0);
});

test('slide increments with loop timer', async () => {
    vi.useFakeTimers();

    const wrapper = mountWrapper();

    expect(slideStore.index).toBe(1);

    vi.advanceTimersToNextTimer();

    expect(slideStore.index).toBe(2);
});

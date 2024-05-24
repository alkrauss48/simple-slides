import ProgressType from '@/enums/progressType.ts'
import dataStore from '@/store/dataStore.ts'
import slideStore from '@/store/slideStore.ts'

afterEach(() => {
    dataStore.reset();
    slideStore.reset();
});

test('gets the right initial values', async () => {
  expect(slideStore.index).toBe(0);
  expect(slideStore.progress).toBe(ProgressType.Bar);
});

test('can reset to initial values', async () => {
  slideStore.index = 5;
  slideStore.progress = ProgressType.Label;

  expect(slideStore.index).toBe(5);
  expect(slideStore.progress).toBe(ProgressType.Label);

  slideStore.reset();

  expect(slideStore.index).toBe(0);
  expect(slideStore.progress).toBe(ProgressType.Bar);
});

test('will not change slide store if no new index', async () => {
  slideStore.index = 0;

  slideStore.increment(-1);

  expect(slideStore.index).toBe(0);
});

test('returns true for isEnd if slide index is at the end', async () => {
  dataStore.data = ['a', 'b', 'c'];
  slideStore.index = 2;

  expect(slideStore.isEnd()).toBe(true);
});

test('returns false for isEnd if slide index is not at the end', async () => {
  dataStore.data = ['a', 'b', 'c'];
  slideStore.index = 1;

  expect(slideStore.isEnd()).toBe(false);
});

test('returns true for canLoop if loop value is valid', async () => {
  slideStore.loop = 5;

  expect(slideStore.canLoop()).toBe(true);
});

test('returns false for canLoop if loop value is invalid', async () => {
  slideStore.loop = 1;

  expect(slideStore.canLoop()).toBe(false);
});

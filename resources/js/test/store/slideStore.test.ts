import ProgressType from '../../enums/progressType.ts'
import slideStore from '../../store/slideStore.ts'

afterEach(() => {
  slideStore.reset()
})

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

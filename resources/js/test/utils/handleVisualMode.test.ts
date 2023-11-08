import { VisualMode } from '../../enums/visualMode.ts';
import { applyVisualMode, getVisualMode, setVisualMode } from '../../utils/handleVisualMode.ts'

afterEach(() => {
  localStorage.clear()
})

test('gets the right base visual mode', async () => {
  expect(getVisualMode()).toBe(VisualMode.Light);
});

test('gets the right visual mode from localStorage', async () => {
  localStorage.setItem('visualMode', VisualMode.Dark.toString())

  expect(getVisualMode()).toBe(VisualMode.Dark);
});

test('sets the visual mode to light', async () => {
  setVisualMode(VisualMode.Light);

  expect(getVisualMode()).toBe(VisualMode.Light);
});

test('sets the visual mode to dark', async () => {
  setVisualMode(VisualMode.Dark);

  expect(getVisualMode()).toBe(VisualMode.Dark);
});

test('applying the visual mode without an option uses the default', async () => {
  applyVisualMode();

  expect(document.body.classList.toString()).not.toContain('dark');
});

test('applying the visual mode to dark changes the body', async () => {
  applyVisualMode(VisualMode.Dark);

  expect(document.body.classList.toString()).toContain('dark');
});

test('applying the visual mode to light changes the body', async () => {
  applyVisualMode(VisualMode.Light);

  expect(document.body.classList.toString()).not.toContain('dark');
});

test('setting the visual mode to dark also applies the visual mode', async () => {
  setVisualMode(VisualMode.Dark);

  expect(document.body.classList.toString()).toContain('dark');
});

test('setting the visual mode to light also applies the visual mode', async () => {
  setVisualMode(VisualMode.Light);

  expect(document.body.classList.toString()).not.toContain('dark');
});

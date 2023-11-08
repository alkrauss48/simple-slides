import { VisualMode, isDarkMode, isLightMode } from '../../enums/visualMode.ts';

test('properly detects dark mode', async () => {
  expect(isDarkMode(VisualMode.Dark)).toBeTruthy();
  expect(isDarkMode(VisualMode.Light)).toBeFalsy();
});

test('properly detects light mode', async () => {
  expect(isLightMode(VisualMode.Dark)).toBeFalsy();
  expect(isLightMode(VisualMode.Light)).toBeTruthy();
});

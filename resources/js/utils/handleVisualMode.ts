import { VisualMode, isDarkMode } from '../enums/visualMode.ts';

export const getVisualMode = () : VisualMode => {
  return (localStorage.getItem('visualMode') as VisualMode) ?? VisualMode.Light
};

export const setVisualMode = (value: VisualMode) => {
  localStorage.setItem('visualMode', value.toString());

  applyVisualMode(value);
};

export const applyVisualMode = (value: VisualMode = getVisualMode()) : void => {
  if (isDarkMode(value)) {
    document.body.classList.add('dark');
    return;
  }

  document.body.classList.remove('dark');
};

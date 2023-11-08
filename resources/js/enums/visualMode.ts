export enum VisualMode {
  Dark = "dark",
  Light = "light",
}

export const isDarkMode = (mode: VisualMode) : boolean => {
  return mode === VisualMode.Dark;
};

export const isLightMode = (mode: VisualMode) : boolean => {
  return mode === VisualMode.Light;
};

export default VisualMode;

export const ENTER = 'Enter';
export const SPACE = ' ';
export const DOLLAR_SIGN = '$';
export const ZERO = '0';

export const INCREMENTORS = [
  ENTER,
  SPACE,
  'ArrowDown',
  'ArrowRight',
  'PageDown',
  'n',
  'N',
  'j',
  'J',
  'l',
  'L',
];

export const DECREMENTORS = [
  'Backspace',
  'ArrowUp',
  'ArrowLeft',
  'PageUp',
  'p',
  'P',
  'k',
  'K',
  'h',
  'H',
];

export const LARGE_INCREMENTORS = [
  'f',
  'F',
];

export const LARGE_DECREMENTORS = [
  'b',
  'B',
];

export const ALL_APP_KEYS = [
    ...INCREMENTORS,
    ...DECREMENTORS,
    ...LARGE_INCREMENTORS,
    ...LARGE_DECREMENTORS,
    DOLLAR_SIGN,
    ZERO,
];

export default {
  ENTER,
  SPACE,
  DOLLAR_SIGN,
  ZERO,
  INCREMENTORS,
  DECREMENTORS,
  LARGE_INCREMENTORS,
  LARGE_DECREMENTORS,
  ALL_APP_KEYS
};

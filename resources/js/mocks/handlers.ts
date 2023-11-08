import { rest } from 'msw'

export const MOCK_INSTRUCTIONS_URL = 'https://fake.dev/instructions';
export const MOCK_WINDOWS_INSTRUCTIONS_URL = 'https://fake.dev/instructions-windows';

const data: String[] = [
  'Welcome to',
  '# Simple Slides',
  '[https://example.com](https://example.com)',
];

export default [
  rest.get(MOCK_INSTRUCTIONS_URL, (_req, res, ctx) => {
    return res(
      ctx.text(data.join("\n\n"))
    );
  }),
  rest.get(MOCK_WINDOWS_INSTRUCTIONS_URL, (_req, res, ctx) => {
    return res(
      ctx.text(data.join("\r\n"))
    );
  }),
];

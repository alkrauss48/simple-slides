import { http, HttpResponse } from 'msw'

export const MOCK_INSTRUCTIONS_URL = 'https://fake.dev/instructions';
export const MOCK_WINDOWS_INSTRUCTIONS_URL = 'https://fake.dev/instructions-windows';

const data: String[] = [
  'Welcome to',
  '# Simple Slides',
  '[https://example.com](https://example.com)',
];

export default [
  http.get(MOCK_INSTRUCTIONS_URL, (info) => {
    return new HttpResponse(data.join("\n\n"));
  }),
  http.get(MOCK_WINDOWS_INSTRUCTIONS_URL, (info) => {
    return new HttpResponse(data.join("\r\n"));
  }),
];

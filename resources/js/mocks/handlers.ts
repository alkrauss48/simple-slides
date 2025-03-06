import { http, HttpResponse } from 'msw'

import { INSTRUCTIONS_URL } from '@/constants/general.ts';

export const MOCK_ADHOC_URL = 'https://fake.dev/presentation';
export const MOCK_WINDOWS_ADHOC_URL = 'https://fake.dev/presentation-windows';

const data: String[] = [
  'Welcome to',
  '# Simple Slides',
  '[https://example.com](https://example.com)',
];

export default [
  http.get(INSTRUCTIONS_URL, (info) => {
    return new HttpResponse(data.join("\n\n"));
  }),
  http.get(MOCK_ADHOC_URL, (info) => {
    return new HttpResponse(data.join("\n\n"));
  }),
  http.get(MOCK_WINDOWS_ADHOC_URL, (info) => {
    return new HttpResponse(data.join("\r\n"));
  }),
];

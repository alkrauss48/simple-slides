import dataStore from '../../store/dataStore.ts';
import { MOCK_INSTRUCTIONS_URL, MOCK_WINDOWS_INSTRUCTIONS_URL } from '../../mocks/handlers.ts';
import { server } from '../../mocks/browser.ts';

beforeEach(() => {
  dataStore.reset()
})

test('gets the right initial values', async () => {
  expect(dataStore.data).toStrictEqual([]);
});

test('can reset to initial values', async () => {
  dataStore.data = ['1', '2', '3'];

  expect(dataStore.data).toStrictEqual(['1', '2', '3']);

  dataStore.reset();

  expect(dataStore.data).toStrictEqual([]);
});

describe('fetching data', () => {
  // Start server before all tests
  beforeAll(() => server.listen({ onUnhandledRequest: 'error' }))

  //  Close server after all tests
  afterAll(() => server.close())

  // Reset handlers after each test `important for test isolation`
  afterEach(() => server.resetHandlers())

  test('gets the instructions', async () => {
    expect(dataStore.data).toStrictEqual([]);

    await dataStore.fetchAndProcessData(MOCK_INSTRUCTIONS_URL);

    expect(dataStore.data.length).toBe(3);
    expect(dataStore.data[0]).toBe('<p>Welcome to</p>');
    expect(dataStore.data[1]).toBe('<h1>Simple Slides</h1>');
    expect(dataStore.data[2]).toBe('<p><a href="https://example.com">https://example.com</a></p>');
  });

  test('gets the instructions with \r\n data', async () => {
    expect(dataStore.data).toStrictEqual([]);

    await dataStore.fetchAndProcessData(MOCK_WINDOWS_INSTRUCTIONS_URL);

    expect(dataStore.data.length).toBe(3);
    expect(dataStore.data[0]).toBe('<p>Welcome to</p>');
    expect(dataStore.data[1]).toBe('<h1>Simple Slides</h1>');
    expect(dataStore.data[2]).toBe('<p><a href="https://example.com">https://example.com</a></p>');
  });
});

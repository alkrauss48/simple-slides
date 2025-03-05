import { server } from '@/mocks/browser.ts';
import { MOCK_ADHOC_URL, MOCK_WINDOWS_ADHOC_URL } from '@/mocks/handlers.ts';
import dataStore from '@/store/dataStore.ts';

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

test('can process provided data with default delimiter', async () => {
    const text = '1\n\n2\n\n3';

    dataStore.processData(text);

    expect(dataStore.data).toStrictEqual([
        '<p>1</p>',
        '<p>2</p>',
        '<p>3</p>',
    ]);
});

test('can process provided data with the triple hyphen delimiter', async () => {
    const text = '1---2---3';

    dataStore.processData(text, '---');

    expect(dataStore.data).toStrictEqual([
        '<p>1</p>',
        '<p>2</p>',
        '<p>3</p>',
    ]);
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

        await dataStore.fetchAndProcessData(MOCK_ADHOC_URL);

        expect(dataStore.data.length).toBe(3);
        expect(dataStore.data[0]).toBe('<p>Welcome to</p>');
        expect(dataStore.data[1]).toBe('<h1>Simple Slides</h1>');
        expect(dataStore.data[2]).toBe('<p><a href="https://example.com">https://example.com</a></p>');
    });

    test('gets the instructions with \r\n data', async () => {
        expect(dataStore.data).toStrictEqual([]);

        await dataStore.fetchAndProcessData(MOCK_WINDOWS_ADHOC_URL);

        expect(dataStore.data.length).toBe(3);
        expect(dataStore.data[0]).toBe('<p>Welcome to</p>');
        expect(dataStore.data[1]).toBe('<h1>Simple Slides</h1>');
        expect(dataStore.data[2]).toBe('<p><a href="https://example.com">https://example.com</a></p>');
    });
});

Object.defineProperty(document, 'fonts', {
    value: {
        // Create an iterable object to avoid TypeError
        [Symbol.iterator]: function* () {
            yield { family: 'Roboto', weight: '400' }; // Example font object
            yield { family: 'Arial', weight: '700' };
        },
        load: vi.fn(),
    },
    writable: true,
});

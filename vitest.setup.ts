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

// Mock Canvas API for emoji preloading
HTMLCanvasElement.prototype.getContext = vi.fn(() => {
    return {
        fillText: vi.fn(),
        getImageData: vi.fn(() => ({
            data: new Uint8ClampedArray(4),
            width: 1,
            height: 1,
        })),
        font: '',
    };
}) as any;

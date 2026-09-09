// Node 26 defines a non-enumerable `globalThis.localStorage` that evaluates to
// undefined unless --localstorage-file is passed. Vitest 2 skips keys that already
// exist on the Node global when it populates jsdom's, so Node's broken accessor wins
// and both `globalThis.localStorage` and `window.localStorage` read undefined.
// Install a minimal in-memory Storage instead.
// Remove this once the Vitest 5 upgrade lands.
const createStorage = (): Storage => {
    let store: Record<string, string> = {};

    return {
        get length() {
            return Object.keys(store).length;
        },
        key: (index: number) => Object.keys(store)[index] ?? null,
        getItem: (key: string) => (key in store ? store[key] : null),
        setItem: (key: string, value: string) => {
            store[key] = String(value);
        },
        removeItem: (key: string) => {
            delete store[key];
        },
        clear: () => {
            store = {};
        },
    };
};

for (const key of ['localStorage', 'sessionStorage'] as const) {
    if (globalThis[key] === undefined) {
        Object.defineProperty(globalThis, key, {
            value: createStorage(),
            configurable: true,
            writable: true,
        });
    }
}

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

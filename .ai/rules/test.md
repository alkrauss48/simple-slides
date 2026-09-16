---
paths:
  - 'resources/js/test/**'
---

# Test

## Testing document-level listeners: attachTo + a real key name, or the test is vacuous
Components here register `document` click/keydown listeners (e.g. SettingsMenu's click-outside and Escape-to-close). Two traps make tests for them pass whether or not the code works:

1. `mount()` is detached from the DOM, so events dispatched on the wrapper never reach `document`. Use `mount(C, { attachTo: document.body })` and `wrapper.unmount()` afterwards.
2. `trigger('keydown.esc')` sets `key: 'esc'`. Handlers that check `event.key === 'Escape'` never match, so nothing fires. Use `trigger('keydown', { key: 'Escape' })` — that satisfies both Vue's `@keydown.esc` and a raw `=== 'Escape'` check.

Verify any such test actually fails when you break the code; both traps produce a green test that asserts nothing.

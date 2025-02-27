import { reactive } from 'vue'
import DOMPurify from 'dompurify';
import { marked } from 'marked';

const dataStore = reactive({
    data: <string[]>[],

    async fetchAndProcessData(url: string): Promise<void> {
        const response = await fetch(url);
        const body = await response.text();

        // Not passing a delimiter, because this situation would always use
        // the default delimiter.
        this.processData(body);
    },

    processData(content: string, delimiter: string = '(\n\n|\r\n)'): void {
        const parsedBody = content
            .split(new RegExp(delimiter))
            .filter((content) => content.trim().length > 0)
            .map((content) => {
                const parsed = marked.parse(content, { async: false });

                return DOMPurify.sanitize(parsed);
            }).map((content) => content.replace(/\n$/, ''));

        this.data = parsedBody;
    },

    reset() {
        this.data = [];
    },
});

export default dataStore;

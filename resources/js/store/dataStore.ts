import { reactive } from 'vue'
import DOMPurify from 'dompurify';
import { marked } from 'marked';

const dataStore = reactive({
    data: <string[]>[],

    async fetchAndProcessData(url: string) {
        const response = await fetch(url);
        const body = await response.text();

        this.processData(body);
    },

    processData(content: string) {
        const parsedBody = content
            .split("\n\n")
            .map((content) => content.split("\r\n"))
            .flat()
            .filter((content) => content.trim().length > 0)
            .map((content) => {
                const parsed = marked.parse(content, {
                    headerIds: false,
                    mangle: false,
                });

                return DOMPurify.sanitize(parsed);
            }).map((content) => content.replace(/\n$/, ''));

        this.data = parsedBody;
    },

    reset() {
        this.data = [];
    },
});

export default dataStore;

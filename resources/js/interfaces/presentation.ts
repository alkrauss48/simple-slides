interface Presentation {
    id: string,
    content: string,
    slide_delimiter: string,
    is_published: boolean,
    user?: {
        username: string,
        name: string,
    },
}

export default Presentation;

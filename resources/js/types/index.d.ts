export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at: string;
}

export interface PublicUser {
    id: number;
    name: string;
    username: string;
}

export interface PresentationListItem {
    id: number;
    title: string;
    slug: string;
    description: string | null;
    updated_at: string;
}

export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface PaginatedData<T> {
    data: T[];
    current_page: number;
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    links: PaginationLink[];
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number;
    total: number;
}

export type PageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    auth: {
        user: User;
    };
};

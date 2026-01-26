export interface Event {
    id: number;
    title: string;
    category_id: number;
    image_path: string | null;
    start_time: string; 
    content: string;
    status: boolean;
    updated_at: string;
    created_at: string;
    deleted_at: string;
}

export interface User {
    id: number;
    name: string;
    email: string;
}
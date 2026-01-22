export interface Event {
    id: number;
    title: string;
    image_path: string | null;
    starts_at: string; // Dates are sent as strings in JSON
    is_fully_booked: boolean;
    created_at: string;
}

export interface User {
    id: number;
    name: string;
    email: string;
}
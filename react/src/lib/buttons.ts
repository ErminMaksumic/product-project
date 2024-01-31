export interface Button {
    text: string;
    link: string;
    state: string;
    color: string;
}

export const orderStateButtons: Button[] = [
    {
        text: "DRAFT",
        link: "/productDraft",
        state: "DRAFT",
        color: "blue",
    },
    {
        text: "ACTIVATE",
        link: "/productActivate",
        state: "DraftToActive",
        color: "green",
    },
    {
        text: "DELETE",
        link: "/productDelete",
        state: "ActiveToDelete",
        color: "red",
    },
];

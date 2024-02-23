export interface Button {
    text: string;
    link: string;
    state: string;
    color: string;
    request?: {};
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
        request: {
            validFrom: "2024-01-28 20:48:55.000",
            validTo: "2029-01-28 20:48:55.000",
        },
    },
    {
        text: "DELETE",
        link: "/productDelete",
        state: "ActiveToDelete",
        color: "red",
    },
];

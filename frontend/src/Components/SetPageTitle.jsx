import React  from "react";
import CONFIG from '../config';

export default function SetPageTitle(title) {
    if (typeof title !== "string") {
        throw new Error("Title should be an string");
    }
    document.title = title + ' - ' + CONFIG.website.afterTitle;
}

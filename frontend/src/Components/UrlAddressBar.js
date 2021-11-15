import { useHistory } from 'react-router-dom';

class UrlAddressBar {
    static updateOrRemovePageFromStringURL(url, subpage, newValue) {
        const urlObj = new URL(url);
        const urlString = urlObj.href;
        const splittedPathnameByQuestionMark = urlObj.pathname.split('/');
        const pageOfUrl = parseInt(splittedPathnameByQuestionMark[splittedPathnameByQuestionMark.length - 1]);

        let newUrl = '';

        if(!isNaN(pageOfUrl) && newValue !== 1) {
            if(pageOfUrl > 1) {
                newUrl = urlString.replace(subpage + pageOfUrl, subpage + newValue);
            }
        } else {
            if(parseInt(newValue) > 1) {
                newUrl = urlObj.origin + urlObj.pathname + subpage + newValue + urlObj.search;
            } else {
                newUrl = urlString.replace(subpage + pageOfUrl, '');
            }
        }

        return newUrl;
    }

    static getCurrentSubPage(subpage) {
        const currentUrl = window.location.href;
        const tempArray = currentUrl.split(subpage);

        return tempArray && tempArray[1] ? parseInt(tempArray[1].replace('/', '')) : null;
    }

    static addParameterToStringURL(url, param, paramVal) {
        let rawURL = url;

        // URL with `?` at the end and without query parameters
        // leads to incorrect result.
        if (rawURL.charAt(rawURL.length - 1) === "?") {
            rawURL = rawURL.slice(0, rawURL.length - 1);
        }

        const parsedURL = new URL(rawURL);
        const urlParams = new URLSearchParams(parsedURL.search);
        urlParams.set(param, paramVal);

        parsedURL.search = urlParams.toString();
        return parsedURL.toString();
    }

    static removeParameterToStringURL(url, params) {
        let rawURL = url;

        // URL with `?` at the end and without query parameters
        // leads to incorrect result.
        if (rawURL.charAt(rawURL.length - 1) === "?") {
            rawURL = rawURL.slice(0, rawURL.length - 1);
        }

        const parsedURL = new URL(rawURL);
        const urlParams = new URLSearchParams(parsedURL.search);

        if(!Array.isArray(params)) {
            params = [params];
        }

        params.map(param => {
            urlParams.delete(param);
        })

        parsedURL.search = urlParams.toString();
        return parsedURL.toString();
    }

    static pushAddressUrl(data, title, url) {
        let history = useHistory();

        history.pushState(data, title, url);
    }

    static replaceAddressUrl(data, title, url) {
        let history = useHistory();

        history.replaceState(data, title, url);
    }

    static getGetValueOfKeyFromAddressURL(key) {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(key);
    }

    static setPageAfterPrefix(prefix, page) {
        const currentUrl = window.location.href;

        return this.updateOrRemovePageFromStringURL(currentUrl, '/' + prefix + '/', page);
    }
}

export default UrlAddressBar;

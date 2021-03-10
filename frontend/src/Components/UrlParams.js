class UrlParams {
    static updateURLParameter(url, param, paramVal){
        const newAdditionalURL = "";
        const tempArray = url.split(param);
        const baseURL = tempArray[0];
        const rows_txt = param + "/" + paramVal;

        return baseURL + (baseURL.charAt(baseURL.length - 1) !== '/' ? '/' : '') + newAdditionalURL + rows_txt;
    }

    static getCurrentSubPage(subpage) {
        const currentUrl = window.location.href;
        const tempArray = currentUrl.split(subpage);

        return tempArray && tempArray[1] ? parseInt(tempArray[1].replace('/', '')) : null;
    }
}

export default UrlParams;

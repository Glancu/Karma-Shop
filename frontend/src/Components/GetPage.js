class GetPage {
    static getSubPage(page = 'page') {
        const splittedText = window.location.href.split(page + '/');
        return (splittedText && splittedText[1] && (!isNaN(parseInt(splittedText[1])))) ? parseInt(splittedText[1]) : 1;
    }
}

export default GetPage;

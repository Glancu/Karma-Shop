function windowScrollTo(top, left = 0) {
    window.scrollTo({
        top,
        left,
        behavior: 'smooth'
    });
}

export { windowScrollTo }

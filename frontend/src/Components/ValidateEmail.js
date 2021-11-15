function ValidateEmail(email) {
    const validEmailRegex = RegExp(/^(([^<>()\[\].,;:\s@"]+(\.[^<>()\[\].,;:\s@"]+)*)|(".+"))@(([^<>()[\].,;:\s@"]+\.)+[^<>()[\].,;:\s@"]{2,})$/i);
    return validEmailRegex.test(email);
}

export default ValidateEmail

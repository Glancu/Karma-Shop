import axios from 'axios';

export const userLoggedIn = () => {
    const userToken = localStorage.getItem(process.env.LOGIN_TOKEN_STORAGE_PREFIX);
    if(userToken) {
        const formData = new FormData();
        formData.append('token', userToken);

        return axios.post("/api/user/validate_token", formData, {
            headers: {'Content-Type': 'multipart/form-data'}
        }).then(result => {
            if(result && result.data && result.data.success) {
                return true;
            }
        }).catch(() => {
            return false;
        });
    }

    return Promise.resolve(false);
}

export const userEmail = () => {
    const userToken = localStorage.getItem(process.env.LOGIN_TOKEN_STORAGE_PREFIX);
    if(userToken) {
        const formData = new FormData();
        formData.append('token', userToken);

        return axios.post("/api/user/get-email", formData, {
            headers: {'Content-Type': 'multipart/form-data'}
        }).then(result => {
            if(result && result.data && result.data.email) {
                return result.data.email;
            }
        }).catch(() => {
            return null;
        });
    }

    return Promise.resolve(null);
}


import axios from 'axios';
import CONFIG from '../../config';

const userStorageLoginToken = CONFIG.user.storage_login_token;
const userStorageLoginRefreshToken = CONFIG.user.storage_login_refresh_token;

export default function removeUserTokensStorage() {
    localStorage.removeItem(userStorageLoginToken);
    localStorage.removeItem(userStorageLoginRefreshToken);

    sessionStorage.removeItem(userStorageLoginToken);
    sessionStorage.removeItem(userStorageLoginRefreshToken);
}

export function getUserToken() {
    let userToken = localStorage.getItem(userStorageLoginToken);
    if(!userToken) {
        userToken = sessionStorage.getItem(userStorageLoginToken);
    }

    return userToken;
}

function refreshUserToken() {
    let userTokenRefreshToken = localStorage.getItem(userStorageLoginRefreshToken);
    if(!userTokenRefreshToken) {
        userTokenRefreshToken = sessionStorage.getItem(userStorageLoginRefreshToken);
    }

    return axios.post("/api/user/refresh-token", {
        refresh_token: userTokenRefreshToken
    }).then(result => {
        const token = result.data ? result.data.token : null;
        if (result.status === 200 && token) {
            if(localStorage.getItem(userStorageLoginToken)) {
                localStorage.setItem(userStorageLoginToken, token);
            } else if(sessionStorage.getItem(userStorageLoginToken)) {
                sessionStorage.setItem(userStorageLoginToken, token);
            }

            return true;
        } else {
            removeUserTokensStorage();

            return false;
        }
    }).catch((err) => {
        if(err.response.status === 401) {
            removeUserTokensStorage();
        }

        return false;
    });
}

export const userLoggedIn = () => {
    const userToken = getUserToken();
    if(userToken) {
        const formData = new FormData();
        formData.append('token', userToken);

        return axios.post("/api/user/validate-token", formData, {
            headers: {'Content-Type': 'multipart/form-data'}
        }).then(result => {
            if(result && result.data && result.data.success) {
                return true;
            }
        }).catch(function(e) {
            if(e.response.status === 401) {
                return refreshUserToken().then((result) => {
                    return result;
                });
            }

            return false;
        });
    }

    return Promise.resolve(false);
}

export const userEmail = () => {
    return userData().then(data => {
        if(data.email) {
            return data.email;
        }

        return null;
    });
}

export const userData = () => {
    const userToken = getUserToken();
    if(userToken) {
        return axios.get("/api/user/data", {
            headers: { Authorization: `Bearer ${userToken}` }
        }).then(result => {
            if(result && result.data && result.data.email) {
                return result.data;
            }
        }).catch(() => {
            return {};
        });
    }

    return Promise.resolve({});
}

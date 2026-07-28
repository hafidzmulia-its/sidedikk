import http from 'k6/http';
import { check, sleep } from 'k6';

export const options = {
    vus: Number(__ENV.VUS || 20),
    duration: __ENV.DURATION || '1m',
    thresholds: {
        http_req_failed: ['rate<0.05'],
        http_req_duration: ['p(50)<1200', 'p(95)<3000'],
    },
};

const baseUrl = __ENV.BASE_URL || 'http://127.0.0.1:8000';
const userEmail = __ENV.USER_EMAIL || 'demo@sidedikk.test';
const userPassword = __ENV.USER_PASSWORD || 'password';

function extractCsrfToken(html) {
    const match = html.match(/name="_token"\s+value="([^"]+)"/i);
    return match ? match[1] : null;
}

export default function () {
    const jar = http.cookieJar();

    const loginPage = http.get(`${baseUrl}/login`);
    const loginToken = extractCsrfToken(loginPage.body);

    check(loginPage, {
        'login page ok': (response) => response.status === 200,
        'login token exists': () => loginToken !== null,
    });

    if (!loginToken) {
        return;
    }

    const loginResponse = http.post(`${baseUrl}/login`, {
        _token: loginToken,
        email: userEmail,
        password: userPassword,
    }, {
        redirects: 0,
        jar,
    });

    check(loginResponse, {
        'login redirect': (response) => response.status === 302,
    });

    const dashboard = http.get(`${baseUrl}/dashboard`, { jar });
    check(dashboard, {
        'dashboard ok': (response) => response.status === 200,
    });

    const dashboardToken = extractCsrfToken(dashboard.body);

    if (!dashboardToken) {
        return;
    }

    const startScreening = http.post(`${baseUrl}/screenings/start`, {
        _token: dashboardToken,
    }, {
        redirects: 0,
        jar,
    });

    check(startScreening, {
        'start screening redirect': (response) => response.status === 302,
    });

    sleep(1);
}

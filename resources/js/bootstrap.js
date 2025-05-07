import axios from "axios";

import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.axios = axios;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: "pusher",
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: import.meta.env.VITE_PUSHER_APP_TLS === "true",
    channelAuthorization: {
        endpoint: "http://127.0.0.1:8000/api/employer/broadcasting/auth",
        headers: {
            Authorization:
                "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNzM3NjI1Njk1LCJleHAiOjE3Mzc2Mjc0OTUsIm5iZiI6MTczNzYyNTY5NSwianRpIjoiUllZTXhDbVc0RkxhZWl4eiIsInN1YiI6IjEiLCJwcnYiOiJlNTM1NGNiNWJkN2NhN2Y0MDM2ODMzYTA5NWY0MDkyODgyZDk3MzYwIiwicm9sZSI6ImVtcGxveWVyIn0.g0fETs3eChssAshGthWFa_GbgDMzCdbrDEGcanktNSk",
        },
    },
});

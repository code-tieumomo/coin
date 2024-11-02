import Echo from "laravel-echo";
import Pusher from "pusher-js";

export default defineNuxtPlugin((nuxtApp) => {
  const config = useRuntimeConfig();

  window.Pusher = Pusher;
  const token = localStorage.getItem("token") || "";

  const echo = new Echo({
    broadcaster: "reverb",
    key: config.public.reverbKey,
    wsHost: config.public.reverbHost,
    wsPort: config.public.reverbPort,
    wssPort: config.public.reverbPort,
    forceTLS: config.public.reverbForceTls,
    enabledTransports: ["ws", "wss"],
    authEndpoint: `${ config.public.serverBase }/broadcasting/auth`,
    auth: {
      headers: {
        Authorization: "Bearer " + token,
        Accept: "application/json"
      }
    }
  });

  return {
    provide: {
      echo
    }
  };
});

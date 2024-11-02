interface RedirectResponse {
  url: string;
}

export interface CallbackResponse {
  user: User;
  token: string;
}

export interface User {
  id: number;
  name: string;
  email: string;
  email_verified_at: any;
  google_id: string;
  token: string;
  created_at: string;
  updated_at: string;
  role: string;
  permission: string[];
}


export default defineNuxtPlugin(async (nuxtApp) => {
  const authStore = useAuthStore();
  const { $api } = useNuxtApp();

  // Initialize auth state on app load
  await authStore.initializeAuth();

  return {
    provide: {
      auth: {
        login: async () => {
          try {
            const config = useRuntimeConfig();
            const data: RedirectResponse = await $api.get(`${ config.public.apiBase }/auth/google/redirect`);
            if (data?.url) {
              window.location.href = data.url;
            }
          } catch (error) {
            console.error("Login error:", error);
          }
        },
        handleCallback: async (code: string) => {
          try {
            const config = useRuntimeConfig();
            const data: CallbackResponse = await $api.get(
              `${ config.public.apiBase }/auth/google/callback?code=${ code }`
            );

            if (data?.token && data?.user) {
              authStore.setToken(data.token);
              authStore.setUser(data.user);

              localStorage.setItem("token", data.token);
              localStorage.setItem("user", JSON.stringify(data.user));

              return true;
            }
            return false;
          } catch (error) {
            console.error("Callback error:", error);
            return false;
          }
        },
        logout: async () => await authStore.logout()
      }
    }
  };
});

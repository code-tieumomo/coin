import axios, { AxiosInstance, AxiosError } from "axios";
import { useAuthStore } from "~/stores/useAuthStore";
import { toast } from "vue-sonner";

export default defineNuxtPlugin((nuxtApp) => {
  const config = useRuntimeConfig();
  const authStore = useAuthStore();

  const api: AxiosInstance = axios.create({
    baseURL: config.public.apiBase,
    timeout: 10000,
    headers: {
      "Content-Type": "application/json",
      "Accept": "application/json"
    }
  });

  api.interceptors.request.use(
    (config) => {
      const token = authStore.token;
      if (token) {
        config.headers.Authorization = `Bearer ${ token }`;
      }
      return config;
    },
    (error) => {
      return Promise.reject(error);
    }
  );

  api.interceptors.response.use(
    (response) => response,
    async (error: AxiosError) => {
      if (error.response?.status === 401) {
        await authStore.logout();
        return Promise.reject(error);
      }

      if (error.response?.status === 403) {
        navigateTo("/unauthorized", { replace: true });
      }

      // Handle other errors
      handleApiError(error);
      return Promise.reject(error);
    }
  );

  const handleApiError = (error: AxiosError) => {
    let message = "An error occurred";

    if (error.response) {
      switch (error.response.status) {
        case 400:
          message = "Bad Request";
          break;
        case 401:
          message = "Unauthorized";
          break;
        case 403:
          message = "Forbidden";
          break;
        case 404:
          message = "Not Found";
          break;
        case 422:
          message = "Validation Error";
          break;
        case 500:
          message = "Server Error";
          break;
        default:
          message = "Something went wrong";
      }
    } else if (error.request) {
      message = "No response from server";
    }

    // TODO: Implement a toast notification system
    toast.error(error.response?.data?.message || message);
  };

  const apiService = {
    async get<T>(url: string, config = {}) {
      const response = await api.get<T>(url, config);
      return response.data;
    },

    async post<T>(url: string, data = {}, config = {}) {
      const response = await api.post<T>(url, data, config);
      return response.data;
    },

    async put<T>(url: string, data = {}, config = {}) {
      const response = await api.put<T>(url, data, config);
      return response.data;
    },

    async delete<T>(url: string, config = {}) {
      const response = await api.delete<T>(url, config);
      return response.data;
    },

    async patch<T>(url: string, data = {}, config = {}) {
      const response = await api.patch<T>(url, data, config);
      return response.data;
    }
  };

  return {
    provide: {
      api: apiService
    }
  };
});

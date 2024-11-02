import { defineStore } from "pinia";

interface User {
  id: number;
  name: string;
  email: string;
  email_verified_at: string;
  google_id: string;
  token: string;
  created_at: string;
  updated_at: string;
  role: string;
  permission: string[];
}

export const useAuthStore = defineStore("auth", {
  state: () => (<{
    user: User | null,
    token: string | null,
    loading: boolean
  } | null>{
    user: null,
    token: null,
    loading: false
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    getUser: (state) => state.user
  },

  actions: {
    setUser(user) {
      this.user = user;
    },
    setToken(token) {
      this.token = token;
    },
    setLoading(loading) {
      this.loading = loading;
    },
    setRoleAndPermission(role: string, permission: string[]) {
      console.log("Setting role and permission", role, permission);
      this.user.role = role;
      this.user.permission = permission;
    },
    async initializeAuth() {
      this.loading = true;
      try {
        const token = localStorage.getItem("token");
        if (token) {
          this.token = token;
          // Verify token and get fresh user data
          const config = useRuntimeConfig();
          const { data: userData } = await useFetch(`${ config.public.apiBase }/auth/user`, {
            headers: {
              "Authorization": `Bearer ${ token }`
            }
          });

          if (userData.value) {
            this.user = userData.value;
          } else {
            // Token is invalid
            await this.logout();
          }
        }
      } catch (error) {
        console.error("Auth initialization error:", error);
        await this.logout();
      } finally {
        this.loading = false;
      }
    },
    async logout() {
      try {
        const token = localStorage.getItem("token");
        if (token) {
          this.token = token;
          // Verify token and get fresh user data
          const config = useRuntimeConfig();
          await useFetch(`${ config.public.apiBase }/auth/logout`, {
            headers: {
              "Authorization": `Bearer ${ token }`
            },
            method: "DELETE"
          });

          this.user = null;
          this.token = null;
          localStorage.removeItem("token");
          localStorage.removeItem("user");
          navigateTo("/login");
        }
      } catch (error) {
        console.error("Logout error:", error);
      }
    },
    hasRole(role: string) {
      return this.user?.role === role;
    },
    hasPermission(permission: string) {
      return this.user?.permission?.includes(permission);
    }
  }
});

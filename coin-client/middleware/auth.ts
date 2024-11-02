export default defineNuxtRouteMiddleware((to, from) => {
  const authStore = useAuthStore();

  // If user is not authenticated and not on login-related pages
  if (!authStore.isAuthenticated && !["login", "auth-callback"].includes(to.name as string)) {
    return navigateTo("/login");
  }

  // If user is authenticated and has not selected role
  if (!authStore.user?.role) {
    return navigateTo("/role");
  }

  const meta = to.meta.meta as { roles: string[]; permission: string };
  const requiredRoles = meta?.roles as [];
  const requiredPermission = meta?.permission as string;

  // If user does not have required role or permission
  if (requiredRoles && !requiredRoles.includes(authStore.user.role as never)) {
    return navigateTo("/unauthorized");
  }

  if (requiredPermission && !authStore.hasPermission(requiredPermission)) {
    return navigateTo("/unauthorized");
  }

  // If user is authenticated and trying to access login page
  if (authStore.isAuthenticated && to.name === "login") {
    return navigateTo("/");
  }
});

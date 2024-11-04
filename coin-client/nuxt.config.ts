// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  ssr: false,
  compatibilityDate: "2024-04-03",
  devtools: { enabled: true },
  css: ["~/assets/css/app.scss"],

  app: {
    pageTransition: { name: "page", mode: "out-in" }
  },

  runtimeConfig: {
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE,
      serverBase: process.env.NUXT_PUBLIC_SERVER_BASE,

      reverbHost: process.env.NUXT_PUBLIC_REVERB_HOST,
      reverbPort: process.env.NUXT_PUBLIC_REVERB_PORT,
      reverbKey: process.env.NUXT_PUBLIC_REVERB_APP_KEY,
      reverbForceTls: process.env.NUXT_PUBLIC_REVERB_FORCE_TLS === "true",
      reverbScheme: process.env.NUXT_PUBLIC_REVERB_SCHEME
    }
  },

  modules: [
    "@pinia/nuxt",
    "@nuxtjs/tailwindcss",
    "shadcn-nuxt",
    "@nuxt/icon",
    "@vueuse/nuxt"
  ],

  shadcn: {
    prefix: "",
    componentDir: "./components/ui"
  },
  components: [
    {
      path: "~/components/ui",
      pathPrefix: false
    }
  ]
});

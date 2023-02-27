// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  // env: {
  //   base_url: process.env.BASE_URL || 'http://localhost:3000',
  //   base_url_wp: process.env.BASE_URL_WP || 'http://tooluploadfile.local'
  // },
  css: [
    'primevue/resources/themes/saga-blue/theme.css',
    'primevue/resources/primevue.css',
    'primeicons/primeicons.css',
    'bootstrap/dist/css/bootstrap.min.css'
  ],
  build: {
    transpile: ['primevue']
  },
  runtimeConfig: {
    public: {
      base_url: process.env.BASE_URL ,
      base_url_wp: process.env.BASE_URL_WP
      // base_url: process.env.BASE_URL || 'http://localhost:3000',
      // base_url_wp: process.env.BASE_URL_WP || 'http://tooluploadfile.local'
    }
  }
  // buildModules: [
  //   '@nuxtjs/dotenv'
  // ]

})

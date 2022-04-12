export default {
  mode: 'universal',

  env: {
    // 认证客户端URL， process.env.authURL
    authURL: process.env.NODE_ENV === 'dev' ? '//localhost:7000': '//login.mengxuegu.com'
  },

  modules: [
    '@nuxtjs/axios',
    'cookie-universal-nuxt', // 针对服务端操作cookie
  ],

  axios: {
    proxy: true, //开启代理转发
    prefix: '/api' // 请求接口添加前缀 /api   /test > /api/test
  },

  proxy: { // 代理转发 
    '/api': { // /api/test > http://mengxuegu.com:7300/mock/5ee6e6a9e56c02034c4c2e89/blog-web/test
      target: 'http://mengxuegu.com:7300/mock/5ee6e6a9e56c02034c4c2e89/blog-web',
      pathRewrite: {'^/api': ''}
    }
  },
  
  /*
  ** Headers of the page
  */
  head: {
    title: '梦学谷博客社区门户网',
    meta: [
      { charset: 'utf-8' },
      { name: 'viewport', content: 'width=device-width, initial-scale=1' },
      { hid: 'description', name: 'description', content: 'IT技术交流，java开发问答_前端问答' }
    ],
    link: [
      { rel: 'icon', type: 'image/x-icon', href: '/meng.ico' }
    ]
  },
  /*
  ** Customize the progress-bar color
  */
  loading: { color: '#fff' },
  /*
  ** Global CSS
  */
  css: [
    // 全局引入样式 
    // 针对 element-ui 组件的各种样式 
    'element-ui/lib/theme-chalk/index.css',
    // 自定义主题样式
    '@/assets/theme/index.css',
    // 布局样式 
    'element-ui/lib/theme-chalk/display.css',
    // 项目自定义的全局样式
    '@/assets/css/global.css',
    // mavon-editor 编辑器使用的样式
    'mavon-editor/dist/css/index.css'
  ],
  /*
  ** Plugins to load before mounting the App
  */
  plugins: [
    '~/plugins/element-ui.js',
    '~/plugins/interceptor.js',
    '~/api/article.js',
    '~/api/common.js',
    // 注意：只能在客户端使用，window
    {src: '~/plugins/mavon-editor.js', mode: 'client'},
    '~/api/question.js',
    '~/api/user.js'
  ],
  /*
  ** Nuxt.js dev-modules
  */
  buildModules: [
  ],
  /*
  ** Nuxt.js modules
  modules: [
  ], */
  /*
  ** Build configuration
  */
  build: {
    // 将位于 node_modules 目录下的element-ui导出
    transpile: [/^element-ui/],
    /*
    ** webpack 自定义配置
    */
    extend (config, ctx) {
    }
  }
}
import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      name: 'home',
      component: () => import('../components/home/HomePage.vue'),
    },
    {
      path: '/player/:sessid',
      name: 'server',
      component: () => import('../components/server/ServerView.vue'),
      props: true,
    },
    {
      path: '/jukebox/:sessid',
      name: 'client',
      component: () => import('../components/client/ClientView.vue'),
      props: true,
    },
  ],
})

export default router

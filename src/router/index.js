
/**
 * router/index.ts
 *
 * Automatic routes for `./src/pages/*.vue`
 */

// Composables
import { createRouter, createWebHistory } from 'vue-router/auto'


const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),

  routes: [

    {
      path: '/',
      name: 'home',
      component: () => import('@/pages/index.vue'),  // Or any other component for the root
    },
    {
      path: '/page',
      name: 'page',
      component: () => import('@/pages/page.vue'),
    },


    // ROUTER FOR THE ENGINEER AND ARCHITECT 

      {
      path: '/pages/Engr/dashboard',
      name: 'engrdashboard',
      component: () => import('@/pages/Engr/dashboard.vue'),
    },

     {
      path: '/pages/Engr/status',
      name: 'engrstatus1',
      component: () => import('@/pages/Engr/status.vue'),
    },


   

    // ROUTER FOR THE CPDO

  {
      path: '/pages/CPDO/CPDOdashboard',
      name: 'cpdopage',
      component: () => import('@/pages/CPDO/CPDOdashboard.vue'),
    },

     {
      path: '/pages/CPDO/status',
      name: 'cpdostatus',
      component: () => import('@/pages/CPDO/status.vue'),
    },




    // ROUTER FOR THE ADMIN

      {
      path: '/pages/Admin/dashboard',
      name: 'page',
      component: () => import('@/pages/Admin/dashboard.vue'),
    },

      {
      path: '/pages/Admin/status',
      name: 'status',
      component: () => import('@/pages/Admin/status.vue'),
    },

       {
      path: '/pages/Admin/status2',
      name: 'status2',
      component: () => import('@/pages/Admin/status2.vue'),
    },



// ADD MORE IN HERE USING THE {  Path: ‘/’},

  ],


})

export default router




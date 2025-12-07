import { createRouter, createWebHistory } from '@ionic/vue-router';
import { RouteRecordRaw } from 'vue-router';
import HomePage from '../views/HomePage.vue'
import BooksListPage from '../views/BooksListPage.vue'
import AddEditBookPage from '../views/AddEditBookPage.vue'

const routes: Array<RouteRecordRaw> = [
  {
    path: '/',
    redirect: '/home'
  },
  {
    path: '/home',
    name: 'Home',
    component: HomePage
  },
  {
    path: '/books',
    name: 'BooksList',
    component: BooksListPage
  },
  {
    path: '/books/add',
    name: 'AddBook',
    component: AddEditBookPage
  },
  {
    path: '/books/edit/:id',
    name: 'EditBook',
    component: AddEditBookPage
  }
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
})

export default router

import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

// Auth views
import LoginView from '@/views/auth/LoginView.vue'
import RegisterView from '@/views/auth/RegisterView.vue'

// General
import HomeView from '@/views/HomeView.vue'

// Student views
import StudentDashboardView from '@/views/student/StudentDashboardView.vue'
import TutorListView from '@/views/student/TutorListView.vue'
import TutorDetailView from '@/views/student/TutorDetailView.vue'
import BookingView from '@/views/student/BookingView.vue'
import StudentBookingsView from '@/views/student/StudentBookingsView.vue'
import StudentProfileView from '@/views/student/StudentProfileView.vue'

// Tutor views
import TutorDashboardView from '@/views/tutor/TutorDashboardView.vue'
import TutorProfileListView from '@/views/tutor/TutorProfileListView.vue'
import TutorProfileEditView from '@/views/tutor/TutorProfileEditView.vue'
import TutorBookingsView from '@/views/tutor/TutorBookingsView.vue'

// Admin views
import AdminDashboardView from '@/views/admin/AdminDashboardView.vue'
import AdminUsersView from '@/views/admin/AdminUsersView.vue'
import AdminUserEditView from '@/views/admin/AdminUserEditView.vue'
import AdminReviewsView from '@/views/admin/AdminReviewsView.vue'

const routes = [
  // Public
  { path: '/login', name: 'login', component: LoginView, meta: { guest: true } },
  { path: '/register', name: 'register', component: RegisterView, meta: { guest: true } },

  // Authenticated
  { path: '/', name: 'home', component: HomeView, meta: { requiresAuth: true } },

  // Tutor directory (public)
  { path: '/tutors', name: 'tutors', component: TutorListView },
  { path: '/tutors/:id', name: 'tutor-detail', component: TutorDetailView, props: true },

  // Student
  { path: '/student/dashboard', name: 'student-dashboard', component: StudentDashboardView, meta: { requiresAuth: true, role: 'student' } },
  { path: '/book/:tutorProfileId', name: 'book-tutor', component: BookingView, props: true, meta: { requiresAuth: true, role: 'student' } },
  { path: '/student/bookings', name: 'student-bookings', component: StudentBookingsView, meta: { requiresAuth: true, role: 'student' } },
  { path: '/student/profile', name: 'student-profile', component: StudentProfileView, meta: { requiresAuth: true, role: 'student' } },

  // Tutor
  { path: '/tutor/dashboard', name: 'tutor-dashboard', component: TutorDashboardView, meta: { requiresAuth: true, role: 'tutor' } },
  { path: '/tutor/profiles', name: 'tutor-profiles', component: TutorProfileListView, meta: { requiresAuth: true, role: 'tutor' } },
  { path: '/tutor/profiles/new', name: 'tutor-profile-new', component: TutorProfileEditView, meta: { requiresAuth: true, role: 'tutor' } },
  { path: '/tutor/profiles/:id/edit', name: 'tutor-profile-edit', component: TutorProfileEditView, props: true, meta: { requiresAuth: true, role: 'tutor' } },
  { path: '/tutor/bookings', name: 'tutor-bookings', component: TutorBookingsView, meta: { requiresAuth: true, role: 'tutor' } },

  // Admin
  { path: '/admin/dashboard', name: 'admin-dashboard', component: AdminDashboardView, meta: { requiresAuth: true, role: 'admin' } },
  { path: '/admin/users', name: 'admin-users', component: AdminUsersView, meta: { requiresAuth: true, role: 'admin' } },
  { path: '/admin/users/:id/edit', name: 'admin-user-edit', component: AdminUserEditView, props: true, meta: { requiresAuth: true, role: 'admin' } },
  { path: '/admin/reviews', name: 'admin-reviews', component: AdminReviewsView, meta: { requiresAuth: true, role: 'admin' } },

  // Catch all
  { path: '/:pathMatch(.*)*', redirect: '/' }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Navigation guards
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    return next({ name: 'login' })
  }

  if (to.meta.guest && authStore.isAuthenticated) {
    return next({ name: 'home' })
  }

  if (to.meta.role && authStore.userRole !== to.meta.role) {
    return next({ name: 'home' })
  }

  next()
})

export default router

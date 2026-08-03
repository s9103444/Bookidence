import FrontLayout from "../layouts/FrontLayout.vue";

export default [
  {
    path: "/",
    component: FrontLayout,
    children: [
      {
        path: "",
        name: "home",
        meta: {  noPadding: true },
        component: () => import("../views/front/HomeView.vue"),
      },
      {
        path: "forgot-password",
        name: "forgot-password",
        component: () => import("../views/front/ForgotPasswordView.vue"),
      },
      {
        path: "profile",
        name: "profile",
        component: () => import("../views/front/ProfileView.vue"),
      },
      {
        path: "settings",
        name: "settings",
        component: () => import("../views/front/SettingsView.vue"),
      },
      {
        path: "friends",
        name: "friends",
        component: () => import("../views/front/FriendsView.vue"),
      },
      {
        path: "study",
        name: "study",
        component: () => import("../views/front/StudyView.vue"),
        meta: { noPadding: true },
      },
      {
        path: "search",
        name: "search",
        component: () => import("../views/front/SearchView.vue"),
      },
      {
        path: "books/:id",
        name: "book-detail",
        component: () => import("../views/front/BookDetailView.vue"),
      },
      {
        path: "guilds",
        name: "guilds",
        component: () => import("../views/front/GuildListView.vue"),
      },
      {
        path: "guilds/:id",
        name: "guild-detail",
        component: () => import("../views/front/GuildDetailView.vue"),
      },
      {
        path: "events/:id",
        name: "event-detail",
        component: () => import("../views/front/EventView.vue"),
      },
      {
        path: "test",
        name: "test",
        component: () => import("../views/front/TestView.vue"),
      },
    ]
  },
  {
    path: "/login",
    name: "login",
    component: () => import("../views/front/LoginView.vue"),
  },
  {
    path: "/register",
    name: "register",
    component: () => import("../views/front/RegisterView.vue"),
  }
]

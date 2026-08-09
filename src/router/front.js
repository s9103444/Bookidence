import FrontLayout from "../layouts/FrontLayout.vue";
// import FrontLayoutWithoutHeader from "../layouts/FrontLayoutWithoutHeader.vue";
import GuildSidebarLayout from "../layouts/GuildSidebarLayout.vue";

export default [

  {
    path: "/",
    component: FrontLayout,
    children: [
      {
        path: "",
        name: "home",
        meta: { noPadding: true },
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
        path: "",
        component: GuildSidebarLayout,
        meta: { noPadding: true },
        children: [
          {
            path: "events/apply",
            name: "event-apply",
            component: () => import("../views/front/EventApply.vue"),
          },
          {
            path: "events/:id",
            name: "event-detail",
            component: () => import("../views/front/EventView.vue"),
          },
          {
            path: "report",
            name: "report",
            component: () => import("../views/front/Report.vue"),
          },
          {
            path: "report/:id",
            name: "report-detail",
            component: () => import("../views/front/ReportDetails.vue"),
          },
          {
            path: "members-content",
            name: "members-content",
            component: () => import("../views/front/GuildMembersContent.vue"),
          },
          {
            path: "reading-scheduleS",
            name: "reading-scheduleS",
            component: () => import("../views/front/ReadingScheduleS.vue"),
          },
        ],
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

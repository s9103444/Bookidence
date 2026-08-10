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
        children: [
          {
            path: "",
            name: "guild-detail",
            component: () => import("../views/front/GuildDetailView.vue"),
          },
          {
            // 公會內部功能頁（活動、檢舉、設定、討論區）共用 GuildSidebarLayout 外框
            path: "",
            component: GuildSidebarLayout,
            meta: { noPadding: true },
            children: [
              {
                path: "events/apply",
                name: "event-apply",
                component: () => import("../views/front/GuildEventApply.vue"),
              },
              {
                path: "events/:eventId",
                name: "event-detail",
                component: () => import("../views/front/GuildEventView.vue"),
              },
              {
                path: "report",
                name: "report",
                component: () => import("../views/front/GuildReport.vue"),
              },
              {
                path: "report/:reportId",
                name: "report-detail",
                component: () => import("../views/front/GuildReportDetails.vue"),
              },
              {
                path: "settings",
                name: "guild-settings",
                component: () => import("../views/front/GuildSettingsView.vue"),
              },
              {
                path: "reading-schedule",
                name: "guild-reading-schedule",
                component: () => import("../views/front/GuildReadingScheduleView.vue"),
              },
              {
                path: "members",
                name: "guild-members",
                component: () => import("../views/front/GuildMembersView.vue"),
              },
              {
                path: "discussion/:milestoneId",
                name: "guild-discussion",
                component: () => import("../views/front/GuildDiscussionView.vue"),
              },
            ],
          },
        ],
      },
      {
        path: "news",
        name: "news",
        component: () => import("../views/front/NewsView.vue"),
      },
      {
        path: "create-guilds",
        name: "create-guilds",
        component: () => import("../views/front/CreateBookGuilds.vue"),
      },
      {
        path: "test",
        name: "test",
        component: () => import("../views/front/TestView.vue"),
      },
    ],
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
  },
];
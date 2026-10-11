import { defineConfig } from 'vitepress'

export default defineConfig({
  title: 'cakephp-calendar',
  description: 'Month calendars and iCalendar exports for CakePHP applications.',
  base: '/cakephp-calendar/',
  cleanUrls: true,
  srcExclude: ['README.md'],
  lastUpdated: true,
  head: [
    ['link', { rel: 'icon', href: '/cakephp-calendar/favicon.svg', type: 'image/svg+xml' }],
  ],
  sitemap: { hostname: 'https://dereuromark.github.io/cakephp-calendar/' },
  themeConfig: {
    logo: '/logo.svg',
    nav: [
      { text: 'Installation', link: '/guide/installation' },
      { text: 'Getting started', link: '/guide/getting-started' },
      { text: 'Reference', link: '/reference/calendar' },
    ],
    sidebar: [
      {
        text: 'Guide',
        items: [
          { text: 'Overview', link: '/guide/' },
          { text: 'Installation', link: '/guide/installation' },
          { text: 'Getting started', link: '/guide/getting-started' },
          { text: 'Contributing', link: '/guide/contributing' },
        ],
      },
      {
        text: 'Reference',
        items: [
          { text: 'Calendar', link: '/reference/calendar' },
          { text: 'iCalendar exports', link: '/reference/icalendar' },
          { text: 'Google Calendar', link: '/reference/google-calendar' },
        ],
      },
    ],
    search: { provider: 'local' },
    socialLinks: [{ icon: 'github', link: 'https://github.com/dereuromark/cakephp-calendar' }],
    editLink: {
      pattern: 'https://github.com/dereuromark/cakephp-calendar/edit/cake6/docs/:path',
      text: 'Edit this page on GitHub',
    },
    footer: { message: 'Released under the MIT License.' },
  },
})

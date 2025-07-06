export default [
  {
    path: '/parties',
    name: 'parties_list',
    component: () => import('@/pages/home/pages/PartiesList.vue') // صفحه لیست احزاب
  },
  {
    path: '/parties/:id',
    name: 'party_profile',
    component: () => import('@/pages/home/pages/PartyProfilePage.vue'), // صفحه پروفایل حزب
    props: true
  }
]

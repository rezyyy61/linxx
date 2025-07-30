export default [
  {
    path: '/parties',
    name: 'parties_list',
    component: () => import('@/pages/home/pages/PartiesList.vue')
  },
  {
    path: '/parties/:id',
    name: 'party_profile',
    component: () => import('@/pages/home/pages/PartyProfilePage.vue'),
    props: true
  }
]

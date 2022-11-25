import Layout from '@/layout';

const tableRoutes = {
  path: '/table',
  component: Layout,
  redirect: '/table/complex-table',
  name: 'Complex Table',
  meta: {
    title: 'Quản lý',
    icon: 'table',
    permissions: ['view menu table'],
  },
  children: [
    // {
    //   path: 'drag-table',
    //   component: () => import('@/views/table/DragTable'),
    //   name: 'DragTable',
    //   meta: { title: 'dragTable' },
    // },
    // {
    //   path: 'inline-edit-table',
    //   component: () => import('@/views/table/InlineEditTable'),
    //   name: 'InlineEditTable',
    //   meta: { title: 'inlineEditTable' },
    // },
    {
      path: 'quan-ly-phong',
      component: () => import('@/views/table/QuanLyPhongTro'),
      name: 'QuanLyPhongTro',
      meta: { title: 'Quản lý phòng trọ' },
    },
    {
      path: 'quan-ly-dich-vu',
      component: () => import('@/views/table/QuanLyDichVu'),
      name: 'QuanLyDichVu',
      meta: { title: 'Quản lý dịch vụ' },
    },
    {
      path: 'quan-ly-hop-dong',
      component: () => import('@/views/table/QuanLyHopDong'),
      name: 'QuanLyHopDong',
      meta: { title: 'Quản lý hợp đồng' },
    },
  ],
};
export default tableRoutes;

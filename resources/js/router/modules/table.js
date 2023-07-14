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
      path: 'quan-ly-combo',
      component: () => import('@/views/table/QuanLyPhongTro'),
      name: 'QuanLyPhongTro',
      meta: { title: 'Quản lý Combo' },
    },
    {
      path: 'quan-ly-dich-vu',
      component: () => import('@/views/table/QuanLyDichVu'),
      name: 'QuanLyDichVu',
      meta: { title: 'Quản lý dịch vụ' },
    },
    {
      path: 'quan-ly-hoa-don',
      component: () => import('@/views/table/QuanLyHopDong'),
      name: 'QuanLyHopDong',
      meta: { title: 'Quản lý hóa đơn' },
    },
    {
      path: 'tao-hoa-don',
      component: () => import('@/views/table/TaoHoaDon'),
      name: 'TaoHoaDon',
      meta: { title: 'Tạo hóa đơn' },
    },
  ],
};
export default tableRoutes;

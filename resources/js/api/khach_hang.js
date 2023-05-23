import request from '@/utils/request';

export function fetchList(query) {
  return request({
    url: '/khachhang/list',
    method: 'get',
    params: query,
  });
}

// export function fetchArticle(id) {
//   return request({
//     url: '/articles/' + id,
//     method: 'get',
//   });
// }

// export function fetchPv(id) {
//   return request({
//     url: '/articles/' + id + '/pageviews',
//     method: 'get',
//   });
// }

// export function createPhong(data) {
//   return request({
//     url: '/phong/addOrEdit',
//     method: 'post',
//     data,
//   });
// }

// export function deletePhong(data) {
//   return request({
//     url: '/phong/delete',
//     method: 'post',
//     data,
//   });
// }

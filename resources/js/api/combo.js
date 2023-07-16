import request from '@/utils/request';

export function fetchListCombo(query) {
  return request({
    url: '/combo/list',
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

export function createCombo(data) {
  return request({
    url: '/combo/update',
    method: 'post',
    data,
  });
}

// export function deletePhong(data) {
//   return request({
//     url: '/phong/delete',
//     method: 'post',
//     data,
//   });
// }

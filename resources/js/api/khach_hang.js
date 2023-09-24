import request from '@/utils/request';

export function fetchList(query) {
  return request({
    url: '/khachhang/list',
    method: 'get',
    params: query,
  });
}

export function listBirthDay(query) {
  return request({
    url: '/khachhang/birthday',
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

export function createKH(data) {
  return request({
    url: '/khachhang/store',
    method: 'post',
    data,
  });
}

export function deleteKH(data) {
  return request({
    url: '/khachhang/delete',
    method: 'post',
    data,
  });
}

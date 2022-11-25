import request from '@/utils/request';

export function fetchList(query) {
  return request({
    url: '/HoaDon/list',
    method: 'get',
    params: query,
  });
}

export function fetchArticle(id) {
  return request({
    url: '/articles/' + id,
    method: 'get',
  });
}

export function fetchPv(id) {
  return request({
    url: '/articles/' + id + '/pageviews',
    method: 'get',
  });
}

export function createHoaDon(data) {
  return request({
    url: '/HoaDon/addOrEdit',
    method: 'post',
    data,
  });
}

export function deleteHoaDon(data) {
  return request({
    url: '/HoaDon/delete',
    method: 'post',
    data,
  });
}

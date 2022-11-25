import request from '@/utils/request';

export function fetchList(query) {
  return request({
    url: '/HopDong/list',
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

export function createHopDong(data) {
  return request({
    url: '/HopDong/addOrEdit',
    method: 'post',
    data,
  });
}

export function deleteHopDong(data) {
  return request({
    url: '/HopDong/delete',
    method: 'post',
    data,
  });
}

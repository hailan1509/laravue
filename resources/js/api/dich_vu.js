import request from '@/utils/request';

export function fetchList(query) {
  return request({
    url: '/DichVu/list',
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

export function createDichVu(data) {
  return request({
    url: '/DichVu/addOrEdit',
    method: 'post',
    data,
  });
}

export function deleteDichVu(data) {
  return request({
    url: '/DichVu/delete',
    method: 'post',
    data,
  });
}

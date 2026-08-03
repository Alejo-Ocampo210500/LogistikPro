import axios from 'axios';

const defaultApiBaseUrl = process.env.NODE_ENV === 'production'
  ? '/api'
  : 'http://127.0.0.1:8000/api';

const api = axios.create({
  baseURL: process.env.VUE_APP_API_BASE_URL || defaultApiBaseUrl,
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
});

api.interceptors.request.use((config) => {
  const token = localStorage.getItem('logistikpro_token') || sessionStorage.getItem('logistikpro_token');

  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }

  return config;
});

export default api;

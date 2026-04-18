import { defineStore } from 'pinia';
import api from '../utils/api';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('user') || 'null'),
    token: localStorage.getItem('token') || null,
  }),
  
  getters: {
    isAuthenticated: (state) => !!state.token || !!state.user,
  },
  
  actions: {
    // For Sanctum CSRF cookie initialization using native fetch
    async getCsrfCookie() {
      const baseUrl = import.meta.env.VITE_BACKEND_URL || 'http://127.0.0.1:8000';
      await fetch(`${baseUrl}/sanctum/csrf-cookie`, {
        method: 'GET',
        headers: {
          'Accept': 'application/json'
        },
        credentials: 'include',
      });
    },
    
    async login(email, password) {
      await this.getCsrfCookie();
      
      const response = await api.post('/login', {
        email,
        password
      });
      
      if (response && response.data && response.data.token) {
        this.token = response.data.token;
        localStorage.setItem('token', this.token);
      }
      
      if (response && response.data && response.data.user) {
        this.user = response.data.user;
        localStorage.setItem('user', JSON.stringify(this.user));
      }

      return response;
    },
    
    async fetchUser() {
      try {
        const response = await api.get('/me');
        this.user = response.data.user;
        localStorage.setItem('user', JSON.stringify(this.user));
      } catch (error) {
        this.user = null;
        this.token = null;
        localStorage.removeItem('token');
        localStorage.removeItem('user');
      }
    },
    
    async logout() {
      try {
        await api.post('/logout');
      } catch (e) {
        console.error(e);
      } finally {
        this.user = null;
        this.token = null;
        localStorage.removeItem('token');
        localStorage.removeItem('user');
      }
    }
  }
});

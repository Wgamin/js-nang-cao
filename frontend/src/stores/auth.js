import { defineStore } from 'pinia';
import api from '../utils/api';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null,
  }),
  
  getters: {
    isAuthenticated: (state) => !!state.token || !!state.user,
  },
  
  actions: {
    // For Sanctum CSRF cookie initialization using native fetch
    async getCsrfCookie() {
      const baseUrl = 'http://127.0.0.1:8000';
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
      
      const data = await api.post('/login', {
        email,
        password
      });
      
      // If using token-based authentication
      if (data && data.access_token) {
        this.token = data.access_token;
        localStorage.setItem('token', this.token);
      }
      
      if (data && data.user) {
        this.user = data.user;
      }
      return data;
    },
    
    async fetchUser() {
      try {
        const data = await api.get('/user');
        this.user = data;
      } catch (error) {
        this.user = null;
        this.token = null;
        localStorage.removeItem('token');
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
      }
    }
  }
});

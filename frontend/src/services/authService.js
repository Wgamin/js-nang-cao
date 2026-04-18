import api from '../utils/api';

export const authService = {
  /**
   * Đăng nhập và lưu thông tin
   */
  async login(email, password) {
    const response = await api.post('/login', { email, password });
    const { token, user } = response.data || {};
    
    localStorage.setItem('token', token);
    localStorage.setItem('user', JSON.stringify(user));
    
    return response.data;
  },

  /**
   * Đăng xuất
   */
  async logout() {
    try {
      await api.post('/logout');
    } finally {
      localStorage.removeItem('token');
      localStorage.removeItem('user');
    }
  },

  /**
   * Lấy thông tin User hiện tại từ localStorage
   */
  getUser() {
    const user = localStorage.getItem('user');
    return user ? JSON.parse(user) : null;
  },

  /**
   * Kiểm tra đã đăng nhập chưa
   */
  isAuthenticated() {
    return !!localStorage.getItem('token');
  },

  /**
   * Điều hướng dựa trên Role
   */
  getRedirectPath(role) {
    switch (role) {
      case 'admin':
        return '/admin/dashboard';
      case 'staff':
        return '/staff';
      case 'teacher':
        return '/teacher/classes';
      case 'parent':
      case 'guardian':
        return '/my-children';
      case 'student':
        return '/my-classes';
      default:
        return '/';
    }
  }
};

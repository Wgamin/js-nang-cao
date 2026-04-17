import api from '../utils/api';

export const authService = {
  /**
   * Đăng nhập và lưu thông tin
   */
  async login(username, password) {
    const response = await api.post('/login', { username, password });
    const { access_token, user } = response.data;
    
    localStorage.setItem('access_token', access_token);
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
      localStorage.removeItem('access_token');
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
    return !!localStorage.getItem('access_token');
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

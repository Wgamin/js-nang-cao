const BASE_URL = import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000/api';

/**
 * Helper function for fetch API requests
 * Automatically includes credentials and common headers
 */
export const customFetch = async (endpoint, options = {}) => {
  const url = `${BASE_URL}${endpoint}`;
  
  const defaultHeaders = {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  };

  // If we have a local token stored (if not using Sanctum cookies only)
  const token = localStorage.getItem('token');
  if (token) {
    defaultHeaders['Authorization'] = `Bearer ${token}`;
  }

  const config = {
    ...options,
    headers: {
      ...defaultHeaders,
      ...options.headers,
    },
    // Required for Sanctum session cookies (CSRF)
    credentials: 'include',
  };

  if (options.body && typeof options.body === 'object' && !(options.body instanceof FormData)) {
    config.body = JSON.stringify(options.body);
  }

  const response = await fetch(url, config);

  if (!response.ok) {
    if (response.status === 401) {
      console.error('Unauthorized, handling redirect...');
      // Logic for removing token or redirecting to /login
    }
    
    // Attempt to parse JSON error message from backend
    let errorData;
    try {
      errorData = await response.json();
    } catch (e) {
      errorData = { message: 'An unexpected error occurred' };
    }
    
    const error = new Error(errorData.message || response.statusText);
    error.status = response.status;
    error.response = response;
    error.data = errorData;
    throw error;
  }

  // Parse JSON automatically, unless it's a 204 No Content response
  if (response.status !== 204) {
    try {
      return await response.json();
    } catch {
      return null;
    }
  }
  
  return null;
};

// Aliases for convenience
export default {
  get: (url, options) => customFetch(url, { ...options, method: 'GET' }),
  post: (url, body, options) => customFetch(url, { ...options, method: 'POST', body }),
  put: (url, body, options) => customFetch(url, { ...options, method: 'PUT', body }),
  delete: (url, options) => customFetch(url, { ...options, method: 'DELETE' }),
};

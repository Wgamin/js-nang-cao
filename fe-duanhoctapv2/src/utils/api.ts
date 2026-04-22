const BASE_URL = import.meta.env.VITE_API_BASE_URL;
export const useApi = () => {
  const fetchApi = async (endpoint: string, options: RequestInit = {}): Promise<any> => {
    const token = localStorage.getItem('token')

    const defaultHeaders: Record<string, string> = {
      'Content-Type': 'application/json',
      Accept: 'application/json',
    }

    if (token) {
      defaultHeaders['Authorization'] = `Bearer ${token}`
    }

    const response = await fetch(`${BASE_URL}${endpoint}`, {
      ...options,
      headers: {
        ...defaultHeaders,
        ...(options.headers as Record<string, string>),
      },
    })

    const data = await response.json()

    if (!response.ok) {
      if (response.status === 401) {
        localStorage.clear()
        window.location.href = '/login'
      }
      throw new Error(data.message || 'Đã xảy ra lỗi')
    }

    return data
  }

  return { fetchApi }
}

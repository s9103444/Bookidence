import { API_STATIC } from '@/common/api'

export function resolveImageUrl(path, fallback) {
    if (!path) return fallback
    return path.startsWith('http') ? path : `${API_STATIC}/uploads/${path}`
}
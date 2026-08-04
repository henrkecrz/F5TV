import { Channel, Content, Coupon, LiveSchedule, Plan, Series } from '../types';

const API_BASE = (import.meta.env.VITE_API_URL || '/api').replace(/\/$/, '');

type RequestOptions = RequestInit & {
  query?: Record<string, string | number | boolean | undefined | null>;
};

async function request<T>(path: string, options: RequestOptions = {}): Promise<T> {
  const url = new URL(`${API_BASE}/${path.replace(/^\//, '')}`, window.location.origin);

  if (options.query) {
    Object.entries(options.query).forEach(([key, value]) => {
      if (value !== undefined && value !== null && value !== '') {
        url.searchParams.set(key, String(value));
      }
    });
  }

  const response = await fetch(url.toString(), {
    ...options,
    headers: {
      'Content-Type': 'application/json',
      ...(options.headers || {}),
    },
  });

  const data = await response.json().catch(() => ({}));

  if (!response.ok) {
    throw new Error(data?.message || data?.error || `Erro HTTP ${response.status}`);
  }

  return data as T;
}

function normalizeContent(row: any): Content {
  return {
    id: row.id,
    type: row.type,
    title: row.title,
    shortDescription: row.short_description ?? row.shortDescription ?? '',
    fullDescription: row.full_description ?? row.fullDescription ?? '',
    categoryId: row.category_id ?? row.categoryId ?? '',
    genre: row.genre,
    ageRating: row.age_rating ?? row.ageRating,
    year: Number(row.year),
    duration: row.duration,
    cast: parseJsonArray(row.cast_members ?? row.cast ?? []),
    directors: parseJsonArray(row.directors ?? []),
    coverUrl: row.cover_url ?? row.coverUrl,
    bannerUrl: row.banner_url ?? row.bannerUrl,
    trailerUrl: row.trailer_url ?? row.trailerUrl ?? '',
    videoUrl: row.video_url ?? row.videoUrl ?? '',
    status: row.status,
    isFeatured: Boolean(Number(row.is_featured ?? row.isFeatured ?? 0)),
    isFree: Boolean(Number(row.is_free ?? row.isFree ?? 0)),
    isExclusive: Boolean(Number(row.is_exclusive ?? row.isExclusive ?? 1)),
    publishDate: row.publish_date ?? row.publishDate ?? '',
    tags: parseJsonArray(row.tags ?? []),
    viewsCount: Number(row.views_count ?? row.viewsCount ?? 0),
  };
}

function normalizePlan(row: any): Plan {
  return {
    id: row.id,
    name: row.name,
    price: Number(row.price),
    features: parseJsonArray(row.features ?? []),
    active: Boolean(Number(row.active ?? 1)),
  };
}

function normalizeChannel(row: any): Channel {
  return {
    id: row.id,
    name: row.name,
    description: row.description,
    logoText: row.logo_text ?? row.logoText,
    streamUrl: row.stream_url ?? row.streamUrl,
    active: Boolean(Number(row.active ?? 1)),
    status: row.status,
    category: row.category,
  };
}

function normalizeSchedule(row: any): LiveSchedule {
  return {
    id: row.id,
    channelId: row.channel_id ?? row.channelId,
    title: row.title,
    description: row.description,
    host: row.host,
    date: row.date,
    startTime: String(row.start_time ?? row.startTime ?? '').slice(0, 5),
    endTime: String(row.end_time ?? row.endTime ?? '').slice(0, 5),
    status: row.status,
    imageUrl: row.image_url ?? row.imageUrl,
    isFeatured: Boolean(Number(row.is_featured ?? row.isFeatured ?? 0)),
  };
}

function normalizeSeries(row: any): Series {
  return {
    id: row.id,
    title: row.title,
    description: row.description,
    coverUrl: row.cover_url ?? row.coverUrl,
    bannerUrl: row.banner_url ?? row.bannerUrl,
    genre: row.genre,
    status: row.status,
    viewsCount: Number(row.views_count ?? row.viewsCount ?? 0),
  };
}

function parseJsonArray(value: unknown): string[] {
  if (Array.isArray(value)) return value.map(String);
  if (typeof value === 'string') {
    try {
      const parsed = JSON.parse(value);
      return Array.isArray(parsed) ? parsed.map(String) : [];
    } catch (_) {
      return value ? [value] : [];
    }
  }
  return [];
}

export const hostingerApi = {
  async health() {
    return request<{ ok: boolean; database: string; service: string }>('health.php');
  },

  async getPlans(): Promise<Plan[]> {
    const data = await request<{ plans: any[] }>('billing.php', { query: { action: 'plans' } });
    return (data.plans || []).map(normalizePlan);
  },

  async getCategories() {
    const data = await request<{ categories: any[] }>('catalog.php', { query: { action: 'categories' } });
    return data.categories || [];
  },

  async getContents(params: { q?: string; type?: string; categoryId?: string } = {}): Promise<Content[]> {
    const data = await request<{ contents: any[] }>('catalog.php', {
      query: {
        q: params.q,
        type: params.type,
        category_id: params.categoryId,
      },
    });
    return (data.contents || []).map(normalizeContent);
  },

  async getContent(id: string) {
    const data = await request<{ content: any; reviews: any[] }>('catalog.php', { query: { action: 'content', id } });
    return { content: normalizeContent(data.content), reviews: data.reviews || [] };
  },

  async getSeries(): Promise<Series[]> {
    const data = await request<{ series: any[] }>('catalog.php', { query: { action: 'series' } });
    return (data.series || []).map(normalizeSeries);
  },

  async getChannels(): Promise<Channel[]> {
    const data = await request<{ channels: any[] }>('live.php');
    return (data.channels || []).map(normalizeChannel);
  },

  async getLiveSchedule(params: { date?: string; channelId?: string } = {}): Promise<LiveSchedule[]> {
    const data = await request<{ schedule: any[] }>('live.php', {
      query: {
        action: 'schedule',
        date: params.date,
        channel_id: params.channelId,
      },
    });
    return (data.schedule || []).map(normalizeSchedule);
  },

  async validateCoupon(code: string, planId: string) {
    return request<{ valid: boolean; coupon?: Coupon; message?: string }>('billing.php?action=validate-coupon', {
      method: 'POST',
      body: JSON.stringify({ code, planId }),
    });
  },

  async checkout(payload: { userId?: string; planId: string; paymentMethod: 'pix' | 'credit_card' | 'boleto'; couponCode?: string }) {
    return request<{ subscriptionId: string; paymentId: string; total: number; status: string }>('billing.php?action=checkout', {
      method: 'POST',
      body: JSON.stringify(payload),
    });
  },
};

export const isHostingerApiEnabled = () => import.meta.env.VITE_USE_HOSTINGER_API === 'true';

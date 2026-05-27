/**
 * @license
 * SPDX-License-Identifier: Apache-2.0
 */

export type UserRole = 'admin' | 'editor' | 'finance' | 'subscriber';
export type UserStatus = 'active' | 'blocked' | 'pending';

export interface User {
  id: string;
  name: string;
  email: string;
  phone?: string;
  role: UserRole;
  planId?: string;
  status: UserStatus;
  createdAt: string;
  lastLogin?: string;
}

export interface Profile {
  id: string;
  userId: string;
  name: string;
  avatarColor: string; // Tailwind color class
}

export interface Plan {
  id: string;
  name: string;
  price: number;
  features: string[];
  active: boolean;
}

export type SubscriptionStatus = 'active' | 'inactive' | 'canceled' | 'unpaid';
export type PaymentMethod = 'credit_card' | 'pix' | 'boleto';

export interface Subscription {
  id: string;
  userId: string;
  planId: string;
  status: SubscriptionStatus;
  startDate: string;
  nextBillingDate: string;
  paymentMethod: PaymentMethod;
}

export type PaymentStatus = 'paid' | 'pending' | 'overdue' | 'refunded' | 'canceled';

export interface Payment {
  id: string;
  userId: string;
  subscriptionId: string;
  value: number;
  date: string;
  status: PaymentStatus;
  paymentMethod: PaymentMethod;
}

export type ContentStatus = 'draft' | 'published' | 'archived';
export type ContentType = 'movie' | 'series' | 'tv_show' | 'news' | 'sports' | 'documentary' | 'special';

export interface Content {
  id: string;
  type: ContentType;
  title: string;
  shortDescription: string;
  fullDescription: string;
  categoryId: string;
  genre: string;
  ageRating: 'L' | '10' | '12' | '14' | '16' | '18';
  year: number;
  duration: string; // e.g. "1h 45m" or "50m"
  cast: string[];
  directors: string[];
  coverUrl: string; // gradient / color info or placeholder url
  bannerUrl: string;
  trailerUrl: string;
  videoUrl: string; // placeholder text or test mp4
  status: ContentStatus;
  isFeatured: boolean;
  isFree: boolean;
  isExclusive: boolean;
  publishDate: string;
  tags: string[];
  viewsCount: number;
  // If associated with a series/season/episode:
  seriesId?: string;
  seasonId?: string;
  episodeId?: string;
}

export interface Category {
  id: string;
  name: string;
  slug: string;
}

export interface Series {
  id: string;
  title: string;
  description: string;
  coverUrl: string;
  bannerUrl: string;
  genre: string;
  status: 'published' | 'hidden';
  viewsCount: number;
}

export interface Season {
  id: string;
  seriesId: string;
  number: number;
  title: string;
  status: 'published' | 'hidden';
}

export interface Episode {
  id: string;
  seasonId: string;
  number: number;
  title: string;
  description: string;
  duration: string;
  videoUrl: string;
  thumbnailUrl: string;
  status: 'published' | 'hidden';
  viewsCount: number;
}

export type UploadStatus = 'uploading' | 'processing' | 'ready' | 'error';

export interface Upload {
  id: string;
  fileName: string;
  fileType: string;
  size: string;
  progress: number;
  status: UploadStatus;
  categoryId: string;
  title: string;
  coverUrl: string;
  bannerUrl: string;
  videoUrl: string;
  createdAt: string;
}

export interface WatchHistory {
  id: string;
  userId: string;
  contentId: string;
  episodeId?: string;
  watchedPercent: number; // e.g., 65
  contentType: ContentType;
  title: string;
  updatedAt: string;
}

export interface Favorite {
  id: string;
  userId: string;
  contentId: string;
}

export interface Notification {
  id: string;
  userId: string | null; // null for global
  title: string;
  message: string;
  read: boolean;
  createdAt: string;
}

export interface Review {
  id: string;
  contentId: string;
  profileId: string;
  profileName: string;
  avatarColor: string;
  rating: number;
  comment: string;
  createdAt: string;
}

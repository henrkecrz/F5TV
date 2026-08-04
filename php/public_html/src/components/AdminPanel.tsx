/**
 * @license
 * SPDX-License-Identifier: Apache-2.0
 */

import React, { useState, useEffect } from 'react';
import { 
  User, Plan, Subscription, Payment, Content, Category, 
  Series, Season, Episode, Upload, UserRole, UserStatus, ContentStatus, ContentType, PaymentStatus
} from '../types';
import { db } from '../data/mockDatabase';
import { 
  BarChart, Bar, AreaChart, Area, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, Legend 
} from 'recharts';
import { 
  Users, Film, CreditCard, UploadCloud, Settings, Database, 
  Plus, Edit2, Trash2, CheckCircle, AlertTriangle, ShieldCheck, 
  DollarSign, BarChart2, ListCollapse, Play, Sparkles, FolderPlus, Download, Check, X, LogOut, Search,
  Compass, LayoutGrid, Calendar, Video, Image, FileText, ToggleLeft, Activity, Eye, ShieldAlert,
  Ticket, MessageSquare, Library, Radio, Tv
} from 'lucide-react';

import { AdminProgramacao } from './admin/AdminProgramacao';
import { AdminCanais } from './admin/AdminCanais';
import { AdminCupons } from './admin/AdminCupons';
import { AdminAvaliacoes } from './admin/AdminAvaliacoes';
import { AdminMidia } from './admin/AdminMidia';

type AdminPanelTab = 
  | 'dashboard' 
  | 'usuarios' 
  | 'assinantes' 
  | 'conteudo' 
  | 'series' 
  | 'temporadas' 
  | 'episodios' 
  | 'uploads' 
  | 'financeiro' 
  | 'planos' 
  | 'banners' 
  | 'relatorios' 
  | 'configuracoes'
  | 'programacao'
  | 'canais'
  | 'cupons'
  | 'avaliacoes'
  | 'midia';

interface AdminPanelProps {
  currentUser: User;
  onLogout: () => void;
  onNavigateToUserApp: () => void;
  activeTabOverride?: AdminPanelTab;
  onTabChange?: (tab: AdminPanelTab) => void;
}

export default function AdminPanel({ 
  currentUser, 
  onLogout, 
  onNavigateToUserApp, 
  activeTabOverride, 
  onTabChange 
}: AdminPanelProps) {
  // DB States
  const [users, setUsers] = useState<User[]>([]);
  const [contents, setContents] = useState<Content[]>([]);
  const [categories, setCategories] = useState<Category[]>([]);
  const [series, setSeries] = useState<Series[]>([]);
  const [seasons, setSeasons] = useState<Season[]>([]);
  const [episodes, setEpisodes] = useState<Episode[]>([]);
  const [subscriptions, setSubscriptions] = useState<Subscription[]>([]);
  const [payments, setPayments] = useState<Payment[]>([]);
  const [uploads, setUploads] = useState<Upload[]>([]);
  const [plans, setPlans] = useState<Plan[]>([]);

  // Navigation tabs
  const [activeTab, setActiveTabInternal] = useState<AdminPanelTab>(activeTabOverride || 'dashboard');

  useEffect(() => {
    if (activeTabOverride && activeTabOverride !== activeTab) {
      setActiveTabInternal(activeTabOverride);
    }
  }, [activeTabOverride]);

  const setActiveTab = (tab: AdminPanelTab) => {
    setActiveTabInternal(tab);
    if (onTabChange) {
      onTabChange(tab);
    }
  };

  // CRM Filters States
  const [crmSearch, setCrmSearch] = useState('');
  const [crmPlanFilter, setCrmPlanFilter] = useState('all');
  const [crmStatusFilter, setCrmStatusFilter] = useState('all');

  // Subscriber CRM Filters & Detail state
  const [subscriberSearch, setSubscriberSearch] = useState('');
  const [subscriberPlanFilter, setSubscriberPlanFilter] = useState('all');
  const [subscriberStatusFilter, setSubscriberStatusFilter] = useState('all');
  const [selectedSubscriberForDetail, setSelectedSubscriberForDetail] = useState<User | null>(null);

  // Seasons Management States
  const [selectedSeriesIdForSeasonFilter, setSelectedSeriesIdForSeasonFilter] = useState('all');
  const [editingSeason, setEditingSeason] = useState<Season | null>(null);
  const [showSeasonModal, setShowSeasonModal] = useState(false);
  const [seasonForm, setSeasonForm] = useState({ id: '', seriesId: '', number: 1, title: '', status: 'published' as 'published' | 'hidden' });

  // Episodes Management States
  const [selectedSeriesIdForEpisodeFilter, setSelectedSeriesIdForEpisodeFilter] = useState('all');
  const [selectedSeasonIdForEpisodeFilter, setSelectedSeasonIdForEpisodeFilter] = useState('all');
  const [editingEpisode, setEditingEpisode] = useState<Episode | null>(null);
  const [showEpisodeModal, setShowEpisodeModal] = useState(false);
  const [episodeForm, setEpisodeForm] = useState({ id: '', seriesId: '', seasonId: '', number: 1, title: '', duration: '', description: '', thumbnailUrl: '', videoUrl: '', status: 'published' as 'published' | 'hidden' });

  // Banners Management States
  const [banners, setBanners] = useState<{ id: string; title: string; subtitle: string; imageUrl: string; linkUrl: string; type: 'public' | 'subscriber'; active: boolean; order: number }[]>(() => {
    try {
      const saved = localStorage.getItem('f5_banners_live');
      if (saved) return JSON.parse(saved);
    } catch (_) {}
    return [
      { id: 'b1', title: 'Conexão F5: Guerra Cibernética', subtitle: 'Novos episódios imperdíveis todas as quartas', imageUrl: 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=1200', linkUrl: '#', type: 'subscriber', active: true, order: 1 },
      { id: 'b2', title: 'F5 Arena Debate Especial', subtitle: 'Assista hoje a mesa redonda sobre os playoffs', imageUrl: 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?q=80&w=1200', linkUrl: '#', type: 'public', active: true, order: 2 }
    ];
  });
  const [editingBanner, setEditingBanner] = useState<{ id: string; title: string; subtitle: string; imageUrl: string; linkUrl: string; type: 'public' | 'subscriber'; active: boolean; order: number } | null>(null);
  const [showBannerModal, setShowBannerModal] = useState(false);
  const [bannerForm, setBannerForm] = useState({ id: '', title: '', subtitle: '', imageUrl: '', linkUrl: '', type: 'subscriber' as 'public' | 'subscriber', active: true, order: 1 });

  // Platform Config States
  const [platformConfig, setPlatformConfig] = useState(() => {
    try {
      const saved = localStorage.getItem('f5_platform_config');
      if (saved) return JSON.parse(saved);
    } catch (_) {}
    return {
      appName: 'F5 TV',
      primaryColor: '#ef4444',
      warningNotice: 'Aviso: Versão de Demonstração de Conceito MVP. Todos os dados financeiros são simulados offline.',
      paymentMockSuccess: true,
      maintenanceMode: false,
      footerLinks: 'Termos de Uso, Política de Privacidade, FAQ, Fale Conosco'
    };
  });

  useEffect(() => {
    localStorage.setItem('f5_banners_live', JSON.stringify(banners));
  }, [banners]);

  useEffect(() => {
    localStorage.setItem('f5_platform_config', JSON.stringify(platformConfig));
  }, [platformConfig]);

  // Form Modals Active states
  const [editingUser, setEditingUser] = useState<User | null>(null);
  const [showUserModal, setShowUserModal] = useState(false);
  const [userForm, setUserForm] = useState<{ id?: string; name: string; email: string; phone: string; role: UserRole; status: UserStatus; planId: string }>({
    name: '', email: '', phone: '', role: 'subscriber', status: 'active', planId: 'plano-premium'
  });

  const [editingContent, setEditingContent] = useState<Content | null>(null);
  const [showContentModal, setShowContentModal] = useState(false);
  const [contentForm, setContentForm] = useState<{
    id?: string; type: ContentType; title: string; shortDescription: string; fullDescription: string;
    categoryId: string; genre: string; ageRating: 'L' | '10' | '12' | '14' | '16' | '18'; year: number;
    duration: string; cast: string; directors: string; coverUrl: string; bannerUrl: string;
    trailerUrl: string; videoUrl: string; status: ContentStatus; isFeatured: boolean; isFree: boolean; isExclusive: boolean; tags: string;
  }>({
    type: 'movie', title: '', shortDescription: '', fullDescription: '', categoryId: 'cat-series',
    genre: '', ageRating: '10', year: 2026, duration: '1h 30m', cast: '', directors: '',
    coverUrl: 'https://images.unsplash.com/photo-1541872703-74c5e44368f9?q=80&w=400',
    bannerUrl: 'https://images.unsplash.com/photo-1517048676732-d65bc937f952?q=80&w=1200',
    trailerUrl: 'https://assets.mixkit.co/videos/preview/mixkit-hands-of-a-hacker-typing-code-on-a-keyboard-34293-large.mp4',
    videoUrl: 'https://assets.mixkit.co/videos/preview/mixkit-hands-of-a-hacker-typing-code-on-a-keyboard-34293-large.mp4',
    status: 'published', isFeatured: false, isFree: false, isExclusive: true, tags: 'Destaque, Série'
  });

  // Series modals states
  const [showSeriesModal, setShowSeriesModal] = useState(false);
  const [seriesForm, setSeriesForm] = useState({ title: '', description: '', genre: '', coverUrl: '', bannerUrl: '' });
  const [showSeriesEpisodeModal, setShowSeriesEpisodeModal] = useState(false);
  const [selectedSeriesIdForEpisode, setSelectedSeriesIdForEpisode] = useState('');
  const [seriesEpisodeForm, setSeriesEpisodeForm] = useState({ title: '', number: 1, seasonId: '', description: '', duration: '45m', thumbnailUrl: '', videoUrl: '' });

  // Upload simulation states
  const [selectedUploadFile, setSelectedUploadFile] = useState<File | null>(null);
  const [uploadTitle, setUploadTitle] = useState('');
  const [uploadProgress, setUploadProgress] = useState(-1);
  const [uploadStatus, setUploadStatus] = useState<'idle' | 'uploading' | 'processing' | 'success'>('idle');

  // Load state on mount
  useEffect(() => {
    reloadAll();
  }, []);

  const reloadAll = () => {
    setUsers(db.getUsers());
    setContents(db.getContents());
    setCategories(db.getCategories());
    setSeries(db.getSeries());
    setSeasons(db.getSeasons());
    setEpisodes(db.getEpisodes());
    setSubscriptions(db.getSubscriptions());
    setPayments(db.getPayments());
    setUploads(db.getUploads());
    setPlans(db.getPlans());
  };

  // Roles verification rules
  const canAccess = (tab: typeof activeTab) => {
    if (currentUser.role === 'admin') return true;
    if (currentUser.role === 'editor' && ['dashboard', 'conteudo', 'series', 'temporadas', 'episodios', 'uploads', 'programacao', 'canais', 'avaliacoes', 'midia'].includes(tab)) return true;
    if (currentUser.role === 'finance' && ['dashboard', 'financeiro', 'planos', 'assinantes', 'relatorios', 'cupons'].includes(tab)) return true;
    return false;
  };

  // Helper values
  const totalSubscribers = users.filter(u => u.role === 'subscriber').length;
  const activeSubs = users.filter(u => u.role === 'subscriber' && u.status === 'active').length;
  const pendingSubs = users.filter(u => u.role === 'subscriber' && u.status === 'pending').length;
  const totalRevenue = payments
    .filter(p => p.status === 'paid' || p.status === 'overdue')
    .reduce((a, b) => a + Number(b.value), 0);

  const monthlyRecurrentRevenue = subscriptions
    .filter(s => s.status === 'active')
    .reduce((acc, sub) => {
      const planPrice = plans.find(p => p.id === sub.planId)?.price || 0;
      return acc + planPrice;
    }, 0);

  // Growth graph seed data
  const growthData = [
    { name: 'Janeiro', Assinantes: 80, Receita: 1200 },
    { name: 'Fevereiro', Assinantes: 120, Receita: 1950 },
    { name: 'Março', Assinantes: 185, Receita: 3100 },
    { name: 'Abril', Assinantes: 260, Receita: 4800 },
    { name: 'Maio (Ativo)', Assinantes: totalSubscribers, Receita: totalRevenue },
  ];

  // Filtered Users CRM list (Staff & Professionals only, non-subscribers)
  const filteredUsers = users.filter((u) => {
    if (u.role === 'subscriber') return false;

    const matchesSearch = crmSearch 
      ? u.name.toLowerCase().includes(crmSearch.toLowerCase()) || 
        u.email.toLowerCase().includes(crmSearch.toLowerCase()) ||
        (u.phone && u.phone.includes(crmSearch))
      : true;

    const matchesStatus = crmStatusFilter === 'all'
      ? true
      : u.status === crmStatusFilter;

    return matchesSearch && matchesStatus;
  });

  // Filtered Subscribers list (role === 'subscriber' only)
  const filteredSubscribers = users.filter((u) => {
    if (u.role !== 'subscriber') return false;

    const matchesSearch = subscriberSearch 
      ? u.name.toLowerCase().includes(subscriberSearch.toLowerCase()) || 
        u.email.toLowerCase().includes(subscriberSearch.toLowerCase()) ||
        (u.phone && u.phone.includes(subscriberSearch))
      : true;

    const matchesPlan = subscriberPlanFilter === 'all'
      ? true
      : u.planId === subscriberPlanFilter;

    const matchesStatus = subscriberStatusFilter === 'all'
      ? true
      : u.status === subscriberStatusFilter;

    return matchesSearch && matchesPlan && matchesStatus;
  });

  // Simulated export users to CSV
  const handleExportUsersCSV = () => {
    let csvContent = '\uFEFFID,Nome,Email,Telefone,Função,Plano,Status,Data de Criação\n';
    filteredUsers.forEach(u => {
      const planName = u.planId ? plans.find(p => p.id === u.planId)?.name || 'N/A' : 'N/A';
      csvContent += `"${u.id}","${u.name}","${u.email}","${u.phone || 'N/A'}","${u.role}","${planName}","${u.status}","${u.createdAt || 'N/A'}"\n`;
    });

    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.setAttribute('href', url);
    link.setAttribute('download', 'relatorio_usuarios_crm_f5_tv.csv');
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  };

  // Subscribers CRM Operations
  const handleToggleBlockSubscriber = (userId: string) => {
    const updated = users.map(u => {
      if (u.id === userId) {
        const newStatus: UserStatus = u.status === 'blocked' ? 'active' : 'blocked';
        return { ...u, status: newStatus };
      }
      return u;
    });
    setUsers(updated);
    db.setUsers(updated);
    if (selectedSubscriberForDetail && selectedSubscriberForDetail.id === userId) {
      const match = updated.find(u => u.id === userId);
      if (match) setSelectedSubscriberForDetail(match);
    }
  };

  const handleChangeSubscriberPlan = (userId: string, newPlanId: string) => {
    const updated = users.map(u => {
      if (u.id === userId) {
        return { ...u, planId: newPlanId };
      }
      return u;
    });
    setUsers(updated);
    db.setUsers(updated);

    const currentSubs = db.getSubscriptions();
    const updatedSubs = currentSubs.map(s => {
      if (s.userId === userId) {
        return { ...s, planId: newPlanId };
      }
      return s;
    });
    setSubscriptions(updatedSubs);
    db.setSubscriptions(updatedSubs);

    if (selectedSubscriberForDetail && selectedSubscriberForDetail.id === userId) {
      const match = updated.find(u => u.id === userId);
      if (match) setSelectedSubscriberForDetail(match);
    }
  };

  const handleExportSubscribersCSV = () => {
    let csvContent = '\uFEFFID,Nome,Email,Telefone,Plano,Status,Proxima Cobrança,Data de Criação\n';
    filteredSubscribers.forEach(u => {
      const planName = u.planId ? plans.find(p => p.id === u.planId)?.name || 'N/A' : 'N/A';
      const sub = subscriptions.find(s => s.userId === u.id);
      const nextBilling = sub ? sub.nextBillingDate : 'N/A';
      csvContent += `"${u.id}","${u.name}","${u.email}","${u.phone || 'N/A'}","${planName}","${u.status}","${nextBilling}","${u.createdAt || 'N/A'}"\n`;
    });

    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.setAttribute('href', url);
    link.setAttribute('download', 'relatorio_assinantes_crm_f5_tv.csv');
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  };

  // Users CRM Operations
  const handleOpenUserModal = (usr: User | null = null) => {
    if (usr) {
      setEditingUser(usr);
      setUserForm({
        name: usr.name,
        email: usr.email,
        phone: usr.phone || '',
        role: usr.role,
        status: usr.status,
        planId: usr.planId || 'plano-premium'
      });
    } else {
      setEditingUser(null);
      setUserForm({
        name: '', email: '', phone: '', role: 'subscriber', status: 'active', planId: 'plano-premium'
      });
    }
    setShowUserModal(true);
  };

  const handleUserFormSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    const allUsers = db.getUsers();
    
    if (editingUser) {
      // update
      const updated = allUsers.map(u => u.id === editingUser.id ? { ...u, ...userForm } : u);
      db.setUsers(updated);
    } else {
      // create
      const newUser: User = {
        id: 'user-' + Math.random().toString(36).substring(2, 9),
        name: userForm.name,
        email: userForm.email,
        phone: userForm.phone,
        role: userForm.role,
        status: userForm.status,
        planId: userForm.planId,
        createdAt: new Date().toISOString()
      };
      db.setUsers([...allUsers, newUser]);
    }
    setShowUserModal(false);
    reloadAll();
  };

  const handleDeleteUser = (userId: string) => {
    if (confirm('Deseja realmente excluir este cadastro da F5 TV?')) {
      const remainingUsers = db.getUsers().filter(u => u.id !== userId);
      db.setUsers(remainingUsers);
      reloadAll();
    }
  };

  const handleResetPasswordSimulated = (email: string) => {
    alert(`Link de reset de credenciais simulado disparado para: ${email}`);
  };

  // Seasons Operations
  const handleOpenSeasonModal = (season: Season | null = null) => {
    if (season) {
      setEditingSeason(season);
      setSeasonForm({
        id: season.id,
        seriesId: season.seriesId,
        number: season.number,
        title: season.title,
        status: season.status || 'published'
      });
    } else {
      setEditingSeason(null);
      setSeasonForm({
        id: '',
        seriesId: series[0]?.id || '',
        number: seasons.length + 1,
        title: `Temporada ${seasons.length + 1}`,
        status: 'published'
      });
    }
    setShowSeasonModal(true);
  };

  const handleSaveSeason = (e: React.FormEvent) => {
    e.preventDefault();
    if (!seasonForm.seriesId || !seasonForm.title) return;

    const currentSeasons = db.getSeasons();
    if (editingSeason) {
      const updated = currentSeasons.map(s => {
        if (s.id === editingSeason.id) {
          return {
            ...s,
            seriesId: seasonForm.seriesId,
            number: Number(seasonForm.number),
            title: seasonForm.title,
            status: seasonForm.status as 'published' | 'hidden'
          };
        }
        return s;
      });
      setSeasons(updated);
      db.setSeasons(updated);
    } else {
      const newSeason: Season = {
        id: `season-${Date.now()}`,
        seriesId: seasonForm.seriesId,
        number: Number(seasonForm.number),
        title: seasonForm.title,
        status: seasonForm.status as 'published' | 'hidden'
      };
      const updated = [...currentSeasons, newSeason];
      setSeasons(updated);
      db.setSeasons(updated);
    }
    setShowSeasonModal(false);
    setEditingSeason(null);
  };

  const handleDeleteSeason = (id: string) => {
    if (confirm('Tem certeza que deseja remover esta temporada? Todos os episódios associados permanecerão órfãos.')) {
      const currentSeasons = db.getSeasons();
      const updated = currentSeasons.filter(s => s.id !== id);
      setSeasons(updated);
      db.setSeasons(updated);
    }
  };

  // Episodes Operations
  const handleOpenEpisodeModal = (episode: Episode | null = null) => {
    if (episode) {
      setEditingEpisode(episode);
      setEpisodeForm({
        id: episode.id,
        seriesId: seasons.find(seas => seas.id === episode.seasonId)?.seriesId || (series[0]?.id || ''),
        seasonId: episode.seasonId,
        number: episode.number,
        title: episode.title,
        duration: episode.duration || '45m',
        description: episode.description || '',
        thumbnailUrl: episode.thumbnailUrl || '',
        videoUrl: episode.videoUrl || '',
        status: episode.status || 'published'
      });
    } else {
      setEditingEpisode(null);
      setEpisodeForm({
        id: '',
        seriesId: series[0]?.id || '',
        seasonId: seasons[0]?.id || '',
        number: episodes.length + 1,
        title: `Episódio ${episodes.length + 1}`,
        duration: '45m',
        description: '',
        thumbnailUrl: 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?q=80&w=400',
        videoUrl: 'https://assets.mixkit.co/videos/preview/mixkit-software-developer-working-on-his-computer-34289-large.mp4',
        status: 'published'
      });
    }
    setShowEpisodeModal(true);
  };

  const handleSaveEpisode = (e: React.FormEvent) => {
    e.preventDefault();
    if (!episodeForm.seriesId || !episodeForm.seasonId || !episodeForm.title) return;

    const currentEpisodes = db.getEpisodes();
    if (editingEpisode) {
      const updated = currentEpisodes.map(ep => {
        if (ep.id === editingEpisode.id) {
          return {
            ...ep,
            seasonId: episodeForm.seasonId,
            number: Number(episodeForm.number),
            title: episodeForm.title,
            duration: episodeForm.duration || '45m',
            description: episodeForm.description,
            thumbnailUrl: episodeForm.thumbnailUrl || 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?q=80&w=400',
            videoUrl: episodeForm.videoUrl || 'https://assets.mixkit.co/videos/preview/mixkit-software-developer-working-on-his-computer-34289-large.mp4',
            status: episodeForm.status as 'published' | 'hidden'
          };
        }
        return ep;
      });
      setEpisodes(updated);
      db.setEpisodes(updated);
    } else {
      const newEp: Episode = {
        id: `episode-${Date.now()}`,
        seasonId: episodeForm.seasonId,
        number: Number(episodeForm.number),
        title: episodeForm.title,
        duration: episodeForm.duration || '45m',
        description: episodeForm.description,
        thumbnailUrl: episodeForm.thumbnailUrl || 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?q=80&w=400',
        videoUrl: episodeForm.videoUrl || 'https://assets.mixkit.co/videos/preview/mixkit-software-developer-working-on-his-computer-34289-large.mp4',
        status: episodeForm.status as 'published' | 'hidden',
        viewsCount: 0
      };
      const updated = [...currentEpisodes, newEp];
      setEpisodes(updated);
      db.setEpisodes(updated);
    }
    setShowEpisodeModal(false);
    setEditingEpisode(null);
  };

  const handleDeleteEpisode = (id: string) => {
    if (confirm('Tem certeza que deseja remover este episódio?')) {
      const currentEpisodes = db.getEpisodes();
      const updated = currentEpisodes.filter(e => e.id !== id);
      setEpisodes(updated);
      db.setEpisodes(updated);
    }
  };

  // Banners Operations
  const handleOpenBannerModal = (banner: typeof banners[0] | null = null) => {
    if (banner) {
      setEditingBanner(banner);
      setBannerForm({
        id: banner.id,
        title: banner.title,
        subtitle: banner.subtitle,
        imageUrl: banner.imageUrl,
        linkUrl: banner.linkUrl,
        type: banner.type,
        active: banner.active,
        order: banner.order
      });
    } else {
      setEditingBanner(null);
      setBannerForm({
        id: '',
        title: '',
        subtitle: '',
        imageUrl: 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=1200',
        linkUrl: '#',
        type: 'subscriber',
        active: true,
        order: banners.length + 1
      });
    }
    setShowBannerModal(true);
  };

  const handleSaveBanner = (e: React.FormEvent) => {
    e.preventDefault();
    if (!bannerForm.title || !bannerForm.imageUrl) return;

    if (editingBanner) {
      const updated = banners.map(b => {
        if (b.id === editingBanner.id) {
          return { ...b, ...bannerForm };
        }
        return b;
      });
      setBanners(updated);
    } else {
      const newBanner = {
        ...bannerForm,
        id: `banner-${Date.now()}`
      };
      setBanners([...banners, newBanner]);
    }
    setShowBannerModal(false);
    setEditingBanner(null);
  };

  const handleDeleteBanner = (id: string) => {
    if (confirm('Deseja realmente remover este banner?')) {
      const updated = banners.filter(b => b.id !== id);
      setBanners(updated);
    }
  };

  const handleToggleBannerActive = (id: string) => {
    const updated = banners.map(b => b.id === id ? { ...b, active: !b.active } : b);
    setBanners(updated);
  };

  // Content Operations
  const handleOpenContentModal = (cnt: Content | null = null) => {
    if (cnt) {
      setEditingContent(cnt);
      setContentForm({
        type: cnt.type,
        title: cnt.title,
        shortDescription: cnt.shortDescription,
        fullDescription: cnt.fullDescription,
        categoryId: cnt.categoryId,
        genre: cnt.genre,
        ageRating: cnt.ageRating,
        year: cnt.year,
        duration: cnt.duration,
        cast: cnt.cast.join(', '),
        directors: cnt.directors.join(', '),
        coverUrl: cnt.coverUrl,
        bannerUrl: cnt.bannerUrl,
        trailerUrl: cnt.trailerUrl,
        videoUrl: cnt.videoUrl,
        status: cnt.status,
        isFeatured: cnt.isFeatured,
        isFree: cnt.isFree,
        isExclusive: cnt.isExclusive,
        tags: cnt.tags.join(', ')
      });
    } else {
      setEditingContent(null);
      setContentForm({
        type: 'movie', title: '', shortDescription: '', fullDescription: '', categoryId: 'cat-series',
        genre: '', ageRating: 'L', year: 2026, duration: '1h 30m', cast: '', directors: '',
        coverUrl: 'https://images.unsplash.com/photo-1541872703-74c5e44368f9?q=80&w=400',
        bannerUrl: 'https://images.unsplash.com/photo-1517048676732-d65bc937f952?q=80&w=1200',
        trailerUrl: 'https://assets.mixkit.co/videos/preview/mixkit-hands-of-a-hacker-typing-code-on-a-keyboard-34293-large.mp4',
        videoUrl: 'https://assets.mixkit.co/videos/preview/mixkit-hands-of-a-hacker-typing-code-on-a-keyboard-34293-large.mp4',
        status: 'published', isFeatured: false, isFree: false, isExclusive: true, tags: 'Destaque, Novo'
      });
    }
    setShowContentModal(true);
  };

  const handleContentFormSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    const allContents = db.getContents();

    const formattedContent: Partial<Content> = {
      type: contentForm.type,
      title: contentForm.title,
      shortDescription: contentForm.shortDescription,
      fullDescription: contentForm.fullDescription,
      categoryId: contentForm.categoryId,
      genre: contentForm.genre,
      ageRating: contentForm.ageRating,
      year: Number(contentForm.year),
      duration: contentForm.duration,
      cast: contentForm.cast.split(',').map(s => s.trim()),
      directors: contentForm.directors.split(',').map(s => s.trim()),
      coverUrl: contentForm.coverUrl,
      bannerUrl: contentForm.bannerUrl,
      trailerUrl: contentForm.trailerUrl,
      videoUrl: contentForm.videoUrl,
      status: contentForm.status,
      isFeatured: contentForm.isFeatured,
      isFree: contentForm.isFree,
      isExclusive: contentForm.isExclusive,
      publishDate: new Date().toISOString().split('T')[0],
      tags: contentForm.tags.split(',').map(s => s.trim()),
      viewsCount: editingContent ? editingContent.viewsCount : Math.floor(Math.random() * 500)
    };

    if (editingContent) {
      const updated = allContents.map(c => c.id === editingContent.id ? { ...c, ...formattedContent } as Content : c);
      db.setContents(updated);
    } else {
      const newContent: Content = {
        id: 'content-' + Math.random().toString(36).substring(2, 9),
        ...formattedContent
      } as Content;
      db.setContents([newContent, ...allContents]);
    }
    setShowContentModal(false);
    reloadAll();
  };

  const handleDeleteContent = (id: string) => {
    if (confirm('Deseja realmente apagar este conteúdo do catálogo F5 TV?')) {
      const remaining = db.getContents().filter(c => c.id !== id);
      db.setContents(remaining);
      reloadAll();
    }
  };

  // Series additions
  const handleCreateSeriesSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    const newSer: Series = {
      id: 'series-' + Math.random().toString(36).substring(2, 9),
      title: seriesForm.title,
      description: seriesForm.description,
      genre: seriesForm.genre,
      coverUrl: seriesForm.coverUrl || 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?q=80&w=400',
      bannerUrl: seriesForm.bannerUrl || 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=1200',
      status: 'published',
      viewsCount: 0
    };
    db.setSeries([...series, newSer]);
    setShowSeriesModal(false);
    setSeriesForm({ title: '', description: '', genre: '', coverUrl: '', bannerUrl: '' });
    reloadAll();
  };

  const handleCreateEpisodeSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    const selectedSeas = seasons.find(s => s.seriesId === selectedSeriesIdForEpisode);
    let targetSeasonId = selectedSeas?.id;

    if (!targetSeasonId) {
      // Create season 1 on the fly if series doesn't have one!
      const newSeasonId = 'season-' + Math.random().toString(36).substring(2, 9);
      const newSeas: Season = {
        id: newSeasonId,
        seriesId: selectedSeriesIdForEpisode,
        number: 1,
        title: 'Temporada 1',
        status: 'published'
      };
      db.setSeasons([...seasons, newSeas]);
      targetSeasonId = newSeasonId;
    }

    const newEp: Episode = {
      id: 'ep-' + Math.random().toString(36).substring(2, 9),
      seasonId: targetSeasonId,
      number: Number(seriesEpisodeForm.number),
      title: seriesEpisodeForm.title,
      description: seriesEpisodeForm.description,
      duration: seriesEpisodeForm.duration,
      videoUrl: seriesEpisodeForm.videoUrl || 'https://assets.mixkit.co/videos/preview/mixkit-software-developer-working-on-his-computer-34289-large.mp4',
      thumbnailUrl: seriesEpisodeForm.thumbnailUrl || 'https://images.unsplash.com/photo-1563986768609-322da13575f3?q=80&w=450',
      status: 'published',
      viewsCount: 0
    };

    db.setEpisodes([...episodes, newEp]);
    setShowSeriesEpisodeModal(false);
    setSeriesEpisodeForm({ title: '', number: 1, seasonId: '', description: '', duration: '45m', thumbnailUrl: '', videoUrl: '' });
    reloadAll();
  };

  // Upload Simulation engine
  const handleUploadTrigger = (e: React.FormEvent) => {
    e.preventDefault();
    if (!uploadTitle.trim()) return;

    setUploadStatus('uploading');
    setUploadProgress(0);

    const intv = setInterval(() => {
      setUploadProgress(prev => {
        if (prev >= 100) {
          clearInterval(intv);
          setUploadStatus('processing');
          
          setTimeout(() => {
            setUploadStatus('success');
            // Write to mock DB
            const newUpload: Upload = {
              id: 'upl-' + Math.random().toString(36).substring(2, 9),
              fileName: 'f5_tv_' + uploadTitle.toLowerCase().replace(/\s/g, '_') + '.mp4',
              fileType: 'video/mp4',
              size: '425 MB',
              progress: 100,
              status: 'ready',
              categoryId: 'cat-series',
              title: uploadTitle,
              coverUrl: 'https://images.unsplash.com/photo-1541872703-74c5e44368f9?q=80&w=400',
              bannerUrl: 'https://images.unsplash.com/photo-1517048676732-d65bc937f952?q=80&w=1200',
              videoUrl: 'https://assets.mixkit.co/videos/preview/mixkit-hands-of-a-hacker-typing-code-on-a-keyboard-34293-large.mp4',
              createdAt: new Date().toISOString()
            };
            db.setUploads([newUpload, ...db.getUploads()]);
            reloadAll();
            setUploadTitle('');
          }, 1500);

          return 100;
        }
        return prev + 15;
      });
    }, 200);
  };

  // Plan toggler
  const togglePlanActive = (planId: string) => {
    const updated = plans.map(p => p.id === planId ? { ...p, active: !p.active } : p);
    db.setPlans(updated);
    reloadAll();
  };

  // Simulated export to CSV matching finance records
  const handleExportCSV = () => {
    let csvContent = 'ID,Usuario,Valor,Data,Status,Meio Pagamento\n';
    payments.forEach(p => {
      const userMail = users.find(u => u.id === p.userId)?.email || 'Desconhecido';
      csvContent += `${p.id},${userMail},R$ ${p.value.toFixed(2)},${p.date || 'Hoje'},${p.status},${p.paymentMethod}\n`;
    });

    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.setAttribute('href', url);
    link.setAttribute('download', 'relatorio_financeiro_f5_tv.csv');
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  };

  return (
    <div id="admin-panel-root" className="min-h-screen bg-zinc-950 text-white select-none font-sans flex text-xs md:text-sm">
      
      {/* 1. Left Sidebar Navigation */}
      <aside className="w-64 bg-zinc-950 border-r border-zinc-900 flex flex-col justify-between shrink-0 p-6">
        <div className="flex flex-col gap-8">
          <div className="flex items-center gap-1.5 cursor-pointer" onClick={onNavigateToUserApp}>
            <span className="text-2xl font-black tracking-tighter uppercase leading-none">
              F5 <span className="text-[#ef4444]">TV</span>
            </span>
            <span className="bg-[#ef4444]/15 text-[#ef4444] text-[9px] px-1.5 py-0.5 rounded font-mono font-bold uppercase shrink-0 leading-none">
              ADMIN
            </span>
          </div>

          <div className="flex items-center gap-2.5 p-3 bg-zinc-900 border border-zinc-800 rounded-xl relative">
            <div className="w-8 h-8 rounded-full bg-red-600 flex items-center justify-center font-bold text-sm text-white shrink-0">
              {currentUser.name.charAt(0)}
            </div>
            <div className="flex flex-col">
              <span className="font-bold text-xs truncate leading-snug">{currentUser.name}</span>
              <span className="text-[10px] text-zinc-500 font-mono uppercase tracking-wider">{currentUser.role}</span>
            </div>
          </div>

          {/* Nav Items */}
          <nav className="flex flex-col gap-1 text-xs font-semibold max-h-[60vh] overflow-y-auto pr-1">
            <button 
              id="tab-dashboard"
              onClick={() => setActiveTab('dashboard')}
              className={`flex items-center gap-3 px-3 py-2 rounded-lg text-left transition cursor-pointer ${
                activeTab === 'dashboard' ? 'bg-[#ef4444] text-white shadow-md shadow-red-900/10' : 'text-zinc-400 hover:text-white hover:bg-zinc-900'
              }`}
            >
              <BarChart2 className="w-4 h-4 shrink-0" />
              <span>Dashboard Geral</span>
            </button>

            <button 
              id="tab-usuarios"
              onClick={() => setActiveTab('usuarios')}
              className={`flex items-center justify-between px-3 py-2 rounded-lg text-left transition cursor-pointer ${
                activeTab === 'usuarios' ? 'bg-[#ef4444] text-white shadow' : 'text-zinc-400 hover:text-white hover:bg-zinc-900'
              }`}
            >
              <div className="flex items-center gap-3">
                <ShieldCheck className="w-4 h-4 shrink-0" />
                <span>Profissionais & Staff</span>
              </div>
              {!canAccess('usuarios') && <span className="text-[9px] font-mono text-zinc-600 uppercase">Bloq</span>}
            </button>

            <button 
              id="tab-assinantes"
              onClick={() => setActiveTab('assinantes')}
              className={`flex items-center justify-between px-3 py-2 rounded-lg text-left transition cursor-pointer relative ${
                activeTab === 'assinantes' ? 'bg-[#ef4444] text-white shadow' : 'text-zinc-400 hover:text-white hover:bg-zinc-900'
              }`}
            >
              <div className="flex items-center gap-3">
                <Users className="w-4 h-4 shrink-0" />
                <span>Assinantes & CRM</span>
              </div>
              {!canAccess('assinantes') && <span className="text-[9px] font-mono text-zinc-600 uppercase">Bloq</span>}
            </button>

            <button 
              id="tab-conteudo"
              onClick={() => setActiveTab('conteudo')}
              className={`flex items-center justify-between px-3 py-2 rounded-lg text-left transition cursor-pointer ${
                activeTab === 'conteudo' ? 'bg-[#ef4444] text-white shadow' : 'text-zinc-400 hover:text-white hover:bg-zinc-900'
              }`}
            >
              <div className="flex items-center gap-3">
                <Film className="w-4 h-4 shrink-0" />
                <span>Vídeos e Catálogo</span>
              </div>
              {!canAccess('conteudo') && <span className="text-[9px] font-mono text-zinc-600 uppercase">Bloq</span>}
            </button>

            <button 
              id="tab-series"
              onClick={() => setActiveTab('series')}
              className={`flex items-center justify-between px-3 py-2 rounded-lg text-left transition cursor-pointer ${
                activeTab === 'series' ? 'bg-[#ef4444] text-white shadow' : 'text-zinc-400 hover:text-white hover:bg-zinc-900'
              }`}
            >
              <div className="flex items-center gap-3">
                <ListCollapse className="w-4 h-4 shrink-0" />
                <span>Séries Governança</span>
              </div>
              {!canAccess('series') && <span className="text-[9px] font-mono text-zinc-600 uppercase">Bloq</span>}
            </button>

            <button 
              id="tab-temporadas"
              onClick={() => setActiveTab('temporadas')}
              className={`flex items-center justify-between px-3 py-2 rounded-lg text-left transition cursor-pointer ${
                activeTab === 'temporadas' ? 'bg-[#ef4444] text-white shadow' : 'text-zinc-400 hover:text-white hover:bg-zinc-900'
              }`}
            >
              <div className="flex items-center gap-3">
                <Calendar className="w-4 h-4 shrink-0" />
                <span>Temporadas</span>
              </div>
              {!canAccess('temporadas') && <span className="text-[9px] font-mono text-zinc-600 uppercase">Bloq</span>}
            </button>

            <button 
              id="tab-episodios"
              onClick={() => setActiveTab('episodios')}
              className={`flex items-center justify-between px-3 py-2 rounded-lg text-left transition cursor-pointer ${
                activeTab === 'episodios' ? 'bg-[#ef4444] text-white shadow' : 'text-zinc-400 hover:text-white hover:bg-zinc-900'
              }`}
            >
              <div className="flex items-center gap-3">
                <Video className="w-4 h-4 shrink-0" />
                <span>Episódios</span>
              </div>
              {!canAccess('episodios') && <span className="text-[9px] font-mono text-zinc-600 uppercase">Bloq</span>}
            </button>

            <button 
              id="tab-uploads"
              onClick={() => setActiveTab('uploads')}
              className={`flex items-center justify-between px-3 py-2 rounded-lg text-left transition cursor-pointer ${
                activeTab === 'uploads' ? 'bg-[#ef4444] text-white shadow' : 'text-zinc-400 hover:text-white hover:bg-zinc-900'
              }`}
            >
              <div className="flex items-center gap-3">
                <UploadCloud className="w-4 h-4 shrink-0" />
                <span>Upload de Mídia</span>
              </div>
              {!canAccess('uploads') && <span className="text-[9px] font-mono text-zinc-600 uppercase">Bloq</span>}
            </button>

            <button 
              id="tab-programacao"
              onClick={() => setActiveTab('programacao')}
              className={`flex items-center justify-between px-3 py-2 rounded-lg text-left transition cursor-pointer ${
                activeTab === 'programacao' ? 'bg-[#ef4444] text-white shadow' : 'text-zinc-400 hover:text-white hover:bg-zinc-900'
              }`}
            >
              <div className="flex items-center gap-3">
                <Calendar className="w-4 h-4 shrink-0" />
                <span>Grade Ao Vivo</span>
              </div>
              {!canAccess('programacao') && <span className="text-[9px] font-mono text-zinc-600 uppercase">Bloq</span>}
            </button>

            <button 
              id="tab-canais"
              onClick={() => setActiveTab('canais')}
              className={`flex items-center justify-between px-3 py-2 rounded-lg text-left transition cursor-pointer ${
                activeTab === 'canais' ? 'bg-[#ef4444] text-white shadow' : 'text-zinc-400 hover:text-white hover:bg-zinc-900'
              }`}
            >
              <div className="flex items-center gap-3">
                <Radio className="w-4 h-4 shrink-0" />
                <span>Canais ao Vivo</span>
              </div>
              {!canAccess('canais') && <span className="text-[9px] font-mono text-zinc-600 uppercase">Bloq</span>}
            </button>

            <button 
              id="tab-midia"
              onClick={() => setActiveTab('midia')}
              className={`flex items-center justify-between px-3 py-2 rounded-lg text-left transition cursor-pointer ${
                activeTab === 'midia' ? 'bg-[#ef4444] text-white shadow' : 'text-zinc-400 hover:text-white hover:bg-zinc-900'
              }`}
            >
              <div className="flex items-center gap-3">
                <Library className="w-4 h-4 shrink-0" />
                <span>Biblioteca de Mídias</span>
              </div>
              {!canAccess('midia') && <span className="text-[9px] font-mono text-zinc-600 uppercase">Bloq</span>}
            </button>

            <button 
              id="tab-financeiro"
              onClick={() => setActiveTab('financeiro')}
              className={`flex items-center justify-between px-3 py-2 rounded-lg text-left transition cursor-pointer ${
                activeTab === 'financeiro' ? 'bg-[#ef4444] text-white shadow' : 'text-zinc-400 hover:text-white hover:bg-zinc-900'
              }`}
            >
              <div className="flex items-center gap-3">
                <CreditCard className="w-4 h-4 shrink-0" />
                <span>Contabilidade & Caixa</span>
              </div>
              {!canAccess('financeiro') && <span className="text-[9px] font-mono text-zinc-600 uppercase">Bloq</span>}
            </button>

            <button 
              id="tab-cupons"
              onClick={() => setActiveTab('cupons')}
              className={`flex items-center justify-between px-3 py-2 rounded-lg text-left transition cursor-pointer ${
                activeTab === 'cupons' ? 'bg-[#ef4444] text-white shadow' : 'text-zinc-400 hover:text-white hover:bg-zinc-900'
              }`}
            >
              <div className="flex items-center gap-3">
                <Ticket className="w-4 h-4 shrink-0" />
                <span>Cupons Oferta</span>
              </div>
              {!canAccess('cupons') && <span className="text-[9px] font-mono text-zinc-600 uppercase">Bloq</span>}
            </button>

            <button 
              id="tab-avaliacoes"
              onClick={() => setActiveTab('avaliacoes')}
              className={`flex items-center justify-between px-3 py-2 rounded-lg text-left transition cursor-pointer ${
                activeTab === 'avaliacoes' ? 'bg-[#ef4444] text-white shadow' : 'text-zinc-400 hover:text-white hover:bg-zinc-900'
              }`}
            >
              <div className="flex items-center gap-3">
                <MessageSquare className="w-4 h-4 shrink-0" />
                <span>Moderar Avaliações</span>
              </div>
              {!canAccess('avaliacoes') && <span className="text-[9px] font-mono text-zinc-600 uppercase">Bloq</span>}
            </button>

            <button 
              id="tab-planos"
              onClick={() => setActiveTab('planos')}
              className={`flex items-center justify-between px-3 py-2 rounded-lg text-left transition cursor-pointer ${
                activeTab === 'planos' ? 'bg-[#ef4444] text-white shadow' : 'text-zinc-400 hover:text-white hover:bg-zinc-900'
              }`}
            >
              <div className="flex items-center gap-3">
                <Settings className="w-4 h-4 shrink-0" />
                <span>Planos</span>
              </div>
              {!canAccess('planos') && <span className="text-[9px] font-mono text-zinc-600 uppercase">Bloq</span>}
            </button>

            <button 
              id="tab-banners"
              onClick={() => setActiveTab('banners')}
              className={`flex items-center justify-between px-3 py-2 rounded-lg text-left transition cursor-pointer ${
                activeTab === 'banners' ? 'bg-[#ef4444] text-white shadow' : 'text-zinc-400 hover:text-white hover:bg-zinc-900'
              }`}
            >
              <div className="flex items-center gap-3">
                <Image className="w-4 h-4 shrink-0" />
                <span>Banners Slider</span>
              </div>
              {!canAccess('banners') && <span className="text-[9px] font-mono text-zinc-600 uppercase">Bloq</span>}
            </button>

            <button 
              id="tab-relatorios"
              onClick={() => setActiveTab('relatorios')}
              className={`flex items-center justify-between px-3 py-2 rounded-lg text-left transition cursor-pointer ${
                activeTab === 'relatorios' ? 'bg-[#ef4444] text-white shadow' : 'text-zinc-400 hover:text-white hover:bg-zinc-900'
              }`}
            >
              <div className="flex items-center gap-3">
                <Activity className="w-4 h-4 shrink-0" />
                <span>Gráficos & Relatórios</span>
              </div>
              {!canAccess('relatorios') && <span className="text-[9px] font-mono text-zinc-600 uppercase">Bloq</span>}
            </button>

            <button 
              id="tab-configuracoes"
              onClick={() => setActiveTab('configuracoes')}
              className={`flex items-center justify-between px-3 py-2 rounded-lg text-left transition cursor-pointer ${
                activeTab === 'configuracoes' ? 'bg-[#ef4444] text-white shadow' : 'text-zinc-400 hover:text-white hover:bg-zinc-900'
              }`}
            >
              <div className="flex items-center gap-3">
                <Database className="w-4 h-4 shrink-0" />
                <span>Configurações</span>
              </div>
              {!canAccess('configuracoes') && <span className="text-[9px] font-mono text-zinc-600 uppercase">Bloq</span>}
            </button>
          </nav>
        </div>

        {/* Bottom profile actions */}
        <div className="flex flex-col gap-2 border-t border-zinc-900 pt-6">
          <button 
            id="admin-to-streaming-mode"
            onClick={onNavigateToUserApp}
            className="w-full text-center bg-zinc-900 hover:bg-zinc-850 p-2 border border-zinc-800 rounded font-bold text-xs hover:text-white transition cursor-pointer flex items-center justify-center gap-1.5"
          >
            <Play className="w-3.5 h-3.5 text-[#ef4444]" />
            <span>Ver Player Normal</span>
          </button>
          <button 
            id="admin-logout-btn"
            onClick={onLogout}
            className="w-full text-center hover:bg-rose-950/20 p-2 border border-transparent hover:border-red-955 rounded text-zinc-500 hover:text-[#ef4444] transition cursor-pointer flex items-center justify-center gap-1.5"
          >
            <LogOut className="w-3.5 h-3.5" />
            <span>Sair do Painel</span>
          </button>
        </div>
      </aside>

      {/* 2. Main content container */}
      <main className="flex-1 overflow-y-auto p-10 bg-[#09090b]">
        
        {/* ACCESS CHECK RULES PROTECTION LAYOUT */}
        {!canAccess(activeTab) ? (
          <div className="min-h-[50vh] flex flex-col justify-center items-center text-center max-w-lg mx-auto gap-4">
            <div className="p-4 bg-red-950/30 border border-red-900 text-[#ef4444] rounded-full">
              <AlertTriangle className="w-10 h-10" />
            </div>
            <h1 className="text-2xl font-black">Acesso Bloqueado para o seu Perfil</h1>
            <p className="text-zinc-450 leading-relaxed font-semibold">
              Desculpe, <strong className="text-white">{currentUser.name}</strong>. A aba <strong className="text-white uppercase">"{activeTab}"</strong> é restrita de acordo com seu cargo corporativo de <strong className="text-rose-500 uppercase">{currentUser.role}</strong>. Solicite ao administrador geral caso necessite de acessos estendidos.
            </p>
          </div>
        ) : (
          <>
            
            {/* TAB CONTENT: 1. DASHBOARD */}
            {activeTab === 'dashboard' && (
              <section className="flex flex-col gap-8 animate-fade-in">
                <div className="flex justify-between items-center border-b border-zinc-900 pb-5">
                  <div>
                    <span className="text-[10px] font-mono font-bold tracking-widest text-zinc-500 uppercase">CENTRAL DE MONITORAMENTO</span>
                    <h1 className="text-3xl font-black tracking-tight mt-1">Bem-vindo ao Painel F5 TV</h1>
                  </div>
                  <span className="text-xs font-mono bg-zinc-900 text-zinc-400 border border-zinc-800 px-3 py-1.5 rounded-full uppercase">
                    Status: Operação Normal
                  </span>
                </div>

                {/* Cards metrics */}
                <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
                  <div className="bg-zinc-950 border border-zinc-900 hover:border-zinc-800 rounded-xl p-5 flex items-center justify-between">
                    <div>
                      <span className="text-xs font-mono font-bold text-zinc-500 uppercase">TOTAL ASSINANTES</span>
                      <h3 className="text-2xl font-black text-white mt-1">{totalSubscribers}</h3>
                      <p className="text-[10px] text-emerald-500 font-mono font-bold mt-1">✓ {activeSubs} ativos hoje</p>
                    </div>
                    <Users className="w-8 h-8 text-[#ef4444]" />
                  </div>

                  <div className="bg-zinc-950 border border-zinc-900 hover:border-zinc-800 rounded-xl p-5 flex items-center justify-between">
                    <div>
                      <span className="text-xs font-mono font-bold text-zinc-500 uppercase">RECEITA HISTÓRICA</span>
                      <h3 className="text-2xl font-black text-white mt-1">R$ {totalRevenue.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}</h3>
                      <p className="text-[10px] text-[#ef4444] font-mono font-bold mt-1">• Simulação incremental</p>
                    </div>
                    <DollarSign className="w-8 h-8 text-emerald-500" />
                  </div>

                  <div className="bg-zinc-950 border border-zinc-900 hover:border-zinc-800 rounded-xl p-5 flex items-center justify-between">
                    <div>
                      <span className="text-xs font-mono font-bold text-zinc-500 uppercase">RECORRÊNCIA MENSAL</span>
                      <h3 className="text-2xl font-black text-white mt-1">R$ {monthlyRecurrentRevenue.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}</h3>
                      <p className="text-[10px] text-zinc-550 font-mono mt-1">MRR estimativo tático</p>
                    </div>
                    <BarChart2 className="w-8 h-8 text-indigo-505 text-indigo-500" />
                  </div>

                  <div className="bg-zinc-950 border border-zinc-900 hover:border-zinc-800 rounded-xl p-5 flex items-center justify-between">
                    <div>
                      <span className="text-xs font-mono font-bold text-zinc-500 uppercase">CADASTRADOS ATIVOS</span>
                      <h3 className="text-2xl font-black text-white mt-1">{contents.length}</h3>
                      <p className="text-[10px] text-zinc-650 font-mono mt-1">{series.length} séries / {episodes.length} episódios</p>
                    </div>
                    <Film className="w-8 h-8 text-blue-500" />
                  </div>
                </div>

                {/* Growth Charts */}
                <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                  <div className="bg-zinc-950 border border-zinc-900 rounded-xl p-6 flex flex-col gap-4">
                    <h4 className="font-bold text-sm text-zinc-300">Crescimento de Assinantes na Base</h4>
                    <div className="h-64">
                      <ResponsiveContainer width="100%" height="100%">
                        <AreaChart data={growthData}>
                          <defs>
                            <linearGradient id="colorAssinantes" x1="0" y1="0" x2="0" y2="1">
                              <stop offset="5%" stopColor="#ef4444" stopOpacity={0.8}/>
                              <stop offset="95%" stopColor="#ef4444" stopOpacity={0}/>
                            </linearGradient>
                          </defs>
                          <CartesianGrid strokeDasharray="3 3" stroke="#18181b" />
                          <XAxis dataKey="name" stroke="#52525b" fontSize={11} />
                          <YAxis stroke="#52525b" fontSize={11} />
                          <Tooltip contentStyle={{ backgroundColor: '#09090b', borderColor: '#27272a', color: '#fff' }} />
                          <Area type="monotone" dataKey="Assinantes" stroke="#ef4444" fillOpacity={1} fill="url(#colorAssinantes)" />
                        </AreaChart>
                      </ResponsiveContainer>
                    </div>
                  </div>

                  <div className="bg-zinc-950 border border-zinc-900 rounded-xl p-6 flex flex-col gap-4">
                    <h4 className="font-bold text-sm text-zinc-300">Curva de Faturamento Estimado (R$)</h4>
                    <div className="h-64">
                      <ResponsiveContainer width="100%" height="100%">
                        <BarChart data={growthData}>
                          <CartesianGrid strokeDasharray="3 3" stroke="#18181b" />
                          <XAxis dataKey="name" stroke="#52525b" fontSize={11} />
                          <YAxis stroke="#52525b" fontSize={11} />
                          <Tooltip contentStyle={{ backgroundColor: '#09090b', borderColor: '#27272a', color: '#fff' }} />
                          <Bar dataKey="Receita" fill="#10b981" radius={[4, 4, 0, 0]} />
                        </BarChart>
                      </ResponsiveContainer>
                    </div>
                  </div>
                </div>

                {/* Top list items views count */}
                <div className="bg-zinc-950 border border-zinc-900 rounded-xl p-6">
                  <h4 className="font-bold text-sm text-zinc-300 mb-4">Rank dos Conteúdos Mais Assistidos</h4>
                  <div className="overflow-x-auto">
                    <table className="w-full text-left text-xs text-zinc-400">
                      <thead>
                        <tr className="border-b border-zinc-900 text-white font-mono uppercase pb-2">
                          <th className="py-2.5">Título do Vídeo</th>
                          <th className="py-2.5">Gênero / Categoria</th>
                          <th className="py-2.5 text-right">Cliques no Player</th>
                        </tr>
                      </thead>
                      <tbody className="divide-y divide-zinc-900 font-medium">
                        {contents.slice().sort((a,b)=>b.viewsCount - a.viewsCount).map((item, index) => (
                          <tr key={item.id} className="hover:bg-zinc-900/40">
                            <td className="py-3 font-semibold text-zinc-200">{index + 1}. {item.title}</td>
                            <td className="py-3 text-zinc-550">{item.genre}</td>
                            <td className="py-3 text-right text-[#ef4444] font-mono font-bold">{item.viewsCount.toLocaleString('pt-BR')} views</td>
                          </tr>
                        ))}
                      </tbody>
                    </table>
                  </div>
                </div>

              </section>
            )}

            {/* TAB CONTENT: 2. USUÁRIOS & CRM */}
            {activeTab === 'usuarios' && (
              <section className="flex flex-col gap-6 animate-fade-in">
                <div className="flex justify-between items-center border-b border-zinc-900 pb-5">
                  <div>
                    <span className="text-xs font-mono font-bold text-zinc-500 uppercase">CADASTROS INTEGRADOS</span>
                    <h1 className="text-2xl font-black tracking-tight mt-1">Assinantes & Permissões</h1>
                  </div>
                  <button 
                    onClick={() => handleOpenUserModal()}
                    className="bg-[#ef4444] hover:bg-red-700 text-white font-bold px-4 py-2 rounded-lg text-xs flex items-center gap-1.5 cursor-pointer transition"
                  >
                    <Plus className="w-4 h-4" />
                    <span>Adicionar Usuário</span>
                  </button>
                </div>

                {/* Advanced Filters and Export Bar */}
                <div className="bg-zinc-950 border border-zinc-900 p-4 rounded-xl flex flex-col md:flex-row gap-4 items-center justify-between">
                  <div className="flex flex-wrap gap-3.5 items-center w-full md:w-auto">
                    {/* Search box */}
                    <div className="relative w-full sm:w-64">
                      <Search className="w-3.5 h-3.5 text-zinc-550 absolute left-3 top-1/2 -translate-y-1/2" />
                      <input
                        type="text"
                        placeholder="Buscar por nome, e-mail ou telefone..."
                        value={crmSearch}
                        onChange={(e) => setCrmSearch(e.target.value)}
                        className="bg-zinc-900 border border-zinc-850 focus:border-[#ef4444] rounded-lg pl-9 pr-3 py-2 text-xs text-zinc-200 outline-none w-full font-medium"
                      />
                    </div>

                    {/* Plan selection */}
                    <div className="flex items-center gap-1.5 w-full sm:w-auto">
                      <span className="text-[10px] font-mono font-bold text-zinc-500 uppercase shrink-0">Plano:</span>
                      <select
                        value={crmPlanFilter}
                        onChange={(e) => setCrmPlanFilter(e.target.value)}
                        className="bg-zinc-900 border border-zinc-850 focus:border-[#ef4444] text-xs text-zinc-300 rounded-lg px-2.5 py-1.5 font-bold outline-none cursor-pointer w-full sm:w-auto"
                      >
                        <option value="all">TODOS OS PLANOS</option>
                        {plans.map(p => (
                          <option key={p.id} value={p.id}>{p.name.toUpperCase()}</option>
                        ))}
                      </select>
                    </div>

                    {/* Status filter */}
                    <div className="flex items-center gap-1.5 w-full sm:w-auto">
                      <span className="text-[10px] font-mono font-bold text-zinc-500 uppercase shrink-0">Status:</span>
                      <select
                        value={crmStatusFilter}
                        onChange={(e) => setCrmStatusFilter(e.target.value)}
                        className="bg-zinc-900 border border-zinc-850 focus:border-[#ef4444] text-xs text-zinc-300 rounded-lg px-2.5 py-1.5 font-bold outline-none cursor-pointer w-full sm:w-auto"
                      >
                        <option value="all">TODOS OS STATUS</option>
                        <option value="active">ATIVO</option>
                        <option value="pending">PENDENTE</option>
                        <option value="blocked">BLOQUEADO</option>
                      </select>
                    </div>
                  </div>

                  {/* Actions column: count & export */}
                  <div className="flex items-center justify-between md:justify-end gap-4 w-full md:w-auto shrink-0 border-t md:border-t-0 border-zinc-900 pt-3 md:pt-0">
                    <span className="text-[10px] font-mono font-bold text-zinc-550 uppercase">
                      {filteredUsers.length} de {users.length} cadastrados
                    </span>
                    
                    <button
                      onClick={handleExportUsersCSV}
                      className="bg-[#0f0f10] hover:bg-zinc-904 border border-zinc-800 text-zinc-200 font-bold px-3.5 py-2 rounded-lg text-xs flex items-center gap-1.5 cursor-pointer transition uppercase tracking-wider font-mono"
                    >
                      <Download className="w-3.5 h-3.5" />
                      <span>Exportar CSV</span>
                    </button>
                  </div>
                </div>

                {/* Table list */}
                <div className="bg-zinc-950 border border-zinc-900 rounded-xl overflow-hidden">
                  <div className="overflow-x-auto">
                    <table className="w-full text-left text-xs text-zinc-400">
                      <thead>
                        <tr className="bg-zinc-900/60 border-b border-zinc-850 text-white font-mono uppercase text-[10px]">
                          <th className="p-4">Nome completo</th>
                          <th className="p-4">E-mail</th>
                          <th className="p-4">Telefone</th>
                          <th className="p-4">Função / Cargo</th>
                          <th className="p-4">Plano</th>
                          <th className="p-4">Estado</th>
                          <th className="p-4 text-center">Ações de Gestão</th>
                        </tr>
                      </thead>
                      <tbody className="divide-y divide-zinc-900 font-medium">
                        {filteredUsers.map((u) => (
                          <tr key={u.id} className="hover:bg-zinc-900/20">
                            <td className="p-4 text-zinc-200 font-bold">{u.name}</td>
                            <td className="p-4 font-mono text-zinc-400">{u.email}</td>
                            <td className="p-4 text-zinc-400">{u.phone || 'N/A'}</td>
                            <td className="p-4">
                              <span className={`px-2 py-0.5 rounded text-[10px] uppercase font-mono font-bold ${
                                u.role === 'admin' ? 'bg-[#ef4444]/10 text-[#ef4444]' : 
                                u.role === 'editor' ? 'bg-orange-950 text-orange-400' :
                                u.role === 'finance' ? 'bg-emerald-950 text-emerald-400' : 'bg-zinc-900 text-zinc-400'
                              }`}>
                                {u.role === 'subscriber' ? 'Assinante' : u.role}
                              </span>
                            </td>
                            <td className="p-4 text-zinc-300 font-bold uppercase font-mono">
                              {u.planId ? plans.find(p => p.id === u.planId)?.name.replace('Plano ', '') : 'N/A'}
                            </td>
                            <td className="p-4">
                              <span className={`px-2 py-0.5 rounded text-[10px] font-mono font-bold ${
                                u.status === 'active' ? 'bg-emerald-900/50 text-emerald-400' :
                                u.status === 'blocked' ? 'bg-red-950 text-red-500' : 'bg-zinc-800 text-zinc-500'
                              }`}>
                                {u.status}
                              </span>
                            </td>
                            <td className="p-4">
                              <div className="flex items-center justify-center gap-2">
                                <button 
                                  onClick={() => handleOpenUserModal(u)}
                                  className="p-1.5 hover:bg-zinc-900 rounded border border-zinc-850 text-zinc-450 hover:text-white transition cursor-pointer"
                                  title="Editar"
                                >
                                  <Edit2 className="w-3.5 h-3.5" />
                                </button>
                                <button 
                                  onClick={() => handleResetPasswordSimulated(u.email)}
                                  className="p-1.5 hover:bg-zinc-900 rounded border border-zinc-850 text-zinc-450 hover:text-white transition cursor-pointer"
                                  title="Disparar e-mail de redefinição de senha"
                                >
                                  <Settings className="w-3.5 h-3.5 text-zinc-500" />
                                </button>
                                <button 
                                  onClick={() => handleDeleteUser(u.id)}
                                  className="p-1.5 hover:bg-red-950/20 rounded border border-zinc-850 hover:border-red-955 text-zinc-455 hover:text-[#ef4444] transition cursor-pointer"
                                  title="Apagar permanentemente"
                                >
                                  <Trash2 className="w-3.5 h-3.5" />
                                </button>
                              </div>
                            </td>
                          </tr>
                        ))}
                      </tbody>
                    </table>
                  </div>
                </div>
              </section>
            )}

            {/* TAB CONTENT: 3. VIDEOS & CONTEUDO */}
            {activeTab === 'conteudo' && (
              <section className="flex flex-col gap-6 animate-fade-in">
                <div className="flex justify-between items-center border-b border-zinc-900 pb-5">
                  <div>
                    <span className="text-xs font-mono font-bold text-zinc-500 uppercase">ARQUIVO DE VÍDEOS</span>
                    <h1 className="text-2xl font-black mt-1">Gerenciamento de Conteúdos</h1>
                  </div>
                  <button 
                    onClick={() => handleOpenContentModal()}
                    className="bg-[#ef4444] hover:bg-red-700 text-white font-bold px-4 py-2 rounded-lg text-xs flex items-center gap-1.5 cursor-pointer transition"
                  >
                    <Plus className="w-4 h-4" />
                    <span>Adicionar Filme/Documentário</span>
                  </button>
                </div>

                <div className="bg-zinc-950 border border-zinc-900 rounded-xl overflow-hidden">
                  <div className="overflow-x-auto">
                    <table className="w-full text-left text-xs text-zinc-400">
                      <thead>
                        <tr className="bg-zinc-900/60 border-b border-zinc-850 text-white font-mono uppercase text-[10px]">
                          <th className="p-4">Imagem</th>
                          <th className="p-4">Título</th>
                          <th className="p-4">Tipo</th>
                          <th className="p-4">Gênero</th>
                          <th className="p-4">Duração</th>
                          <th className="p-4">Ano</th>
                          <th className="p-4">Limites</th>
                          <th className="p-4">Estado</th>
                          <th className="p-4 text-center">Ações</th>
                        </tr>
                      </thead>
                      <tbody className="divide-y divide-zinc-900 font-medium">
                        {contents.map((c) => (
                          <tr key={c.id} className="hover:bg-zinc-900/20">
                            <td className="p-4 shrink-0">
                              <img 
                                src={c.coverUrl} 
                                alt={c.title} 
                                referrerPolicy="no-referrer"
                                className="w-10 h-14 object-cover rounded bg-zinc-900 border border-zinc-800"
                              />
                            </td>
                            <td className="p-4 text-zinc-200 font-bold max-w-xs truncate">{c.title}</td>
                            <td className="p-4 uppercase font-mono text-[10px] text-zinc-500">{c.type}</td>
                            <td className="p-4 text-zinc-400 font-bold">{c.genre}</td>
                            <td className="p-4 font-mono">{c.duration}</td>
                            <td className="p-4 font-mono">{c.year}</td>
                            <td className="p-4">
                              <div className="flex flex-wrap gap-1">
                                {c.isExclusive && <span className="bg-rose-950 text-rose-400 text-[9px] font-mono px-1 rounded">Exclusive</span>}
                                {c.isFree && <span className="bg-emerald-900/45 text-emerald-400 text-[9px] font-mono px-1 rounded">Grátis</span>}
                                {c.isFeatured && <span className="bg-[#ef4444]/15 text-[#ef4444] text-[9px] font-mono px-1 rounded">Featured</span>}
                              </div>
                            </td>
                            <td className="p-4 uppercase font-mono text-[10px]">
                              <span className={c.status === 'published' ? 'text-emerald-500 font-bold' : 'text-zinc-500'}>
                                {c.status}
                              </span>
                            </td>
                            <td className="p-4">
                              <div className="flex justify-center gap-1.5">
                                <button 
                                  onClick={() => handleOpenContentModal(c)}
                                  className="p-1.5 hover:bg-zinc-900 rounded border border-zinc-850 text-zinc-400 hover:text-white cursor-pointer"
                                  title="Editar metadados"
                                >
                                  <Edit2 className="w-3.5 h-3.5" />
                                </button>
                                <button 
                                  onClick={() => handleDeleteContent(c.id)}
                                  className="p-1.5 hover:bg-red-950/20 rounded border border-zinc-850 hover:border-red-955 text-zinc-500 hover:text-[#ef4444] cursor-pointer"
                                  title="Excluir"
                                >
                                  <Trash2 className="w-3.5 h-3.5" />
                                </button>
                              </div>
                            </td>
                          </tr>
                        ))}
                      </tbody>
                    </table>
                  </div>
                </div>
              </section>
            )}

            {/* TAB CONTENT: 4. SERIES & EPISODES */}
            {activeTab === 'series' && (
              <section className="flex flex-col gap-6 animate-fade-in">
                <div className="flex justify-between items-center border-b border-zinc-900 pb-5">
                  <div>
                    <span className="text-xs font-mono font-bold text-zinc-500 uppercase">ESTRUTURA SERIAL</span>
                    <h1 className="text-2xl font-black mt-1">Séries, Temporadas & Episódios</h1>
                  </div>
                  
                  <div className="flex items-center gap-2">
                    <button 
                      onClick={() => setShowSeriesModal(true)}
                      className="bg-zinc-900 hover:bg-zinc-800 border border-zinc-800 hover:border-zinc-700 text-white font-bold px-3 py-2 rounded-lg text-xs flex items-center gap-1.5 cursor-pointer tracking-wide"
                    >
                      <FolderPlus className="w-4 h-4 text-[#ef4444]" />
                      <span>Criar Série</span>
                    </button>
                    <button 
                      onClick={() => {
                        if (series.length === 0) {
                          alert('Crie uma série primeiro!');
                          return;
                        }
                        setSelectedSeriesIdForEpisode(series[0].id);
                        setShowSeriesEpisodeModal(true);
                      }}
                      className="bg-[#ef4444] hover:bg-red-700 text-white font-bold px-3 py-2 rounded-lg text-xs flex items-center gap-1.5 cursor-pointer"
                    >
                      <Plus className="w-4 h-4" />
                      <span>Cadastrar Episódio</span>
                    </button>
                  </div>
                </div>

                {/* Series Catalog and episodes visual map */}
                <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                  
                  {/* Left Series List Card wrapper */}
                  <div className="bg-zinc-950 border border-zinc-900 rounded-xl p-5 flex flex-col gap-4">
                    <h4 className="font-bold text-sm text-zinc-350">Séries Ativas na Plataforma ({series.length})</h4>
                    <div className="flex flex-col gap-3">
                      {series.map(s => (
                        <div key={s.id} className="p-3 bg-zinc-900/60 border border-zinc-850/80 rounded-lg flex gap-4 items-center justify-between">
                          <div className="flex gap-3 items-center">
                            <img src={s.coverUrl} className="w-10 h-10 rounded object-cover shrink-0" alt={s.title} referrerPolicy="no-referrer" />
                            <div className="flex flex-col text-left">
                              <h5 className="font-bold text-sm text-white">{s.title}</h5>
                              <span className="text-[10px] text-zinc-500 font-mono font-semibold uppercase">{s.genre}</span>
                            </div>
                          </div>
                          <span className="text-[11px] font-mono text-[#ef4444] font-bold">
                            {seasons.filter(seas => seas.seriesId === s.id).length} Temporadas
                          </span>
                        </div>
                      ))}
                    </div>
                  </div>

                  {/* Right: Last published episodes audit list */}
                  <div className="bg-zinc-950 border border-zinc-900 rounded-xl p-5 flex flex-col gap-4">
                    <h4 className="font-bold text-sm text-zinc-350">Lista de Capítulos Publicados ({episodes.length})</h4>
                    <div className="flex flex-col gap-2 max-h-96 overflow-y-auto pr-1">
                      {episodes.map(ep => {
                        const sseason = seasons.find(s=>s.id === ep.seasonId);
                        const sseries = sseason ? series.find(ser=>ser.id === sseason.seriesId) : null;
                        return (
                          <div key={ep.id} className="p-2.5 bg-zinc-900/40 border border-zinc-850 rounded flex items-center justify-between text-xs">
                            <div className="flex flex-col gap-0.5">
                              <span className="text-[9px] font-mono text-zinc-500 uppercase">{sseries?.title || 'F5 TV'} • Temp {sseason?.number || 1} Ep {ep.number}</span>
                              <h5 className="font-bold text-zinc-200">{ep.title}</h5>
                            </div>
                            <span className="font-mono text-zinc-500 text-[11px]">{ep.duration}</span>
                          </div>
                        );
                      })}
                    </div>
                  </div>
                </div>

              </section>
            )}

            {/* TAB CONTENT: 5. UPLOAD DE MÍDIA */}
            {activeTab === 'uploads' && (
              <section className="flex flex-col gap-6 animate-fade-in">
                <div className="border-b border-zinc-900 pb-5">
                  <span className="text-xs font-mono font-bold text-zinc-500 uppercase">TRANSCODIFICAÇÃO DE VÍDEO</span>
                  <h1 className="text-2xl font-black mt-1 font-sans">Canal de Uploads Ativos</h1>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
                  
                  {/* Left: Interactive Sim Dropzone */}
                  <div className="md:col-span-1 bg-zinc-950 border border-zinc-900 p-6 rounded-xl flex flex-col gap-4">
                    <h4 className="font-bold text-sm text-zinc-300">Novo Envio de Vídeo</h4>
                    
                    <form onSubmit={handleUploadTrigger} className="flex flex-col gap-4">
                      <div className="flex flex-col gap-1.5">
                        <label className="text-xs font-mono font-bold text-zinc-400 uppercase">Título amigável de mídia</label>
                        <input
                          id="upl-title-input"
                          type="text"
                          required
                          value={uploadTitle}
                          onChange={(e) => setUploadTitle(e.target.value)}
                          placeholder="Ex: Conexão F5 - O Caso dos Ransomwares"
                          className="w-full bg-zinc-900 border border-zinc-800 focus:border-[#ef4444] rounded-lg p-2.5 outline-none text-zinc-100 placeholder:text-zinc-650"
                        />
                      </div>

                      {/* Fake drag and drop dropzone */}
                      <div className="border border-dashed border-zinc-800 hover:border-zinc-600 bg-zinc-900/40 py-8 px-4 text-center rounded-xl cursor-not-allowed">
                        <UploadCloud className="w-8 h-8 mx-auto text-zinc-500 mb-2" />
                        <span className="text-xs font-semibold text-zinc-300 block">Arraste arquivos de vídeo aqui</span>
                        <span className="text-[10px] text-zinc-600 font-mono block mt-1">(Aceita MP4, MOV, WebM - Max 5 GB)</span>
                      </div>

                      <button
                        id="upl-sim-trigger-btn"
                        type="submit"
                        disabled={uploadStatus === 'uploading' || uploadStatus === 'processing'}
                        className="bg-[#ef4444] hover:bg-red-700 disabled:bg-zinc-850 disabled:text-zinc-500 text-white font-bold py-2.5 rounded-lg text-xs cursor-pointer tracking-wider font-mono transition"
                      >
                        {uploadStatus === 'idle' && 'Simular Envio de Vídeo'}
                        {uploadStatus === 'uploading' && 'Enviando...'}
                        {uploadStatus === 'processing' && 'Processando para HD...'}
                      </button>
                    </form>

                    {/* Progress visual state */}
                    {uploadProgress >= 0 && (
                      <div className="border-t border-zinc-900 pt-4 mt-2 flex flex-col gap-2 text-xs">
                        <div className="flex justify-between font-mono">
                          <span className="font-bold text-[#ef4444] uppercase tracking-wide">
                            {uploadStatus === 'uploading' && 'Uploading...'}
                            {uploadStatus === 'processing' && 'Transcodificando...'}
                            {uploadStatus === 'success' && 'Completo com Êxito!'}
                          </span>
                          <span className="font-bold text-zinc-300">{uploadProgress}%</span>
                        </div>
                        <div className="h-1.5 bg-zinc-900 rounded-full overflow-hidden">
                          <div 
                            className="h-full bg-red-650 transition-all duration-150" 
                            style={{ width: `${uploadProgress}%` }}
                          />
                        </div>
                        {uploadStatus === 'processing' && (
                          <p className="text-[10px] text-zinc-500 font-medium leading-normal animate-pulse">
                            Nossa infraestrutura está gerando manifestos de reprodução HLS multimídia, capas elegantes e thumbnails automáticos.
                          </p>
                        )}
                        {uploadStatus === 'success' && (
                          <p className="text-[10px] text-emerald-400 font-bold">
                            ✓ Arquivo de vídeo guardado no sandbox local com sucesso. Pronto para publicação!
                          </p>
                        )}
                      </div>
                    )}
                  </div>

                  {/* Right: Uploaded media queue history list */}
                  <div className="md:col-span-2 bg-zinc-950 border border-zinc-900 p-6 rounded-xl flex flex-col gap-4">
                    <h4 className="font-bold text-sm text-zinc-300">Histórico de Transcodificações Recentes</h4>
                    <div className="flex flex-col gap-3">
                      {uploads.map(u => (
                        <div key={u.id} className="p-3 bg-zinc-900/60 border border-zinc-850/80 rounded-xl flex gap-4 items-center justify-between text-xs">
                          <div className="flex gap-3 items-center">
                            <UploadCloud className="w-6 h-6 text-[#ef4444]" />
                            <div className="flex flex-col text-left">
                              <h5 className="font-bold text-white leading-snug">{u.title}</h5>
                              <span className="text-[10px] text-zinc-500 font-mono mt-0.5">{u.fileName} ({u.size})</span>
                            </div>
                          </div>
                          
                          <div className="flex items-center gap-3">
                            <span className="font-mono text-[10px] bg-emerald-950 text-emerald-400 border border-emerald-900 px-1.5 py-0.5 rounded font-black uppercase">Pronto</span>
                            <span className="text-[10px] text-zinc-550 font-mono">{new Date(u.createdAt).toLocaleDateString()}</span>
                          </div>
                        </div>
                      ))}
                    </div>
                  </div>

                </div>

              </section>
            )}

            {/* TAB CONTENT: 6. FINANCEIRO & EXPORTAÇÕES */}
            {activeTab === 'financeiro' && (
              <section className="flex flex-col gap-6 animate-fade-in">
                <div className="flex justify-between items-center border-b border-zinc-900 pb-5">
                  <div>
                    <span className="text-xs font-mono font-bold text-zinc-500 uppercase">LUCROS & CONTABILIDADE</span>
                    <h1 className="text-2xl font-black mt-1">Gerenciamento Financeiro</h1>
                  </div>
                  <button 
                    id="finance-export-csv"
                    onClick={handleExportCSV}
                    className="bg-zinc-900 hover:bg-zinc-805 border border-zinc-800 hover:border-zinc-700 text-white font-bold px-3 py-2 rounded-lg text-xs flex items-center gap-1.5 cursor-pointer"
                  >
                    <Download className="w-4 h-4 text-[#ef4444]" />
                    <span>Exportar Transações (CSV)</span>
                  </button>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                  <div className="bg-zinc-950 border border-zinc-900 p-5 rounded-xl text-left">
                    <span className="text-zinc-550 text-[11px] font-mono tracking-wider block mb-1">FATURAMENTO ACUMULADO</span>
                    <h3 className="text-2xl font-black text-white">R$ {totalRevenue.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}</h3>
                    <p className="text-[10px] text-[#ef4444] font-mono font-bold mt-1.5">• Total faturado no MVP</p>
                  </div>
                  <div className="bg-zinc-950 border border-zinc-900 p-5 rounded-xl text-left">
                    <span className="text-zinc-550 text-[11px] font-mono tracking-wider block mb-1">MENSAL RECORRENTE SIMULADO (MRR)</span>
                    <h3 className="text-2xl font-black text-emerald-400">R$ {monthlyRecurrentRevenue.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}</h3>
                    <p className="text-[10px] text-zinc-650 font-mono font-bold mt-1.5">• Baseado em planos de contas ativos</p>
                  </div>
                  <div className="bg-zinc-950 border border-zinc-900 p-5 rounded-xl text-left">
                    <span className="text-zinc-550 text-[11px] font-mono tracking-wider block mb-1">DÉBITOS EM ATRAZO</span>
                    <h3 className="text-2xl font-black text-red-500">R$ 29,90</h3>
                    <p className="text-[10px] text-orange-400 font-mono font-bold mt-1.5">• 1 inadimplente pendente de faturar</p>
                  </div>
                </div>

                {/* Audit payments ledger tables */}
                <div className="bg-zinc-950 border border-zinc-900 rounded-xl overflow-hidden mt-2">
                  <div className="bg-zinc-900/60 p-4 border-b border-zinc-850 flex justify-between items-center">
                    <h4 className="font-bold text-sm text-zinc-300">Histórico de Transações de Assinantes</h4>
                    <span className="text-[10px] text-zinc-500 font-mono">Mostrando {payments.length} transações salvas</span>
                  </div>
                  
                  <div className="overflow-x-auto animate-fade-in">
                    <table className="w-full text-left text-xs text-zinc-400">
                      <thead>
                        <tr className="border-b border-zinc-850 font-mono uppercase text-white">
                          <th className="p-4">Identificação</th>
                          <th className="p-4">Assinante / E-mail</th>
                          <th className="p-4">Valor Faturado</th>
                          <th className="p-4">Data Pagamento</th>
                          <th className="p-4">Portão Cobrança</th>
                          <th className="p-4">Estado Transação</th>
                        </tr>
                      </thead>
                      <tbody className="divide-y divide-zinc-90 w-full font-medium">
                        {payments.map(p => {
                          const assocUser = users.find(u => u.id === p.userId);
                          return (
                            <tr key={p.id} className="hover:bg-zinc-900/10">
                              <td className="p-4 font-mono font-bold text-rose-500">{p.id}</td>
                              <td className="p-4 text-left">
                                <span className="font-bold text-zinc-200 block">{assocUser?.name || 'Assinante Desconhecido'}</span>
                                <span className="text-[10px] font-mono text-zinc-500">{assocUser?.email || 'N/A'}</span>
                              </td>
                              <td className="p-4 text-zinc-250 font-bold text-white font-mono">R$ {p.value.toFixed(2)}</td>
                              <td className="p-4 font-mono text-zinc-400">{p.date || 'Hoje'}</td>
                              <td className="p-4 font-mono uppercase tracking-wide text-zinc-500">{p.paymentMethod}</td>
                              <td className="p-4 uppercase font-mono text-[10px]">
                                <span className={`px-2 py-0.5 rounded ${
                                  p.status === 'paid' ? 'bg-emerald-900/50 text-emerald-400 font-bold' :
                                  p.status === 'overdue' ? 'bg-red-950 text-red-400' : 'bg-zinc-800 text-zinc-500'
                                }`}>
                                  {p.status}
                                </span>
                              </td>
                            </tr>
                          );
                        })}
                      </tbody>
                    </table>
                  </div>
                </div>

              </section>
            )}

            {/* TAB CONTENT: 7. CONFIGURAÇÃO DE PLANOS */}
            {activeTab === 'planos' && (
              <section className="flex flex-col gap-6 animate-fade-in text-white">
                <div className="border-b border-zinc-900 pb-5">
                  <span className="text-xs font-mono font-bold text-zinc-500 uppercase">DADOS DE NEGÓCIO</span>
                  <h1 className="text-2xl font-black mt-1">Configurações de Pacotes de Planos</h1>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                  {plans.map(p => (
                    <div key={p.id} className="bg-zinc-950 border border-zinc-900 rounded-xl p-6 flex flex-col justify-between gap-5 text-left">
                      <div className="flex flex-col gap-2">
                        <div className="flex justify-between items-center mb-1">
                          <span className="text-xs font-mono font-bold text-zinc-500 uppercase">{p.id}</span>
                          <span className={`px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase ${
                            p.active ? 'bg-emerald-950 text-emerald-400 border border-emerald-900' : 'bg-red-950 text-red-400 border border-red-900'
                          }`}>
                            {p.active ? 'Ativo' : 'Inativo'}
                          </span>
                        </div>
                        <h4 className="font-bold text-lg text-white">{p.name}</h4>
                        <span className="text-xl font-black text-white font-mono">R$ {p.price.toFixed(2)} / mês</span>
                        
                        <div className="border-t border-zinc-900 pt-3 flex flex-col gap-1.5 mt-2">
                          {p.features.map((f, i) => (
                            <span key={i} className="text-xs text-zinc-400 flex gap-1.5 items-start">
                              <span className="text-[#ef4444] font-bold">•</span>
                              <span className="leading-snug">{f}</span>
                            </span>
                          ))}
                        </div>
                      </div>

                      <button
                        onClick={() => togglePlanActive(p.id)}
                        className={`w-full py-2 rounded text-xs font-mono font-bold uppercase transition cursor-pointer ${
                          p.active 
                            ? 'bg-rose-950/20 hover:bg-rose-950/50 text-[#ef4444] border border-rose-950' 
                            : 'bg-emerald-950/20 hover:bg-emerald-950/50 text-emerald-400 border border-emerald-900'
                        }`}
                      >
                        {p.active ? 'Suspender Assinaturas Novas' : 'Ativar Plano para Venda'}
                      </button>
                    </div>
                  ))}
                </div>

                {/* Reset database notice */}
                <div className="mt-8 border border-zinc-900 bg-neutral-950/60 rounded-xl p-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 text-left">
                  <div className="flex flex-col">
                    <span className="text-[#ef4444] font-bold font-mono text-xs uppercase flex items-center gap-1.5">
                      <AlertTriangle className="w-4 h-4" />
                      <span>Manutenção Tática de Sistema</span>
                    </span>
                    <p className="text-zinc-500 text-xs mt-1 leading-normal max-w-xl">
                      Para apagar as simulações, resets de dados das séries cadastradas ou modificações feitas nas tabelas e restaurar as configurações padrão de fábrica do MVP da F5 TV, utilize o botão ao lado.
                    </p>
                  </div>
                  <button 
                    onClick={() => {
                      if (confirm('Atenção: isto apagará de forma irreversível todas as alterações locais no localStorage! Continuar?')) {
                        localStorage.clear();
                        window.location.reload();
                      }
                    }}
                    className="bg-[#ef4444] hover:bg-red-700 text-white font-bold py-2.5 px-4 rounded text-xs uppercase font-mono tracking-widest cursor-pointer transition shrink-0"
                  >
                    Zerar Storage Geral
                  </button>
                </div>

              </section>
            )}

            {/* TAB CONTENT: 8. ASSINANTES & CRM */}
            {activeTab === 'assinantes' && (
              <section className="flex flex-col gap-6 animate-fade-in text-white text-left">
                <div className="flex justify-between items-center border-b border-zinc-900 pb-5">
                  <div>
                    <span className="text-xs font-mono font-bold text-zinc-500 uppercase">GESTÃO DE CLIENTES</span>
                    <h1 className="text-2xl font-black mt-1">Controle de Assinantes</h1>
                  </div>
                  <button 
                    onClick={handleExportSubscribersCSV}
                    className="bg-zinc-900 hover:bg-zinc-800 border border-zinc-800 text-white font-bold px-4 py-2 rounded-lg text-xs flex items-center gap-1.5 cursor-pointer transition"
                  >
                    <Download className="w-4 h-4 text-[#ef4444]" />
                    <span>Exportar Assinantes (CSV)</span>
                  </button>
                </div>

                {/* Filters */}
                <div className="bg-zinc-950 border border-zinc-905 p-4 rounded-xl flex flex-col md:flex-row gap-4 items-center justify-between">
                  <div className="flex flex-wrap gap-3.5 items-center w-full md:w-auto">
                    <div className="relative w-full sm:w-64">
                      <Search className="w-3.5 h-3.5 text-zinc-500 absolute left-3 top-1/2 -translate-y-1/2" />
                      <input
                        type="text"
                        placeholder="Buscar assinante por nome, e-mail..."
                        value={subscriberSearch}
                        onChange={(e) => setSubscriberSearch(e.target.value)}
                        className="bg-zinc-900 border border-zinc-800 focus:border-[#ef4444] rounded-lg pl-9 pr-3 py-2 text-xs text-zinc-200 outline-none w-full font-medium"
                      />
                    </div>

                    <div className="flex items-center gap-1.5 w-full sm:w-auto">
                      <span className="text-[10px] font-mono font-bold text-zinc-500 uppercase">Plano:</span>
                      <select
                        value={subscriberPlanFilter}
                        onChange={(e) => setSubscriberPlanFilter(e.target.value)}
                        className="bg-zinc-900 border border-zinc-800 text-xs text-zinc-300 rounded-lg px-2 py-1 outline-none cursor-pointer"
                      >
                        <option value="all">TODOS</option>
                        {plans.map(p => (
                          <option key={p.id} value={p.id}>{p.name.toUpperCase()}</option>
                        ))}
                      </select>
                    </div>

                    <div className="flex items-center gap-1.5 w-full sm:w-auto">
                      <span className="text-[10px] font-mono font-bold text-zinc-500 uppercase">Status:</span>
                      <select
                        value={subscriberStatusFilter}
                        onChange={(e) => setSubscriberStatusFilter(e.target.value)}
                        className="bg-zinc-900 border border-zinc-800 text-xs text-zinc-300 rounded-lg px-2 py-1 outline-none cursor-pointer"
                      >
                        <option value="all">TODOS</option>
                        <option value="active">ATIVO</option>
                        <option value="pending">PENDENTE</option>
                        <option value="blocked">BLOQUEADO</option>
                      </select>
                    </div>
                  </div>

                  <span className="text-[10px] font-mono text-zinc-500 uppercase font-bold">
                    {filteredSubscribers.length} de {users.filter(u => u.role === 'subscriber').length} assinantes
                  </span>
                </div>

                {/* Table */}
                <div className="bg-zinc-950 border border-zinc-900 rounded-xl overflow-hidden">
                  <div className="overflow-x-auto">
                    <table className="w-full text-left text-xs text-zinc-400">
                      <thead>
                        <tr className="bg-zinc-900/60 border-b border-zinc-800 text-white font-mono uppercase text-[10px]">
                          <th className="p-4">Assinante</th>
                          <th className="p-4">E-mail</th>
                          <th className="p-4">Telefone</th>
                          <th className="p-4">Plano Vinculado</th>
                          <th className="p-4">Status</th>
                          <th className="p-4">Próxima Cobrança</th>
                          <th className="p-4 text-center">Ações</th>
                        </tr>
                      </thead>
                      <tbody className="divide-y divide-zinc-900 font-medium">
                        {filteredSubscribers.map(sub => {
                          const associatedSub = subscriptions.find(s => s.userId === sub.id);
                          return (
                            <tr key={sub.id} className="hover:bg-zinc-900/20">
                              <td className="p-4 text-zinc-200 font-bold">{sub.name}</td>
                              <td className="p-4 font-mono text-zinc-400">{sub.email}</td>
                              <td className="p-4 text-zinc-400">{sub.phone || 'Não informado'}</td>
                              <td className="p-4 text-[#ef4444] font-black font-mono">
                                {sub.planId ? plans.find(p => p.id === sub.planId)?.name : 'Sem Plano'}
                              </td>
                              <td className="p-4">
                                <span className={`px-2 py-0.5 rounded text-[9px] font-mono font-bold uppercase ${
                                  sub.status === 'active' ? 'bg-emerald-950 text-emerald-400 border border-emerald-900' :
                                  sub.status === 'blocked' ? 'bg-red-950 text-red-400 border border-red-900' : 'bg-amber-950 text-amber-400 border border-amber-900'
                                }`}>
                                  {sub.status}
                                </span>
                              </td>
                              <td className="p-4 font-mono text-zinc-400">
                                {associatedSub ? associatedSub.nextBillingDate : 'N/A'}
                              </td>
                              <td className="p-4 text-center">
                                <div className="flex gap-2 justify-center">
                                  <button
                                    onClick={() => setSelectedSubscriberForDetail(sub)}
                                    className="px-2.5 py-1 text-[10px] uppercase font-mono font-bold bg-zinc-905 hover:bg-zinc-800 border border-zinc-800 rounded transition cursor-pointer text-zinc-300 hover:text-white"
                                  >
                                    Ver Detalhes
                                  </button>
                                  <button
                                    onClick={() => handleToggleBlockSubscriber(sub.id)}
                                    className={`px-2.5 py-1 text-[10px] uppercase font-mono font-bold border rounded transition cursor-pointer ${
                                      sub.status === 'blocked'
                                        ? 'bg-emerald-950 hover:bg-emerald-900 text-emerald-400 border-emerald-900'
                                        : 'bg-rose-950 hover:bg-rose-900 text-red-400 border-rose-900'
                                    }`}
                                  >
                                    {sub.status === 'blocked' ? 'Reativar' : 'Bloquear'}
                                  </button>
                                </div>
                              </td>
                            </tr>
                          );
                        })}
                      </tbody>
                    </table>
                  </div>
                </div>

                {/* Sub Detail Slide-Over Modal */}
                {selectedSubscriberForDetail && (
                  <div className="fixed inset-0 bg-black/85 backdrop-blur-xs flex justify-end z-[60] animate-fade-in text-white/90">
                    <div className="bg-zinc-950 w-full max-w-xl border-l border-zinc-850 h-full p-8 overflow-y-auto flex flex-col gap-6 relative shadow-2xl">
                      <button 
                        onClick={() => setSelectedSubscriberForDetail(null)}
                        className="absolute top-6 right-6 p-2 hover:bg-zinc-905 rounded-full transition cursor-pointer"
                      >
                        <X className="w-5 h-5 text-zinc-400 hover:text-white" />
                      </button>

                      <div className="border-b border-zinc-900 pb-5">
                        <span className="text-[10px] font-mono text-zinc-550 uppercase">PAINEL DO ASSINANTE</span>
                        <h2 className="text-2xl font-black mt-1 text-white">{selectedSubscriberForDetail.name}</h2>
                        <span className="text-xs text-zinc-500 font-mono">{selectedSubscriberForDetail.email}</span>
                      </div>

                      <div className="grid grid-cols-2 gap-4">
                        <div className="bg-zinc-900/50 p-4 border border-zinc-850 rounded-xl">
                          <span className="text-[10px] font-mono text-zinc-500">ID INTERNO</span>
                          <p className="font-mono mt-1 text-xs truncate font-bold text-zinc-300">{selectedSubscriberForDetail.id}</p>
                        </div>
                        <div className="bg-zinc-900/50 p-4 border border-zinc-850 rounded-xl">
                          <span className="text-[10px] font-mono text-zinc-500">DATA CADASTRO</span>
                          <p className="font-mono mt-1 text-xs font-bold text-zinc-300">{selectedSubscriberForDetail.createdAt ? new Date(selectedSubscriberForDetail.createdAt).toLocaleDateString() : 'Não informada'}</p>
                        </div>
                      </div>

                      <div className="bg-zinc-900/30 border border-zinc-900 p-5 rounded-xl flex flex-col gap-4 text-left">
                        <h4 className="font-black text-sm text-zinc-200">Plano Vinculado & Estado</h4>
                        
                        <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                          <div className="flex flex-col">
                            <span className="text-xs text-zinc-500">ALTERAR CATEGORIA DO PLANO</span>
                            <select
                              value={selectedSubscriberForDetail.planId || 'plano-premium'}
                              onChange={(e) => handleChangeSubscriberPlan(selectedSubscriberForDetail.id, e.target.value)}
                              className="bg-zinc-950 border border-zinc-800 text-xs font-bold mt-1.5 p-2 rounded outline-none"
                            >
                              {plans.map(p => (
                                <option key={p.id} value={p.id}>{p.name.toUpperCase()} (R$ {p.price.toFixed(2)})</option>
                              ))}
                            </select>
                          </div>

                          <div className="flex flex-col text-right sm:items-end">
                            <span className="text-xs text-zinc-500 block">ESTADO ATUAL</span>
                            <span className={`px-3 py-1 mt-1 rounded text-xs font-mono font-bold uppercase inline-block ${
                              selectedSubscriberForDetail.status === 'active' ? 'bg-emerald-950 text-emerald-400 border border-emerald-900' : 'bg-red-950 text-red-400 border border-red-900'
                            }`}>
                              {selectedSubscriberForDetail.status}
                            </span>
                          </div>
                        </div>
                      </div>

                      {/* Financial History Resume */}
                      <div className="flex flex-col gap-3">
                        <h4 className="font-bold text-sm text-zinc-300 border-b border-zinc-900 pb-2">Histórico Financeiro Resumido</h4>
                        <div className="flex flex-col gap-2 max-h-48 overflow-y-auto pr-1">
                          {payments.filter(p => p.userId === selectedSubscriberForDetail.id).length === 0 ? (
                            <p className="text-xs text-zinc-500 py-3 text-center">Nenhum pagamento registrado localmente no MVP para esta conta.</p>
                          ) : (
                            payments.filter(p => p.userId === selectedSubscriberForDetail.id).map(p => (
                              <div key={p.id} className="p-3 bg-zinc-900/60 border border-zinc-850 rounded-lg flex items-center justify-between text-xs font-semibold">
                                <div className="flex flex-col text-left">
                                  <span className="font-mono text-[9px] text-[#ef4444]">{p.id} • {p.date || 'Hoje'}</span>
                                  <span className="text-zinc-400 uppercase font-mono">{p.paymentMethod}</span>
                                </div>
                                <div className="text-right">
                                  <span className="text-white font-mono block">R$ {p.value.toFixed(2)}</span>
                                  <span className="text-emerald-500 font-mono uppercase text-[9px]">{p.status}</span>
                                </div>
                              </div>
                            ))
                          )}
                        </div>
                      </div>
                    </div>
                  </div>
                )}
              </section>
            )}

            {/* TAB CONTENT: 9. TEMPORADAS */}
            {activeTab === 'temporadas' && (
              <section className="flex flex-col gap-6 animate-fade-in text-white text-left">
                <div className="flex justify-between items-center border-b border-zinc-900 pb-5">
                  <div>
                    <span className="text-xs font-mono font-bold text-zinc-500 uppercase">ESTRUTURA DE SÉRIES</span>
                    <h1 className="text-2xl font-black mt-1">Gerenciador de Temporadas</h1>
                  </div>
                  <button 
                    onClick={() => handleOpenSeasonModal()}
                    className="bg-[#ef4444] hover:bg-red-700 text-white font-bold px-4 py-2 rounded-lg text-xs flex items-center gap-1.5 cursor-pointer transition shadow"
                  >
                    <Plus className="w-4 h-4" />
                    <span>Adicionar Temporada</span>
                  </button>
                </div>

                {/* Filter */}
                <div className="bg-zinc-950 border border-zinc-900 p-4 rounded-xl flex items-center justify-between">
                  <div className="flex items-center gap-1.5">
                    <span className="text-xs font-mono text-zinc-500 font-bold uppercase">Filtrar por Série:</span>
                    <select
                      value={selectedSeriesIdForSeasonFilter}
                      onChange={(e) => setSelectedSeriesIdForSeasonFilter(e.target.value)}
                      className="bg-zinc-900 border border-zinc-800 text-xs font-bold text-zinc-350 p-2 rounded outline-none"
                    >
                      <option value="all">TODAS AS SÉRIES</option>
                      {series.map(s => (
                        <option key={s.id} value={s.id}>{s.title}</option>
                      ))}
                    </select>
                  </div>
                  <span className="text-xs font-mono text-zinc-500">Exibindo {seasons.filter(seas => selectedSeriesIdForSeasonFilter === 'all' || seas.seriesId === selectedSeriesIdForSeasonFilter).length} registros</span>
                </div>

                {/* Table list */}
                <div className="bg-zinc-950 border border-zinc-900 rounded-xl overflow-hidden">
                  <div className="overflow-x-auto">
                    <table className="w-full text-left text-xs text-zinc-400">
                      <thead>
                        <tr className="bg-zinc-900/60 border-b border-zinc-850 text-white font-mono uppercase text-[10px]">
                          <th className="p-4">Série Correspondente</th>
                          <th className="p-4">Nº Temporada</th>
                          <th className="p-4">Título Exibido</th>
                          <th className="p-4">Estado no Player</th>
                          <th className="p-4 text-center">Ações</th>
                        </tr>
                      </thead>
                      <tbody className="divide-y divide-zinc-900 font-medium">
                        {seasons.filter(seas => selectedSeriesIdForSeasonFilter === 'all' || seas.seriesId === selectedSeriesIdForSeasonFilter).map(seas => {
                          const associatedSeries = series.find(s => s.id === seas.seriesId);
                          return (
                            <tr key={seas.id} className="hover:bg-zinc-900/20">
                              <td className="p-4 font-bold text-zinc-200">{associatedSeries?.title || 'Série Não Localizada'}</td>
                              <td className="p-4 font-mono font-extrabold text-[#ef4444]">Temporada {seas.number}</td>
                              <td className="p-4 text-zinc-350">{seas.title}</td>
                              <td className="p-4">
                                <span className={`px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase ${
                                  seas.status === 'published' ? 'bg-emerald-950 text-emerald-400 border border-emerald-900' : 'bg-red-950 text-red-400 border border-red-900'
                                }`}>
                                  {seas.status || 'published'}
                                </span>
                              </td>
                              <td className="p-4 text-center">
                                <div className="flex gap-2 justify-center">
                                  <button
                                    onClick={() => handleOpenSeasonModal(seas)}
                                    className="p-1.5 hover:bg-zinc-900 border border-zinc-800 rounded text-zinc-300 hover:text-white transition cursor-pointer"
                                  >
                                    <Edit2 className="w-3.5 h-3.5" />
                                  </button>
                                  <button
                                    onClick={() => handleDeleteSeason(seas.id)}
                                    className="p-1.5 hover:bg-rose-950 border border-zinc-800 hover:border-rose-900 rounded text-zinc-400 hover:text-red-500 transition cursor-pointer"
                                  >
                                    <Trash2 className="w-3.5 h-3.5" />
                                  </button>
                                </div>
                              </td>
                            </tr>
                          );
                        })}
                      </tbody>
                    </table>
                  </div>
                </div>

                {/* Season Create/Edit Modal */}
                {showSeasonModal && (
                  <div className="fixed inset-0 bg-black/80 backdrop-blur-xs z-50 flex items-center justify-center p-4">
                    <div className="bg-zinc-950 border border-zinc-805 rounded-xl max-w-md w-full p-6 relative">
                      <button 
                        onClick={() => setShowSeasonModal(false)}
                        className="absolute top-4 right-4 text-zinc-400 hover:text-white cursor-pointer"
                      >
                        <X className="w-5 h-5" />
                      </button>

                      <form onSubmit={handleSaveSeason} className="flex flex-col gap-4 text-left">
                        <div className="border-b border-zinc-900 pb-3">
                          <h3 className="text-lg font-black">{editingSeason ? 'Editar Temporada' : 'Adicionar Nova Temporada'}</h3>
                          <span className="text-[10px] text-zinc-500 font-mono">Estrutura de dados serial do streaming</span>
                        </div>

                        <div className="flex flex-col gap-1.5 text-xs">
                          <label className="text-zinc-400 font-bold uppercase font-mono">Vincular Série</label>
                          <select
                            value={seasonForm.seriesId}
                            onChange={(e) => setSeasonForm({ ...seasonForm, seriesId: e.target.value })}
                            className="bg-zinc-900 border border-zinc-800 text-xs font-bold p-2.5 rounded text-white"
                          >
                            {series.map(s => (
                              <option key={s.id} value={s.id}>{s.title}</option>
                            ))}
                          </select>
                        </div>

                        <div className="grid grid-cols-2 gap-4">
                          <div className="flex flex-col gap-1.5 text-xs">
                            <label className="text-zinc-400 font-bold uppercase font-mono">Nº Sequencial</label>
                            <input
                              type="number"
                              required
                              value={seasonForm.number}
                              onChange={(e) => setSeasonForm({ ...seasonForm, number: Number(e.target.value) })}
                              className="bg-zinc-900 border border-zinc-800 p-2.5 rounded text-white"
                            />
                          </div>

                          <div className="flex flex-col gap-1.5 text-xs">
                            <label className="text-zinc-400 font-bold uppercase font-mono">Estado</label>
                            <select
                              value={seasonForm.status}
                              onChange={(e) => setSeasonForm({ ...seasonForm, status: e.target.value as 'published'|'draft' })}
                              className="bg-zinc-900 border border-zinc-800 p-2.5 rounded text-white font-bold"
                            >
                              <option value="published">Publicado</option>
                              <option value="draft">Rascunho</option>
                            </select>
                          </div>
                        </div>

                        <div className="flex flex-col gap-1.5 text-xs">
                          <label className="text-zinc-400 font-bold uppercase font-mono font-mono">Título Exibido</label>
                          <input
                            type="text"
                            required
                            value={seasonForm.title}
                            onChange={(e) => setSeasonForm({ ...seasonForm, title: e.target.value })}
                            placeholder="Ex: 1ª Temporada / Volume I"
                            className="bg-zinc-900 border border-zinc-800 p-2.5 rounded text-white"
                          />
                        </div>

                        <div className="flex gap-2 justify-end border-t border-zinc-900 pt-4 mt-2">
                          <button
                            type="button"
                            onClick={() => setShowSeasonModal(false)}
                            className="px-4 py-2 hover:bg-zinc-900 border border-transparent rounded text-xs font-mono font-bold uppercase text-zinc-400 hover:text-white"
                          >
                            Voltar
                          </button>
                          <button
                            type="submit"
                            className="px-5 py-2 bg-[#ef4444] hover:bg-red-700 text-white font-bold rounded text-xs font-mono uppercase tracking-widest"
                          >
                            Confirmar
                          </button>
                        </div>
                      </form>
                    </div>
                  </div>
                )}
              </section>
            )}

            {/* TAB CONTENT: 10. EPISODIOS */}
            {activeTab === 'episodios' && (
              <section className="flex flex-col gap-6 animate-fade-in text-white text-left">
                <div className="flex justify-between items-center border-b border-zinc-900 pb-5">
                  <div>
                    <span className="text-xs font-mono font-bold text-zinc-500 uppercase">CAPÍTULOS STREAMING</span>
                    <h1 className="text-2xl font-black mt-1">Gerenciador de Episódios</h1>
                  </div>
                  <button 
                    onClick={() => handleOpenEpisodeModal()}
                    className="bg-[#ef4444] hover:bg-red-700 text-white font-bold px-4 py-2 rounded-lg text-xs flex items-center gap-1.5 cursor-pointer transition shadow"
                  >
                    <Plus className="w-4 h-4" />
                    <span>Adicionar Episódio</span>
                  </button>
                </div>

                {/* Filters */}
                <div className="bg-zinc-950 border border-zinc-900 p-4 rounded-xl flex flex-wrap gap-4 items-center justify-between">
                  <div className="flex flex-wrap gap-3.5 items-center">
                    <div className="flex items-center gap-1.5">
                      <span className="text-xs font-mono text-zinc-500 font-bold">Série:</span>
                      <select
                        value={selectedSeriesIdForEpisodeFilter}
                        onChange={(e) => setSelectedSeriesIdForEpisodeFilter(e.target.value)}
                        className="bg-zinc-900 border border-zinc-800 text-xs font-bold text-zinc-350 p-2 rounded outline-none"
                      >
                        <option value="all">TODAS OS CONTEÚDOS</option>
                        {series.map(s => (
                          <option key={s.id} value={s.id}>{s.title}</option>
                        ))}
                      </select>
                    </div>

                    <div className="flex items-center gap-1.5">
                      <span className="text-xs font-mono text-zinc-500 font-bold">Temporada:</span>
                      <select
                        value={selectedSeasonIdForEpisodeFilter}
                        onChange={(e) => setSelectedSeasonIdForEpisodeFilter(e.target.value)}
                        className="bg-zinc-900 border border-zinc-800 text-xs font-bold text-zinc-350 p-2 rounded outline-none"
                      >
                        <option value="all">TODOS</option>
                        {seasons.map(seas => (
                          <option key={seas.id} value={seas.id}>Temp {seas.number} • {(series.find(s=>s.id === seas.seriesId))?.title}</option>
                        ))}
                      </select>
                    </div>
                  </div>

                  <span className="text-xs font-mono text-zinc-550 mr-2">Total local de capitulos: {episodes.length}</span>
                </div>

                {/* Grid Lists */}
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                  {episodes
                    .filter(ep => {
                      const associatedSeason = seasons.find(seas => seas.id === ep.seasonId);
                      const assocSeriesId = associatedSeason ? associatedSeason.seriesId : '';
                      return selectedSeriesIdForEpisodeFilter === 'all' || assocSeriesId === selectedSeriesIdForEpisodeFilter;
                    })
                    .filter(ep => selectedSeasonIdForEpisodeFilter === 'all' || ep.seasonId === selectedSeasonIdForEpisodeFilter)
                    .map(ep => {
                      const associatedSeason = seasons.find(seas => seas.id === ep.seasonId);
                      const associatedSeries = associatedSeason ? series.find(s => s.id === associatedSeason.seriesId) : undefined;
                      return (
                        <div key={ep.id} className="bg-zinc-950 border border-zinc-900 p-4 rounded-xl flex gap-4 text-left relative overflow-hidden group">
                          <img 
                            src={ep.thumbnailUrl || 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?q=80&w=400'} 
                            alt={ep.title} 
                            referrerPolicy="no-referrer"
                            className="w-24 h-16 object-cover rounded-lg bg-zinc-900/60 border border-zinc-850 shrink-0"
                          />
                          <div className="flex flex-col justify-between flex-1 min-w-0">
                            <div>
                              <span className="text-[9px] font-mono font-bold uppercase tracking-wider text-zinc-500">
                                {associatedSeries?.title || 'F5 TV'} • Temp {associatedSeason?.number || 1} Ep {ep.number}
                              </span>
                              <h5 className="font-bold text-sm text-white truncate leading-snug">{ep.title}</h5>
                              <p className="text-[10px] text-zinc-400 line-clamp-1 mt-0.5">{ep.description || 'Nenhuma sinopse disponível'}</p>
                            </div>
                            <span className="text-[10px] font-mono text-[#ef4444] font-semibold mt-1 inline-block">Duração: {ep.duration || 'N/A'}</span>
                          </div>

                          <div className="absolute right-4 top-4 flex gap-1 bg-zinc-950/80 p-1 border border-zinc-850 rounded backdrop-blur-xs">
                            <button
                              onClick={() => handleOpenEpisodeModal(ep)}
                              className="p-1 hover:bg-zinc-900 text-zinc-400 hover:text-white rounded cursor-pointer"
                              title="Editar Episódio"
                            >
                              <Edit2 className="w-3.5 h-3.5" />
                            </button>
                            <button
                              onClick={() => handleDeleteEpisode(ep.id)}
                              className="p-1 hover:bg-zinc-900 text-zinc-400 hover:text-red-500 rounded cursor-pointer"
                              title="Remover Episódio"
                            >
                              <Trash2 className="w-3.5 h-3.5" />
                            </button>
                          </div>
                        </div>
                      );
                    })}
                </div>

                {/* Episode Modal Form */}
                {showEpisodeModal && (
                  <div className="fixed inset-0 bg-black/80 backdrop-blur-xs z-50 flex items-center justify-center p-4">
                    <div className="bg-zinc-950 border border-zinc-850 rounded-xl max-w-lg w-full p-6 relative">
                      <button 
                        onClick={() => setShowEpisodeModal(false)}
                        className="absolute top-4 right-4 text-zinc-400 hover:text-white cursor-pointer"
                      >
                        <X className="w-5 h-5" />
                      </button>

                      <form onSubmit={handleSaveEpisode} className="flex flex-col gap-4 text-left text-xs text-white">
                        <div className="border-b border-zinc-900 pb-3">
                          <h3 className="text-base font-black text-white">{editingEpisode ? 'Editar Episódio' : 'Adicionar Novo Episódio'}</h3>
                          <span className="text-[10px] text-zinc-500 font-mono">Ficha técnica e links CDN simulados</span>
                        </div>

                        <div className="grid grid-cols-2 gap-4">
                          <div className="flex flex-col gap-1.5">
                            <label className="text-zinc-400 font-bold uppercase font-mono">Vincular Série</label>
                            <select
                              value={episodeForm.seriesId}
                              onChange={(e) => setEpisodeForm({ ...episodeForm, seriesId: e.target.value })}
                              className="bg-zinc-900 border border-zinc-805 p-2 rounded text-white"
                            >
                              {series.map(s => (
                                <option key={s.id} value={s.id}>{s.title}</option>
                              ))}
                            </select>
                          </div>

                          <div className="flex flex-col gap-1.5">
                            <label className="text-zinc-400 font-bold uppercase font-mono">Vincular Temporada</label>
                            <select
                              value={episodeForm.seasonId}
                              onChange={(e) => setEpisodeForm({ ...episodeForm, seasonId: e.target.value })}
                              className="bg-zinc-900 border border-zinc-805 p-2 rounded text-white"
                            >
                              {seasons.map(seas => (
                                <option key={seas.id} value={seas.id}>Temp {seas.number} • {(series.find(s=>s.id === seas.seriesId))?.title}</option>
                              ))}
                            </select>
                          </div>
                        </div>

                        <div className="grid grid-cols-3 gap-4">
                          <div className="flex flex-col gap-1.5">
                            <label className="text-zinc-400 font-bold uppercase font-mono">Nº Sequencial</label>
                            <input
                              type="number"
                              required
                              value={episodeForm.number}
                              onChange={(e) => setEpisodeForm({ ...episodeForm, number: Number(e.target.value) })}
                              className="bg-zinc-900 border border-zinc-800 p-2.5 rounded text-white"
                            />
                          </div>

                          <div className="flex flex-col gap-1.5">
                            <label className="text-zinc-400 font-bold uppercase font-mono font-mono">Duração (Ex: 50m)</label>
                            <input
                              type="text"
                              required
                              value={episodeForm.duration}
                              onChange={(e) => setEpisodeForm({ ...episodeForm, duration: e.target.value })}
                              className="bg-zinc-900 border border-zinc-800 p-2.5 rounded text-white font-mono"
                            />
                          </div>

                          <div className="flex flex-col gap-1.5">
                            <label className="text-zinc-400 font-bold uppercase font-mono">Estado</label>
                            <select
                              value={episodeForm.status}
                              onChange={(e) => setEpisodeForm({ ...episodeForm, status: e.target.value as 'published'|'draft' })}
                              className="bg-zinc-900 border border-zinc-805 p-2 rounded text-white font-bold"
                            >
                              <option value="published">Publicado</option>
                              <option value="draft">Rascunho</option>
                            </select>
                          </div>
                        </div>

                        <div className="flex flex-col gap-1.5">
                          <label className="text-zinc-400 font-bold uppercase font-mono">Título do Capítulo</label>
                          <input
                            type="text"
                            required
                            value={episodeForm.title}
                            onChange={(e) => setEpisodeForm({ ...episodeForm, title: e.target.value })}
                            className="bg-zinc-900 border border-zinc-800 p-2.5 rounded text-white"
                          />
                        </div>

                        <div className="flex flex-col gap-1.5">
                          <label className="text-zinc-400 font-bold uppercase font-mono">Cartaz / Thumbnail (URL Unsplash)</label>
                          <input
                            type="text"
                            required
                            value={episodeForm.thumbnailUrl}
                            onChange={(e) => setEpisodeForm({ ...episodeForm, thumbnailUrl: e.target.value })}
                            className="bg-zinc-900 border border-zinc-800 p-2.5 rounded text-white font-mono text-[11px]"
                          />
                        </div>

                        <div className="flex flex-col gap-1.5">
                          <label className="text-zinc-400 font-bold uppercase font-mono">Vídeo CDN streaming file (URL MP4)</label>
                          <input
                            type="text"
                            required
                            value={episodeForm.videoUrl}
                            onChange={(e) => setEpisodeForm({ ...episodeForm, videoUrl: e.target.value })}
                            className="bg-zinc-900 border border-zinc-800 p-2.5 rounded text-white font-mono text-[11px]"
                          />
                        </div>

                        <div className="flex flex-col gap-1.5">
                          <label className="text-zinc-400 font-bold uppercase font-mono">Sinopse Breve do Episódio</label>
                          <textarea
                            value={episodeForm.description}
                            rows={2}
                            onChange={(e) => setEpisodeForm({ ...episodeForm, description: e.target.value })}
                            className="bg-zinc-900 border border-zinc-800 p-2.5 rounded text-white resize-none"
                          />
                        </div>

                        <div className="flex gap-2 justify-end border-t border-zinc-900 pt-3 mt-1">
                          <button
                            type="button"
                            onClick={() => setShowEpisodeModal(false)}
                            className="px-4 py-2 hover:bg-zinc-900 rounded font-bold font-mono text-zinc-400 hover:text-white uppercase"
                          >
                            Voltar
                          </button>
                          <button
                            type="submit"
                            className="px-5 py-2 bg-[#ef4444] hover:bg-red-700 text-white font-bold rounded font-mono uppercase tracking-widest"
                          >
                            Salvar
                          </button>
                        </div>
                      </form>
                    </div>
                  </div>
                )}
              </section>
            )}

            {/* TAB CONTENT: 11. BANNERS SLIDER */}
            {activeTab === 'banners' && (
              <section className="flex flex-col gap-6 animate-fade-in text-white text-left">
                <div className="flex justify-between items-center border-b border-zinc-900 pb-5">
                  <div>
                    <span className="text-xs font-mono font-bold text-zinc-500 uppercase">PROPAGANDA & HIGHLIGHTS</span>
                    <h1 className="text-2xl font-black mt-1">Banners Rotativos Slider</h1>
                  </div>
                  <button 
                    onClick={() => handleOpenBannerModal()}
                    className="bg-[#ef4444] hover:bg-red-700 text-white font-bold px-4 py-2 rounded-lg text-xs flex items-center gap-1.5 cursor-pointer transition shadow"
                  >
                    <Plus className="w-4 h-4" />
                    <span>Adicionar Banner</span>
                  </button>
                </div>

                {/* Banner list */}
                <div className="grid grid-cols-1 gap-4">
                  {banners.map(b => (
                    <div key={b.id} className="bg-zinc-950 border border-zinc-900 p-5 rounded-2xl flex flex-col md:flex-row gap-6 justify-between items-start md:items-center text-left relative overflow-hidden group">
                      <div className="absolute right-0 top-0 bottom-0 w-1/3 bg-linear-to-r from-transparent to-black pointer-events-none opacity-50 block" />
                      
                      <div className="flex gap-5 items-center flex-1 min-w-0">
                        <img 
                          src={b.imageUrl} 
                          alt={b.title} 
                          referrerPolicy="no-referrer"
                          className="w-32 h-20 object-cover rounded-xl border border-zinc-850 shrink-0 shadow-lg"
                        />
                        <div className="flex flex-col text-left min-w-0">
                          <div className="flex items-center gap-2 mb-1">
                            <span className="text-[10px] font-mono text-[#ef4444] font-bold">#{b.order}</span>
                            <span className="text-[9px] font-mono font-extrabold uppercase bg-zinc-900/80 px-2 py-0.5 rounded border border-zinc-800 text-zinc-400">
                              Visível: {b.type === 'subscriber' ? 'Plataforma Assinante' : 'Página Pública'}
                            </span>
                          </div>
                          <h4 className="font-extrabold text-white text-base leading-snug truncate">{b.title}</h4>
                          <p className="text-zinc-450 text-xs truncate max-w-sm mt-0.5 text-zinc-400">{b.subtitle}</p>
                        </div>
                      </div>

                      <div className="flex gap-4 items-center shrink-0 border-t md:border-t-0 border-zinc-900 pt-3 md:pt-0 w-full md:w-auto justify-between md:justify-end z-10">
                        <div className="flex items-center gap-1.5">
                          <span className="text-xs text-zinc-500 font-mono">Disponibilizado:</span>
                          <button
                            onClick={() => handleToggleBannerActive(b.id)}
                            className={`px-3 py-1 rounded text-[10px] font-mono font-black uppercase inline-flex items-center gap-1 cursor-pointer transition ${
                              b.active ? 'bg-emerald-950 text-emerald-400 border border-emerald-900' : 'bg-red-950 text-red-400 border border-red-900'
                            }`}
                          >
                            <CheckCircle className="w-3 h-3" />
                            <span>{b.active ? 'Ativo' : 'Pausado'}</span>
                          </button>
                        </div>

                        <div className="flex gap-1.5 items-center">
                          <button
                            onClick={() => handleOpenBannerModal(b)}
                            className="p-2 bg-zinc-900 hover:bg-zinc-800 border border-zinc-800 rounded-lg text-zinc-300 hover:text-white cursor-pointer"
                            title="Editar Banner"
                          >
                            <Edit2 className="w-4 h-4" />
                          </button>
                          <button
                            onClick={() => handleDeleteBanner(b.id)}
                            className="p-2 bg-zinc-900 hover:bg-rose-950/20 border border-zinc-850 hover:border-rose-900 rounded-lg text-zinc-500 hover:text-red-400 cursor-pointer"
                            title="Remover Banner"
                          >
                            <Trash2 className="w-4 h-4" />
                          </button>
                        </div>
                      </div>
                    </div>
                  ))}
                </div>

                {/* Banner Creation/Edition Modal */}
                {showBannerModal && (
                  <div className="fixed inset-0 bg-black/80 backdrop-blur-xs z-50 flex items-center justify-center p-4">
                    <div className="bg-zinc-950 border border-zinc-850 rounded-xl p-6 max-w-md w-full relative">
                      <button 
                        onClick={() => setShowBannerModal(false)}
                        className="absolute top-4 right-4 text-zinc-400 hover:text-white cursor-pointer"
                      >
                        <X className="w-5 h-5" />
                      </button>

                      <form onSubmit={handleSaveBanner} className="flex flex-col gap-4 text-left text-xs text-white">
                        <div className="border-b border-zinc-900 pb-3">
                          <h3 className="text-base font-black">{editingBanner ? 'Editar Banner' : 'Adicionar Banner'}</h3>
                          <span className="text-[10px] text-zinc-500 font-mono">Banners promocionais em alta definição</span>
                        </div>

                        <div className="flex flex-col gap-1.5">
                          <label className="text-zinc-400 font-bold uppercase font-mono">Título Principal</label>
                          <input
                            type="text"
                            required
                            value={bannerForm.title}
                            onChange={(e) => setBannerForm({ ...bannerForm, title: e.target.value })}
                            placeholder="Ex: Novo Lançamento F5"
                            className="bg-zinc-900 border border-zinc-800 p-2.5 rounded text-white"
                          />
                        </div>

                        <div className="flex flex-col gap-1.5">
                          <label className="text-zinc-400 font-bold uppercase font-mono">Subtítulo / Descrição</label>
                          <input
                            type="text"
                            required
                            value={bannerForm.subtitle}
                            onChange={(e) => setBannerForm({ ...bannerForm, subtitle: e.target.value })}
                            placeholder="Destaques, atores ou data de estreia"
                            className="bg-zinc-900 border border-zinc-800 p-2.5 rounded text-white"
                          />
                        </div>

                        <div className="flex flex-col gap-1.5">
                          <label className="text-zinc-400 font-bold uppercase font-mono">Papel de Parede (URL Unsplash)</label>
                          <input
                            type="text"
                            required
                            value={bannerForm.imageUrl}
                            onChange={(e) => setBannerForm({ ...bannerForm, imageUrl: e.target.value })}
                            className="bg-zinc-900 border border-zinc-800 p-2.5 rounded text-white font-mono text-[11px]"
                          />
                        </div>

                        <div className="grid grid-cols-2 gap-4">
                          <div className="flex flex-col gap-1.5">
                            <label className="text-zinc-400 font-bold uppercase font-mono">Vincular Link Redirecionamento</label>
                            <input
                              type="text"
                              value={bannerForm.linkUrl}
                              onChange={(e) => setBannerForm({ ...bannerForm, linkUrl: e.target.value })}
                              placeholder="Filtro id, # ou URL"
                              className="bg-zinc-900 border border-zinc-805 p-2 rounded text-white font-mono"
                            />
                          </div>

                          <div className="flex flex-col gap-1.5">
                            <label className="text-zinc-400 font-bold uppercase font-mono">Tipo de Audiência</label>
                            <select
                              value={bannerForm.type}
                              onChange={(e) => setBannerForm({ ...bannerForm, type: e.target.value as 'public' | 'subscriber' })}
                              className="bg-zinc-900 border border-zinc-805 p-2 rounded text-white font-bold"
                            >
                              <option value="subscriber">Área do Assinante (Dashboard)</option>
                              <option value="public">Página Inicial Pública</option>
                            </select>
                          </div>
                        </div>

                        <div className="grid grid-cols-2 gap-4">
                          <div className="flex flex-col gap-1.5">
                            <label className="text-zinc-400 font-bold uppercase font-mono">Ordem Sequencial</label>
                            <input
                              type="number"
                              required
                              value={bannerForm.order}
                              onChange={(e) => setBannerForm({ ...bannerForm, order: Number(e.target.value) })}
                              className="bg-zinc-900 border border-zinc-800 p-2 rounded text-white"
                            />
                          </div>

                          <div className="flex flex-col gap-1.5">
                            <label className="text-zinc-400 font-bold uppercase font-mono">Estado Inicial</label>
                            <select
                              value={bannerForm.active ? 'yes' : 'no'}
                              onChange={(e) => setBannerForm({ ...bannerForm, active: e.target.value === 'yes' })}
                              className="bg-zinc-900 border border-zinc-805 p-2 rounded text-white font-bold"
                            >
                              <option value="yes">Ativo imediato</option>
                              <option value="no">Pausado temporariamente</option>
                            </select>
                          </div>
                        </div>

                        <div className="flex gap-2 justify-end border-t border-zinc-900 pt-3 mt-1">
                          <button
                            type="button"
                            onClick={() => setShowBannerModal(false)}
                            className="px-4 py-2 hover:bg-zinc-900 rounded font-bold font-mono text-zinc-400 hover:text-white uppercase"
                          >
                            Voltar
                          </button>
                          <button
                            type="submit"
                            className="px-5 py-2 bg-[#ef4444] hover:bg-red-700 text-white font-bold rounded font-mono uppercase tracking-widest"
                          >
                            Confirmar
                          </button>
                        </div>
                      </form>
                    </div>
                  </div>
                )}
              </section>
            )}

            {/* TAB CONTENT: 12. GRÁFICOS & RELATÓRIOS */}
            {activeTab === 'relatorios' && (
              <section className="flex flex-col gap-6 animate-fade-in text-white text-left">
                <div className="border-b border-zinc-900 pb-5">
                  <span className="text-xs font-mono font-bold text-zinc-500 uppercase">BI & ANALYTICS</span>
                  <h1 className="text-2xl font-black mt-1">BI de Desempenho F5 TV</h1>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                  <div className="bg-zinc-950 border border-zinc-900 p-5 rounded-xl">
                    <span className="text-zinc-500 text-[10px] font-mono tracking-widest uppercase block">FATURAMENTO INTEGRADO</span>
                    <h3 className="text-2xl font-black text-white mt-1">R$ {totalRevenue.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}</h3>
                    <p className="text-[10px] text-zinc-500 font-mono mt-1.5">• Total acumulado e processado no MVP</p>
                  </div>
                  
                  <div className="bg-zinc-950 border border-zinc-900 p-5 rounded-xl">
                    <span className="text-zinc-500 text-[10px] font-mono tracking-widest uppercase block">MENSAL RECORRENTE SIMULADO (MRR)</span>
                    <h3 className="text-2xl font-black text-emerald-400 mt-1">R$ {monthlyRecurrentRevenue.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}</h3>
                    <p className="text-[10px] text-zinc-500 font-mono mt-1.5">• Carteira recorrente projetada localmente</p>
                  </div>

                  <div className="bg-zinc-950 border border-zinc-900 p-5 rounded-xl">
                    <span className="text-zinc-500 text-[10px] font-mono tracking-widest uppercase block">TÍQUETE MÉDIO PROJETADO</span>
                    <h3 className="text-2xl font-black text-white mt-1">
                      R$ {totalSubscribers > 0 ? (monthlyRecurrentRevenue / totalSubscribers).toLocaleString('pt-BR', { minimumFractionDigits: 2 }) : '0,00'}
                    </h3>
                    <p className="text-[10px] text-zinc-500 font-mono mt-1.5">• Margem unitária por conta</p>
                  </div>
                </div>

                {/* Growth and status grids */}
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div className="bg-zinc-950 border border-zinc-900 rounded-xl p-5 flex flex-col gap-4">
                    <h4 className="font-bold text-sm text-zinc-300">Curva Demográfica de Assinaturas</h4>
                    <div className="h-64">
                      <ResponsiveContainer width="100%" height="100%">
                        <AreaChart data={growthData}>
                          <defs>
                            <linearGradient id="colorAssinantesRel" x1="0" y1="0" x2="0" y2="1">
                              <stop offset="5%" stopColor="#ef4444" stopOpacity={0.8}/>
                              <stop offset="95%" stopColor="#ef4444" stopOpacity={0}/>
                            </linearGradient>
                          </defs>
                          <CartesianGrid strokeDasharray="3 3" stroke="#27272a" opacity={0.3} />
                          <XAxis dataKey="name" stroke="#52525b" fontSize={11} />
                          <YAxis stroke="#52525b" fontSize={11} />
                          <Tooltip contentStyle={{ backgroundColor: '#09090b', borderColor: '#27272a' }} />
                          <Area type="monotone" dataKey="Assinantes" stroke="#ef4444" fillOpacity={1} fill="url(#colorAssinantesRel)" />
                        </AreaChart>
                      </ResponsiveContainer>
                    </div>
                  </div>

                  <div className="bg-zinc-950 border border-zinc-900 rounded-xl p-5 flex flex-col gap-4 text-left">
                    <h4 className="font-bold text-sm text-zinc-300">Auditoria de Audiência e Clieques no Player</h4>
                    <div className="flex flex-col gap-2.5 max-h-64 overflow-y-auto pr-1">
                      {contents.slice(0, 6).map((c, i) => (
                        <div key={c.id} className="p-3 bg-zinc-900/40 border border-zinc-850 rounded-lg flex items-center justify-between text-xs">
                          <div className="min-w-0 flex-1 pr-4">
                            <span className="text-[9px] font-mono text-zinc-500">#{i + 1} • {c.genre.toUpperCase()}</span>
                            <h5 className="font-bold text-white truncate">{c.title}</h5>
                          </div>
                          <span className="font-mono text-xs font-black text-emerald-400">{c.viewsCount.toLocaleString('pt-BR')} clicks</span>
                        </div>
                      ))}
                    </div>
                  </div>
                </div>

                {/* Instant dynamic export ledger builder */}
                <div className="bg-zinc-950 border border-zinc-900 p-6 rounded-xl flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 text-left">
                  <div className="flex flex-col">
                    <span className="text-zinc-[#ef4444] font-bold text-xs font-mono uppercase flex items-center gap-1.5">
                      <FileText className="w-4 h-4 text-[#ef4444]" />
                      <span>Montador de Planilhas BI Corporativas</span>
                    </span>
                    <p className="text-zinc-500 text-xs mt-1 leading-normal max-w-xl">
                      Configure e baixe instantaneamente planilhas prontas de auditoria contendo dados consolidados de usuários, assinantes, financeiros e todos os vídeos cadastrados no localStorage do MVP da F5 TV.
                    </p>
                  </div>
                  <button
                    onClick={() => {
                      let csv = '\uFEFF=== FATURAMENTO E RELATORIOS BI ===\n';
                      csv += `Assinantes Ativos,${activeSubs}\n`;
                      csv += `Assinantes Inadimplentes,${pendingSubs}\n`;
                      csv += `Séries Cadastradas,${series.length}\n`;
                      csv += `Capítulos Totais,${episodes.length}\n`;
                      csv += `Faturamento Bruto,R$ ${totalRevenue.toFixed(2)}\n`;
                      csv += `Faturamento Recorrente Mensal (MRR),R$ ${monthlyRecurrentRevenue.toFixed(2)}\n\n`;
                      csv += '=== RANKING DE AUDIÊNCIA ===\n';
                      csv += 'Categoria,Vídeo,Views\n';
                      contents.forEach(c => {
                        csv += `"${c.genre}","${c.title}","${c.viewsCount}"\n`;
                      });

                      const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                      const url = URL.createObjectURL(blob);
                      const link = document.createElement('a');
                      link.setAttribute('href', url);
                      link.setAttribute('download', 'relator_consolidado_analytics_bi_f5_tv.csv');
                      document.body.appendChild(link);
                      link.click();
                      document.body.removeChild(link);
                    }}
                    className="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-4 rounded text-xs uppercase font-mono tracking-widest cursor-pointer transition shrink-0"
                  >
                    Gerar e Baixar CSV Consolidado
                  </button>
                </div>
              </section>
            )}

            {/* TAB CONTENT: 13. CONFIGURAÇÕES DA TV */}
            {activeTab === 'configuracoes' && (
              <section className="flex flex-col gap-6 animate-fade-in text-white text-left">
                <div className="border-b border-zinc-900 pb-5">
                  <span className="text-xs font-mono font-bold text-zinc-500 uppercase">CONFIGURAÇÕES GERAIS</span>
                  <h1 className="text-2xl font-black mt-1">Preferências da Plataforma</h1>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                  <div className="bg-zinc-950 border border-zinc-900 p-6 rounded-xl flex flex-col gap-4">
                    <h4 className="font-bold text-sm text-zinc-300">Visual, Textos e Avisos do Player</h4>
                    
                    <div className="flex flex-col gap-1.5 text-xs">
                      <label className="text-zinc-500 font-bold uppercase font-mono">Nome Oficial do Aplicativo</label>
                      <input
                        type="text"
                        value={platformConfig.appName}
                        onChange={(e) => setPlatformConfig({ ...platformConfig, appName: e.target.value })}
                        className="bg-zinc-900 border border-zinc-800 p-2.5 rounded text-white"
                      />
                    </div>

                    <div className="flex flex-col gap-1.5 text-xs">
                      <label className="text-zinc-500 font-bold uppercase font-mono">Aviso Informativo Global (Footer Banner)</label>
                      <textarea
                        value={platformConfig.warningNotice}
                        rows={3}
                        onChange={(e) => setPlatformConfig({ ...platformConfig, warningNotice: e.target.value })}
                        className="bg-zinc-900 border border-zinc-800 p-2.5 rounded text-white resize-none"
                      />
                    </div>

                    <div className="flex flex-col gap-1.5 text-xs">
                      <label className="text-zinc-500 font-bold uppercase font-mono">Links do Menu Rodapé (Separados por vírgula)</label>
                      <input
                        type="text"
                        value={platformConfig.footerLinks}
                        onChange={(e) => setPlatformConfig({ ...platformConfig, footerLinks: e.target.value })}
                        className="bg-zinc-900 border border-zinc-800 p-2.5 rounded text-white"
                      />
                    </div>
                  </div>

                  <div className="bg-zinc-950 border border-zinc-900 p-6 rounded-xl flex flex-col gap-5 text-left text-xs text-white">
                    <h4 className="font-bold text-sm text-zinc-300">Sandbox, Cobrança & Manutenção</h4>

                    <div className="space-y-4">
                      <div className="flex justify-between items-center bg-zinc-900/40 p-3.5 border border-zinc-850 rounded-xl">
                        <div className="flex flex-col">
                          <span className="font-bold text-zinc-200">Simulador de Transações Pix/Cartão</span>
                          <span className="text-[10px] text-zinc-500 mt-0.5">Define se novas assinaturas de teste faturam com sucesso</span>
                        </div>
                        <button
                          onClick={() => setPlatformConfig({ ...platformConfig, paymentMockSuccess: !platformConfig.paymentMockSuccess })}
                          className={`px-3 py-1.5 rounded font-mono font-bold text-[10px] uppercase cursor-pointer ${
                            platformConfig.paymentMockSuccess ? 'bg-emerald-950 text-emerald-400 border border-emerald-900' : 'bg-red-950 text-red-500 border border-red-900'
                          }`}
                        >
                          {platformConfig.paymentMockSuccess ? 'CONFIRMA AUTOMÁTICA_SIM' : 'FALHA AUTOMÁTICA_NÃO'}
                        </button>
                      </div>

                      <div className="flex justify-between items-center bg-zinc-900/40 p-3.5 border border-zinc-850 rounded-xl">
                        <div className="flex flex-col">
                          <span className="font-bold text-zinc-200">Modo de Manutenção Estrito</span>
                          <span className="text-[10px] text-zinc-500 mt-0.5">Simula indisponibilidade para assinantes normais</span>
                        </div>
                        <button
                          onClick={() => setPlatformConfig({ ...platformConfig, maintenanceMode: !platformConfig.maintenanceMode })}
                          className={`px-3 py-1.5 rounded font-mono font-bold text-[10px] uppercase cursor-pointer ${
                            platformConfig.maintenanceMode ? 'bg-amber-950 text-amber-500 border border-amber-900' : 'bg-zinc-900 text-zinc-500 border border-zinc-800'
                          }`}
                        >
                          {platformConfig.maintenanceMode ? 'ATIVADO (MANUTENÇÃO_SIM)' : 'DESATIVADO (NO AR)'}
                        </button>
                      </div>
                    </div>

                    <button
                      onClick={() => {
                        alert('Configurações de sistema da F5 TV guardadas localmente no MVP com sucesso!');
                      }}
                      className="w-full bg-[#ef4444] hover:bg-red-700 py-3 rounded-lg text-xs font-mono font-bold uppercase tracking-wider transition cursor-pointer"
                    >
                      Salvar Definições de Plataforma
                    </button>
                  </div>
                </div>
              </section>
            )}

            {activeTab === 'programacao' && (
              <AdminProgramacao />
            )}

            {activeTab === 'canais' && (
              <AdminCanais />
            )}

            {activeTab === 'cupons' && (
              <AdminCupons />
            )}

            {activeTab === 'avaliacoes' && (
              <AdminAvaliacoes />
            )}

            {activeTab === 'midia' && (
              <AdminMidia />
            )}

          </>
        )}

      </main>

      {/* POPUP MODAL: USER CADASTRO/EDIÇÃO */}
      {showUserModal && (
        <div id="user-maintenance-modal" className="fixed inset-0 bg-black/80 backdrop-blur-xs z-50 flex items-center justify-center p-4 overflow-y-auto select-none font-sans text-white">
          <div className="bg-zinc-950 border border-zinc-850 rounded-2xl w-full max-w-md overflow-hidden shadow-2xl relative p-6">
            
            <button 
              onClick={() => setShowUserModal(false)}
              className="absolute top-4 right-4 p-1.5 hover:bg-zinc-900 text-zinc-500 hover:text-white rounded-full transition cursor-pointer"
            >
              <X className="w-5 h-5" />
            </button>

            <form onSubmit={handleUserFormSubmit} className="flex flex-col gap-4 text-left">
              <div className="border-b border-zinc-900 pb-4">
                <h3 className="text-lg font-black tracking-tight">{editingUser ? 'Editar Cadastro Corporativo' : 'Adicionar Novo Usuário'}</h3>
                <span className="text-zinc-500 text-[11px]">Campos de controle de permissões e cobrança do assinante</span>
              </div>

              <div className="flex flex-col gap-1 text-xs">
                <label className="text-zinc-400 font-bold uppercase font-mono">Nome completo</label>
                <input
                  id="user-form-name"
                  type="text"
                  required
                  value={userForm.name}
                  onChange={(e) => setUserForm({ ...userForm, name: e.target.value })}
                  placeholder="Nome completo do assinante"
                  className="bg-zinc-900 border border-zinc-800 p-2.5 rounded outline-none text-zinc-100"
                />
              </div>

              <div className="flex flex-col gap-1 text-xs">
                <label className="text-zinc-400 font-bold uppercase font-mono">E-mail de Login</label>
                <input
                  id="user-form-email"
                  type="email"
                  required
                  value={userForm.email}
                  disabled={!!editingUser}
                  onChange={(e) => setUserForm({ ...userForm, email: e.target.value })}
                  placeholder="usuario@f5tv.com.br"
                  className="bg-zinc-950 border border-zinc-850 p-2.5 rounded outline-none text-zinc-400 disabled:opacity-60"
                />
              </div>

              <div className="grid grid-cols-2 gap-4">
                <div className="flex flex-col gap-1 text-xs">
                  <label className="text-zinc-400 font-bold uppercase font-mono">Cargo / Hierarquia</label>
                  <select
                    id="user-form-role"
                    value={userForm.role}
                    onChange={(e) => setUserForm({ ...userForm, role: e.target.value as UserRole })}
                    className="bg-zinc-900 border border-zinc-800 p-2 rounded outline-none"
                  >
                    <option value="subscriber">Assinante</option>
                    <option value="editor">Editor de Conteúdo</option>
                    <option value="finance">Financeiro</option>
                    <option value="admin">Administrador Geral</option>
                  </select>
                </div>

                <div className="flex flex-col gap-1 text-xs">
                  <label className="text-zinc-400 font-bold uppercase font-mono">Estado da Conta</label>
                  <select
                    id="user-form-status"
                    value={userForm.status}
                    onChange={(e) => setUserForm({ ...userForm, status: e.target.value as UserStatus })}
                    className="bg-zinc-900 border border-zinc-800 p-2 rounded outline-none"
                  >
                    <option value="active">Active (Liberado)</option>
                    <option value="blocked">Blocked (Bloqueado)</option>
                    <option value="pending">Pending (Atrasado)</option>
                  </select>
                </div>
              </div>

              {userForm.role === 'subscriber' && (
                <div className="flex flex-col gap-1 text-xs border-t border-zinc-900 pt-3">
                  <label className="text-zinc-400 font-bold uppercase font-mono text-[10px]">Vincular Plano F5 TV</label>
                  <select
                    id="user-form-plan"
                    value={userForm.planId}
                    onChange={(e) => setUserForm({ ...userForm, planId: e.target.value })}
                    className="bg-zinc-900 border border-zinc-800 p-2 rounded outline-none mt-1"
                  >
                    {plans.map(p => (
                      <option key={p.id} value={p.id}>{p.name} (R$ {p.price.toFixed(2)}/mês)</option>
                    ))}
                  </select>
                </div>
              )}

              <div className="flex gap-2 justify-end border-t border-zinc-900 pt-4 mt-2">
                <button
                  type="button"
                  onClick={() => setShowUserModal(false)}
                  className="bg-zinc-900 hover:bg-zinc-805 text-zinc-400 font-bold py-2 px-4 rounded text-xs cursor-pointer"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  className="bg-[#ef4444] hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-xs cursor-pointer"
                >
                  Guardar Cadastro
                </button>
              </div>
            </form>

          </div>
        </div>
      )}

      {/* POPUP MODAL: FILME/VÍDEO CADASTRO/EDIÇÃO */}
      {showContentModal && (
        <div id="content-maintenance-modal" className="fixed inset-0 bg-black/80 backdrop-blur-xs z-50 flex items-center justify-center p-4 overflow-y-auto select-none font-sans text-white">
          <div className="bg-zinc-950 border border-zinc-850 rounded-2xl w-full max-w-2xl overflow-hidden shadow-2xl relative p-6 my-8">
            
            <button 
              onClick={() => setShowContentModal(false)}
              className="absolute top-4 right-4 p-1.5 hover:bg-zinc-900 text-zinc-500 hover:text-white rounded-full transition cursor-pointer"
            >
              <X className="w-5 h-5" />
            </button>

            <form onSubmit={handleContentFormSubmit} className="flex flex-col gap-4 text-left">
              <div className="border-b border-zinc-900 pb-4">
                <h3 className="text-lg font-black tracking-tight">{editingContent ? 'Editar Mídia do Catálogo' : 'Adicionar Conteúdo de TV'}</h3>
                <span className="text-zinc-500 text-[11px]">Preencha os metadados técnicos do filme, show ou notícia</span>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div className="flex flex-col gap-1 text-xs">
                  <label className="text-zinc-400 font-bold uppercase font-mono">Título do Filme / Programa</label>
                  <input
                    id="content-form-title"
                    type="text"
                    required
                    value={contentForm.title}
                    onChange={(e) => setContentForm({ ...contentForm, title: e.target.value })}
                    placeholder="Ex: Vozes do Brasil Ep 3"
                    className="bg-zinc-900 border border-zinc-800 p-2 rounded text-zinc-100"
                  />
                </div>

                <div className="flex flex-col gap-1 text-xs">
                  <label className="text-zinc-400 font-bold uppercase font-mono">Tipo de Conteúdo</label>
                  <select
                    id="content-form-type"
                    value={contentForm.type}
                    onChange={(e) => setContentForm({ ...contentForm, type: e.target.value as ContentType })}
                    className="bg-zinc-900 border border-zinc-800 p-2 rounded"
                  >
                    <option value="movie">Filme Independente</option>
                    <option value="series">Série Multi-episódio</option>
                    <option value="news">Noticiário / Jornalismo</option>
                    <option value="tv_show">Programa de Estúdio / Show</option>
                    <option value="sports">Transmissão de Esportes</option>
                    <option value="documentary">Documentário</option>
                    <option value="special">Especial F5 TV</option>
                  </select>
                </div>
              </div>

              <div className="grid grid-cols-2 gap-4">
                <div className="flex flex-col gap-1 text-xs">
                  <label className="text-zinc-400 font-bold uppercase font-mono">Gênero Curto</label>
                  <input
                    id="content-form-genre"
                    type="text"
                    required
                    value={contentForm.genre}
                    onChange={(e) => setContentForm({ ...contentForm, genre: e.target.value })}
                    placeholder="Ex: Ação, Jornalismo Tático"
                    className="bg-zinc-900 border border-zinc-800 p-2 rounded text-blue-200"
                  />
                </div>

                <div className="flex flex-col gap-1 text-xs">
                  <label className="text-zinc-400 font-bold uppercase font-mono">Categoria Associada</label>
                  <select
                    id="content-form-category"
                    value={contentForm.categoryId}
                    onChange={(e) => setContentForm({ ...contentForm, categoryId: e.target.value })}
                    className="bg-zinc-900 border border-zinc-800 p-2 rounded"
                  >
                    {categories.map(cat => (
                      <option key={cat.id} value={cat.id}>{cat.name}</option>
                    ))}
                  </select>
                </div>
              </div>

              <div className="grid grid-cols-3 gap-4">
                <div className="flex flex-col gap-1 text-xs">
                  <label className="text-zinc-400 font-bold uppercase font-mono">Classificação Etária</label>
                  <select
                    id="content-form-rating"
                    value={contentForm.ageRating}
                    onChange={(e) => setContentForm({ ...contentForm, ageRating: e.target.value as any })}
                    className="bg-zinc-900 border border-zinc-800 p-2 rounded"
                  >
                    <option value="L">Livre (L)</option>
                    <option value="10">Classificação 10</option>
                    <option value="12">Classificação 12</option>
                    <option value="14">Classificação 14</option>
                    <option value="16">Classificação 16</option>
                    <option value="18">Classificação 18</option>
                  </select>
                </div>

                <div className="flex flex-col gap-1 text-xs">
                  <label className="text-zinc-400 font-bold uppercase font-mono">Ano Lançamento</label>
                  <input
                    id="content-form-year"
                    type="number"
                    required
                    value={contentForm.year}
                    onChange={(e) => setContentForm({ ...contentForm, year: Number(e.target.value) })}
                    className="bg-zinc-900 border border-zinc-800 p-2 rounded"
                  />
                </div>

                <div className="flex flex-col gap-1 text-xs">
                  <label className="text-zinc-400 font-bold uppercase font-mono">Duração Estimada</label>
                  <input
                    id="content-form-duration"
                    type="text"
                    required
                    value={contentForm.duration}
                    onChange={(e) => setContentForm({ ...contentForm, duration: e.target.value })}
                    placeholder="Ex: 1h 45m ou 45m"
                    className="bg-zinc-900 border border-zinc-800 p-2 rounded"
                  />
                </div>
              </div>

              <div className="flex flex-col gap-1.5 text-xs">
                <label className="text-zinc-400 font-bold uppercase font-mono">Descrição Curta (Banner Home)</label>
                <textarea
                  id="content-form-short-desc"
                  rows={2}
                  required
                  value={contentForm.shortDescription}
                  onChange={(e) => setContentForm({ ...contentForm, shortDescription: e.target.value })}
                  placeholder="Sinopse curta para a primeira linha da plataforma..."
                  className="bg-zinc-900 border border-zinc-800 p-2.5 rounded outline-none h-14"
                />
              </div>

              <div className="flex flex-col gap-1.5 text-xs">
                <label className="text-zinc-400 font-bold uppercase font-mono">Descrição Completa</label>
                <textarea
                  id="content-form-full-desc"
                  rows={3}
                  required
                  value={contentForm.fullDescription}
                  onChange={(e) => setContentForm({ ...contentForm, fullDescription: e.target.value })}
                  placeholder="Sinopse completa com detalhes da trama ou da grade..."
                  className="bg-zinc-900 border border-zinc-800 p-2.5 rounded outline-none h-20"
                />
              </div>

              <div className="grid grid-cols-2 gap-4">
                <div className="flex flex-col gap-1 text-xs">
                  <label className="text-zinc-400 font-bold uppercase font-mono">Apresentadores / Diretores (S. por vírgula)</label>
                  <input
                    id="content-form-directors"
                    type="text"
                    required
                    value={contentForm.directors}
                    onChange={(e) => setContentForm({ ...contentForm, directors: e.target.value })}
                    placeholder="Gabriel Silva, Karina Lemes"
                    className="bg-zinc-900 border border-zinc-800 p-2 rounded"
                  />
                </div>

                <div className="flex flex-col gap-1 text-xs">
                  <label className="text-zinc-400 font-bold uppercase font-mono">Elenco principal (Split por vírgula)</label>
                  <input
                    id="content-form-cast"
                    type="text"
                    required
                    value={contentForm.cast}
                    onChange={(e) => setContentForm({ ...contentForm, cast: e.target.value })}
                    placeholder="Renata Albuquerque, Sandro Vieira"
                    className="bg-zinc-900 border border-zinc-800 p-2 rounded"
                  />
                </div>
              </div>

              <div className="grid grid-cols-2 gap-4">
                <div className="flex flex-col gap-1 text-xs">
                  <label className="text-zinc-400 font-bold uppercase font-mono">Capa Vertical URL</label>
                  <input
                    id="content-form-cover"
                    type="text"
                    required
                    value={contentForm.coverUrl}
                    onChange={(e) => setContentForm({ ...contentForm, coverUrl: e.target.value })}
                    className="bg-zinc-900 border border-zinc-800 p-2 rounded font-mono text-[11px]"
                  />
                </div>

                <div className="flex flex-col gap-1 text-[11px]">
                  <label className="text-zinc-400 font-bold uppercase font-mono">Banner Amplo URL</label>
                  <input
                    id="content-form-banner"
                    type="text"
                    required
                    value={contentForm.bannerUrl}
                    onChange={(e) => setContentForm({ ...contentForm, bannerUrl: e.target.value })}
                    className="bg-zinc-900 border border-zinc-800 p-2 rounded font-mono text-[11px]"
                  />
                </div>
              </div>

              <div className="grid grid-cols-3 gap-6 border-t border-zinc-900 pt-3">
                <label className="flex items-center gap-2 cursor-pointer font-bold font-mono text-xs">
                  <input 
                    type="checkbox" 
                    checked={contentForm.isExclusive} 
                    onChange={(e)=>setContentForm({...contentForm, isExclusive: e.target.checked})} 
                    className="accent-[#ef4444]"
                  />
                  <span>Exclusivo Assinantes</span>
                </label>

                <label className="flex items-center gap-2 cursor-pointer font-bold font-mono text-xs">
                  <input 
                    type="checkbox" 
                    checked={contentForm.isFeatured} 
                    onChange={(e)=>setContentForm({...contentForm, isFeatured: e.target.checked})} 
                    className="accent-[#ef4444]"
                  />
                  <span>Destaque Carousel Banner</span>
                </label>

                <label className="flex items-center gap-2 cursor-pointer font-bold font-mono text-xs">
                  <input 
                    type="checkbox" 
                    checked={contentForm.isFree} 
                    onChange={(e)=>setContentForm({...contentForm, isFree: e.target.checked})} 
                    className="accent-[#ef4444]"
                  />
                  <span>Gratuito Livre</span>
                </label>
              </div>

              <div className="flex gap-2 justify-end border-t border-zinc-900 pt-4 mt-2">
                <button
                  type="button"
                  onClick={() => setShowContentModal(false)}
                  className="bg-zinc-900 hover:bg-zinc-805 text-zinc-400 font-bold py-2 px-4 rounded text-xs cursor-pointer"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  className="bg-[#ef4444] hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-xs cursor-pointer"
                >
                  Confirmar Cadastro
                </button>
              </div>
            </form>

          </div>
        </div>
      )}

      {/* POPUP MODAL: NOVA SÉRIE CADASTRAR */}
      {showSeriesModal && (
        <div className="fixed inset-0 bg-black/80 backdrop-blur-xs z-50 flex items-center justify-center p-4 overflow-y-auto select-none font-sans text-white">
          <div className="bg-zinc-950 border border-zinc-850 rounded-2xl w-full max-w-md overflow-hidden shadow-2xl relative p-6">
            
            <button onClick={() => setShowSeriesModal(false)} className="absolute top-4 right-4 text-zinc-500 hover:text-white cursor-pointer"><X className="w-5 h-5" /></button>

            <form onSubmit={handleCreateSeriesSubmit} className="flex flex-col gap-4 text-left">
              <div className="border-b border-zinc-900 pb-3 mb-1">
                <h3 className="text-base font-bold font-sans flex items-center gap-1.5 text-zinc-100">
                  <FolderPlus className="text-[#ef4444]" />
                  <span>Cadastrar Nova Série F5 TV</span>
                </h3>
              </div>

              <div className="flex flex-col gap-1 text-xs">
                <label className="text-zinc-400 font-bold uppercase font-mono">Título Geral da Série</label>
                <input
                  type="text"
                  required
                  value={seriesForm.title}
                  onChange={(e) => setSeriesForm({ ...seriesForm, title: e.target.value })}
                  placeholder="Ex: Conexão F5"
                  className="bg-zinc-900 border border-zinc-800 p-2 rounded text-zinc-100 placeholder:text-zinc-600 outline-none"
                />
              </div>

              <div className="flex flex-col gap-1 text-xs">
                <label className="text-zinc-400 font-bold uppercase font-mono">Gênero Curto</label>
                <input
                  type="text"
                  required
                  value={seriesForm.genre}
                  onChange={(e) => setSeriesForm({ ...seriesForm, genre: e.target.value })}
                  placeholder="Ex: Jornalismo Investigativo"
                  className="bg-zinc-900 border border-zinc-800 p-2 rounded text-zinc-100 outline-none"
                />
              </div>

              <div className="flex flex-col gap-1 text-xs">
                <label className="text-zinc-400 font-bold uppercase font-mono">Descrição Geral da Série</label>
                <textarea
                  required
                  rows={2}
                  value={seriesForm.description}
                  onChange={(e) => setSeriesForm({ ...seriesForm, description: e.target.value })}
                  placeholder="Breve sumário dos episódios reunidos..."
                  className="bg-zinc-900 border border-zinc-800 p-2 rounded text-zinc-150 outline-none"
                />
              </div>

              <div className="flex flex-col gap-1 text-xs">
                <label className="text-zinc-400 font-bold uppercase font-mono">Capa Vertical URL</label>
                <input
                  type="text"
                  value={seriesForm.coverUrl}
                  onChange={(e) => setSeriesForm({ ...seriesForm, coverUrl: e.target.value })}
                  placeholder="Cole ou deixe em branco para simular"
                  className="bg-zinc-900 border border-zinc-800 p-2 rounded text-zinc-300 font-mono text-[10px]"
                />
              </div>

              <div className="flex flex-col gap-1 text-xs">
                <label className="text-zinc-400 font-bold uppercase font-mono">Banner Horizontal URL</label>
                <input
                  type="text"
                  value={seriesForm.bannerUrl}
                  onChange={(e) => setSeriesForm({ ...seriesForm, bannerUrl: e.target.value })}
                  placeholder="Cole ou deixe em branco para simular"
                  className="bg-zinc-900 border border-zinc-800 p-2 rounded text-zinc-300 font-mono text-[10px]"
                />
              </div>

              <div className="flex gap-2 justify-end pt-3 text-xs border-t border-zinc-900">
                <button type="button" onClick={() => setShowSeriesModal(false)} className="bg-zinc-900 hover:bg-zinc-805 p-2 rounded">Cancelar</button>
                <button type="submit" className="bg-[#ef4444] hover:bg-red-700 text-white font-bold py-2 px-3 rounded">Confirmar Série</button>
              </div>
            </form>
          </div>
        </div>
      )}

      {/* POPUP MODAL: NOVO EPISÓDIO CADASTRAR */}
      {showSeriesEpisodeModal && (
        <div className="fixed inset-0 bg-black/80 backdrop-blur-xs z-50 flex items-center justify-center p-4 overflow-y-auto select-none font-sans text-white">
          <div className="bg-zinc-950 border border-zinc-850 rounded-2xl w-full max-w-md overflow-hidden shadow-2xl relative p-6 my-8">
            
            <button onClick={() => setShowSeriesEpisodeModal(false)} className="absolute top-4 right-4 text-zinc-500 hover:text-white cursor-pointer"><X className="w-5 h-5" /></button>

            <form onSubmit={handleCreateEpisodeSubmit} className="flex flex-col gap-4 text-left">
              <div className="border-b border-zinc-900 pb-3 mb-1">
                <h3 className="text-base font-bold font-sans flex items-center gap-1.5 text-zinc-100">
                  <Plus className="text-[#ef4444]" />
                  <span>Cadastrar Capítulo ou Episódio</span>
                </h3>
              </div>

              <div className="flex flex-col gap-1 text-xs">
                <label className="text-zinc-400 font-bold uppercase font-mono">Vincular à Série</label>
                <select
                  value={selectedSeriesIdForEpisode}
                  onChange={(e)=>setSelectedSeriesIdForEpisode(e.target.value)}
                  className="bg-zinc-900 border border-zinc-800 p-2 rounded"
                >
                  {series.map(s => <option key={s.id} value={s.id}>{s.title}</option>)}
                </select>
              </div>

              <div className="grid grid-cols-3 gap-3">
                <div className="col-span-2 flex flex-col gap-1 text-xs">
                  <label className="text-zinc-400 font-bold uppercase font-mono">Nome do Episódio</label>
                  <input
                    type="text"
                    required
                    value={seriesEpisodeForm.title}
                    onChange={(e) => setSeriesEpisodeForm({ ...seriesEpisodeForm, title: e.target.value })}
                    placeholder="Ex: O Caso Sequestro Digital"
                    className="bg-zinc-900 border border-zinc-800 p-2 rounded"
                  />
                </div>

                <div className="flex flex-col gap-1 text-xs">
                  <label className="text-zinc-400 font-bold uppercase font-mono">Ep. Número</label>
                  <input
                    type="number"
                    required
                    value={seriesEpisodeForm.number}
                    onChange={(e) => setSeriesEpisodeForm({ ...seriesEpisodeForm, number: Number(e.target.value) })}
                    className="bg-zinc-900 border border-zinc-800 p-2 rounded text-center"
                  />
                </div>
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div className="flex flex-col gap-1 text-xs">
                  <label className="text-zinc-400 font-bold uppercase font-mono">Duração Estimada</label>
                  <input
                    type="text"
                    required
                    value={seriesEpisodeForm.duration}
                    onChange={(e) => setSeriesEpisodeForm({ ...seriesEpisodeForm, duration: e.target.value })}
                    placeholder="Ex: 45m"
                    className="bg-zinc-900 border border-zinc-800 p-2 rounded"
                  />
                </div>

                <div className="flex flex-col gap-1 text-xs">
                  <label className="text-zinc-400 font-bold uppercase font-mono">Versão Vídeo URL (Simulado)</label>
                  <input
                    type="text"
                    value={seriesEpisodeForm.videoUrl || ''}
                    onChange={(e) => setSeriesEpisodeForm({ ...seriesEpisodeForm, videoUrl: e.target.value })}
                    placeholder="Cole mp4 ou deixe em branco"
                    className="bg-zinc-900 border border-zinc-800 p-2 rounded text-[10px] font-mono"
                  />
                </div>
              </div>

              <div className="flex flex-col gap-1 text-xs">
                <label className="text-zinc-400 font-bold uppercase font-mono">Breve Sinopse do Episódio</label>
                <textarea
                  required
                  rows={2}
                  value={seriesEpisodeForm.description}
                  onChange={(e) => setSeriesEpisodeForm({ ...seriesEpisodeForm, description: e.target.value })}
                  placeholder="Resumo do enredo do episódio corrente..."
                  className="bg-zinc-900 border border-zinc-800 p-2 rounded"
                />
              </div>

              <div className="flex flex-col gap-1 text-xs">
                <label className="text-zinc-400 font-bold uppercase font-mono">Thumbnail de Capa Macia URL</label>
                <input
                  type="text"
                  value={seriesEpisodeForm.thumbnailUrl || ''}
                  onChange={(e) => setSeriesEpisodeForm({ ...seriesEpisodeForm, thumbnailUrl: e.target.value })}
                  placeholder="Cole ou deixe em branco para simulação"
                  className="bg-zinc-900 border border-zinc-800 p-2 rounded text-[10px] font-mono"
                />
              </div>

              <div className="flex gap-2 justify-end pt-3 text-xs border-t border-zinc-900">
                <button type="button" onClick={() => setShowSeriesEpisodeModal(false)} className="bg-zinc-900 hover:bg-zinc-805 p-2 rounded">Cancelar</button>
                <button type="submit" className="bg-[#ef4444] hover:bg-red-700 text-white font-bold py-2 px-3 rounded">Confirmar Capítulo</button>
              </div>
            </form>
          </div>
        </div>
      )}

    </div>
  );
}

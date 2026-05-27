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
  DollarSign, BarChart2, ListCollapse, Play, Sparkles, FolderPlus, Download, Check, X, LogOut, Search 
} from 'lucide-react';

interface AdminPanelProps {
  currentUser: User;
  onLogout: () => void;
  onNavigateToUserApp: () => void;
  activeTabOverride?: 'dashboard' | 'usuarios' | 'conteudo' | 'series' | 'uploads' | 'financeiro' | 'planos';
  onTabChange?: (tab: 'dashboard' | 'usuarios' | 'conteudo' | 'series' | 'uploads' | 'financeiro' | 'planos') => void;
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
  const [activeTab, setActiveTabInternal] = useState<'dashboard' | 'usuarios' | 'conteudo' | 'series' | 'uploads' | 'financeiro' | 'planos'>(activeTabOverride || 'dashboard');

  useEffect(() => {
    if (activeTabOverride && activeTabOverride !== activeTab) {
      setActiveTabInternal(activeTabOverride);
    }
  }, [activeTabOverride]);

  const setActiveTab = (tab: 'dashboard' | 'usuarios' | 'conteudo' | 'series' | 'uploads' | 'financeiro' | 'planos') => {
    setActiveTabInternal(tab);
    if (onTabChange) {
      onTabChange(tab);
    }
  };

  // CRM Filters States
  const [crmSearch, setCrmSearch] = useState('');
  const [crmPlanFilter, setCrmPlanFilter] = useState('all');
  const [crmStatusFilter, setCrmStatusFilter] = useState('all');

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
  const [showEpisodeModal, setShowEpisodeModal] = useState(false);
  const [selectedSeriesIdForEpisode, setSelectedSeriesIdForEpisode] = useState('');
  const [episodeForm, setEpisodeForm] = useState({ title: '', number: 1, seasonId: '', description: '', duration: '45m', thumbnailUrl: '', videoUrl: '' });

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
    if (currentUser.role === 'editor' && ['dashboard', 'conteudo', 'series', 'uploads'].includes(tab)) return true;
    if (currentUser.role === 'finance' && ['dashboard', 'financeiro', 'planos'].includes(tab)) return true;
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

  // Filtered Users CRM list
  const filteredUsers = users.filter((u) => {
    const matchesSearch = crmSearch 
      ? u.name.toLowerCase().includes(crmSearch.toLowerCase()) || 
        u.email.toLowerCase().includes(crmSearch.toLowerCase()) ||
        (u.phone && u.phone.includes(crmSearch))
      : true;

    const matchesPlan = crmPlanFilter === 'all'
      ? true
      : u.planId === crmPlanFilter;

    const matchesStatus = crmStatusFilter === 'all'
      ? true
      : u.status === crmStatusFilter;

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
      number: Number(episodeForm.number),
      title: episodeForm.title,
      description: episodeForm.description,
      duration: episodeForm.duration,
      videoUrl: episodeForm.videoUrl || 'https://assets.mixkit.co/videos/preview/mixkit-software-developer-working-on-his-computer-34289-large.mp4',
      thumbnailUrl: episodeForm.thumbnailUrl || 'https://images.unsplash.com/photo-1563986768609-322da13575f3?q=80&w=450',
      status: 'published',
      viewsCount: 0
    };

    db.setEpisodes([...episodes, newEp]);
    setShowEpisodeModal(false);
    setEpisodeForm({ title: '', number: 1, seasonId: '', description: '', duration: '45m', thumbnailUrl: '', videoUrl: '' });
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

          <div className="flex items-center gap-2.5 p-3 bg-zinc-900 border border-zinc-850 rounded-xl relative">
            <div className="w-8 h-8 rounded-full bg-red-650 flex items-center justify-center font-bold text-sm text-white shrink-0">
              {currentUser.name.charAt(0)}
            </div>
            <div className="flex flex-col">
              <span className="font-bold text-xs truncate leading-snug">{currentUser.name}</span>
              <span className="text-[10px] text-zinc-500 font-mono uppercase tracking-wider">{currentUser.role}</span>
            </div>
          </div>

          {/* Nav Items */}
          <nav className="flex flex-col gap-1 text-sm font-medium">
            <button 
              id="tab-dashboard"
              onClick={() => setActiveTab('dashboard')}
              className={`flex items-center gap-3 px-4 py-3 rounded-lg text-left transition ${
                activeTab === 'dashboard' ? 'bg-[#ef4444] text-white shadow-md shadow-red-900/10' : 'text-zinc-400 hover:text-white hover:bg-zinc-900'
              }`}
            >
              <BarChart2 className="w-4 h-4" />
              <span>Dashboard Geral</span>
            </button>

            <button 
              id="tab-usuarios"
              onClick={() => setActiveTab('usuarios')}
              className={`flex items-center justify-between px-4 py-3 rounded-lg text-left transition ${
                activeTab === 'usuarios' ? 'bg-[#ef4444] text-white shadow' : 'text-zinc-400 hover:text-white hover:bg-zinc-900'
              }`}
            >
              <div className="flex items-center gap-3">
                <Users className="w-4 h-4" />
                <span>Assinantes & CRM</span>
              </div>
              {!canAccess('usuarios') && <span className="text-[9px] font-mono text-zinc-650 uppercase">Bloq</span>}
            </button>

            <button 
              id="tab-conteudo"
              onClick={() => setActiveTab('conteudo')}
              className={`flex items-center justify-between px-4 py-3 rounded-lg text-left transition ${
                activeTab === 'conteudo' ? 'bg-[#ef4444] text-white shadow' : 'text-zinc-400 hover:text-white hover:bg-zinc-900'
              }`}
            >
              <div className="flex items-center gap-3">
                <Film className="w-4 h-4" />
                <span>Vídeos e Programas</span>
              </div>
              {!canAccess('conteudo') && <span className="text-[9px] font-mono text-zinc-650 uppercase">Bloq</span>}
            </button>

            <button 
              id="tab-series"
              onClick={() => setActiveTab('series')}
              className={`flex items-center justify-between px-4 py-3 rounded-lg text-left transition ${
                activeTab === 'series' ? 'bg-[#ef4444] text-white shadow' : 'text-zinc-400 hover:text-white hover:bg-zinc-900'
              }`}
            >
              <div className="flex items-center gap-3">
                <ListCollapse className="w-4 h-4" />
                <span>Séries e Capítulos</span>
              </div>
              {!canAccess('series') && <span className="text-[9px] font-mono text-zinc-650 uppercase">Bloq</span>}
            </button>

            <button 
              id="tab-uploads"
              onClick={() => setActiveTab('uploads')}
              className={`flex items-center justify-between px-4 py-3 rounded-lg text-left transition ${
                activeTab === 'uploads' ? 'bg-[#ef4444] text-white shadow' : 'text-zinc-400 hover:text-white hover:bg-zinc-900'
              }`}
            >
              <div className="flex items-center gap-3">
                <UploadCloud className="w-4 h-4" />
                <span>Upload de Mídia</span>
              </div>
              {!canAccess('uploads') && <span className="text-[9px] font-mono text-zinc-650 uppercase">Bloq</span>}
            </button>

            <button 
              id="tab-financeiro"
              onClick={() => setActiveTab('financeiro')}
              className={`flex items-center justify-between px-4 py-3 rounded-lg text-left transition ${
                activeTab === 'financeiro' ? 'bg-[#ef4444] text-white shadow' : 'text-zinc-400 hover:text-white hover:bg-zinc-900'
              }`}
            >
              <div className="flex items-center gap-3">
                <CreditCard className="w-4 h-4" />
                <span>Contabilidade & Caixa</span>
              </div>
              {!canAccess('financeiro') && <span className="text-[9px] font-mono text-zinc-650 uppercase">Bloq</span>}
            </button>

            <button 
              id="tab-planos"
              onClick={() => setActiveTab('planos')}
              className={`flex items-center justify-between px-4 py-3 rounded-lg text-left transition ${
                activeTab === 'planos' ? 'bg-[#ef4444] text-white shadow' : 'text-zinc-400 hover:text-white hover:bg-zinc-900'
              }`}
            >
              <div className="flex items-center gap-3">
                <Settings className="w-4 h-4" />
                <span>Configurar Planos</span>
              </div>
              {!canAccess('planos') && <span className="text-[9px] font-mono text-zinc-650 uppercase">Bloq</span>}
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
                        setShowEpisodeModal(true);
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
              <section className="flex flex-col gap-6 animate-fade-in">
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
                            <span key={i} className="text-xs text-zinc-455 text-zinc-400 flex gap-1.5 items-start">
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
      {showEpisodeModal && (
        <div className="fixed inset-0 bg-black/80 backdrop-blur-xs z-50 flex items-center justify-center p-4 overflow-y-auto select-none font-sans text-white">
          <div className="bg-zinc-950 border border-zinc-850 rounded-2xl w-full max-w-md overflow-hidden shadow-2xl relative p-6 my-8">
            
            <button onClick={() => setShowEpisodeModal(false)} className="absolute top-4 right-4 text-zinc-500 hover:text-white cursor-pointer"><X className="w-5 h-5" /></button>

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
                    value={episodeForm.title}
                    onChange={(e) => setEpisodeForm({ ...episodeForm, title: e.target.value })}
                    placeholder="Ex: O Caso Sequestro Digital"
                    className="bg-zinc-900 border border-zinc-800 p-2 rounded"
                  />
                </div>

                <div className="flex flex-col gap-1 text-xs">
                  <label className="text-zinc-400 font-bold uppercase font-mono">Ep. Número</label>
                  <input
                    type="number"
                    required
                    value={episodeForm.number}
                    onChange={(e) => setEpisodeForm({ ...episodeForm, number: Number(e.target.value) })}
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
                    value={episodeForm.duration}
                    onChange={(e) => setEpisodeForm({ ...episodeForm, duration: e.target.value })}
                    placeholder="Ex: 45m"
                    className="bg-zinc-900 border border-zinc-800 p-2 rounded"
                  />
                </div>

                <div className="flex flex-col gap-1 text-xs">
                  <label className="text-zinc-400 font-bold uppercase font-mono">Versão Vídeo URL (Simulado)</label>
                  <input
                    type="text"
                    value={episodeForm.videoUrl}
                    onChange={(e) => setEpisodeForm({ ...episodeForm, videoUrl: e.target.value })}
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
                  value={episodeForm.description}
                  onChange={(e) => setEpisodeForm({ ...episodeForm, description: e.target.value })}
                  placeholder="Resumo do enredo do episódio corrente..."
                  className="bg-zinc-900 border border-zinc-800 p-2 rounded"
                />
              </div>

              <div className="flex flex-col gap-1 text-xs">
                <label className="text-zinc-400 font-bold uppercase font-mono">Thumbnail de Capa Macia URL</label>
                <input
                  type="text"
                  value={episodeForm.thumbnailUrl}
                  onChange={(e) => setEpisodeForm({ ...episodeForm, thumbnailUrl: e.target.value })}
                  placeholder="Cole ou deixe em branco para simulação"
                  className="bg-zinc-900 border border-zinc-800 p-2 rounded text-[10px] font-mono"
                />
              </div>

              <div className="flex gap-2 justify-end pt-3 text-xs border-t border-zinc-900">
                <button type="button" onClick={() => setShowEpisodeModal(false)} className="bg-zinc-900 hover:bg-zinc-805 p-2 rounded">Cancelar</button>
                <button type="submit" className="bg-[#ef4444] hover:bg-red-700 text-white font-bold py-2 px-3 rounded">Confirmar Capítulo</button>
              </div>
            </form>
          </div>
        </div>
      )}

    </div>
  );
}

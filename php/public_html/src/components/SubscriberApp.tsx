/**
 * @license
 * SPDX-License-Identifier: Apache-2.0
 */

import React, { useState, useEffect } from 'react';
import { 
  User, Profile, Plan, Content, Category, Series, Season, Episode, Notification, Favorite, WatchHistory, Review 
} from '../types';
import { db } from '../data/mockDatabase';
import VideoPlayer from './VideoPlayer';
import { 
  Search, Bell, User as UserIcon, LogOut, Check, Heart, Play, Film, Info, 
  ChevronRight, ArrowLeft, Star, Settings, Trash, RefreshCw, X, Clapperboard, Layers 
} from 'lucide-react';

interface SubscriberAppProps {
  user: User;
  profile: Profile;
  onLogout: () => void;
  onNavigateToProfiles: () => void;
}

export default function SubscriberApp({ user, profile, onLogout, onNavigateToProfiles }: SubscriberAppProps) {
  // DB States
  const [contents, setContents] = useState<Content[]>([]);
  const [categories, setCategories] = useState<Category[]>([]);
  const [series, setSeries] = useState<Series[]>([]);
  const [seasons, setSeasons] = useState<Season[]>([]);
  const [episodes, setEpisodes] = useState<Episode[]>([]);
  const [favorites, setFavorites] = useState<Favorite[]>([]);
  const [watchHistory, setWatchHistory] = useState<WatchHistory[]>([]);
  const [notifications, setNotifications] = useState<Notification[]>([]);
  const [plans, setPlans] = useState<Plan[]>([]);
  const [reviews, setReviews] = useState<Review[]>([]);

  // Search & Filters
  const [searchQuery, setSearchQuery] = useState('');
  const [activeCategoryFilter, setActiveCategoryFilter] = useState<string | null>(null);
  const [activeTypeFilter, setActiveTypeFilter] = useState<string | null>(null);

  // Modals & Panels Active state
  const [selectedContent, setSelectedContent] = useState<Content | null>(null);
  const [selectedSeries, setSelectedSeries] = useState<Series | null>(null);
  const [selectedSeasonNumber, setSelectedSeasonNumber] = useState<number>(1);
  const [activePlayer, setActivePlayer] = useState<{ url: string; title: string; subtitle?: string; contentId: string; episodeId?: string } | null>(null);
  
  const [showAccountPanel, setShowAccountPanel] = useState(false);
  const [showNotificationDrawer, setShowNotificationDrawer] = useState(false);
  const [showMyListPanel, setShowMyListPanel] = useState(false);

  // Account Form fields
  const [changePasswordModal, setChangePasswordModal] = useState(false);
  const [newPass, setNewPass] = useState('');
  const [successMsg, setSuccessMsg] = useState('');

  // Review Form state
  const [userRating, setUserRating] = useState<number>(5);
  const [userComment, setUserComment] = useState<string>('');
  const [reviewSuccessMsg, setReviewSuccessMsg] = useState<string>('');

  // Loaded DB data on mount
  useEffect(() => {
    reloadDb();
  }, []);

  const reloadDb = () => {
    setContents(db.getContents().filter(c => c.status === 'published'));
    setCategories(db.getCategories());
    setSeries(db.getSeries().filter(s => s.status === 'published'));
    setSeasons(db.getSeasons().filter(s => s.status === 'published'));
    setEpisodes(db.getEpisodes().filter(e => e.status === 'published'));
    setFavorites(db.getFavorites().filter(f => f.userId === user.id));
    setWatchHistory(db.getWatchHistory().filter(h => h.userId === user.id));
    setNotifications(db.getNotifications());
    setPlans(db.getPlans());
    setReviews(db.getReviews());
  };

  // Manage Favorites
  const toggleFavorite = (contentId: string) => {
    const allFav = db.getFavorites();
    const existingIndex = allFav.findIndex(f => f.userId === user.id && f.contentId === contentId);
    let nextFav: Favorite[] = [];

    if (existingIndex > -1) {
      nextFav = allFav.filter((_, idx) => idx !== existingIndex);
    } else {
      nextFav = [...allFav, { id: 'fav-' + Math.random().toString(36).substring(2, 9), userId: user.id, contentId }];
    }

    db.setFavorites(nextFav);
    setFavorites(nextFav.filter(f => f.userId === user.id));
  };

  const isFavorited = (contentId: string) => {
    return favorites.some(f => f.contentId === contentId);
  };

  // Record Watch History on Play
  const recordPlayHistory = (content: Content, episode?: Episode) => {
    const allHistory = db.getWatchHistory();
    const newRecord: WatchHistory = {
      id: 'hist-' + Math.random().toString(36).substring(2, 9),
      userId: user.id,
      contentId: content.id,
      episodeId: episode?.id,
      watchedPercent: 10 + Math.floor(Math.random() * 85), // simulated random completion
      contentType: content.type,
      title: episode ? `${content.title} S${selectedSeasonNumber}E${episode.number}: ${episode.title}` : content.title,
      updatedAt: new Date().toISOString()
    };

    // Remove duplicates for the same content
    const cleanedHistory = allHistory.filter(h => h.userId !== user.id || h.contentId !== content.id);
    db.setWatchHistory([newRecord, ...cleanedHistory]);
    setWatchHistory([newRecord, ...cleanedHistory.filter(h => h.userId === user.id)]);
  };

  // Simulated Cancellation
  const handleCancelSubscription = () => {
    if (confirm('Tem certeza que deseja solicitar o cancelamento simulado da sua assinatura da F5 TV?')) {
      const subs = db.getSubscriptions();
      const updated = subs.map(s => s.userId === user.id ? { ...s, status: 'canceled' as const } : s);
      db.setSubscriptions(updated);

      // notify user
      alert('Assinatura cancelada com sucesso na simulação. Seu acesso expirará ao final do ciclo corrente.');
      reloadDb();
    }
  };

  const handleUpdatePassword = (e: React.FormEvent) => {
    e.preventDefault();
    if (!newPass.trim()) return;
    setSuccessMsg('Senha tática redefinida com sucesso no ambiente local!');
    setNewPass('');
    setTimeout(() => {
      setSuccessMsg('');
      setChangePasswordModal(false);
    }, 2500);
  };

  const handleSubmitReview = (e: React.FormEvent) => {
    e.preventDefault();
    if (!selectedContent || !userComment.trim()) return;

    const newReview: Review = {
      id: 'rev-' + Math.random().toString(36).substring(2, 9),
      contentId: selectedContent.id,
      profileId: profile.id,
      profileName: profile.name,
      avatarColor: profile.avatarColor || 'bg-red-650',
      rating: userRating,
      comment: userComment,
      createdAt: new Date().toISOString()
    };

    const nextReviews = [newReview, ...db.getReviews()];
    db.setReviews(nextReviews);
    setReviews(nextReviews);
    setUserComment('');
    setUserRating(5);
    setReviewSuccessMsg('Opinião publicada com sucesso!');
    setTimeout(() => {
      setReviewSuccessMsg('');
    }, 3000);
  };

  // Search filter matches
  const filteredContents = contents.filter(item => {
    const matchesSearch = searchQuery 
      ? item.title.toLowerCase().includes(searchQuery.toLowerCase()) || 
        item.genre.toLowerCase().includes(searchQuery.toLowerCase()) || 
        item.shortDescription.toLowerCase().includes(searchQuery.toLowerCase())
      : true;

    const matchesCategory = activeCategoryFilter 
      ? item.categoryId === activeCategoryFilter 
      : true;

    const matchesType = activeTypeFilter 
      ? item.type === activeTypeFilter 
      : true;

    return matchesSearch && matchesCategory && matchesType;
  });

  const featured = contents.find(c => c.isFeatured) || contents[0];

  // Map Category Slug titles
  const getCatTitle = (catId: string) => {
    return categories.find(c => c.id === catId)?.name || 'Outros';
  };

  // Manage serial relationships inside Details modal
  const contentSeries = selectedContent ? series.find(s => s.title.toLowerCase().trim() === selectedContent.title.toLowerCase().trim()) : null;
  const seriesSeasons = contentSeries ? seasons.filter(s => s.seriesId === contentSeries.id) : [];
  const activeSeason = seriesSeasons.find(s => s.number === selectedSeasonNumber);
  const seasonEpisodes = activeSeason ? episodes.filter(e => e.seasonId === activeSeason.id) : [];

  return (
    <div id="subscriber-app-root" className="min-h-screen bg-[#050505] text-white selection:bg-red-600 text-sm font-sans flex flex-col relative animate-fade-in">
      
      {/* Upper Navigation Bar */}
      <header className="sticky top-0 z-40 bg-[#050505]/90 backdrop-blur-md border-b border-white/5 px-8 h-20 flex items-center justify-between">
        <div className="flex items-center gap-8">
          <span 
            className="text-2xl font-black tracking-tighter uppercase text-white hover:scale-102 transition duration-200 cursor-pointer"
            onClick={() => {
              setActiveCategoryFilter(null);
              setActiveTypeFilter(null);
              setSearchQuery('');
            }}
          >
            F5 <span className="text-red-655">TV</span>
          </span>

          {/* Type filters tabs */}
          <nav className="hidden md:flex items-center gap-6 text-xs font-mono font-bold tracking-widest text-white/50 uppercase">
            <button 
              onClick={() => { setActiveTypeFilter(null); setActiveCategoryFilter(null); }} 
              className={`hover:text-white transition uppercase cursor-pointer ${!activeTypeFilter && !activeCategoryFilter ? 'text-red-600 border-b-2 border-red-600 pb-1' : ''}`}
            >
              Início
            </button>
            <button 
              onClick={() => { setActiveTypeFilter('series'); setActiveCategoryFilter(null); }} 
              className={`hover:text-white transition uppercase cursor-pointer ${activeTypeFilter === 'series' ? 'text-red-600 border-b-2 border-red-600 pb-1' : ''}`}
            >
              Séries
            </button>
            <button 
              onClick={() => { setActiveTypeFilter('news'); setActiveCategoryFilter(null); }} 
              className={`hover:text-white transition uppercase cursor-pointer ${activeTypeFilter === 'news' ? 'text-red-600 border-b-2 border-red-600 pb-1' : ''}`}
            >
              Jornalismo
            </button>
            <button 
              onClick={() => { setActiveTypeFilter('documentary'); setActiveCategoryFilter(null); }} 
              className={`hover:text-white transition uppercase cursor-pointer ${activeTypeFilter === 'documentary' ? 'text-red-600 border-b-2 border-red-600 pb-1' : ''}`}
            >
              Documentários
            </button>
            <button 
              onClick={() => { setActiveTypeFilter('sports'); setActiveCategoryFilter(null); }} 
              className={`hover:text-white transition uppercase cursor-pointer ${activeTypeFilter === 'sports' ? 'text-red-600 border-b-2 border-red-600 pb-1' : ''}`}
            >
              Esportes
            </button>
          </nav>
        </div>

        {/* Right side interactive tray */}
        <div className="flex items-center gap-5">
          
          {/* Search Box */}
          <div className="relative hidden sm:block">
            <Search className="absolute left-3 top-2.5 w-3.5 h-3.5 text-white/40" />
            <input
              id="sub-search-input"
              type="text"
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              placeholder="Filtre programas, séries, notícias..."
              className="bg-[#0a0a0a] border border-white/5 focus:border-red-600/50 rounded-full pl-9 pr-4 py-2 focus:w-64 w-48 transition-all duration-300 text-xs outline-none text-white/95 placeholder:text-white/30"
            />
          </div>

          {/* My List Trigger Drawer */}
          <button 
            id="sub-mylist-trigger"
            onClick={() => setShowMyListPanel(!showMyListPanel)}
            className={`p-2 bg-white/[0.03] border border-white/5 hover:bg-[#151515] rounded-full transition cursor-pointer relative ${showMyListPanel ? 'text-red-600 border-red-650' : 'text-white/60 hover:text-white'}`}
            title="Minha Lista"
          >
            <Heart className="w-4 h-4 fill-current" />
            {favorites.length > 0 && (
              <span className="absolute -top-1 -right-1 bg-red-605 text-white text-[9px] font-bold w-4 h-4 rounded-full flex items-center justify-center">
                {favorites.length}
              </span>
            )}
          </button>

          {/* Dynamic notifications Bell */}
          <button 
            id="sub-notif-trigger"
            onClick={() => setShowNotificationDrawer(!showNotificationDrawer)}
            className="p-2 bg-white/[0.03] border border-white/5 hover:bg-[#151515] rounded-full transition relative cursor-pointer text-white/60 hover:text-white"
            title="Notificações"
          >
            <Bell className="w-4 h-4" />
            <span className="absolute top-1.5 right-1.5 w-1.5 h-1.5 bg-red-600 rounded-full animate-pulse" />
          </button>

          {/* Account Profile Badge */}
          <div className="relative group/prof">
            <button 
              id="sub-profile-menu-trigger"
              onClick={() => setShowAccountPanel(!showAccountPanel)}
              className="flex items-center gap-2 p-1 bg-[#0a0a0a] hover:bg-[#151515] rounded-full border border-white/5 transition cursor-pointer pr-3"
            >
              <div className={`w-7 h-7 ${profile.avatarColor || 'bg-red-600'} rounded-full text-xs font-black flex items-center justify-center uppercase text-white`}>
                {profile.name ? profile.name.charAt(0) : 'U'}
              </div>
              <span className="text-xs font-semibold text-white/80 hidden md:inline">{profile.name}</span>
            </button>
          </div>

        </div>
      </header>

      {/* MOBILE SEARCH BAR */}
      <div className="p-3.5 sm:hidden border-b border-white/5 bg-[#050505] flex gap-2">
        <div className="relative flex-1">
          <Search className="absolute left-3 top-2.5 w-4 h-4 text-white/40" />
          <input
            id="sub-search-mobile"
            type="text"
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            placeholder="Buscar capas, jornalistas..."
            className="w-full bg-[#0a0a0a] border border-white/5 focus:border-red-600 rounded-lg pl-9 pr-4 py-2 text-xs outline-none text-white placeholder:text-white/30"
          />
        </div>
      </div>

      {/* CATEGORIES CHIPS BAR */}
      <div className="px-8 py-3.5 border-b border-white/5 bg-[#050505] overflow-x-auto flex gap-2.5 scrollbar-none items-center">
        <button
          onClick={() => setActiveCategoryFilter(null)}
          className={`px-4 py-1.5 rounded-full text-xs font-mono font-bold uppercase shrink-0 transition cursor-pointer border ${
            !activeCategoryFilter 
              ? 'bg-red-600 border-red-600 text-white' 
              : 'bg-[#0a0a0a] border-white/5 text-white/50 hover:text-white hover:bg-neutral-900'
          }`}
        >
          Todos os Gêneros
        </button>
        {categories.map((cat) => (
          <button
            key={cat.id}
            onClick={() => setActiveCategoryFilter(cat.id)}
            className={`px-4 py-1.5 rounded-full text-xs font-mono font-bold uppercase shrink-0 transition cursor-pointer border ${
              activeCategoryFilter === cat.id 
                ? 'bg-red-600 border-red-600 text-white' 
                : 'bg-[#0a0a0a] border-white/5 text-white/50 hover:text-white hover:bg-neutral-900'
            }`}
          >
            {cat.name}
          </button>
        ))}
      </div>

      {/* CORE WORKSPACE SPACE */}
      <main className="flex-1 pb-16">
        
        {/* If Search/Filters active: render matching grid. Else: render netflix layout */}
        {searchQuery || activeCategoryFilter || activeTypeFilter ? (
          <section className="p-6 md:p-8 animate-fade-in">
            <div className="mb-6 flex flex-col gap-1">
              <h2 className="text-xl md:text-2xl font-black tracking-tight uppercase">Resultados da Filtragem</h2>
              <span className="text-zinc-500 text-xs">A pesquisa encontrou {filteredContents.length} conteúdos cadastrados</span>
            </div>

            {filteredContents.length === 0 ? (
              <div className="border border-zinc-900 bg-zinc-950/30 p-12 text-center rounded-xl my-4 text-zinc-500">
                <Clapperboard className="w-12 h-12 mx-auto text-zinc-700 mb-3" />
                <p className="font-medium text-sm">Nenhum programa correspondente aos filtros.</p>
                <button 
                  onClick={() => { setSearchQuery(''); setActiveCategoryFilter(null); setActiveTypeFilter(null); }}
                  className="mt-3 text-[#ef4444] hover:underline text-xs font-mono uppercase font-bold"
                >
                  Limpar Filtros
                </button>
              </div>
            ) : (
              <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                {filteredContents.map((item) => (
                  <div 
                    key={item.id}
                    onClick={() => setSelectedContent(item)}
                    className="group relative bg-zinc-950 border border-zinc-900 hover:border-[#ef4444] rounded-lg overflow-hidden cursor-pointer transition transform hover:-translate-y-1 block"
                  >
                    <div className="aspect-[3/4] relative bg-zinc-900">
                      <img 
                        src={item.coverUrl} 
                        alt={item.title} 
                        referrerPolicy="no-referrer"
                        className="w-full h-full object-cover group-hover:opacity-100 opacity-80"
                      />
                      <div className="absolute top-2 right-2 bg-black/85 text-[10px] font-mono font-bold text-gray-300 px-1.5 py-0.5 rounded border border-zinc-800">
                        {item.ageRating}
                      </div>
                    </div>
                    <div className="p-3 bg-zinc-950 flex flex-col gap-0.5">
                      <span className="text-[9px] font-mono uppercase text-zinc-500 tracking-wider">
                        {item.genre}
                      </span>
                      <h3 className="font-bold text-sm text-zinc-200 line-clamp-1 group-hover:text-white transition">
                        {item.title}
                      </h3>
                    </div>
                  </div>
                ))}
              </div>
            )}
          </section>
        ) : (
          <>
            {/* 1. Immersive Wide Hero Banner */}
            {featured && (
              <section id="hero-banner" className="relative h-[55vh] md:h-[65vh] flex items-end p-6 md:p-12 border-b border-zinc-900 bg-black overflow-hidden select-none">
                <div 
                  className="absolute inset-0 bg-cover bg-center opacity-40 md:opacity-50"
                  style={{ backgroundImage: `url(${featured.bannerUrl})` }}
                />
                <div className="absolute inset-0 bg-gradient-to-t from-zinc-950 via-zinc-950/40 to-black/30" />
                
                <div className="relative z-10 max-w-3xl flex flex-col items-start gap-4">
                  <div className="inline-flex items-center gap-1.5 bg-[#ef4444] text-white font-mono font-black text-[10px] tracking-wider uppercase px-2 py-0.5 rounded-sm">
                    EM DESTAQUE F5 HOJE
                  </div>

                  <h1 className="text-3xl md:text-5xl font-black tracking-tight leading-none text-white">{featured.title}</h1>
                  <p className="text-zinc-300 text-sm md:text-base leading-relaxed line-clamp-2 md:line-clamp-3 font-medium max-w-2xl">{featured.shortDescription}</p>

                  <div className="flex flex-wrap gap-3 mt-2">
                    <button
                      id="hero-play-featured"
                      onClick={() => {
                        recordPlayHistory(featured);
                        setActivePlayer({ url: featured.videoUrl, title: featured.title, contentId: featured.id });
                      }}
                      className="bg-[#ef4444] hover:bg-red-700 text-white font-bold px-5 py-2.5 rounded flex items-center gap-1.5 text-xs uppercase cursor-pointer tracking-wider font-mono transition"
                    >
                      <Play className="w-4 h-4 fill-white" />
                      <span>Assistir Agora</span>
                    </button>
                    <button
                      id="hero-info-featured"
                      onClick={() => setSelectedContent(featured)}
                      className="bg-zinc-905-trans base-border border-zinc-800 hover:bg-zinc-800 text-white font-bold px-5 py-2.5 rounded flex items-center gap-1.5 text-xs uppercase cursor-pointer tracking-wider font-mono transition"
                    >
                      <Info className="w-4 h-4" />
                      <span>Detalhes</span>
                    </button>
                    <button
                      id="hero-add-featured"
                      onClick={() => toggleFavorite(featured.id)}
                      className="bg-zinc-900/80 border border-zinc-800 hover:border-zinc-700 text-zinc-350 hover:text-white p-2.5 rounded cursor-pointer transition"
                      title="Salvar em minha lista"
                    >
                      <Heart className={`w-4 h-4 ${isFavorited(featured.id) ? 'fill-red-500 text-red-500' : ''}`} />
                    </button>
                  </div>
                </div>
              </section>
            )}

            {/* 2. Continuar Assistindo Row (if watch history exists) */}
            {watchHistory.length > 0 && (
              <section id="row-continue" className="px-6 md:px-12 py-6">
                <h3 className="text-sm font-mono tracking-widest text-[#ef4444] font-black uppercase mb-4">CONTINUAR ASSISTINDO</h3>
                <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                  {watchHistory.slice(0, 4).map((hist) => {
                    const originalItem = contents.find(c => c.id === hist.contentId) || series.find(s => s.id === hist.contentId);
                    if (!originalItem) return null;

                    return (
                      <div 
                        key={hist.id}
                        className="bg-zinc-900 border border-zinc-850 hover:border-zinc-700 rounded-lg overflow-hidden flex flex-col gap-2 relative group p-3.5"
                      >
                        <div className="flex justify-between items-start gap-2">
                          <div className="flex flex-col">
                            <span className="text-[9px] font-mono tracking-wider font-bold text-zinc-500 uppercase">HISTÓRICO</span>
                            <h4 className="font-bold text-sm text-zinc-100 line-clamp-1">{hist.title}</h4>
                          </div>
                          <button 
                            onClick={() => {
                              // Play again
                              setActivePlayer({ 
                                url: 'videoUrl' in originalItem ? originalItem.videoUrl : 'https://assets.mixkit.co/videos/preview/mixkit-software-developer-working-on-his-computer-34289-large.mp4', 
                                title: originalItem.title,
                                contentId: originalItem.id,
                                episodeId: hist.episodeId
                              });
                            }}
                            className="p-1.5 bg-[#ef4444] rounded-full text-white cursor-pointer hover:scale-105 transition"
                          >
                            <Play className="w-3.5 h-3.5 fill-current" />
                          </button>
                        </div>
                        {/* Fake completion progress lines */}
                        <div className="mt-2 flex flex-col gap-1 text-[10px] text-zinc-400 font-mono font-bold">
                          <div className="flex justify-between">
                            <span>Sua reprodução:</span>
                            <span>{hist.watchedPercent}%</span>
                          </div>
                          <div className="h-1 bg-zinc-800 rounded-lg overflow-hidden">
                            <div className="h-full bg-red-600 rounded-lg" style={{ width: `${hist.watchedPercent}%` }} />
                          </div>
                        </div>
                      </div>
                    );
                  })}
                </div>
              </section>
            )}

            {/* 3. General Series collection grid */}
            <section id="collection-series-row" className="px-6 md:px-12 py-6">
              <h3 className="text-sm font-mono tracking-widest text-zinc-400 font-black uppercase mb-4 flex items-center gap-1">
                <Layers className="w-4 h-4 text-[#ef4444]" />
                <span>SÉRIES INVESTIGATIVAS & EXCLUSIVAS</span>
              </h3>
              <div className="grid grid-cols-1 sm:grid-cols-3 gap-6">
                {series.map((ser) => {
                  const itemsSeriesMatches = contents.filter(c => c.title.toLowerCase().trim() === ser.title.toLowerCase().trim());
                  const representativeContent = itemsSeriesMatches[0];
                  
                  return (
                    <div 
                      key={ser.id}
                      onClick={() => {
                        if (representativeContent) {
                          setSelectedContent(representativeContent);
                        } else {
                          // Fallback mock serial wrapper
                          alert('Série vazia no momento. Adicione episódios através da Área Administrativa para preencher!');
                        }
                      }}
                      className="group relative bg-[#09090b] border border-zinc-900 rounded-xl overflow-hidden cursor-pointer hover:border-red-600 transition shadow-lg shrink-0 flex flex-col"
                    >
                      <div className="aspect-[16/9] w-full bg-zinc-900 overflow-hidden relative">
                        <img 
                          src={ser.bannerUrl} 
                          alt={ser.title} 
                          referrerPolicy="no-referrer"
                          className="w-full h-full object-cover opacity-60 group-hover:opacity-90 transform group-hover:scale-102 transition duration-300"
                        />
                        <div className="absolute top-2 left-2 bg-red-600 text-[9px] font-bold text-white px-1.5 py-0.5 rounded uppercase font-mono tracking-wider">
                          SÉRIE F5 TV
                        </div>
                      </div>
                      <div className="p-4 flex flex-col gap-1 justify-between flex-1">
                        <div className="flex flex-col gap-1">
                          <span className="text-[10px] font-mono tracking-wider font-semibold uppercase text-[#ef4444]">
                            {ser.genre}
                          </span>
                          <h4 className="font-bold text-base text-zinc-100 group-hover:text-white">
                            {ser.title}
                          </h4>
                          <p className="text-zinc-500 text-xs line-clamp-2 leading-relaxed font-semibold">
                            {ser.description}
                          </p>
                        </div>
                      </div>
                    </div>
                  );
                })}
              </div>
            </section>

            {/* 4. Categorized collections rows */}
            {categories.map((cat) => {
              const matches = contents.filter(c => c.categoryId === cat.id);
              if (matches.length === 0) return null;

              return (
                <section key={cat.id} className="px-6 md:px-12 py-6">
                  <h3 className="text-sm font-mono tracking-widest text-zinc-400 font-black uppercase mb-4 flex items-center gap-1.5">
                    <span className="w-1.5 h-3 bg-[#ef4444] rounded-sm" />
                    <span>{cat.name}</span>
                  </h3>

                  <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    {matches.map((item) => (
                      <div 
                        key={item.id}
                        onClick={() => setSelectedContent(item)}
                        className="group relative bg-[#0a0a0a] border border-white/5 hover:border-red-655 rounded-lg overflow-hidden cursor-pointer transition transform hover:-translate-y-1 block animate-fade-in"
                      >
                        <div className="aspect-[3/4] relative bg-[#151515]">
                          <img 
                            src={item.coverUrl} 
                            alt={item.title} 
                            referrerPolicy="no-referrer"
                            className="w-full h-full object-cover group-hover:opacity-100 opacity-80 transition duration-200"
                          />
                          <div className="absolute top-2 right-2 bg-black/85 text-[10px] font-mono font-bold text-gray-300 px-1.5 py-0.5 rounded border border-white/5">
                            {item.ageRating}
                          </div>
                          {item.isExclusive && (
                            <div className="absolute bottom-2 left-2 bg-red-650 text-[9px] font-bold text-white px-1.5 py-0.5 rounded uppercase font-mono tracking-wider shadow">
                              Exclusivo
                            </div>
                          )}
                        </div>
                        <div className="p-3 bg-[#0a0a0a] flex flex-col gap-0.5">
                          <span className="text-[9px] font-mono uppercase text-white/40 tracking-wider">
                            {item.genre}
                          </span>
                          <h4 className="font-bold text-sm text-white/90 line-clamp-1 group-hover:text-white transition">
                            {item.title}
                          </h4>
                        </div>
                      </div>
                    ))}
                  </div>
                </section>
              );
            })}

          </>
        )}

      </main>

      {/* FOOTER */}
      <footer className="mt-auto border-t border-white/5 bg-[#050505] text-center p-8 text-xs text-white/40 font-mono flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
          <span>F5 TV Streaming Platform MVP • Sintonizado como </span>
          <strong className="text-red-550 font-semibold">{profile.name}</strong>
        </div>
        <div className="flex gap-4">
          <button onClick={onNavigateToProfiles} className="hover:underline hover:text-white transition text-left cursor-pointer">• Trocar Perfil</button>
          <button onClick={onLogout} className="hover:underline hover:text-red-650 transition text-left cursor-pointer">• Sair da Conta</button>
        </div>
      </footer>

      {/* OVERLAY: MULTI-PURPOSE CONTENT DETALHE MODAL */}
      {selectedContent && (
        <div id="content-detail-modal" className="fixed inset-0 bg-black/90 backdrop-blur-sm z-40 flex items-center justify-center p-4 overflow-y-auto select-none font-sans text-white">
          <div className="bg-[#0a0a0a] border border-white/5 rounded-2xl w-full max-w-4xl overflow-hidden shadow-2xl relative my-8">
            
            {/* Close modal button */}
            <button 
              id="close-detail-modal"
              onClick={() => {
                setSelectedContent(null);
                setSelectedSeries(null);
                setSelectedSeasonNumber(1);
              }}
              className="absolute top-4 right-4 p-2.5 bg-[#050505]/90 border border-white/5 hover:bg-[#151515] text-white rounded-full transition z-10 cursor-pointer"
              title="Fechar Detalhes"
            >
              <X className="w-5 h-5" />
            </button>

            {/* Banner top */}
            <div className="relative h-60 md:h-80 w-full bg-[#151515]">
              <img 
                src={selectedContent.bannerUrl} 
                alt={selectedContent.title} 
                referrerPolicy="no-referrer"
                className="w-full h-full object-cover opacity-50"
              />
              <div className="absolute inset-0 bg-gradient-to-t from-[#0a0a0a] to-transparent" />
              
              <div className="absolute bottom-6 left-6 md:left-10 right-6 z-10">
                <div className="flex flex-col gap-2">
                  <span className="text-xs font-mono font-bold tracking-wider text-red-650 uppercase leading-relaxed">
                    {getCatTitle(selectedContent.categoryId)} • {selectedContent.genre}
                  </span>
                  <h2 className="text-2xl md:text-4xl font-black text-white leading-tight">{selectedContent.title}</h2>
                </div>
              </div>
            </div>

            {/* Details body split */}
            <div className="p-6 md:p-10 grid grid-cols-1 md:grid-cols-3 gap-8 text-zinc-300">
              
              {/* Left Column: descriptions and playback choices */}
              <div className="md:col-span-2 flex flex-col gap-5">
                <div className="flex flex-wrap items-center gap-3 text-xs font-mono font-bold text-zinc-400">
                  <span className="bg-zinc-900 text-[#ef4444] px-2 py-0.5 rounded border border-zinc-805 tracking-widest">{selectedContent.ageRating}</span>
                  <span>{selectedContent.year}</span>
                  <span>{selectedContent.duration}</span>
                  {selectedContent.isExclusive && (
                    <span className="bg-red-950 text-red-500 border border-red-900 px-2 py-0.5 rounded tracking-wide font-sans text-[10px] uppercase">Exclusivo Assinante</span>
                  )}
                  {selectedContent.isFree ? (
                    <span className="bg-emerald-950 text-emerald-400 border border-emerald-900 px-1.5 py-0.5 rounded text-[10px] font-sans tracking-wide uppercase">Gratuito</span>
                  ) : null}
                </div>

                <p className="text-sm md:text-base leading-relaxed font-semibold text-zinc-350">
                  {selectedContent.fullDescription}
                </p>

                {/* Sub Action controls buttons inside details modal */}
                <div className="flex flex-wrap gap-3">
                  <button
                    id="modal-play-now"
                    onClick={() => {
                      recordPlayHistory(selectedContent);
                      if (seasonEpisodes.length > 0) {
                        // If it is serial, play the first episode!
                        const firstEp = seasonEpisodes[0];
                        setActivePlayer({
                          url: firstEp.videoUrl,
                          title: selectedContent.title,
                          subtitle: `T${selectedSeasonNumber}E1 - ${firstEp.title}`,
                          contentId: selectedContent.id,
                          episodeId: firstEp.id
                        });
                      } else {
                        setActivePlayer({
                          url: selectedContent.videoUrl,
                          title: selectedContent.title,
                          contentId: selectedContent.id
                        });
                      }
                    }}
                    className="bg-[#ef4444] hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg text-xs tracking-wider uppercase font-mono flex items-center justify-center gap-2 cursor-pointer transition shadow-md shadow-red-900/30"
                  >
                    <Play className="w-4 h-4 fill-white" />
                    <span>{contentSeries ? 'Assistir Ep. 1' : 'Assistir Agora'}</span>
                  </button>

                  <button
                    id="modal-play-trailer"
                    onClick={() => {
                      setActivePlayer({
                        url: selectedContent.trailerUrl,
                        title: `${selectedContent.title} (Teaser Oficial)`,
                        contentId: selectedContent.id
                      });
                    }}
                    className="bg-zinc-900/90 border border-zinc-800 hover:bg-zinc-850 hover:border-zinc-700 text-white font-bold py-3 px-6 rounded-lg text-xs tracking-wider uppercase font-mono flex items-center justify-center gap-2 cursor-pointer transition"
                  >
                    <Clapperboard className="w-4 h-4" />
                    <span>Ver Trailer</span>
                  </button>

                  <button
                    onClick={() => toggleFavorite(selectedContent.id)}
                    className={`p-3 rounded-lg border flex items-center justify-center gap-1.5 cursor-pointer text-xs font-mono font-bold uppercase transition ${
                      isFavorited(selectedContent.id)
                        ? 'bg-red-955/20 border-red-900 text-[#ef4444]'
                        : 'bg-zinc-900/50 border-zinc-850 text-zinc-400 hover:border-zinc-700 hover:text-white'
                    }`}
                  >
                    <Heart className={`w-4 h-4 ${isFavorited(selectedContent.id) ? 'fill-red-500' : ''}`} />
                    <span>{isFavorited(selectedContent.id) ? 'Na Minha Lista' : 'Salvar'}</span>
                  </button>
                </div>

                {/* SERIES / EPISODES SECTION IF APPLICABLE */}
                {contentSeries && (
                  <div className="border-t border-zinc-900 pt-6 mt-4">
                    <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4">
                      <h3 className="text-zinc-200 font-bold text-base flex items-center gap-2">
                        <Layers className="w-5 h-5 text-[#ef4444]" />
                        <span>Selecione a Temporada</span>
                      </h3>
                      {seriesSeasons.length > 0 && (
                        <select
                          id="season-selector-dropdown"
                          value={selectedSeasonNumber}
                          onChange={(e) => setSelectedSeasonNumber(parseInt(e.target.value))}
                          className="bg-zinc-900 border border-zinc-800 focus:border-[#ef4444] rounded px-3 py-1.5 text-xs text-zinc-300 font-bold outline-none cursor-pointer"
                        >
                          {seriesSeasons.map((se) => (
                            <option key={se.id} value={se.number}>
                              Temporada {se.number}
                            </option>
                          ))}
                        </select>
                      )}
                    </div>

                    {/* Episodes list */}
                    {seasonEpisodes.length === 0 ? (
                      <div className="bg-zinc-950 p-6 text-center text-zinc-600 rounded-lg border border-dashed border-zinc-900 text-xs">
                        Nenhum episódio publicado para esta temporada ainda.
                      </div>
                    ) : (
                      <div className="flex flex-col gap-3 max-h-80 overflow-y-auto pr-2 scrollbar-thin">
                        {seasonEpisodes.map((ep) => (
                          <div 
                            key={ep.id}
                            onClick={() => {
                              recordPlayHistory(selectedContent, ep);
                              setActivePlayer({
                                url: ep.videoUrl,
                                title: selectedContent.title,
                                subtitle: `Temp. ${selectedSeasonNumber} - Ep. ${ep.number}: ${ep.title}`,
                                contentId: selectedContent.id,
                                episodeId: ep.id
                              });
                            }}
                            className="bg-zinc-900/60 hover:bg-zinc-900 border border-zinc-850/80 hover:border-zinc-750 p-3 rounded-lg flex gap-4 items-center cursor-pointer group transition duration-200"
                          >
                            <div className="w-24 md:w-32 aspect-video bg-zinc-950 rounded-md overflow-hidden shrink-0 relative">
                              <img 
                                src={ep.thumbnailUrl} 
                                alt={ep.title} 
                                referrerPolicy="no-referrer"
                                className="w-full h-full object-cover opacity-60 group-hover:opacity-100 transition duration-200"
                              />
                              <div className="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                                <Play className="w-6 h-6 text-white fill-white" />
                              </div>
                            </div>
                            <div className="flex-1 flex flex-col gap-1 text-left">
                              <div className="flex items-center gap-2">
                                <span className="text-xs font-mono font-bold text-[#ef4444]">Episódio {ep.number}</span>
                                <span className="text-[10px] text-zinc-550 font-mono">({ep.duration})</span>
                              </div>
                              <h4 className="font-bold text-sm text-zinc-205 group-hover:text-white line-clamp-1">{ep.title}</h4>
                              <p className="text-[11px] text-zinc-500 line-clamp-2 md:line-clamp-3 leading-relaxed font-semibold">
                                {ep.description}
                              </p>
                            </div>
                          </div>
                        ))}
                      </div>
                    )}
                  </div>
                )}

              </div>

              {/* Right Column: credits details */}
              <div className="flex flex-col gap-5 text-xs font-mono border-t md:border-t-0 border-zinc-900 pt-6 md:pt-0">
                <div className="flex flex-col gap-1.5">
                  <span className="text-zinc-500 font-bold uppercase">Diretor / Produtor</span>
                  <span className="text-zinc-200 font-bold text-xs">{selectedContent.directors.join(', ')}</span>
                </div>

                <div className="flex flex-col gap-1.5">
                  <span className="text-zinc-500 font-bold uppercase">Cast / Apresentação</span>
                  <span className="text-zinc-200 font-bold text-xs">{selectedContent.cast.join(', ')}</span>
                </div>

                <div className="flex flex-col gap-1.5">
                  <span className="text-zinc-500 font-bold uppercase">Popularidade</span>
                  <span className="text-zinc-300 font-bold text-xs">{selectedContent.viewsCount.toLocaleString('pt-BR')} visualizações</span>
                </div>

                <div className="flex flex-col gap-1.5">
                  <span className="text-zinc-500 font-bold uppercase">Marcas de Tag</span>
                  <div className="flex flex-wrap gap-1 mt-1 text-[10px]">
                    {selectedContent.tags.map((tg, i) => (
                      <span key={i} className="bg-zinc-900 text-zinc-400 border border-zinc-800 px-2 py-0.5 rounded uppercase">{tg}</span>
                    ))}
                  </div>
                </div>
              </div>

            </div>

            {/* INTEGRATED RATINGS & COMMUNITY COMMENTS MODULE */}
            <div className="border-t border-white/5 bg-[#070707] p-6 md:p-10 flex flex-col gap-8">
              <div className="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 pb-4 border-b border-white/5">
                <div>
                  <h3 className="text-lg font-bold text-white uppercase tracking-wider font-mono flex items-center gap-2">
                    <Star className="w-5 h-5 text-amber-400 fill-amber-400" />
                    <span>Opinião da Comunidade F5 TV</span>
                  </h3>
                  <p className="text-zinc-500 text-xs font-semibold mt-1">Veja e registre avaliações espontâneas para este título exclusivo.</p>
                </div>
                
                {/* Stats recap */}
                <div className="flex items-center gap-6 bg-[#0c0c0d] border border-white/5 px-4 py-2.5 rounded-xl font-mono">
                  <div className="flex flex-col items-center">
                    <span className="text-[10px] text-zinc-500 uppercase font-black">Média Geral</span>
                    <span className="text-xl font-black text-amber-400 mt-0.5">
                      {reviews.filter(r => r.contentId === selectedContent.id).length > 0
                        ? (reviews.filter(r => r.contentId === selectedContent.id).reduce((sum, r) => sum + r.rating, 0) / reviews.filter(r => r.contentId === selectedContent.id).length).toFixed(1)
                        : '5.0'} ★
                    </span>
                  </div>
                  <div className="h-8 w-px bg-zinc-800" />
                  <div className="flex flex-col items-center">
                    <span className="text-[10px] text-zinc-500 uppercase font-black">Resenhas</span>
                    <span className="text-lg font-black text-zinc-300 mt-0.5">
                      {reviews.filter(r => r.contentId === selectedContent.id).length}
                    </span>
                  </div>
                </div>
              </div>

              <div className="grid grid-cols-1 lg:grid-cols-5 gap-8">
                
                {/* Left: Input Feedback form */}
                <div className="lg:col-span-2 bg-[#0a0a0b]/40 border border-[#ef4444]/5 p-5 rounded-xl flex flex-col gap-4">
                  <h4 className="text-xs font-mono font-black text-zinc-300 uppercase tracking-widest">Enviar sua avaliação como <span className="text-red-500">{profile.name}</span></h4>
                  
                  {reviewSuccessMsg && (
                    <div className="p-3 bg-emerald-950/40 border border-emerald-900 rounded-lg text-xs text-emerald-400 font-medium">
                      {reviewSuccessMsg}
                    </div>
                  )}

                  <form onSubmit={handleSubmitReview} className="flex flex-col gap-4">
                    <div className="flex flex-col gap-1.5">
                      <span className="text-xs font-semibold text-zinc-400">Quantas estrelas este título merece?</span>
                      <div className="flex gap-1 mt-0.5">
                        {[1, 2, 3, 4, 5].map((star) => (
                          <button
                            key={star}
                            type="button"
                            onClick={() => setUserRating(star)}
                            className="cursor-pointer focus:outline-none transition hover:scale-110"
                            title={`Avaliar com ${star} estrelas`}
                          >
                            <Star 
                              className={`w-6 h-6 transition ${
                                userRating >= star 
                                  ? 'fill-amber-400 text-amber-400' 
                                  : 'text-zinc-700 hover:text-zinc-550'
                              }`} 
                            />
                          </button>
                        ))}
                      </div>
                    </div>

                    <div className="flex flex-col gap-1.5">
                      <span className="text-xs font-semibold text-zinc-400">Seu Comentário Crítico</span>
                      <textarea
                        required
                        maxLength={300}
                        rows={3}
                        value={userComment}
                        onChange={(e) => setUserComment(e.target.value)}
                        placeholder="Diga à comunidade o que achou da produção F5 TV..."
                        className="w-full bg-[#030303] border border-white/5 focus:border-[#ef4444] rounded-lg p-3 text-xs text-zinc-100 placeholder:text-zinc-600 outline-none transition resize-none font-medium leading-relaxed"
                      />
                      <span className="text-[10px] text-zinc-650 font-mono text-right font-bold">{userComment.length}/300 caracteres</span>
                    </div>

                    <button
                      type="submit"
                      className="w-full bg-[#ef4444] hover:bg-red-700 text-white font-bold py-2.5 rounded-lg text-xs uppercase tracking-wider font-mono cursor-pointer transition shadow hover:shadow-red-950/40"
                    >
                      Publicar Opinião
                    </button>
                  </form>
                </div>

                {/* Right: Reviews List scrollable */}
                <div className="lg:col-span-3 flex flex-col gap-4">
                  <h4 className="text-xs font-mono font-black text-zinc-400 uppercase tracking-widest">Resenhas Recentes</h4>
                  
                  {reviews.filter(r => r.contentId === selectedContent.id).length === 0 ? (
                    <div className="flex-1 border border-zinc-900 border-dashed rounded-xl p-8 text-center text-zinc-600 text-xs flex flex-col items-center justify-center gap-2">
                      <Star className="w-8 h-8 text-zinc-800" />
                      <p>Este título ainda não possui resenhas. Seja o primeiro assinante a deixar sua opinião crítica!</p>
                    </div>
                  ) : (
                    <div className="flex flex-col gap-3 max-h-80 overflow-y-auto pr-2 scrollbar-thin">
                      {reviews
                        .filter(r => r.contentId === selectedContent.id)
                        .map((rev) => {
                          const firstLetter = rev.profileName ? rev.profileName.charAt(0).toUpperCase() : 'U';
                          return (
                            <div 
                              key={rev.id} 
                              className="bg-[#0b0b0c] border border-white/5 p-4 rounded-xl flex flex-col gap-2 transition hover:border-zinc-850"
                            >
                              <div className="flex justify-between items-center gap-2 text-xs">
                                <div className="flex items-center gap-2.5">
                                  <div className={`w-7 h-7 rounded-lg ${rev.avatarColor || 'bg-zinc-800'} text-xs font-bold text-white flex items-center justify-center`}>
                                    {firstLetter}
                                  </div>
                                  <div className="flex flex-col text-left">
                                    <span className="font-bold text-zinc-200">{rev.profileName}</span>
                                    <span className="text-[10px] text-zinc-550 font-mono">
                                      {new Date(rev.createdAt).toLocaleString('pt-BR', { dateStyle: 'short' }) || rev.createdAt}
                                    </span>
                                  </div>
                                </div>
                                <div className="flex gap-0.5">
                                  {Array.from({ length: 5 }).map((_, i) => (
                                    <Star 
                                      key={i} 
                                      className={`w-3.5 h-3.5 ${
                                        i < rev.rating 
                                          ? 'fill-amber-400 text-amber-400' 
                                          : 'text-zinc-800'
                                      }`} 
                                    />
                                  ))}
                                </div>
                              </div>
                              <p className="text-xs text-zinc-350 leading-relaxed text-left font-medium">
                                {rev.comment}
                              </p>
                            </div>
                          );
                        })}
                    </div>
                  )}
                </div>

              </div>
            </div>

          </div>
        </div>
      )}

      {/* OVERLAY: ACTIVE FULLSCREEN STANDARD PLAYER */}
      {activePlayer && (
        <VideoPlayer
          videoUrl={activePlayer.url}
          title={activePlayer.title}
          subtitle={activePlayer.subtitle}
          onClose={() => setActivePlayer(null)}
          hasNextEpisode={
            activePlayer.episodeId ? (
              // If series episode exists, check if next episode integer is in cache
              episodes.some(e => {
                const curEp = episodes.find(cur => cur.id === activePlayer.episodeId);
                return curEp && e.seasonId === curEp.seasonId && e.number === curEp.number + 1;
              })
            ) : false
          }
          onNextEpisode={() => {
            if (!activePlayer.episodeId) return;
            const curEp = episodes.find(cur => cur.id === activePlayer.episodeId);
            if (!curEp) return;
            const nextEp = episodes.find(e => e.seasonId === curEp.seasonId && e.number === curEp.number + 1);
            if (nextEp) {
              setActivePlayer({
                url: nextEp.videoUrl,
                title: activePlayer.title, // Series title
                subtitle: `Temp. ${selectedSeasonNumber} - Ep. ${nextEp.number}: ${nextEp.title}`,
                contentId: activePlayer.contentId,
                episodeId: nextEp.id
              });
            }
          }}
        />
      )}

      {/* DRAWER: FAVORITES/MINHA LISTA PANEL */}
      {showMyListPanel && (
        <div id="sub-mylist-drawer" className="fixed inset-y-0 right-0 w-full sm:max-w-md bg-zinc-950 border-l border-zinc-900 shadow-2xl z-50 p-6 flex flex-col justify-between font-sans text-white">
          <div className="flex flex-col gap-6 overflow-y-auto flex-1">
            <div className="flex justify-between items-center border-b border-zinc-900 pb-4">
              <h2 className="text-lg font-black tracking-tight flex items-center gap-2">
                <Heart className="w-5 h-5 text-[#ef4444] fill-red-500" />
                <span>Minha Lista F5 TV</span>
              </h2>
              <button 
                onClick={() => setShowMyListPanel(false)}
                className="p-1 hover:bg-zinc-900 rounded-full text-gray-500 hover:text-white cursor-pointer"
              >
                <X className="w-5 h-5" />
              </button>
            </div>

            {favorites.length === 0 ? (
              <div className="py-20 text-center text-zinc-500 font-medium">
                Sua lista de favoritos está vazia no momento. Curta itens no catálogo de streaming F5 TV para guardá-los!
              </div>
            ) : (
              <div className="flex flex-col gap-3">
                {favorites.map((fav) => {
                  const item = contents.find(c => c.id === fav.contentId);
                  if (!item) return null;

                  return (
                    <div 
                      key={fav.id}
                      className="bg-zinc-900/60 border border-zinc-850 p-3 rounded-lg flex items-center gap-4 hover:border-zinc-700 transition"
                    >
                      <img 
                        src={item.coverUrl} 
                        alt={item.title} 
                        referrerPolicy="no-referrer"
                        className="w-12 h-16 object-cover rounded bg-zinc-950 border border-zinc-800 shrink-0"
                      />
                      <div className="flex-1 text-left flex flex-col gap-0.5">
                        <span className="text-[10px] font-mono text-zinc-500 uppercase">{item.genre}</span>
                        <h4 className="font-bold text-xs text-white line-clamp-1">{item.title}</h4>
                        <div className="flex gap-2">
                          <button 
                            onClick={() => {
                              setShowMyListPanel(false);
                              setSelectedContent(item);
                            }}
                            className="text-[#ef4444] hover:underline text-[11px] font-bold font-mono uppercase"
                          >
                            Ver detalhes
                          </button>
                          <span className="text-zinc-700">|</span>
                          <button 
                            onClick={() => toggleFavorite(item.id)}
                            className="text-zinc-505 hover:text-red-400 text-[11px] font-bold font-mono uppercase"
                          >
                            Remover
                          </button>
                        </div>
                      </div>
                    </div>
                  );
                })}
              </div>
            )}
          </div>
        </div>
      )}

      {/* DRAWER: NOTIFICATIONS PANEL */}
      {showNotificationDrawer && (
        <div id="sub-notifications-drawer" className="fixed inset-y-0 right-0 w-full sm:max-w-md bg-zinc-950 border-l border-zinc-900 shadow-2xl z-50 p-6 flex flex-col justify-between font-sans text-white animate-fade-in">
          <div className="flex flex-col gap-6 overflow-y-auto flex-1">
            <div className="flex justify-between items-center border-b border-zinc-900 pb-4">
              <h2 className="text-lg font-black tracking-tight flex items-center gap-2">
                <Bell className="w-5 h-5 text-[#ef4444]" />
                <span>Notificações Internas</span>
              </h2>
              <button 
                onClick={() => setShowNotificationDrawer(false)}
                className="p-1 hover:bg-zinc-900 rounded-full text-gray-500 hover:text-white cursor-pointer"
              >
                <X className="w-5 h-5" />
              </button>
            </div>

            <div className="flex flex-col gap-3">
              {notifications.map((n) => {
                const isMyNotif = !n.userId || n.userId === user.id;
                if (!isMyNotif) return null;

                return (
                  <div 
                    key={n.id}
                    className="p-4 bg-zinc-900 border border-zinc-800/80 rounded-xl"
                  >
                    <div className="flex justify-between items-center mb-1">
                      <span className="text-[10px] font-mono font-bold text-[#ef4444] tracking-wider uppercase">F5 TV NEWS</span>
                      <span className="text-[9px] font-mono text-zinc-500">{new Date(n.createdAt).toLocaleDateString()}</span>
                    </div>
                    <h4 className="font-bold text-sm text-zinc-100 mb-1">{n.title}</h4>
                    <p className="text-zinc-400 text-xs leading-relaxed font-semibold">{n.message}</p>
                  </div>
                );
              })}
            </div>
          </div>
        </div>
      )}

      {/* OVERLAY: "MINHA CONTA" SYSTEM PANEL */}
      {showAccountPanel && (
        <div id="sub-account-panel-modal" className="fixed inset-0 bg-black/80 backdrop-blur-xs z-50 flex items-center justify-center p-4 overflow-y-auto select-none font-sans text-white">
          <div className="bg-zinc-950 border border-zinc-850 rounded-2xl w-full max-w-md overflow-hidden shadow-2xl relative p-6">
            
            <button 
              onClick={() => {
                setShowAccountPanel(false);
                setChangePasswordModal(false);
              }}
              className="absolute top-4 right-4 p-1.5 hover:bg-zinc-900 text-zinc-500 hover:text-white rounded-full transition cursor-pointer"
            >
              <X className="w-5 h-5" />
            </button>

            <div className="flex flex-col gap-4 text-left">
              <div className="border-b border-zinc-900 pb-4 mb-2">
                <h2 className="text-lg font-black tracking-tight">Minha Conta F5 TV</h2>
                <p className="text-zinc-500 text-xs mt-0.5">Gerencie os parâmetros da sua assinatura de streaming</p>
              </div>

              {/* User overview details */}
              <div className="flex items-center gap-3 bg-zinc-900/60 p-3 rounded-lg border border-zinc-850">
                <div className={`w-11 h-11 ${profile.avatarColor || 'bg-red-600'} rounded-full text-lg font-black flex items-center justify-center uppercase`}>
                  {profile.name ? profile.name.charAt(0) : 'U'}
                </div>
                <div className="flex flex-col">
                  <span className="font-bold text-sm text-white">{user.name}</span>
                  <span className="text-zinc-500 text-xs text-zinc-450 font-mono">{user.email}</span>
                </div>
              </div>

              {/* Plan parameters */}
              <div className="bg-zinc-900/40 border border-zinc-850 rounded-lg p-4 flex flex-col gap-3 font-normal">
                <div className="flex justify-between items-center text-xs font-mono font-bold text-zinc-400">
                  <span>PLANO ATUAL</span>
                  <span className="text-emerald-500 uppercase tracking-widest text-[10px] bg-emerald-950 px-1.5 py-0.5 rounded border border-emerald-900">Ativo</span>
                </div>
                <div>
                  <h3 className="font-bold text-lg text-white">
                    {plans.find(p => p.id === user.planId)?.name || 'Plano Premium'}
                  </h3>
                  <p className="text-zinc-500 text-xs leading-normal mt-0.5">Assinatura mensal faturada simulada via cartão.</p>
                </div>
                <div className="border-t border-zinc-900 pt-3 flex justify-between items-center text-xs mt-1">
                  <span className="text-zinc-450 font-semibold text-zinc-500">Próxima Cobrança:</span>
                  <span className="font-mono text-zinc-350 font-bold">01/06/2026</span>
                </div>
              </div>

              {/* Actions */}
              <div className="flex flex-col gap-2 mt-2">
                
                {changePasswordModal ? (
                  <form onSubmit={handleUpdatePassword} className="bg-zinc-900 border border-zinc-800 p-3 rounded-lg flex flex-col gap-2 text-xs font-normal">
                    {successMsg && <p className="text-xs font-bold text-emerald-400 mb-1">{successMsg}</p>}
                    <label className="text-zinc-400 font-bold uppercase font-mono text-[9px]">Nova senha de teste</label>
                    <div className="flex gap-2">
                      <input
                        id="acct-newpass-input"
                        type="password"
                        required
                        value={newPass}
                        onChange={(e) => setNewPass(e.target.value)}
                        placeholder="Mínimo 6 carácteres"
                        className="flex-1 bg-zinc-950 border border-zinc-800 p-1 rounded outline-none text-zinc-200"
                      />
                      <button type="submit" className="bg-[#ef4444] text-white px-3 font-bold rounded cursor-pointer">Salvar</button>
                    </div>
                  </form>
                ) : (
                  <button
                    onClick={() => setChangePasswordModal(true)}
                    className="w-full text-left bg-zinc-900 hover:bg-zinc-850 p-2.5 rounded-lg border border-zinc-850 text-xs font-bold text-zinc-300 flex items-center gap-2 transition cursor-pointer"
                  >
                    <Settings className="w-4 h-4 text-[#ef4444]" />
                    <span>Redefinir senha da conta</span>
                  </button>
                )}

                <button
                  id="acct-upgrade-sim-btn"
                  onClick={() => alert('Sua conta já está rodando sob as melhores especificações do Plano Premium da F5 TV!')}
                  className="w-full text-left bg-zinc-900 hover:bg-zinc-850 p-2.5 rounded-lg border border-zinc-850 text-xs font-bold text-zinc-300 flex items-center gap-2 transition cursor-pointer"
                >
                  <RefreshCw className="w-4 h-4 text-emerald-500" />
                  <span>Alterar plano de assinatura</span>
                </button>

                <button
                  id="acct-cancel-btn"
                  onClick={handleCancelSubscription}
                  className="w-full text-left bg-rose-950/20 hover:bg-rose-950/40 p-2.5 rounded-lg border border-rose-950 text-xs font-bold text-[#ef4444] flex items-center gap-2 transition cursor-pointer"
                >
                  <Trash className="w-4 h-4" />
                  <span>Cancelar assinatura da F5 TV</span>
                </button>
              </div>
            </div>

          </div>
        </div>
      )}

    </div>
  );
}

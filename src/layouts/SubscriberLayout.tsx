import React, { useState } from 'react';
import { Outlet, useNavigate, Link, useSearchParams } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { useData } from '../context/DataContext';
import { 
  Search, Heart, Bell, X, LogOut, Users, Settings, Grid, Play, AlertCircle, Tv
} from 'lucide-react';

export const SubscriberLayout: React.FC = () => {
  const { currentUser, currentProfile, logout, selectProfile } = useAuth();
  const { favorites, notifications, updateNotifications } = useData();
  const navigate = useNavigate();
  const [searchParams] = useSearchParams();

  const [showNotificationDrawer, setShowNotificationDrawer] = useState(false);
  const [searchVal, setSearchVal] = useState(searchParams.get('q') || '');

  if (!currentUser || !currentProfile) {
    return null;
  }

  const handleSearchSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (searchVal.trim()) {
      navigate(`/app/busca?q=${encodeURIComponent(searchVal.trim())}`);
    } else {
      navigate('/app');
    }
  };

  const handleSearchChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const val = e.target.value;
    setSearchVal(val);
    navigate(`/app/busca?q=${encodeURIComponent(val)}`);
  };

  const handleClearSearch = () => {
    setSearchVal('');
    navigate('/app');
  };

  const profileChar = currentProfile.name ? currentProfile.name.charAt(0).toUpperCase() : 'U';

  const userNotifications = notifications.filter(n => !n.userId || n.userId === currentUser.id);

  return (
    <div id="subscriber-layout-root" className="min-h-screen bg-[#050505] text-white flex flex-col relative font-sans selection:bg-[#ef4444] selection:text-white">
      
      {/* 1. Header Internal */}
      <header className="sticky top-0 z-40 bg-[#050505]/95 backdrop-blur-md border-b border-white/5 px-6 md:px-8 h-20 flex items-center justify-between">
        <div className="flex items-center gap-8">
          <div 
            onClick={() => {
              setSearchVal('');
              navigate('/app');
            }} 
            className="flex items-center gap-2 cursor-pointer select-none"
          >
            <span className="text-2xl font-black tracking-tighter uppercase text-white hover:scale-102 transition duration-200">
              F5 <span className="text-[#ef4444]">TV</span>
            </span>
            <span className="text-[10px] bg-red-600/10 text-[#ef4444] px-1.5 py-0.5 rounded font-mono font-bold tracking-widest uppercase hidden sm:inline">
              ASSINANTE
            </span>
          </div>

          {/* Menus matches: Início, Ao Vivo, Programação, Séries, Jornalismo, Documentários, Esportes, Minha Lista */}
          <nav className="hidden lg:flex items-center gap-5 text-xs font-mono font-bold tracking-widest text-zinc-400 uppercase">
            <Link to="/app" className="hover:text-white transition uppercase">
              Início
            </Link>
            <Link to="/app/ao-vivo" className="hover:text-red-400 transition uppercase text-[#ef4444] flex items-center gap-1.5">
              <span className="w-2 h-2 rounded-full bg-[#ef4444] animate-pulse" />
              Ao Vivo
            </Link>
            <Link to="/app/programacao" className="hover:text-white transition uppercase">
              Programação
            </Link>
            <Link to="/app/busca?type=series" className="hover:text-white transition uppercase">
              Séries
            </Link>
            <Link to="/app/busca?type=news" className="hover:text-white transition uppercase">
              Jornalismo
            </Link>
            <Link to="/app/busca?type=documentary" className="hover:text-white transition uppercase">
              Documentários
            </Link>
            <Link to="/app/busca?type=sports" className="hover:text-white transition uppercase">
              Esportes
            </Link>
            <Link to="/app/minha-lista" className="hover:text-white transition uppercase text-red-500 hover:text-red-400">
              Minha Lista
            </Link>
          </nav>
        </div>

        {/* Right tools row */}
        <div className="flex items-center gap-4 md:gap-5">
          
          {/* Search Box Form */}
          <form onSubmit={handleSearchSubmit} className="relative hidden md:block">
            <Search className="absolute left-3 top-2.5 w-3.5 h-3.5 text-zinc-500" />
            <input
              type="text"
              value={searchVal}
              onChange={handleSearchChange}
              placeholder="Séries, esportes, notícias..."
              className="bg-zinc-950 border border-zinc-900 focus:border-red-650 rounded-full pl-9 pr-8 py-2 focus:w-64 w-44 transition-all duration-300 text-xs font-semibold outline-none text-white/95 placeholder:text-zinc-650"
            />
            {searchVal && (
              <button 
                type="button" 
                onClick={handleClearSearch} 
                className="absolute right-3 top-2.5 text-zinc-500 hover:text-white transition"
              >
                <X className="w-3.5 h-3.5" />
              </button>
            )}
          </form>

          {/* Favorites List Indicator */}
          <button 
            onClick={() => navigate('/app/minha-lista')}
            className="p-2 bg-white/[0.03] border border-white/5 hover:bg-zinc-900 rounded-full transition cursor-pointer relative text-zinc-400 hover:text-white"
            title="Minha Lista"
          >
            <Heart className="w-4 h-4 fill-current text-red-500" />
            {favorites.length > 0 && (
              <span className="absolute -top-1 -right-1 bg-red-600 text-white text-[9px] font-bold w-4 h-4 rounded-full flex items-center justify-center font-mono">
                {favorites.length}
              </span>
            )}
          </button>

          {/* Alarm bell for notifications */}
          <button 
            onClick={() => setShowNotificationDrawer(!showNotificationDrawer)}
            className="p-2 bg-white/[0.03] border border-white/5 hover:bg-zinc-900 rounded-full transition relative cursor-pointer text-zinc-400 hover:text-white"
            title="Notificações"
          >
            <Bell className="w-4 h-4" />
            {userNotifications.length > 0 && (
              <span className="absolute top-1.5 right-1.5 w-1.5 h-1.5 bg-red-600 rounded-full animate-pulse" />
            )}
          </button>

          {/* Profile Quick menu dropdown details */}
          <div className="relative group">
            <button className="flex items-center gap-2 p-1 bg-zinc-950 hover:bg-zinc-900 rounded-full border border-white/5 transition pr-3 select-none">
              <div className={`w-7 h-7 ${currentProfile.avatarColor || 'bg-red-600'} rounded-full text-xs font-extrabold flex items-center justify-center uppercase text-white shadow shadow-black`}>
                {profileChar}
              </div>
              <span className="text-xs font-bold text-zinc-300 hidden sm:inline max-w-24 truncate">{currentProfile.name}</span>
            </button>

            {/* Dropdown container */}
            <div className="absolute right-0 top-10 pointer-events-none group-hover:pointer-events-auto opacity-0 group-hover:opacity-100 transition-all duration-200 mt-2 w-48 bg-zinc-950 border border-zinc-900 rounded-xl shadow-2xl p-2 z-50 flex flex-col gap-1">
              <Link
                to="/app/minha-conta"
                className="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-zinc-400 hover:text-white hover:bg-zinc-900 rounded-lg transition"
              >
                <Settings className="w-4 h-4" />
                <span>Minha Conta</span>
              </Link>
              <Link
                to="/app/perfis"
                className="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-zinc-400 hover:text-white hover:bg-zinc-900 rounded-lg transition"
              >
                <Users className="w-4 h-4" />
                <span>Trocar Perfil</span>
              </Link>
              <Link
                to="/app/dispositivos"
                className="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-zinc-400 hover:text-white hover:bg-zinc-900 rounded-lg transition"
              >
                <Tv className="w-4 h-4" />
                <span>Gerenciar Dispositivos</span>
              </Link>
              <div className="h-px bg-zinc-900 my-1" />
              <button
                onClick={() => {
                  logout();
                  navigate('/landing');
                }}
                className="w-full flex items-center gap-2 px-3 py-2 text-xs font-black text-red-500 hover:text-white hover:bg-red-950/20 rounded-lg transition text-left cursor-pointer font-mono uppercase"
              >
                <LogOut className="w-4 h-4" />
                <span>Logout Sair</span>
              </button>
            </div>
          </div>

        </div>
      </header>

      {/* Mobile search bar (under headers) */}
      <div className="p-3.5 md:hidden border-b border-white/5 bg-[#050505] flex gap-2">
        <form onSubmit={handleSearchSubmit} className="relative flex-1">
          <Search className="absolute left-3 top-2.5 w-4 h-4 text-zinc-500" />
          <input
            type="text"
            value={searchVal}
            onChange={handleSearchChange}
            placeholder="Buscar títulos, categorias..."
            className="w-full bg-[#0a0a0a] border border-white/5 focus:border-red-600 rounded-lg pl-9 pr-8 py-2 text-xs outline-none text-white font-medium"
          />
          {searchVal && (
            <button 
              type="button" 
              onClick={handleClearSearch} 
              className="absolute right-3 top-2.5 text-zinc-550"
            >
              <X className="w-4 h-4" />
            </button>
          )}
        </form>
      </div>

      {/* MOBILE LOWER NAVIGATION BAR */}
      <div className="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-zinc-950/95 border-t border-zinc-900 py-2.5 px-2 flex justify-around text-[9px] font-mono font-bold uppercase tracking-wider text-zinc-400">
        <Link to="/app" className="flex flex-col items-center gap-1 hover:text-white">
          <Grid className="w-4 h-4" />
          <span>Início</span>
        </Link>
        <Link to="/app/ao-vivo" className="flex flex-col items-center gap-1 text-red-500 hover:text-red-400 relative">
          <span className="absolute -top-1 -right-1 w-1.5 h-1.5 bg-red-600 rounded-full animate-ping" />
          <Play className="w-4 h-4 fill-current text-red-500" />
          <span>Ao Vivo</span>
        </Link>
        <Link to="/app/programacao" className="flex flex-col items-center gap-1 hover:text-white">
          <Grid className="w-4 h-4" />
          <span>Agenda</span>
        </Link>
        <Link to="/app/minha-lista" className="flex flex-col items-center gap-1 hover:text-white">
          <Heart className="w-4 h-4" />
          <span>Lista</span>
        </Link>
        <Link to="/app/minha-conta" className="flex flex-col items-center gap-1 hover:text-white">
          <Settings className="w-4 h-4" />
          <span>Conta</span>
        </Link>
      </div>

      {/* Main Routed views */}
      <main className="flex-1 pb-20 lg:pb-8">
        <Outlet />
      </main>

      {/* NOTIFICATIONS SLIDE PANEL DRAWER */}
      {showNotificationDrawer && (
        <>
          <div className="fixed inset-0 bg-black/60 z-40" onClick={() => setShowNotificationDrawer(false)} />
          <div id="sub-notifications-drawer" className="fixed inset-y-0 right-0 w-full sm:max-w-md bg-zinc-950 border-l border-zinc-900 shadow-2xl z-50 p-6 flex flex-col justify-between font-sans text-white animate-fade-in">
            <div className="flex flex-col gap-6 overflow-y-auto flex-1">
              <div className="flex justify-between items-center border-b border-zinc-900 pb-4">
                <h2 className="text-lg font-black tracking-tight flex items-center gap-2">
                  <Bell className="w-5 h-5 text-[#ef4444]" />
                  <span>Notificações Internas</span>
                </h2>
                <button 
                  onClick={() => setShowNotificationDrawer(false)}
                  className="p-1 hover:bg-zinc-900 rounded-full text-zinc-500 hover:text-white cursor-pointer"
                >
                  <X className="w-5 h-5" />
                </button>
              </div>

              {userNotifications.length === 0 ? (
                <div className="flex flex-col items-center justify-center p-10 text-center text-zinc-650 text-xs gap-2">
                  <AlertCircle className="w-7 h-7 text-zinc-800" />
                  <p>Nenhuma notificação nova no momento.</p>
                </div>
              ) : (
                <div className="flex flex-col gap-3">
                  {userNotifications.map((n) => (
                    <div 
                      key={n.id}
                      className="p-4 bg-zinc-900 border border-zinc-850 rounded-xl"
                    >
                      <div className="flex justify-between items-center mb-1">
                        <span className="text-[10px] font-mono font-bold text-[#ef4444] tracking-wider uppercase">F5 TV REPORT</span>
                        <span className="text-[9px] font-mono text-zinc-500">{new Date(n.createdAt).toLocaleDateString()}</span>
                      </div>
                      <h4 className="font-bold text-sm text-zinc-100 mb-1">{n.title}</h4>
                      <p className="text-zinc-400 text-xs leading-relaxed font-semibold">{n.message}</p>
                    </div>
                  ))}
                </div>
              )}
            </div>
          </div>
        </>
      )}

      {/* Footer F5 */}
      <footer className="border-t border-white/5 py-8 text-center text-zinc-600 font-semibold text-xs font-mono max-w-7xl mx-auto w-full px-6 mb-12 lg:mb-0">
        <p>© 2026 F5 TV | Premium streaming brasileiro. Todos os direitos reservados.</p>
      </footer>
    </div>
  );
};

export default SubscriberLayout;

import React from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';
import AdminPanel from '../../components/AdminPanel';

interface AdminPageWrapperProps {
  tab: 'dashboard' | 'usuarios' | 'conteudo' | 'series' | 'uploads' | 'financeiro' | 'planos';
}

export const AdminPageWrapper: React.FC<AdminPageWrapperProps> = ({ tab }) => {
  const { currentUser, logout } = useAuth();
  const navigate = useNavigate();

  if (!currentUser) return null;

  // Enforce correct permissions role checks
  const canAccess = (t: string) => {
    if (currentUser.role === 'admin') return true;
    if (currentUser.role === 'editor' && ['dashboard', 'conteudo', 'series', 'uploads'].includes(t)) return true;
    if (currentUser.role === 'finance' && ['dashboard', 'financeiro', 'planos', 'usuarios'].includes(t)) return true;
    return false;
  };

  if (!canAccess(tab)) {
    return (
      <div className="min-h-screen bg-zinc-950 text-white flex flex-col items-center justify-center p-6 text-center select-none font-sans">
        <div className="w-16 h-16 bg-red-950/20 border border-red-900 text-red-550 flex items-center justify-center rounded-full text-lg font-bold mb-4">
          !
        </div>
        <h2 className="text-xl font-black uppercase tracking-tight text-white mb-2">Acesso Restrito / Bloqueado</h2>
        <p className="text-zinc-500 text-xs font-semibold leading-relaxed max-w-md">
          Seu perfil atual de login (<strong className="text-red-500 uppercase">{currentUser.role}</strong>) não possui as chaves de acesso exigidas para acessar o painel de <strong>{tab === 'conteudo' ? 'conteúdos' : tab}</strong>.
        </p>
        <button
          onClick={() => navigate('/admin')}
          className="mt-6 px-5 py-2.5 bg-[#ef4444] hover:bg-red-700 text-white text-xs font-bold font-mono uppercase tracking-wider rounded-xl transition"
        >
          Voltar ao Dashboard
        </button>
      </div>
    );
  }

  const handleTabChange = (newTab: 'dashboard' | 'usuarios' | 'conteudo' | 'series' | 'uploads' | 'financeiro' | 'planos') => {
    let path = '/admin';
    if (newTab === 'usuarios') path = '/admin/usuarios';
    else if (newTab === 'conteudo') path = '/admin/conteudos';
    else if (newTab === 'series') path = '/admin/series';
    else if (newTab === 'financeiro') path = '/admin/financeiro';
    navigate(path);
  };

  const handleLogout = () => {
    logout();
    navigate('/landing');
  };

  return (
    <div className="animate-fade-in min-h-screen w-full select-none">
      <AdminPanel
        currentUser={currentUser}
        activeTabOverride={tab}
        onTabChange={handleTabChange}
        onNavigateToUserApp={() => navigate('/app')}
        onLogout={handleLogout}
      />
    </div>
  );
};

export default AdminPageWrapper;

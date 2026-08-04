import React from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';
import AdminPanel from '../../components/AdminPanel';

type AdminTab = 
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

interface AdminPageWrapperProps {
  tab: AdminTab;
}

export const AdminPageWrapper: React.FC<AdminPageWrapperProps> = ({ tab }) => {
  const { currentUser, logout } = useAuth();
  const navigate = useNavigate();

  if (!currentUser) return null;

  // Enforce correct permissions role checks
  const canAccess = (t: AdminTab) => {
    if (currentUser.role === 'admin') return true;
    if (currentUser.role === 'editor' && ['dashboard', 'conteudo', 'series', 'temporadas', 'episodios', 'uploads', 'programacao', 'canais', 'avaliacoes', 'midia'].includes(t)) return true;
    if (currentUser.role === 'finance' && ['dashboard', 'financeiro', 'planos', 'assinantes', 'relatorios', 'cupons'].includes(t)) return true;
    return false;
  };

  if (!canAccess(tab)) {
    return (
      <div className="min-h-screen bg-zinc-950 text-white flex flex-col items-center justify-center p-6 text-center select-none font-sans">
        <div className="w-16 h-16 bg-red-950/20 border border-red-900 text-red-500 flex items-center justify-center rounded-full text-lg font-bold mb-4">
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

  const handleTabChange = (newTab: AdminTab) => {
    let path = '/admin';
    if (newTab === 'usuarios') path = '/admin/usuarios';
    else if (newTab === 'assinantes') path = '/admin/assinantes';
    else if (newTab === 'conteudo') path = '/admin/conteudos';
    else if (newTab === 'series') path = '/admin/series';
    else if (newTab === 'temporadas') path = '/admin/temporadas';
    else if (newTab === 'episodios') path = '/admin/episodios';
    else if (newTab === 'uploads') path = '/admin/uploads';
    else if (newTab === 'financeiro') path = '/admin/financeiro';
    else if (newTab === 'planos') path = '/admin/planos';
    else if (newTab === 'banners') path = '/admin/banners';
    else if (newTab === 'relatorios') path = '/admin/relatorios';
    else if (newTab === 'configuracoes') path = '/admin/configuracoes';
    else if (newTab === 'programacao') path = '/admin/programacao';
    else if (newTab === 'canais') path = '/admin/canais';
    else if (newTab === 'cupons') path = '/admin/cupons';
    else if (newTab === 'avaliacoes') path = '/admin/avaliacoes';
    else if (newTab === 'midia') path = '/admin/midia';
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
        onTabChange={handleTabChange as any}
        onNavigateToUserApp={() => navigate('/app')}
        onLogout={handleLogout}
      />
    </div>
  );
};

export default AdminPageWrapper;

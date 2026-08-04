import React, { useState } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';
import { useData } from '../../context/DataContext';
import { 
  User, ShieldCheck, CreditCard, RefreshCw, Trash2, ArrowLeft,
  Users, Key, AlertTriangle, CheckCircle, Smartphone 
} from 'lucide-react';

export const MyAccountPage: React.FC = () => {
  const { currentUser, currentProfile, updateUser, selectProfile, logout } = useAuth();
  const { plans, payments } = useData();
  const navigate = useNavigate();
  const effectiveProfile = currentProfile || {
    name: `${currentUser?.name?.split(' ')[0] || 'Staff'} (Admin)`,
    avatarColor: 'bg-red-600'
  };

  const [newPassword, setNewPassword] = useState('');
  const [showPasswordForm, setShowPasswordForm] = useState(false);
  const [securitySuccess, setSecuritySuccess] = useState('');
  const [simulatedCancelSuccess, setSimulatedCancelSuccess] = useState(false);

  if (!currentUser) {
    return null;
  }

  if (currentUser.role === 'subscriber' && !currentProfile) {
    return null;
  }

  const currentPlan = plans.find((p) => p.id === currentUser.planId) || plans[0];

  const handleUpdatePassword = (e: React.FormEvent) => {
    e.preventDefault();
    if (newPassword.trim().length < 6) return;

    setSecuritySuccess('Sua senha foi redefinida com sucesso no ambiente simulado!');
    setNewPassword('');
    setTimeout(() => {
      setSecuritySuccess('');
      setShowPasswordForm(false);
    }, 4000);
  };

  const handleCancelSubscription = () => {
    const confirm = window.confirm('Deseja realmente cancelar sua assinatura fictícia da F5 TV?');
    if (confirm) {
      const updatedUser = {
        ...currentUser,
        status: 'canceled' as const,
      };
      updateUser(updatedUser);
      setSimulatedCancelSuccess(true);
    }
  };

  const handleActivateSubscription = () => {
    const updatedUser = {
      ...currentUser,
      status: 'active' as const,
    };
    updateUser(updatedUser);
    setSimulatedCancelSuccess(false);
  };

  // Resolve user transaction payments
  const userPayments = payments.filter((p) => p.userId === currentUser.id);

  return (
    <div id="my-account-page" className="max-w-4xl mx-auto px-6 md:px-8 pt-10 animate-fade-in text-white font-sans">
      
      {/* Top Banner Navigation */}
      <div className="border-b border-zinc-900 pb-5 mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
          <span className="text-xs font-mono font-black text-[#ef4444] uppercase tracking-widest">Painel de Segurança</span>
          <h1 className="text-2xl md:text-3xl font-black text-white mt-1">Gerenciamento da Conta</h1>
        </div>
        <button
          onClick={() => navigate('/app/perfis')}
          className="flex items-center gap-1.5 text-zinc-400 hover:text-white transition text-xs font-mono font-bold uppercase cursor-pointer"
        >
          <Users className="w-4 h-4 text-red-500" />
          <span>Trocar Perfil</span>
        </button>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
        
        {/* Left menu column */}
        <div className="md:col-span-1 flex flex-col gap-4">
          
          {/* User overview stats */}
          <div className="bg-zinc-950 border border-zinc-900 rounded-2xl p-6 text-center flex flex-col items-center gap-3">
            <div className={`w-14 h-14 ${effectiveProfile.avatarColor || 'bg-red-600'} rounded-full text-xl font-black flex items-center justify-center uppercase shadow-lg border border-black`}>
              {effectiveProfile.name ? effectiveProfile.name.charAt(0) : 'U'}
            </div>
            
            <div className="flex flex-col">
              <span className="font-extrabold text-sm text-zinc-100">{currentUser.name}</span>
              <span className="text-zinc-550 text-[11px] font-mono mt-0.5">{currentUser.email}</span>
            </div>

            <span className="text-[9px] bg-red-600/10 text-[#ef4444] border border-red-950 px-2 py-0.5 rounded font-mono font-black uppercase tracking-wider">
              {currentUser.role === 'subscriber' ? 'Plano de Assinatura' : currentUser.role}
            </span>
          </div>

          <div className="bg-zinc-950 border border-zinc-900 rounded-2xl p-4 flex flex-col gap-2 font-mono text-[10px] uppercase font-bold text-zinc-500">
            <span className="px-2 pb-2 border-b border-zinc-90 w-full text-zinc-450 font-black">Links Rápidos</span>
            <Link to="/app" className="flex items-center gap-2 hover:text-[#ef4444] hover:bg-zinc-900 p-2 rounded-lg transition text-left">
              <span>• Voltar ao Catálogo</span>
            </Link>
            <Link to="/app/minha-lista" className="flex items-center gap-2 hover:text-[#ef4444] hover:bg-zinc-900 p-2 rounded-lg transition text-left">
              <span>• Ver Itens Salvos</span>
            </Link>
          </div>

        </div>

        {/* Right content column */}
        <div className="md:col-span-2 flex flex-col gap-6">
          
          {/* Active plan status */}
          <div className="bg-zinc-950 border border-zinc-900 rounded-2xl p-6 flex flex-col gap-4">
            <div className="flex justify-between items-center pb-4 border-b border-zinc-900">
              <div className="flex items-center gap-2">
                <CreditCard className="w-5 h-5 text-[#ef4444]" />
                <h2 className="text-sm font-mono font-black uppercase text-zinc-100">Configuração da Assinatura</h2>
              </div>
              <span className={`text-[9px] font-mono font-bold uppercase tracking-widest px-2 py-0.5 border rounded ${
                currentUser.status === 'active' 
                  ? 'bg-emerald-950 border-emerald-900 text-emerald-400' 
                  : 'bg-rose-950/20 border-rose-900 text-rose-500'
              }`}>
                {currentUser.status === 'active' ? 'Ativo' : 'Cancelado'}
              </span>
            </div>

            <div className="flex flex-col gap-1.5 text-left">
              <span className="text-zinc-500 text-[10px] font-mono tracking-wider font-extrabold uppercase">PLANO ATUAL</span>
              <h3 className="text-base font-black text-white">{currentPlan?.name || 'Fienticial Premium Plan'}</h3>
              <p className="text-xs text-zinc-500 font-semibold leading-normal">
                Acesso irrestrito a todas as séries brasileiras originais F5, notícias táticas ao vivo 24h e painéis Dolby Atmos de cinema.
              </p>
            </div>

            <div className="grid grid-cols-2 gap-4 bg-zinc-900/40 border border-zinc-900/60 p-4 rounded-xl font-mono text-[11px] font-bold mt-2 text-zinc-400">
              <div className="flex flex-col gap-1">
                <span className="text-zinc-600 text-[9px] uppercase font-black">Próxima renovação</span>
                <span className="text-zinc-300">01/06/2026</span>
              </div>
              <div className="flex flex-col gap-1">
                <span className="text-zinc-600 text-[9px] uppercase font-black">Preço Mensal</span>
                <span className="text-white">R$ {currentPlan?.price ? currentPlan.price.toFixed(2) : '39.90'}</span>
              </div>
            </div>

            {/* Simulated actions */}
            <div className="flex flex-wrap gap-3 mt-2">
              {currentUser.status === 'active' ? (
                <button
                  onClick={handleCancelSubscription}
                  className="bg-rose-950/20 hover:bg-rose-950/45 text-rose-500 hover:text-white border border-rose-900 rounded-xl px-4 py-2.5 text-xs font-bold font-mono uppercase tracking-wider flex items-center gap-1.5 transition cursor-pointer"
                >
                  <Trash2 className="w-4 h-4" />
                  <span>Cancelar Assinatura</span>
                </button>
              ) : (
                <button
                  onClick={handleActivateSubscription}
                  className="bg-emerald-950 border border-emerald-900 hover:bg-emerald-900 text-emerald-400 hover:text-white rounded-xl px-5 py-2.5 text-xs font-bold font-mono uppercase tracking-wider flex items-center gap-1.5 transition cursor-pointer"
                >
                  <RefreshCw className="w-4 h-4" />
                  <span>Reativar Assinatura</span>
                </button>
              )}
              <button
                onClick={() => alert(`Você já está aproveitando os melhores canais do plano ${currentPlan?.name}!`)}
                className="bg-zinc-900 hover:bg-zinc-850 text-zinc-300 hover:text-white rounded-xl border border-zinc-850 px-4 py-2.5 text-xs font-bold font-mono uppercase tracking-wider flex items-center gap-1.5 transition cursor-pointer"
              >
                <RefreshCw className="w-4 h-4 text-zinc-550" />
                <span>Mudar Plano</span>
              </button>
            </div>
          </div>

          {/* Account Security Change password */}
          <div className="bg-zinc-950 border border-zinc-900 rounded-2xl p-6 flex flex-col gap-4">
            <div className="flex justify-between items-center pb-4 border-b border-zinc-900">
              <div className="flex items-center gap-2">
                <Key className="w-5 h-5 text-[#ef4444]" />
                <h2 className="text-sm font-mono font-black uppercase text-zinc-100">Redefinir Credenciais</h2>
              </div>
            </div>

            {securitySuccess && (
              <div className="p-4 bg-emerald-950/40 border border-emerald-900 rounded-xl text-xs text-emerald-400 font-bold font-mono">
                {securitySuccess}
              </div>
            )}

            {showPasswordForm ? (
              <form onSubmit={handleUpdatePassword} className="flex flex-col gap-4">
                <div className="flex flex-col gap-1.5">
                  <label className="text-xs font-semibold text-zinc-400">Nova Senha de Acesso</label>
                  <div className="flex gap-2">
                    <input
                      type="password"
                      required
                      minLength={6}
                      value={newPassword}
                      onChange={(e) => setNewPassword(e.target.value)}
                      placeholder="Mínimo 6 caracteres"
                      className="flex-1 bg-zinc-900 border border-zinc-850 rounded-xl px-3 py-2 text-xs outline-none focus:border-red-600 text-white font-medium"
                    />
                    <button
                      type="submit"
                      className="bg-[#ef4444] hover:bg-red-700 text-white font-mono font-bold px-4 rounded-xl text-xs uppercase cursor-pointer transition"
                    >
                      Salvar
                    </button>
                    <button
                      type="button"
                      onClick={() => setShowPasswordForm(false)}
                      className="bg-zinc-900 border border-zinc-800 text-zinc-450 hover:text-white font-mono px-4 rounded-xl text-xs cursor-pointer transition"
                    >
                      Cancelar
                    </button>
                  </div>
                </div>
              </form>
            ) : (
              <button
                onClick={() => setShowPasswordForm(true)}
                className="self-start text-[#ef4444] hover:text-red-500 transition text-xs font-mono font-black uppercase tracking-wider flex items-center gap-1 mt-1 cursor-pointer"
              >
                <span>&gt; Clique para alterar senha de teste</span>
              </button>
            )}
          </div>

          {/* Payment simulations transaction history */}
          <div className="bg-zinc-950 border border-zinc-900 rounded-2xl p-6 flex flex-col gap-4">
            <h2 className="text-sm font-mono font-black uppercase text-zinc-100 pb-4 border-b border-zinc-900 flex items-center gap-2">
              <Smartphone className="w-5 h-5 text-[#ef4444]" />
              <span>Extrato de Pagamentos Simulados</span>
            </h2>

            {userPayments.length === 0 ? (
              <div className="p-6 bg-zinc-900/30 border border-zinc-900 border-dashed rounded-xl flex flex-col items-center justify-center text-center text-zinc-650 text-xs gap-2 font-mono">
                <AlertTriangle className="w-6 h-6 text-zinc-805" />
                <p>Nenhum recibo de transação simulada registrada ainda.</p>
              </div>
            ) : (
              <div className="overflow-x-auto">
                <table className="w-full text-left text-xs font-semibold">
                  <thead>
                    <tr className="border-b border-zinc-900 text-zinc-600 font-mono text-[9px] uppercase">
                      <th className="py-2.5 pr-2">Recibo ID</th>
                      <th className="py-2.5 px-2">Data Pgto</th>
                      <th className="py-2.5 px-2 text-center">Método</th>
                      <th className="py-2.5 pl-2 text-right">Valor</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-zinc-950 text-zinc-450 font-mono">
                    {userPayments.map((p) => (
                      <tr key={p.id} className="hover:bg-zinc-900/10">
                        <td className="py-2.5 pr-2 text-zinc-350 truncate max-w-28 font-bold">{p.id}</td>
                        <td className="py-2.5 px-2 font-medium">{new Date(p.createdAt).toLocaleDateString()}</td>
                        <td className="py-2.5 px-2 text-center font-bold text-zinc-500 uppercase">{p.method}</td>
                        <td className="py-2.5 pl-2 text-right text-emerald-400 font-bold">R$ {p.amount.toFixed(2)}</td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            )}
          </div>

        </div>

      </div>

    </div>
  );
};

export default MyAccountPage;

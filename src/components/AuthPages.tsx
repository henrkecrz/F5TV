/**
 * @license
 * SPDX-License-Identifier: Apache-2.0
 */

import React, { useState } from 'react';
import { User, Plan, Profile } from '../types';
import { db } from '../data/mockDatabase';
import { ShieldAlert, UserPlus, LogIn, Key, Mail, Phone, User as UserIcon, Plus, Check } from 'lucide-react';

interface LoginScreenProps {
  onLoginSuccess: (user: User) => void;
  onNavigate: (route: string, extra?: any) => void;
  preselectedEmail?: string;
}

export function LoginScreen({ onLoginSuccess, onNavigate, preselectedEmail = '' }: LoginScreenProps) {
  const [email, setEmail] = useState(preselectedEmail);
  const [password, setPassword] = useState('123456'); // Default default password
  const [error, setError] = useState('');

  // Auto-sync email when credentials clicked from footer
  React.useEffect(() => {
    if (preselectedEmail) {
      setEmail(preselectedEmail);
    }
  }, [preselectedEmail]);

  const handleLoginSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setError('');

    if (!email) {
      setError('Por favor, informe seu e-mail corporativo ou de assinante.');
      return;
    }

    const users = db.getUsers();
    // Simple mock auth matching
    const found = users.find(u => u.email.toLowerCase().trim() === email.toLowerCase().trim());
    
    if (!found) {
      setError('E-mail não cadastrado. Teste com um dos botões rápidos do rodapé para login automático.');
      return;
    }

    if (found.status === 'blocked') {
      setError('Esta conta foi bloqueada por razões de segurança. Por favor, contate o administrador.');
      return;
    }

    // Success! Update lastLogin
    const updatedUsers = users.map(u => u.id === found.id ? { ...u, lastLogin: new Date().toISOString() } : u);
    db.setUsers(updatedUsers);

    onLoginSuccess(found);
  };

  const selectAutoUser = (autoEmail: string) => {
    setEmail(autoEmail);
    setPassword('123456');
    setError('');
  };

  return (
    <div id="auth-login-screen" className="min-h-screen bg-black flex items-center justify-center p-6 text-white font-sans relative selection:bg-[#ef4444] selection:text-white">
      <div className="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1536440136628-849c177e76a1?q=80&w=1200')] bg-cover bg-center opacity-10" />

      <div className="w-full max-w-md bg-zinc-950 border border-zinc-900 rounded-2xl p-8 shadow-2xl relative z-10">
        <div className="flex flex-col items-center gap-2 mb-8 text-center">
          <span className="text-3xl font-black tracking-tighter uppercase cursor-pointer" onClick={() => onNavigate('/')}>
            F5 <span className="text-[#ef4444]">TV</span>
          </span>
          <p className="text-zinc-400 text-sm font-medium">Faça login para entrar na área exclusiva.</p>
        </div>

        {error && (
          <div className="p-3 bg-red-950/60 border border-red-900 rounded-lg text-xs leading-relaxed text-rose-300 mb-6 flex items-start gap-2">
            <ShieldAlert className="w-4 h-4 text-[#ef4444] shrink-0" />
            <span>{error}</span>
          </div>
        )}

        <form onSubmit={handleLoginSubmit} className="flex flex-col gap-4 font-normal text-sm">
          <div className="flex flex-col gap-1.5">
            <label className="text-xs font-mono font-bold text-zinc-400 uppercase">E-mail corporativo ou assinante</label>
            <div className="relative">
              <Mail className="absolute left-3.5 top-3 w-4 h-4 text-zinc-500" />
              <input
                id="login-email-input"
                type="email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                placeholder="nome@f5tv.com.br ou henrikeaps@gmail.com"
                className="w-full bg-zinc-900 border border-zinc-800 focus:border-[#ef4444] rounded-lg pl-10 pr-4 py-2.5 text-zinc-100 placeholder:text-zinc-600 outline-none transition"
              />
            </div>
          </div>

          <div className="flex flex-col gap-1.5">
            <div className="flex justify-between items-center">
              <label className="text-xs font-mono font-bold text-zinc-400 uppercase">Sua senha tática</label>
              <button 
                type="button" 
                onClick={() => onNavigate('/recuperar-senha')} 
                className="text-xs text-[#ef4444] hover:underline"
              >
                Esqueceu?
              </button>
            </div>
            <div className="relative">
              <Key className="absolute left-3.5 top-3 w-4 h-4 text-zinc-500" />
              <input
                id="login-password-input"
                type="password"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                placeholder="◦◦◦◦◦◦"
                className="w-full bg-zinc-900 border border-zinc-800 focus:border-[#ef4444] rounded-lg pl-10 pr-4 py-2.5 text-zinc-100 placeholder:text-zinc-600 outline-none transition"
              />
            </div>
          </div>

          <button 
            id="login-submit-btn"
            type="submit" 
            className="w-full bg-[#ef4444] hover:bg-red-700 text-white font-bold py-3 px-4 rounded-lg mt-2 flex items-center justify-center gap-2 cursor-pointer transition shadow-lg shadow-red-900/30"
          >
            <LogIn className="w-4 h-4" />
            <span>Entrar com Segurança</span>
          </button>
        </form>

        <div className="border-t border-zinc-900/80 pt-6 mt-6 text-center text-xs">
          <p className="text-zinc-500 font-medium">
            Não é cadastrado?{' '}
            <button 
              onClick={() => onNavigate('/planos')} 
              className="text-[#ef4444] hover:underline font-bold"
            >
              Assine um Plano F5
            </button>
          </p>
        </div>

        {/* Demo Roles Shortcut box inside the login panel */}
        <div className="mt-8 pt-4 border-t border-zinc-900/60">
          <span className="text-[10px] font-mono font-black text-rose-500/80 uppercase block mb-3 text-center tracking-widest">
            SIMULAR CONTAS DE TESTE (MVP)
          </span>
          <div className="grid grid-cols-2 gap-2">
            <button 
              id="set-demo-admin"
              onClick={() => selectAutoUser('admin@f5tv.com.br')}
              className="px-2 py-1.5 bg-zinc-900 hover:bg-zinc-850 border border-zinc-800 hover:border-zinc-700 rounded text-[11px] font-medium text-zinc-300 text-left transition cursor-pointer"
            >
              • Administrador Geral
            </button>
            <button 
              id="set-demo-editor" 
              onClick={() => selectAutoUser('editor@f5tv.com.br')}
              className="px-2 py-1.5 bg-zinc-900 hover:bg-zinc-850 border border-zinc-800 hover:border-zinc-700 rounded text-[11px] font-medium text-zinc-300 text-left transition cursor-pointer"
            >
              • Editor de Conteúdo
            </button>
            <button 
              id="set-demo-finance" 
              onClick={() => selectAutoUser('financeiro@f5tv.com.br')}
              className="px-2 py-1.5 bg-zinc-900 hover:bg-zinc-850 border border-zinc-800 hover:border-zinc-700 rounded text-[11px] font-medium text-zinc-300 text-left transition cursor-pointer"
            >
              • Diretor Financeiro
            </button>
            <button 
              id="set-demo-subscriber" 
              onClick={() => selectAutoUser('henrikeaps@gmail.com')}
              className="px-2 py-1.5 bg-zinc-900 hover:bg-zinc-850 border border-zinc-800 hover:border-zinc-700 rounded text-[11px] font-medium text-zinc-300 text-left transition cursor-pointer"
            >
              • Assinante Premium
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}

interface RegisterScreenProps {
  plans: Plan[];
  onRegisterSuccess: (user: User) => void;
  onNavigate: (route: string, extra?: any) => void;
  preselectedPlanId?: string;
}

export function RegisterScreen({ plans, onRegisterSuccess, onNavigate, preselectedPlanId = 'plano-premium' }: RegisterScreenProps) {
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [phone, setPhone] = useState('(11) 99999-9999');
  const [password, setPassword] = useState('123456');
  const [planId, setPlanId] = useState(preselectedPlanId);
  const [paymentMethod, setPaymentMethod] = useState<'credit_card' | 'pix' | 'boleto'>('pix');
  const [error, setError] = useState('');

  const handleRegisterSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setError('');

    if (!name || !email) {
      setError('Por favor, digite seu nome completo e endereço de e-mail.');
      return;
    }

    const users = db.getUsers();
    if (users.some(u => u.email.toLowerCase().trim() === email.toLowerCase().trim())) {
      setError('Este e-mail já está em uso na plataforma F5 TV.');
      return;
    }

    // Process simulated subscription billing
    const selectedPlan = plans.find(p => p.id === planId);
    if (!selectedPlan) {
      setError('Plano selecionado inválido.');
      return;
    }

    // Create User record
    const newUserId = 'user-' + Math.random().toString(36).substring(2, 9);
    const newUser: User = {
      id: newUserId,
      name,
      email,
      phone,
      role: 'subscriber',
      planId: selectedPlan.id,
      status: 'active',
      createdAt: new Date().toISOString()
    };

    // Save user record
    db.setUsers([...users, newUser]);

    // Save profile record
    const profiles = db.getProfiles();
    const newProf: Profile = {
      id: 'prof-' + Math.random().toString(36).substring(2, 9),
      userId: newUserId,
      name: name.split(' ')[0] + ' F5',
      avatarColor: 'bg-red-650'
    };
    db.setProfiles([...profiles, newProf]);

    // Save subscription record
    const subscriptions = db.getSubscriptions();
    const subId = 'sub-' + Math.random().toString(36).substring(2, 9);
    const billingDate = new Date();
    billingDate.setMonth(billingDate.getMonth() + 1);
    
    db.setSubscriptions([
      ...subscriptions,
      {
        id: subId,
        userId: newUserId,
        planId: selectedPlan.id,
        status: 'active',
        startDate: new Date().toISOString().split('T')[0],
        nextBillingDate: billingDate.toISOString().split('T')[0],
        paymentMethod
      }
    ]);

    // Save visual payment record
    const payments = db.getPayments();
    db.setPayments([
      ...payments,
      {
        id: 'pay-' + Math.random().toString(36).substring(2, 9),
        userId: newUserId,
        subscriptionId: subId,
        value: selectedPlan.price,
        date: new Date().toISOString().split('T')[0],
        status: 'paid', // Instant fake payment success
        paymentMethod
      }
    ]);

    // Save a notification
    const notifications = db.getNotifications();
    db.setNotifications([
      ...notifications,
      {
        id: 'notif-' + Math.random().toString(36).substring(2, 9),
        userId: newUserId,
        title: 'Bem-vindo à F5 TV!',
        message: `Sua assinatura do plano ${selectedPlan.name} está ativa. Defina seu perfil e divirta-se!`,
        read: false,
        createdAt: new Date().toISOString()
      }
    ]);

    onRegisterSuccess(newUser);
  };

  return (
    <div id="auth-register-screen" className="min-h-screen bg-black flex items-center justify-center p-6 text-white font-sans relative selection:bg-[#ef4444]">
      <div className="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1574267431647-2da853839c06?q=80&w=1200')] bg-cover bg-center opacity-10" />

      <div className="w-full max-w-lg bg-zinc-950 border border-zinc-900 rounded-2xl p-8 shadow-2xl relative z-10 my-8">
        <div className="flex flex-col items-center gap-2 mb-8 text-center animate-fade-in">
          <span className="text-3xl font-black tracking-tighter uppercase cursor-pointer" onClick={() => onNavigate('/')}>
            F5 <span className="text-[#ef4444]">TV</span>
          </span>
          <h2 className="text-xl font-bold font-sans">Sua Assinatura Inteligente</h2>
          <p className="text-zinc-500 text-xs">Acesso ilimitado e imediato pós transação simulada.</p>
        </div>

        {error && (
          <div className="p-3 bg-red-950/60 border border-red-900 rounded-lg text-xs leading-relaxed text-rose-300 mb-6 flex items-start gap-2">
            <ShieldAlert className="w-4 h-4 text-[#ef4444] shrink-0" />
            <span>{error}</span>
          </div>
        )}

        <form onSubmit={handleRegisterSubmit} className="flex flex-col gap-5 text-sm">
          
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div className="flex flex-col gap-1.5">
              <label className="text-xs font-mono font-bold text-zinc-400 uppercase">Nome Completo</label>
              <div className="relative">
                <UserIcon className="absolute left-3.5 top-3 w-4 h-4 text-zinc-500" />
                <input
                  id="reg-name-input"
                  type="text"
                  required
                  value={name}
                  onChange={(e) => setName(e.target.value)}
                  placeholder="Seu nome completo"
                  className="w-full bg-zinc-900 border border-zinc-800 focus:border-[#ef4444] rounded-lg pl-10 pr-4 py-2.5 text-zinc-100 placeholder:text-zinc-650 outline-none transition"
                />
              </div>
            </div>

            <div className="flex flex-col gap-1.5">
              <label className="text-xs font-mono font-bold text-zinc-400 uppercase">Seu E-mail principal</label>
              <div className="relative">
                <Mail className="absolute left-3.5 top-3 w-4 h-4 text-zinc-500" />
                <input
                  id="reg-email-input"
                  type="email"
                  required
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  placeholder="exemplo@gmail.com"
                  className="w-full bg-zinc-900 border border-zinc-800 focus:border-[#ef4444] rounded-lg pl-10 pr-4 py-2.5 text-zinc-100 placeholder:text-zinc-650 outline-none transition"
                />
              </div>
            </div>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div className="flex flex-col gap-1.5">
              <label className="text-xs font-mono font-bold text-zinc-400 uppercase">Telefone de Contato</label>
              <div className="relative">
                <Phone className="absolute left-3.5 top-3 w-4 h-4 text-zinc-500" />
                <input
                  id="reg-phone-input"
                  type="text"
                  value={phone}
                  onChange={(e) => setPhone(e.target.value)}
                  placeholder="(11) 99999-9999"
                  className="w-full bg-zinc-900 border border-zinc-800 focus:border-[#ef4444] rounded-lg pl-10 pr-4 py-2.5 text-zinc-100 placeholder:text-zinc-650 outline-none transition"
                />
              </div>
            </div>

            <div className="flex flex-col gap-1.5">
              <label className="text-xs font-mono font-bold text-zinc-400 uppercase">Escolha sua Senha de Acesso</label>
              <div className="relative">
                <Key className="absolute left-3.5 top-3 w-4 h-4 text-zinc-500" />
                <input
                  id="reg-password-input"
                  type="password"
                  required
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  placeholder="******"
                  className="w-full bg-zinc-900 border border-zinc-800 focus:border-[#ef4444] rounded-lg pl-10 pr-4 py-2.5 text-zinc-100 placeholder:text-zinc-650 outline-none transition"
                />
              </div>
            </div>
          </div>

          <div className="border-t border-zinc-900/80 pt-4 flex flex-col gap-2.5">
            <label className="text-xs font-mono font-bold text-zinc-400 uppercase">Assinatura: Seleção do Plano</label>
            <div className="grid grid-cols-3 gap-2">
              {plans.map((pl) => (
                <button
                  key={pl.id}
                  type="button"
                  onClick={() => setPlanId(pl.id)}
                  className={`p-3 rounded-lg border text-left transition select-none flex flex-col gap-1 cursor-pointer ${
                    planId === pl.id 
                      ? 'border-[#ef4444] bg-red-955/10 text-white' 
                      : 'border-zinc-800 bg-zinc-900/60 text-zinc-400 hover:border-zinc-700'
                  }`}
                >
                  <span className="text-[10px] font-mono tracking-wider uppercase opacity-50 block">PLANO</span>
                  <span className="text-xs font-bold line-clamp-1">{pl.name}</span>
                  <span className="text-xs font-black text-white mt-1">R$ {pl.price.toFixed(2)}</span>
                </button>
              ))}
            </div>
          </div>

          <div className="flex flex-col gap-2">
            <label className="text-xs font-mono font-bold text-zinc-400 uppercase">Método de Cobrança (Simulação)</label>
            <div className="grid grid-cols-3 gap-2.5">
              {(['pix', 'credit_card', 'boleto'] as const).map((method) => {
                const isSel = paymentMethod === method;
                const labels = { pix: 'PIX Instantâneo', credit_card: 'Cartão Crédito', boleto: 'Boleto Bancário' };
                return (
                  <button
                    key={method}
                    type="button"
                    onClick={() => setPaymentMethod(method)}
                    className={`py-2 px-3 border rounded-lg text-xs font-medium cursor-pointer text-center transition ${
                      isSel 
                        ? 'border-[#ef4444] bg-zinc-900 text-white font-bold' 
                        : 'border-zinc-850 bg-zinc-900/30 text-zinc-500 hover:border-zinc-700'
                    }`}
                  >
                    {labels[method]}
                  </button>
                );
              })}
            </div>
          </div>

          <button 
            id="reg-submit-btn"
            type="submit" 
            className="w-full bg-[#ef4444] hover:bg-red-700 text-white font-bold py-3.5 px-4 rounded-lg mt-2 flex items-center justify-center gap-2 cursor-pointer transition shadow-lg shadow-red-900/30"
          >
            <UserPlus className="w-4 h-4" />
            <span>Faturar & Assistir Agora</span>
          </button>
        </form>

        <div className="border-t border-zinc-900/80 pt-6 mt-6 text-center text-xs text-zinc-500">
          <p className="font-medium">
            Já possui uma assinatura ativa?{' '}
            <button 
              onClick={() => onNavigate('/login')} 
              className="text-[#ef4444] hover:underline font-bold"
            >
              Fazer login
            </button>
          </p>
        </div>
      </div>
    </div>
  );
}

interface RecoveryScreenProps {
  onNavigate: (route: string) => void;
}

export function RecoveryScreen({ onNavigate }: RecoveryScreenProps) {
  const [email, setEmail] = useState('');
  const [submitted, setSubmitted] = useState(false);

  const handleRecoverySubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!email) return;
    setSubmitted(true);
  };

  return (
    <div id="auth-recovery-screen" className="min-h-screen bg-black flex items-center justify-center p-6 text-white font-sans relative selection:bg-[#ef4444]">
      <div className="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1536440136628-849c177e76a1?q=80&w=1200')] bg-cover bg-center opacity-10" />

      <div className="w-full max-w-sm bg-zinc-950 border border-zinc-900 rounded-2xl p-8 shadow-2xl relative z-10 text-center">
        <span className="text-3xl font-black tracking-tighter uppercase block mb-6 cursor-pointer" onClick={() => onNavigate('/')}>
          F5 <span className="text-[#ef4444]">TV</span>
        </span>

        {submitted ? (
          <div className="flex flex-col items-center gap-4">
            <div className="p-3 bg-emerald-950/60 border border-emerald-900 rounded-full text-emerald-400">
              <Check className="w-8 h-8" />
            </div>
            <h2 className="text-xl font-bold">Instruções enviadas!</h2>
            <p className="text-zinc-400 text-xs leading-relaxed max-w-xs mx-auto">
              Simulamos o envio de um e-mail de recuperação de credenciais para <strong className="text-white font-mono">{email}</strong>. Verifique sua caixa de entrada.
            </p>
            <button
              onClick={() => onNavigate('/login')}
              className="mt-4 w-full bg-zinc-800 hover:bg-zinc-700 text-white text-xs font-bold py-2.5 rounded-lg cursor-pointer"
            >
              Voltar ao Login
            </button>
          </div>
        ) : (
          <form onSubmit={handleRecoverySubmit} className="flex flex-col gap-4 text-left">
            <div className="flex flex-col mb-2 text-center items-center">
              <h2 className="text-base font-bold text-white uppercase font-mono tracking-widest">Recuperação de Senha</h2>
              <p className="text-zinc-500 text-xs max-w-xs mt-1 leading-normal">Informe o e-mail cadastrado na F5 TV que enviaremos o link imediato de reconfiguração de credenciais.</p>
            </div>

            <div className="flex flex-col gap-1.5 text-sm">
              <label className="text-xs font-mono font-bold text-zinc-400 uppercase">E-mail Cadastrado</label>
              <div className="relative">
                <Mail className="absolute left-3.5 top-3 w-4 h-4 text-zinc-500" />
                <input
                  id="recovery-email-input"
                  type="email"
                  required
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  placeholder="voce@exemplo.com"
                  className="w-full bg-zinc-900 border border-zinc-800 focus:border-[#ef4444] rounded-lg pl-10 pr-4 py-2.5 text-zinc-100 placeholder:text-zinc-650 outline-none transition"
                />
              </div>
            </div>

            <button
              id="recovery-submit-btn"
              type="submit"
              className="w-full bg-[#ef4444] hover:bg-red-700 text-white font-bold py-2.5 rounded-lg text-xs tracking-wider uppercase font-mono mt-2 cursor-pointer transition"
            >
              Enviar link simulado
            </button>
            <button
              type="button"
              onClick={() => onNavigate('/login')}
              className="w-full text-center text-zinc-500 hover:text-white text-xs transition"
            >
              Voltar ao Login
            </button>
          </form>
        )}
      </div>
    </div>
  );
}

interface ProfileSelectorScreenProps {
  user: User;
  onSelectProfile: (profile: Profile) => void;
  onNavigate: (route: string) => void;
}

export function ProfileSelectorScreen({ user, onSelectProfile, onNavigate }: ProfileSelectorScreenProps) {
  const [profiles, setProfiles] = useState<Profile[]>([]);
  const [newProfileName, setNewProfileName] = useState('');
  const [isAdding, setIsAdding] = useState(false);

  React.useEffect(() => {
    // Read from DB filtered for active user
    const dbProfs = db.getProfiles().filter(p => p.userId === user.id);
    if (dbProfs.length === 0) {
      // Auto seed if empty
      const defaultProf: Profile = {
        id: 'prof-' + user.id,
        userId: user.id,
        name: user.name.split(' ')[0],
        avatarColor: 'bg-red-650'
      };
      const allProfs = db.getProfiles();
      db.setProfiles([...allProfs, defaultProf]);
      setProfiles([defaultProf]);
    } else {
      setProfiles(dbProfs);
    }
  }, [user]);

  const handleAddProfile = (e: React.FormEvent) => {
    e.preventDefault();
    if (!newProfileName.trim()) return;

    if (profiles.length >= 6) {
      alert('Limite de 6 perfis por assinatura atingido no Plano Premium.');
      setIsAdding(false);
      return;
    }

    const tailcolors = ['bg-indigo-600', 'bg-emerald-600', 'bg-purple-600', 'bg-orange-600', 'bg-pink-600', 'bg-rose-600', 'bg-sky-600', 'bg-amber-600'];
    const randColor = tailcolors[Math.floor(Math.random() * tailcolors.length)];

    const newProf: Profile = {
      id: 'prof-' + Math.random().toString(36).substring(2, 9),
      userId: user.id,
      name: newProfileName,
      avatarColor: randColor
    };

    const allP = db.getProfiles();
    db.setProfiles([...allP, newProf]);

    setProfiles([...profiles, newProf]);
    setNewProfileName('');
    setIsAdding(false);
  };

  return (
    <div id="auth-profile-selector" className="min-h-screen bg-black text-white flex flex-col items-center justify-center p-6 font-sans relative">
      <div className="absolute top-6 left-6 flex items-center gap-1.5">
        <span className="text-xl font-black tracking-tighter uppercase">
          F5 <span className="text-[#ef4444]">TV</span>
        </span>
      </div>

      <div className="max-w-4xl w-full text-center flex flex-col items-center gap-10">
        <div className="flex flex-col gap-2">
          <h1 className="text-2xl md:text-4xl font-black tracking-tight font-sans">Quem está assistindo agora na F5 TV?</h1>
          <p className="text-zinc-500 text-sm font-medium">Cada perfil tem suas séries do peito, favoritos e histórico de reprodução individuais.</p>
        </div>

        <div className="flex flex-wrap items-center justify-center gap-8">
          {profiles.map((p) => {
            const firstLetter = p.name ? p.name.charAt(0).toUpperCase() : 'U';
            return (
              <div 
                key={p.id}
                onClick={() => onSelectProfile(p)}
                className="group flex flex-col items-center gap-3 cursor-pointer select-none"
              >
                <div className={`w-28 h-28 ${p.avatarColor || 'bg-zinc-805'} border-3 border-transparent group-hover:border-white rounded-2xl flex items-center justify-center text-4xl font-extrabold text-white shadow-lg transition transform group-hover:scale-105 duration-200`}>
                  {firstLetter}
                </div>
                <span className="text-sm font-bold text-zinc-400 group-hover:text-white transition duration-200">
                  {p.name}
                </span>
              </div>
            );
          })}

          {profiles.length < 6 && (
            <div className="flex flex-col items-center gap-3 select-none">
              {isAdding ? (
                <form onSubmit={handleAddProfile} className="w-28 h-28 bg-zinc-900 border border-zinc-800 rounded-2xl flex flex-col p-2.5 items-center justify-between text-xs font-normal">
                  <input
                    id="new-profile-name-input"
                    type="text"
                    required
                    maxLength={15}
                    value={newProfileName}
                    onChange={(e) => setNewProfileName(e.target.value)}
                    placeholder="Nome"
                    className="w-full bg-zinc-950 border border-zinc-800 focus:border-[#ef4444] p-1.5 text-center text-zinc-200 text-xs rounded outline-none"
                    autoFocus
                  />
                  <div className="flex gap-1 w-full justify-between">
                    <button
                      type="submit"
                      className="flex-1 bg-emerald-600 hover:bg-emerald-700 text-[10px] font-bold py-1 px-1 rounded text-center cursor-pointer"
                    >
                      Salvar
                    </button>
                    <button
                      type="button"
                      onClick={() => setIsAdding(false)}
                      className="flex-1 bg-zinc-800 hover:bg-zinc-700 text-[10px] py-1 px-1 rounded text-center cursor-pointer text-zinc-300"
                    >
                      Sair
                    </button>
                  </div>
                </form>
              ) : (
                <button 
                  onClick={() => setIsAdding(true)}
                  className="w-28 h-28 bg-zinc-900 border-2 border-dashed border-zinc-800 hover:border-zinc-600 hover:bg-zinc-850 rounded-2xl flex items-center justify-center text-zinc-500 hover:text-zinc-300 transition duration-200 cursor-pointer"
                  title="Criar Novo Perfil"
                >
                  <Plus className="w-8 h-8" />
                </button>
              )}
              <span className="text-sm font-bold text-zinc-500">Adicionar Perfil</span>
            </div>
          )}
        </div>

        <button 
          onClick={() => {
            // Simulated logout
            onNavigate('/');
          }}
          className="mt-6 border border-zinc-850 hover:bg-zinc-900/60 font-semibold text-xs py-2 px-6 rounded text-zinc-500 hover:text-zinc-300 transition uppercase tracking-wider font-mono cursor-pointer"
        >
          Sair da Conta Assinante
        </button>
      </div>
    </div>
  );
}

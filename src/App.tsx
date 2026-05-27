/**
 * @license
 * SPDX-License-Identifier: Apache-2.0
 */

import React, { useState, useEffect } from 'react';
import { User, Profile, Plan, Content } from './types';
import { db } from './data/mockDatabase';
import LandingPage from './components/LandingPage';
import { LoginScreen, RegisterScreen, RecoveryScreen, ProfileSelectorScreen } from './components/AuthPages';
import SubscriberApp from './components/SubscriberApp';
import AdminPanel from './components/AdminPanel';
import { Play, ShieldAlert, MonitorCheck, HelpCircle } from 'lucide-react';

export default function App() {
  const [currentRoute, setCurrentRoute] = useState<string>('/');
  const [currentUser, setCurrentUser] = useState<User | null>(null);
  const [currentProfile, setCurrentProfile] = useState<Profile | null>(null);

  // Form helpers
  const [preselectedEmail, setPreselectedEmail] = useState<string>('');
  const [preselectedPlanId, setPreselectedPlanId] = useState<string>('plano-premium');

  // Shared generic database state for landing display
  const [plans, setPlans] = useState<Plan[]>([]);
  const [contents, setContents] = useState<Content[]>([]);

  // Load from local storage
  useEffect(() => {
    // Read plans and content to pass matching specs
    setPlans(db.getPlans().filter(p => p.active));
    setContents(db.getContents().filter(c => c.status === 'published'));

    // Check if there is an active session in local storage for quick reload
    const storedUser = localStorage.getItem('f5_active_user');
    if (storedUser) {
      try {
        const parsed = JSON.parse(storedUser) as User;
        setCurrentUser(parsed);
        
        // Auto route matching role
        if (['admin', 'editor', 'finance'].includes(parsed.role)) {
          setCurrentRoute('/admin');
        } else {
          setCurrentRoute('/app/perfis');
        }
      } catch (err) {
        console.error('Failed to reload session', err);
      }
    }
  }, []);

  // Sync contents and plans changes when route changes
  useEffect(() => {
    setPlans(db.getPlans().filter(p => p.active));
    setContents(db.getContents().filter(c => c.status === 'published'));
  }, [currentRoute]);

  // Handle route switching
  const handleNavigate = (route: string, extra?: any) => {
    setCurrentRoute(route);
    if (route === '/login' && extra?.email) {
      setPreselectedEmail(extra.email);
    }
    if (route === '/cadastro' && extra?.planId) {
      setPreselectedPlanId(extra.planId);
    }
  };

  // Login event logic
  const handleLoginSuccess = (user: User) => {
    setCurrentUser(user);
    localStorage.setItem('f5_active_user', JSON.stringify(user));
    
    // Set email prefill back to empty
    setPreselectedEmail('');

    if (['admin', 'editor', 'finance'].includes(user.role)) {
      // Conduct staff to Admin panel
      setCurrentRoute('/admin');
    } else {
      // Conduct subscriber. First, force profile selection
      setCurrentProfile(null);
      setCurrentRoute('/app/perfis');
    }
  };

  // Sign out
  const handleLogout = () => {
    setCurrentUser(null);
    setCurrentProfile(null);
    localStorage.removeItem('f5_active_user');
    setCurrentRoute('/');
  };

  // Profile Click Success
  const handleProfileSelect = (prof: Profile) => {
    setCurrentProfile(prof);
    setCurrentRoute('/app');
  };

  // Quick switch between subscriber sandboxes and administrative controls
  const handleGoToStreamingApp = () => {
    if (currentUser) {
      setCurrentRoute('/app/perfis');
    } else {
      // Create guest subscriber on the fly for immediate try out!
      const guestUser: User = {
        id: 'user-guest',
        name: 'Henrique Convidado',
        email: 'henrikeaps@gmail.com',
        role: 'subscriber',
        planId: 'plano-premium',
        status: 'active',
        createdAt: new Date().toISOString()
      };
      // Write back so db filters work
      const allU = db.getUsers().filter(u => u.email !== guestUser.email);
      db.setUsers([guestUser, ...allU]);
      
      handleLoginSuccess(guestUser);
    }
  };

  const handleGoToAdminPanel = () => {
    if (currentUser && ['admin', 'editor', 'finance'].includes(currentUser.role)) {
      setCurrentRoute('/admin');
    } else {
      // Connect as Administrator role on the fly
      const adminUsr: User = {
        id: 'user-admin',
        name: 'Henrique Administrador',
        email: 'admin@f5tv.com.br',
        role: 'admin',
        status: 'active',
        createdAt: new Date().toISOString()
      };
      handleLoginSuccess(adminUsr);
    }
  };

  const handleFooterEmailSelect = (email: string) => {
    setPreselectedEmail(email);
    setCurrentRoute('/login');
    // Scroll window to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  return (
    <div id="f5-app-wrapper" className="min-h-screen bg-black text-white relative flex flex-col justify-between selection:bg-[#ef4444] selection:text-white">
      
      {/* 1. DEMO FLOATING TOOLBAR: Enables switching view contexts instantly */}
      <div 
        id="demo-mode-floating-bar" 
        className="fixed bottom-4 left-4 z-50 bg-zinc-950/95 border border-zinc-850 py-2.5 px-4 rounded-xl shadow-2xl flex items-center gap-4 text-xs font-mono font-bold max-w-sm sm:max-w-none backdrop-blur-sm"
      >
        <div className="flex items-center gap-1.5 shrink-0 text-[#ef4444] animate-pulse">
          <MonitorCheck className="w-4 h-4" />
          <span>F5 MODEL DE PLAYGROUND</span>
        </div>
        <div className="h-4 w-[1px] bg-zinc-800" />
        <div className="flex items-center gap-2">
          <button
            id="playground-btn-admin"
            onClick={handleGoToAdminPanel}
            className={`px-2.5 py-1 rounded text-[10px] uppercase transition cursor-pointer ${
              currentUser && ['admin', 'editor', 'finance'].includes(currentUser.role) && currentRoute === '/admin'
                ? 'bg-[#ef4444] text-white shadow'
                : 'bg-zinc-900 text-zinc-400 hover:text-white hover:bg-zinc-800'
            }`}
            title="Acessar o painel corporativo como Administrador Geral"
          >
            Aba Painel Admin
          </button>
          <button
            id="playground-btn-streaming"
            onClick={handleGoToStreamingApp}
            className={`px-2.5 py-1 rounded text-[10px] uppercase transition cursor-pointer ${
              currentUser && currentUser.role === 'subscriber' && ['/app', '/app/perfis'].includes(currentRoute)
                ? 'bg-[#ef4444] text-white shadow'
                : 'bg-zinc-900 text-zinc-400 hover:text-white hover:bg-zinc-800'
            }`}
            title="Acessar a visualização interna de streaming do Assinante"
          >
            Aba Streaming Client
          </button>
        </div>
      </div>

      {/* 2. DYNAMIC CONTENT RENDERING BASED ON MASTER ROUTE */}
      <div className="flex-1 flex flex-col justify-between">
        
        {/* PUBLIC WEBPAGE: Home / Landing */}
        {currentRoute === '/' && (
          <div className="animate-fade-in flex-1">
            <LandingPage 
              plans={plans} 
              contents={contents} 
              onNavigate={handleNavigate} 
            />
          </div>
        )}

        {/* AUTH PAGES */}
        {currentRoute === '/login' && (
          <div className="animate-fade-in flex-1">
            <LoginScreen 
              onLoginSuccess={handleLoginSuccess} 
              onNavigate={handleNavigate} 
              preselectedEmail={preselectedEmail}
            />
            {/* Direct injection of visual Footer so credentials work */}
            <div className="bg-black/80">
              <LandingPage plans={[]} contents={[]} onNavigate={handleNavigate} />
            </div>
          </div>
        )}

        {currentRoute === '/cadastro' && (
          <div className="animate-fade-in flex-1">
            <RegisterScreen 
              plans={plans} 
              onRegisterSuccess={handleLoginSuccess} 
              onNavigate={handleNavigate} 
              preselectedPlanId={preselectedPlanId}
            />
          </div>
        )}

        {currentRoute === '/recuperar-senha' && (
          <div className="animate-fade-in flex-1">
            <RecoveryScreen onNavigate={handleNavigate} />
          </div>
        )}

        {/* SECURE INTERNAL SUITE: PROFILE SELECTOR */}
        {currentRoute === '/app/perfis' && currentUser && (
          <div className="animate-fade-in flex-1">
            <ProfileSelectorScreen 
              user={currentUser} 
              onSelectProfile={handleProfileSelect} 
              onNavigate={handleNavigate}
            />
          </div>
        )}

        {/* SECURE INTERNAL SUITE: CATALOG STREAMING ENVIRONMENT */}
        {currentRoute === '/app' && currentUser && currentProfile && (
          <div className="animate-fade-in flex-1">
            <SubscriberApp 
              user={currentUser} 
              profile={currentProfile} 
              onLogout={handleLogout} 
              onNavigateToProfiles={() => setCurrentRoute('/app/perfis')}
            />
          </div>
        )}

        {/* CORPORATE ADMIN PANEL CONTROL HUB */}
        {currentRoute === '/admin' && currentUser && (
          <div className="animate-fade-in flex-1">
            <AdminPanel 
              currentUser={currentUser} 
              onLogout={handleLogout} 
              onNavigateToUserApp={handleGoToStreamingApp}
            />
          </div>
        )}

      </div>
    </div>
  );
}

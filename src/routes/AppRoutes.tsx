import React from 'react';
import { Routes, Route, Navigate } from 'react-router-dom';
import { ProtectedRoute } from './ProtectedRoute';
import { PublicLayout } from '../layouts/PublicLayout';
import { SubscriberLayout } from '../layouts/SubscriberLayout';

// Public pages
import { HomePage } from '../pages/public/HomePage';
import { LandingPageRoute } from '../pages/public/LandingPageRoute';
import { PlansPage } from '../pages/public/PlansPage';
import { SobrePage } from '../pages/public/SobrePage';
import { ContatoPage } from '../pages/public/ContatoPage';
import { TermosPage } from '../pages/public/TermosPage';
import { PrivacidadePage } from '../pages/public/PrivacidadePage';

// Auth pages
import { LoginPage } from '../pages/auth/LoginPage';
import { RegisterPage } from '../pages/auth/RegisterPage';
import { RecoveryPage } from '../pages/auth/RecoveryPage';

// Subscriber pages
import { ProfilesPage } from '../pages/subscriber/ProfilesPage';
import { AppHomePage } from '../pages/subscriber/AppHomePage';
import { ContentDetailsPage } from '../pages/subscriber/ContentDetailsPage';
import { WatchPage } from '../pages/subscriber/WatchPage';
import { MyListPage } from '../pages/subscriber/MyListPage';
import { ContinueWatchingPage } from '../pages/subscriber/ContinueWatchingPage';
import { MyAccountPage } from '../pages/subscriber/MyAccountPage';
import { SearchPage } from '../pages/subscriber/SearchPage';
import { AoVivoPage } from '../pages/subscriber/AoVivoPage';
import { ProgramacaoPage } from '../pages/subscriber/ProgramacaoPage';
import { DispositivosPage } from '../pages/subscriber/DispositivosPage';
import { CheckoutPage } from '../pages/subscriber/CheckoutPage';
import { CheckoutSucessoPage } from '../pages/subscriber/CheckoutSucessoPage';

// Admin pages
import { AdminPageWrapper } from '../pages/admin/AdminPageWrapper';

export const AppRoutes: React.FC = () => {
  return (
    <Routes>
      
      {/* 1. Public Facing Pages (With PublicLayout) */}
      <Route element={<PublicLayout />}>
        <Route path="/" element={<HomePage />} />
        <Route path="/landing" element={<LandingPageRoute />} />
        <Route path="/login" element={<LoginPage />} />
        <Route path="/cadastro" element={<RegisterPage />} />
        <Route path="/recuperar-senha" element={<RecoveryPage />} />
        <Route path="/planos" element={<PlansPage />} />
        <Route path="/sobre" element={<SobrePage />} />
        <Route path="/contato" element={<ContatoPage />} />
        <Route path="/termos" element={<TermosPage />} />
        <Route path="/privacidade" element={<PrivacidadePage />} />
      </Route>

      {/* 2. Premium Live Gate & Simulated Checkout Paths */}
      <Route 
        path="/checkout" 
        element={
          <ProtectedRoute allowedRoles={['subscriber', 'admin', 'editor', 'finance']}>
            <CheckoutPage />
          </ProtectedRoute>
        } 
      />
      <Route 
        path="/checkout/sucesso" 
        element={
          <ProtectedRoute allowedRoles={['subscriber', 'admin', 'editor', 'finance']}>
            <CheckoutSucessoPage />
          </ProtectedRoute>
        } 
      />

      {/* 3. Subscriber Area Setup (Perfis) - Requires Authenticated User */}
      <Route 
        path="/app/perfis" 
        element={
          <ProtectedRoute allowedRoles={['subscriber', 'admin', 'editor', 'finance']}>
            <ProfilesPage />
          </ProtectedRoute>
        } 
      />

      {/* 4. Fully Routed Subscriber Area (With SubscriberLayout) */}
      <Route 
        path="/app" 
        element={
          <ProtectedRoute allowedRoles={['subscriber', 'admin', 'editor', 'finance']} requiresProfile>
            <SubscriberLayout />
          </ProtectedRoute>
        }
      >
        <Route index element={<AppHomePage />} />
        <Route path="conteudo/:id" element={<ContentDetailsPage />} />
        <Route path="minha-lista" element={<MyListPage />} />
        <Route path="continuar-assistindo" element={<ContinueWatchingPage />} />
        <Route path="minha-conta" element={<MyAccountPage />} />
        <Route path="busca" element={<SearchPage />} />
        <Route path="ao-vivo" element={<AoVivoPage />} />
        <Route path="programacao" element={<ProgramacaoPage />} />
        <Route path="dispositivos" element={<DispositivosPage />} />
      </Route>

      {/* 5. Independent Watch Route (No Layout for Cinematic Fullscreen feel) */}
      <Route 
        path="/app/assistir/:id" 
        element={
          <ProtectedRoute allowedRoles={['subscriber', 'admin', 'editor', 'finance']} requiresProfile>
            <WatchPage />
          </ProtectedRoute>
        } 
      />

      {/* 6. Fully Routed Administrative Panel */}
      <Route 
        path="/admin" 
        element={
          <ProtectedRoute allowedRoles={['admin', 'editor', 'finance']}>
            <AdminPageWrapper tab="dashboard" />
          </ProtectedRoute>
        } 
      />
      <Route 
        path="/admin/usuarios" 
        element={
          <ProtectedRoute allowedRoles={['admin', 'finance']}>
            <AdminPageWrapper tab="usuarios" />
          </ProtectedRoute>
        } 
      />
      <Route 
        path="/admin/assinantes" 
        element={
          <ProtectedRoute allowedRoles={['admin', 'finance']}>
            <AdminPageWrapper tab="assinantes" />
          </ProtectedRoute>
        } 
      />
      <Route 
        path="/admin/conteudos" 
        element={
          <ProtectedRoute allowedRoles={['admin', 'editor']}>
            <AdminPageWrapper tab="conteudo" />
          </ProtectedRoute>
        } 
      />
      <Route 
        path="/admin/series" 
        element={
          <ProtectedRoute allowedRoles={['admin', 'editor']}>
            <AdminPageWrapper tab="series" />
          </ProtectedRoute>
        } 
      />
      <Route 
        path="/admin/temporadas" 
        element={
          <ProtectedRoute allowedRoles={['admin', 'editor']}>
            <AdminPageWrapper tab="temporadas" />
          </ProtectedRoute>
        } 
      />
      <Route 
        path="/admin/episodios" 
        element={
          <ProtectedRoute allowedRoles={['admin', 'editor']}>
            <AdminPageWrapper tab="episodios" />
          </ProtectedRoute>
        } 
      />
      <Route 
        path="/admin/uploads" 
        element={
          <ProtectedRoute allowedRoles={['admin', 'editor']}>
            <AdminPageWrapper tab="uploads" />
          </ProtectedRoute>
        } 
      />
      <Route 
        path="/admin/programacao" 
        element={
          <ProtectedRoute allowedRoles={['admin', 'editor']}>
            <AdminPageWrapper tab="programacao" />
          </ProtectedRoute>
        } 
      />
      <Route 
        path="/admin/canais" 
        element={
          <ProtectedRoute allowedRoles={['admin', 'editor']}>
            <AdminPageWrapper tab="canais" />
          </ProtectedRoute>
        } 
      />
      <Route 
        path="/admin/midia" 
        element={
          <ProtectedRoute allowedRoles={['admin', 'editor']}>
            <AdminPageWrapper tab="midia" />
          </ProtectedRoute>
        } 
      />
      <Route 
        path="/admin/financeiro" 
        element={
          <ProtectedRoute allowedRoles={['admin', 'finance']}>
            <AdminPageWrapper tab="financeiro" />
          </ProtectedRoute>
        } 
      />
      <Route 
        path="/admin/plans" 
        element={
          <ProtectedRoute allowedRoles={['admin', 'finance']}>
            <AdminPageWrapper tab="planos" />
          </ProtectedRoute>
        } 
      />
      <Route 
        path="/admin/planos" 
        element={
          <ProtectedRoute allowedRoles={['admin', 'finance']}>
            <AdminPageWrapper tab="planos" />
          </ProtectedRoute>
        } 
      />
      <Route 
        path="/admin/banners" 
        element={
          <ProtectedRoute allowedRoles={['admin']}>
            <AdminPageWrapper tab="banners" />
          </ProtectedRoute>
        } 
      />
      <Route 
        path="/admin/cupons" 
        element={
          <ProtectedRoute allowedRoles={['admin', 'finance']}>
            <AdminPageWrapper tab="cupons" />
          </ProtectedRoute>
        } 
      />
      <Route 
        path="/admin/avaliacoes" 
        element={
          <ProtectedRoute allowedRoles={['admin', 'editor']}>
            <AdminPageWrapper tab="avaliacoes" />
          </ProtectedRoute>
        } 
      />
      <Route 
        path="/admin/relatorios" 
        element={
          <ProtectedRoute allowedRoles={['admin', 'finance']}>
            <AdminPageWrapper tab="relatorios" />
          </ProtectedRoute>
        } 
      />
      <Route 
        path="/admin/configuracoes" 
        element={
          <ProtectedRoute allowedRoles={['admin']}>
            <AdminPageWrapper tab="configuracoes" />
          </ProtectedRoute>
        } 
      />

      {/* 6. Wildcard Catchall Recovery redirection */}
      <Route path="*" element={<Navigate to="/" replace />} />

    </Routes>
  );
};

export default AppRoutes;

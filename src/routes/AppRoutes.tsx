import React from 'react';
import { Routes, Route, Navigate } from 'react-router-dom';
import { ProtectedRoute } from './ProtectedRoute';
import { PublicLayout } from '../layouts/PublicLayout';
import { SubscriberLayout } from '../layouts/SubscriberLayout';

// Public pages
import { HomePage } from '../pages/public/HomePage';
import { LandingPageRoute } from '../pages/public/LandingPageRoute';
import { PlansPage } from '../pages/public/PlansPage';

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
      </Route>

      {/* 2. Subscriber Area Setup (Perfis) - Requires Authenticated User */}
      <Route 
        path="/app/perfis" 
        element={
          <ProtectedRoute allowedRoles={['subscriber', 'admin', 'editor', 'finance']}>
            <ProfilesPage />
          </ProtectedRoute>
        } 
      />

      {/* 3. Fully Routed Subscriber Area (With SubscriberLayout) */}
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
      </Route>

      {/* 4. Independent Watch Route (No Layout for Cinematic Fullscreen feel) */}
      <Route 
        path="/app/assistir/:id" 
        element={
          <ProtectedRoute allowedRoles={['subscriber', 'admin', 'editor', 'finance']} requiresProfile>
            <WatchPage />
          </ProtectedRoute>
        } 
      />

      {/* 5. Fully Routed Administrative Panel */}
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
        path="/admin/financeiro" 
        element={
          <ProtectedRoute allowedRoles={['admin', 'finance']}>
            <AdminPageWrapper tab="financeiro" />
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

import React from 'react';
import { Navigate, useLocation } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { ShieldAlert, ArrowLeft } from 'lucide-react';

interface ProtectedRouteProps {
  children: React.ReactNode;
  allowedRoles?: string[];
  requiresProfile?: boolean;
}

export const ProtectedRoute: React.FC<ProtectedRouteProps> = ({ 
  children, 
  allowedRoles,
  requiresProfile = false 
}) => {
  const { currentUser, currentProfile, isAuthenticated } = useAuth();
  const location = useLocation();

  if (!isAuthenticated || !currentUser) {
    // Redirect to login, storing current path to return back if needed
    return <Navigate to="/login" state={{ from: location }} replace />;
  }

  // Check roles permissions
  if (allowedRoles && allowedRoles.length > 0) {
    const userRole = currentUser.role;
    const hasPermission = allowedRoles.includes(userRole);

    if (!hasPermission) {
      // Access Denied Screen
      return (
        <div className="min-h-screen bg-black text-white flex flex-col items-center justify-center p-6 text-center select-none font-sans">
          <div className="bg-zinc-950 border border-red-950 p-8 rounded-2xl max-w-md w-full shadow-2xl flex flex-col items-center gap-5">
            <div className="w-16 h-16 bg-red-950/20 border border-red-900 rounded-full flex items-center justify-center text-[#ef4444] animate-bounce">
              <ShieldAlert className="w-8 h-8" />
            </div>
            
            <div className="flex flex-col gap-1.5">
              <h1 className="text-xl font-black font-sans tracking-wide uppercase text-zinc-100">
                Acesso negado
              </h1>
              <p className="text-zinc-500 text-xs font-semibold font-mono leading-relaxed">
                Seu perfil com função <span className="text-[#ef4444] uppercase font-bold">"{userRole}"</span> não possui privilégios de segurança para acessar este diretório avançado.
              </p>
            </div>

            <div className="w-full h-px bg-zinc-900 my-1" />

            <button
              onClick={() => {
                // Sane default: if subscriber, go to App, otherwise go to their designated Dashboard
                window.location.href = userRole === 'subscriber' ? '/app' : '/admin';
              }}
              className="w-full bg-[#ef4444] hover:bg-red-700 text-white text-xs font-bold py-2.5 px-4 rounded-lg flex items-center justify-center gap-2 cursor-pointer transition uppercase tracking-wider font-mono shadow-md hover:shadow-red-900/10"
            >
              <ArrowLeft className="w-4 h-4" />
              <span>Retornar ao Painel Seguro</span>
            </button>
          </div>
        </div>
      );
    }
  }

  // Check subscriber profile constraint
  if (requiresProfile && currentUser.role === 'subscriber' && !currentProfile) {
    // Forces subscriber to select a profile first
    return <Navigate to="/app/perfis" replace />;
  }

  return <>{children}</>;
};

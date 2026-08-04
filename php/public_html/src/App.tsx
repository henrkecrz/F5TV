import React from 'react';
import { BrowserRouter, useNavigate, useLocation } from 'react-router-dom';
import { DataProvider, useData } from './context/DataContext';
import { AuthProvider, useAuth } from './context/AuthContext';
import { AppRoutes } from './routes/AppRoutes';
import { MonitorCheck, HelpCircle } from 'lucide-react';

// Child components of BrowserRouter so we can safely read location and navigate
const PlaygroundToolbar: React.FC = () => {
  const { currentUser, login, logout } = useAuth();
  const { users } = useData();
  const navigate = useNavigate();
  const location = useLocation();

  const handleGoToAdminPanel = () => {
    // If logged in as admin/staff already, navigate to /admin
    if (currentUser && ['admin', 'editor', 'finance'].includes(currentUser.role)) {
      navigate('/admin');
    } else {
      // Find admin account and login
      const adminAcc = users.find((u) => u.email === 'admin@f5tv.com.br');
      if (adminAcc) {
        login(adminAcc.email, 'password_mock_bypass').then(() => {
          navigate('/admin');
        });
      }
    }
  };

  const handleGoToStreamingApp = () => {
    if (currentUser) {
      if (currentUser.role === 'subscriber') {
        navigate('/app/perfis');
      } else {
        // Swap user to guest subscriber
        const guestAcc = users.find((u) => u.email === 'henrikeaps@gmail.com') || users.find(u => u.role === 'subscriber');
        if (guestAcc) {
          login(guestAcc.email, 'password_mock_bypass').then(() => {
            navigate('/app/perfis');
          });
        }
      }
    } else {
      // Quick login as subscriber
      const guestAcc = users.find((u) => u.email === 'henrikeaps@gmail.com') || users.find(u => u.role === 'subscriber');
      if (guestAcc) {
        login(guestAcc.email, 'password_mock_bypass').then(() => {
          navigate('/app/perfis');
        });
      }
    }
  };

  // Determine active tab highlighted classes
  const isAdminActive = ['/admin', '/admin/usuarios', '/admin/conteudos', '/admin/series', '/admin/financeiro'].some(path => location.pathname.startsWith(path));
  const isSubscriberActive = location.pathname.startsWith('/app');

  return (
    <div 
      id="demo-mode-floating-bar" 
      className="fixed bottom-4 left-4 z-50 bg-zinc-950/95 border border-zinc-850 py-2.5 px-4 rounded-xl shadow-2xl flex items-center gap-4 text-xs font-mono font-bold max-w-sm sm:max-w-none backdrop-blur-sm select-none"
    >
      <div className="flex items-center gap-1.5 shrink-0 text-[#ef4444] animate-pulse">
        <MonitorCheck className="w-4 h-4" />
        <span className="hidden sm:inline">F5 MODEL PLAYGROUND</span>
        <span className="sm:hidden">F5 PLAYGROUND</span>
      </div>
      <div className="h-4 w-[1px] bg-zinc-800" />
      <div className="flex items-center gap-2">
        <button
          id="playground-btn-admin"
          onClick={handleGoToAdminPanel}
          className={`px-2.5 py-1 rounded text-[10px] uppercase transition cursor-pointer font-bold ${
            isAdminActive
              ? 'bg-[#ef4444] text-white shadow shadow-red-950'
              : 'bg-zinc-900 text-zinc-400 hover:text-white hover:bg-zinc-800'
          }`}
          title="Logar e chavear instantaneamente para o Painel Admin"
        >
          Painel Admin
        </button>
        <button
          id="playground-btn-streaming"
          onClick={handleGoToStreamingApp}
          className={`px-2.5 py-1 rounded text-[10px] uppercase transition cursor-pointer font-bold ${
            isSubscriberActive
              ? 'bg-[#ef4444] text-white shadow shadow-red-950'
              : 'bg-zinc-900 text-zinc-400 hover:text-white hover:bg-zinc-800'
          }`}
          title="Logar e chavear instantaneamente para a Área de Assinantes"
        >
          Área Assinante
        </button>
      </div>
    </div>
  );
};

export default function App() {
  return (
    <DataProvider>
      <BrowserRouter>
        <AuthProvider>
          <div id="f5-app-wrapper" className="min-h-screen bg-black text-white relative flex flex-col justify-between selection:bg-[#ef4444] selection:text-white">
            
            {/* Visual routing paths renderer */}
            <AppRoutes />

            {/* F5 Model Playground bar */}
            <PlaygroundToolbar />

          </div>
        </AuthProvider>
      </BrowserRouter>
    </DataProvider>
  );
}

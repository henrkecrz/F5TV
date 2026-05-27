import React from 'react';
import { useNavigate, useLocation } from 'react-router-dom';
import { LoginScreen } from '../../components/AuthPages';
import { useAuth } from '../../context/AuthContext';
import LandingPage from '../../components/LandingPage';
import { useData } from '../../context/DataContext';

export const LoginPage: React.FC = () => {
  const { login } = useAuth();
  const { plans, contents } = useData();
  const navigate = useNavigate();
  const location = useLocation();

  const preselectedEmail = (location.state as any)?.email || '';

  const handleLoginSuccess = (user: any) => {
    login(user);

    if (['admin', 'editor', 'finance'].includes(user.role)) {
      navigate('/admin');
    } else {
      navigate('/app/perfis');
    }
  };

  const handleNavigate = (route: string, extra?: any) => {
    if (route === '/login' && extra?.email) {
      navigate('/login', { state: { email: extra.email } });
    } else if (route === '/cadastro' && extra?.planId) {
      navigate('/cadastro', { state: { planId: extra.planId } });
    } else {
      navigate(route);
    }
  };

  return (
    <div className="animate-fade-in flex-1">
      <LoginScreen 
        onLoginSuccess={handleLoginSuccess} 
        onNavigate={handleNavigate} 
        preselectedEmail={preselectedEmail}
      />
      {/* Background landing footer decoration as injected in the original code */}
      <div className="bg-black/80">
        <LandingPage plans={plans.filter(p => p.active)} contents={contents.filter(c => c.status === 'published')} onNavigate={handleNavigate} />
      </div>
    </div>
  );
};

export default LoginPage;

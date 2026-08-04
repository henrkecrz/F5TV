import React from 'react';
import { useNavigate, useLocation } from 'react-router-dom';
import { RegisterScreen } from '../../components/AuthPages';
import { useAuth } from '../../context/AuthContext';
import { useData } from '../../context/DataContext';

export const RegisterPage: React.FC = () => {
  const { login } = useAuth();
  const { plans } = useData();
  const navigate = useNavigate();
  const location = useLocation();

  const preselectedPlanId = (location.state as any)?.planId || 'plano-premium';

  const handleRegisterSuccess = (user: any) => {
    login(user);
    navigate('/app/perfis');
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
      <RegisterScreen 
        plans={plans.filter(p => p.active)} 
        onRegisterSuccess={handleRegisterSuccess} 
        onNavigate={handleNavigate} 
        preselectedPlanId={preselectedPlanId}
      />
    </div>
  );
};

export default RegisterPage;

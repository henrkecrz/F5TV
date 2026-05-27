import React from 'react';
import { useNavigate } from 'react-router-dom';
import LandingPage from '../../components/LandingPage';
import { useData } from '../../context/DataContext';

export const LandingPageRoute: React.FC = () => {
  const { plans, contents } = useData();
  const navigate = useNavigate();

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
      <LandingPage 
        plans={plans.filter(p => p.active)} 
        contents={contents.filter(c => c.status === 'published')} 
        onNavigate={handleNavigate} 
      />
    </div>
  );
};

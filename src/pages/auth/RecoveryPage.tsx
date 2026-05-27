import React from 'react';
import { useNavigate } from 'react-router-dom';
import { RecoveryScreen } from '../../components/AuthPages';

export const RecoveryPage: React.FC = () => {
  const navigate = useNavigate();

  const handleNavigate = (route: string) => {
    navigate(route);
  };

  return (
    <div className="animate-fade-in flex-1">
      <RecoveryScreen onNavigate={handleNavigate} />
    </div>
  );
};

export default RecoveryPage;

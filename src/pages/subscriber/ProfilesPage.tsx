import React from 'react';
import { useNavigate } from 'react-router-dom';
import { ProfileSelectorScreen } from '../../components/AuthPages';
import { useAuth } from '../../context/AuthContext';

export const ProfilesPage: React.FC = () => {
  const { currentUser, selectProfile, logout } = useAuth();
  const navigate = useNavigate();

  if (!currentUser) {
    return null;
  }

  const handleSelectProfile = (p: any) => {
    selectProfile(p);
    navigate('/app');
  };

  const handleNavigate = (route: string) => {
    if (route === '/') {
      logout();
      navigate('/landing');
    } else {
      navigate(route);
    }
  };

  return (
    <div className="animate-fade-in flex-1">
      <ProfileSelectorScreen 
        user={currentUser} 
        onSelectProfile={handleSelectProfile} 
        onNavigate={handleNavigate}
      />
    </div>
  );
};

export default ProfilesPage;

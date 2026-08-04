import React, { createContext, useContext, useState, useEffect } from 'react';
import { User, Profile } from '../types';
import { db } from '../data/mockDatabase';

interface AuthContextType {
  currentUser: User | null;
  currentProfile: Profile | null;
  login: (user: User) => void;
  logout: () => void;
  selectProfile: (profile: Profile | null) => void;
  updateUser: (user: User) => void;
  isAuthenticated: boolean;
  role: string;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export const AuthProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const [currentUser, setCurrentUser] = useState<User | null>(null);
  const [currentProfile, setCurrentProfile] = useState<Profile | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    // Sync session on mount
    const storedUser = localStorage.getItem('f5_active_user');
    const storedProfile = localStorage.getItem('f5_active_profile');

    if (storedUser) {
      try {
        const parsedUser = JSON.parse(storedUser) as User;
        let u = parsedUser;
        if (!parsedUser.role) {
          const dbUser = db.getUsers().find(
            (candidate) => candidate.id === parsedUser.id || candidate.email === parsedUser.email
          );
          if (dbUser) {
            u = dbUser;
            localStorage.setItem('f5_active_user', JSON.stringify(dbUser));
          }
        }
        setCurrentUser(u);
        if (storedProfile) {
          const p = JSON.parse(storedProfile) as Profile;
          setCurrentProfile(p);
        }
      } catch (err) {
        console.error('Failed to parse stored auth session', err);
      }
    }
    setLoading(false);
  }, []);

  const login = (user: User) => {
    setCurrentUser(user);
    setCurrentProfile(null);
    localStorage.setItem('f5_active_user', JSON.stringify(user));
    localStorage.removeItem('f5_active_profile');
  };

  const logout = () => {
    setCurrentUser(null);
    setCurrentProfile(null);
    localStorage.removeItem('f5_active_user');
    localStorage.removeItem('f5_active_profile');
  };

  const selectProfile = (profile: Profile | null) => {
    setCurrentProfile(profile);
    if (profile) {
      localStorage.setItem('f5_active_profile', JSON.stringify(profile));
    } else {
      localStorage.removeItem('f5_active_profile');
    }
  };

  const updateUser = (user: User) => {
    setCurrentUser(user);
    localStorage.setItem('f5_active_user', JSON.stringify(user));
  };

  const isAuthenticated = !!currentUser;
  const role = currentUser?.role || '';

  if (loading) {
    return (
      <div className="min-h-screen bg-black text-[#ef4444] flex items-center justify-center font-mono font-bold">
        <span>CARREGANDO F5 TV...</span>
      </div>
    );
  }

  return (
    <AuthContext.Provider
      value={{
        currentUser,
        currentProfile,
        login,
        logout,
        selectProfile,
        updateUser,
        isAuthenticated,
        role,
      }}
    >
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => {
  const context = useContext(AuthContext);
  if (context === undefined) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
};

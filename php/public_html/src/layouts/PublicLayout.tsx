import React from 'react';
import { Outlet } from 'react-router-dom';

export const PublicLayout: React.FC = () => {
  return (
    <div className="min-h-screen bg-[#050505] text-white flex flex-col justify-between selection:bg-[#ef4444] selection:text-white font-sans">
      <Outlet />
    </div>
  );
};

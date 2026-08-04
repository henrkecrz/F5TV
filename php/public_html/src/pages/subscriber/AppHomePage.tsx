import React from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';
import { useData } from '../../context/DataContext';
import { Play, Info, Heart, Layers, AlertCircle, HeartCrack } from 'lucide-react';

export const AppHomePage: React.FC = () => {
  const { currentProfile, currentUser } = useAuth();
  const { contents, series, categories, watchHistory, favorites, updateFavorites, updateWatchHistory } = useData();
  const navigate = useNavigate();
  const profileId = currentProfile?.id || `staff-${currentUser?.id || 'guest'}`;

  if (!currentUser) {
    return null;
  }

  if (currentUser.role === 'subscriber' && !currentProfile) {
    return null;
  }

  // Find candidate for Featured Hero banner
  const featured = contents.find((c) => c.isFeatured && c.status === 'published') || contents.find(c => c.status === 'published');

  const isFavorited = (itemId: string) => {
    return favorites.some(fav => fav.userId === currentUser.id && fav.profileId === profileId && fav.contentId === itemId);
  };

  const toggleFavorite = (itemId: string) => {
    let updated;
    if (isFavorited(itemId)) {
      updated = favorites.filter(fav => !(fav.userId === currentUser.id && fav.profileId === profileId && fav.contentId === itemId));
    } else {
      updated = [
        ...favorites,
        {
          id: `fav-${Date.now()}`,
          userId: currentUser.id,
          profileId,
          contentId: itemId,
          createdAt: new Date().toISOString()
        }
      ];
    }
    updateFavorites(updated);
  };

  const recordPlayHistory = (item: any) => {
    const existingIndex = watchHistory.findIndex(
      (h) => h.userId === currentUser.id && h.profileId === profileId && h.contentId === item.id
    );

    let updatedHistory = [...watchHistory];
    if (existingIndex !== -1) {
      // update watched Percent to make it rich
      updatedHistory[existingIndex] = {
        ...updatedHistory[existingIndex],
        watchedPercent: Math.min(updatedHistory[existingIndex].watchedPercent + 15, 100),
        lastWatchedAt: new Date().toISOString()
      };
    } else {
      updatedHistory.unshift({
        id: `wh-${Date.now()}`,
        userId: currentUser.id,
        profileId,
        contentId: item.id,
        episodeId: undefined,
        watchedPercent: 12,
        lastWatchedAt: new Date().toISOString()
      });
    }
    updateWatchHistory(updatedHistory);
  };

  // Get only history of current user's profile
  const profileHistory = watchHistory.filter(
    (h) => h.userId === currentUser.id && h.profileId === profileId
  );

  return (
    <div id="subscriber-app-home" className="flex flex-col gap-8 animate-fade-in text-white">
      
      {/* 1. Immersive Wide Hero Banner */}
      {featured && (
        <section id="hero-banner" className="relative h-[60vh] flex items-end p-6 md:p-12 border-b border-zinc-900 bg-black overflow-hidden select-none">
          <div 
            className="absolute inset-0 bg-cover bg-center opacity-40 md:opacity-50"
            style={{ backgroundImage: `url(${featured.bannerUrl})` }}
          />
          <div className="absolute inset-0 bg-gradient-to-t from-zinc-950 via-zinc-950/40 to-black/30" />
          
          <div className="relative z-10 max-w-3xl flex flex-col items-start gap-4 mx-auto w-full">
            <div className="inline-flex items-center gap-1.5 bg-[#ef4444] text-white font-mono font-black text-[10px] tracking-wider uppercase px-2 py-0.5 rounded-sm">
              EM DESTAQUE F5 HOJE
            </div>

            <h1 className="text-3xl md:text-5xl font-black tracking-tight leading-none text-white">{featured.title}</h1>
            <p className="text-zinc-350 text-xs md:text-sm leading-relaxed line-clamp-2 md:line-clamp-3 font-semibold max-w-2xl">{featured.shortDescription}</p>

            <div className="flex flex-wrap gap-3 mt-2">
              <button
                onClick={() => {
                  recordPlayHistory(featured);
                  navigate(`/app/assistir/${featured.id}`);
                }}
                className="bg-[#ef4444] hover:bg-red-700 text-white font-bold px-5 py-2.5 rounded flex items-center gap-1.5 text-xs uppercase cursor-pointer tracking-wider font-mono transition"
              >
                <Play className="w-4 h-4 fill-white" />
                <span>Assistir Agora</span>
              </button>
              <button
                onClick={() => navigate(`/app/conteudo/${featured.id}`)}
                className="bg-zinc-900/80 border border-zinc-800 hover:bg-zinc-805 hover:border-zinc-750 text-white font-bold px-5 py-2.5 rounded flex items-center gap-1.5 text-xs uppercase cursor-pointer tracking-wider font-mono transition"
              >
                <Info className="w-4 h-4 text-[#ef4444]" />
                <span>Detalhes</span>
              </button>
              <button
                onClick={() => toggleFavorite(featured.id)}
                className="bg-zinc-900/85 border border-zinc-800 hover:border-zinc-700 text-zinc-350 hover:text-white p-2.5 rounded cursor-pointer transition"
                title="Salvar em minha lista"
              >
                <Heart className={`w-4 h-4 ${isFavorited(featured.id) ? 'fill-red-500 text-red-500' : ''}`} />
              </button>
            </div>
          </div>
        </section>
      )}

      {/* 2. Continuar Assistindo Row */}
      {profileHistory.length > 0 && (
        <section id="row-continue" className="max-w-7xl w-full mx-auto px-6 md:px-8 mt-4">
          <h3 className="text-xs font-mono tracking-widest text-[#ef4444] font-black uppercase mb-4">CONTINUAR ASSISTINDO</h3>
          <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            {profileHistory.slice(0, 4).map((hist) => {
              const originalItem = contents.find(c => c.id === hist.contentId) || series.find(s => s.id === hist.contentId);
              if (!originalItem) return null;

              return (
                <div 
                  key={hist.id}
                  className="bg-zinc-950/80 border border-zinc-900 hover:border-zinc-800 rounded-lg overflow-hidden flex flex-col gap-2 relative group p-3.5"
                >
                  <div className="flex justify-between items-start gap-2">
                    <div className="flex flex-col">
                      <span className="text-[8px] font-mono tracking-wider font-extrabold text-zinc-500 uppercase">PROGRAMA</span>
                      <h4 className="font-bold text-xs text-zinc-100 line-clamp-1">{hist.title || originalItem.title}</h4>
                    </div>
                    <button 
                      onClick={() => {
                        recordPlayHistory(originalItem);
                        navigate(`/app/assistir/${originalItem.id}`);
                      }}
                      className="p-1.5 bg-[#ef4444] rounded-full text-white cursor-pointer hover:scale-105 transition"
                    >
                      <Play className="w-3.5 h-3.5 fill-current" />
                    </button>
                  </div>
                  {/* Visual Completion progress bar */}
                  <div className="mt-2 flex flex-col gap-1 text-[10px] text-zinc-400 font-mono font-bold">
                    <div className="flex justify-between">
                      <span>Visto:</span>
                      <span>{hist.watchedPercent}%</span>
                    </div>
                    <div className="h-1 bg-zinc-900 rounded-lg overflow-hidden">
                      <div className="h-full bg-red-600 rounded-lg" style={{ width: `${hist.watchedPercent}%` }} />
                    </div>
                  </div>
                </div>
              );
            })}
          </div>
        </section>
      )}

      {/* 3. General Series collection grid */}
      <section id="collection-series-row" className="max-w-7xl w-full mx-auto px-6 md:px-8">
        <h3 className="text-xs font-mono tracking-widest text-zinc-400 font-black uppercase mb-4 flex items-center gap-1.5">
          <Layers className="w-4 h-4 text-[#ef4444]" />
          <span>SÉRIES INVESTIGATIVAS & EXCLUSIVAS</span>
        </h3>
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          {series.map((ser) => {
            // Find representative published content to display details page
            const itemsSeriesMatches = contents.filter(c => c.title.toLowerCase().trim() === ser.title.toLowerCase().trim() && c.status === 'published');
            const targetId = itemsSeriesMatches[0]?.id || ser.id;

            return (
              <div 
                key={ser.id}
                onClick={() => navigate(`/app/conteudo/${targetId}`)}
                className="group relative bg-zinc-950/80 border border-zinc-900 rounded-xl overflow-hidden cursor-pointer hover:border-red-600 transition shadow-lg shrink-0 flex flex-col"
              >
                <div className="aspect-[16/9] w-full bg-zinc-900 overflow-hidden relative">
                  <img 
                    src={ser.bannerUrl} 
                    alt={ser.title} 
                    referrerPolicy="no-referrer"
                    className="w-full h-full object-cover opacity-60 group-hover:opacity-90 transform group-hover:scale-102 transition duration-300"
                  />
                  <div className="absolute top-2 left-2 bg-red-650 text-[9px] font-bold text-white px-1.5 py-0.5 rounded uppercase font-mono tracking-wider">
                    SÉRIE F5 TV
                  </div>
                </div>
                <div className="p-4 flex flex-col gap-1 justify-between flex-1">
                  <div className="flex flex-col gap-1">
                    <span className="text-[10px] font-mono tracking-wider font-semibold uppercase text-[#ef4444]">
                      {ser.genre}
                    </span>
                    <h4 className="font-bold text-sm text-zinc-150 group-hover:text-white">
                      {ser.title}
                    </h4>
                    <p className="text-zinc-500 text-xs line-clamp-2 leading-relaxed font-semibold">
                      {ser.description}
                    </p>
                  </div>
                </div>
              </div>
            );
          })}
        </div>
      </section>

      {/* 4. Categorized collections rows */}
      <div className="flex flex-col gap-4">
        {categories.map((cat) => {
          const matches = contents.filter(c => c.categoryId === cat.id && c.status === 'published');
          if (matches.length === 0) return null;

          return (
            <section key={cat.id} className="max-w-7xl w-full mx-auto px-6 md:px-8 py-2">
              <h3 className="text-xs font-mono tracking-widest text-zinc-400 font-black uppercase mb-4 flex items-center gap-1.5">
                <span className="w-1.5 h-3 bg-[#ef4444] rounded-sm" />
                <span>{cat.name}</span>
              </h3>

              <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                {matches.map((item) => (
                  <div 
                    key={item.id}
                    onClick={() => navigate(`/app/conteudo/${item.id}`)}
                    className="group relative bg-zinc-950 border border-zinc-900 hover:border-red-600 rounded-lg overflow-hidden cursor-pointer transition transform hover:-translate-y-1 block animate-fade-in"
                  >
                    <div className="aspect-[3/4] relative bg-[#151515]">
                      <img 
                        src={item.coverUrl} 
                        alt={item.title} 
                        referrerPolicy="no-referrer"
                        className="w-full h-full object-cover group-hover:opacity-100 opacity-80 transition duration-200"
                      />
                      <div className="absolute top-2 right-2 bg-black/85 text-[10px] font-mono font-bold text-gray-300 px-1.5 py-0.5 rounded border border-white/5">
                        {item.ageRating}
                      </div>
                      {item.isExclusive && (
                        <div className="absolute bottom-2 left-2 bg-[#ef4444] text-[9px] font-bold text-white px-1.5 py-0.5 rounded uppercase font-mono tracking-wider shadow">
                          Exclusivo
                        </div>
                      )}
                    </div>
                    <div className="p-3 bg-zinc-950 flex flex-col gap-0.5">
                      <span className="text-[9px] font-mono uppercase text-zinc-500 tracking-wider">
                        {item.genre}
                      </span>
                      <h4 className="font-bold text-xs text-zinc-150 line-clamp-1 group-hover:text-white transition">
                        {item.title}
                      </h4>
                    </div>
                  </div>
                ))}
              </div>
            </section>
          );
        })}
      </div>

    </div>
  );
};

export default AppHomePage;

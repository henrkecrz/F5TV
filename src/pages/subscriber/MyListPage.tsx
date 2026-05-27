import React from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';
import { useData } from '../../context/DataContext';
import { HeartCrack, Play, Eye, AlertCircle, Sparkles } from 'lucide-react';

export const MyListPage: React.FC = () => {
  const { currentUser, currentProfile } = useAuth();
  const { favorites, updateFavorites, contents } = useData();
  const navigate = useNavigate();

  if (!currentUser || !currentProfile) {
    return null;
  }

  // Filter user/profile favorites
  const myFavs = favorites.filter(
    (f) => f.userId === currentUser.id && f.profileId === currentProfile.id
  );

  const favoritedContents = myFavs
    .map((fav) => contents.find((c) => c.id === fav.contentId))
    .filter((c): c is NonNullable<typeof c> => !!c && c.status === 'published');

  const handleRemoveFavorite = (contentId: string, e: React.MouseEvent) => {
    e.stopPropagation(); // Avoid triggering card details routing
    const updated = favorites.filter(
      (f) => !(f.userId === currentUser.id && f.profileId === currentProfile.id && f.contentId === contentId)
    );
    updateFavorites(updated);
  };

  return (
    <div id="mylist-page-view" className="max-w-7xl mx-auto px-6 md:px-8 pt-10 animate-fade-in text-white">
      <div className="border-b border-zinc-900 pb-5 mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <span className="text-xs font-mono font-black text-[#ef4444] uppercase tracking-widest">Coleção Especial</span>
          <h1 className="text-2xl md:text-3xl font-black tracking-tight text-white mt-1">Minha Lista de Salvamentos</h1>
        </div>
        <span className="text-xs font-mono bg-red-600/10 text-[#ef4444] border border-red-950/40 px-3 py-1 rounded-full font-bold">
          {favoritedContents.length} {favoritedContents.length === 1 ? 'Título' : 'Títulos'}
        </span>
      </div>

      {favoritedContents.length === 0 ? (
        <div className="bg-zinc-950 border border-zinc-900 p-12 md:p-16 rounded-2xl flex flex-col items-center justify-center max-w-xl mx-auto text-center gap-6 shadow-2xl">
          <div className="w-16 h-16 bg-red-950/20 border border-red-900/40 rounded-full flex items-center justify-center text-[#ef4444]">
            <Sparkles className="w-7 h-7" />
          </div>
          <div className="flex flex-col gap-1.5">
            <h2 className="text-lg font-bold text-zinc-100 uppercase">Sua lista está intocada</h2>
            <p className="text-zinc-500 text-xs font-semibold leading-relaxed">
              Explore o catálogo premium da F5 TV e selecione seus documentários favoritos ou programas de jornalismo clicando em "Salvar" para preencher seu painel.
            </p>
          </div>
          <Link
            to="/app"
            className="bg-[#ef4444] hover:bg-red-700 text-white text-xs font-bold font-mono px-5 py-2.5 rounded-xl uppercase tracking-wider transition duration-200"
          >
            Navegar Pelo Catálogo
          </Link>
        </div>
      ) : (
        <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-6">
          {favoritedContents.map((item) => (
            <div 
              key={item.id}
              onClick={() => navigate(`/app/conteudo/${item.id}`)}
              className="group bg-zinc-950 border border-zinc-900 hover:border-red-650 rounded-xl overflow-hidden cursor-pointer transition transform hover:-translate-y-1"
            >
              <div className="aspect-[3/4] relative bg-zinc-900 overflow-hidden">
                <img 
                  src={item.coverUrl} 
                  alt={item.title} 
                  referrerPolicy="no-referrer"
                  className="w-full h-full object-cover group-hover:scale-102 opacity-80 group-hover:opacity-100 transition duration-350"
                />
                
                {/* Float hover overlay tools */}
                <div className="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition flex flex-col justify-end p-3 gap-2">
                  <button 
                    onClick={(e) => handleRemoveFavorite(item.id, e)}
                    className="w-full bg-red-950/90 border border-red-900 text-[#ef4444] hover:bg-red-600 hover:text-white transition py-2 rounded-lg text-[10px] font-mono font-bold uppercase flex items-center justify-center gap-1.5"
                  >
                    <HeartCrack className="w-3.5 h-3.5" />
                    <span>Excluir</span>
                  </button>
                </div>
              </div>
              <div className="p-3">
                <span className="text-[8px] font-mono font-extrabold uppercase text-zinc-500">{item.genre}</span>
                <h3 className="font-bold text-xs text-zinc-200 group-hover:text-white line-clamp-1 truncate mt-0.5">{item.title}</h3>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
};

export default MyListPage;

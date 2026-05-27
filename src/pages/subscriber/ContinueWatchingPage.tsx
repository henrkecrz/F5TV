import React from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';
import { useData } from '../../context/DataContext';
import { Play, Trash2, Calendar, Clock, AlertCircle } from 'lucide-react';

export const ContinueWatchingPage: React.FC = () => {
  const { currentUser, currentProfile } = useAuth();
  const { watchHistory, updateWatchHistory, contents, series } = useData();
  const navigate = useNavigate();
  const profileId = currentProfile?.id || `staff-${currentUser?.id || 'guest'}`;

  if (!currentUser) {
    return null;
  }

  if (currentUser.role === 'subscriber' && !currentProfile) {
    return null;
  }

  // Filter history belonging to current user and profile
  const myHistory = watchHistory.filter(
    (h) => h.userId === currentUser.id && h.profileId === profileId
  );

  const handleRemoveHistory = (historyId: string, e: React.MouseEvent) => {
    e.stopPropagation();
    const updated = watchHistory.filter((h) => h.id !== historyId);
    updateWatchHistory(updated);
  };

  const handleResumePlayback = (contentId: string, episodeId?: string) => {
    if (episodeId) {
      navigate(`/app/assistir/${contentId}?episodeId=${episodeId}`);
    } else {
      navigate(`/app/assistir/${contentId}`);
    }
  };

  return (
    <div id="continue-watching-view" className="max-w-7xl mx-auto px-6 md:px-8 pt-10 animate-fade-in text-white">
      
      <div className="border-b border-zinc-900 pb-5 mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <span className="text-xs font-mono font-black text-[#ef4444] uppercase tracking-widest">Seu Registro de Reprodução</span>
          <h1 className="text-2xl md:text-3xl font-black tracking-tight text-white mt-1">Continuar Assistindo</h1>
        </div>
        <span className="text-xs font-mono bg-[#ef4444]/10 text-[#ef4444] border border-red-950 px-3 py-1 rounded-full font-bold">
          {myHistory.length} Programas salvos
        </span>
      </div>

      {myHistory.length === 0 ? (
        <div className="bg-zinc-950 border border-zinc-900 p-12 md:p-16 rounded-2xl flex flex-col items-center justify-center max-w-xl mx-auto text-center gap-6 shadow-2xl">
          <div className="w-16 h-16 bg-red-950/20 border border-red-900/40 rounded-full flex items-center justify-center text-[#ef4444]">
            <Clock className="w-7 h-7" />
          </div>
          <div className="flex flex-col gap-1.5">
            <h2 className="text-lg font-bold text-zinc-100 uppercase">Histórico limpo</h2>
            <p className="text-zinc-500 text-xs font-semibold leading-relaxed">
              Você ainda não iniciou a reprodução de mídias sob demanda ou transmissões. Quando iniciar, seu rastro de progresso será exibido aqui para continuar de onde parou.
            </p>
          </div>
          <Link
            to="/app"
            className="bg-[#ef4444] hover:bg-red-700 text-white text-xs font-bold font-mono px-5 py-2.5 rounded-xl uppercase tracking-wider transition duration-200"
          >
            Começar a assistir
          </Link>
        </div>
      ) : (
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          {myHistory.map((hist) => {
            const originalItem = contents.find((c) => c.id === hist.contentId) || series.find((s) => s.id === hist.contentId);
            if (!originalItem) return null;

            return (
              <div 
                key={hist.id}
                onClick={() => navigate(`/app/conteudo/${originalItem.id}`)}
                className="bg-zinc-950 border border-zinc-900 hover:border-zinc-800 rounded-xl overflow-hidden p-4 flex flex-col gap-3 relative cursor-pointer group transition"
              >
                <div className="flex justify-between items-start gap-4">
                  <div className="flex flex-col text-left">
                    <span className="text-[9px] font-mono font-bold text-zinc-500 uppercase tracking-widest">
                      {originalItem.genre || 'PROGRAMA'}
                    </span>
                    <h3 className="font-bold text-sm text-zinc-200 mt-1 line-clamp-1 group-hover:text-[#ef4444] transition-colors">{hist.title || originalItem.title}</h3>
                    {hist.episodeId && (
                      <span className="text-[10px] text-zinc-505 font-mono font-medium mt-0.5">Dispositivo Móvel</span>
                    )}
                  </div>
                  <div className="flex gap-1.5 self-start">
                    <button
                      onClick={(e) => handleRemoveHistory(hist.id, e)}
                      className="p-2 bg-zinc-900 border border-zinc-850 hover:bg-red-950/20 text-zinc-550 hover:text-[#ef4444] rounded-lg transition"
                      title="Excluir do Histórico de Progresso"
                    >
                      <Trash2 className="w-3.5 h-3.5" />
                    </button>
                    <button
                      onClick={(e) => {
                        e.stopPropagation();
                        handleResumePlayback(originalItem.id, hist.episodeId);
                      }}
                      className="p-2 bg-[#ef4444] text-white hover:bg-red-750 rounded-lg transition hover:scale-105 shadow"
                      title="Continuar Assistindo"
                    >
                      <Play className="w-3.5 h-3.5 fill-current" />
                    </button>
                  </div>
                </div>

                <div className="mt-2 text-left">
                  <div className="flex justify-between items-center text-[10px] font-mono font-bold text-zinc-500 uppercase">
                    <span>Sua Reprodução:</span>
                    <span className="text-[#ef4444]">{hist.watchedPercent}%</span>
                  </div>
                  <div className="h-1.5 bg-zinc-900 rounded-full overflow-hidden mt-1.5">
                    <div 
                      className="h-full bg-red-600 rounded-full" 
                      style={{ width: `${hist.watchedPercent}%` }} 
                    />
                  </div>
                </div>

                <div className="h-px bg-zinc-900 my-1" />

                <div className="flex justify-between items-center text-[9px] font-mono text-zinc-650 font-bold">
                  <span className="flex items-center gap-1">
                    <Calendar className="w-3 h-3 text-[#ef4444]" />
                    <span>Visto em {new Date(hist.lastWatchedAt).toLocaleDateString()}</span>
                  </span>
                  <span>Retomar</span>
                </div>
              </div>
            );
          })}
        </div>
      )}
    </div>
  );
};

export default ContinueWatchingPage;

import React from 'react';
import { useParams, useNavigate, useSearchParams } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';
import { useData } from '../../context/DataContext';
import VideoPlayer from '../../components/VideoPlayer';
import { AlertCircle } from 'lucide-react';

export const WatchPage: React.FC = () => {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const [searchParams] = useSearchParams();
  const { currentUser, currentProfile } = useAuth();
  const { contents, episodes } = useData();

  if (!currentUser || !currentProfile) {
    return null;
  }

  const currentContent = contents.find(c => c.id === id);

  if (!currentContent) {
    return (
      <div className="min-h-screen bg-black text-[#ef4444] flex flex-col items-center justify-center p-6 text-center">
        <AlertCircle className="w-12 h-12 mb-2" />
        <h2 className="text-lg font-bold uppercase">Mídia Indisponível</h2>
        <button onClick={() => navigate('/app')} className="mt-4 px-4 py-2 bg-zinc-900 border border-zinc-800 rounded font-mono text-xs uppercase text-white">
          Voltar ao Catálogo
        </button>
      </div>
    );
  }

  const episodeId = searchParams.get('episodeId');
  const isTrailer = searchParams.get('trailer') === 'true';

  let resolvedVideoUrl = currentContent.videoUrl;
  let resolvedTitle = currentContent.title;
  let resolvedSubtitle = '';

  if (isTrailer) {
    resolvedVideoUrl = currentContent.trailerUrl || currentContent.videoUrl;
    resolvedSubtitle = 'Teaser / Trailer Oficial';
  } else if (episodeId) {
    const ep = episodes.find(e => e.id === episodeId);
    if (ep) {
      resolvedVideoUrl = ep.videoUrl;
      resolvedSubtitle = `Episódio ${ep.number}: ${ep.title}`;
    }
  }

  const hasNextEpisode = episodeId ? (
    episodes.some(e => {
      const curEp = episodes.find(cur => cur.id === episodeId);
      return curEp && e.seasonId === curEp.seasonId && e.number === curEp.number + 1;
    })
  ) : false;

  const handleNextEpisode = () => {
    if (!episodeId) return;
    const curEp = episodes.find(cur => cur.id === episodeId);
    if (!curEp) return;
    const nextEp = episodes.find(e => e.seasonId === curEp.seasonId && e.number === curEp.number + 1);
    if (nextEp) {
      navigate(`/app/assistir/${currentContent.id}?episodeId=${nextEp.id}`, { replace: true });
    }
  };

  const handleClose = () => {
    navigate(`/app/conteudo/${currentContent.id}`);
  };

  return (
    <div id="full-watch-player-route" className="fixed inset-0 bg-black z-50 overflow-hidden">
      <VideoPlayer
        videoUrl={resolvedVideoUrl}
        title={resolvedTitle}
        subtitle={resolvedSubtitle}
        onClose={handleClose}
        hasNextEpisode={hasNextEpisode}
        onNextEpisode={handleNextEpisode}
      />
    </div>
  );
};

export default WatchPage;

import React, { useState, useEffect } from 'react';
import { useParams, useNavigate, Link } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';
import { useData } from '../../context/DataContext';
import { 
  Play, Heart, Star, Layers, Clapperboard, Check, Calendar, Clock, 
  User, Video, MessageSquare, ArrowLeft, ChevronRight, AlertCircle 
} from 'lucide-react';

export const ContentDetailsPage: React.FC = () => {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const { currentUser, currentProfile } = useAuth();
  const { 
    contents, series, seasons, episodes, categories, 
    favorites, updateFavorites, watchHistory, updateWatchHistory,
    reviews, updateReviews
  } = useData();

  const [selectedSeasonNumber, setSelectedSeasonNumber] = useState(1);
  const [userRating, setUserRating] = useState(5);
  const [reviewComment, setReviewComment] = useState('');
  const [reviewSuccessMsg, setReviewSuccessMsg] = useState('');
  const profileId = currentProfile?.id || `staff-${currentUser?.id || 'guest'}`;
  const profileName = currentProfile?.name || currentUser?.name || 'Equipe F5';

  const currentContent = contents.find((c) => c.id === id);

  useEffect(() => {
    window.scrollTo(0, 0);
  }, [id]);

  if (!currentUser) {
    return null;
  }

  if (currentUser.role === 'subscriber' && !currentProfile) {
    return null;
  }

  if (!currentContent) {
    return (
      <div className="min-h-[60vh] max-w-7xl mx-auto px-6 flex flex-col items-center justify-center text-center gap-4">
        <AlertCircle className="w-12 h-12 text-[#ef4444]" />
        <h2 className="text-xl font-bold">Conteúdo não localizado</h2>
        <p className="text-zinc-500 text-xs">Este conteúdo pode ter sido arquivado ou removido pelo administrador.</p>
        <Link to="/app" className="bg-[#ef4444] hover:bg-red-700 text-white text-xs font-bold px-4 py-2 rounded uppercase font-mono">
          Voltar ao Início
        </Link>
      </div>
    );
  }

  const categoryName = categories.find(c => c.id === currentContent.categoryId)?.name || 'Especiais';

  // Manage serial relationships
  const contentSeries = series.find(s => s.title.toLowerCase().trim() === currentContent.title.toLowerCase().trim());
  const seriesSeasons = contentSeries ? seasons.filter(s => s.seriesId === contentSeries.id) : [];
  const activeSeason = seriesSeasons.find(s => s.number === selectedSeasonNumber);
  const seasonEpisodes = activeSeason ? episodes.filter(e => e.seasonId === activeSeason.id) : [];

  // Related contents recommendation
  const relatedContent = contents.filter(
    c => c.categoryId === currentContent.categoryId && c.id !== currentContent.id && c.status === 'published'
  ).slice(0, 4);

  const isFavorited = favorites.some(
    fav => fav.userId === currentUser.id && fav.profileId === profileId && fav.contentId === currentContent.id
  );

  const toggleFavorite = () => {
    let updated;
    if (isFavorited) {
      updated = favorites.filter(
        fav => !(fav.userId === currentUser.id && fav.profileId === profileId && fav.contentId === currentContent.id)
      );
    } else {
      updated = [
        ...favorites,
        {
          id: `fav-${Date.now()}`,
          userId: currentUser.id,
          profileId,
          contentId: currentContent.id,
          createdAt: new Date().toISOString()
        }
      ];
    }
    updateFavorites(updated);
  };

  const recordPlayHistory = (epId?: string) => {
    const existingIndex = watchHistory.findIndex(
      (h) => h.userId === currentUser.id && h.profileId === profileId && h.contentId === currentContent.id
    );

    let updatedHistory = [...watchHistory];
    if (existingIndex !== -1) {
      updatedHistory[existingIndex] = {
        ...updatedHistory[existingIndex],
        watchedPercent: Math.min(updatedHistory[existingIndex].watchedPercent + 10, 100),
        lastWatchedAt: new Date().toISOString(),
        episodeId: epId
      };
    } else {
      updatedHistory.unshift({
        id: `wh-${Date.now()}`,
        userId: currentUser.id,
        profileId,
        contentId: currentContent.id,
        episodeId: epId,
        watchedPercent: 15,
        lastWatchedAt: new Date().toISOString()
      });
    }
    updateWatchHistory(updatedHistory);
  };

  const handlePlayNow = () => {
    recordPlayHistory(seasonEpisodes[0]?.id);
    if (seasonEpisodes.length > 0) {
      navigate(`/app/assistir/${currentContent.id}?episodeId=${seasonEpisodes[0].id}`);
    } else {
      navigate(`/app/assistir/${currentContent.id}`);
    }
  };

  const handlePlayEpisode = (epId: string) => {
    recordPlayHistory(epId);
    navigate(`/app/assistir/${currentContent.id}?episodeId=${epId}`);
  };

  const handlePlayTrailer = () => {
    navigate(`/app/assistir/${currentContent.id}?trailer=true`);
  };

  const handleSubmitReview = (e: React.FormEvent) => {
    e.preventDefault();
    if (!reviewComment.trim()) return;

    const newReview = {
      id: `rev-${Date.now()}`,
      userId: currentUser.id,
      profileId,
      profileName,
      contentId: currentContent.id,
      rating: userRating,
      comment: reviewComment,
      createdAt: new Date().toISOString()
    };

    updateReviews([...reviews, newReview]);
    setReviewComment('');
    setReviewSuccessMsg('Sua avaliação foi registrada com sucesso e publicada para todos!');
    setTimeout(() => setReviewSuccessMsg(''), 4500);
  };

  const contentReviews = reviews.filter(r => r.contentId === currentContent.id);
  const averageRating = contentReviews.length > 0
    ? (contentReviews.reduce((sum, r) => sum + r.rating, 0) / contentReviews.length).toFixed(1)
    : '5.0';

  return (
    <div id="content-details-page" className="animate-fade-in text-zinc-300 font-sans">
      
      {/* Navigation Breadcrumb */}
      <div className="max-w-7xl mx-auto px-6 md:px-8 pt-6">
        <button 
          onClick={() => navigate('/app')}
          className="flex items-center gap-1 text-zinc-500 hover:text-white transition text-xs font-mono font-bold uppercase shrink-0"
        >
          <ArrowLeft className="w-4 h-4" />
          <span>Voltar ao catálogo</span>
        </button>
      </div>

      {/* Main Hero Header */}
      <section className="relative h-[45vh] md:h-[55vh] flex items-end p-6 md:p-12 border-b border-zinc-900 bg-black overflow-hidden select-none mb-8">
        <div 
          className="absolute inset-0 bg-cover bg-center opacity-30 md:opacity-50"
          style={{ backgroundImage: `url(${currentContent.bannerUrl})` }}
        />
        <div className="absolute inset-0 bg-gradient-to-t from-zinc-950 via-zinc-950/40 to-transparent" />
        
        <div className="relative z-10 max-w-7xl mx-auto w-full flex items-end justify-between gap-6">
          <div className="flex flex-col items-start gap-3 max-w-3xl">
            <div className="inline-flex items-center gap-1.5 bg-red-650 text-white font-mono font-black text-[10px] tracking-wider uppercase px-2 py-0.5 rounded-sm">
              {categoryName}
            </div>

            <h1 className="text-3xl md:text-5xl font-black tracking-tight leading-none text-white">{currentContent.title}</h1>
            <p className="text-zinc-400 text-xs md:text-sm font-semibold max-w-2xl">{currentContent.shortDescription}</p>
          </div>
        </div>
      </section>

      {/* Core Split columns details */}
      <div className="max-w-7xl mx-auto px-6 md:px-8 grid grid-cols-1 lg:grid-cols-3 gap-12 mb-16">
        
        {/* Left main pane */}
        <div className="lg:col-span-2 flex flex-col gap-6">
          
          {/* Metadata pills bar */}
          <div className="flex flex-wrap items-center gap-3 text-xs font-mono font-bold text-zinc-500">
            <span className="bg-zinc-900 text-[#ef4444] px-2 py-0.5 rounded border border-zinc-800 tracking-widest">{currentContent.ageRating}</span>
            <span className="flex items-center gap-1">
              <Calendar className="w-3.5 h-3.5" />
              <span>{currentContent.year}</span>
            </span>
            <span className="flex items-center gap-1">
              <Clock className="w-3.5 h-3.5" />
              <span>{currentContent.duration}</span>
            </span>
            {currentContent.isExclusive && (
              <span className="bg-red-950 text-red-500 border border-red-900 px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-sans">Exclusivo Assinante</span>
            )}
            {currentContent.isFree && (
              <span className="bg-emerald-950 text-emerald-400 border border-emerald-900 px-1.5 py-0.5 rounded text-[9px] uppercase tracking-wider font-sans">Acesso Gratuito</span>
            )}
          </div>

          <div className="p-1 bg-zinc-950/40 rounded-2xl border border-zinc-900">
            <div className="p-6 md:p-8 flex flex-col gap-5">
              <h3 className="text-sm font-mono tracking-wider font-black text-[#ef4444] uppercase">Sobre este Programa</h3>
              <p className="text-sm md:text-base leading-relaxed text-zinc-300 font-semibold">
                {currentContent.fullDescription}
              </p>
            </div>
          </div>

          {/* Sub Action controls buttons */}
          <div className="flex flex-wrap gap-3.5">
            <button
              onClick={handlePlayNow}
              className="bg-[#ef4444] hover:bg-red-700 text-white font-bold py-3.5 px-7 rounded-xl text-xs tracking-wider uppercase font-mono flex items-center justify-center gap-2 cursor-pointer transition shadow-lg shadow-red-900/10 hover:scale-101"
            >
              <Play className="w-4 h-4 fill-white" />
              <span>{contentSeries ? 'Assistir Ep. 1' : 'Assistir Agora'}</span>
            </button>

            <button
              onClick={handlePlayTrailer}
              className="bg-zinc-900 border border-zinc-800 hover:bg-zinc-850 hover:border-zinc-700 text-white font-bold py-3.5 px-6 rounded-xl text-xs tracking-wider uppercase font-mono flex items-center justify-center gap-2 cursor-pointer transition"
            >
              <Clapperboard className="w-4 h-4" />
              <span>Ver Teaser</span>
            </button>

            <button
              onClick={toggleFavorite}
              className={`py-3.5 px-5 rounded-xl border flex items-center justify-center gap-1.5 cursor-pointer text-xs font-mono font-bold uppercase transition ${
                isFavorited
                  ? 'bg-red-950/20 border-red-900 text-[#ef4444]'
                  : 'bg-zinc-950/50 border-zinc-900 text-zinc-400 hover:border-zinc-700 hover:text-white'
              }`}
            >
              <Heart className={`w-4 h-4 ${isFavorited ? 'fill-red-505 text-red-500' : ''}`} />
              <span>{isFavorited ? 'Na Minha Lista' : 'Salvar Lista'}</span>
            </button>
          </div>

          {/* SERIES AND EPISODES PANEL */}
          {contentSeries && (
            <div className="border-t border-zinc-900 pt-8 mt-4">
              <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <h3 className="text-zinc-100 font-bold text-base flex items-center gap-2">
                  <Layers className="w-5 h-5 text-[#ef4444]" />
                  <span>Selecione a Temporada</span>
                </h3>
                {seriesSeasons.length > 0 && (
                  <select
                    value={selectedSeasonNumber}
                    onChange={(e) => setSelectedSeasonNumber(parseInt(e.target.value))}
                    className="bg-zinc-950 border border-zinc-900 focus:border-[#ef4444] rounded-lg px-4 py-2 text-xs text-zinc-300 font-bold outline-none cursor-pointer"
                  >
                    {seriesSeasons.map((se) => (
                      <option key={se.id} value={se.number}>
                        Temporada {se.number}
                      </option>
                    ))}
                  </select>
                )}
              </div>

              {/* Episodes List Grid */}
              {seasonEpisodes.length === 0 ? (
                <div className="bg-zinc-950/30 p-10 text-center text-zinc-650 rounded-xl border border-dashed border-zinc-900 text-xs">
                  Nenhum episódio publicado para esta temporada ainda.
                </div>
              ) : (
                <div className="flex flex-col gap-3 max-h-96 overflow-y-auto pr-2 scrollbar-thin">
                  {seasonEpisodes.map((ep) => (
                    <div 
                      key={ep.id}
                      onClick={() => handlePlayEpisode(ep.id)}
                      className="bg-zinc-950/30 hover:bg-zinc-950 border border-zinc-900 hover:border-zinc-800 p-4 rounded-xl flex gap-4 items-center cursor-pointer group transition duration-200"
                    >
                      <div className="w-24 md:w-32 aspect-video bg-zinc-900 rounded-lg overflow-hidden shrink-0 relative">
                        <img 
                          src={ep.thumbnailUrl} 
                          alt={ep.title} 
                          referrerPolicy="no-referrer"
                          className="w-full h-full object-cover opacity-60 group-hover:opacity-100 transition duration-200"
                        />
                        <div className="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition bg-black/40">
                          <Play className="w-6 h-6 text-white fill-white" />
                        </div>
                      </div>
                      <div className="flex-1 flex flex-col gap-1 text-left">
                        <div className="flex items-center gap-2">
                          <span className="text-xs font-mono font-bold text-[#ef4444]">Episódio {ep.number}</span>
                          <span className="text-[10px] text-zinc-600 font-mono">({ep.duration})</span>
                        </div>
                        <h4 className="font-bold text-sm text-zinc-200 group-hover:text-white line-clamp-1">{ep.title}</h4>
                        <p className="text-[11px] text-zinc-500 line-clamp-2 leading-relaxed font-semibold">
                          {ep.description}
                        </p>
                      </div>
                    </div>
                  ))}
                </div>
              )}
            </div>
          )}

        </div>

        {/* Right credits pane */}
        <div className="flex flex-col gap-6 text-zinc-400 font-semibold border-t lg:border-t-0 border-zinc-900 pt-8 lg:pt-0">
          
          <div className="bg-zinc-950/50 border border-zinc-900 rounded-2xl p-6 flex flex-col gap-5 text-xs font-mono">
            
            <div className="flex flex-col gap-1.5">
              <span className="text-zinc-650 font-bold uppercase tracking-wider text-[10px] flex items-center gap-1">
                <Video className="w-3.5 h-3.5 text-[#ef4444]" />
                <span>Direção / Produção</span>
              </span>
              <span className="text-zinc-250 font-bold text-xs">{currentContent.directors.join(', ')}</span>
            </div>

            <div className="flex flex-col gap-1.5">
              <span className="text-zinc-650 font-bold uppercase tracking-wider text-[10px] flex items-center gap-1">
                <User className="w-3.5 h-3.5 text-[#ef4444]" />
                <span>Elenco / Apresentação</span>
              </span>
              <span className="text-zinc-250 font-bold text-xs leading-relaxed">{currentContent.cast.join(', ')}</span>
            </div>

            <div className="flex flex-col gap-1.5">
              <span className="text-zinc-650 font-bold uppercase tracking-wider text-[10px]">Popularidade F5</span>
              <span className="text-zinc-300 font-bold text-xs">{currentContent.viewsCount.toLocaleString('pt-BR')} visualizações</span>
            </div>

            <div className="flex flex-col gap-1.5">
              <span className="text-zinc-650 font-bold uppercase tracking-wider text-[10px]">Categoria Geral</span>
              <span className="text-zinc-300 font-bold text-xs text-red-500">{categoryName}</span>
            </div>

            <div className="flex flex-col gap-1.5">
              <span className="text-zinc-650 font-bold uppercase tracking-wider text-[10px]">Tags do Conteúdo</span>
              <div className="flex flex-wrap gap-1 mt-1 text-[9px] uppercase tracking-wider">
                {currentContent.tags.map((tg, i) => (
                  <span key={i} className="bg-zinc-900 text-zinc-500 border border-zinc-850 px-2 py-0.5 rounded">{tg}</span>
                ))}
              </div>
            </div>

          </div>

          {/* Related Recommendations cards */}
          {relatedContent.length > 0 && (
            <div className="flex flex-col gap-4">
              <h3 className="text-xs font-mono font-black uppercase text-zinc-500 tracking-wider">Você Também Pode Curtir</h3>
              <div className="flex flex-col gap-3">
                {relatedContent.map((related) => (
                  <div
                    key={related.id}
                    onClick={() => navigate(`/app/conteudo/${related.id}`)}
                    className="flex gap-3 bg-zinc-950 p-2 rounded-xl border border-zinc-900 hover:border-zinc-800 transition cursor-pointer group"
                  >
                    <div className="w-16 h-20 rounded bg-zinc-900 overflow-hidden shrink-0">
                      <img 
                        src={related.coverUrl} 
                        alt={related.title} 
                        referrerPolicy="no-referrer"
                        className="w-full h-full object-cover group-hover:opacity-100 opacity-70 transition"
                      />
                    </div>
                    <div className="flex-1 flex flex-col justify-center text-left">
                      <span className="text-[9px] font-mono font-bold text-[#ef4444] uppercase tracking-wider">{related.genre}</span>
                      <h4 className="font-bold text-xs text-white/90 group-hover:text-white line-clamp-1 mt-0.5">{related.title}</h4>
                      <span className="text-[10px] text-zinc-650 mt-1">{related.duration}</span>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          )}

        </div>

      </div>

      {/* RATINGS & COMMUNITY COMMENTS */}
      <section className="border-t border-zinc-90 w-full bg-[#070707] py-16 px-6 md:px-8">
        <div className="max-w-7xl mx-auto flex flex-col gap-8">
          
          <div className="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 pb-6 border-b border-zinc-900">
            <div>
              <h3 className="text-md font-bold text-white uppercase tracking-wider font-mono flex items-center gap-2">
                <Star className="w-5 h-5 text-amber-500 fill-amber-500" />
                <span>Avaliações F5 TV</span>
              </h3>
              <p className="text-zinc-500 text-xs font-semibold mt-1">Sua opinião é vital para a evolução de nosso portfólio de notícias e entretenimento.</p>
            </div>
            
            <div className="flex items-center gap-5 bg-zinc-950 border border-zinc-900 px-4 py-2.5 rounded-xl font-mono">
              <div className="flex flex-col items-center">
                <span className="text-[9px] text-zinc-555 uppercase font-black">Score社区</span>
                <span className="text-lg font-black text-amber-500 mt-0.5">{averageRating} ★</span>
              </div>
              <div className="h-8 w-[1px] bg-zinc-850" />
              <div className="flex flex-col items-center">
                <span className="text-[9px] text-zinc-555 uppercase font-black">Resenhas</span>
                <span className="text-base font-black text-zinc-350 mt-0.5">{contentReviews.length}</span>
              </div>
            </div>
          </div>

          <div className="grid grid-cols-1 lg:grid-cols-5 gap-12">
            
            {/* Input review box */}
            <div className="lg:col-span-2 bg-zinc-950 border border-zinc-900 p-6 rounded-2xl flex flex-col gap-4">
              <h4 className="text-xs font-mono font-black text-zinc-300 uppercase tracking-widest flex items-center gap-1 text-[#ef4444]">
                <MessageSquare className="w-4 h-4" />
                <span>Registrar seu feedback</span>
              </h4>
              
              {reviewSuccessMsg && (
                <div className="p-3 bg-emerald-950/40 border border-emerald-900 rounded-lg text-xs text-emerald-400 font-bold font-mono">
                  {reviewSuccessMsg}
                </div>
              )}

              <form onSubmit={handleSubmitReview} className="flex flex-col gap-4 mt-2">
                <div className="flex flex-col gap-1.5">
                  <span className="text-xs font-semibold text-zinc-400">Classifique este título:</span>
                  <div className="flex gap-1.5 mt-1">
                    {[1, 2, 3, 4, 5].map((star) => (
                      <button
                        key={star}
                        type="button"
                        onClick={() => setUserRating(star)}
                        className="cursor-pointer focus:outline-none transition hover:scale-110"
                        title={`${star} Estrelas`}
                      >
                        <Star 
                          className={`w-6 h-6 ${
                            star <= userRating 
                              ? 'text-amber-550 fill-amber-500' 
                              : 'text-zinc-800'
                          }`} 
                        />
                      </button>
                    ))}
                  </div>
                </div>

                <div className="flex flex-col gap-1.5">
                  <span className="text-xs font-semibold text-zinc-400">Seu Comentário Crítico:</span>
                  <textarea
                    rows={4}
                    value={reviewComment}
                    onChange={(e) => setReviewComment(e.target.value)}
                    placeholder="O que achou da abordagem crítica, elenco, roteiro ou captação técnica das câmeras?"
                    className="w-full bg-zinc-900/40 focus:bg-zinc-900 border border-zinc-900 focus:border-[#ef4444] rounded-lg p-3 text-xs outline-none text-zinc-200 leading-relaxed font-semibold"
                  />
                </div>

                <button
                  type="submit"
                  disabled={!reviewComment.trim()}
                  className="w-full bg-[#ef4444] hover:bg-red-700 disabled:opacity-40 disabled:cursor-not-allowed text-white font-bold py-2.5 px-4 rounded-xl text-xs uppercase tracking-wider font-mono cursor-pointer transition"
                >
                  Registrar Avaliação
                </button>
              </form>
            </div>

            {/* List reviews of current title */}
            <div className="lg:col-span-3 flex flex-col gap-4">
              <h4 className="text-xs font-mono font-black text-zinc-500 uppercase tracking-widest mb-1">Timeline de Comentários</h4>
              
              {contentReviews.length === 0 ? (
                <div className="p-8 bg-zinc-950/20 border border-zinc-900 rounded-2xl flex flex-col items-center justify-center text-center text-zinc-600 font-mono text-xs gap-3">
                  <MessageSquare className="w-8 h-8 text-zinc-800" />
                  <p>Inicie a discussão! Seja o primeiro a registrar sua opinião técnica sobre este programa.</p>
                </div>
              ) : (
                <div className="flex flex-col gap-4 max-h-[450px] overflow-y-auto pr-2 scrollbar-thin">
                  {contentReviews.map((rev) => (
                    <div 
                      key={rev.id}
                      className="p-5 bg-zinc-950/80 border border-zinc-900 rounded-xl flex flex-col gap-2"
                    >
                      <div className="flex justify-between items-center">
                        <div className="flex items-center gap-2">
                          <div className="w-6 h-6 bg-red-950 border border-red-900/60 rounded-full flex items-center justify-center text-xs font-black text-white uppercase font-mono">
                            {rev.profileName.charAt(0)}
                          </div>
                          <span className="font-bold text-xs text-zinc-200">{rev.profileName}</span>
                        </div>
                        <div className="flex items-center gap-1 text-xs text-amber-500">
                          {Array.from({ length: rev.rating }).map((_, i) => (
                            <Star key={i} className="w-3.5 h-3.5 fill-current" />
                          ))}
                        </div>
                      </div>
                      <p className="text-xs text-zinc-400 font-semibold leading-relaxed mt-1">{rev.comment}</p>
                      <span className="text-[9px] font-mono text-zinc-650 self-end mt-1">{new Date(rev.createdAt).toLocaleDateString()}</span>
                    </div>
                  ))}
                </div>
              )}
            </div>

          </div>

        </div>
      </section>

    </div>
  );
};

export default ContentDetailsPage;

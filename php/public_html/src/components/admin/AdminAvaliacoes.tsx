import React, { useState } from 'react';
import { useData } from '../../context/DataContext';
import { Review } from '../../types';
import { Star, CheckCircle, AlertOctagon, Trash2, ShieldAlert, Sparkles, SlidersHorizontal, AlertCircle } from 'lucide-react';

export const AdminAvaliacoes: React.FC = () => {
  const { reviews, updateReviews, contents } = useData();
  const [filterRating, setFilterRating] = useState('all');
  const [filterStatus, setFilterStatus] = useState('all');

  const getContentName = (contentId: string) => {
    const matched = contents.find(c => c.id === contentId);
    return matched ? matched.title : 'Conteúdo Desconhecido';
  };

  const handleApprove = (id: string) => {
    const updated = reviews.map(r => 
      r.id === id ? { ...r, status: 'approved' as const } : r
    );
    updateReviews(updated);
  };

  const handleReject = (id: string) => {
    const updated = reviews.map(r => 
      r.id === id ? { ...r, status: 'hidden' as const } : r
    );
    updateReviews(updated);
  };

  const handleDelete = (id: string) => {
    if (confirm('Tem certeza que deseja banir/excluir este comentário do assinante permanentemente?')) {
      const filtered = reviews.filter(r => r.id !== id);
      updateReviews(filtered);
    }
  };

  const filteredReviews = reviews.filter(r => {
    const matchesRating = filterRating === 'all' 
      ? true 
      : Math.round(r.rating) === Number(filterRating);

    const matchesStatus = filterStatus === 'all'
      ? true
      : filterStatus === 'approved' 
        ? r.status === 'approved' || r.status === 'active'
        : r.status === filterStatus;

    return matchesRating && matchesStatus;
  });

  return (
    <section className="flex flex-col gap-6 animate-fade-in text-white text-left font-sans select-none">
      <div className="border-b border-zinc-900 pb-5">
        <span className="text-xs font-mono font-bold text-zinc-500 uppercase">MODERAÇÃO INTERATIVA</span>
        <h1 className="text-2xl font-black tracking-tight mt-1 font-sans">Avaliações & Comentários</h1>
      </div>

      {/* Sliders and filters */}
      <div className="bg-zinc-950 border border-zinc-900 p-4 rounded-xl flex flex-wrap gap-4 items-center text-xs justify-between">
        <div className="flex items-center gap-2">
          <SlidersHorizontal className="w-4 h-4 text-red-500" />
          <span className="font-bold uppercase font-mono text-zinc-400">Filtros de Moderação</span>
        </div>

        <div className="flex flex-wrap gap-3">
          <div className="flex items-center gap-1.5">
            <span className="text-zinc-500 font-bold uppercase font-mono text-[10px]">Filtrar Nota:</span>
            <select
              value={filterRating}
              onChange={e => setFilterRating(e.target.value)}
              className="bg-zinc-900 border border-zinc-800 p-2 rounded text-white"
            >
              <option value="all">Todas as Notas</option>
              <option value="5">5 estrelas</option>
              <option value="4">4 estrelas</option>
              <option value="3">3 estrelas</option>
              <option value="2">2 estrelas</option>
              <option value="1">1 estrela</option>
            </select>
          </div>

          <div className="flex items-center gap-1.5">
            <span className="text-zinc-500 font-bold uppercase font-mono text-[10px]">Filtrar Status:</span>
            <select
              value={filterStatus}
              onChange={e => setFilterStatus(e.target.value)}
              className="bg-zinc-900 border border-zinc-800 p-2 rounded text-white"
            >
              <option value="all">Todos os Status</option>
              <option value="pending">Pendentes</option>
              <option value="approved">Aprovados</option>
              <option value="hidden">Rejeitados / Ocultados</option>
            </select>
          </div>
        </div>
      </div>

      {/* list grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
        {filteredReviews.length === 0 ? (
          <div className="md:col-span-2 bg-zinc-950 border border-zinc-900 p-16 text-center text-zinc-650 flex flex-col items-center justify-center gap-2 text-xs">
            <AlertCircle className="w-8 h-8 text-zinc-850" />
            <p>Nenhuma avaliação atende a este critério de filtro.</p>
          </div>
        ) : (
          filteredReviews.map((r) => (
            <div key={r.id} className="bg-zinc-950 border border-zinc-900 p-5 rounded-2xl flex flex-col gap-4 relative justify-between">
              
              <div className="flex flex-col gap-1.5 text-xs">
                <div className="flex justify-between items-start gap-4">
                  <span className="font-mono text-zinc-400 font-extrabold truncate">@{r.userName}</span>
                  <div className="flex items-center gap-1 text-zinc-500 text-[10px] font-mono">
                    <span>{r.date}</span>
                  </div>
                </div>

                <div className="flex items-center gap-1.5 my-1">
                  <div className="flex gap-0.5" title={`${r.rating}/5`}>
                    {[...Array(5)].map((_, i) => (
                      <Star 
                        key={i} 
                        className={`w-3.5 h-3.5 ${i < r.rating ? 'fill-red-500 text-red-500' : 'text-zinc-800'}`} 
                      />
                    ))}
                  </div>
                  <span className="text-zinc-500 font-bold font-mono text-[10px] uppercase">sobre:</span>
                  <span className="text-zinc-200 font-black truncate max-w-xs">{getContentName(r.contentId)}</span>
                </div>

                <p className="text-zinc-450 font-semibold leading-relaxed p-3 bg-zinc-900/40 border border-zinc-900 rounded-xl leading-relaxed italic text-xs">
                  "{r.comment}"
                </p>
              </div>

              <div className="flex items-center justify-between border-t border-zinc-900 pt-4 mt-2">
                <span className={`text-[9px] font-mono font-bold uppercase tracking-widest px-1.5 py-0.5 rounded ${
                  r.status === 'approved' || r.status === 'active' ? 'bg-green-950/50 text-green-400 border border-green-955' :
                  r.status === 'pending' ? 'bg-amber-950/40 text-amber-500 border border-amber-900/60' :
                  'bg-zinc-900 text-zinc-650'
                }`}>
                  {r.status === 'approved' || r.status === 'active' ? 'Aprovado' : r.status === 'pending' ? 'Pendente' : 'Ocultado'}
                </span>

                <div className="flex gap-1">
                  {r.status === 'pending' && (
                    <button
                      onClick={() => handleApprove(r.id)}
                      className="p-1.5 hover:bg-zinc-900 border border-zinc-900 hover:border-zinc-800 rounded text-green-500 hover:text-green-400 transition flex items-center gap-1 text-[10px] font-mono font-bold uppercase"
                      title="Aprovar e Publicar Comentário"
                    >
                      <CheckCircle className="w-3.5 h-3.5" />
                      <span>Aprovar</span>
                    </button>
                  )}
                  {r.status !== 'hidden' && (
                    <button
                      onClick={() => handleReject(r.id)}
                      className="p-1.5 hover:bg-zinc-900 border border-zinc-900 hover:border-zinc-800 rounded text-amber-500 hover:text-amber-400 transition flex items-center gap-1 text-[10px] font-mono font-bold uppercase"
                      title="Ocultar do Feed"
                    >
                      <ShieldAlert className="w-3.5 h-3.5" />
                      <span>Rejeitar</span>
                    </button>
                  )}
                  <button
                    onClick={() => handleDelete(r.id)}
                    className="p-1.5 hover:bg-zinc-900 border border-zinc-900 hover:border-red-955 rounded text-zinc-550 hover:text-red-500 transition"
                    title="Banir Comentário"
                  >
                    <Trash2 className="w-3.5 h-3.5" />
                  </button>
                </div>
              </div>
            </div>
          ))
        )}
      </div>
    </section>
  );
};
export default AdminAvaliacoes;

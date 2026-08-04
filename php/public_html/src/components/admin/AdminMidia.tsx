import React, { useState } from 'react';
import { useData } from '../../context/DataContext';
import { Upload } from '../../types';
import { Image, Video, Copy, ClipboardCheck, Trash2, FolderOpen, Play, RefreshCw, Layers, AlertCircle } from 'lucide-react';

export const AdminMidia: React.FC = () => {
  const { uploads, updateUploads } = useData();
  const [copiedId, setCopiedId] = useState<string | null>(null);
  const [categoryFilter, setCategoryFilter] = useState('all');

  const handleCopyLink = (url: string, id: string) => {
    navigator.clipboard.writeText(url);
    setCopiedId(id);
    setTimeout(() => setCopiedId(null), 2000);
  };

  const handleDelete = (id: string) => {
    if (confirm('Deseja realmente deletar este arquivo físico de mídia do storage simulado?')) {
      const filtered = uploads.filter(u => u.id !== id);
      updateUploads(filtered);
    }
  };

  const filteredUploads = categoryFilter === 'all'
    ? uploads
    : uploads.filter(u => u.categoryId === categoryFilter);

  return (
    <section className="flex flex-col gap-6 animate-fade-in text-white text-left font-sans select-none">
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between justify-start gap-4 border-b border-zinc-900 pb-5">
        <div>
          <span className="text-xs font-mono font-bold text-zinc-500 uppercase">BIBLIOTECA DE ATIVOS</span>
          <h1 className="text-2xl font-black tracking-tight mt-1">Nuvem & Mídia Compartilhada</h1>
        </div>

        <div className="flex gap-2">
          <select
            value={categoryFilter}
            onChange={e => setCategoryFilter(e.target.value)}
            className="bg-zinc-950 border border-zinc-900 p-2 text-xs rounded-xl font-semibold outline-none text-zinc-300 focus:border-[#ef4444]"
          >
            <option value="all">Todas as Categorias</option>
            <option value="cat-series">Séries / Ficção</option>
            <option value="cat-documentarios">Documentários</option>
            <option value="cat-esportes">Canais Esportivos</option>
            <option value="cat-bastidores">Bastidores F5</option>
            <option value="cat-infantil">Infantil / Kids</option>
          </select>
        </div>
      </div>

      {/* Storage highlights */}
      <div className="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div className="bg-zinc-950 border border-zinc-900 p-4 rounded-xl">
          <span className="text-zinc-550 font-mono font-bold text-[9px] uppercase tracking-wider block">Armazenamento Usado</span>
          <h3 className="text-lg font-black mt-1">3.15 Terabytes</h3>
          <span className="text-[10px] text-zinc-500 font-mono">SSD Elastic Cloud Storage</span>
        </div>
        <div className="bg-zinc-950 border border-zinc-900 p-4 rounded-xl">
          <span className="text-zinc-550 font-mono font-bold text-[9px] uppercase tracking-wider block">Total de Arquivos</span>
          <h3 className="text-lg font-black mt-1">{uploads.length} Arquivos de Mídia</h3>
          <span className="text-[10px] text-[#ef4444] font-mono">98% Saudáveis & Indexados</span>
        </div>
        <div className="bg-zinc-950 border border-zinc-900 p-4 rounded-xl">
          <span className="text-zinc-550 font-mono font-bold text-[9px] uppercase tracking-wider block">Codec Recomendado</span>
          <h3 className="text-lg font-black mt-1">H.264 MP4 / HEVC</h3>
          <span className="text-[10px] text-zinc-500 font-mono">Auto-transcodificação ativa</span>
        </div>
      </div>

      {/* Grid of files */}
      <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
        {filteredUploads.length === 0 ? (
          <div className="col-span-full py-16 text-center text-zinc-650 flex flex-col items-center justify-center gap-2 text-xs bg-zinc-950 border border-zinc-900 rounded-xl">
            <AlertCircle className="w-8 h-8 text-zinc-850" />
            <p>Nenhum ativo de mídia encontrado nessa categoria.</p>
          </div>
        ) : (
          filteredUploads.map((u) => {
            const isVideo = u.fileType.startsWith('video');
            return (
              <div key={u.id} className="bg-zinc-950 border border-zinc-900 rounded-2xl overflow-hidden flex flex-col justify-between group">
                <div className="relative aspect-video bg-zinc-900 flex items-center justify-center overflow-hidden">
                  {u.coverUrl ? (
                    <img 
                      src={u.coverUrl} 
                      alt={u.title} 
                      className="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                      referrerPolicy="no-referrer"
                    />
                  ) : (
                    <FolderOpen className="w-8 h-8 text-zinc-800" />
                  )}

                  <div className="absolute top-2 left-2 px-2 py-0.5 rounded bg-zinc-950/70 backdrop-blur-md text-[8px] font-mono font-bold tracking-widest uppercase border border-white/5">
                    {isVideo ? 'video' : 'imagem'}
                  </div>

                  <div className="absolute top-2 right-2 px-2 py-0.5 rounded bg-zinc-950/70 backdrop-blur-md text-[8px] font-mono font-bold tracking-widest uppercase text-zinc-400 border border-white/5">
                    {u.size}
                  </div>
                </div>

                <div className="p-4 flex-1 flex flex-col justify-between gap-3 text-xs">
                  <div className="flex flex-col gap-1">
                    <h4 className="font-extrabold text-zinc-100 line-clamp-1">{u.title || u.fileName}</h4>
                    <span className="text-zinc-500 font-mono block text-[9px] truncate max-w-[200px]" title={u.fileName}>{u.fileName}</span>
                  </div>

                  <div className="flex flex-col gap-1.5 border-t border-zinc-900 pt-3">
                    <span className="text-[10px] font-mono font-bold text-zinc-550 uppercase">Simular Transcodificador</span>
                    <div className="flex items-center gap-2">
                      <span className={`text-[8px] tracking-widest font-bold uppercase font-mono px-1.5 py-0.5 rounded ${
                        u.status === 'ready' ? 'bg-green-950 text-green-400 border border-green-900' :
                        u.status === 'processing' ? 'bg-blue-950 text-blue-400 border border-blue-900 animate-pulse' :
                        'bg-red-950 text-red-400 border border-red-900'
                      }`}>
                        {u.status === 'ready' ? 'Pronto (Sinal 1080p)' : 'Transcodificando'}
                      </span>
                    </div>
                  </div>

                  <div className="flex items-center justify-between border-t border-zinc-900 pt-3.5 mt-1 gap-2">
                    <button
                      onClick={() => handleCopyLink(u.videoUrl || u.coverUrl, u.id)}
                      className="flex-1 flex items-center justify-center gap-1 bg-zinc-900 hover:bg-zinc-850 p-2 rounded-lg font-mono font-bold text-[9px] uppercase border border-zinc-850 text-zinc-400 hover:text-white transition"
                    >
                      {copiedId === u.id ? (
                        <>
                          <ClipboardCheck className="w-3.5 h-3.5 text-green-500" />
                          <span>Copiado!</span>
                        </>
                      ) : (
                        <>
                          <Copy className="w-3.5 h-3.5" />
                          <span>Copiar Link</span>
                        </>
                      )}
                    </button>

                    <button
                      onClick={() => handleDelete(u.id)}
                      className="p-2 hover:bg-zinc-900 border border-zinc-900 hover:border-red-955 rounded-lg text-zinc-650 hover:text-red-500 transition shrink-0"
                      title="Deletar Ativo"
                    >
                      <Trash2 className="w-3.5 h-3.5" />
                    </button>
                  </div>
                </div>
              </div>
            );
          })
        )}
      </div>
    </section>
  );
};
export default AdminMidia;

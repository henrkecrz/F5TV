import React, { useState, useEffect } from 'react';
import { useNavigate, useSearchParams } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';
import { useData } from '../../context/DataContext';
import { Search, Grid, Clock, Star, Play, SlidersHorizontal, AlertCircle } from 'lucide-react';

export const SearchPage: React.FC = () => {
  const { currentUser, currentProfile } = useAuth();
  const { contents, categories } = useData();
  const navigate = useNavigate();
  const [searchParams, setSearchParams] = useSearchParams();

  // Active query parameters
  const searchQuery = searchParams.get('q') || '';
  const selectedType = searchParams.get('type') || 'all';
  const selectedCategory = searchParams.get('category') || 'all';
  const selectedGenre = searchParams.get('genre') || 'all';

  const [localSearch, setLocalSearch] = useState(searchQuery);

  useEffect(() => {
    setLocalSearch(searchQuery);
  }, [searchQuery]);

  if (!currentUser || !currentProfile) {
    return null;
  }

  const handleSearchSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    updateParams({ q: localSearch.trim() });
  };

  const updateParams = (newParams: Record<string, string>) => {
    const nextParams = new URLSearchParams(searchParams);
    Object.entries(newParams).forEach(([key, value]) => {
      if (value === 'all' || value === '') {
        nextParams.delete(key);
      } else {
        nextParams.set(key, value);
      }
    });
    setSearchParams(nextParams);
  };

  // Filter criteria logic
  const filteredContents = contents.filter((item) => {
    if (item.status !== 'published') return false;

    // Search query match
    if (searchQuery) {
      const q = searchQuery.toLowerCase().trim();
      const matchTitle = item.title.toLowerCase().includes(q);
      const matchDesc = item.shortDescription.toLowerCase().includes(q) || item.fullDescription.toLowerCase().includes(q);
      const matchTags = item.tags.some(t => t.toLowerCase().includes(q));
      const matchGenre = item.genre.toLowerCase().includes(q);
      if (!matchTitle && !matchDesc && !matchTags && !matchGenre) return false;
    }

    // Type match
    // Mapping types as requested: 'series', 'news', 'documentary', 'sports'
    if (selectedType !== 'all') {
      const genreAndTags = `${item.genre} ${item.tags.join(' ')}`.toLowerCase();
      if (selectedType === 'series') {
        const titleLower = item.title.toLowerCase();
        const isSeries = item.tags.some(t => t.toLowerCase() === 'episódio' || t.toLowerCase() === 'temporada' || t.toLowerCase() === 'série');
        // Let's also check if there exists series matches
        if (!isSeries && !titleLower.includes('conexão') && !titleLower.includes('código') && !titleLower.includes('mundo f5 kids')) return false;
      } else if (selectedType === 'news') {
        const isNews = item.categoryId === 'cat-jornalismo' || genreAndTags.includes('jornalismo') || genreAndTags.includes('news') || genreAndTags.includes('notícia');
        if (!isNews) return false;
      } else if (selectedType === 'documentary') {
        const isDoc = item.categoryId === 'cat-documentarios' || genreAndTags.includes('doc') || genreAndTags.includes('documentário');
        if (!isDoc) return false;
      } else if (selectedType === 'sports') {
        const isSports = item.categoryId === 'cat-esportes' || genreAndTags.includes('esporte') || genreAndTags.includes('sports');
        if (!isSports) return false;
      }
    }

    // Category match
    if (selectedCategory !== 'all') {
      if (item.categoryId !== selectedCategory) return false;
    }

    // Genre match
    if (selectedGenre !== 'all') {
      if (item.genre.toLowerCase().trim() !== selectedGenre.toLowerCase().trim()) return false;
    }

    return true;
  });

  // Extract unique genres for picker chips
  const uniqueGenres = Array.from(
    new Set(contents.filter(c => c.status === 'published').map(c => c.genre.trim()))
  );

  return (
    <div id="search-layout-view" className="max-w-7xl mx-auto px-6 md:px-8 pt-6 animate-fade-in text-white font-sans">
      
      {/* Header and Input banner */}
      <div className="flex flex-col gap-6 md:flex-row md:items-center justify-between pb-6 border-b border-zinc-900 mb-6">
        <div>
          <span className="text-xs font-mono font-bold text-[#ef4444] uppercase tracking-widest">Painel de Pesquisa</span>
          <h1 className="text-2xl md:text-3xl font-black mt-1">Busca Inteligente</h1>
        </div>

        <form onSubmit={handleSearchSubmit} className="relative w-full max-w-md shrink-0">
          <Search className="absolute left-4 top-3.5 w-4 h-4 text-zinc-555" />
          <input
            type="text"
            value={localSearch}
            onChange={(e) => {
              setLocalSearch(e.target.value);
              // Live update query param for ultra smooth key matching search!
              updateParams({ q: e.target.value });
            }}
            placeholder="Digite palavras-chave, tags, diretores..."
            className="w-full bg-zinc-950 border border-zinc-900 focus:border-red-650 rounded-xl pl-11 pr-4 py-3 text-xs md:text-sm font-semibold outline-none text-white/90 placeholder:text-zinc-650 shadow-2xl"
          />
        </form>
      </div>

      {/* FILTER BOX CONTROLS PANEL */}
      <div className="bg-zinc-950/60 border border-zinc-90 w-full p-5 md:p-6 rounded-2xl flex flex-col gap-4 text-xs font-mono mb-8">
        <h3 className="font-extrabold uppercase tracking-widest text-[#ef4444] text-[10px] flex items-center gap-2">
          <SlidersHorizontal className="w-4 h-4" />
          <span>Filtros Rápidos do Portfólio</span>
        </h3>

        {/* Filters Select row */}
        <div className="grid grid-cols-1 sm:grid-cols-4 gap-4 mt-2">
          
          {/* 1. Filter Type */}
          <div className="flex flex-col gap-1.5 text-left">
            <span className="text-zinc-550 text-[10px] font-black uppercase">Filtro por Tipo</span>
            <select
              value={selectedType}
              onChange={(e) => updateParams({ type: e.target.value })}
              className="bg-zinc-900 border border-zinc-800 text-zinc-300 font-bold px-3 py-2 rounded-lg outline-none cursor-pointer text-xs"
            >
              <option value="all">Ver Tudo</option>
              <option value="series">🎬 Séries Exclusivas</option>
              <option value="news">📰 Jornalismo F5</option>
              <option value="documentary">🎥 Documentários</option>
              <option value="sports">⚽ Esportes Gerais</option>
            </select>
          </div>

          {/* 2. Filter Category */}
          <div className="flex flex-col gap-1.5 text-left">
            <span className="text-zinc-550 text-[10px] font-black uppercase">Categoria Trilha</span>
            <select
              value={selectedCategory}
              onChange={(e) => updateParams({ category: e.target.value })}
              className="bg-zinc-900 border border-zinc-800 text-zinc-300 font-bold px-3 py-2 rounded-lg outline-none cursor-pointer text-xs"
            >
              <option value="all">Todas Categorias</option>
              {categories.map((cat) => (
                <option key={cat.id} value={cat.id}>
                  {cat.name}
                </option>
              ))}
            </select>
          </div>

          {/* 3. Filter Genre */}
          <div className="flex flex-col gap-1.5 text-left">
            <span className="text-zinc-550 text-[10px] font-black uppercase">Filtro por Gêneros</span>
            <select
              value={selectedGenre}
              onChange={(e) => updateParams({ genre: e.target.value })}
              className="bg-zinc-900 border border-zinc-800 text-zinc-300 font-bold px-3 py-2 rounded-lg outline-none cursor-pointer text-xs"
            >
              <option value="all">Todos Gêneros</option>
              {uniqueGenres.map((gen, i) => (
                <option key={i} value={gen}>
                  {gen}
                </option>
              ))}
            </select>
          </div>

          {/* 4. Reset Button */}
          <div className="flex flex-col justify-end text-left">
            <button
              onClick={() => {
                setLocalSearch('');
                setSearchParams(new URLSearchParams());
              }}
              className="w-full bg-zinc-900 hover:bg-zinc-800 text-[#ef4444] uppercase font-bold py-2 rounded-lg border border-zinc-850 cursor-pointer transition text-center text-xs"
            >
              Limpar Filtros
            </button>
          </div>

        </div>
      </div>

      {/* Grid Results display */}
      <div className="border-b border-zinc-900 pb-3 mb-6 flex justify-between items-center">
        <span className="text-zinc-500 font-mono text-xs font-black uppercase">
          Resultado ({filteredContents.length} correspondências)
        </span>
        <Grid className="w-4 h-4 text-zinc-700" />
      </div>

      {filteredContents.length === 0 ? (
        <div className="bg-zinc-950 p-12 rounded-2xl flex flex-col items-center justify-center text-center max-w-lg mx-auto gap-4 border border-zinc-900 my-10 font-mono text-xs">
          <AlertCircle className="w-10 h-10 text-zinc-800 animate-bounce" />
          <div className="flex flex-col gap-1">
            <h3 className="font-extrabold uppercase text-zinc-200">Nenhum resultado para estes filtros</h3>
            <p className="text-zinc-650 text-[11px] leading-relaxed">
              Tente redefinir os seletores de gênero, tipo ou simplifique sua palavra de busca digitada.
            </p>
          </div>
          <button
            onClick={() => {
              setLocalSearch('');
              setSearchParams(new URLSearchParams());
            }}
            className="mt-3 px-4 py-2 bg-zinc-900 rounded border border-zinc-800 hover:border-zinc-750 text-[#ef4444] font-bold uppercase cursor-pointer transition text-[10px]"
          >
            Limpar Busca
          </button>
        </div>
      ) : (
        <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6 mb-16">
          {filteredContents.map((item) => (
            <div
              key={item.id}
              onClick={() => navigate(`/app/conteudo/${item.id}`)}
              className="group bg-zinc-950 border border-zinc-900 hover:border-red-600 rounded-xl overflow-hidden cursor-pointer transition transform hover:-translate-y-1 block animate-fade-in"
            >
              <div className="aspect-[3/4] relative bg-zinc-900 overflow-hidden">
                <img 
                  src={item.coverUrl} 
                  alt={item.title} 
                  referrerPolicy="no-referrer"
                  className="w-full h-full object-cover group-hover:scale-102 opacity-80 group-hover:opacity-100 transition duration-300"
                />
                <div className="absolute top-2 right-2 bg-black/85 text-[10px] font-mono font-bold text-gray-300 px-1.5 py-0.5 rounded border border-white/5">
                  {item.ageRating}
                </div>
                {item.isExclusive && (
                  <div className="absolute bottom-2 left-2 bg-red-650 text-[9px] font-bold text-white px-1.5 py-0.5 rounded uppercase font-mono tracking-wider shadow">
                    Exclusivo
                  </div>
                )}
              </div>
              <div className="p-3 text-left">
                <span className="text-[9px] font-mono uppercase text-[#ef4444] tracking-wider">{item.genre}</span>
                <h4 className="font-bold text-xs text-zinc-200 line-clamp-1 mt-0.5 group-hover:text-white transition">{item.title}</h4>
              </div>
            </div>
          ))}
        </div>
      )}

    </div>
  );
};

export default SearchPage;

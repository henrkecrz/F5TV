import React, { useState } from 'react';
import { useData } from '../../context/DataContext';
import { Channel } from '../../types';
import { Radio, Plus, Trash2, Edit2, Play, ToggleLeft, ToggleRight, Loader2, AlertCircle } from 'lucide-react';

export const AdminCanais: React.FC = () => {
  const { channels, updateChannels } = useData();
  const [showForm, setShowForm] = useState(false);
  const [editingId, setEditingId] = useState<string | null>(null);

  const [form, setForm] = useState({
    name: '',
    description: '',
    logoText: '',
    streamUrl: 'https://assets.mixkit.co/videos/preview/mixkit-software-developer-working-on-his-computer-34289-large.mp4',
    active: true,
    status: 'online' as 'online' | 'offline',
    category: 'Geral'
  });

  const handleEdit = (ch: Channel) => {
    setEditingId(ch.id);
    setForm({
      name: ch.name,
      description: ch.description,
      logoText: ch.logoText,
      streamUrl: ch.streamUrl,
      active: ch.active,
      status: ch.status,
      category: ch.category || 'Geral'
    });
    setShowForm(true);
  };

  const handleCancel = () => {
    setShowForm(false);
    setEditingId(null);
    setForm({
      name: '',
      description: '',
      logoText: '',
      streamUrl: 'https://assets.mixkit.co/videos/preview/mixkit-software-developer-working-on-his-computer-34289-large.mp4',
      active: true,
      status: 'online',
      category: 'Geral'
    });
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!form.name.trim() || !form.logoText.trim()) return;

    if (editingId) {
      const updated = channels.map(c => 
        c.id === editingId ? { ...c, ...form } : c
      );
      updateChannels(updated);
    } else {
      const newChannel: Channel = {
        id: `channel-${Date.now()}`,
        ...form
      };
      updateChannels([...channels, newChannel]);
    }
    handleCancel();
  };

  const toggleActive = (id: string) => {
    const updated = channels.map(c => 
      c.id === id ? { ...c, active: !c.active, status: !c.active ? 'online' as const : 'offline' as const } : c
    );
    updateChannels(updated);
  };

  const handleDelete = (id: string) => {
    if (confirm('Deseja realmente remover este canal ao vivo? Todos os agendamentos vinculados a ele podem ficar órfãos.')) {
      const filtered = channels.filter(c => c.id !== id);
      updateChannels(filtered);
    }
  };

  return (
    <section className="flex flex-col gap-6 animate-fade-in text-white text-left font-sans select-none">
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between justify-start gap-4 border-b border-zinc-900 pb-5">
        <div>
          <span className="text-xs font-mono font-bold text-zinc-500 uppercase">EMISSORAS INTEGRADAS</span>
          <h1 className="text-2xl font-black tracking-tight mt-1">Canais ao Vivo</h1>
        </div>
        {!showForm && (
          <button
            onClick={() => setShowForm(true)}
            className="flex items-center gap-2 bg-[#ef4444] hover:bg-red-700 text-white text-xs font-bold font-mono uppercase tracking-wider px-4 py-2.5 rounded-xl transition cursor-pointer"
          >
            <Plus className="w-4 h-4" />
            <span>Adicionar Emissora</span>
          </button>
        )}
      </div>

      {showForm && (
        <div className="bg-zinc-950 border border-zinc-900 p-6 rounded-xl">
          <h3 className="text-sm font-bold uppercase font-mono text-zinc-400 mb-4 pb-2 border-b border-zinc-900">
            {editingId ? 'Editar Canal' : 'Instanciar Nova Emissora'}
          </h3>
          <form onSubmit={handleSubmit} className="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div className="flex flex-col gap-1 text-xs">
              <label className="text-zinc-550 font-bold uppercase font-mono">Nome da Emissora</label>
              <input
                type="text"
                required
                value={form.name}
                onChange={e => setForm({ ...form, name: e.target.value })}
                placeholder="Ex: F5 Sports Premium, F5 News 24h"
                className="bg-zinc-900 border border-zinc-800 p-2.5 rounded text-white outline-none"
              />
            </div>

            <div className="flex flex-col gap-1 text-xs">
              <label className="text-zinc-550 font-bold uppercase font-mono">Logotipo Curto (Visualizador)</label>
              <input
                type="text"
                required
                value={form.logoText}
                onChange={e => setForm({ ...form, logoText: e.target.value })}
                placeholder="Ex: F5 SPORTS, F5 NEWS"
                className="bg-zinc-900 border border-zinc-800 p-2.5 rounded text-white outline-none"
              />
            </div>

            <div className="flex flex-col gap-1 text-xs">
              <label className="text-zinc-550 font-bold uppercase font-mono">Categoria / Gênero</label>
              <input
                type="text"
                value={form.category}
                onChange={e => setForm({ ...form, category: e.target.value })}
                placeholder="Ex: Esportes, Geral, Infantil, Filmes"
                className="bg-zinc-900 border border-zinc-800 p-2.5 rounded text-white outline-none"
              />
            </div>

            <div className="flex flex-col gap-1 text-xs md:col-span-3">
              <label className="text-zinc-550 font-bold uppercase font-mono">Resumo de Linha Editorial</label>
              <input
                type="text"
                value={form.description}
                onChange={e => setForm({ ...form, description: e.target.value })}
                placeholder="Ex: Transmissões de jogos de basquete regionais, automotivos e especiais..."
                className="bg-zinc-900 border border-zinc-800 p-2.5 rounded text-white outline-none"
              />
            </div>

            <div className="flex flex-col gap-1 text-xs md:col-span-2">
              <label className="text-zinc-550 font-bold uppercase font-mono">Stream Source URL (HL/MP4/HLS)</label>
              <input
                type="text"
                required
                value={form.streamUrl}
                onChange={e => setForm({ ...form, streamUrl: e.target.value })}
                className="bg-zinc-900 border border-zinc-800 p-2.5 rounded text-white outline-none font-mono"
              />
            </div>

            <div className="flex flex-col gap-1 text-xs">
              <label className="text-zinc-550 font-bold uppercase font-mono">Status da Emissão</label>
              <select
                value={form.status}
                onChange={e => setForm({ ...form, status: e.target.value as any })}
                className="bg-zinc-900 border border-zinc-800 p-2.5 rounded text-white"
              >
                <option value="online">Online (Sinal OK)</option>
                <option value="offline">Sinal Offline (Manutenção)</option>
              </select>
            </div>

            <div className="md:col-span-3 flex justify-end gap-3 mt-4 border-t border-zinc-900 pt-4">
              <button
                type="button"
                onClick={handleCancel}
                className="px-4 py-2 bg-zinc-900 hover:bg-zinc-850 text-zinc-400 hover:text-white rounded-lg text-xs font-mono font-bold uppercase transition"
              >
                Cancelar
              </button>
              <button
                type="submit"
                className="px-5 py-2 bg-[#ef4444] hover:bg-red-700 text-white rounded-lg text-xs font-mono font-bold uppercase tracking-wider transition cursor-pointer"
              >
                {editingId ? 'Confirmar Alterações' : 'Criar Emissora'}
              </button>
            </div>
          </form>
        </div>
      )}

      {/* Table grid listing */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
        {channels.map(c => (
          <div key={c.id} className="bg-zinc-950 border border-zinc-900 p-5 rounded-2xl flex flex-col gap-4 relative overflow-hidden">
            <div className="absolute right-4 top-4 font-mono font-bold text-[8px] bg-white/[0.03] border border-white/5 px-2 py-0.5 rounded text-zinc-500 pointer-events-none uppercase">
              {c.id}
            </div>

            <div className="flex items-start gap-4">
              <div className="w-12 h-12 bg-red-650 rounded-xl flex items-center justify-center font-black tracking-tighter text-xs shrink-0 select-none shadow-md shadow-red-950/20 text-white self-center">
                {c.logoText}
              </div>
              <div className="flex flex-col text-xs">
                <span className="font-extrabold text-base text-zinc-100">{c.name}</span>
                <span className="text-zinc-500 font-bold uppercase block mt-0.5 font-mono tracking-widest text-[9px]">Gênero: {c.category || 'Geral'}</span>
              </div>
            </div>

            <p className="text-zinc-400 text-xs font-semibold leading-relaxed line-clamp-2">
              {c.description}
            </p>

            <div className="bg-zinc-900/40 border border-zinc-900 text-zinc-500 font-mono text-[10px] p-2.5 rounded-lg truncate">
              {c.streamUrl}
            </div>

            <div className="flex items-center justify-between border-t border-zinc-900 pt-4 mt-1">
              <div className="flex items-center gap-2">
                <button
                  onClick={() => toggleActive(c.id)}
                  className="focus:outline-none transition shrink-0"
                  title={c.active ? 'Desativar Canal' : 'Ativar Canal'}
                >
                  {c.active ? (
                    <div className="flex items-center gap-1.5 text-green-550 text-xs">
                      <ToggleRight className="w-5 h-5 text-green-500" />
                      <span className="font-bold text-[10px] uppercase font-mono text-green-500">No Ar (Ativo)</span>
                    </div>
                  ) : (
                    <div className="flex items-center gap-1.5 text-zinc-500 text-xs">
                      <ToggleLeft className="w-5 h-5" />
                      <span className="font-bold text-[10px] uppercase font-mono">Fora do Ar</span>
                    </div>
                  )}
                </button>
              </div>

              <div className="flex gap-2">
                <button
                  onClick={() => handleEdit(c)}
                  className="p-2 hover:bg-zinc-900 border border-zinc-900 hover:border-zinc-800 rounded-lg text-zinc-400 hover:text-white transition"
                  title="Editar Canal"
                >
                  <Edit2 className="w-3.5 h-3.5" />
                </button>
                <button
                  onClick={() => handleDelete(c.id)}
                  className="p-2 hover:bg-zinc-900 border border-zinc-900 hover:border-red-955 rounded-lg text-zinc-500 hover:text-red-500 transition"
                  title="Excluir Canal"
                >
                  <Trash2 className="w-3.5 h-3.5" />
                </button>
              </div>
            </div>
          </div>
        ))}
      </div>
    </section>
  );
};
export default AdminCanais;

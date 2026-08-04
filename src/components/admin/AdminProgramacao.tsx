import React, { useState } from 'react';
import { useData } from '../../context/DataContext';
import { LiveSchedule, Channel } from '../../types';
import { Calendar, Clock, Plus, Trash2, Edit2, Play, AlertCircle, RefreshCw } from 'lucide-react';

export const AdminProgramacao: React.FC = () => {
  const { liveSchedules, updateLiveSchedules, channels } = useData();
  const [showForm, setShowForm] = useState(false);
  const [editingId, setEditingId] = useState<string | null>(null);

  const [form, setForm] = useState({
    channelId: channels[0]?.id || '',
    title: '',
    description: '',
    host: '',
    date: '2026-05-27',
    startTime: '14:00',
    endTime: '15:30',
    status: 'scheduled' as 'scheduled' | 'live' | 'ended' | 'rerun' | 'premiere',
    imageUrl: 'https://images.unsplash.com/photo-1585829365295-ab7cd400c167?q=80&w=400',
    isFeatured: false
  });

  const handleEdit = (ls: LiveSchedule) => {
    setEditingId(ls.id);
    setForm({
      channelId: ls.channelId,
      title: ls.title,
      description: ls.description,
      host: ls.host,
      date: ls.date,
      startTime: ls.startTime,
      endTime: ls.endTime,
      status: ls.status,
      imageUrl: ls.imageUrl || 'https://images.unsplash.com/photo-1585829365295-ab7cd400c167?q=80&w=400',
      isFeatured: ls.isFeatured || false
    });
    setShowForm(true);
  };

  const handleCancel = () => {
    setShowForm(false);
    setEditingId(null);
    setForm({
      channelId: channels[0]?.id || '',
      title: '',
      description: '',
      host: '',
      date: '2026-05-27',
      startTime: '14:00',
      endTime: '15:30',
      status: 'scheduled',
      imageUrl: 'https://images.unsplash.com/photo-1585829365295-ab7cd400c167?q=80&w=400',
      isFeatured: false
    });
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!form.title.trim()) return;

    if (editingId) {
      const updatedSchedules = liveSchedules.map(ls => 
        ls.id === editingId ? { ...ls, ...form } : ls
      );
      updateLiveSchedules(updatedSchedules);
    } else {
      const newSchedule: LiveSchedule = {
        id: `live-${Date.now()}`,
        ...form
      };
      updateLiveSchedules([newSchedule, ...liveSchedules]);
    }
    handleCancel();
  };

  const handleDelete = (id: string) => {
    if (confirm('Tem certeza que deseja excluir esta grade de programação ao vivo?')) {
      const filtered = liveSchedules.filter(ls => ls.id !== id);
      updateLiveSchedules(filtered);
    }
  };

  const getChannelName = (channelId: string) => {
    const ch = channels.find(c => c.id === channelId);
    return ch ? ch.name : 'Outro Canal';
  };

  const getStatusColor = (status: LiveSchedule['status']) => {
    switch (status) {
      case 'live': return 'bg-red-600 text-white animate-pulse';
      case 'scheduled': return 'bg-blue-900/40 text-blue-300 border border-blue-900/60';
      case 'ended': return 'bg-zinc-900 text-zinc-500';
      case 'rerun': return 'bg-amber-950 text-amber-500 border border-amber-900';
      case 'premiere': return 'bg-purple-950 text-purple-400 border border-purple-900';
      default: return 'bg-zinc-800 text-zinc-300';
    }
  };

  return (
    <section className="flex flex-col gap-6 animate-fade-in text-white text-left font-sans select-none">
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between justify-start gap-4 border-b border-zinc-900 pb-5">
        <div>
          <span className="text-xs font-mono font-bold text-zinc-500 uppercase">TRANSMISSÃO AO VIVO</span>
          <h1 className="text-2xl font-black tracking-tight mt-1">Grade de Programação</h1>
        </div>
        {!showForm && (
          <button
            onClick={() => setShowForm(true)}
            className="flex items-center gap-2 bg-[#ef4444] hover:bg-red-700 text-white text-xs font-bold font-mono uppercase tracking-wider px-4 py-2.5 rounded-xl transition cursor-pointer"
          >
            <Plus className="w-4 h-4" />
            <span>Adicionar Transmissão</span>
          </button>
        )}
      </div>

      {showForm && (
        <div className="bg-zinc-950 border border-zinc-900 p-6 rounded-xl">
          <h3 className="text-sm font-bold uppercase font-mono text-zinc-400 mb-4 pb-2 border-b border-zinc-900">
            {editingId ? 'Editar Grade Existente' : 'Programar Nova Transmissão'}
          </h3>
          <form onSubmit={handleSubmit} className="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div className="flex flex-col gap-1 text-xs">
              <label className="text-zinc-550 font-bold uppercase font-mono">Canal Transmissor</label>
              <select
                value={form.channelId}
                onChange={e => setForm({ ...form, channelId: e.target.value })}
                className="bg-zinc-900 border border-zinc-800 p-2.5 rounded text-white"
              >
                {channels.map(c => (
                  <option key={c.id} value={c.id}>{c.name}</option>
                ))}
              </select>
            </div>

            <div className="flex flex-col gap-1 text-xs md:col-span-2">
              <label className="text-zinc-555 font-bold uppercase font-mono">Título do Programa</label>
              <input
                type="text"
                required
                value={form.title}
                onChange={e => setForm({ ...form, title: e.target.value })}
                placeholder="Ex: Jornal F5 Segunda Edição, Grande Prêmio SP, etc."
                className="bg-zinc-900 border border-zinc-800 p-2.5 rounded text-white outline-none"
              />
            </div>

            <div className="flex flex-col gap-1 text-xs md:col-span-3">
              <label className="text-zinc-555 font-bold uppercase font-mono">Sinopse curta / Descrição</label>
              <textarea
                value={form.description}
                onChange={e => setForm({ ...form, description: e.target.value })}
                placeholder="Descreva brevemente o que acontecerá nesta transmissão..."
                rows={2}
                className="bg-zinc-900 border border-zinc-800 p-2.5 rounded text-white resize-none outline-none"
              />
            </div>

            <div className="flex flex-col gap-1 text-xs">
              <label className="text-zinc-555 font-bold uppercase font-mono">Apresentador / Âncora / Atração</label>
              <input
                type="text"
                value={form.host}
                onChange={e => setForm({ ...form, host: e.target.value })}
                placeholder="Ex: Sandro Albuquerque"
                className="bg-zinc-900 border border-zinc-800 p-2.5 rounded text-white outline-none"
              />
            </div>

            <div className="flex flex-col gap-1 text-xs">
              <label className="text-zinc-555 font-bold uppercase font-mono">Data programada</label>
              <input
                type="date"
                required
                value={form.date}
                onChange={e => setForm({ ...form, date: e.target.value })}
                className="bg-zinc-900 border border-zinc-800 p-2.5 rounded text-white outline-none"
              />
            </div>

            <div className="grid grid-cols-2 gap-2">
              <div className="flex flex-col gap-1 text-xs">
                <label className="text-zinc-555 font-bold uppercase font-mono">Hora Início</label>
                <input
                  type="text"
                  required
                  value={form.startTime}
                  onChange={e => setForm({ ...form, startTime: e.target.value })}
                  placeholder="08:00"
                  className="bg-zinc-900 border border-zinc-800 p-2.5 rounded text-white outline-none"
                />
              </div>
              <div className="flex flex-col gap-1 text-xs">
                <label className="text-zinc-555 font-bold uppercase font-mono">Hora Fim</label>
                <input
                  type="text"
                  required
                  value={form.endTime}
                  onChange={e => setForm({ ...form, endTime: e.target.value })}
                  placeholder="09:30"
                  className="bg-zinc-900 border border-zinc-800 p-2.5 rounded text-white outline-none"
                />
              </div>
            </div>

            <div className="flex flex-col gap-1 text-xs">
              <label className="text-zinc-555 font-bold uppercase font-mono">Status do Evento</label>
              <select
                value={form.status}
                onChange={e => setForm({ ...form, status: e.target.value as any })}
                className="bg-zinc-900 border border-zinc-800 p-2.5 rounded text-white"
              >
                <option value="scheduled">Agendado</option>
                <option value="live">AO VIVO AGORA</option>
                <option value="rerun">Reprise / Reride</option>
                <option value="premiere">Estreia Exclusiva</option>
                <option value="ended">Encerrado</option>
              </select>
            </div>

            <div className="flex flex-col gap-1 text-xs md:col-span-2">
              <label className="text-zinc-555 font-bold uppercase font-mono">Fundo de Imagem URL</label>
              <input
                type="text"
                value={form.imageUrl}
                onChange={e => setForm({ ...form, imageUrl: e.target.value })}
                className="bg-zinc-900 border border-zinc-800 p-2.5 rounded text-white outline-none"
              />
            </div>

            <div className="flex items-center gap-2.5 pt-5 text-xs">
              <input
                type="checkbox"
                id="isFeatured"
                checked={form.isFeatured}
                onChange={e => setForm({ ...form, isFeatured: e.target.checked })}
                className="w-4 h-4 accent-red-650"
              />
              <label htmlFor="isFeatured" className="text-zinc-300 font-bold uppercase font-mono cursor-pointer select-none">
                Destaque Principal de Capa Ao Vivo
              </label>
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
                {editingId ? 'Gravar Alterações' : 'Salvar Transmissão'}
              </button>
            </div>
          </form>
        </div>
      )}

      {/* Grid of existing schedules */}
      <div className="bg-zinc-950 border border-zinc-900 rounded-xl overflow-hidden shadow-2xl">
        <div className="p-4 border-b border-zinc-900 bg-zinc-950/50 flex justify-between items-center text-xs">
          <span className="text-zinc-400 font-bold uppercase font-mono">Grade agendada da emissora ({liveSchedules.length})</span>
          <span className="text-zinc-650 font-mono">UTC-3 Horário de Brasília</span>
        </div>

        {liveSchedules.length === 0 ? (
          <div className="p-16 text-center text-xs text-zinc-600 flex flex-col items-center justify-center gap-2">
            <AlertCircle className="w-8 h-8 text-zinc-850" />
            <p>Nenhuma transmissão programada no banco de dados.</p>
          </div>
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full text-left border-collapse text-xs">
              <thead>
                <tr className="border-b border-zinc-900 text-zinc-500 uppercase font-mono font-bold bg-[#0d0d0f]">
                  <th className="p-4"> Canal</th>
                  <th className="p-4">Programa</th>
                  <th className="p-4">Horário</th>
                  <th className="p-4">Apresentação</th>
                  <th className="p-4">Status</th>
                  <th className="p-4 text-right">Ações</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-zinc-950">
                {liveSchedules.map((ls) => (
                  <tr key={ls.id} className="hover:bg-zinc-900/30 transition-all font-semibold">
                    <td className="p-4 text-zinc-300 font-bold">
                      {getChannelName(ls.channelId)}
                    </td>
                    <td className="p-4">
                      <div className="flex flex-col gap-0.5">
                        <span className="text-zinc-100 font-extrabold max-w-sm truncate">{ls.title}</span>
                        {ls.description && <span className="text-zinc-550 text-[11px] leading-relaxed max-w-sm truncate">{ls.description}</span>}
                      </div>
                    </td>
                    <td className="p-4 font-mono text-zinc-400">
                      <div className="flex items-center gap-1">
                        <Clock className="w-3.5 h-3.5 text-zinc-550" />
                        <span>{ls.startTime} - {ls.endTime}</span>
                      </div>
                      <span className="text-[10px] text-zinc-600 block">{ls.date}</span>
                    </td>
                    <td className="p-4 text-zinc-400">
                      {ls.host || 'Sem apresentador'}
                    </td>
                    <td className="p-4">
                      <span className={`text-[9px] font-mono uppercase tracking-widest px-2 py-0.5 rounded-full ${getStatusColor(ls.status)}`}>
                        {ls.status === 'live' ? 'no ar' : ls.status === 'rerun' ? 'reprise' : ls.status === 'premiere' ? 'estreia' : ls.status === 'scheduled' ? 'agendado' : 'encerrado'}
                      </span>
                      {ls.isFeatured && (
                        <span className="block mt-1 text-[8px] font-mono text-rose-500 font-bold tracking-widest uppercase">★ Destaque</span>
                      )}
                    </td>
                    <td className="p-4 text-right">
                      <div className="flex items-center justify-end gap-1">
                        <button
                          onClick={() => handleEdit(ls)}
                          className="p-1.5 hover:bg-zinc-900 text-zinc-400 hover:text-white rounded"
                          title="Editar Grade"
                        >
                          <Edit2 className="w-3.5 h-3.5" />
                        </button>
                        <button
                          onClick={() => handleDelete(ls.id)}
                          className="p-1.5 hover:bg-zinc-900 text-zinc-500 hover:text-red-500 rounded"
                          title="Excluir Grade"
                        >
                          <Trash2 className="w-3.5 h-3.5" />
                        </button>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </div>
    </section>
  );
};

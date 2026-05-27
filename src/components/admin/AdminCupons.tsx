import React, { useState } from 'react';
import { useData } from '../../context/DataContext';
import { Coupon, Plan } from '../../types';
import { Percent, DollarSign, Plus, Trash2, Edit2, Key, Ticket, AlertCircle } from 'lucide-react';

export const AdminCupons: React.FC = () => {
  const { coupons, updateCoupons, plans } = useData();
  const [showForm, setShowForm] = useState(false);
  const [editingId, setEditingId] = useState<string | null>(null);

  const [form, setForm] = useState({
    code: '',
    discountType: 'percent' as 'percent' | 'fixed',
    discountValue: 10,
    expiresAt: '2026-12-31',
    usageLimit: 1000,
    usageCount: 0,
    applicablePlans: [] as string[],
    status: 'active' as 'active' | 'inactive'
  });

  const handleEdit = (cp: Coupon) => {
    setEditingId(cp.id);
    setForm({
      code: cp.code,
      discountType: cp.discountType,
      discountValue: cp.discountValue,
      expiresAt: cp.expiresAt,
      usageLimit: cp.usageLimit,
      usageCount: cp.usageCount,
      applicablePlans: cp.applicablePlans,
      status: cp.status
    });
    setShowForm(true);
  };

  const handleCancel = () => {
    setShowForm(false);
    setEditingId(null);
    setForm({
      code: '',
      discountType: 'percent',
      discountValue: 10,
      expiresAt: '2026-12-31',
      usageLimit: 1000,
      usageCount: 0,
      applicablePlans: [],
      status: 'active'
    });
  };

  const handlePlanToggle = (planId: string) => {
    if (form.applicablePlans.includes(planId)) {
      setForm({ ...form, applicablePlans: form.applicablePlans.filter(id => id !== planId) });
    } else {
      setForm({ ...form, applicablePlans: [...form.applicablePlans, planId] });
    }
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!form.code.trim() || form.discountValue <= 0) return;

    const formattedCode = form.code.toUpperCase().replace(/\s+/g, '');

    if (editingId) {
      const updated = coupons.map(c => 
        c.id === editingId ? { ...c, ...form, code: formattedCode } : c
      );
      updateCoupons(updated);
    } else {
      const newCoupon: Coupon = {
        id: `coupon-${Date.now()}`,
        ...form,
        code: formattedCode
      };
      updateCoupons([newCoupon, ...coupons]);
    }
    handleCancel();
  };

  const handleDelete = (id: string) => {
    if (confirm('Deseja realmente apagar este cupom promocional do sistema?')) {
      const filtered = coupons.filter(c => c.id !== id);
      updateCoupons(filtered);
    }
  };

  const toggleStatus = (id: string) => {
    const updated = coupons.map(c => {
      if (c.id === id) {
        const newStatus: Coupon['status'] = c.status === 'active' ? 'inactive' : 'active';
        return { ...c, status: newStatus };
      }
      return c;
    });
    updateCoupons(updated);
  };

  return (
    <section className="flex flex-col gap-6 animate-fade-in text-white text-left font-sans select-none">
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between justify-start gap-4 border-b border-zinc-900 pb-5">
        <div>
          <span className="text-xs font-mono font-bold text-zinc-500 uppercase">CAMPANHAS E OFERTAS</span>
          <h1 className="text-2xl font-black tracking-tight mt-1 font-sans">Cupons Promocionais</h1>
        </div>
        {!showForm && (
          <button
            onClick={() => {
              // Pre-select all plans by default
              setForm(prev => ({ ...prev, applicablePlans: plans.map(p => p.id) }));
              setShowForm(true);
            }}
            className="flex items-center gap-2 bg-[#ef4444] hover:bg-red-700 text-white text-xs font-bold font-mono uppercase tracking-wider px-4 py-2.5 rounded-xl transition cursor-pointer"
          >
            <Plus className="w-4 h-4" />
            <span>Gerar Novo Cupom</span>
          </button>
        )}
      </div>

      {showForm && (
        <div className="bg-zinc-950 border border-zinc-900 p-6 rounded-xl animate-fade-in">
          <h3 className="text-sm font-bold uppercase font-mono text-zinc-400 mb-4 pb-2 border-b border-zinc-900">
            {editingId ? `Editar Cupom: ${form.code}` : 'Criar Nova Chave de Cupom'}
          </h3>
          <form onSubmit={handleSubmit} className="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div className="flex flex-col gap-1 text-xs">
              <label className="text-zinc-550 font-bold uppercase font-mono">Código do Cupom (Código Único)</label>
              <input
                type="text"
                required
                value={form.code}
                onChange={e => setForm({ ...form, code: e.target.value })}
                placeholder="Ex: SELECAO20, BLACKFRIDAY"
                className="bg-zinc-900 border border-zinc-800 p-2.5 rounded text-white outline-none font-mono uppercase"
              />
            </div>

            <div className="flex flex-col gap-1 text-xs">
              <label className="text-zinc-550 font-bold uppercase font-mono">Tipo de Desconto</label>
              <select
                value={form.discountType}
                onChange={e => setForm({ ...form, discountType: e.target.value as any })}
                className="bg-zinc-900 border border-zinc-800 p-2.5 rounded text-white outline-none"
              >
                <option value="percent">Porcentagem (%)</option>
                <option value="fixed">Valor Fixo (R$)</option>
              </select>
            </div>

            <div className="flex flex-col gap-1 text-xs">
              <label className="text-zinc-550 font-bold uppercase font-mono">Valor do Abatimento</label>
              <input
                type="number"
                required
                min={1}
                value={form.discountValue}
                onChange={e => setForm({ ...form, discountValue: Number(e.target.value) })}
                className="bg-zinc-900 border border-zinc-800 p-2.5 rounded text-white outline-none font-semibold"
              />
            </div>

            <div className="flex flex-col gap-1 text-xs">
              <label className="text-zinc-550 font-bold uppercase font-mono">Limite de Utilizações (MÁX)</label>
              <input
                type="number"
                required
                min={1}
                value={form.usageLimit}
                onChange={e => setForm({ ...form, usageLimit: Number(e.target.value) })}
                className="bg-zinc-900 border border-zinc-800 p-2.5 rounded text-white outline-none"
              />
            </div>

            <div className="flex flex-col gap-1 text-xs">
              <label className="text-zinc-550 font-bold uppercase font-mono">Data de Expiração</label>
              <input
                type="date"
                required
                value={form.expiresAt}
                onChange={e => setForm({ ...form, expiresAt: e.target.value })}
                className="bg-zinc-900 border border-zinc-800 p-2.5 rounded text-white outline-none"
              />
            </div>

            <div className="flex flex-col gap-1 text-xs">
              <label className="text-zinc-550 font-bold uppercase font-mono">Status Inicial</label>
              <select
                value={form.status}
                onChange={e => setForm({ ...form, status: e.target.value as any })}
                className="bg-zinc-900 border border-zinc-800 p-2.5 rounded text-white outline-none"
              >
                <option value="active">Roda Ativo (Permitido)</option>
                <option value="inactive">Suspenso / Pausado</option>
              </select>
            </div>

            <div className="md:col-span-3 flex flex-col gap-2.5 border-t border-zinc-900 pt-3">
              <label className="text-zinc-550 font-bold uppercase font-mono text-xs">Planos Aplicáveis</label>
              <div className="flex flex-wrap gap-4 text-xs font-semibold">
                {plans.map(p => (
                  <label key={p.id} className="flex items-center gap-2 cursor-pointer select-none">
                    <input
                      type="checkbox"
                      checked={form.applicablePlans.includes(p.id)}
                      onChange={() => handlePlanToggle(p.id)}
                      className="w-4 h-4 rounded text-red-650 focus:ring-0 cursor-pointer accent-red-655"
                    />
                    <span className="text-zinc-300 hover:text-white transition">{p.name} - R$ {p.price.toFixed(2)}</span>
                  </label>
                ))}
              </div>
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
                {editingId ? 'Salvar Edição' : 'Gerar Chave de Cupom'}
              </button>
            </div>
          </form>
        </div>
      )}

      {/* Coupon Lists */}
      <div className="bg-zinc-950 border border-zinc-900 rounded-xl overflow-hidden shadow-2xl">
        <div className="p-4 border-b border-zinc-900 font-mono font-bold uppercase text-zinc-400 text-xs">
          Registro de Cupons ({coupons.length})
        </div>

        {coupons.length === 0 ? (
          <div className="p-16 text-center text-zinc-650 flex flex-col items-center justify-center gap-2 text-xs">
            <AlertCircle className="w-8 h-8 text-zinc-850" />
            <p>Nenhum cupom ativo ou registrado.</p>
          </div>
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full text-left border-collapse text-xs">
              <thead>
                <tr className="border-b border-zinc-900 text-zinc-550 font-mono font-bold uppercase bg-[#0c0c0e]">
                  <th className="p-4">Código</th>
                  <th className="p-4">Desconto</th>
                  <th className="p-4">Utilização (Progresso)</th>
                  <th className="p-4">Válido até</th>
                  <th className="p-4">Status</th>
                  <th className="p-4 text-right">Ações</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-zinc-950 font-semibold text-zinc-300">
                {coupons.map(c => {
                  const percentOfUsage = Math.min(100, Math.round((c.usageCount / c.usageLimit) * 100));
                  return (
                    <tr key={c.id} className="hover:bg-zinc-900/30 transition">
                      <td className="p-4">
                        <div className="flex items-center gap-2">
                          <Ticket className="w-4 h-4 text-red-500" />
                          <span className="font-mono font-black text-white text-sm bg-zinc-900 border border-zinc-850 px-2.5 py-1 rounded-lg">
                            {c.code}
                          </span>
                        </div>
                      </td>
                      <td className="p-4 text-xs font-extrabold text-zinc-100">
                        {c.discountType === 'percent' ? (
                          <div className="flex items-center text-green-500">
                            <Percent className="w-3.5 h-3.5" />
                            <span>{c.discountValue}% OFF</span>
                          </div>
                        ) : (
                          <div className="flex items-center text-green-500">
                            <span className="font-sans font-bold text-xs">R$ {c.discountValue.toFixed(2)} OFF</span>
                          </div>
                        )}
                      </td>
                      <td className="p-4">
                        <div className="flex flex-col gap-1 max-w-[150px]">
                          <div className="flex justify-between font-mono text-[9px] text-zinc-550">
                            <span>{c.usageCount} / {c.usageLimit} usos</span>
                            <span>{percentOfUsage}%</span>
                          </div>
                          <div className="w-full bg-zinc-900 h-1.5 rounded-full overflow-hidden border border-white/5">
                            <div className="bg-red-650 h-full rounded-full" style={{ width: `${percentOfUsage}%` }} />
                          </div>
                        </div>
                      </td>
                      <td className="p-4 font-mono text-zinc-450">
                        {c.expiresAt}
                      </td>
                      <td className="p-4">
                        <span className={`text-[9px] font-mono uppercase tracking-widest font-extrabold px-1.5 py-0.5 rounded ${
                          c.status === 'active' ? 'bg-green-950 text-green-400 border border-green-900' :
                          'bg-zinc-900 text-zinc-600'
                        }`}>
                          {c.status === 'active' ? 'Ativo' : 'Inativo'}
                        </span>
                      </td>
                      <td className="p-4 text-right">
                        <div className="flex items-center justify-end gap-1">
                          <button
                            onClick={() => toggleStatus(c.id)}
                            className="p-1.5 hover:bg-zinc-900 rounded text-zinc-450 hover:text-white font-mono text-[10px]"
                            title={c.status === 'active' ? 'Pausar Cupom' : 'Ativar Cupom'}
                          >
                            {c.status === 'active' ? 'Suspender' : 'Ativar'}
                          </button>
                          <button
                            onClick={() => handleEdit(c)}
                            className="p-1.5 hover:bg-zinc-900 text-zinc-450 hover:text-white rounded"
                            title="Editar Coupon"
                          >
                            <Edit2 className="w-3.5 h-3.5" />
                          </button>
                          <button
                            onClick={() => handleDelete(c.id)}
                            className="p-1.5 hover:bg-zinc-900 text-zinc-550 hover:text-red-500 rounded"
                            title="Deletar Coupon"
                          >
                            <Trash2 className="w-3.5 h-3.5" />
                          </button>
                        </div>
                      </td>
                    </tr>
                  )
                })}
              </tbody>
            </table>
          </div>
        )}
      </div>
    </section>
  );
};
export default AdminCupons;

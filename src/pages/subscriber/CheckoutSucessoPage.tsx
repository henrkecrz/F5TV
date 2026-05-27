import React from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import { CheckCircle, ShieldCheck, Sparkles, Tv, ArrowRight } from 'lucide-react';

export const CheckoutSucessoPage: React.FC = () => {
  const { state } = useLocation();
  const navigate = useNavigate();
  
  const planName = state?.planName || 'Plano Premium Especial';
  const valuePaid = state?.valuePaid !== undefined ? state.valuePaid : 29.90;
  const orderId = `REC-${Math.floor(100000 + Math.random() * 900000)}`;

  const handleStartWatching = () => {
    // Navigate home, which re-evaluates authentications and active subscriptions profiles
    navigate('/app');
  };

  return (
    <div className="min-h-screen bg-[#09090b] text-white flex items-center justify-center p-6 text-left selection:bg-red-650 font-sans select-none animate-fade-in text-xs">
      <div className="max-w-md w-full bg-zinc-950 border border-zinc-900 rounded-3xl p-8 shadow-2xl flex flex-col gap-6 text-center">
        
        {/* Animated Check Success Sphere */}
        <div className="w-16 h-16 bg-green-950/40 border border-green-900 rounded-full flex items-center justify-center self-center shadow">
          <CheckCircle className="w-8 h-8 text-green-500" />
        </div>

        <div className="flex flex-col gap-1">
          <h1 className="text-2xl font-black tracking-tight text-white leading-tight">Assinatura Ativada!</h1>
          <p className="text-zinc-[450] text-xs font-semibold">Parabéns! Sua forma de assistir televisão mudou para premium.</p>
        </div>

        {/* Invoice specifications */}
        <div className="bg-zinc-900/40 border border-zinc-900 p-5 rounded-2xl flex flex-col gap-3 text-left">
          <span className="text-[10px] font-mono tracking-widest text-zinc-500 font-bold uppercase block text-center pb-2 border-b border-zinc-900">
            Recibo Eletrônico de Transação
          </span>
          <div className="flex justify-between items-center">
            <span className="text-zinc-500 font-bold font-mono">ID do Pedido:</span>
            <span className="font-mono text-zinc-300 font-extrabold">{orderId}</span>
          </div>
          <div className="flex justify-between items-center">
            <span className="text-zinc-500 font-bold font-mono">Plano Contratado:</span>
            <span className="text-zinc-200 font-black">{planName}</span>
          </div>
          <div className="flex justify-between items-center">
            <span className="text-zinc-500 font-bold font-mono">Valor Faturado:</span>
            <span className="font-mono text-zinc-150 font-extrabold text-sm">R$ {valuePaid.toFixed(2)}</span>
          </div>
          <div className="flex justify-between items-center">
            <span className="text-zinc-500 font-bold font-mono">Situação Pagamento:</span>
            <span className="text-[9px] font-mono font-bold uppercase tracking-widest bg-green-950 text-green-400 border border-green-900 px-2 py-0.5 rounded-full">
              Confirmado
            </span>
          </div>
        </div>

        <div className="flex flex-col gap-2.5">
          <button
            onClick={handleStartWatching}
            className="w-full bg-[#ef4444] hover:bg-red-700 text-white font-mono font-extrabold text-xs uppercase tracking-widest py-3 rounded-xl transition flex items-center justify-center gap-1.5 cursor-pointer shadow"
          >
            <span>Acessar Programas</span>
            <ArrowRight className="w-4 h-4" />
          </button>
          
          <span className="text-[9.5px] text-zinc-650 font-mono flex items-center gap-1.5 justify-center mt-1">
            <Sparkles className="w-3.5 h-3.5 text-rose-500 shrink-0" /> Sinal Premium Liberado em Smart TVs, Consoles e Apps móveis
          </span>
        </div>

      </div>
    </div>
  );
};
export default CheckoutSucessoPage;

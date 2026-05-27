import React from 'react';
import { useNavigate } from 'react-router-dom';
import { useData } from '../../context/DataContext';
import { Check, Star, ShieldCheck, ArrowLeft } from 'lucide-react';

export const PlansPage: React.FC = () => {
  const { plans } = useData();
  const navigate = useNavigate();

  const activePlans = plans.filter((p) => p.active);

  const handleSelectPlan = (planId: string) => {
    navigate('/cadastro', { state: { planId } });
  };

  return (
    <div id="plans-page-root" className="min-h-screen bg-[#050505] text-white flex flex-col justify-between font-sans relative">
      <header className="sticky top-0 z-40 bg-[#050505]/85 backdrop-blur-md border-b border-white/5 px-8 py-5">
        <div className="max-w-7xl mx-auto flex items-center justify-between">
          <div className="flex items-center gap-4 cursor-pointer" onClick={() => navigate('/landing')}>
            <ArrowLeft className="w-4 h-4 text-zinc-400 hover:text-white" />
            <span className="text-xl font-black tracking-tighter uppercase text-white">
              F5 <span className="text-red-650">TV</span>
            </span>
          </div>
          <button
            onClick={() => navigate('/login')}
            className="text-xs font-mono font-bold uppercase tracking-widest text-[#ef4444] hover:text-red-500 transition cursor-pointer"
          >
            Entrar na conta
          </button>
        </div>
      </header>

      <main className="flex-1 max-w-7xl w-full mx-auto px-6 py-16 flex flex-col items-center gap-12">
        <div className="text-center max-w-2xl flex flex-col gap-3">
          <span className="text-[#ef4444] font-mono font-black text-xs uppercase tracking-widest">Escolha a sua experiência</span>
          <h1 className="text-3xl md:text-5xl font-black tracking-tight leading-none text-zinc-100">
            Assinaturas Simples e Transparentes
          </h1>
          <p className="text-zinc-500 text-sm md:text-base font-medium">
            Selecione o plano ideal para você e sua família. Sem fidelidade, sem taxas ocultas, cancele a qualquer segundo online.
          </p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-8 w-full max-w-5xl mt-4">
          {activePlans.map((plan) => {
            const isPremium = plan.id.includes('premium') || plan.name.toLowerCase().includes('premium');
            const isFamily = plan.id.includes('familia') || plan.name.toLowerCase().includes('fam');
            
            return (
              <div
                key={plan.id}
                id={`plan-card-${plan.id}`}
                className={`flex flex-col rounded-2xl border bg-zinc-950/80 p-8 shadow-2xl relative transition hover:scale-102 duration-300 ${
                  isPremium
                    ? 'border-[#ef4444]/60 bg-gradient-to-b from-red-950/10 to-transparent'
                    : 'border-zinc-900'
                }`}
              >
                {isPremium && (
                  <span className="absolute -top-3 left-1/2 -translate-x-1/2 bg-[#ef4444] text-white text-[9px] font-mono font-black uppercase tracking-widest px-3.5 py-1 rounded-full shadow shadow-red-900/45 flex items-center gap-1">
                    <Star className="w-3 h-3 fill-current" />
                    <span>Mais Recomendado</span>
                  </span>
                )}

                <div className="flex flex-col gap-2 pb-6 border-b border-zinc-900">
                  <span className="text-xs font-mono text-zinc-500 uppercase font-black tracking-widest">{plan.name}</span>
                  <div className="flex items-baseline gap-1 mt-1">
                    <span className="text-4xl font-extrabold text-white">R$ {plan.price.toFixed(2)}</span>
                    <span className="text-xs text-zinc-500 font-medium">/mês</span>
                  </div>
                </div>

                <div className="flex-1 py-8 flex flex-col gap-4">
                  <span className="text-[10px] font-mono font-bold text-zinc-500 uppercase tracking-wider">Recursos Inclusos:</span>
                  <ul className="flex flex-col gap-3.5">
                    {plan.features.map((feature, i) => (
                      <li key={i} className="flex items-start gap-2.5 text-xs text-zinc-350">
                        <Check className="w-4 h-4 text-[#ef4444] shrink-0 mt-0.5" />
                        <span className="font-semibold leading-relaxed">{feature}</span>
                      </li>
                    ))}
                  </ul>
                </div>

                <button
                  onClick={() => handleSelectPlan(plan.id)}
                  className={`w-full py-3 px-4 rounded-xl font-bold font-mono text-xs uppercase tracking-wider cursor-pointer transition duration-300 ${
                    isPremium
                      ? 'bg-[#ef4444] hover:bg-red-700 text-white shadow-lg shadow-red-900/10'
                      : 'bg-zinc-900 hover:bg-zinc-800 text-zinc-100'
                  }`}
                >
                  Assinar Agora
                </button>
              </div>
            );
          })}
        </div>

        {/* COMPARATIVE TABLE */}
        <div className="w-full max-w-4xl mt-12 bg-zinc-950 border border-zinc-900 rounded-2xl overflow-hidden shadow-2xl p-6 md:p-8">
          <h2 className="text-md font-mono font-black uppercase tracking-widest text-[#ef4444] mb-6 flex items-center gap-2">
            <ShieldCheck className="w-5 h-5 text-[#ef4444]" />
            <span>Comparativo Detalhado de Benefícios</span>
          </h2>
          <div className="overflow-x-auto">
            <table className="w-full text-left border-collapse text-xs font-semibold">
              <thead>
                <tr className="border-b border-zinc-900 text-zinc-500 font-mono text-[10px] uppercase">
                  <th className="py-4 pr-4">Recurso do Serviço</th>
                  <th className="py-4 px-4 text-center">Básico</th>
                  <th className="py-4 px-4 text-center">Família</th>
                  <th className="py-4 px-4 text-center">Premium 4K</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-zinc-950 text-zinc-300">
                <tr className="hover:bg-zinc-900/20">
                  <td className="py-4 pr-4 text-zinc-400">Qualidade Máxima de Vídeo</td>
                  <td className="py-4 px-4 text-center">HD (720p)</td>
                  <td className="py-4 px-4 text-center">Full HD (1080p)</td>
                  <td className="py-4 px-4 text-center text-[#ef4444] font-bold">UHD @ 4K Ultra</td>
                </tr>
                <tr className="hover:bg-zinc-900/20">
                  <td className="py-4 pr-4 text-zinc-400">Telas Simultâneas Ativas</td>
                  <td className="py-4 px-4 text-center">1 Tela</td>
                  <td className="py-4 px-4 text-center">3 Telas</td>
                  <td className="py-4 px-4 text-center text-white font-bold">5 Telas</td>
                </tr>
                <tr className="hover:bg-zinc-900/20">
                  <td className="py-4 pr-4 text-zinc-400">Seção Exclusiva de Banners</td>
                  <td className="py-4 px-4 text-center">Sim</td>
                  <td className="py-4 px-4 text-center">Sim</td>
                  <td className="py-4 px-4 text-center text-white">Sim</td>
                </tr>
                <tr className="hover:bg-zinc-900/20">
                  <td className="py-4 pr-4 text-zinc-400">Downloads offline no App</td>
                  <td className="py-4 px-4 text-center text-zinc-650 font-mono font-medium">Não</td>
                  <td className="py-4 px-4 text-center text-white">Sim</td>
                  <td className="py-4 px-4 text-center text-white">Sim</td>
                </tr>
                <tr className="hover:bg-zinc-900/20">
                  <td className="py-4 pr-4 text-zinc-400">Áudio Imersivo Dolby Atmos</td>
                  <td className="py-4 px-4 text-center text-zinc-650 font-mono font-medium">Não</td>
                  <td className="py-4 px-4 text-center text-zinc-650 font-mono font-medium">Não</td>
                  <td className="py-4 px-4 text-center text-[#ef4444] font-bold">Sim</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </main>

      <footer className="border-t border-white/5 py-8 text-center text-zinc-600 font-semibold text-xs font-mono">
        <p>© 2026 F5 TV | Todos os direitos reservados. Projeto MVP.</p>
      </footer>
    </div>
  );
};

export default PlansPage;

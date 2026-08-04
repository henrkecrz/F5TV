import React from 'react';
import { useNavigate } from 'react-router-dom';
import Footer from '../../components/Footer';
import { Award, Eye, Shield, Tv, Sparkles, MessageCircle, ArrowLeft } from 'lucide-react';

export const SobrePage: React.FC = () => {
  const navigate = useNavigate();

  const handleSelectCredential = (email: string) => {
    navigate('/login', { state: { email } });
  };

  return (
    <div className="min-h-screen bg-[#050505] text-white font-sans flex flex-col justify-between selection:bg-red-655">
      
      {/* Header */}
      <header className="sticky top-0 z-40 bg-[#050505]/85 backdrop-blur-md border-b border-white/5 px-8 py-5">
        <div className="max-w-7xl mx-auto flex items-center justify-between">
          <div className="flex items-center gap-6 cursor-pointer" onClick={() => navigate('/landing')}>
            <span className="text-2xl font-black tracking-tighter uppercase text-white hover:scale-102 transition duration-200">
              F5 <span className="text-red-600">TV</span>
            </span>
            <span className="text-[10px] bg-red-600/10 text-red-600 px-2 py-0.5 rounded font-mono font-bold tracking-widest uppercase shrink-0">
              PREMIUM
            </span>
          </div>

          <div className="flex items-center gap-4">
            <button
              onClick={() => navigate('/landing')}
              className="text-xs font-bold text-zinc-400 hover:text-white transition uppercase tracking-wider flex items-center gap-1.5 cursor-pointer"
            >
              <ArrowLeft className="w-4 h-4" />
              <span>Voltar ao Início</span>
            </button>
          </div>
        </div>
      </header>

      {/* Main Content */}
      <main className="flex-grow max-w-4xl mx-auto px-8 py-16 text-left">
        <div className="flex flex-col gap-3 mb-10">
          <span className="text-[10px] font-mono font-bold tracking-[0.2em] text-red-500 uppercase">F5 TV STREAMING</span>
          <h1 className="text-4xl sm:text-6xl font-black tracking-tighter leading-none mb-2">A 1ª TV Streaming de Portugal</h1>
          <p className="text-zinc-400 text-sm leading-relaxed max-w-2xl font-semibold">
            Uma nova forma de fazer televisão.
          </p>
        </div>

        {/* Dynamic decorative image banner */}
        <div className="relative aspect-video rounded-3xl overflow-hidden border border-white/5 mb-16 shadow-2xl">
          <div className="absolute inset-0 bg-gradient-to-t from-[#050505] via-transparent to-transparent z-10" />
          <img 
            src="https://images.unsplash.com/photo-1461896836934-ffe607ba8211?q=80&w=2070&auto=format&fit=crop" 
            alt="F5 TV Broadcast station" 
            className="w-full h-full object-cover opacity-70 grayscale-[0.2]"
            referrerPolicy="no-referrer"
          />
          <div className="absolute bottom-6 left-6 z-20 flex gap-2">
            <span className="text-[10px] bg-red-650 text-white font-mono px-2.5 py-1 rounded font-bold uppercase tracking-wider flex items-center gap-1">
              <span className="w-1.5 h-1.5 bg-white rounded-full animate-ping" />
              Sinal Ao Vivo 24/7
            </span>
          </div>
        </div>

        {/* Grid features */}
        <div className="grid grid-cols-1 sm:grid-cols-3 gap-8 mb-16">
          <div className="flex flex-col gap-3 p-6 bg-[#0a0a0a] border border-white/5 rounded-2xl">
            <Eye className="w-8 h-8 text-red-500" />
            <h3 className="text-lg font-black tracking-tight mt-1 text-white">Televisão sem fronteiras</h3>
            <p className="text-zinc-500 text-xs font-semibold leading-relaxed">
              Informação, entretenimento, cultura, entrevistas, opinião, negócios e lifestyle num só lugar.
            </p>
          </div>

          <div className="flex flex-col gap-3 p-6 bg-[#0a0a0a] border border-white/5 rounded-2xl">
            <Shield className="w-8 h-8 text-red-500" />
            <h3 className="text-lg font-black tracking-tight mt-1 text-white">Uma visão contemporânea</h3>
            <p className="text-zinc-500 text-xs font-semibold leading-relaxed">
              Conteúdos próprios e parceiros, formatos diferenciados e uma experiência adaptada aos novos ecrãs.
            </p>
          </div>

          <div className="flex flex-col gap-3 p-6 bg-[#0a0a0a] border border-white/5 rounded-2xl">
            <Award className="w-8 h-8 text-red-500" />
            <h3 className="text-lg font-black tracking-tight mt-1 text-white">Portugal e o mundo</h3>
            <p className="text-zinc-500 text-xs font-semibold leading-relaxed">
              Histórias, ideias e protagonistas que merecem ser vistos e ouvidos.
            </p>
          </div>
        </div>

        {/* Narrative editorial statement */}
        <section className="border-t border-white/5 pt-12 flex flex-col gap-6 font-semibold text-zinc-350 text-sm leading-relaxed max-w-3xl">
          <h2 className="text-2xl font-black text-white tracking-tight">Uma nova forma de fazer televisão</h2>
          <p>
            A F5 TV Streaming nasce para marcar uma nova etapa na televisão em Portugal. Como 1ª TV Streaming de Portugal, assumimos uma posição pioneira num mercado em transformação, onde a televisão deixou de estar limitada a horários, grelhas e formatos convencionais. Hoje, o público escolhe o que quer ver, quando quer ver e através de diferentes ecrãs. É nesse novo território que a F5 se posiciona.
          </p>
          <p>
            Somos uma plataforma de televisão concebida para reunir informação, entretenimento, cultura, entrevistas, opinião, negócios, lifestyle e conteúdos especiais, aproximando diferentes públicos de histórias, ideias e protagonistas que merecem ser vistos e ouvidos.
          </p>
          <p>
            Na F5TV acreditamos que a televisão do futuro não será apenas aquela que transmite conteúdos. Será aquela que cria relevância, estabelece ligações e acompanha a transformação da sociedade. Por isso, construímos uma experiência de televisão mais flexível, contemporânea e conectada com o seu tempo, com conteúdos próprios e parceiros, formatos diferenciados e uma visão aberta ao que acontece em Portugal e no mundo.
          </p>
          <p>
            A F5TV é televisão sem fronteiras de horário, de espaço ou de formato. É televisão para uma nova geração de espectadores. É conteúdo que encontra o seu público. É comunicação que permanece para além do ecrã.
          </p>
          <p className="text-lg font-black text-white">F5 TV Streaming. O futuro da televisão começa aqui.</p>
          <div className="bg-zinc-950 p-5 rounded-2xl border border-white/5 flex items-center gap-4 mt-2">
            <Tv className="w-10 h-10 text-red-500 shrink-0" />
            <div className="text-xs">
              <span className="font-bold block text-white">Transmissão Certificada Pro-HD</span>
              <span className="text-zinc-650 font-semibold block mt-1 font-mono">Conformidade técnica sob protocolos de criptografia e compressão AV1/VP9.</span>
            </div>
          </div>
        </section>

      </main>

      {/* Footer */}
      <Footer onSelectCredential={handleSelectCredential} />

    </div>
  );
};

export default SobrePage;

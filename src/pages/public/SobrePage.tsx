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
          <span className="text-[10px] font-mono font-bold tracking-[0.2em] text-red-500 uppercase">QUEM SOMOS NOS</span>
          <h1 className="text-4xl sm:text-6xl font-black tracking-tighter leading-none mb-2">Sobre a F5 TV Brasil</h1>
          <p className="text-zinc-400 text-sm leading-relaxed max-w-2xl font-semibold">
            Uma emissora com DNA tático e digital. Nascemos na interseção entre o jornalismo tático livre, os esportes de alta adrenalina e o entretenimento familiar seguro.
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
            <h3 className="text-lg font-black tracking-tight mt-1 text-white">Transparência Tática</h3>
            <p className="text-zinc-500 text-xs font-semibold leading-relaxed">
              Jornalismo que expõe fatos sem agendas de corporações, reportando as notícias de forma objetiva sobre economia, infraestrutura e segurança.
            </p>
          </div>

          <div className="flex flex-col gap-3 p-6 bg-[#0a0a0a] border border-white/5 rounded-2xl">
            <Shield className="w-8 h-8 text-red-500" />
            <h3 className="text-lg font-black tracking-tight mt-1 text-white">Ambiente Protegido</h3>
            <p className="text-zinc-500 text-xs font-semibold leading-relaxed">
              Dedicamos canais infantis e infanto-juvenis totalmente livres de doutrinação ideológica e com curadoria pedagógica severa para segurança dos filhos.
            </p>
          </div>

          <div className="flex flex-col gap-3 p-6 bg-[#0a0a0a] border border-white/5 rounded-2xl">
            <Award className="w-8 h-8 text-red-500" />
            <h3 className="text-lg font-black tracking-tight mt-1 text-white">Esportes e Lazer</h3>
            <p className="text-zinc-500 text-xs font-semibold leading-relaxed">
              Transmissões completas de automobilismo de teste, lutas de alta performance e campeonatos regionais esquecidos pelas grandes redes.
            </p>
          </div>
        </div>

        {/* Narrative editorial statement */}
        <section className="border-t border-white/5 pt-12 flex flex-col gap-6 font-semibold text-zinc-350 text-sm leading-relaxed max-w-3xl">
          <h2 className="text-2xl font-black text-white tracking-tight">Nossa Jornada & Concessionária</h2>
          <p>
            Fundada pelo grupo F5 de Comunicação S.A., a F5 TV atua como emissora concessionária de radiodifusão de sons e imagens com outorga e cobertura em território brasileiro. Em 2024, expandimos nossas operações com o lançamento da plataforma de streaming F5 Premium, garantindo acesso em tempo real e sob demanda para mais de 10 milhões de lares via Smart TV, consoles de vídeo game, tablets e smartphones.
          </p>
          <p>
            Acreditamos que todo cidadão merece veracidade em tempo real. Por isso, a F5 TV investe integralmente em tecnologia de tráfego ultra-low-latency e estúdios modernos baseados em inteligência computacional tática, garantindo que nossas câmeras estejam posicionadas nos corações dos acontecimentos mais impactantes do país.
          </p>
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

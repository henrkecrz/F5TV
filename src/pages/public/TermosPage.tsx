import React from 'react';
import { useNavigate } from 'react-router-dom';
import Footer from '../../components/Footer';
import { FileText, Shield, Key, AlertTriangle, Scale, ArrowLeft } from 'lucide-react';

export const TermosPage: React.FC = () => {
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

      {/* Main Container */}
      <main className="flex-grow max-w-4xl mx-auto px-8 py-16 text-left">
        <div className="flex flex-col gap-3 mb-10">
          <span className="text-[10px] font-mono font-bold tracking-[0.2em] text-red-500 uppercase">DIRETRIZES LEGAIS</span>
          <h1 className="text-4xl sm:text-6xl font-black tracking-tighter leading-none mb-2">Termos de Serviço</h1>
          <p className="text-zinc-550 text-xs font-mono uppercase font-bold">Última Atualização: 27 de Maio de 2026</p>
        </div>

        {/* Highlight notification */}
        <div className="bg-zinc-950 p-5 rounded-2xl border border-white/5 flex items-start gap-4 mb-10 text-xs">
          <Scale className="w-6 h-6 text-red-500 shrink-0 mt-0.5" />
          <div className="text-zinc-[450] font-semibold leading-relaxed">
            <span className="font-bold text-white block mb-0.5">Visão Geral dos Termos</span>
            Ao acessar e se cadastrar na plataforma F5 TV Premium, você concorda em cumprir estes termos regulamentares de licenciamento, direitos autorais e políticas de faturamento recorrentes. Leia com atenção antes de realizar qualquer checkout de planos.
          </div>
        </div>

        {/* Terms articles */}
        <article className="flex flex-col gap-8 text-sm text-zinc-350 leading-relaxed font-semibold max-w-3xl">
          
          <section className="flex flex-col gap-2">
            <h2 className="text-xl font-black text-white tracking-tight">1. Objeto do Serviço</h2>
            <p>
              A platforma F5 TV Premium consiste em um canal unificado de transmissão de televisão simultânea (simulcasting) e biblioteca de vídeos sob demanda (VOD) gerenciado e de propriedade da emissora F5 TV Brasil. O licenciamento de conta é pessoal, intransferível e estritamente para uso não comercial.
            </p>
          </section>

          <section className="flex flex-col gap-2">
            <h2 className="text-xl font-black text-white tracking-tight">2. Política de Reembolso e Fidelidade</h2>
            <p>
              Em total conformidade com o Artigo 49 do Código de Defesa do Consumidor (CDC) brasileiro, garantimos o direito de arrependimento incondicional com devolução integral de valores em até 7 (sete) dias corridos após a contratação de qualquer plano de assinatura anual ou mensal. Passado o prazo legal de 7 dias ou em caso de uso ostensivo de stream de vídeo o cancelamento restringe-se a não renovação do ciclo atual.
            </p>
          </section>

          <section className="flex flex-col gap-2">
            <h2 className="text-xl font-black text-white tracking-tight">3. Conexões & Limite de Dispositivos</h2>
            <p>
              Cada plano de assinatura estipula um limite tático de telas operacionais simultâneas para reprodução de vídeo. A F5 TV reserva-se o direito de suspender temporariamente ou emitir alertas de enlace de logins excessivos para contas com logins simultâneos abusivos que excedam o contratual do perfil cadastrado.
            </p>
          </section>

          <section className="flex flex-col gap-2">
            <h2 className="text-xl font-black text-white tracking-tight">4. Propriedade Intelectual & Sinais Protegidos</h2>
            <p>
              Todas as transmissões, marcas exibidas nos canais, gráficos dos streamings, conteúdos infantis, novelas ou coberturas de jornalismo são de titularidade ou licenciamento oficial da F5 TV Brasil. É terminantemente proibido retransmitir, gravar, fazer captura de imagens para redistribuição comercial ou comercializar pacotes alternativos (IPTV clandestino) sob pena de responsabilização civil e penal.
            </p>
          </section>

          <section className="flex flex-col gap-2">
            <h2 className="text-xl font-black text-white tracking-tight">5. Legislação Aplicável e Foro</h2>
            <p>
              Estes Termos de Serviço são regidos e interpretados segundo as Leis da República Federativa do Brasil. Fica eleito o foro da Comarca de São Paulo/SP para dirimir quaisquer controvérsias judiciais decorrentes deste pacto legal.
            </p>
          </section>

        </article>
      </main>

      {/* Footer */}
      <Footer onSelectCredential={handleSelectCredential} />

    </div>
  );
};

export default TermosPage;

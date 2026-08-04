import React from 'react';
import { useNavigate } from 'react-router-dom';
import Footer from '../../components/Footer';
import { Shield, EyeOff, Lock, Database, ArrowLeft } from 'lucide-react';

export const PrivacidadePage: React.FC = () => {
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
          <span className="text-[10px] font-mono font-bold tracking-[0.2em] text-red-500 uppercase">SEGURANÇA DA INFORMAÇÃO</span>
          <h1 className="text-4xl sm:text-6xl font-black tracking-tighter leading-none mb-2">Política de Privacidade</h1>
          <p className="text-zinc-550 text-xs font-mono uppercase font-bold">Última Atualização: 27 de Maio de 2026</p>
        </div>

        {/* Security warning card */}
        <div className="bg-zinc-950 p-5 rounded-2xl border border-white/5 flex items-start gap-4 mb-10 text-xs">
          <Lock className="w-6 h-6 text-red-500 shrink-0 mt-0.5" />
          <div className="text-zinc-[450] font-semibold leading-relaxed">
            <span className="font-bold text-white block mb-0.5">Criptografia de ponta-a-ponta SSL/TLS</span>
            Seus dados cadastrais, histórico de exibição de canais e credenciais bancárias durante o checkout são totalmente encriptados digitalmente. Nós não compartilhamos informações privadas com agências governamentais ou terceiros sem autorização prévia por escrito.
          </div>
        </div>

        {/* Policy list articles */}
        <article className="flex flex-col gap-8 text-sm text-zinc-350 leading-relaxed font-semibold max-w-3xl">
          
          <section className="flex flex-col gap-2">
            <h2 className="text-xl font-black text-white tracking-tight">1. Dados Coletados</h2>
            <p>
              Ao utilizar os canais da F5 TV, coletamos informações essenciais para a melhoria tática do serviço:
            </p>
            <ul className="list-disc list-inside text-zinc-400 pl-2 flex flex-col gap-1 mt-1 text-xs">
              <li>E-mail e Nome do assinante para gerenciamento de perfis e faturamento.</li>
              <li>Dados de reprodução e taxa de bits para otimizar os fluxos CDN de streams.</li>
              <li>Sinais do sistema como modelos de dispositivos cadastrados para gerenciamento de logins simultâneos ativos.</li>
            </ul>
          </section>

          <section className="flex flex-col gap-2">
            <h2 className="text-xl font-black text-white tracking-tight">2. Uso de Chaves e Informações Bancárias</h2>
            <p>
              Os dados de cartão bancário ou CPF fornecidos durante checkouts simulados ou reais são processados sob diretrizes de segurança PCI-DSS. A F5 TV não mantém chaves brutas de CVV em seus bancos de dados compartilhados, guardando apenas tokens seguros cedidos pelas bandeiras de pagamento parceiras.
            </p>
          </section>

          <section className="flex flex-col gap-2">
            <h2 className="text-xl font-black text-white tracking-tight">3. LGPD Compliance (Lei Geral de Proteção de Dados)</h2>
            <p>
              Você, como titular de dados sob amparo da Lei nº 13.709/2018 (LGPD), detém livre direito de solicitar a qualquer tempo o acesso, a correção de inconformidades, restrições temporárias ou a deleção absoluta dos dados cadastrados em nosso sistema através da nossa central tática de contato.
            </p>
          </section>

          <section className="flex flex-col gap-2">
            <h2 className="text-xl font-black text-white tracking-tight">4. Cookies Dinâmicos de Analytics</h2>
            <p>
              Utilizamos cookies estritamente necessários para manter a integridade da sessão de login no navegador do assinante e preferência de volume de áudio do player. Cookies adicionais de rastreio de audiência são anônimos e focados em fornecer estatísticas gerais de audiência.
            </p>
          </section>

        </article>
      </main>

      {/* Footer */}
      <Footer onSelectCredential={handleSelectCredential} />

    </div>
  );
};

export default PrivacidadePage;

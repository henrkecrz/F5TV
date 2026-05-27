/**
 * @license
 * SPDX-License-Identifier: Apache-2.0
 */

import React, { useState } from 'react';
import { 
  Tv, Sparkles, Database, Smartphone, Users, ShieldCheck, 
  HelpCircle, ChevronDown, ChevronUp, Star, Check, Play, UserPlus 
} from 'lucide-react';
import { Plan, Content } from '../types';
import Footer from './Footer';

interface LandingPageProps {
  plans: Plan[];
  contents: Content[];
  onNavigate: (route: string, extra?: any) => void;
}

export default function LandingPage({ plans, contents, onNavigate }: LandingPageProps) {
  const [openFaq, setOpenFaq] = useState<number | null>(null);

  const toggleFaq = (index: number) => {
    setOpenFaq(openFaq === index ? null : index);
  };

  const featuredContents = contents.slice(0, 5);

  const benefits = [
    {
      icon: <Smartphone className="w-6 h-6 text-[#ef4444]" />,
      title: 'Assista onde quiser',
      desc: 'Disponível em Celulares, Tablets, Smart TVs, Computadores e consoles de videogame sem custo extra.'
    },
    {
      icon: <Sparkles className="w-6 h-6 text-[#ef4444]" />,
      title: 'Conteúdos Exclusivos F5',
      desc: 'Séries investigativas brutas, bastidores das grandes cidades do Brasil e jornalismo ao vivo 24h.'
    },
    {
      icon: <Database className="w-6 h-6 text-[#ef4444]" />,
      title: 'Downloads sob demanda',
      desc: 'Baixe episódios inteiros em segundos e assista no avião, metrô ou estrada mesmo sem internet.'
    },
    {
      icon: <Tv className="w-6 h-6 text-[#ef4444]" />,
      title: 'Alta Fidelidade 4K / HDR',
      desc: 'Assista seus documentários favoritos com cores calibradas de cinema e som Dolby Atmos imersivo.'
    },
    {
      icon: <Users className="w-6 h-6 text-[#ef4444]" />,
      title: 'Multi-perfil Familiar',
      desc: 'Crie perfis independentes para cada membro da família, incluindo um perfil Kids totalmente vigiado.'
    },
    {
      icon: <ShieldCheck className="w-6 h-6 text-[#ef4444]" />,
      title: 'Sem taxas de cancelamento',
      desc: 'Assine mensalmente, sem carência ou multas rescisórias. Cancele online em dois cliques a qualquer segundo.'
    }
  ];

  const faqItems = [
    {
      q: 'O que é a F5 TV?',
      a: 'A F5 TV é uma plataforma de streaming premium nacional controlada pela rede F5 de emissoras. Ela combina transmissões de jornalismo investigativo ao vivo com um catálogo rico sob demanda contendo séries exclusivas, documentários contemporâneos, programas de entretenimento tático e animações infantis.'
    },
    {
      q: 'Como funciona o período de cancelamento?',
      a: 'Nossa política de assinatura é 100% transparente. Não há contratos de fidelidade ou carência de permanência. Você pode simular o cancelamento da sua assinatura de forma instantânea diretamente na seção "Minha Conta" sem burocracias ou cobranças adicionais ocultas.'
    },
    {
      q: 'Quais aparelhos são compatíveis?',
      a: 'Você pode sintonizar a F5 TV em computadores via navegador web, smart TVs modernas (Samsung Tizen, LG WebOS, Android TV), dispositivos de streaming como Chromecast, Amazon Fire Stick e Apple TV, além de smartphones e tablets rodando iOS e Android.'
    },
    {
      q: 'O Plano Família realmente suporta visualização em telas simultâneas?',
      a: 'Sim! Com o Plano Família você pode assistir em até 3 telas simultaneamente em altíssima qualidade Full HD, além de configurar até 4 perfis individuais completos para manter suas respectivas recomendações e históricos intocados.'
    },
    {
      q: 'Como posso realizar o pagamento fictício da minha assinatura para testar?',
      a: 'Por se tratar de um ambiente MVP experimental, implementamos um gateway totalmente interativo que gera transações simuladas por Pix, Cartão de Crédito e Boleto Bancário. Qualquer fluxo registrará dados reais em seu histórico dentro d\'água administrativa!'
    }
  ];

  const testimonials = [
    {
      name: 'Fabiano Albuquerque',
      role: 'Assinante Premium',
      rating: 5,
      comment: 'O Jornal F5 ao vivo é excelente e tem qualidade impecável de transmissão. A comodidade de assistir aos documentários sob demanda no tablet durante minhas viagens me conquistou de verdade.'
    },
    {
      name: 'Camila Fernandes',
      role: 'Plano Família',
      rating: 5,
      comment: 'Estávamos procurando um streaming alternativo focado em material brasileiro sério e conteúdo infantil sem algoritmos bizarros. O Mundo F5 Kids é super seguro e educativo. Super recomendo!'
    },
    {
      name: 'Breno Gusmão',
      role: 'Assinante Básico',
      rating: 4,
      comment: 'Excelente custo-benefício. A série "Conexão F5" sobre golpes cibernéticos é simplesmente fantástica. O player abre rápido e não tem engasgos na reprodução.',
    }
  ];

  return (
    <div id="f5-landing-root" className="min-h-screen bg-[#050505] text-white selection:bg-red-600 selection:text-white font-sans">
      
      {/* Dynamic Header */}
      <header className="sticky top-0 z-40 bg-[#050505]/85 backdrop-blur-md border-b border-white/5 px-8 py-5">
        <div className="max-w-7xl mx-auto flex items-center justify-between">
          <div className="flex items-center gap-6 cursor-pointer" onClick={() => onNavigate('/')}>
            <span className="text-2xl font-black tracking-tighter uppercase text-white hover:scale-102 transition duration-200">
              F5 <span className="text-red-600">TV</span>
            </span>
            <span className="text-[10px] bg-red-600/10 text-red-600 px-2 py-0.5 rounded font-mono font-bold tracking-widest uppercase shrink-0">
              PREMIUM
            </span>
          </div>

          <div className="flex items-center gap-6">
            <button 
              id="landing-login-btn"
              onClick={() => onNavigate('/login')}
              className="text-sm font-medium text-white/60 hover:text-white transition px-3 py-1.5 cursor-pointer"
            >
              Entrar
            </button>
            <button 
              id="landing-subscribe-top-btn"
              onClick={() => {
                const plansSection = document.getElementById('planos');
                if (plansSection) {
                  plansSection.scrollIntoView({ behavior: 'smooth' });
                }
              }}
              className="bg-red-600 hover:bg-red-700 text-xs font-bold text-white px-5 py-2.5 rounded uppercase tracking-wider transition duration-200 cursor-pointer shadow-lg shadow-red-900/10"
            >
              Assine Agora
            </button>
          </div>
        </div>
      </header>

      {/* Hero Banner Section */}
      <section id="hero" className="relative h-[560px] px-8 pt-12 flex flex-col justify-end pb-16 overflow-hidden border-b border-white/5">
        <div className="absolute inset-0 bg-gradient-to-r from-[#050505] via-[#050505]/75 to-transparent z-10" />
        <div className="absolute inset-0 bg-gradient-to-t from-[#050505] to-transparent z-10" />
        {/* Simulated Hero Image Background */}
        <div className="absolute inset-0 z-0 bg-[url('https://images.unsplash.com/photo-1536440136628-849c177e76a1?q=80&w=2025&auto=format&fit=crop')] bg-cover bg-center opacity-65 grayscale-[0.25]" />
        
        <div className="max-w-4xl mx-auto w-full relative z-20 flex flex-col items-start gap-4">
          <div className="inline-flex items-center gap-2 bg-red-600 px-2.5 py-0.5 text-[10px] font-mono font-bold rounded uppercase tracking-widest">
            <Sparkles className="w-3.5 h-3.5 animate-pulse" />
            <span>SÉRIE ORIGINAL F5</span>
          </div>

          <h1 className="text-5xl sm:text-7xl font-black tracking-tighter leading-none text-white max-w-3xl">
            CONEXÃO <span className="text-red-600 italic">F5</span>
          </h1>

          <p className="text-white/70 text-base md:text-lg max-w-2xl leading-relaxed font-normal">
            Mergulhe nos bastidores do poder, do jornalismo investigativo tático, de documentários de alta voltagem e de esportes ao vivo. Sem censura, com qualidade calibrada de cinema.
          </p>

          <div className="flex flex-col sm:flex-row gap-4 w-full h-fit max-w-md mt-4">
            <button 
              id="hero-subscribe-cta"
              onClick={() => {
                const plansSection = document.getElementById('planos');
                if (plansSection) {
                  plansSection.scrollIntoView({ behavior: 'smooth' });
                }
              }}
              className="px-8 py-3.5 rounded bg-white text-black font-bold flex items-center justify-center gap-2 hover:bg-red-600 hover:text-white transition-colors cursor-pointer text-sm uppercase tracking-wider"
            >
              <UserPlus className="w-4 h-4" />
              <span>Assinar Agora</span>
            </button>
            <button 
              id="hero-catalogo-preview-cta"
              onClick={() => onNavigate('/app')} // Goes into streaming client sandbox
              className="bg-white/10 backdrop-blur-md text-white border border-white/20 px-8 py-3.5 rounded font-bold flex items-center justify-center gap-2 hover:bg-white/20 transition cursor-pointer text-sm uppercase tracking-wider"
            >
              <Play className="w-4 h-4 fill-current" />
              <span>Explorar Prévia</span>
            </button>
          </div>
          
          <div className="flex items-center gap-6 text-[10px] text-white/30 uppercase tracking-[0.2em] font-medium font-mono mt-8">
            <span>✓ CANCELAMENTO 100% ONLINE</span>
            <span>•</span>
            <span>✓ FILMES FILTRADOS EM 4K</span>
          </div>
        </div>
      </section>

      {/* CAPAS DE CONTEÚDO FICTÍCIAS SECTION */}
      <section id="conteudo-previa" className="py-20 px-8 bg-[#050505] border-b border-white/5">
        <div className="max-w-7xl mx-auto">
          <div className="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mb-12">
            <div>
              <span className="text-[10px] font-mono font-bold tracking-[0.2em] text-red-650 uppercase">NO CATÁLOGO</span>
              <h2 className="text-3xl md:text-5xl font-black tracking-tight text-white mt-2">Nossos Maiores Sucessos Disponíveis</h2>
            </div>
            <button 
              onClick={() => onNavigate('/login')}
              className="text-sm font-semibold text-red-600 hover:text-red-500 flex items-center gap-2 transition-colors cursor-pointer uppercase tracking-wider font-mono text-xs"
            >
              <span>Ver catálogo completo</span>
              <Play className="w-3.5 h-3.5 fill-current" />
            </button>
          </div>

          <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            {contents.map((item) => (
              <div 
                key={item.id}
                onClick={() => onNavigate('/login')}
                className="group relative bg-[#0a0a0a] border border-white/5 rounded-lg overflow-hidden cursor-pointer hover:border-red-600/50 transform transition-all duration-300 hover:scale-[1.03] shadow-2xl"
              >
                {/* Horizontal Capa Wrapper */}
                <div className="aspect-[3/4] relative w-full bg-[#151515]">
                  <img 
                    src={item.coverUrl} 
                    alt={item.title} 
                    referrerPolicy="no-referrer"
                    className="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition-opacity duration-300"
                  />
                  <div className="absolute top-2.5 right-2.5 bg-[#050505]/90 text-[10px] font-mono font-bold text-red-600 px-2 py-0.5 rounded border border-white/5">
                    {item.ageRating}
                  </div>
                  {item.isExclusive && (
                    <div className="absolute bottom-2.5 left-2.5 bg-red-600 text-[10px] font-bold text-white px-2 py-0.5 rounded uppercase font-sans tracking-tight">
                      Exclusivo
                    </div>
                  )}
                </div>
                <div className="p-4 bg-[#0a0a0a] flex flex-col gap-1.5">
                  <span className="text-[9px] font-mono tracking-wider font-semibold uppercase text-white/40">
                    {item.genre}
                  </span>
                  <h3 className="font-bold text-sm text-white/90 line-clamp-1 group-hover:text-white transition-colors duration-250">
                    {item.title}
                  </h3>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Benefícios Section */}
      <section id="beneficios" className="py-24 px-8 bg-[#050505] border-b border-white/5">
        <div className="max-w-7xl mx-auto">
          <div className="text-left max-w-2xl mb-20 flex flex-col gap-3">
            <span className="text-[10px] font-mono font-bold tracking-[0.2em] text-red-650 uppercase">BENEFÍCIOS EXCLUSIVOS</span>
            <h2 className="text-4xl sm:text-6xl font-black tracking-tighter text-white leading-none">Por que assinar a F5 TV?</h2>
            <p className="text-white/60 text-base font-normal mt-2 leading-relaxed">Elevamos o padrão de streaming nacional com jornalismo tático, documentários premium e tecnologia de ponta.</p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            {benefits.map((b, i) => (
              <div 
                key={i} 
                className="p-8 bg-[#0a0a0a] border border-white/5 hover:border-white/10 rounded-xl transition-all duration-300 flex flex-col gap-4 group"
              >
                <div className="p-3 bg-[#050505] w-fit rounded-lg border border-white/5 group-hover:bg-red-600/10 group-hover:border-red-600/30 transition duration-350">
                  {b.icon}
                </div>
                <h3 className="text-xl font-bold text-white group-hover:text-red-600 transition duration-200">{b.title}</h3>
                <p className="text-sm text-white/60 leading-relaxed font-normal">{b.desc}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Planos de Assinatura Section */}
      <section id="planos" className="py-24 px-8 bg-[#050505] border-b border-white/5">
        <div className="max-w-7xl mx-auto">
          <div className="text-left max-w-2xl mb-18 flex flex-col gap-3">
            <span className="text-[10px] font-mono font-bold tracking-[0.2em] text-red-655 uppercase">PREÇOS TRANSPARENTES</span>
            <h2 className="text-4xl sm:text-6xl font-black tracking-tighter text-white leading-none">Escolha o seu plano ideal</h2>
            <p className="text-white/60 text-base font-normal mt-2">Valores simples, táticos e sem fidelidade. Cancele digitalmente na simulação de conta em 2 cliques.</p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl">
            {plans.map((p) => {
              const isPremium = p.id === 'plano-premium';

              return (
                <div 
                  key={p.id}
                  className={`flex flex-col rounded-xl relative border overflow-hidden ${
                    isPremium 
                      ? 'bg-gradient-to-b from-[#100c0c] to-[#050505] border-red-600 shadow-2xl' 
                      : 'bg-[#0a0a0a] border-white/5'
                  }`}
                >
                  {isPremium && (
                    <div className="absolute top-0 right-0 left-0 bg-red-600 text-white text-[9px] font-bold text-center py-1.5 uppercase tracking-widest font-mono">
                      RECOMENDADO / MAIOR QUALIDADE
                    </div>
                  )}

                  <div className={`p-8 flex flex-col gap-5 border-b border-white/5 ${isPremium ? 'pt-10' : ''}`}>
                    <span className="text-xs font-mono tracking-widest font-bold uppercase text-white/40">{p.name}</span>
                    <div className="flex items-baseline gap-1">
                      <span className="text-white/40 font-bold text-sm">R$</span>
                      <span className="text-4xl font-black text-white">
                        {p.price.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}
                      </span>
                      <span className="text-white/45 text-xs">/mês</span>
                    </div>
                    {p.active ? (
                      <button 
                        id={`btn-subscribe-plan-${p.id}`}
                        onClick={() => onNavigate('/cadastro', { planId: p.id })}
                        className={`w-full font-bold px-4 py-3.5 rounded text-xs text-center transition tracking-wider uppercase font-mono cursor-pointer flex items-center justify-center gap-2 ${
                          isPremium 
                            ? 'bg-red-600 hover:bg-red-700 text-white shadow-lg shadow-red-900/10' 
                            : 'bg-white/10 hover:bg-white/20 text-white border border-white/10'
                        }`}
                      >
                        Definir Plano & Seguir
                      </button>
                    ) : (
                      <div className="w-full bg-[#050505] border border-white/5 py-3.5 text-center text-white/30 font-mono text-xs rounded uppercase">
                        Temporariamente Inativo
                      </div>
                    )}
                  </div>

                  {/* List Benefits */}
                  <div className="flex-1 p-8 flex flex-col gap-4 bg-black/20">
                    {p.features.map((feat, i) => (
                      <div key={i} className="flex gap-3 items-start text-xs text-white/80">
                        <Check className="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
                        <span className="leading-relaxed font-normal">{feat}</span>
                      </div>
                    ))}
                  </div>
                </div>
              );
            })}
          </div>

          {/* Plan Comparison Matrix */}
          <div className="mt-20 max-w-5xl border border-white/5 bg-[#0a0a0a]/50 rounded-xl p-8 hidden md:block">
            <h3 className="text-lg font-bold text-white mb-6 font-sans">Comparativo Detalhado de Recursos</h3>
            <table className="w-full text-left text-sm text-white/60">
              <thead>
                <tr className="border-b border-white/5 text-white font-mono text-xs font-bold">
                  <th className="pb-4 font-semibold uppercase tracking-wider">Recurso</th>
                  <th className="pb-4 font-semibold text-center tracking-wider">Básico</th>
                  <th className="pb-4 font-semibold text-center tracking-wider">Família</th>
                  <th className="pb-4 font-semibold text-center tracking-wider">Premium</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-white/5 font-normal text-white/80">
                <tr>
                  <td className="py-4 font-medium text-white">Telas simultâneas</td>
                  <td className="py-4 text-center">1 tela</td>
                  <td className="py-4 text-center">3 telas</td>
                  <td className="py-4 text-center">5 telas</td>
                </tr>
                <tr>
                  <td className="py-4 font-medium text-white">Qualidade máxima</td>
                  <td className="py-4 text-center">HD (720p)</td>
                  <td className="py-4 text-center">Full HD (1080p)</td>
                  <td className="py-4 text-center text-red-600">Ultra HD (4K) & HDR</td>
                </tr>
                <tr>
                  <td className="py-4 font-medium text-white">Downloads para ver offline</td>
                  <td className="py-4 text-center text-white/20">✕</td>
                  <td className="py-4 text-center text-emerald-500">✓</td>
                  <td className="py-4 text-center text-emerald-500">✓ (Ilimitados)</td>
                </tr>
                <tr>
                  <td className="py-4 font-medium text-white">Som Especial Dolby Atmos</td>
                  <td className="py-4 text-center text-white/20">✕</td>
                  <td className="py-4 text-center text-white/25">✕</td>
                  <td className="py-4 text-center text-emerald-500">✓</td>
                </tr>
                <tr>
                  <td className="py-4 font-medium text-white">Anúncios</td>
                  <td className="py-4 text-center uppercase text-white/40 font-mono text-xs">Pontuais</td>
                  <td className="py-4 text-center text-emerald-500">Sem anúncios</td>
                  <td className="py-4 text-center text-emerald-500">Sem anúncios</td>
                </tr>
              </tbody>
            </table>
          </div>

        </div>
      </section>

      {/* Testemunhos Clientes Section */}
      <section className="py-24 px-8 bg-[#050505] border-b border-white/5">
        <div className="max-w-7xl mx-auto">
          <div className="text-left max-w-2xl mb-20 flex flex-col gap-3">
            <span className="text-[10px] font-mono font-bold tracking-[0.2em] text-red-650 uppercase">CLIENTES SATISFEITOS</span>
            <h2 className="text-4xl sm:text-6xl font-black tracking-tighter text-white leading-none">Opinião de quem já apoia</h2>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl">
            {testimonials.map((t, index) => (
              <div 
                key={index}
                className="p-8 bg-[#0a0a0a] border border-white/5 rounded-xl flex flex-col justify-between gap-6 relative"
              >
                <div className="flex flex-col gap-3">
                  <div className="flex text-red-600 gap-1">
                    {[...Array(t.rating)].map((_, i) => (
                      <Star key={i} className="w-3.5 h-3.5 fill-current" />
                    ))}
                  </div>
                  <p className="text-white/70 italic text-sm leading-relaxed font-normal">
                    "{t.comment}"
                  </p>
                </div>
                <div className="border-t border-white/5 pt-4 mt-2 flex flex-col">
                  <span className="font-bold text-sm text-white">{t.name}</span>
                  <span className="text-[10px] text-white/40 font-mono uppercase tracking-wider mt-0.5">{t.role}</span>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* FAQ Seção Accordion */}
      <section id="faq" className="py-24 px-8 bg-[#050505] border-b border-white/5">
        <div className="max-w-3xl mx-auto">
          <div className="text-left mb-20 flex flex-col gap-3">
            <span className="text-[10px] font-mono font-bold tracking-[0.2em] text-red-650 uppercase">DÚVIDAS FREQUENTES</span>
            <h2 className="text-4xl sm:text-5xl font-black tracking-tighter text-white leading-none">Perguntas Frequentes (FAQ)</h2>
          </div>

          <div className="flex flex-col gap-3">
            {faqItems.map((item, index) => {
              const isOpen = openFaq === index;
              return (
                <div 
                  key={index}
                  className="border border-white/5 rounded-lg overflow-hidden bg-[#0a0a0a] transition-all duration-200"
                >
                  <button 
                    id={`faq-toggle-${index}`}
                    onClick={() => toggleFaq(index)}
                    className="w-full text-left p-6 flex justify-between items-center gap-4 hover:bg-neutral-900 transition font-bold font-sans cursor-pointer text-zinc-100 hover:text-white"
                  >
                    <span>{item.q}</span>
                    {isOpen ? <ChevronUp className="w-5 h-5 text-red-650 shrink-0" /> : <ChevronDown className="w-5 h-5 text-zinc-500 shrink-0" />}
                  </button>

                  <div 
                    className={`transition-all duration-300 overflow-hidden ${
                      isOpen ? 'max-h-60 border-t border-white/5' : 'max-h-0'
                    }`}
                  >
                    <p className="p-6 text-white/60 text-sm leading-relaxed font-normal bg-[#050505]">
                      {item.a}
                    </p>
                  </div>
                </div>
              );
            })}
          </div>

          {/* CTA Bottom Banner */}
          <div className="mt-24 border border-white/5 bg-gradient-to-r from-[#0a0a0a] via-red-950/20 to-[#0a0a0a] rounded-xl p-8 sm:p-14 text-center relative overflow-hidden">
            <div className="relative z-10 flex flex-col items-center gap-4">
              <h3 className="text-3xl sm:text-4xl font-black tracking-tighter text-white leading-tight">Pronto para dar o seu próximo F5?</h3>
              <p className="text-white/60 text-sm max-w-md font-normal leading-relaxed">Navegue pelas notícias sem censura, séries exclusivas e conteúdo seguro para crianças desde o primeiro minuto.</p>
              <button 
                id="landing-bottom-cta"
                onClick={() => {
                  const plansSection = document.getElementById('planos');
                  if (plansSection) {
                    plansSection.scrollIntoView({ behavior: 'smooth' });
                  }
                }}
                className="mt-4 bg-red-600 hover:bg-red-700 text-xs font-bold tracking-wider uppercase font-mono text-white px-8 py-3.5 rounded transition duration-200 cursor-pointer shadow-lg shadow-red-900/15"
              >
                Escolher Meu Plano & Assinar
              </button>
            </div>
          </div>
        </div>
      </section>

      {/* Render Public Footer */}
      <Footer onSelectCredential={() => {}} />

    </div>
  );
}

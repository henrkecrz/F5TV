import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import Footer from '../../components/Footer';
import { Send, FileText, CheckCircle, Mail, MapPin, Phone, ArrowLeft, ArrowRight, Activity } from 'lucide-react';

export const ContatoPage: React.FC = () => {
  const navigate = useNavigate();
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [department, setDepartment] = useState('suporte');
  const [message, setMessage] = useState('');
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [sentSuccess, setSentSuccess] = useState(false);

  const handleSelectCredential = (emailToLogin: string) => {
    navigate('/login', { state: { email: emailToLogin } });
  };

  const handleSendMessage = (e: React.FormEvent) => {
    e.preventDefault();
    if (!name.trim() || !email.trim() || !message.trim()) return;

    setIsSubmitting(true);
    setTimeout(() => {
      setIsSubmitting(false);
      setSentSuccess(true);
      // Reset
      setName('');
      setEmail('');
      setMessage('');
    }, 1800);
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
      <main className="flex-grow max-w-5xl mx-auto px-8 py-16 text-left w-full">
        <div className="flex flex-col gap-3 mb-10">
          <span className="text-[10px] font-mono font-bold tracking-[0.2em] text-red-500 uppercase">FALE CONOSCO</span>
          <h1 className="text-4xl sm:text-6xl font-black tracking-tighter leading-none mb-2">Central de Atendimento</h1>
          <p className="text-zinc-400 text-sm leading-relaxed max-w-xl font-semibold">
            Seja para dúvidas comerciais, reportes de problemas técnicos ao reproduzir vídeos ou agendamento de imprensa, nossa central tática está pronta para ajudar.
          </p>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-5 gap-12 mt-12">
          
          {/* Support options column */}
          <div className="lg:col-span-2 flex flex-col gap-6 font-semibold">
            <div className="bg-[#0a0a0a] border border-white/5 p-6 rounded-2xl flex flex-col gap-5">
              <h3 className="text-lg font-black text-white tracking-tight">Canais Globais</h3>
              
              <div className="flex items-start gap-4 text-xs font-medium text-zinc-400">
                <MapPin className="w-5 h-5 text-red-500 shrink-0 mt-0.5" />
                <div className="flex flex-col gap-0.5">
                  <span className="text-white font-black font-sans text-sm">Escritório Central</span>
                  <span>Av. das Nações Unidas, 12901 - Brooklin</span>
                  <span>São Paulo - SP, Brasil</span>
                </div>
              </div>

              <div className="flex items-start gap-4 text-xs font-medium text-zinc-400">
                <Phone className="w-5 h-5 text-red-500 shrink-0 mt-0.5" />
                <div className="flex flex-col gap-0.5">
                  <span className="text-white font-black font-sans text-sm">Contato Telefônico</span>
                  <span className="font-mono">0800 707 9900 (SAC Geral)</span>
                  <span className="font-mono">+55 11 3060-4000 (Comercial)</span>
                </div>
              </div>

              <div className="flex items-start gap-4 text-xs font-medium text-zinc-400">
                <Mail className="w-5 h-5 text-red-500 shrink-0 mt-0.5" />
                <div className="flex flex-col gap-0.5">
                  <span className="text-white font-black font-sans text-sm">Correspondência Eletrônica</span>
                  <span className="font-mono text-red-400">suporte@f5tv.com.br</span>
                  <span className="font-mono text-red-400">comercial@f5tv.com.br</span>
                </div>
              </div>
            </div>

            <div className="p-5 border border-dashed border-white/5 bg-[#050505]/30 rounded-2xl flex items-center gap-3">
              <Activity className="w-8 h-8 text-green-500 shrink-0" />
              <div className="text-[11px] text-zinc-550 leading-relaxed font-mono">
                <span className="font-bold text-zinc-400 block uppercase">Tempo de Resposta Médio</span>
                <span>Nossas equipes táticas respondem e solucionam tickets corporativos em menos de 45 minutos.</span>
              </div>
            </div>
          </div>

          {/* Form container Column */}
          <div className="lg:col-span-3">
            {sentSuccess ? (
              <div className="bg-[#0a0a0a] border border-green-950/40 p-10 rounded-3xl text-center flex flex-col gap-6 shadow-xl relative overflow-hidden">
                <div className="w-14 h-14 bg-green-950/40 border border-green-900 rounded-full flex items-center justify-center self-center">
                  <CheckCircle className="w-7 h-7 text-green-500" />
                </div>
                <div className="flex flex-col gap-1.5">
                  <h3 className="text-2xl font-black tracking-tight text-white">Mensagem Enviada!</h3>
                  <p className="text-zinc-500 text-xs font-semibold leading-relaxed max-w-sm mx-auto">
                    Agradecemos seu contato. Seu ticket foi gerado com sucesso e encaminhado ao departamento selecionado. Um especialista responderá em breve.
                  </p>
                </div>
                <button
                  onClick={() => setSentSuccess(false)}
                  className="font-mono font-bold text-xs uppercase tracking-wider text-red-500 hover:text-red-400 transition flex items-center justify-center gap-1 mt-2 cursor-pointer"
                >
                  <span>Enviar Nova Mensagem</span>
                  <ArrowRight className="w-4 h-4" />
                </button>
              </div>
            ) : (
              <div className="bg-[#0a0a0a] border border-white/5 p-8 rounded-2xl">
                <h3 className="text-lg font-black tracking-tight text-white mb-6">Envie um Ticket Direto</h3>
                
                <form onSubmit={handleSendMessage} className="flex flex-col gap-4">
                  <div className="flex flex-col gap-1 text-xs sm:col-span-2">
                    <label className="text-zinc-[450] font-mono font-bold uppercase text-[10px] pb-0.5">Seu Nome Completo</label>
                    <input
                      type="text"
                      required
                      value={name}
                      onChange={e => setName(e.target.value)}
                      placeholder="Ex: SANDRA ALMEIDA CORREIA"
                      className="bg-zinc-950 border border-white/5 p-3 rounded text-white outline-none text-xs focus:border-red-650"
                    />
                  </div>

                  <div className="flex flex-col gap-1 text-xs">
                    <label className="text-zinc-[450] font-mono font-bold uppercase text-[10px] pb-0.5">Endereço de E-mail</label>
                    <input
                      type="email"
                      required
                      value={email}
                      onChange={e => setEmail(e.target.value)}
                      placeholder="Ex: sandra@exemplo.com"
                      className="bg-zinc-950 border border-white/5 p-3 rounded text-white outline-none text-xs focus:border-red-650"
                    />
                  </div>

                  <div className="flex flex-col gap-1 text-xs">
                    <label className="text-zinc-[450] font-mono font-bold uppercase text-[10px] pb-0.5">Destinatário / Departamento</label>
                    <select
                      value={department}
                      onChange={e => setDepartment(e.target.value)}
                      className="bg-zinc-950 border border-white/5 p-3 rounded text-zinc-400 outline-none text-xs focus:border-red-650 cursor-pointer"
                    >
                      <option value="suporte">Ajuda Técnica & Player de Vídeo</option>
                      <option value="comercial">Anúncios & Contrato Comercial</option>
                      <option value="imprensa">Fatos & Envio de Pautas Jornalísticas</option>
                      <option value="financeiro">Cobranças & Estorno de Assinatura</option>
                    </select>
                  </div>

                  <div className="flex flex-col gap-1 text-xs">
                    <label className="text-zinc-[450] font-mono font-bold uppercase text-[10px] pb-0.5">Mensagem Completa</label>
                    <textarea
                      required
                      rows={5}
                      value={message}
                      onChange={e => setMessage(e.target.value)}
                      placeholder="Descreva detalhadamente o motivo do seu contato..."
                      className="bg-zinc-950 border border-white/5 p-3 rounded text-white outline-none text-xs resize-none focus:border-red-650 leading-relaxed font-semibold"
                    />
                  </div>

                  <button
                    type="submit"
                    disabled={isSubmitting}
                    className="w-full bg-red-600 hover:bg-red-700 disabled:bg-zinc-800 text-white font-mono font-extrabold text-xs uppercase tracking-widest py-3 rounded-xl transition flex items-center justify-center gap-2 cursor-pointer mt-2"
                  >
                    {isSubmitting ? (
                      <span>Enviando ticket...</span>
                    ) : (
                      <>
                        <span>Emitir Ticket de Contato</span>
                        <Send className="w-3.5 h-3.5" />
                      </>
                    )}
                  </button>
                </form>
              </div>
            )}
          </div>

        </div>
      </main>

      {/* Footer */}
      <Footer onSelectCredential={handleSelectCredential} />

    </div>
  );
};

export default ContatoPage;

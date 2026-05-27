import React, { useState, useEffect, useRef } from 'react';
import { useData } from '../../context/DataContext';
import { Channel, LiveSchedule } from '../../types';
import { Radio, Users, Send, MessageSquare, Play, Volume2, Shield, Calendar, Share2, HelpCircle } from 'lucide-react';

interface ChatMessage {
  id: string;
  userName: string;
  text: string;
  time: string;
}

const CHAT_TEMPLATES = [
  { userName: 'Carlos_Silva', text: 'Essa transmissão está muito fluida! Qualidade nota 10!' },
  { userName: 'AnaMariano', text: 'Excelente debate! F5 sempre na frente.' },
  { userName: 'FelipeGamer', text: 'Melhor som de transmissão disparado' },
  { userName: 'Bruno_M', text: 'O sinal do canal premium ao vivo está excelente.' },
  { userName: 'Sandro_B', text: 'Grande cobertura esportiva!' },
  { userName: 'PattyOliveira', text: 'Uau, que qualidade sensacional de stream!' },
  { userName: 'Dev_Gusta', text: 'A f5tv é o melhor streaming de TV hoje em dia, sem travar nada.' },
  { userName: 'LucasRibeiro', text: 'Muito bom ver notícias em tempo real assim' }
];

export const AoVivoPage: React.FC = () => {
  const { channels, liveSchedules } = useData();
  const [activeChannel, setActiveChannel] = useState<Channel | null>(null);
  const [chatMessages, setChatMessages] = useState<ChatMessage[]>([]);
  const [newMsg, setNewMsg] = useState('');
  const [viewerCount, setViewerCount] = useState(1240);
  const chatEndRef = useRef<HTMLDivElement>(null);
  const videoRef = useRef<HTMLVideoElement>(null);

  // Set default active channel to the first one available
  useEffect(() => {
    if (channels && channels.length > 0 && !activeChannel) {
      setActiveChannel(channels[0]);
    }
  }, [channels]);

  // Seed chat comments and simulate live growth
  useEffect(() => {
    const initialMsgs: ChatMessage[] = [
      { id: 'c1', userName: 'LucianoSales', text: 'Boa tarde pessoal! Acompanhando de SP!', time: 'Agora' },
      { id: 'c2', userName: 'MariSouza', text: 'Estava esperando por essa programação!', time: 'Agora' }
    ];
    setChatMessages(initialMsgs);

    const chatInterval = setInterval(() => {
      const randomTpl = CHAT_TEMPLATES[Math.floor(Math.random() * CHAT_TEMPLATES.length)];
      const date = new Date();
      const timeStr = `${String(date.getHours()).padStart(2, '0')}:${String(date.getMinutes()).padStart(2, '0')}`;
      
      const newMsgObj: ChatMessage = {
        id: `chat-${Date.now()}`,
        userName: randomTpl.userName,
        text: randomTpl.text,
        time: timeStr
      };

      setChatMessages(prev => [...prev.slice(-30), newMsgObj]); // Keep last 30
      // Randomize viewer count slightly (live simulation)
      setViewerCount(prev => prev + Math.floor(Math.random() * 11) - 5);
    }, 4500);

    return () => clearInterval(chatInterval);
  }, []);

  // Auto scroll chat to bottom on new message
  useEffect(() => {
    chatEndRef.current?.scrollIntoView({ behavior: 'smooth' });
  }, [chatMessages]);

  const handleSendMessage = (e: React.FormEvent) => {
    e.preventDefault();
    if (!newMsg.trim()) return;

    const date = new Date();
    const timeStr = `${String(date.getHours()).padStart(2, '0')}:${String(date.getMinutes()).padStart(2, '0')}`;

    const userMessage: ChatMessage = {
      id: `chat-user-${Date.now()}`,
      userName: 'Você (Assinante)',
      text: newMsg,
      time: timeStr
    };

    setChatMessages(prev => [...prev, userMessage]);
    setNewMsg('');
  };

  const currentProgram = activeChannel
    ? liveSchedules.find(ls => ls.channelId === activeChannel.id && ls.status === 'live')
    : null;

  const nextPrograms = activeChannel
    ? liveSchedules
        .filter(ls => ls.channelId === activeChannel.id && ls.status === 'scheduled')
        .sort((a, b) => a.startTime.localeCompare(b.startTime))
    : [];

  return (
    <div className="flex flex-col gap-6 text-white text-left font-sans select-none min-h-[85vh]">
      
      {/* Upper header summary */}
      <div className="flex flex-col md:flex-row md:items-center justify-between gap-3 border-b border-zinc-900 pb-4">
        <div>
          <span className="text-rose-500 font-mono font-black text-xs tracking-widest uppercase flex items-center gap-1.5">
            <span className="w-2.5 h-2.5 rounded-full bg-red-650 animate-pulse block" />
            Transmissão Simultânea Ao Vivo
          </span>
          <h1 className="text-3xl font-black tracking-tight mt-1">Central de Transmissões</h1>
        </div>
        <div className="flex items-center gap-3 bg-zinc-950 border border-zinc-900 px-4 py-2 rounded-xl text-xs text-zinc-400 font-mono">
          <Users className="w-4 h-4 text-rose-500 shrink-0" />
          <span>{viewerCount} assinantes assistindo agora</span>
        </div>
      </div>

      {channels.length === 0 ? (
        <div className="bg-zinc-950 border border-zinc-900 p-16 text-center text-zinc-600 rounded-3xl flex flex-col items-center gap-3">
          <HelpCircle className="w-10 h-10 text-zinc-800" />
          <h3 className="font-bold text-base text-zinc-450">Nenhum canal no ar no momento</h3>
          <p className="text-xs">Entre em contato com o suporte ou aguarde o retorno da transmissão oficial.</p>
        </div>
      ) : (
        <div className="grid grid-cols-1 lg:grid-cols-4 gap-6">
          
          {/* Main live interactive video layout */}
          <div className="lg:col-span-3 flex flex-col gap-5">
            
            {/* Live TV Video Sandbox with controls fallback */}
            <div className="relative aspect-video bg-black rounded-2xl overflow-hidden shadow-2xl border border-zinc-900">
              {activeChannel ? (
                <video
                  ref={videoRef}
                  src={activeChannel.streamUrl}
                  autoPlay
                  loop
                  muted
                  playsInline
                  className="w-full h-full object-cover"
                />
              ) : (
                <div className="w-full h-full flex items-center justify-center bg-zinc-950 text-xs text-zinc-500">
                  Carregando sinal da emissora...
                </div>
              )}

              {/* Live Tag inside video screen overlay */}
              <div className="absolute top-4 left-4 bg-red-650 text-white font-mono font-extrabold text-[10px] tracking-wider uppercase px-2.5 py-1 rounded-md flex items-center gap-1.5 shadow-lg shadow-black/50">
                <span className="w-1.5 h-1.5 rounded-full bg-white animate-pulse" />
                AO VIVO
              </div>

              {activeChannel && (
                <div className="absolute top-4 right-4 bg-black/70 backdrop-blur-md border border-white/5 text-zinc-300 font-mono font-bold text-[10px] uppercase px-3 py-1 rounded-lg">
                  Canal: {activeChannel.logoText}
                </div>
              )}

              {/* Playback simulation toolbar overlay */}
              <div className="absolute bottom-4 left-4 right-4 bg-zinc-950/80 backdrop-blur-md border border-white/5 p-3 rounded-xl flex items-center justify-between text-xs text-zinc-400">
                <div className="flex items-center gap-3">
                  <button className="p-1 hover:text-white transition cursor-pointer">
                    <Play className="w-4 h-4 text-[#ef4444]" />
                  </button>
                  <div className="flex items-center gap-1.5">
                    <Volume2 className="w-4 h-4" />
                    <div className="w-16 bg-zinc-800 h-1 rounded-full overflow-hidden">
                      <div className="bg-[#ef4444] h-full w-2/3" />
                    </div>
                  </div>
                </div>

                <div className="flex items-center gap-3 text-[10px] font-mono font-bold tracking-wider">
                  <span className="text-green-500 flex items-center gap-1"><Shield className="w-3 h-3" /> F5-PROTECT</span>
                  <span>1080P PRO-HD</span>
                </div>
              </div>
            </div>

            {/* Program specifications */}
            <div className="bg-zinc-950 border border-zinc-900 p-6 rounded-2xl flex flex-col gap-4">
              <div className="flex items-center gap-3">
                <div className="w-12 h-12 bg-red-650 rounded-xl flex items-center justify-center font-black tracking-tighter text-sm text-white shrink-0 shadow-md">
                  {activeChannel?.logoText || 'F5'}
                </div>
                <div>
                  <h2 className="text-xl font-black text-zinc-100">{activeChannel?.name || 'Iniciando transmissões'}</h2>
                  <p className="text-xs text-zinc-500 font-mono uppercase mt-0.5 font-bold">Categoria: {activeChannel?.category || 'Geral'}</p>
                </div>
              </div>

              {currentProgram ? (
                <div className="bg-zinc-900/60 p-4 border border-zinc-850 rounded-xl flex flex-col gap-2">
                  <span className="text-[10px] font-mono font-extrabold tracking-widest text-[#ef4444] uppercase">NO AR AGORA:</span>
                  <div className="flex flex-col">
                    <h3 className="text-lg font-black text-white">{currentProgram.title}</h3>
                    {currentProgram.description && <p className="text-xs text-zinc-400 mt-1 leading-relaxed">{currentProgram.description}</p>}
                    {currentProgram.host && <span className="text-zinc-500 font-mono text-[11px] font-bold uppercase mt-2">Apresentador: {currentProgram.host}</span>}
                  </div>
                </div>
              ) : (
                <div className="bg-[#09090b] p-4 text-center border border-dashed border-zinc-900 rounded-xl text-xs text-zinc-650 italic">
                  Nenhuma grade cadastrada como 'No Ar' no momento para este canal. Executando sinal de autoplay.
                </div>
              )}

              {/* Next in the schedule line-up for this channel */}
              {nextPrograms.length > 0 && (
                <div className="flex flex-col gap-2 mt-2">
                  <span className="text-xs font-mono font-bold text-zinc-550 uppercase">Próximas atrações de hoje</span>
                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-1">
                    {nextPrograms.slice(0, 2).map((ls) => (
                      <div key={ls.id} className="bg-[#09090b] p-3 border border-zinc-900 rounded-xl flex justify-between items-center text-xs">
                        <div className="flex flex-col gap-0.5 truncate max-w-[200px]">
                          <span className="font-extrabold text-zinc-350 truncate">{ls.title}</span>
                          {ls.host && <span className="text-zinc-550 text-[10px]">Apresentação: {ls.host}</span>}
                        </div>
                        <span className="font-mono bg-zinc-900 border border-zinc-850 text-[#ef4444] px-2 py-0.5 rounded text-[10px] text-zinc-400 select-none">
                          {ls.startTime}
                        </span>
                      </div>
                    ))}
                  </div>
                </div>
              )}
            </div>
          </div>

          {/* Sidebar Area: Live chat & selector carousel */}
          <div className="flex flex-col gap-5">
            
            {/* Selector: Channels lists buttons */}
            <div className="bg-zinc-950 border border-zinc-900 p-4 rounded-xl flex flex-col gap-3">
              <span className="text-xs font-mono font-bold text-zinc-500 uppercase">Selecione o Canal</span>
              <div className="flex flex-col gap-2">
                {channels.map((ch) => (
                  <button
                    key={ch.id}
                    onClick={() => setActiveChannel(ch)}
                    className={`w-full flex items-center justify-between p-3 rounded-xl transition cursor-pointer text-left border ${
                      activeChannel?.id === ch.id
                        ? 'bg-zinc-900 border-red-650'
                        : 'bg-[#09090b] border-zinc-900 hover:border-zinc-850'
                    }`}
                  >
                    <div className="flex items-center gap-3">
                      <div className="w-8 h-8 rounded-lg bg-zinc-900 flex items-center justify-center font-black tracking-tight text-[10px] border border-zinc-850">
                        {ch.logoText}
                      </div>
                      <div className="flex flex-col">
                        <span className="font-bold text-xs text-zinc-200">{ch.name}</span>
                        <span className="text-[9px] font-mono text-zinc-500">{ch.category || 'Geral'}</span>
                      </div>
                    </div>
                    {ch.active ? (
                      <span className="w-2 h-2 rounded-full bg-green-500" />
                    ) : (
                      <span className="w-2 h-2 rounded-full bg-zinc-750" />
                    )}
                  </button>
                ))}
              </div>
            </div>

            {/* Chat Room Area Simulator */}
            <div className="bg-zinc-950 border border-zinc-900 rounded-2xl flex flex-col h-[400px]">
              <div className="p-3 border-b border-zinc-900 bg-zinc-950/20 flex items-center justify-between text-xs">
                <span className="font-bold flex items-center gap-1.5"><MessageSquare className="w-4 h-4 text-[#ef4444]" /> Chat do Assinante</span>
                <span className="font-mono text-[10px] text-green-500">Sinal {viewerCount > 10 ? 'online' : 'offline'}</span>
              </div>

              {/* Messages feed */}
              <div className="flex-1 overflow-y-auto p-4 flex flex-col gap-3 text-xs scrollbar-thin">
                {chatMessages.map((msg) => (
                  <div key={msg.id} className="flex flex-col gap-0.5 leading-snug break-words">
                    <div className="flex items-center justify-between">
                      <span className={`font-mono font-black ${msg.userName.includes('Você') ? 'text-rose-500' : 'text-zinc-350'}`}>
                        @{msg.userName}
                      </span>
                      <span className="text-[9px] text-zinc-650 font-mono">{msg.time}</span>
                    </div>
                    <p className="text-zinc-400 font-semibold">{msg.text}</p>
                  </div>
                ))}
                <div ref={chatEndRef} />
              </div>

              {/* Typing inputs */}
              <form onSubmit={handleSendMessage} className="p-2.5 border-t border-zinc-900 bg-zinc-950 flex gap-2">
                <input
                  type="text"
                  value={newMsg}
                  onChange={e => setNewMsg(e.target.value)}
                  placeholder="Envie uma mensagem..."
                  className="flex-grow bg-zinc-900 border border-zinc-850 outline-none text-xs rounded-lg p-2.5 text-zinc-150 focus:border-[#ef4444]"
                />
                <button
                  type="submit"
                  className="bg-[#ef4444] hover:bg-red-700 text-white p-2.5 rounded-lg transition shrink-0 cursor-pointer"
                >
                  <Send className="w-4 h-4" />
                </button>
              </form>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};
export default AoVivoPage;

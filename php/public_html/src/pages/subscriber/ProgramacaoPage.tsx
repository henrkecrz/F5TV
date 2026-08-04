import React, { useState } from 'react';
import { useData } from '../../context/DataContext';
import { LiveSchedule, Channel } from '../../types';
import { Calendar, Bell, Clock, Radio, Users, Smile, HelpCircle } from 'lucide-react';

export const ProgramacaoPage: React.FC = () => {
  const { liveSchedules, channels } = useData();
  const [activeChannelId, setActiveChannelId] = useState<string>('all');
  const [alertsSet, setAlertsSet] = useState<string[]>([]);

  // Simple reminder toast simulation
  const handleToggleAlert = (scheduleId: string, title: string) => {
    if (alertsSet.includes(scheduleId)) {
      setAlertsSet(prev => prev.filter(id => id !== scheduleId));
      alert(`Alerta cancelado para: "${title}"`);
    } else {
      setAlertsSet(prev => [...prev, scheduleId]);
      alert(`Definido! Você receberá um aviso 5 minutos antes do início de: "${title}"`);
    }
  };

  const filteredSchedules = liveSchedules.filter(ls => {
    if (activeChannelId === 'all') return true;
    return ls.channelId === activeChannelId;
  });

  const getChannelLogoText = (channelId: string) => {
    const ch = channels.find(c => c.id === channelId);
    return ch ? ch.logoText : 'F5';
  };

  const getChannelName = (channelId: string) => {
    const ch = channels.find(c => c.id === channelId);
    return ch ? ch.name : 'Emissora F5';
  };

  return (
    <div className="flex flex-col gap-6 text-white text-left font-sans select-none min-h-[85vh]">
      
      {/* Intro info header */}
      <div className="border-b border-zinc-900 pb-5">
        <span className="text-zinc-550 font-mono font-bold text-xs tracking-wider uppercase">PROGRAMAÇÃO INTEGRAL</span>
        <h1 className="text-3xl font-black tracking-tight mt-1">Grade de Programação</h1>
        <p className="text-zinc-550 text-xs mt-1">Agende alertas automáticos e não perca nenhum debate, notícia, ou jogo ao vivo.</p>
      </div>

      {channels.length === 0 ? (
        <div className="bg-zinc-950 border border-zinc-900 p-16 text-center text-zinc-650 rounded-2xl flex flex-col items-center gap-3">
          <HelpCircle className="w-8 h-8 text-zinc-800" />
          <p>Nenhuma grade de canais ativa carregada.</p>
        </div>
      ) : (
        <div className="flex flex-col gap-6">

          {/* Quick Filter channel selection list */}
          <div className="flex flex-wrap gap-2.5 items-center bg-zinc-950 p-3 border border-zinc-900 rounded-2xl">
            <span className="text-[10px] font-mono font-extrabold text-zinc-500 uppercase px-2">Canal:</span>
            <button
              onClick={() => setActiveChannelId('all')}
              className={`px-3.5 py-2 rounded-xl text-xs font-bold transition duration-200 cursor-pointer ${
                activeChannelId === 'all'
                  ? 'bg-red-650 text-white'
                  : 'bg-zinc-900 hover:bg-zinc-850 text-zinc-400 hover:text-white'
              }`}
            >
              Todos os Canais
            </button>
            {channels.map(c => (
              <button
                key={c.id}
                onClick={() => setActiveChannelId(c.id)}
                className={`px-3.5 py-2 rounded-xl text-xs font-bold transition duration-200 cursor-pointer ${
                  activeChannelId === c.id
                    ? 'bg-red-650 text-white'
                    : 'bg-zinc-900 hover:bg-zinc-850 text-zinc-400 hover:text-white'
                }`}
              >
                {c.name}
              </button>
            ))}
          </div>

          {/* Agenda Grid elements */}
          {filteredSchedules.length === 0 ? (
            <div className="bg-zinc-950 border border-zinc-900 p-16 text-center text-zinc-600 rounded-2xl">
              <p>Nenhum evento programado para o canal selecionado hoje.</p>
            </div>
          ) : (
            <div className="flex flex-col gap-4">
              {filteredSchedules
                .sort((a, b) => a.startTime.localeCompare(b.startTime))
                .map((ls) => {
                  const setForAlert = alertsSet.includes(ls.id);
                  const isLive = ls.status === 'live';
                  
                  return (
                    <div 
                      key={ls.id} 
                      className={`group border rounded-2xl p-5 flex flex-col md:flex-row md:items-center justify-between gap-5 transition duration-300 relative overflow-hidden ${
                        isLive 
                          ? 'bg-gradient-to-r from-red-950/15 to-zinc-950/20 border-red-900/40 shadow-lg shadow-red-950/5' 
                          : 'bg-zinc-950 border-zinc-900 hover:border-zinc-800'
                      }`}
                    >
                      <div className="flex items-start md:items-center gap-4 flex-1">
                        {/* Hour badge block */}
                        <div className="flex flex-col shrink-0 items-center justify-center p-3.5 bg-zinc-900 border border-zinc-850 rounded-xl min-w-[85px]">
                          <Clock className={`w-4 h-4 mb-2.5 ${isLive ? 'text-red-500 animate-pulse' : 'text-zinc-550'}`} />
                          <span className="font-mono font-black text-xs text-zinc-100">{ls.startTime}</span>
                          <span className="font-mono text-[9px] text-zinc-550 block mt-0.5">{ls.endTime}</span>
                        </div>

                        {/* Title block */}
                        <div className="flex flex-col gap-1.5 flex-1 min-w-0">
                          <div className="flex flex-wrap items-center gap-2">
                            <span className="text-[10px] font-mono tracking-widest font-black text-rose-500 uppercase bg-rose-950/30 border border-rose-900/30 px-2 py-0.5 rounded">
                              {getChannelLogoText(ls.channelId)}
                            </span>
                            {isLive && (
                              <span className="text-[9px] font-mono font-black text-white px-2 py-0.5 rounded bg-red-650 animate-pulse uppercase">
                                no ar agora
                              </span>
                            )}
                          </div>

                          <h3 className="text-lg font-black text-zinc-200 group-hover:text-white transition leading-snug">{ls.title}</h3>
                          {ls.description && <p className="text-xs text-zinc-500 leading-relaxed font-semibold line-clamp-2 max-w-2xl">{ls.description}</p>}
                          {ls.host && <span className="text-zinc-[450] font-mono text-[10px] font-bold uppercase">Âncora: {ls.host}</span>}
                        </div>
                      </div>

                      {/* Reminder and action tags column */}
                      <div className="flex items-center gap-3 justify-end shrink-0 md:border-l md:border-zinc-900 md:pl-6">
                        {!isLive && ls.status === 'scheduled' && (
                          <button
                            onClick={() => handleToggleAlert(ls.id, ls.title)}
                            className={`flex items-center gap-2 p-3 text-xs font-bold rounded-xl transition cursor-pointer select-none ${
                              setForAlert
                                ? 'bg-amber-950/50 text-amber-400 border border-amber-900'
                                : 'bg-zinc-900 hover:bg-zinc-850 text-zinc-400 border border-zinc-850 hover:text-white'
                            }`}
                          >
                            <Bell className={`w-4 h-4 ${setForAlert ? 'fill-amber-400 text-amber-400' : ''}`} />
                            <span className="font-mono font-bold uppercase text-[10px] tracking-wider">
                              {setForAlert ? 'Lembrete Ativo' : 'Lembrar 5min'}
                            </span>
                          </button>
                        )}
                        
                        {isLive && (
                          <a 
                            href="/app/ao-vivo"
                            className="bg-red-650 hover:bg-red-700 text-white font-mono font-bold uppercase text-[10px] tracking-wider px-4 py-3 rounded-xl transition text-center shadow shadow-red-950/20"
                          >
                            Assistir Ao Vivo
                          </a>
                        )}
                      </div>
                    </div>
                  );
                })}
            </div>
          )}
        </div>
      )}
    </div>
  );
};
export default ProgramacaoPage;

/**
 * @license
 * SPDX-License-Identifier: Apache-2.0
 */

import React from 'react';
import { Link } from 'react-router-dom';
import { resetDBToDefault } from '../data/mockDatabase';
import { RefreshCw } from 'lucide-react';

interface FooterProps {
  onSelectCredential?: (email: string) => void;
}

export default function Footer({ onSelectCredential }: FooterProps) {
  return (
    <footer id="f5-platform-footer" className="bg-[#050505] border-t border-white/5 py-16 px-8 font-sans">
      <div className="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-8">
        
        {/* Brand Column */}
        <div className="flex flex-col gap-4">
          <div className="flex items-center gap-2">
            <span className="text-2xl font-black tracking-tighter text-white uppercase">
              F5 <span className="text-red-650">TV</span>
            </span>
          </div>
          <p className="text-white/50 text-xs leading-relaxed max-w-xs">
            A plataforma premium de streaming e portal de conteúdos exclusivos da emissora F5 TV. Jornalismo tático, entretenimento, esportes e muito mais.
          </p>
          <span className="text-white/30 text-[10px] font-mono tracking-wider mt-4 block">
            © 2026 F5 TV Brasil. All rights reserved.
          </span>
        </div>

        {/* Link Column 1 */}
        <div className="flex flex-col gap-4">
          <h3 className="text-white font-mono tracking-[0.2em] uppercase text-[11px] font-bold">Navegação</h3>
          <ul className="flex flex-col gap-2.5 text-xs text-white/50">
            <li><a href="#hero" className="hover:text-white transition">Início</a></li>
            <li><a href="#beneficios" className="hover:text-white transition">Benefícios</a></li>
            <li><a href="#planos" className="hover:text-white transition">Planos de Assinatura</a></li>
            <li><a href="#faq" className="hover:text-white transition">Perguntas Frequentes</a></li>
          </ul>
        </div>

        {/* Link Column 2 */}
        <div className="flex flex-col gap-4">
          <h3 className="text-white font-mono tracking-[0.2em] uppercase text-[11px] font-bold">Institucional</h3>
          <ul className="flex flex-col gap-2.5 text-xs text-white/50">
            <li><Link to="/sobre" className="hover:text-white transition cursor-pointer">Sobre a F5 TV</Link></li>
            <li><Link to="/contato" className="hover:text-white transition cursor-pointer">Contato & Suporte</Link></li>
            <li><Link to="/termos" className="hover:text-white transition cursor-pointer">Termos de Serviço</Link></li>
            <li><Link to="/privacidade" className="hover:text-white transition cursor-pointer">Políticas de Privacidade</Link></li>
          </ul>
        </div>

        {/* Credentials Column */}
        <div className="flex flex-col gap-4">
          <h3 className="text-white font-mono tracking-[0.2em] uppercase text-[11px] font-bold">Ambiente de Demonstração</h3>
          <p className="text-white/50 text-xs leading-relaxed">
            Clique rápida nos e-mails abaixo na tela de login para simular acessos instantâneos com permissões táticas:
          </p>
          <div className="flex flex-col gap-2.5 text-xs bg-white/[0.02] border border-white/5 rounded-lg p-3.5">
            <div className="flex justify-between items-center text-white/60 text-[11px]">
              <span>Admin:</span>
              <button 
                onClick={() => onSelectCredential?.('admin@f5tv.com.br')} 
                className="text-red-650 hover:underline font-mono"
              >
                admin@f5tv.com.br
              </button>
            </div>
            <div className="flex justify-between items-center text-white/60 text-[11px]">
              <span>Editor:</span>
              <button 
                onClick={() => onSelectCredential?.('editor@f5tv.com.br')} 
                className="text-red-655 hover:underline font-mono"
              >
                editor@f5tv.com.br
              </button>
            </div>
            <div className="flex justify-between items-center text-white/60 text-[11px]">
              <span>Financeiro:</span>
              <button 
                onClick={() => onSelectCredential?.('financeiro@f5tv.com.br')} 
                className="text-red-655 hover:underline font-mono"
              >
                financeiro@f5tv.com.br
              </button>
            </div>
            <div className="flex justify-between items-center text-white/60 text-[11px]">
              <span>Assinante:</span>
              <button 
                onClick={() => onSelectCredential?.('henrikeaps@gmail.com')} 
                className="text-red-650 hover:underline font-mono"
              >
                henrikeaps@gmail.com
              </button>
            </div>
          </div>
          <div className="flex items-center justify-between mt-2">
            <button 
              onClick={resetDBToDefault} 
              className="text-white/40 hover:text-red-400 text-xs flex items-center gap-1.5 transition cursor-pointer"
              title="Apaga os dados salvos no localStorage e restaura os padrões"
            >
              <RefreshCw className="w-3 h-3" />
              <span>Resetar Banco de Dados</span>
            </button>
            <div className="flex items-center gap-1.5 text-[10px] text-zinc-550 font-mono">
              <span className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse inline-block" />
              <span>SP-01 REGIONAL OPERATIONAL</span>
            </div>
          </div>
        </div>

      </div>
    </footer>
  );
}

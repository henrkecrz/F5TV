import React, { useState } from 'react';
import { useData } from '../../context/DataContext';
import { useAuth } from '../../context/AuthContext';
import { ConnectedDevice } from '../../types';
import { Tv, Smartphone, Laptop, Trash2, Sparkles, MonitorSmartphone, ShieldCheck, AlertCircle } from 'lucide-react';

export const DispositivosPage: React.FC = () => {
  const { connectedDevices, updateConnectedDevices } = useData();
  const { currentUser } = useAuth();
  const [pairingCode, setPairingCode] = useState('');
  const [showPairingModal, setShowPairingModal] = useState(false);
  const [pinCode, setPinCode] = useState<string | null>(null);

  const handleDisconnect = (id: string, name: string) => {
    if (confirm(`Tem certeza que deseja revogar o acesso do dispositivo "${name}"? Ele será deslogado da F5 TV.`)) {
      const filtered = connectedDevices.filter(d => d.id !== id);
      updateConnectedDevices(filtered);
    }
  };

  const handleGeneratePin = () => {
    const generatedPin = Math.floor(100000 + Math.random() * 900000).toString();
    setPinCode(generatedPin);
  };

  const handlePair = (e: React.FormEvent) => {
    e.preventDefault();
    if (!pairingCode.trim()) return;

    const newDevice: ConnectedDevice = {
      id: `dev-${Date.now()}`,
      userId: currentUser?.id || 'sys-default',
      deviceName: 'Smart TV Living Room (Nova)',
      deviceType: 'smart_tv',
      lastActive: 'Ativo agora',
      location: 'São Paulo, BR',
      isActive: true
    };

    updateConnectedDevices([...connectedDevices, newDevice]);
    setPairingCode('');
    setShowPairingModal(false);
    setPinCode(null);
    alert('Smart TV emparelhada na sua assinatura F5 com sucesso!');
  };

  const getDeviceIcon = (type: ConnectedDevice['deviceType']) => {
    switch (type) {
      case 'smart_tv': return <Tv className="w-5 h-5 text-red-500" />;
      case 'mobile': return <Smartphone className="w-5 h-5 text-red-500" />;
      case 'desktop': return <Laptop className="w-5 h-5 text-red-500" />;
      case 'browser': return <Laptop className="w-5 h-5 text-red-500" />;
      default: return <Tv className="w-5 h-5 text-red-500" />;
    }
  };

  return (
    <div className="flex flex-col gap-6 text-white text-left font-sans select-none min-h-[85vh]">
      
      {/* Page Title */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between justify-start gap-4 border-b border-zinc-900 pb-5">
        <div>
          <span className="text-zinc-550 font-mono font-bold text-xs tracking-wider uppercase">LOGINS SEGURANÇA</span>
          <h1 className="text-3xl font-black tracking-tight mt-1">Gerenciar Dispositivos</h1>
          <p className="text-zinc-550 text-xs mt-1">Monitore e revogue sessões ativas na sua conta ou emparelhe novas telas.</p>
        </div>

        <button
          onClick={() => {
            handleGeneratePin();
            setShowPairingModal(true);
          }}
          className="flex items-center gap-2 bg-[#ef4444] hover:bg-red-700 text-white text-xs font-bold font-mono uppercase tracking-wider px-4 py-2.5 rounded-xl transition cursor-pointer"
        >
          <MonitorSmartphone className="w-4 h-4" />
          <span>Emparelhar TV</span>
        </button>
      </div>

      {/* Grid connected */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        {connectedDevices.length === 0 ? (
          <div className="col-span-full bg-zinc-950 border border-zinc-900 p-16 text-center text-zinc-600 rounded-2xl flex flex-col items-center justify-center gap-2">
            <AlertCircle className="w-8 h-8 text-zinc-850" />
            <p>Nenhum dispositivo conectado para esta conta.</p>
          </div>
        ) : (
          connectedDevices.map((dev) => (
            <div key={dev.id} className="bg-zinc-950 border border-zinc-900 rounded-2xl p-5 flex flex-col justify-between gap-5 relative group">
              <div className="absolute top-4 right-4 text-[9px] font-mono font-bold bg-[#09090b] text-zinc-650 p-1.5 border border-zinc-900 rounded-lg uppercase">
                {dev.deviceType}
              </div>

              <div className="flex items-start gap-4">
                <div className="p-3 bg-zinc-900 border border-zinc-850 rounded-xl shrink-0 self-center">
                  {getDeviceIcon(dev.deviceType)}
                </div>

                <div className="flex flex-col text-xs min-w-0">
                  <span className="font-extrabold text-white text-base truncate pr-10">{dev.deviceName}</span>
                  <span className="text-zinc-[450] font-semibold block mt-1">Último sinal: {dev.lastActive}</span>
                  <span className="text-zinc-650 font-mono text-[10px] block mt-1">{dev.location} • {dev.isActive ? 'Em Enlace Ativo' : 'Inativo'}</span>
                </div>
              </div>

              <div className="flex items-center justify-between border-t border-zinc-900 pt-3.5 mt-1">
                <span className="text-[10px] font-mono text-zinc-650 font-bold flex items-center gap-1.5 uppercase tracking-wide">
                  <ShieldCheck className="w-3.5 h-3.5 text-zinc-600" /> Criptografia SSL
                </span>

                <button
                  onClick={() => handleDisconnect(dev.id, dev.deviceName)}
                  className="p-2 hover:bg-zinc-900 rounded-lg text-zinc-500 hover:text-[#ef4444] transition cursor-pointer"
                  title="Desconectar Dispositivo"
                >
                  <Trash2 className="w-4 h-4" />
                </button>
              </div>
            </div>
          ))
        )}
      </div>

      {/* Pairing simulator modal overlay */}
      {showPairingModal && (
        <div id="device-pairing-modal" className="fixed inset-0 bg-black/80 backdrop-blur-xs z-50 flex items-center justify-center p-4">
          <div className="bg-zinc-950 border border-zinc-850 rounded-2xl w-full max-w-md overflow-hidden shadow-2xl relative p-6 font-sans text-white text-left selection:bg-red-650 select-none">
            <h3 className="text-lg font-black tracking-tight flex items-center gap-1.5 text-zinc-100">
              <Sparkles className="w-5 h-5 text-red-500" />
              Emparelhar Smart TV ou Console
            </h3>
            <p className="text-zinc-550 text-xs mt-1">Siga as instruções descritas abaixo na tela da sua TV.</p>

            <div className="my-5 bg-zinc-900 border border-zinc-850 p-5 rounded-2xl flex flex-col items-center gap-4 text-center">
              <span className="text-[10px] font-mono tracking-widest text-zinc-500 font-bold uppercase">PIN Code Gerado para TV</span>
              {pinCode && (
                <span className="font-mono text-3xl font-black text-rose-500 tracking-widest bg-[#0a0a0c] px-6 py-2 border border-zinc-800 rounded-xl">
                  {pinCode}
                </span>
              )}
              <span className="text-[11px] text-zinc-550 max-w-xs leading-relaxed font-semibold">Instale o APP F5 TV na sua Smart TV, abra a tela de login e digite o código acima no campo correspondente para autenticação direta.</span>
            </div>

            <form onSubmit={handlePair} className="flex flex-col gap-3.5">
              <div className="flex flex-col gap-1 text-xs">
                <label className="text-zinc-500 font-mono font-bold uppercase pb-1">Confirmar Código PIN na TV (Simulador de TV)</label>
                <input
                  type="text"
                  required
                  placeholder="Digite o código de 6 dígitos acima"
                  maxLength={6}
                  value={pairingCode}
                  onChange={e => setPairingCode(e.target.value)}
                  className="bg-zinc-900 border border-zinc-850 p-2.5 rounded text-center font-mono font-black text-lg text-white"
                />
              </div>

              <div className="flex gap-2 justify-end mt-4 border-t border-zinc-900 pt-4 text-xs font-mono font-bold">
                <button
                  type="button"
                  onClick={() => {
                    setShowPairingModal(false);
                    setPinCode(null);
                  }}
                  className="px-4 py-2 bg-zinc-900 hover:bg-zinc-850 text-zinc-450 hover:text-white rounded-lg transition text-xs uppercase cursor-pointer"
                >
                  Fechar
                </button>
                <button
                  type="submit"
                  className="px-5 py-2 bg-[#ef4444] hover:bg-red-700 text-white rounded-lg transition text-xs uppercase cursor-pointer"
                >
                  Ativar TV
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
};
export default DispositivosPage;

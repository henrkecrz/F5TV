import React, { useState, useEffect } from 'react';
import { useNavigate, useSearchParams } from 'react-router-dom';
import { useData } from '../../context/DataContext';
import { useAuth } from '../../context/AuthContext';
import { Plan, Coupon, Payment } from '../../types';
import { CreditCard, Ticket, Check, ShieldCheck, QrCode, ClipboardCheck, ArrowLeft, Loader2, AlertCircle } from 'lucide-react';

export const CheckoutPage: React.FC = () => {
  const navigate = useNavigate();
  const [searchParams] = useSearchParams();
  const { plans, coupons, updateCoupons, updatePayments, users, updateUsers } = useData();
  const { currentUser } = useAuth();
  
  const selectedPlanId = searchParams.get('plano') || 'plano-premium';
  const [selectedPlan, setSelectedPlan] = useState<Plan | null>(null);

  // Form states
  const [couponCode, setCouponCode] = useState('');
  const [appliedCoupon, setAppliedCoupon] = useState<Coupon | null>(null);
  const [couponError, setCouponError] = useState('');
  const [paymentMethod, setPaymentMethod] = useState<'card' | 'pix'>('card');
  const [isProcessing, setIsProcessing] = useState(false);

  // Input states
  const [cardName, setCardName] = useState('');
  const [cardNumber, setCardNumber] = useState('');
  const [cardExpiry, setCardExpiry] = useState('');
  const [cardCvv, setCardCvv] = useState('');
  const [cpf, setCpf] = useState('');

  // Find plan
  useEffect(() => {
    const matched = plans.find(p => p.id === selectedPlanId);
    if (matched) {
      setSelectedPlan(matched);
    } else if (plans && plans.length > 0) {
      setSelectedPlan(plans[0]);
    }
  }, [selectedPlanId, plans]);

  // Format Card Number input nicely
  const handleCardNumberChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const v = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
    const matches = v.match(/\d{4,16}/g);
    const match = (matches && matches[0]) || '';
    const parts = [];

    for (let i = 0, len = match.length; i < len; i += 4) {
      parts.push(match.substring(i, i + 4));
    }

    if (parts.length > 0) {
      setCardNumber(parts.join(' '));
    } else {
      setCardNumber(v);
    }
  };

  const handleApplyCoupon = (e: React.FormEvent) => {
    e.preventDefault();
    setCouponError('');
    if (!couponCode.trim()) return;

    const query = couponCode.toUpperCase().trim();
    const matched = coupons.find(c => c.code === query);

    if (!matched) {
      setCouponError('Cupom inválido ou não encontrado.');
      setAppliedCoupon(null);
      return;
    }

    if (matched.status !== 'active') {
      setCouponError('Este cupom promocional expirou ou está inativo.');
      setAppliedCoupon(null);
      return;
    }

    if (matched.usageCount >= matched.usageLimit) {
      setCouponError('Limite de utilizações deste cupom esgotado.');
      setAppliedCoupon(null);
      return;
    }

    if (selectedPlan && matched.applicablePlans && matched.applicablePlans.length > 0) {
      if (!matched.applicablePlans.includes(selectedPlan.id)) {
        setCouponError('Este cupom não se aplica ao plano selecionado.');
        setAppliedCoupon(null);
        return;
      }
    }

    // Success!
    setAppliedCoupon(matched);
    setCouponError('');
  };

  // Math totals
  const subtotal = selectedPlan ? selectedPlan.price : 0;
  let savings = 0;
  if (appliedCoupon && selectedPlan) {
    if (appliedCoupon.discountType === 'percent') {
      savings = subtotal * (appliedCoupon.discountValue / 100);
    } else {
      savings = Math.min(subtotal, appliedCoupon.discountValue);
    }
  }
  const total = Math.max(0, subtotal - savings);

  const handleSubmitCheckout = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!selectedPlan || !currentUser) return;

    setIsProcessing(true);

    // Simulate merchant delay
    await new Promise(resolve => setTimeout(resolve, 3000));

    // 1. Upgrade user in db
    const updatedUsers = users.map(u => {
      if (u.id === currentUser.id) {
        return {
          ...u,
          status: 'active' as const,
          planId: selectedPlan.id
        };
      }
      return u;
    });
    updateUsers(updatedUsers);

    // 2. Insert payment record
    const newPayment: Payment = {
      id: `pay-${Date.now()}`,
      userId: currentUser.id,
      subscriptionId: `sub-${selectedPlan.id}`,
      value: total,
      date: new Date().toISOString().split('T')[0],
      status: 'paid',
      paymentMethod: paymentMethod === 'card' ? 'credit_card' : 'pix'
    };
    updatePayments([newPayment]);

    // 3. Update coupon usage count if applied
    if (appliedCoupon) {
      const updatedCoupons = coupons.map(c => {
        if (c.id === appliedCoupon.id) {
          return { ...c, usageCount: c.usageCount + 1 };
        }
        return c;
      });
      updateCoupons(updatedCoupons);
    }

    setIsProcessing(false);
    navigate('/checkout/sucesso', { state: { planName: selectedPlan.name, valuePaid: total } });
  };

  if (!selectedPlan) {
    return (
      <div className="min-h-screen bg-[#09090b] text-white flex items-center justify-center p-6 text-xs font-sans">
        <Loader2 className="w-8 h-8 text-red-500 animate-spin" />
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-[#09090b] text-white font-sans text-left pb-16 pt-8 px-4 selection:bg-red-650 select-none">
      <div className="max-w-4xl mx-auto flex flex-col gap-6">
        
        {/* Back navigation link code */}
        <button
          onClick={() => navigate('/planos')}
          className="flex items-center gap-1.5 text-xs text-zinc-500 hover:text-white transition w-fit cursor-pointer font-bold uppercase font-mono"
        >
          <ArrowLeft className="w-4 h-4" />
          <span>Voltar para Planos</span>
        </button>

        <div className="grid grid-cols-1 md:grid-cols-5 gap-8">
          
          {/* Billing and Info checkout forms */}
          <div className="md:col-span-3 flex flex-col gap-6">
            <div className="bg-zinc-950 border border-zinc-900 p-6 rounded-2xl flex flex-col gap-4">
              <h2 className="text-xl font-black text-zinc-100 flex items-center gap-2">
                <ShieldCheck className="w-5 h-5 text-red-500" />
                Checkout de Assinatura
              </h2>
              <span className="text-zinc-[450] text-xs font-semibold leading-relaxed">
                Ambiente de teste simulado seguro SSL-SANDBOX. Nenhum saldo real será sacado do seu cartão bancário.
              </span>

              {/* Toggle credit pix cards */}
              <div className="grid grid-cols-2 gap-3 mt-1.5 p-1 bg-zinc-900 rounded-xl">
                <button
                  type="button"
                  onClick={() => setPaymentMethod('card')}
                  className={`flex items-center justify-center gap-2 py-3 rounded-lg text-xs font-bold font-mono uppercase tracking-wider cursor-pointer ${
                    paymentMethod === 'card' ? 'bg-[#ef4444] text-white' : 'text-zinc-500 hover:text-zinc-350'
                  }`}
                >
                  <CreditCard className="w-4 h-4" />
                  Cartão Crédito
                </button>
                <button
                  type="button"
                  onClick={() => setPaymentMethod('pix')}
                  className={`flex items-center justify-center gap-2 py-3 rounded-lg text-xs font-bold font-mono uppercase tracking-wider cursor-pointer ${
                    paymentMethod === 'pix' ? 'bg-[#ef4444] text-white' : 'text-zinc-500 hover:text-zinc-350'
                  }`}
                >
                  <QrCode className="w-4 h-4" />
                  PIX Instantâneo
                </button>
              </div>

              <form onSubmit={handleSubmitCheckout} className="flex flex-col gap-4 mt-2">
                {paymentMethod === 'card' ? (
                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div className="flex flex-col gap-1 text-xs sm:col-span-2">
                      <label className="text-zinc-[450] font-mono font-bold uppercase text-[10px]">Nome Impresso no Cartão</label>
                      <input
                        type="text"
                        required
                        value={cardName}
                        onChange={e => setCardName(e.target.value)}
                        placeholder="Ex: SANDRO OLIVEIRA COUTINHO"
                        className="bg-zinc-900 border border-zinc-850 p-2.5 rounded text-white outline-none"
                      />
                    </div>

                    <div className="flex flex-col gap-1 text-xs sm:col-span-2">
                      <label className="text-zinc-[450] font-mono font-bold uppercase text-[10px]">Número do Cartão</label>
                      <input
                        type="text"
                        required
                        maxLength={19}
                        value={cardNumber}
                        onChange={handleCardNumberChange}
                        placeholder="0000 0000 0000 0000"
                        className="bg-zinc-900 border border-zinc-850 p-2.5 rounded text-white outline-none font-mono"
                      />
                    </div>

                    <div className="flex flex-col gap-1 text-xs">
                      <label className="text-zinc-[450] font-mono font-bold uppercase text-[10px]">Validade (MM/AA)</label>
                      <input
                        type="text"
                        required
                        maxLength={5}
                        value={cardExpiry}
                        onChange={e => setCardExpiry(e.target.value)}
                        placeholder="11/30"
                        className="bg-zinc-900 border border-zinc-850 p-2.5 rounded text-white outline-none font-mono text-center"
                      />
                    </div>

                    <div className="flex flex-col gap-1 text-xs">
                      <label className="text-zinc-[450] font-mono font-bold uppercase text-[10px]">Cód. Segurança CVV</label>
                      <input
                        type="password"
                        required
                        maxLength={4}
                        value={cardCvv}
                        onChange={e => setCardCvv(e.target.value)}
                        placeholder="000"
                        className="bg-zinc-900 border border-zinc-850 p-2.5 rounded text-white outline-none font-mono text-center"
                      />
                    </div>

                    <div className="flex flex-col gap-1 text-xs sm:col-span-2">
                      <label className="text-zinc-[450] font-mono font-bold uppercase text-[10px]">CPF do Titular</label>
                      <input
                        type="text"
                        required
                        value={cpf}
                        onChange={e => setCpf(e.target.value)}
                        placeholder="000.000.000-00"
                        className="bg-zinc-900 border border-zinc-850 p-2.5 rounded text-white outline-none font-mono"
                      />
                    </div>
                  </div>
                ) : (
                  <div className="bg-zinc-900 border border-zinc-850 p-5 rounded-xl flex flex-col items-center gap-4 text-center">
                    <QrCode className="w-24 h-24 text-red-500 animate-pulse bg-white p-2.5 rounded-lg" />
                    <span className="text-[10px] font-mono tracking-widest text-zinc-500 font-bold uppercase">QRCode do PIX Gerado</span>
                    <span className="font-mono text-[9px] bg-[#09090b] border border-zinc-850 p-2.5 rounded-lg text-zinc-400 select-all font-semibold max-w-sm truncate block mt-0.5">
                      00020126580014br.gov.bcb.pix0136f5tv-key-offline-sandbox-mvp-receipt-99
                    </span>
                    <div className="flex flex-col gap-1 text-xs">
                      <label className="text-zinc-[450] font-mono font-bold uppercase text-[10px]">Informe um CPF para emissão da chave Pix</label>
                      <input
                        type="text"
                        required
                        value={cpf}
                        onChange={e => setCpf(e.target.value)}
                        placeholder="000.000.000-00"
                        className="bg-[#09090b] border border-zinc-850 p-2.5 rounded text-white outline-none font-mono text-center max-w-[250px]"
                      />
                    </div>
                  </div>
                )}

                <button
                  type="submit"
                  disabled={isProcessing}
                  className="w-full bg-[#ef4444] hover:bg-red-700 disabled:bg-zinc-800 text-white font-mono font-extrabold text-xs uppercase tracking-widest py-3.5 mt-2 rounded-xl transition flex items-center justify-center gap-2 cursor-pointer shadow-lg shadow-red-950/10"
                >
                  {isProcessing ? (
                    <>
                      <Loader2 className="w-4 h-4 animate-spin text-white" />
                      <span>Processando Assinatura...</span>
                    </>
                  ) : (
                    <>
                      <span>Inscrever-se no Plano</span>
                    </>
                  )}
                </button>
              </form>
            </div>
          </div>

          {/* Right column: Order summaries and coupons */}
          <div className="md:col-span-2 flex flex-col gap-6">
            
            {/* Box summary */}
            <div className="bg-zinc-950 border border-zinc-900 p-6 rounded-2xl flex flex-col gap-4">
              <h3 className="font-bold uppercase font-mono tracking-wider text-xs text-zinc-500">Resumo da Compra</h3>
              
              <div className="flex justify-between items-center text-xs">
                <div className="flex flex-col gap-0.5">
                  <span className="font-black text-white text-base">{selectedPlan.name}</span>
                  <span className="text-zinc-500 font-mono text-[10px]">Acesso ao vivo + gravados</span>
                </div>
                <span className="font-mono font-black text-zinc-200">R$ {subtotal.toFixed(2)}/mês</span>
              </div>

              {/* Coupon forms */}
              <div className="border-t border-zinc-900 pt-4 mt-2">
                <form onSubmit={handleApplyCoupon} className="flex gap-2">
                  <input
                    type="text"
                    value={couponCode}
                    onChange={e => setCouponCode(e.target.value)}
                    placeholder="Código Cupom"
                    className="flex-grow bg-zinc-900 border border-zinc-850 p-2 text-xs rounded-lg font-mono text-zinc-150 outline-none uppercase"
                  />
                  <button
                    type="submit"
                    className="bg-zinc-900 hover:bg-zinc-850 text-zinc-300 hover:text-white border border-zinc-850 px-3 rounded-lg text-xs font-mono font-bold uppercase transition shrink-0 cursor-pointer"
                  >
                    Aplicar
                  </button>
                </form>

                {couponError && (
                  <span className="text-rose-500 font-mono text-[10px] mt-2 block font-semibold">{couponError}</span>
                )}
                {appliedCoupon && (
                  <div className="flex items-center gap-1 text-green-500 text-[10px] font-mono font-bold uppercase tracking-wider mt-2.5">
                    <Check className="w-3.5 h-3.5 shrink-0" />
                    <span>Cupom {appliedCoupon.code} aplicado!</span>
                  </div>
                )}
              </div>

              {/* Calculation table */}
              <div className="border-t border-zinc-900 pt-4 flex flex-col gap-2.5 text-xs text-zinc-400">
                <div className="flex justify-between font-semibold">
                  <span>Subtotal</span>
                  <span className="font-mono">R$ {subtotal.toFixed(2)}</span>
                </div>
                
                {savings > 0 && (
                  <div className="flex justify-between font-bold text-green-500">
                    <span>Desconto Aplicado</span>
                    <span className="font-mono">- R$ {savings.toFixed(2)}</span>
                  </div>
                )}

                <div className="border-t border-zinc-900 pt-3.5 flex justify-between items-center text-white">
                  <span className="font-extrabold text-sm">Valor Total</span>
                  <span className="font-mono font-black text-rose-500 text-lg">R$ {total.toFixed(2)}</span>
                </div>
              </div>
            </div>
            
            {/* Guarantee safe tags */}
            <div className="flex items-center gap-3 p-4 bg-zinc-950/40 border border-zinc-900 rounded-xl text-zinc-500 text-xs">
              <ShieldCheck className="w-5 h-5 text-green-500 shrink-0" />
              <span className="leading-snug">Sua compra é totalmente segura. Garantia de reembolso de 7 dias incondicional.</span>
            </div>
          </div>

        </div>
      </div>
    </div>
  );
};
export default CheckoutPage;

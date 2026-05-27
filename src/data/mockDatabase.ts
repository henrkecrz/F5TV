/**
 * @license
 * SPDX-License-Identifier: Apache-2.0
 */

import { 
  User, Profile, Plan, Subscription, Payment, Content, Category, 
  Series, Season, Episode, Upload, WatchHistory, Favorite, Notification, Review,
  Coupon, Channel, LiveSchedule, ConnectedDevice
} from '../types';
import cardConexaoF5 from './cards/Conexao F5.png';
import cardExplorandoEstudios from './cards/Explorando os Novos Estudios F5.png';
import cardF5EntrevistaEdicaoExclusiva from './cards/F5 Entrevista - Edicao Exclusiva.png';
import cardF5EntrevistaGrandesIdeias from './cards/F5 Entrevista - Grandes Ideias e Inovacao.png';
import cardF5TechDocs from './cards/F5 Tech Docs - Fronteira da IA.png';
import cardMaratonaIA from './cards/F5 TV - Maratona Fronteiras da IA.png';
import cardMenteSilicio from './cards/F5 TV - Sua Mente em Silicio.png';
import cardInfiltracaoDarknet from './cards/Infiltracao na Darknet.png';
import cardRastreadoresCriminais from './cards/Rastreadores Criminais.png';
import cardRotaTrafico from './cards/TV F5 - A Rota do Trafico de Armas.png';
import cardAventuraTurma from './cards/TV F5 - Aventura da Turma.png';
import cardBastidoresCidade from './cards/TV F5 - Bastidores da Cidade.png';
import cardCidadesSustentaveis from './cards/TV F5 - Cidades Sustentaveis.png';
import cardCienciaAventura from './cards/TV F5 - Ciencia e Aventura.png';
import cardGravidade from './cards/TV F5 - Gravidade - O Misterio da Gravidade.png';
import cardHackersVsEstado from './cards/TV F5 - Hackers vs Estado.png';
import cardJornalAoVivo from './cards/TV F5 - Jornal F5 Edicao Ao Vivo.png';
import cardJornalDoDia from './cards/TV F5 - Jornal F5 Edicao do Dia.png';
import cardJornalPrimeiraEdicao from './cards/TV F5 - Jornal F5 Primeira Edicao.png';
import cardNoiteF5AcusticaContraste from './cards/TV F5 - Noite F5 Acustica - MPB em Alto Contraste.png';
import cardNoiteF5Acustica from './cards/TV F5 - Noite F5 Acustica.png';
import cardRansomware from './cards/TV F5 - Ransomware Detectado.png';
import cardResenhaEsportes from './cards/TV F5 - Resenha F5 Esportes.png';
import cardSubterraneoSP from './cards/TV F5 - Subterraneo de Sao Paulo.png';
import cardSupercopaBase from './cards/TV F5 - Supercopa F5 Base.png';
import cardSupercopaPaulista from './cards/TV F5 - Supercopa Paulista.png';

const PROGRAM_TV_COVERS: string[] = [
  cardConexaoF5,
  cardExplorandoEstudios,
  cardF5EntrevistaEdicaoExclusiva,
  cardF5EntrevistaGrandesIdeias,
  cardF5TechDocs,
  cardMaratonaIA,
  cardMenteSilicio,
  cardInfiltracaoDarknet,
  cardRastreadoresCriminais,
  cardRotaTrafico,
  cardAventuraTurma,
  cardBastidoresCidade,
  cardCidadesSustentaveis,
  cardCienciaAventura,
  cardGravidade,
  cardHackersVsEstado,
  cardJornalAoVivo,
  cardJornalDoDia,
  cardJornalPrimeiraEdicao,
  cardNoiteF5AcusticaContraste,
  cardNoiteF5Acustica,
  cardRansomware,
  cardResenhaEsportes,
  cardSubterraneoSP,
  cardSupercopaBase,
  cardSupercopaPaulista
];

// Seed initial plans
const INITIAL_PLANS: Plan[] = [
  {
    id: 'plano-basico',
    name: 'Plano Básico',
    price: 19.90,
    features: ['Acesso em 1 tela', 'Resolução HD (720p)', 'Com anúncios pontuais', 'Mais de 1.000 horas de conteúdo'],
    active: true
  },
  {
    id: 'plano-familia',
    name: 'Plano Família',
    price: 29.90,
    features: ['Acesso em 3 telas simultâneas', 'Resolução Full HD (1080p)', 'Sem anúncios', 'Suporte multi-perfil (até 4 perfis)', 'Downloads para assistir offline'],
    active: true
  },
  {
    id: 'plano-premium',
    name: 'Plano Premium',
    price: 44.90,
    features: ['Acesso em 5 telas simultâneas', 'Resolução Ultra HD 4K & HDR', 'Sem anúncios', 'Áudio Espacial Dolby Atmos', 'Suporte multi-perfil (até 6 perfis)', 'Downloads ilimitados'],
    active: true
  }
];

// Seed initial categories
const INITIAL_CATEGORIES: Category[] = [
  { id: 'cat-aovivo', name: 'Ao Vivo', slug: 'ao-vivo' },
  { id: 'cat-jornalismo', name: 'Jornalismo', slug: 'jornalismo' },
  { id: 'cat-series', name: 'Séries', slug: 'series' },
  { id: 'cat-programastv', name: 'Programas de TV', slug: 'programastv' },
  { id: 'cat-esportes', name: 'Esportes', slug: 'esportes' },
  { id: 'cat-documentarios', name: 'Documentários', slug: 'documentarios' },
  { id: 'cat-entretenimento', name: 'Entretenimento', slug: 'entretenimento' },
  { id: 'cat-infantil', name: 'Infantil', slug: 'infantil' },
  { id: 'cat-bastidores', name: 'Bastidores', slug: 'bastidores' },
  { id: 'cat-especiais', name: 'Especiais F5 TV', slug: 'especiais' }
];

// Seed users containing different roles (admin, editor, finance, subscriber)
const INITIAL_USERS: User[] = [
  {
    id: 'user-admin',
    name: 'Henrique Administrador',
    email: 'admin@f5tv.com.br',
    phone: '(11) 98765-4321',
    role: 'admin',
    status: 'active',
    createdAt: '2026-01-10T14:30:00Z',
    lastLogin: '2026-05-27T01:15:00Z'
  },
  {
    id: 'user-editor',
    name: 'Carolina Editora',
    email: 'editor@f5tv.com.br',
    phone: '(21) 97654-3210',
    role: 'editor',
    status: 'active',
    createdAt: '2026-02-15T09:00:00Z',
    lastLogin: '2026-05-26T18:45:00Z'
  },
  {
    id: 'user-finance',
    name: 'Rodrigo Financeiro',
    email: 'financeiro@f5tv.com.br',
    phone: '(11) 99887-7665',
    role: 'finance',
    status: 'active',
    createdAt: '2026-03-01T10:15:00Z',
    lastLogin: '2026-05-27T02:00:00Z'
  },
  {
    id: 'user-normal1',
    name: 'Gisele Assinante',
    email: 'henrikeaps@gmail.com', // Setting user email as subscriber or standard
    phone: '(19) 99123-4567',
    role: 'subscriber',
    planId: 'plano-premium',
    status: 'active',
    createdAt: '2026-04-01T11:00:00Z',
    lastLogin: '2026-05-27T02:10:00Z'
  },
  {
    id: 'user-normal2',
    name: 'Arthur Souza',
    email: 'arthur@gmail.com',
    phone: '(11) 95555-1234',
    role: 'subscriber',
    planId: 'plano-basico',
    status: 'active',
    createdAt: '2026-04-12T08:30:00Z',
    lastLogin: '2026-05-25T20:11:00Z'
  },
  {
    id: 'user-inadimplente',
    name: 'Marcos Inadimplente',
    email: 'marcos@bol.com.br',
    phone: '(31) 98888-9999',
    role: 'subscriber',
    planId: 'plano-familia',
    status: 'pending', // Pending/Overdue
    createdAt: '2026-02-20T16:22:00Z',
    lastLogin: '2026-05-15T10:40:00Z'
  },
  {
    id: 'user-bloqueado',
    name: 'Roberto Bloqueado',
    email: 'roberto@yahoo.com',
    phone: '(81) 97777-6666',
    role: 'subscriber',
    planId: 'plano-basico',
    status: 'blocked',
    createdAt: '2026-01-05T09:12:00Z',
    lastLogin: '2026-03-01T22:30:00Z'
  }
];

// Seed default profiles for subscription accounts
const INITIAL_PROFILES: Profile[] = [
  { id: 'prof-1', userId: 'user-normal1', name: 'Gisele Principal', avatarColor: 'bg-red-600' },
  { id: 'prof-2', userId: 'user-normal1', name: 'Kids F5', avatarColor: 'bg-emerald-600' },
  { id: 'prof-3', userId: 'user-normal1', name: 'Julio (Amigo)', avatarColor: 'bg-indigo-600' },
  { id: 'prof-4', userId: 'user-normal2', name: 'Arthur', avatarColor: 'bg-amber-600' }
];

// Seed initial subscriptions for subscribers
const INITIAL_SUBSCRIPTIONS: Subscription[] = [
  {
    id: 'sub-normal1',
    userId: 'user-normal1',
    planId: 'plano-premium',
    status: 'active',
    startDate: '2026-04-01',
    nextBillingDate: '2026-06-01',
    paymentMethod: 'credit_card'
  },
  {
    id: 'sub-normal2',
    userId: 'user-normal2',
    planId: 'plano-basico',
    status: 'active',
    startDate: '2026-04-12',
    nextBillingDate: '2026-06-12',
    paymentMethod: 'pix'
  },
  {
    id: 'sub-inadimplente',
    userId: 'user-inadimplente',
    planId: 'plano-familia',
    status: 'unpaid',
    startDate: '2026-02-20',
    nextBillingDate: '2026-05-20', // Overdue
    paymentMethod: 'boleto'
  },
  {
    id: 'sub-bloqueado',
    userId: 'user-bloqueado',
    planId: 'plano-basico',
    status: 'canceled',
    startDate: '2026-01-05',
    nextBillingDate: '2026-02-05',
    paymentMethod: 'credit_card'
  }
];

// Seed initial Payments
const INITIAL_PAYMENTS: Payment[] = [
  {
    id: 'pay-001',
    userId: 'user-normal1',
    subscriptionId: 'sub-normal1',
    value: 44.90,
    date: '2026-04-01',
    status: 'paid',
    paymentMethod: 'credit_card'
  },
  {
    id: 'pay-002',
    userId: 'user-normal1',
    subscriptionId: 'sub-normal1',
    value: 44.90,
    date: '2026-05-01',
    status: 'paid',
    paymentMethod: 'credit_card'
  },
  {
    id: 'pay-003',
    userId: 'user-normal2',
    subscriptionId: 'sub-normal2',
    value: 19.90,
    date: '2026-04-12',
    status: 'paid',
    paymentMethod: 'pix'
  },
  {
    id: 'pay-004',
    userId: 'user-normal2',
    subscriptionId: 'sub-normal2',
    value: 19.90,
    date: '2026-05-12',
    status: 'paid',
    paymentMethod: 'pix'
  },
  {
    id: 'pay-005',
    userId: 'user-inadimplente',
    subscriptionId: 'sub-inadimplente',
    value: 29.90,
    date: '2026-02-20',
    status: 'paid',
    paymentMethod: 'boleto'
  },
  {
    id: 'pay-006',
    userId: 'user-inadimplente',
    subscriptionId: 'sub-inadimplente',
    value: 29.90,
    date: '2026-03-20',
    status: 'paid',
    paymentMethod: 'boleto'
  },
  {
    id: 'pay-007',
    userId: 'user-inadimplente',
    subscriptionId: 'sub-inadimplente',
    value: 29.90,
    date: '2026-04-20',
    status: 'paid',
    paymentMethod: 'boleto'
  },
  {
    id: 'pay-008',
    userId: 'user-inadimplente',
    subscriptionId: 'sub-inadimplente',
    value: 29.90,
    date: '2026-05-20',
    status: 'overdue', // overdue payment
    paymentMethod: 'boleto'
  },
  {
    id: 'pay-009',
    userId: 'user-bloqueado',
    subscriptionId: 'sub-bloqueado',
    value: 19.90,
    date: '2026-01-05',
    status: 'paid',
    paymentMethod: 'credit_card'
  }
];

// Seed Initial Content Series
const INITIAL_SERIES: Series[] = [
  {
    id: 'series-conexao-f5',
    title: 'Conexão F5',
    description: 'A série jornalística investigativa que desvenda os grandes mistérios e as maiores fraudes tecnológicas do Brasil. Entrevistas profundas, infiltrações e dados exclusivos.',
    coverUrl: 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?q=80&w=400',
    bannerUrl: 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=1200',
    genre: 'Jornalismo Investigativo',
    status: 'published',
    viewsCount: 12540
  },
  {
    id: 'series-mundo-kids',
    title: 'Mundo F5 Kids',
    description: 'Animações educativas divertidas, jogos cognitivos interativos e muita música alegre para acelerar o desenvolvimento criativo das crianças da nova geração.',
    coverUrl: 'https://images.unsplash.com/photo-1485546246426-74dc88dec4d9?q=80&w=400',
    bannerUrl: 'https://images.unsplash.com/photo-1502086223501-7ea6ecd79368?q=80&w=1200',
    genre: 'Infantil / Educativo',
    status: 'published',
    viewsCount: 8430
  },
  {
    id: 'series-bastidores',
    title: 'Bastidores da Cidade',
    description: 'Os segredos das maiores metrópoles brasileiras. A vida noturna, a infraestrutura invisível dos metrôs, os túneis centenários e o trabalho dos profissionais que mantêm tudo de pé.',
    coverUrl: 'https://images.unsplash.com/photo-1514565131-fce0801e5785?q=80&w=400',
    bannerUrl: 'https://images.unsplash.com/photo-1477959858617-67f85cf4f1df?q=80&w=1200',
    genre: 'Urbanismo & Sociedade',
    status: 'published',
    viewsCount: 9320
  },
  {
    id: 'series-rastreadores',
    title: 'Rastreadores Criminais',
    description: 'Acompanhe de perto as operações táticas da polícia civil e federal brasileira no combate a crimes organizados de colarinho branco e facções de fronteiras.',
    coverUrl: 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?q=80&w=400&auto=format&fit=crop',
    bannerUrl: 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?q=80&w=1200',
    genre: 'Ação & Investigação Policial',
    status: 'published',
    viewsCount: 16500
  },
  {
    id: 'series-fronteira-ia',
    title: 'F5 Tech Docs: Fronteira da I.A.',
    description: 'Uma expedição pelos laboratórios de ponta no Brasil e Vale do Silício para entender a inteligência artificial, bioengenharia, computação quântica e o impacto no desemprego tecnológico.',
    coverUrl: 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?q=80&w=400',
    bannerUrl: 'https://images.unsplash.com/photo-1518770660439-4636190af475?q=80&w=1200',
    genre: 'Tecnologia & Futuro',
    status: 'published',
    viewsCount: 21900
  },
  {
    id: 'series-pequenos-exploradores',
    title: 'Pequenos Exploradores',
    description: 'Como funciona um vulcão? Por que chove? O que é gravidade? Aventuras animadas de forma lúdica em 3D, respondendo às maiores dúvidas de mentes mirins brilhantes.',
    coverUrl: 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?q=80&w=400',
    bannerUrl: 'https://images.unsplash.com/photo-1516627145497-ae6968895b74?q=80&w=1200',
    genre: 'Infantil / Educação Científica',
    status: 'published',
    viewsCount: 11050
  }
];

// Seed seasons for the series
const INITIAL_SEASONS: Season[] = [
  { id: 'season-conexao-s1', seriesId: 'series-conexao-f5', number: 1, title: 'Temporada 1: Ciber-ameaças', status: 'published' },
  { id: 'season-conexao-s2', seriesId: 'series-conexao-f5', number: 2, title: 'Temporada 2: Crimes Ambientais', status: 'published' },
  { id: 'season-kids-s1', seriesId: 'series-mundo-kids', number: 1, title: 'Temporada 1: Letras e Cores', status: 'published' },
  { id: 'season-bastidores-s1', seriesId: 'series-bastidores', number: 1, title: 'Temporada 1: O Coração de Ferro', status: 'published' },
  { id: 'season-rastreadores-s1', seriesId: 'series-rastreadores', number: 1, title: 'Temporada 1: Na Linha de Frente', status: 'published' },
  { id: 'season-fronteira-s1', seriesId: 'series-fronteira-ia', number: 1, title: 'Temporada 1: A Nova Era Cognitiva', status: 'published' },
  { id: 'season-exploradores-s1', seriesId: 'series-pequenos-exploradores', number: 1, title: 'Temporada 1: Mistérios da Ciência', status: 'published' }
];

// Seed episodes
const INITIAL_EPISODES: Episode[] = [
  // Conexão F5 S1
  {
    id: 'ep-conexao-1',
    seasonId: 'season-conexao-s1',
    number: 1,
    title: 'O Golpe do Pix Reverso',
    description: 'Uma investigação profunda sobre quadrilhas virtuais que usam falsas promessas de cupons de reembolso para extrair saldos bancários de idosos de forma instantânea.',
    duration: '45m',
    videoUrl: 'https://assets.mixkit.co/videos/preview/mixkit-software-developer-working-on-his-computer-34289-large.mp4',
    thumbnailUrl: 'https://images.unsplash.com/photo-1563986768609-322da13575f3?q=80&w=450',
    status: 'published',
    viewsCount: 4320
  },
  {
    id: 'ep-conexao-2',
    seasonId: 'season-conexao-s1',
    number: 2,
    title: 'Infiltração na Darknet',
    description: 'Nossa equipe acompanha especialistas em cibersegurança tática que entram no submundo dos fóruns criptografados para localizar e desativar leilões de dados pessoais.',
    duration: '50m',
    videoUrl: 'https://assets.mixkit.co/videos/preview/mixkit-matrix-style-code-scrolling-on-a-screen-23374-large.mp4',
    thumbnailUrl: 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?q=80&w=450',
    status: 'published',
    viewsCount: 3810
  },
  {
    id: 'ep-conexao-3',
    seasonId: 'season-conexao-s1',
    number: 3,
    title: 'A Ira do Ransomware',
    description: 'Como uma prefeitura do interior paulista foi sequestrada digitalmente, paralisando hospitais, escolas e serviços públicos sob exigência de criptomoedas.',
    duration: '48m',
    videoUrl: 'https://assets.mixkit.co/videos/preview/mixkit-hands-of-a-hacker-typing-code-on-a-keyboard-34293-large.mp4',
    thumbnailUrl: 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?q=80&w=450',
    status: 'published',
    viewsCount: 3120
  },
  // Conexão F5 S2
  {
    id: 'ep-conexao-s2-1',
    seasonId: 'season-conexao-s2',
    number: 1,
    title: 'Mercadores do Fogo',
    description: 'Satélites apontam queimadas coordenadas em áreas de preservação na Amazônia Legal. Investigamos os laranjas que lucram milhões com gado clandestino.',
    duration: '52m',
    videoUrl: 'https://assets.mixkit.co/videos/preview/mixkit-forest-fire-burning-at-night-42284-large.mp4',
    thumbnailUrl: 'https://images.unsplash.com/photo-1508697014387-db70afd36b6a?q=80&w=450',
    status: 'published',
    viewsCount: 1290
  },
  // Mundo Kids
  {
    id: 'ep-kids-1',
    seasonId: 'season-kids-s1',
    number: 1,
    title: 'A Dança dos Animais Coloridos',
    description: 'Aprenda os nomes das cores e os sons de animais divertidos como o elefante gigante, o leão amigável e o passarinho cantador com rimas mágicas.',
    duration: '15m',
    videoUrl: 'https://assets.mixkit.co/videos/preview/mixkit-happy-toys-spinning-on-a-baby-crib-40156-large.mp4',
    thumbnailUrl: 'https://images.unsplash.com/photo-1515488042361-404e9250afef?q=80&w=450',
    status: 'published',
    viewsCount: 5100
  },
  {
    id: 'ep-kids-2',
    seasonId: 'season-kids-s1',
    number: 2,
    title: 'Aventura Ecológica da Turma F5',
    description: 'Mel e Leo ensinam a importância de colocar o plástico na lixeira vermelha, o vidro na verde e o papel na azul para plantar uma macieira no clubinho.',
    duration: '18m',
    videoUrl: 'https://assets.mixkit.co/videos/preview/mixkit-children-running-together-in-the-garden-lightly-blurred-40154-large.mp4',
    thumbnailUrl: 'https://images.unsplash.com/photo-1502086223501-7ea6ecd79368?q=80&w=450',
    status: 'published',
    viewsCount: 3330
  },
  // Bastidores
  {
    id: 'ep-bast-1',
    seasonId: 'season-bastidores-s1',
    number: 1,
    title: 'Metrô Subterrâneo de São Paulo',
    description: 'Documentário inédito que caminha pelos trilhos escuros à meia-noite, revelando a rotina de manutenção dos trens de alta velocidade subterrâneos.',
    duration: '42m',
    videoUrl: 'https://assets.mixkit.co/videos/preview/mixkit-futuristic-time-lapse-of-subway-station-lights-and-people-41002-large.mp4',
    thumbnailUrl: 'https://images.unsplash.com/photo-1477959858617-67f85cf4f1df?q=80&w=450',
    status: 'published',
    viewsCount: 9320
  },
  // Rastreadores Criminais
  {
    id: 'ep-rastro-1',
    seasonId: 'season-rastreadores-s1',
    number: 1,
    title: 'A Rota do Tráfico de Armas',
    description: 'Acompanhe investigadores do CORE em uma emboscada na divisa do Estado do Rio para interceptar cargas secretas de fuzis e munições trazidos de avião.',
    duration: '46m',
    videoUrl: 'https://assets.mixkit.co/videos/preview/mixkit-software-developer-working-on-his-computer-34289-large.mp4',
    thumbnailUrl: 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?q=80&w=450',
    status: 'published',
    viewsCount: 5200
  },
  {
    id: 'ep-rastro-2',
    seasonId: 'season-rastreadores-s1',
    number: 2,
    title: 'O Rei do Colarinho Branco',
    description: 'As escutas táticas autorizadas pela Justiça Federal que derrubaram a rede de postos de gasolina que lavava mais de R$ 300 milhões por mês.',
    duration: '50m',
    videoUrl: 'https://assets.mixkit.co/videos/preview/mixkit-matrix-style-code-scrolling-on-a-screen-23374-large.mp4',
    thumbnailUrl: 'https://images.unsplash.com/photo-1507679799987-c73779587ccf?q=80&w=450',
    status: 'published',
    viewsCount: 4100
  },
  // Fronteira da I.A.
  {
    id: 'ep-front-1',
    seasonId: 'season-fronteira-s1',
    number: 1,
    title: 'Sua Mente em Silício',
    description: 'Mapeando as redes neurais artificiais de grandes modelos de linguagem brasileiras. Como a máquina aprende a simular empatia sob trilhas computacionais.',
    duration: '52m',
    videoUrl: 'https://assets.mixkit.co/videos/preview/mixkit-developer-working-on-different-screens-simultaneously-34290-large.mp4',
    thumbnailUrl: 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?q=80&w=450',
    status: 'published',
    viewsCount: 6300
  },
  {
    id: 'ep-front-2',
    seasonId: 'season-fronteira-s1',
    number: 2,
    title: 'A Bio-Impressora 3D de Órgãos',
    description: 'Viajamos ao laboratório da USP onde cientistas imprimem válvulas cardíacas vivas usando células-tronco de pacientes reais.',
    duration: '45m',
    videoUrl: 'https://assets.mixkit.co/videos/preview/mixkit-hands-of-a-hacker-typing-code-on-a-keyboard-34293-large.mp4',
    thumbnailUrl: 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?q=80&w=450',
    status: 'published',
    viewsCount: 5120
  },
  // Pequenos Exploradores
  {
    id: 'ep-expl-1',
    seasonId: 'season-exploradores-s1',
    number: 1,
    title: 'De Onde Vem a Chuva?',
    description: 'Guto e Bel embarcam em uma viagem mágica como gotinhas de água que flutuam até o céu para descobrir o ciclo das nuvens.',
    duration: '18m',
    videoUrl: 'https://assets.mixkit.co/videos/preview/mixkit-happy-toys-spinning-on-a-baby-crib-40156-large.mp4',
    thumbnailUrl: 'https://images.unsplash.com/photo-1534274988757-a28bf1a57c17?q=80&w=450',
    status: 'published',
    viewsCount: 3800
  },
  {
    id: 'ep-expl-2',
    seasonId: 'season-exploradores-s1',
    number: 2,
    title: 'O Mistério da Gravidade',
    description: 'Por que tudo o que sobe cai no chão? Uma deliciosa brincadeira de pular corda e lançar maçãs explica como a Terra nos puxa gentilmente.',
    duration: '19m',
    videoUrl: 'https://assets.mixkit.co/videos/preview/mixkit-children-running-together-in-the-garden-lightly-blurred-40154-large.mp4',
    thumbnailUrl: 'https://images.unsplash.com/photo-1485546246426-74dc88dec4d9?q=80&w=450',
    status: 'published',
    viewsCount: 4200
  }
];

// Seed initial general metadata movie contents (films, live events, standalones etc)
const OLD_INITIAL_CONTENTS: Content[] = [
  {
    id: 'content-series-conexao',
    type: 'series',
    title: 'Conexão F5',
    shortDescription: 'A série jornalística investigativa que desvenda as maiores fraudes tecnológicas e ciber-ameaças.',
    fullDescription: 'Descubra os mistérios do cibercrime brasileiro, fraudes financeiras e os crimes ambientais examinados com profundidade táctica.',
    categoryId: 'cat-series',
    genre: 'Jornalismo Investigativo',
    ageRating: '12',
    year: 2026,
    duration: '2 Temporadas',
    cast: ['Equipe F5 TV', 'Investigadores de Crimes Inteligentes'],
    directors: ['Renata Vasconcelos'],
    coverUrl: 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?q=80&w=400',
    bannerUrl: 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=1200',
    trailerUrl: 'https://assets.mixkit.co/videos/preview/mixkit-software-developer-working-on-his-computer-34289-large.mp4',
    videoUrl: 'https://assets.mixkit.co/videos/preview/mixkit-software-developer-working-on-his-computer-34289-large.mp4',
    status: 'published',
    isFeatured: false,
    isFree: false,
    isExclusive: true,
    publishDate: '2026-05-20',
    tags: ['Cybersecurity', 'Fraudes', 'Investigação', 'F5'],
    viewsCount: 12540
  },
  {
    id: 'content-series-kids',
    type: 'series',
    title: 'Mundo F5 Kids',
    shortDescription: 'Desenhos animados educativos, cores e rimas mágicas para divertir e educar crianças pequenas.',
    fullDescription: 'Animações divertidas interativas criadas especialmente para o desenvolvimento cognitivo e noções ecológicas das mentes mirins.',
    categoryId: 'cat-infantil',
    genre: 'Infantil / Educativo',
    ageRating: 'L',
    year: 2026,
    duration: '1 Temporada',
    cast: ['Mundo Kids Voice Overs'],
    directors: ['Mel e Leo'],
    coverUrl: 'https://images.unsplash.com/photo-1485546246426-74dc88dec4d9?q=80&w=400',
    bannerUrl: 'https://images.unsplash.com/photo-1502086223501-7ea6ecd79368?q=80&w=1200',
    trailerUrl: 'https://assets.mixkit.co/videos/preview/mixkit-happy-toys-spinning-on-a-baby-crib-40156-large.mp4',
    videoUrl: 'https://assets.mixkit.co/videos/preview/mixkit-happy-toys-spinning-on-a-baby-crib-40156-large.mp4',
    status: 'published',
    isFeatured: false,
    isFree: true,
    isExclusive: false,
    publishDate: '2026-05-18',
    tags: ['Kids', 'Animação', 'Aprender', 'Amigável'],
    viewsCount: 8430
  },
  {
    id: 'content-series-bastidores',
    type: 'series',
    title: 'Bastidores da Cidade',
    shortDescription: 'Os segredos ocultos da infraestrutura invisível e vida noturna sob as grandes metrópoles.',
    fullDescription: 'Entre sob os trilhos escuros dos metrôs, os túneis centenários e descubra a engrenagem tática invisível de profissionais trabalhando à meia-noite.',
    categoryId: 'cat-bastidores',
    genre: 'Urbanismo & Sociedade',
    ageRating: '10',
    year: 2026,
    duration: '1 Temporada',
    cast: ['Engenheiros do Metrô', 'Trabalhadores da Madrugada'],
    directors: ['Gabriel Peixoto'],
    coverUrl: 'https://images.unsplash.com/photo-1514565131-fce0801e5785?q=80&w=400',
    bannerUrl: 'https://images.unsplash.com/photo-1477959858617-67f85cf4f1df?q=80&w=1200',
    trailerUrl: 'https://assets.mixkit.co/videos/preview/mixkit-futuristic-time-lapse-of-subway-station-lights-and-people-41002-large.mp4',
    videoUrl: 'https://assets.mixkit.co/videos/preview/mixkit-futuristic-time-lapse-of-subway-station-lights-and-people-41002-large.mp4',
    status: 'published',
    isFeatured: false,
    isFree: false,
    isExclusive: true,
    publishDate: '2026-05-15',
    tags: ['Metrópole', 'História', 'Infraestrutura', 'Bastidores'],
    viewsCount: 9320
  },
  {
    id: 'content-series-rastreadores',
    type: 'series',
    title: 'Rastreadores Criminais',
    shortDescription: 'Operações táticas das polícias no combate aos crimes de fronteira e colarinho branco.',
    fullDescription: 'Acompanhe grampos táticos reais autorizados pela Justiça, emboscadas para deter tráfico de armas pesadas e de lavagem de dinheiro milionária.',
    categoryId: 'cat-series',
    genre: 'Ação & Investigação Policial',
    ageRating: '14',
    year: 2026,
    duration: '1 Temporada',
    cast: ['Investigadores CORE', 'Polícia Federal'],
    directors: ['Wellington Prado'],
    coverUrl: 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?q=80&w=400&auto=format&fit=crop',
    bannerUrl: 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?q=80&w=1200',
    trailerUrl: 'https://assets.mixkit.co/videos/preview/mixkit-software-developer-working-on-his-computer-34289-large.mp4',
    videoUrl: 'https://assets.mixkit.co/videos/preview/mixkit-software-developer-working-on-his-computer-34289-large.mp4',
    status: 'published',
    isFeatured: false,
    isFree: false,
    isExclusive: true,
    publishDate: '2026-05-22',
    tags: ['Polícia', 'Ação', 'Tática', 'Crimes Real'],
    viewsCount: 16500
  },
  {
    id: 'content-series-fronteira',
    type: 'series',
    title: 'F5 Tech Docs: Fronteira da I.A.',
    shortDescription: 'Uma reflexão profunda sobre inteligência artificial, bioengenharia, e computação quântica.',
    fullDescription: 'Expedição exclusiva pelos laboratórios de ponta internacionais e nacionais focados no impacto da automação moderna na mente humana e na bio-impressora 3D.',
    categoryId: 'cat-documentarios',
    genre: 'Tecnologia & Futuro',
    ageRating: '12',
    year: 2026,
    duration: '1 Temporada',
    cast: ['Drs. em Inteligência Artificial', 'Bioengenheiros'],
    directors: ['Gabriel Peixoto'],
    coverUrl: 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?q=80&w=400',
    bannerUrl: 'https://images.unsplash.com/photo-1518770660439-4636190af475?q=80&w=1200',
    trailerUrl: 'https://assets.mixkit.co/videos/preview/mixkit-developer-working-on-different-screens-simultaneously-34290-large.mp4',
    videoUrl: 'https://assets.mixkit.co/videos/preview/mixkit-developer-working-on-different-screens-simultaneously-34290-large.mp4',
    status: 'published',
    isFeatured: false,
    isFree: false,
    isExclusive: true,
    publishDate: '2026-05-24',
    tags: ['Tecnologia', 'I.A.', 'Futuro', 'Computação'],
    viewsCount: 21900
  },
  {
    id: 'content-series-exploradores',
    type: 'series',
    title: 'Pequenos Exploradores',
    shortDescription: 'Aventuras animadas em 3D explicando vulcões, chuva e gravidade de forma lúdica.',
    fullDescription: 'Embarque com Guto e Bel para responder as maiores e mais instigantes dúvidas de mentes brilhantes de forma super amigável.',
    categoryId: 'cat-infantil',
    genre: 'Infantil / Educação Científica',
    ageRating: 'L',
    year: 2026,
    duration: '1 Temporada',
    cast: ['Guto', 'Bel'],
    directors: ['Animação Brasileira'],
    coverUrl: 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?q=80&w=400',
    bannerUrl: 'https://images.unsplash.com/photo-1516627145497-ae6968895b74?q=80&w=1200',
    trailerUrl: 'https://assets.mixkit.co/videos/preview/mixkit-happy-toys-spinning-on-a-baby-crib-40156-large.mp4',
    videoUrl: 'https://assets.mixkit.co/videos/preview/mixkit-happy-toys-spinning-on-a-baby-crib-40156-large.mp4',
    status: 'published',
    isFeatured: false,
    isFree: true,
    isExclusive: true,
    publishDate: '2026-05-26',
    tags: ['Ciência', 'Astronomia', 'Infantil', 'Didático'],
    viewsCount: 11050
  },
  {
    id: 'content-jornal-f5',
    type: 'news',
    title: 'Jornal F5: Edição Ao Vivo',
    shortDescription: 'As notícias cruciais do Brasil e do mundo, com comentários táticos, economia e reportagens de rua sem censura.',
    fullDescription: 'Transmitido diariamente, o Jornal F5 reinventou a forma de informar os brasileiros. Unindo infográficos modernos 3D e o jornalismo ético e combativo da equipe F5 TV para cobrir política, finanças e tecnologia de ponta.',
    categoryId: 'cat-jornalismo',
    genre: 'Noticiário Diário',
    ageRating: 'L',
    year: 2026,
    duration: '1h 20m',
    cast: ['Sandro Albuquerque', 'Renata Vasconcelos'],
    directors: ['Gabriel Peixoto'],
    coverUrl: 'https://images.unsplash.com/photo-1495020689067-958852a6565d?q=80&w=400',
    bannerUrl: 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?q=80&w=1200',
    trailerUrl: 'https://assets.mixkit.co/videos/preview/mixkit-camera-viewfinder-screen-recording-close-up-34304-large.mp4',
    videoUrl: 'https://assets.mixkit.co/videos/preview/mixkit-camera-viewfinder-screen-recording-close-up-34304-large.mp4',
    status: 'published',
    isFeatured: true,
    isFree: true,
    isExclusive: false,
    publishDate: '2026-05-27',
    tags: ['Ao Vivo', 'Notícias', 'Política', 'Brasil'],
    viewsCount: 22800
  },
  {
    id: 'content-f5-entrevista',
    type: 'tv_show',
    title: 'F5 Entrevista: Grandes Ideias',
    shortDescription: 'Conversas aprofundadas com cientistas, empresários e pensadores que estão desenhando nossa civilização em 2026.',
    fullDescription: 'F5 Entrevista traz encontros tête-à-tête focando na desconstrução intelectual dos maiores visionários do século. Sem pressa, sem cortes de estúdio comerciais, permitindo fluir o pensamento complexo.',
    categoryId: 'cat-programastv',
    genre: 'Talk Show',
    ageRating: '10',
    year: 2026,
    duration: '55m',
    cast: ['Juliana Beltrão', 'Prof. Miguel Archanjo'],
    directors: ['Karina Lemes'],
    coverUrl: cardF5EntrevistaGrandesIdeias,
    bannerUrl: 'https://images.unsplash.com/photo-1517048676732-d65bc937f952?q=80&w=1200',
    trailerUrl: 'https://assets.mixkit.co/videos/preview/mixkit-business-team-in-a-conference-meeting-room-33924-large.mp4',
    videoUrl: 'https://assets.mixkit.co/videos/preview/mixkit-business-team-in-a-conference-meeting-room-33924-large.mp4',
    status: 'published',
    isFeatured: false,
    isFree: false,
    isExclusive: true,
    publishDate: '2026-05-24',
    tags: ['Entrevista', 'Estilo de Vida', 'Ciência', 'Negócios'],
    viewsCount: 8900
  },
  {
    id: 'content-vozes-brasil',
    type: 'documentary',
    title: 'Vozes do Brasil: O Sertão Tecnológico',
    shortDescription: 'Como o semiárido nordestino está liderando a revolução de startups agrícolas e painéis solares residenciais autofinanciados.',
    fullDescription: 'Dos confins do semiárido até os polos de software do Agreste, este documentário de alta definição com lentes anamórficas revela a incrível engenhosidade brasileira que prospera com tecnologia limpa onde a água é escassa.',
    categoryId: 'cat-documentarios',
    genre: 'Documentário Social',
    ageRating: 'L',
    year: 2025,
    duration: '1h 12m',
    cast: ['Comunidades Locais', 'Narrado por Camila Pitanga'],
    directors: ['Benoit Camargo'],
    coverUrl: 'https://images.unsplash.com/photo-1460661419201-fd4cecdf8a8b?q=80&w=400',
    bannerUrl: 'https://images.unsplash.com/photo-1509316975850-ff9c5deb0cd9?q=80&w=1200',
    trailerUrl: 'https://assets.mixkit.co/videos/preview/mixkit-sunlight-on-dry-desert-soil-43282-large.mp4',
    videoUrl: 'https://assets.mixkit.co/videos/preview/mixkit-sunlight-on-dry-desert-soil-43282-large.mp4',
    status: 'published',
    isFeatured: false,
    isFree: false,
    isExclusive: true,
    publishDate: '2026-05-10',
    tags: ['Tecnologia', 'Sustentabilidade', 'Brasil', 'Inovação'],
    viewsCount: 15400
  },
  {
    id: 'content-noite-f5',
    type: 'special',
    title: 'Noite F5 Acústica: MPB em Alto Contraste',
    shortDescription: 'A reinterpretação intimista de clássicos da bossa e MPB por novos talentos brasileiros, gravado em Dolby Atmos no Rio.',
    fullDescription: 'Um show exclusivo idealizado nos modernos estúdios de performance da F5 TV. Cantores proeminentes dão vida nova aos poemas harmônicos de Tom Jobim, Cartola e Gilberto Gil sob iluminação âmbar relaxante.',
    categoryId: 'cat-especiais',
    genre: 'Musical Acústico',
    ageRating: 'L',
    year: 2026,
    duration: '1h 38m',
    cast: ['Mariana Lessa', 'Thiago da Viola'],
    directors: ['Felipe Gagliardi'],
    coverUrl: 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?q=80&w=400',
    bannerUrl: 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?q=80&w=1200',
    trailerUrl: 'https://assets.mixkit.co/videos/preview/mixkit-guitarist-performing-acoustic-concert-on-stage-34138-large.mp4',
    videoUrl: 'https://assets.mixkit.co/videos/preview/mixkit-guitarist-performing-acoustic-concert-on-stage-34138-large.mp4',
    status: 'published',
    isFeatured: true,
    isFree: false,
    isExclusive: true,
    publishDate: '2026-05-18',
    tags: ['Música', 'Cultura', 'Show', 'F5 Exclusivo'],
    viewsCount: 19800
  },
  {
    id: 'content-futebol-campeonato',
    type: 'sports',
    title: 'Supercopa F5: Semifinal Paulista',
    shortDescription: 'A decisão eletrizante por pênaltis entre os atletas que marcam a nova dinastia do futebol de base do Estado.',
    fullDescription: 'Com exclusividade de streaming online da F5 TV, acompanhe a eletrizante disputa onde craques adolescentes duelam pelo passaporte rumo à elite europeia de futebol em tempo real.',
    categoryId: 'cat-esportes',
    genre: 'Transmissão Esportiva',
    ageRating: 'L',
    year: 2026,
    duration: '2h 15m',
    cast: ['Narrado por Kléber Machado', 'Comentários de Grafite'],
    directors: ['Wellington Prado'],
    coverUrl: 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?q=80&w=400',
    bannerUrl: 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?q=80&w=1200',
    trailerUrl: 'https://assets.mixkit.co/videos/preview/mixkit-stadium-lights-shining-brightly-over-the-field-28406-large.mp4',
    videoUrl: 'https://assets.mixkit.co/videos/preview/mixkit-stadium-lights-shining-brightly-over-the-field-28406-large.mp4',
    status: 'published',
    isFeatured: false,
    isFree: true,
    isExclusive: false,
    publishDate: '2026-05-25',
    tags: ['Esportes', 'Futebol', 'Ao Vivo', 'Eletrizante'],
    viewsCount: 31200
  },
  {
    id: 'content-comedia-standup',
    type: 'special',
    title: 'F5 Stand-up Show: Humor sem Filtro',
    shortDescription: 'O especial de comédia de standup nacional mais ácido do ano, desafiando tabus cotidianos com gargalhadas garantidas.',
    fullDescription: 'Trazendo comédia visceral de improviso de um dos humoristas mais aclamados de São Paulo. Assuntos tabus como a vida no trânsito paulistano, relacionamentos virtuais e terapia em grupo.',
    categoryId: 'cat-entretenimento',
    genre: 'Stand-up Comedy',
    ageRating: '16',
    year: 2025,
    duration: '1h 05m',
    cast: ['Danilo Gentilli', 'Murilo Gun'],
    directors: ['Sérgio Malandro'],
    coverUrl: 'https://images.unsplash.com/photo-1516280440614-37939bbacd6a?q=80&w=400',
    bannerUrl: 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?q=80&w=1200',
    trailerUrl: 'https://assets.mixkit.co/videos/preview/mixkit-stage-curtains-sliding-open-revealing-bright-light-33925-large.mp4',
    videoUrl: 'https://assets.mixkit.co/videos/preview/mixkit-stage-curtains-sliding-open-revealing-bright-light-33925-large.mp4',
    status: 'published',
    isFeatured: false,
    isFree: false,
    isExclusive: false,
    publishDate: '2026-01-20',
    tags: ['Comédia', 'Risos', 'Adulto', 'Standup'],
    viewsCount: 11200
  },
  {
    id: 'content-codigo-ferro',
    type: 'documentary',
    title: 'O Código de Ferro: Hackers vs Estado',
    shortDescription: 'A guerra secreta por trás dos firewalls governamentais. Entenda como operam os grupos hackers estatais e como o Brasil se defende.',
    fullDescription: 'Uma obra investigativa espetacular com especialistas em segurança cibernética militar revelando o dia a dia de ataques digitais massivos direcionados a hospitais, usinas de energia e dados confidenciais do governo.',
    categoryId: 'cat-documentarios',
    genre: 'Documentário de Tecnologia',
    ageRating: '12',
    year: 2026,
    duration: '1h 15m',
    cast: ['Consultores de Segurança GSI', 'Hacker Anonimizados'],
    directors: ['Gabriel Peixoto'],
    coverUrl: 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?q=80&w=400',
    bannerUrl: 'https://images.unsplash.com/photo-1510511459019-5dda7724fd87?q=80&w=1200',
    trailerUrl: 'https://assets.mixkit.co/videos/preview/mixkit-hands-of-a-hacker-typing-code-on-a-keyboard-34293-large.mp4',
    videoUrl: 'https://assets.mixkit.co/videos/preview/mixkit-hands-of-a-hacker-typing-code-on-a-keyboard-34293-large.mp4',
    status: 'published',
    isFeatured: true,
    isFree: false,
    isExclusive: true,
    publishDate: '2026-05-15',
    tags: ['Cybersecurity', 'Documentário', 'Militar', 'Hackers'],
    viewsCount: 20450
  },
  {
    id: 'content-box-interlagos',
    type: 'sports',
    title: 'F5 Esportes: Segredos nos Boxes de Interlagos',
    shortDescription: 'Como engenheiros mudam 4 pneus, trocam asas e reabastecem a estratégia de corrida em menos de 2.0 segundos de pura adrenalina.',
    fullDescription: 'Este documentário esportivo imersivo tático revela os bastidores de alta precisão que dão vitórias nas corridas de endurance e Fórmula em São Paulo, mostrando a dinâmica incrível dos mecânicos de elite.',
    categoryId: 'cat-esportes',
    genre: 'Formativa de Esporte',
    ageRating: 'L',
    year: 2026,
    duration: '48m',
    cast: ['Felipe Giaffone', 'Equipe Técnica de Box'],
    directors: ['Karina Lemes'],
    coverUrl: 'https://images.unsplash.com/photo-1511919884226-fd3cad34687c?q=80&w=400',
    bannerUrl: 'https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?q=80&w=1200',
    trailerUrl: 'https://assets.mixkit.co/videos/preview/mixkit-sports-car-racing-on-a-track-31516-large.mp4',
    videoUrl: 'https://assets.mixkit.co/videos/preview/mixkit-sports-car-racing-on-a-track-31516-large.mp4',
    status: 'published',
    isFeatured: false,
    isFree: false,
    isExclusive: true,
    publishDate: '2026-04-18',
    tags: ['Velocidade', 'Fórmula', 'Interlagos', 'Engenharia'],
    viewsCount: 14200
  },
  {
    id: 'content-f5-politica-urgente',
    type: 'news',
    title: 'F5 Plantão Especial: Reforma Tributária',
    shortDescription: 'Análise tática minuto a minuto da votação histórica do novo regime fiscal brasileiro direto de Brasília.',
    fullDescription: 'A bancada do Jornal F5 se reúne em edição especial de urgência conectada diretamente com os correspondentes de Brasília para explicar de forma simples e direta o impacto real que o novo imposto causará na sua vida financeira.',
    categoryId: 'cat-jornalismo',
    genre: 'Plantão Especial',
    ageRating: 'L',
    year: 2026,
    duration: '1h 10m',
    cast: ['Sandro Albuquerque', 'Renata Vasconcelos', 'Comentaristas Financeiros'],
    directors: ['Gabriel Peixoto'],
    coverUrl: 'https://images.unsplash.com/photo-1540910419892-4a36d2c3266c?q=80&w=400',
    bannerUrl: 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=1200',
    trailerUrl: 'https://assets.mixkit.co/videos/preview/mixkit-camera-viewfinder-screen-recording-close-up-34304-large.mp4',
    videoUrl: 'https://assets.mixkit.co/videos/preview/mixkit-camera-viewfinder-screen-recording-close-up-34304-large.mp4',
    status: 'published',
    isFeatured: false,
    isFree: true,
    isExclusive: false,
    publishDate: '2026-05-26',
    tags: ['Política', 'Dinheiro', 'Economia', 'Ao Vivo'],
    viewsCount: 29850
  },
  {
    id: 'content-pequeno-astronauta',
    type: 'special',
    title: 'O Pequeno Astronauta: Viagem à Lua',
    shortDescription: 'Uma fantástica jornada para crianças aprenderem os segredos dos planetas, gravidade e as constelações com o cachorrinho Cosmo.',
    fullDescription: 'Aventure-se no espaço sideral de forma extremamente interativa e visual! Aprenda de maneira dinâmica como funcionam os foguetes, por que flutuamos no espaço e de onde veio o vento solar, idealizado pela equipe infantil F5 TV.',
    categoryId: 'cat-infantil',
    genre: 'Animação Educativa',
    ageRating: 'L',
    year: 2026,
    duration: '35m',
    cast: ['Dubladores F5 Kids'],
    directors: ['Karina Lemes'],
    coverUrl: 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=400',
    bannerUrl: 'https://images.unsplash.com/photo-1506318137071-a8e063b4bec0?q=80&w=1200',
    trailerUrl: 'https://assets.mixkit.co/videos/preview/mixkit-happy-toys-spinning-on-a-baby-crib-40156-large.mp4',
    videoUrl: 'https://assets.mixkit.co/videos/preview/mixkit-happy-toys-spinning-on-a-baby-crib-40156-large.mp4',
    status: 'published',
    isFeatured: false,
    isFree: true,
    isExclusive: true,
    publishDate: '2026-04-20',
    tags: ['Espaço', 'Estrelas', 'Infantil', 'Diversão'],
    viewsCount: 9780
  },
  {
    id: 'content-furious-waves',
    type: 'sports',
    title: 'Grandes Ondas: Nazaré & Saquarema',
    shortDescription: 'Os melhores surfistas do planeta duelam em paredões líquidos com mais de 25 metros de altura desafiando todos os limites físicos.',
    fullDescription: 'Sinta o frio na barriga acompanhando filmagens aéreas de altíssima definição 4K e câmeras de ação acopladas nas pranchas dos maiores atletas mundiais, enfrentando as lendárias ondas gigantes do circuito de surf.',
    categoryId: 'cat-esportes',
    genre: 'Esporte Radical',
    ageRating: 'L',
    year: 2025,
    duration: '1h 05m',
    cast: ['Medina', 'Lucas Chumbo', 'Maya Gabeira'],
    directors: ['Wellington Prado'],
    coverUrl: 'https://images.unsplash.com/photo-1502680390469-be75c86b636f?q=80&w=400',
    bannerUrl: 'https://images.unsplash.com/photo-1505118380757-91f5f5632de0?q=80&w=1200',
    trailerUrl: 'https://assets.mixkit.co/videos/preview/mixkit-sunlight-on-dry-desert-soil-43282-large.mp4',
    videoUrl: 'https://assets.mixkit.co/videos/preview/mixkit-sunlight-on-dry-desert-soil-43282-large.mp4',
    status: 'published',
    isFeatured: false,
    isFree: false,
    isExclusive: false,
    publishDate: '2026-02-12',
    tags: ['Surf', 'Aventura', 'Radical', 'Natureza'],
    viewsCount: 18900
  },
  {
    id: 'content-tecnologia-social-f5',
    type: 'documentary',
    title: 'Cidades Sustentáveis: O Futuro Urbano',
    shortDescription: 'Como capitais da Europa e América do Sul resolveram o trânsito e transformaram detritos urbanos em energia barata e renovável.',
    fullDescription: 'Projetos revolucionários mostram soluções inovadoras que podem ser adotadas nas nossas cidades hoje mesmo. Ciclovias integradas, edifícios verdes autofinanciados e transporte público 100% livre de emissões.',
    categoryId: 'cat-documentarios',
    genre: 'Documentário Ecológico',
    ageRating: 'L',
    year: 2026,
    duration: '1h 10m',
    cast: ['Arquitetos', 'Prefeitos Globais'],
    directors: ['Benoit Camargo'],
    coverUrl: 'https://images.unsplash.com/photo-1477959858617-67f85cf4f1df?q=80&w=400',
    bannerUrl: 'https://images.unsplash.com/photo-1449844908441-8829872d2607?q=80&w=1200',
    trailerUrl: 'https://assets.mixkit.co/videos/preview/mixkit-forest-fire-burning-at-night-42284-large.mp4',
    videoUrl: 'https://assets.mixkit.co/videos/preview/mixkit-forest-fire-burning-at-night-42284-large.mp4',
    status: 'published',
    isFeatured: false,
    isFree: true,
    isExclusive: true,
    publishDate: '2026-01-30',
    tags: ['Arquitetura', 'Clima', 'Soluções', 'Metrópole'],
    viewsCount: 16120
  }
];

// Seed active upload record logs
const INITIAL_UPLOADS: Upload[] = [
  {
    id: 'upl-001',
    fileName: 'novos_estudios_f5.mp4',
    fileType: 'video/mp4',
    size: '1.2 GB',
    progress: 100,
    status: 'ready',
    categoryId: 'cat-bastidores',
    title: 'Explorando os Novos Estúdios F5',
    coverUrl: 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?q=80&w=400',
    bannerUrl: 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?q=80&w=1200',
    videoUrl: 'https://assets.mixkit.co/videos/preview/mixkit-camera-viewfinder-screen-recording-close-up-34304-large.mp4',
    createdAt: '2026-05-26T15:20:00Z'
  },
  {
    id: 'upl-002',
    fileName: 'kids_ep3_v2_comp.mov',
    fileType: 'video/quicktime',
    size: '850 MB',
    progress: 100,
    status: 'processing',
    categoryId: 'cat-infantil',
    title: 'Mundo Kids: Melodia Colorida',
    coverUrl: 'https://images.unsplash.com/photo-151548804246426-74dc88dec4d9?q=80&w=400',
    bannerUrl: 'https://images.unsplash.com/photo-1515488042361-404e9250afef?q=80&w=1200',
    videoUrl: 'https://assets.mixkit.co/videos/preview/mixkit-happy-toys-spinning-on-a-baby-crib-40156-large.mp4',
    createdAt: '2026-05-27T01:10:00Z'
  }
];

const INITIAL_COUPONS: Coupon[] = [
  {
    id: 'cupom-1',
    code: 'F5BEMVINDO',
    discountType: 'percent',
    discountValue: 20,
    expiresAt: '2026-12-31',
    usageLimit: 1000,
    usageCount: 142,
    applicablePlans: ['plano-basico', 'plano-familia', 'plano-premium'],
    status: 'active'
  },
  {
    id: 'cupom-2',
    code: 'F5PREMIUM',
    discountType: 'percent',
    discountValue: 30,
    expiresAt: '2026-08-31',
    usageLimit: 500,
    usageCount: 88,
    applicablePlans: ['plano-premium'],
    status: 'active'
  },
  {
    id: 'cupom-3',
    code: 'F5ANUAL',
    discountType: 'fixed',
    discountValue: 10.00,
    expiresAt: '2026-12-31',
    usageLimit: 2000,
    usageCount: 520,
    applicablePlans: ['plano-basico', 'plano-familia', 'plano-premium'],
    status: 'active'
  }
];

const INITIAL_CHANNELS: Channel[] = [
  {
    id: 'channel-f5-tv',
    name: 'F5 TV Ao Vivo',
    description: 'Canal principal do ecossistema F5 TV com jornalismo geral, talk-shows, documentários premiados e programas de auditório e especiais.',
    logoText: 'F5 TV',
    streamUrl: 'https://assets.mixkit.co/videos/preview/mixkit-software-developer-working-on-his-computer-34289-large.mp4',
    active: true,
    status: 'online',
    category: 'Geral'
  },
  {
    id: 'channel-f5-news',
    name: 'F5 News',
    description: 'Noticiário 24 horas por dia com análises financeiras, política, economia e correspondentes ao vivo em todo o mundo.',
    logoText: 'F5 NEWS',
    streamUrl: 'https://assets.mixkit.co/videos/preview/mixkit-camera-viewfinder-screen-recording-close-up-34304-large.mp4',
    active: true,
    status: 'online',
    category: 'Jornalismo'
  },
  {
    id: 'channel-f5-esportes',
    name: 'F5 Esportes',
    description: 'Cobertura esportiva com resenhas diárias, reportagens, transmissões automotivas e partidas de base regionais ao vivo.',
    logoText: 'F5 SPORTS',
    streamUrl: 'https://assets.mixkit.co/videos/preview/mixkit-stadium-lights-shining-brightly-over-the-field-28406-large.mp4',
    active: true,
    status: 'online',
    category: 'Esportes'
  },
  {
    id: 'channel-f5-docs',
    name: 'F5 Documentários',
    description: 'Maratonas de documentários sobre tecnologia profunda, ecossistemas, problemas sociais e história do Brasil.',
    logoText: 'F5 DOCS',
    streamUrl: 'https://assets.mixkit.co/videos/preview/mixkit-forest-fire-burning-at-night-42284-large.mp4',
    active: true,
    status: 'online',
    category: 'Documentários'
  }
];

const INITIAL_LIVE_SCHEDULES: LiveSchedule[] = [
  // F5 TV Ao Vivo
  {
    id: 'live-p1',
    channelId: 'channel-f5-tv',
    title: 'Jornal F5 Primeira Edição',
    description: 'Abertura do dia com notícias do trânsito, tempo e os principais fatos da manhã.',
    host: 'Sandro Albuquerque',
    date: '2026-05-27',
    startTime: '07:00',
    endTime: '08:30',
    status: 'ended',
    imageUrl: 'https://images.unsplash.com/photo-1495020689067-958852a6565d?q=80&w=400'
  },
  {
    id: 'live-p2',
    channelId: 'channel-f5-tv',
    title: 'F5 Entrevista Especial',
    description: 'Ronaldo Lemos discute as novas leis de direito digital para empresas que operam na nuvem.',
    host: 'Juliana Beltrão',
    date: '2026-05-27',
    startTime: '13:00',
    endTime: '14:30',
    status: 'scheduled',
    imageUrl: 'https://images.unsplash.com/photo-1541872703-74c5e44368f9?q=80&w=400'
  },
  {
    id: 'live-p3',
    channelId: 'channel-f5-tv',
    title: 'Conexão F5 Ao Vivo',
    description: 'Operações em tempo real sendo mostradas ao vivo com repórteres nas maiores rodovias de SP.',
    host: 'Sandro Albuquerque',
    date: '2026-05-27',
    startTime: '18:00',
    endTime: '19:30',
    status: 'live',
    imageUrl: 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?q=80&w=400',
    isFeatured: true
  },
  {
    id: 'live-p4',
    channelId: 'channel-f5-tv',
    title: 'Noite F5 Acústica',
    description: 'Show acústico ao vivo com convidados especiais da MPB cantando Caetano Veloso.',
    host: 'Mariana Lessa',
    date: '2026-05-27',
    startTime: '21:00',
    endTime: '22:30',
    status: 'scheduled',
    imageUrl: 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?q=80&w=400'
  },

  // F5 News (Jornalismo)
  {
    id: 'live-n1',
    channelId: 'channel-f5-news',
    title: 'Jornal F5: Edição do Dia',
    description: 'Acompanhe as principais notícias do dia no Brasil e no mundo ao vivo com nossos comentaristas.',
    host: 'Renata Vasconcelos',
    date: '2026-05-27',
    startTime: '12:00',
    endTime: '13:30',
    status: 'rerun',
    imageUrl: 'https://images.unsplash.com/photo-1495020689067-958852a6565d?q=80&w=400'
  },
  {
    id: 'live-n2',
    channelId: 'channel-f5-news',
    title: 'Plantão Reforma Tributária',
    description: 'Boletim analítico extraordinário sobre a aprovação das novas alíquotas de impostos.',
    host: 'Sandro Albuquerque',
    date: '2026-05-27',
    startTime: '18:15',
    endTime: '19:45',
    status: 'live',
    imageUrl: 'https://images.unsplash.com/photo-1540910419892-4a36d2c3266c?q=80&w=400'
  },

  // F5 Esportes
  {
    id: 'live-e1',
    channelId: 'channel-f5-esportes',
    title: 'Resenha F5 Esportes',
    description: 'Comentários sobre a rodada do futebol paulista e as finais estaduais do final de semana.',
    host: 'Kléber Machado',
    date: '2026-05-27',
    startTime: '11:00',
    endTime: '12:00',
    status: 'ended',
    imageUrl: 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?q=80&w=400'
  },
  {
    id: 'live-e2',
    channelId: 'channel-f5-esportes',
    title: 'Supercopa F5 Base',
    description: 'Transmissão ao vivo do clássico paulista sub-20 direto do estádio municipal.',
    host: 'Narrado por Kléber Machado',
    date: '2026-05-27',
    startTime: '15:00',
    endTime: '17:00',
    status: 'premiere',
    imageUrl: 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?q=80&w=400'
  },

  // F5 Documentários
  {
    id: 'live-d1',
    channelId: 'channel-f5-docs',
    title: 'Maratona Fronteiras da I.A.',
    description: 'Episódios em sequência dissecando o surgimento da inteligência geral artificial.',
    host: 'Narração de Camila Pitanga',
    date: '2026-05-27',
    startTime: '20:00',
    endTime: '22:00',
    status: 'scheduled',
    imageUrl: 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?q=80&w=400'
  }
];

const INITIAL_DEVICES: ConnectedDevice[] = [
  {
    id: 'dev-1',
    userId: 'user-normal1',
    deviceName: 'Chrome no Windows (Atual)',
    deviceType: 'browser',
    lastActive: 'Hoje, ativo agora',
    location: 'São Paulo, SP',
    isActive: true
  },
  {
    id: 'dev-2',
    userId: 'user-normal1',
    deviceName: 'Safari no iPhone 15 Pro',
    deviceType: 'mobile',
    lastActive: 'Hoje, 2 horas atrás',
    location: 'Sorocaba, SP',
    isActive: true
  },
  {
    id: 'dev-3',
    userId: 'user-normal1',
    deviceName: 'Smart TV Samsung QLED 4K',
    deviceType: 'smart_tv',
    lastActive: 'Ontem, às 20:45',
    location: 'São Paulo, SP',
    isActive: true
  },
  {
    id: 'dev-4',
    userId: 'user-normal1',
    deviceName: 'Android TV Mi Box',
    deviceType: 'smart_tv',
    lastActive: '2 dias atrás',
    location: 'Mogi das Cruzes, SP',
    isActive: true
  },
  {
    id: 'dev-5',
    userId: 'user-normal1',
    deviceName: 'Microsoft Edge no Notebook',
    deviceType: 'desktop',
    lastActive: '3 dias atrás',
    location: 'São Paulo, SP',
    isActive: true
  }
];

// Seed Watch history for the subscriber
const INITIAL_WATCH_HISTORY: WatchHistory[] = [
  {
    id: 'hist-1',
    userId: 'user-normal1',
    contentId: 'content-jornal-f5',
    watchedPercent: 85,
    contentType: 'news',
    title: 'Jornal F5: Edição Ao Vivo',
    updatedAt: '2026-05-27T02:05:00Z'
  },
  {
    id: 'hist-2',
    userId: 'user-normal1',
    contentId: 'series-conexao-f5',
    episodeId: 'ep-conexao-1',
    watchedPercent: 40,
    contentType: 'series',
    title: 'Conexão F5 S1E1: O Golpe do Pix Reverso',
    updatedAt: '2026-05-26T21:40:00Z'
  }
];

// Seed Favorites
const INITIAL_FAVORITES: Favorite[] = [
  { id: 'fav-1', userId: 'user-normal1', contentId: 'content-noite-f5' },
  { id: 'fav-2', userId: 'user-normal1', contentId: 'series-conexao-f5' }
];

// Seed Notifications
const INITIAL_NOTIFICATIONS: Notification[] = [
  {
    id: 'notif-1',
    userId: null,
    title: 'Lançamento F5 Exclusivo',
    message: 'Já está disponível "Noite F5 Acústica: MPB em Alto Contraste", sintonize em resolução Master Dolby!',
    read: false,
    createdAt: '2026-05-18T20:00:00Z'
  },
  {
    id: 'notif-2',
    userId: 'user-normal1',
    title: 'Mensalidade Paga',
    message: 'Seu pagamento do Plano Premium com vencimento em 01/06 foi faturado e aprovado com sucesso.',
    read: false,
    createdAt: '2026-05-27T01:00:00Z'
  }
];

// Seed Reviews
const INITIAL_REVIEWS: Review[] = [
  {
    id: 'rev-1',
    contentId: 'content-series-conexao',
    profileId: 'prof-1',
    profileName: 'Gisele Principal',
    avatarColor: 'bg-red-600',
    rating: 5,
    comment: 'Série de investigação incrível! Esclarece pontos fundamentais com muita independência e dados precisos.',
    createdAt: '2026-05-25T14:20:00Z'
  },
  {
    id: 'rev-2',
    contentId: 'content-series-conexao',
    profileId: 'prof-3',
    profileName: 'Julio (Amigo)',
    avatarColor: 'bg-indigo-600',
    rating: 4,
    comment: 'Roteiro muito bem produzido e com condução ágil. Prende do começo ao fim!',
    createdAt: '2026-05-26T18:15:00Z'
  },
  {
    id: 'rev-3',
    contentId: 'content-jornal-f5',
    profileId: 'prof-4',
    profileName: 'Arthur',
    avatarColor: 'bg-amber-600',
    rating: 5,
    comment: 'Jornalismo sério e contundente. Uma lufada de ar fresco no cenário atual do streaming brasileiro.',
    createdAt: '2026-05-26T09:12:00Z'
  },
  {
    id: 'rev-4',
    contentId: 'content-vozes-brasil',
    profileId: 'prof-1',
    profileName: 'Gisele Principal',
    avatarColor: 'bg-red-600',
    rating: 5,
    comment: 'Trabalho de campo e captação de áudio surreais! Dá muito orgulho de assistir produções brasileiras desse kilate.',
    createdAt: '2026-05-27T02:05:00Z'
  }
];

// Local Storage Core helper
function getLocalStorageItem<T>(key: string, initialDefault: T): T {
  try {
    const item = localStorage.getItem(key);
    if (!item) {
      localStorage.setItem(key, JSON.stringify(initialDefault));
      return initialDefault;
    }
    const parsed = JSON.parse(item);
    
    // Automatically merge missing default elements for our catalog datasets
    if (Array.isArray(parsed) && Array.isArray(initialDefault)) {
      let changed = false;
      const merged = [...parsed];
      for (const defItem of initialDefault) {
        if (defItem && typeof defItem === 'object' && 'id' in defItem) {
          const exists = parsed.some(p => p && p.id === defItem.id);
          if (!exists) {
            merged.push(defItem);
            changed = true;
          }
        }
      }
      if (changed) {
        localStorage.setItem(key, JSON.stringify(merged));
        return merged as unknown as T;
      }
    }
    return parsed as T;
  } catch (error) {
    console.error('LocalStorage error reading ' + key, error);
    return initialDefault;
  }
}

function setLocalStorageItem<T>(key: string, data: T): void {
  try {
    localStorage.setItem(key, JSON.stringify(data));
  } catch (error) {
    console.error('LocalStorage error writing ' + key, error);
  }
}

// Seed initial general metadata movie contents (films, live events, standalones etc) dynamically
const COMPACT_CATALOG: Record<string, [string, string, string, string, string][]> = {
  'cat-aovivo': [
    ['F5 Debate: O Impacto da I.A. no Trabalho', 'Debate em tempo real com cientistas sociais e tecnólogos sobre automação e emprego legal no Brasil.', 'Debate,I.A.,Trabalho', 'Debate Ao Vivo', 'business'],
    ['Copa Regional F5: Grande Final', 'A partida decisiva pelo troféu regional dita o ritmo do futebol amador de várzea paulista.', 'Futebol,Copa,Esportes', 'Festa Ao Vivo', 'soccer'],
    ['F5 Tecnologia Summit 2026', 'Acompanhe as palestras principais sobre robótica avançada, biohacking e computação quântica.', 'Tecnologia,Summit,Hardware', 'Summit Tecnológico', 'computer'],
    ['Grande Prêmio de Interlagos de Kart', 'Pilotos novos travam um duelo na tradicional e rápida pista de karts de São Paulo.', 'Corrida,Kart,Velocidade', 'Corrida Ao Vivo', 'car'],
    ['Tribuna Livre: A Voz das Ruas', 'Repórteres entram ao vivo nas principais praças das capitais ouvindo opiniões populares.', 'Opinião,Sociedade,Cidades', 'Jornalismo Popular', 'street'],
    ['F5 Festival de Jazz Ao Vivo', 'Apresentação intimista dos maiores nomes de jazz instrumental brasileiro.', 'Jazz,Música,Acústico', 'Musical Ao Vivo', 'music'],
    ['Congresso de Biotecnologia Médica', 'Painéis científicos ao vivo expondo os avanços sobre sequenciamento genético e patologias.', 'Saúde,Medicina,Ciência', 'Congresso de Saúde', 'science'],
    ['Boletim Econômico: Fechamento de Bolsa', 'Acompanhe a análise em tempo real do pregão financeiro e tendências para taxas de juros.', 'Finanças,Bolsa,Dinheiro', 'Boletim Ao Vivo', 'money'],
    ['F5 Agro: O Futuro do Campo Ao Vivo', 'Transmissão focada na nova linha de tratores autónomos e defensivos bio-ecológicos.', 'Agro,Inovação,Negócios', 'Agroturismo', 'field'],
    ['Show de Talentos F5 Universitários', 'A final da batalha musical congregando estudantes das principais faculdades públicas.', 'Música,Duelo,Universidade', 'Festival Ao Vivo', 'stage']
  ],
  'cat-jornalismo': [
    ['Jornal F5: Edição Ao Vivo', 'As notícias cruciais do Brasil e do mundo, com comentários táticos, economia e reportagens de rua sem censura.', 'Notícias,Brasil,Urgente', 'Noticiário Diário', 'news'],
    ['F5 Plantão Especial: Reforma Tributária', 'Análise tática minuto a minuto da votação histórica do novo regime fiscal brasileiro.', 'Política,Reforma,Dinheiro', 'Plantão Especial', 'business'],
    ['A Crise da Água Doce', 'A investigação profunda sobre o esgotamento acelerado dos principais reservatórios de água.', 'Clima,Seca,Planeta', 'Jornalismo Investigativo', 'lake'],
    ['Inflação no Prato: O Preço do Alimento', 'Repórteres analisam de perto por que as frutas e grãos essenciais atingiram o maior valor da década.', 'Inflação,Alimento,Dinheiro', 'Jornalismo Econômico', 'food'],
    ['Fronteira Seca: Tráfico de Madeira', 'Câmeras escondidas mostram as rotas secretas de desmatamento ilegal exportando madeira nativa.', 'Desmatamento,Fronteira,Crimes', 'Jornalismo Investigativo', 'forest'],
    ['Golpe da Falsa Central de Segurança', 'Investigação revela como operam quadrilhas telefônicas sofisticadas clonando apps de bancos.', 'Fraude,Segurança,Banco', 'Jornalismo Policial', 'phone'],
    ['Trabalho Fantasma: Fraude do Home Office', 'Análise detalhando golpes corporativos onde robôs movem cursores simulando tarefas laborais.', 'Trabalho,Corporate,Denúncia', 'Jornalismo Investigativo', 'office'],
    ['Dinastia do Lixo: Máfias Ecológicas', 'Cartéis clandestinos extorquem as grandes redes de reciclagem urbana travando o avanço ecológico.', 'Reciclagem,Máfia,Cidades', 'Jornalismo Investigativo', 'trash'],
    ['Especial Pantanal: O Fogo Invisível', 'Por dentro das táticas militares e de brigadistas caçando incêndios que queimam sob as camadas do solo.', 'Pantanal,Fogo,Clima', 'Plantão Especial', 'fire'],
    ['A Era do Remédio Falsificado', 'Laboratórios de remédios clandestinos na criminalidade farmacêutica adulterando quimioterápicos.', 'Saúde,Fraude,Polícia', 'Jornalismo Investigativo', 'pills']
  ],
  'cat-series': [
    ['Conexão F5', 'A série jornalística investigativa que desvenda as maiores fraudes tecnológicas e ciber-ameaças.', 'Cyber,Segurança,Fraude', 'Jornalismo Investigativo', 'cyber'],
    ['Rastreadores Criminais', 'Operações táticas das polícias no combate aos crimes de fronteira e colarinho branco.', 'Polícia,Ação,Fronteira', 'Ação Policial', 'police'],
    ['O Cérebro Criminal', 'Uma série fascinante onde psiquiatras forenses dissecam os perfis dos mentores dos maiores roubos digitais.', 'Mente,Forense,Crime', 'Suspense Investigativo', 'brain'],
    ['Hackers de Estado', 'Análise dramática de espionagem geopolítica onde agentes decifram ameaças sobre dados confidenciais.', 'Geopolítica,Defesa,Espiões', 'Suspense Político', 'code'],
    ['Rotas Proibidas', 'Uma série tática dramática com a rotina de policiais investigando o contrabando de cargas.', 'Estrada,Carga,Polícia', 'Ação Investigativa', 'truck'],
    ['Subsolo SP: Mistérios Sob Asfalto', 'Acompanhe as incríveis escavações do metrô de São Paulo e os resquícios arqueológicos soterrados.', 'Metrô,Arqueologia,História', 'Série de Aventura', 'underground'],
    ['Código Vermelho: Ciberataque', 'As 24 horas dramáticas da equipe técnica de TI tentando salvar registros médicos quando o hospital principal é hackeado.', 'Hospital,Hackers,Ação', 'Suspense Tecnológico', 'server'],
    ['Código Aberto: No princípio de Tudo', 'Uma crônica espetacular sobre os pioneiros do código brasileiro montando os primeiros computadores.', 'História,Software,Computador', 'Série Documental', 'keyboard'],
    ['Agentes da Mata Atlântica', 'O dia a dia de guardas militares de floresta caçando desmatadores e gangues de caça ilegal.', 'Ecológico,Natureza,Polícia', 'Ação Ecológica', 'wood'],
    ['Vigilância Máxima: Olhos da Cidade', 'Por dentro das câmeras e computadores municipais regulando emergências de trânsito e crimes em tempo real.', 'Câmeras,Cidades,Monitoramento', 'Rotina Urbana', 'camera']
  ],
  'cat-programastv': [
    ['F5 Entrevista: Grandes Ideias', 'Conversas aprofundadas com cientistas, empresários e pensadores que estão desenhando nossa civilização.', 'Entrevista,Debate,Cultura', 'Talk Show', 'people'],
    ['Sabores do Interior Rústico', 'Descubra a culinária caipira tradicional em panelas antigas de barro no interior paulista.', 'Cozinha,Comida,Interior', 'Culinária Rústica', 'food'],
    ['F5 Garagem: Carros Clássicos', 'Restauradores renomados recuperam os carros icônicos da indústria automotiva nacional de 1970 a 1990.', 'Carros,Motores,Clássicos', 'Automobilismo', 'car'],
    ['Desafio Empreendedor F5', 'Investidores avaliam propostas de negócios de startups de tecnologia e impacto social.', 'Business,Pitch,Aporte', 'Reality Show', 'office'],
    ['Casas Autônomas e Conectadas', 'A simulação robótica e de painéis térmicos nas novas casas inteligentes e verdes pelo Brasil.', 'Decoração,Automação,Design', 'Estilo de Vida', 'house'],
    ['Espaço Verde: Jardinagem', 'Dicas práticas de horta orgânica e paisagismo para espaços pequenos e sacadas de apartamento.', 'Horta,Plantas,Flores', 'Instrucional', 'garden'],
    ['Química na Cozinha', 'Chefes de renome ensinam como a ciência ajuda na criação de emulsões e assados perfeitos.', 'Química,Cozinha,Gastronomia', 'Culinária Científica', 'baking'],
    ['Mestres Cervejeiros F5', 'Uma divertida batalha de micro-cervejarias artesanais disputando a melhor receita de IPA.', 'Cerveja,Artesanal,Duelo', 'Reality Show', 'beer'],
    ['F5 Arena Debate', 'Ex-atletas paulistas conversam em estúdio sobre desafios táticos dos campeonatos continentais.', 'Futebol,Resenha,Tática', 'Mesa Redonda', 'soccer'],
    ['Arquitetura de Ideias', 'Entrevistas fantásticas com arquitetos e historiadores decifrando o desenho das capitais.', 'Cidades,Design,Urbanismo', 'Arquitetura', 'city']
  ],
  'cat-esportes': [
    ['Supercopa F5: Semifinal Paulista', 'A decisão eletrizante por pênaltis entre os atletas que marcam a nova dinastia do futebol de base.', 'Futebol,Supercopa,Pênaltis', 'Transmissão Esportiva', 'soccer'],
    ['F5 Esportes: Segredos nos Boxes de Interlagos', 'Como engenheiros fazem pit stops de menos de 2 segundos sob alta adrenalina.', 'Corrida,Interlagos,Fórmula', 'Documental Esportivo', 'car'],
    ['Grandes Ondas: Nazaré & Saquarema', 'Os melhores surfistas do planeta duelam em paredões líquidos com mais de 25 metros de altura.', 'Surf,Ondas,Radical', 'Esporte Radical', 'sea'],
    ['Ciclismo de Alta Montanha', 'A brutal subida e descida eletrizante por estradas íngremes na Serra da Mantiqueira.', 'Ciclismo,Serra,Mountain', 'Esporte Radical', 'bike'],
    ['O Caminho do Tatame: Jiu-Jitsu', 'Investigue as grandes dinastias de lutadores e táticas de solo que fizeram do Brasil o berço da luta.', 'ArtesMarciais,JiuJitsu,Tática', 'Artes Marciais', 'martial'],
    ['Basquete de Rua: O Jogo do Asfalto', 'Atletas paulistas mostram as manobras rápidas e a intensa cultura de basquete de rua.', 'Basquete,Streetball,Manobras', 'Esporte Urbano', 'basketball'],
    ['Skate no Concreto Paulistano', 'Uma expedição pelas praças paulistanas e obstáculos urbanos com a elite do skate de rua.', 'Skate,Manobras,Sampa', 'Esporte Radical', 'skate'],
    ['Guerreiros do Asfalto: Maratonas', 'A incrível resiliência mental e de oxigênio de corredores sob altas temperaturas de Sol.', 'Maratona,Atleta,Corrida', 'Atletismo', 'run'],
    ['Triatlo Extremo das Montanhas', 'Do nascer ao pôr do sol, competidores nadam, pedalam e correm picos íngremes sob clima gelado.', 'Triatlo,Físico,Superação', 'Resistência Extrema', 'mountain'],
    ['A Classe da Vela: Ventos Fortes', 'Táticas de iatismo e dinâmica das rajadas necessárias para navegar em embarcações olímpicas.', 'Vela,Navegação,Ventos', 'Iatismo', 'sail']
  ],
  'cat-documentarios': [
    ['F5 Tech Docs: Fronteira da I.A.', 'Uma reflexão profunda sobre inteligência artificial, bioengenharia, e computação quântica.', 'I.A.,Robôs,Futuro', 'Ciência & Tecnologia', 'computer'],
    ['Vozes do Brasil: Sertão Tecnológico', 'Como o semiárido nordestino está liderando a revolução de startups agrícolas e painéis solares.', 'Agro,Inovação,Nordeste', 'Socio-Documentário', 'field'],
    ['O Código de Ferro: Hackers vs Estado', 'A guerra secreta por trás dos firewalls governamentais. Entenda como operam os grupos hackers.', 'Hackers,Invasão,Federal', 'Documentário Tecnológico', 'code'],
    ['Cidades Sustentáveis: O Futuro Urbano', 'Como capitais resolveram o trânsito e transformaram detritos urbanos em energia barata e renovável.', 'Ecológico,Sociedade,Cidades', 'Urbanismo & Ecologia', 'city'],
    ['O Império do Silício', 'Uma detalhada crônica sobre os monopólios de microchips, semicondutores e a ávida caçada por lítio.', 'Hardware,Semicondutor,Chip', 'Documentário de Ciência', 'board'],
    ['Fusão Nuclear: A Criação do Sol', 'Como cientistas simulam as pressões do Sol na Terra para obter energia ilimitada eletrizante.', 'Física,Fusão,Energia', 'Ciência & Tecnologia', 'star'],
    ['Arqueologia Amazônica: Mundos Perdidos', 'Drones equipados com sensores LiDAR mapeiam enormes vilas de milênios soterrados na floresta.', 'Arqueologia,Amazônia,História', 'Pesquisa Científica', 'jungle'],
    ['A Rede Oculta das Florestas', 'A incrível e complexa biologia de fungos e líbens que carregam recursos de árvore em árvore.', 'Biologia,Plantas,Natureza', 'Natureza & Ecologia', 'tree'],
    ['Parametrismo: Algoritmo das Linhas', 'A evolução do modernismo ao código de computadores modelando novos marcos arquitetônicos.', 'Design,Computação,Estrutura', 'Desenho Urbano', 'building'],
    ['O Último Glaciar Andino', 'Cientistas escalam geleiras tropicais para registrar as drásticas mudanças do manto glacial.', 'Clima,Gelo,Andes', 'Pesquisa Científica', 'glacier']
  ],
  'cat-entretenimento': [
    ['F5 Stand-up Show: Humor sem Filtro', 'O especial de comédia de standup nacional mais ácido do ano, desafiando tabus cotidianos.', 'Standup,Risos,Comédia', 'Humorismo', 'stage'],
    ['Cinema de Calçada Independente', 'Reportagem cultural sobre exibições de curtas-metragens gratuitas armadas em praças de São Paulo.', 'Cinema,Pipoca,Lazer', 'Entretenimento Cultural', 'projector'],
    ['F5 Stand-up Feminino: Elas dão o Ritmo', 'Comediantes consagradas tratam de casamento virtual, apps de paquera e rotina corporativa.', 'Standup,Humor,Mulheres', 'Humorismo', 'microphone'],
    ['Mestres de Tabuleiro: A Nova Moda', 'O retorno dos jogos analógicos e tabuleiros de madeira como mercado bilionário de lazer tático.', 'Jogos,Lazer,Tabuleiros', 'Estilo de Vida', 'board'],
    ['F5 Humor Instantâneo', 'Um compilado espetacular e hilário de sketches do dia a dia encenados pelos humoristas da F5 TV.', 'Sketches,Improviso,Humor', 'Comédia de Sketches', 'laugh'],
    ['Batalhas de Rimas: Arena de Versos', 'A rítmica veloz e disputas de freestyle que moldam a juventude cultural das maiores periferias.', 'Rimas,Rap,Duelo', 'Batalha de Freestyle', 'streets'],
    ['Festival do Riso: Rostos da Comédia', 'Comediantes amadores em testes hilários frente a jurados e plateia implacável da capital.', 'Duelo,Improviso,Risos', 'Humorismo', 'stage'],
    ['Cinema de Guerrilha Nacional', 'Obras de ficção independentes de baixo custo ganhando repercussão e aclamação de festivais.', 'Indie,Cine,Ficção', 'Dramas Premiados', 'camera'],
    ['Mágica de Calçada: Mentalistas', 'Mágicos paulistas lêem as mentes dos pedestres em praças públicas, decifrando dados ocultos.', 'Mágica,Ilusionismo,Mente', 'Mentalismo de Rua', 'cards'],
    ['Os Reis da Tira: Desenhando Histórias', 'Ilustradores e cartunistas mostram o refinamento por trás de tirinhas ácidas e charges de fôlego.', 'HQ,Quadrinhos,Desenho', 'Arte & Ilustração', 'pencil']
  ],
  'cat-infantil': [
    ['Mundo F5 Kids', 'Desenhos animados educativos, cores e rimas mágicas para divertir e educar crianças pequenas.', 'Desenho,Kids,Cores', 'Infantil / Educativo', 'kids'],
    ['Pequenos Exploradores', 'Aventuras animadas em 3D explicando vulcões, chuva e gravidade de forma de forma lúdica.', 'Aprendizado,Dúvidas,Astronomia', 'Infantil / Educativo', 'baby'],
    ['O Pequeno Astronauta: Viagem à Lua', 'Uma fantástica jornada para crianças aprenderem os segredos dos planetas com o cachorrinho Cosmo.', 'Espaço,Estrelas,Lua', 'Infantil / Educativo', 'moon'],
    ['Abecedário de Animais', 'Canções fofas rimadas onde carismáticos animais introduzem letras, silabas e novos vocabulários brincando.', 'Letras,Animais,Sons', 'Musical Educativo', 'animal'],
    ['Mil Cores de Mel & Leo', 'Duas crianças desenham objetos mágicos colorizando mundos cinzentos e apagados.', 'Desenho,Educação,Pintura', 'Animação Infantil', 'paint'],
    ['Melodias para Banho Calmo', 'Composições suaves e vídeos engraçados ensinando práticas ecológicas e higiene corporal de forma leve.', 'Melodia,Banho,Higiene', 'Infantil / Educativo', 'soap'],
    ['O Clube dos Insetos Cientistas', 'Insetos engenheiros reúnem blocos de folhas para resolver desafios lógicos com rimas.', 'Física,Lógica,Insetos', 'Animação lúdica', 'forest'],
    ['Estrelinhas Azuis: Ninar', 'Sinfonias em piano suave e animações de fadinhas embaladas para acalmar os pequeninos no sono.', 'Sono,Ninar,Calmo', 'Dormir Bem', 'stars'],
    ['A Liga dos Brinquedos Secretos', 'À noite, brinquedos ganham vida de detetives vasculhando o quarto para achar meias perdidas.', 'Brinquedos,Detetives,Aventura', 'Animação Infantil', 'toys'],
    ['Rimas & Formas com Guto e Bel', 'Triângulos e esferas são apresentados de forma lúdica em rimas divertidas em português.', 'Formas,Lógica,Geometria', 'Aprendizado Lúdico', 'blocks']
  ],
  'cat-bastidores': [
    ['Bastidores da Cidade', 'Os segredos ocultos da infraestrutura invisível e vida noturna sob as grandes metrópoles.', 'Logística,Cidade,Invisível', 'Urbanismo', 'city'],
    ['Por Trás da Linha Virtual F5', 'A correria dos carpinteiros e editores virtuais programando os cenários 3D reais e telão LED.', 'F5,Cenário,Tecnologia', 'Bastidores de Estúdio', 'studio'],
    ['A Engenharia dos Semáforos', 'Como o tráfego urbano é calculated em milésimos de segundo para evitar nós viários de rush.', 'Tráfego,Trânsito,Cidades', 'Engenharia de Tráfego', 'lights'],
    ['Fios no Mar: Cabos Submarinos', 'Logística gigantesca de navios lançando milhares de quilômetros de cabos ópticos nas fossas.', 'Mar,Fibra,Internet', 'Infraestrutura Global', 'cable'],
    ['Logística Colossal dos Correios', 'Por dentro dos scanners e esteiras ultra velozes que catalogam milhões de encomendas diárias.', 'Encomendas,Logística,Scanner', 'Automatização', 'shipment'],
    ['Bastidores da Ópera Municipal', 'Equipes de ensaios curando cenários seculares, figurinos dourados e microfones para a estréia.', 'Teatro,Ópera,Ensaios', 'Nos Bastidores', 'opera'],
    ['Ruídos Fantásticos: Foley Sound', 'Como artistas de estúdio simulam trovões de filmes usando chapas finas de metal e areia.', 'Áudio,Som,Cinema', 'Foley & Efeitos', 'sound'],
    ['Logística de Solo de Aviões', 'Atletas de pista abastecendo e preparando jatos em minutos para pistas de alta rotatividade.', 'Aeroporto,Aviões,Trabalho', 'Logística Aérea', 'airport'],
    ['Química da Restauração de Arte', 'Restauradores renomados removem poeira secular de pinturas antiga usando solventes químicos.', 'Restauração,Museu,Arte', 'Restauração Científica', 'paint'],
    ['Guerreiros de Alta Voltagem', 'Eletricistas escalam torres energizadas de 45 metros sob temporais para restaurar a rede.', 'Energia,Trabalho,Risco', 'Engenharia Elétrica', 'power']
  ],
  'cat-especiais': [
    ['Noite F5 Acústica: MPB em Alto Contraste', 'A reinterpretação intimista de clássicos da bossa e MPB por novos talentos brasileiros nos estúdios F5.', 'Música,Cultura,Show', 'Musical Acústico', 'guitar'],
    ['Especial Sinfônico: Villa-Lobos', 'Orquestra paulistana relê clássicos em concerto gravado com microfones binaurais de alta definição.', 'Clássica,Sinfonia,Show', 'Sinfonietta', 'violin'],
    ['Histórias Ocultas do Ontem', 'Especial jornalístico digitalizando notícias e gravações antigas das primeiras transmissões da TV.', 'História,Acervo,Imagens', 'Especial de Acervo', 'vintage'],
    ['Música de Verão: Samba de Areia', 'Grandes sambistas reúnem-se nas dunas costeiras para cantar clássicos de violão acústico ao pôr do sol.', 'Samba,Verão,Show', 'Roda de Samba', 'beach'],
    ['A Voz Humana: Coral Polifônico', 'Vinte cantores interpretam modinhas do século XIX acoplando técnicas acústicas de eco sintonizado.', 'Coral,Vozes,Acústico', 'Canto Coral', 'stage'],
    ['F5 Masterclass: Escrita Novelesca', 'Novelistas de sucesso palestram sobre a formulação de ganchos táticos literários e roteiro dramático.', 'Escrita,Roteiro,Dicas', 'Masterclass F5', 'typewriter'],
    ['Direção Fotográfica de Cinema', 'Os segredos das lentes anamórficas, ângulos de luz dramática e correção de cores para alta tela.', 'Cinema,Luzes,Lentes', 'Fotografia tática', 'lenses'],
    ['Sons de Folha: Meditação Binaural', 'Duas horas imersivas de áudio ecológico coletado em florestas nativas para foco sereno e relaxamento.', 'Sons,Natureza,Meditação', 'Meditação Guiada', 'forest'],
    ['Poesia Acústica sob Violoncelo', 'Poetas modernos recitam versos urbanos sob a suave e comovente melodia de violoncelistas convidados.', 'Poesia,Acústico,Música', 'Espetáculo Poético', 'cello'],
    ['Fábulas de Acervo: Especial de Natal', 'Uma maravilhosa releitura de causos folclóricos e rimas típicas brasileiras encenadas no estúdio.', 'Nostalgia,Natal,Episódio', 'Teatro Especial', 'christmas']
  ]
};

const INITIAL_CONTENTS: Content[] = [];

const ID_MAP: Record<string, string> = {
  'Conexão F5': 'content-series-conexao',
  'Rastreadores Criminais': 'content-series-rastreadores',
  'Mundo F5 Kids': 'content-series-kids',
  'Pequenos Exploradores': 'content-series-exploradores',
  'O Pequeno Astronauta: Viagem à Lua': 'content-pequeno-astronauta',
  'Bastidores da Cidade': 'content-series-bastidores',
  'F5 Tech Docs: Fronteira da I.A.': 'content-series-fronteira',
  'Vozes do Brasil: Sertão Tecnológico': 'content-vozes-brasil',
  'O Código de Ferro: Hackers vs Estado': 'content-codigo-ferro',
  'Cidades Sustentáveis: O Futuro Urbano': 'content-tecnologia-social-f5',
  'Jornal F5: Edição Ao Vivo': 'content-jornal-f5',
  'F5 Plantão Especial: Reforma Tributária': 'content-f5-politica-urgente',
  'F5 Entrevista: Grandes Ideias': 'content-f5-entrevista',
  'Noite F5 Acústica: MPB em Alto Contraste': 'content-noite-f5',
  'Supercopa F5: Semifinal Paulista': 'content-futebol-campeonato',
  'F5 Esportes: Segredos nos Boxes de Interlagos': 'content-box-interlagos',
  'Grandes Ondas: Nazaré & Saquarema': 'content-furious-waves',
  'F5 Stand-up Show: Humor sem Filtro': 'content-comedia-standup'
};

const CATEGORY_UNSPLASH: Record<string, string[]> = {
  'cat-aovivo': [
    'https://images.unsplash.com/photo-1475721027785-f74eccf877e2?q=80&w=400',
    'https://images.unsplash.com/photo-1475721027785-f74eccf877e2?q=80&w=1200'
  ],
  'cat-jornalismo': [
    'https://images.unsplash.com/photo-1495020689067-958852a6565d?q=80&w=400',
    'https://images.unsplash.com/photo-1504711434969-e33886168f5c?q=80&w=1200'
  ],
  'cat-series': [
    'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?q=80&w=400',
    'https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=1200'
  ],
  'cat-programastv': [
    'https://images.unsplash.com/photo-1541872703-74c5e44368f9?q=80&w=400',
    'https://images.unsplash.com/photo-1517048676732-d65bc937f952?q=80&w=1200'
  ],
  'cat-esportes': [
    'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?q=80&w=400',
    'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?q=80&w=1200'
  ],
  'cat-documentarios': [
    'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?q=80&w=400',
    'https://images.unsplash.com/photo-1518770660439-4636190af475?q=80&w=1200'
  ],
  'cat-entretenimento': [
    'https://images.unsplash.com/photo-1516280440614-37939bbacd6a?q=80&w=400',
    'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?q=80&w=1200'
  ],
  'cat-infantil': [
    'https://images.unsplash.com/photo-1485546246426-74dc88dec4d9?q=80&w=400',
    'https://images.unsplash.com/photo-1502086223501-7ea6ecd79368?q=80&w=1200'
  ],
  'cat-bastidores': [
    'https://images.unsplash.com/photo-1514565131-fce0801e5785?q=80&w=400',
    'https://images.unsplash.com/photo-1477959858617-67f85cf4f1df?q=80&w=1200'
  ],
  'cat-especiais': [
    'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?q=80&w=400',
    'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?q=80&w=1200'
  ]
};

Object.entries(COMPACT_CATALOG).forEach(([catId, items]) => {
  items.forEach(([title, desc, tagsStr, genre, kw], idx) => {
    let cType: Content['type'] = 'movie';
    if (catId === 'cat-series') cType = 'series';
    else if (catId === 'cat-jornalismo' || catId === 'cat-aovivo') cType = 'news';
    else if (catId === 'cat-programastv') cType = 'tv_show';
    else if (catId === 'cat-documentarios') cType = 'documentary';
    else if (catId === 'cat-esportes') cType = 'sports';
    else if (catId === 'cat-infantil') cType = 'special';
    else if (catId === 'cat-bastidores') cType = 'documentary';
    else if (catId === 'cat-especiais') cType = 'special';
    else if (catId === 'cat-entretenimento') cType = 'special';

    const tags = tagsStr.split(',');
    const finalId = ID_MAP[title] || `content-cat-${catId}-${idx + 1}`;
    
    // Choose appropriate image urls
    const urls = CATEGORY_UNSPLASH[catId];
    const coverUrl = catId === 'cat-programastv'
      ? PROGRAM_TV_COVERS[idx % PROGRAM_TV_COVERS.length]
      : (urls ? urls[0] : 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?q=80&w=400');
    const bannerUrl = urls ? urls[1] : 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=1200';

    INITIAL_CONTENTS.push({
      id: finalId,
      type: cType,
      title,
      shortDescription: desc,
      fullDescription: `${desc} Este conteúdo exclusivo da F5 TV apresenta reportagens detalhadas, investigações minuciosas, análises aprofundadas conduzidas com excelência e qualidade técnica, desenhando um retrato fiel dos fatos cruciais em alta resolução.`,
      categoryId: catId,
      genre,
      ageRating: idx % 3 === 0 ? '12' : idx % 2 === 0 ? 'L' : '14',
      year: 2026,
      duration: idx % 2 === 0 ? '1h 10m' : '55m',
      cast: ['Sandro Albuquerque', 'Renata Vasconcelos', 'Apresentadores F5 TV'],
      directors: ['Gabriel Peixoto'],
      coverUrl,
      bannerUrl,
      trailerUrl: 'https://assets.mixkit.co/videos/preview/mixkit-software-developer-working-on-his-computer-34289-large.mp4',
      videoUrl: 'https://assets.mixkit.co/videos/preview/mixkit-software-developer-working-on-his-computer-34289-large.mp4',
      status: 'published',
      isFeatured: title === 'Jornal F5: Edição Ao Vivo' || title === 'O Código de Ferro: Hackers vs Estado',
      isFree: idx % 3 === 0,
      isExclusive: idx % 3 !== 0,
      publishDate: `2026-05-${15 + (idx % 10)}`,
      tags,
      viewsCount: 12500 + idx * 850
    });
  });
});

// DB state loader
export const db = {
  getUsers: () => getLocalStorageItem<User[]>('f5_users', INITIAL_USERS),
  setUsers: (users: User[]) => setLocalStorageItem<User[]>('f5_users', users),

  getProfiles: () => getLocalStorageItem<Profile[]>('f5_profiles', INITIAL_PROFILES),
  setProfiles: (p: Profile[]) => setLocalStorageItem<Profile[]>('f5_profiles', p),

  getPlans: () => getLocalStorageItem<Plan[]>('f5_plans', INITIAL_PLANS),
  setPlans: (plans: Plan[]) => setLocalStorageItem<Plan[]>('f5_plans', plans),

  getCategories: () => getLocalStorageItem<Category[]>('f5_categories', INITIAL_CATEGORIES),
  setCategories: (c: Category[]) => setLocalStorageItem<Category[]>('f5_categories', c),

  getSeries: () => getLocalStorageItem<Series[]>('f5_series', INITIAL_SERIES),
  setSeries: (series: Series[]) => setLocalStorageItem<Series[]>('f5_series', series),

  getSeasons: () => getLocalStorageItem<Season[]>('f5_seasons', INITIAL_SEASONS),
  setSeasons: (seasons: Season[]) => setLocalStorageItem<Season[]>('f5_seasons', seasons),

  getEpisodes: () => getLocalStorageItem<Episode[]>('f5_episodes', INITIAL_EPISODES),
  setEpisodes: (episodes: Episode[]) => setLocalStorageItem<Episode[]>('f5_episodes', episodes),

  getContents: () => getLocalStorageItem<Content[]>('f5_contents', INITIAL_CONTENTS),
  setContents: (contents: Content[]) => setLocalStorageItem<Content[]>('f5_contents', contents),

  getSubscriptions: () => getLocalStorageItem<Subscription[]>('f5_subscriptions', INITIAL_SUBSCRIPTIONS),
  setSubscriptions: (subs: Subscription[]) => setLocalStorageItem<Subscription[]>('f5_subscriptions', subs),

  getPayments: () => getLocalStorageItem<Payment[]>('f5_payments', INITIAL_PAYMENTS),
  setPayments: (p: Payment[]) => setLocalStorageItem<Payment[]>('f5_payments', p),

  getUploads: () => getLocalStorageItem<Upload[]>('f5_uploads', INITIAL_UPLOADS),
  setUploads: (u: Upload[]) => setLocalStorageItem<Upload[]>('f5_uploads', u),

  getWatchHistory: () => getLocalStorageItem<WatchHistory[]>('f5_watch_history', INITIAL_WATCH_HISTORY),
  setWatchHistory: (wh: WatchHistory[]) => setLocalStorageItem<WatchHistory[]>('f5_watch_history', wh),

  getFavorites: () => getLocalStorageItem<Favorite[]>('f5_favorites', INITIAL_FAVORITES),
  setFavorites: (fav: Favorite[]) => setLocalStorageItem<Favorite[]>('f5_favorites', fav),

  getNotifications: () => getLocalStorageItem<Notification[]>('f5_notifications', INITIAL_NOTIFICATIONS),
  setNotifications: (n: Notification[]) => setLocalStorageItem<Notification[]>('f5_notifications', n),

  getReviews: () => getLocalStorageItem<Review[]>('f5_reviews', INITIAL_REVIEWS),
  setReviews: (r: Review[]) => setLocalStorageItem<Review[]>('f5_reviews', r),

  getCoupons: () => getLocalStorageItem<Coupon[]>('f5_coupons', INITIAL_COUPONS),
  setCoupons: (c: Coupon[]) => setLocalStorageItem<Coupon[]>('f5_coupons', c),

  getChannels: () => getLocalStorageItem<Channel[]>('f5_channels', INITIAL_CHANNELS),
  setChannels: (c: Channel[]) => setLocalStorageItem<Channel[]>('f5_channels', c),

  getLiveSchedules: () => getLocalStorageItem<LiveSchedule[]>('f5_live_schedules', INITIAL_LIVE_SCHEDULES),
  setLiveSchedules: (l: LiveSchedule[]) => setLocalStorageItem<LiveSchedule[]>('f5_live_schedules', l),

  getConnectedDevices: () => getLocalStorageItem<ConnectedDevice[]>('f5_connected_devices', INITIAL_DEVICES),
  setConnectedDevices: (d: ConnectedDevice[]) => setLocalStorageItem<ConnectedDevice[]>('f5_connected_devices', d)
};

// Seed utility to easily reload default state anytime
export function resetDBToDefault(): void {
  localStorage.removeItem('f5_users');
  localStorage.removeItem('f5_profiles');
  localStorage.removeItem('f5_plans');
  localStorage.removeItem('f5_categories');
  localStorage.removeItem('f5_series');
  localStorage.removeItem('f5_seasons');
  localStorage.removeItem('f5_episodes');
  localStorage.removeItem('f5_contents');
  localStorage.removeItem('f5_subscriptions');
  localStorage.removeItem('f5_payments');
  localStorage.removeItem('f5_uploads');
  localStorage.removeItem('f5_watch_history');
  localStorage.removeItem('f5_favorites');
  localStorage.removeItem('f5_notifications');
  localStorage.removeItem('f5_reviews');
  localStorage.removeItem('f5_coupons');
  localStorage.removeItem('f5_channels');
  localStorage.removeItem('f5_live_schedules');
  localStorage.removeItem('f5_connected_devices');
  window.location.reload();
}

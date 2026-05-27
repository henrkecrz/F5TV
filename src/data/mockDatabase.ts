/**
 * @license
 * SPDX-License-Identifier: Apache-2.0
 */

import { 
  User, Profile, Plan, Subscription, Payment, Content, Category, 
  Series, Season, Episode, Upload, WatchHistory, Favorite, Notification
} from '../types';

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
  }
];

// Seed seasons for the series
const INITIAL_SEASONS: Season[] = [
  { id: 'season-conexao-s1', seriesId: 'series-conexao-f5', number: 1, title: 'Temporada 1: Ciber-ameaças', status: 'published' },
  { id: 'season-conexao-s2', seriesId: 'series-conexao-f5', number: 2, title: 'Temporada 2: Crimes Ambientais', status: 'published' },
  { id: 'season-kids-s1', seriesId: 'series-mundo-kids', number: 1, title: 'Temporada 1: Letras e Cores', status: 'published' },
  { id: 'season-bastidores-s1', seriesId: 'series-bastidores', number: 1, title: 'Temporada 1: O Coração de Ferro', status: 'published' }
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
  }
];

// Seed initial general metadata movie contents (films, live events, standalones etc)
const INITIAL_CONTENTS: Content[] = [
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
    coverUrl: 'https://images.unsplash.com/photo-1541872703-74c5e44368f9?q=80&w=400',
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

// Local Storage Core helper
function getLocalStorageItem<T>(key: string, initialDefault: T): T {
  try {
    const item = localStorage.getItem(key);
    if (!item) {
      localStorage.setItem(key, JSON.stringify(initialDefault));
      return initialDefault;
    }
    return JSON.parse(item) as T;
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
  setNotifications: (n: Notification[]) => setLocalStorageItem<Notification[]>('f5_notifications', n)
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
  window.location.reload();
}

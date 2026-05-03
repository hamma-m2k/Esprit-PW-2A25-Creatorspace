/* ============================================================
   CREATORSPACE — FAKE DATA
============================================================ */

const CS = {
  currentUser: null,
  selectedAccountType: 'creator',
  currentPage: 1,
  itemsPerPage: 8,
  allUsers: [],
  filteredUsers: [],

  users: [
    { id:1,  name:'Sophie Martin',   initials:'SM', email:'sophie.martin@gmail.com',    role:'Creator Pro', status:'active',   date:'12 Jan 2022', color:'#6C3FC5', followers:'248K', views:'4.2M', content:312, completion:94 },
    { id:2,  name:'Lucas Bernard',   initials:'LB', email:'lucas.bernard@outlook.com',   role:'Créateur',    status:'active',   date:'03 Mar 2022', color:'#00C2CB', followers:'89K',  views:'1.1M', content:147, completion:78 },
    { id:3,  name:'Emma Dubois',     initials:'ED', email:'emma.dubois@yahoo.fr',         role:'Marque',      status:'active',   date:'18 Jun 2022', color:'#9B5DE5', followers:'312K', views:'6.8M', content:428, completion:100 },
    { id:4,  name:'Thomas Leroy',    initials:'TL', email:'thomas.leroy@gmail.com',       role:'Créateur',    status:'inactive', date:'07 Sep 2022', color:'#e53e3e', followers:'22K',  views:'310K', content:54,  completion:45 },
    { id:5,  name:'Chloé Moreau',    initials:'CM', email:'chloe.moreau@gmail.com',       role:'Creator Pro', status:'active',   date:'22 Nov 2022', color:'#38a169', followers:'55K',  views:'780K', content:93,  completion:65 },
    { id:6,  name:'Antoine Petit',   initials:'AP', email:'antoine.petit@hotmail.com',    role:'Créateur',    status:'pending',  date:'14 Jan 2023', color:'#ed8936', followers:'8K',   views:'95K',  content:21,  completion:38 },
    { id:7,  name:'Léa Rousseau',    initials:'LR', email:'lea.rousseau@gmail.com',       role:'Marque',      status:'active',   date:'05 Feb 2023', color:'#6C3FC5', followers:'127K', views:'2.3M', content:201, completion:88 },
    { id:8,  name:'Hugo Garnier',    initials:'HG', email:'hugo.garnier@gmail.com',       role:'Créateur',    status:'active',   date:'19 Mar 2023', color:'#00C2CB', followers:'41K',  views:'560K', content:88,  completion:72 },
    { id:9,  name:'Inès Faure',      initials:'IF', email:'ines.faure@outlook.com',       role:'Creator Pro', status:'active',   date:'30 Apr 2023', color:'#9B5DE5', followers:'44K',  views:'520K', content:67,  completion:55 },
    { id:10, name:'Maxime Girard',   initials:'MG', email:'maxime.girard@gmail.com',      role:'Créateur',    status:'inactive', date:'11 Jun 2023', color:'#e53e3e', followers:'5K',   views:'48K',  content:12,  completion:22 },
    { id:11, name:'Camille Dupont',  initials:'CD', email:'camille.dupont@gmail.com',     role:'Marque',      status:'active',   date:'02 Jul 2023', color:'#38a169', followers:'198K', views:'3.1M', content:267, completion:91 },
    { id:12, name:'Romain Blanc',    initials:'RB', email:'romain.blanc@outlook.com',     role:'Créateur',    status:'pending',  date:'15 Aug 2023', color:'#ed8936', followers:'3K',   views:'22K',  content:8,   completion:18 },
  ],

  activities: [
    { color:'#38a169', text:'<strong>Sophie Martin</strong> a publié un nouveau contenu',       time:'Il y a 2 min' },
    { color:'#6C3FC5', text:'<strong>Lucas Bernard</strong> a rejoint la plateforme',            time:'Il y a 8 min' },
    { color:'#00C2CB', text:'<strong>Emma Dubois</strong> a atteint 300K abonnés',               time:'Il y a 15 min' },
    { color:'#ed8936', text:'<strong>Antoine Petit</strong> est en attente de vérification',     time:'Il y a 32 min' },
    { color:'#e53e3e', text:'Alerte sécurité : tentative de connexion suspecte détectée',        time:'Il y a 1h' },
    { color:'#9B5DE5', text:'<strong>Chloé Moreau</strong> a signé un contrat avec L\'Oréal',   time:'Il y a 2h' },
  ],

  demoCredentials: {
    email: 'sophie.martin@gmail.com',
    password: 'password123'
  }
};

import './bootstrap';

import.meta.env.DEV && import.meta.env.VITE_INSTRUCKT_ENABLED !== 'false' && (async () => {
    const { Instruckt } = await import('instruckt');
    new Instruckt({ endpoint: '/instruckt', adapters: ['livewire'] });
})();

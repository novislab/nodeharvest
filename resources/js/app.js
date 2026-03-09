import './bootstrap';

window.instrucktEnabled && (async () => {
    const { Instruckt } = await import('instruckt');
    new Instruckt({ endpoint: '/instruckt', adapters: ['livewire'] });
})();

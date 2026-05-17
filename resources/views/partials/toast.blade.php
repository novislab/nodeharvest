@persist('toast')
<flux:toast.group x-init="if (sessionStorage.getItem('passkey_login_success')) { $dispatch('toast-show', { text: 'Great to see you again!', variant: 'success' }); sessionStorage.removeItem('passkey_login_success'); }">
    <flux:toast />
</flux:toast.group>
@endpersist
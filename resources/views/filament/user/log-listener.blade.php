<script>
  document.addEventListener('livewire:init', () => {
    Livewire.on('log-data', (payload) => {
      const message = payload?.message ?? payload;
      console.log('[Registration]', message);
    });
  });
</script>

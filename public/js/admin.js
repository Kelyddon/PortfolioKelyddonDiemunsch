document.addEventListener('DOMContentLoaded', () => {
  // Sauvegarde des skills
  const saveBtn = document.getElementById('save-skills');
  if (saveBtn) {
    saveBtn.addEventListener('click', async () => {
      const hardItems = document.querySelectorAll('li.skill-item[data-skill-type="hard"]');
      const softItems = document.querySelectorAll('li.skill-item[data-skill-type="soft"]');
      const hardUpdates = [], softUpdates = [], hardDeletes = [], softDeletes = [];

      hardItems.forEach(li => {
        const id = Number(li.dataset.skillId);
        const value = li.textContent.trim();
        if (!id) return;
        if (!value) hardDeletes.push(id); else hardUpdates.push({ id, value });
      });
      softItems.forEach(li => {
        const id = Number(li.dataset.skillId);
        const value = li.textContent.trim();
        if (!id) return;
        if (!value) softDeletes.push(id); else softUpdates.push({ id, value });
      });

      try {
        const res = await fetch(saveBtn.dataset.saveUrl, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
          body: JSON.stringify({ hardUpdates, softUpdates, hardDeletes, softDeletes }),
        });
        const data = await res.json();
        if (data.ok) location.reload(); else alert('Erreur: ' + (data.error || 'inconnue'));
      } catch (e) { alert('Échec de la sauvegarde.'); console.error(e); }
    });
  }

  // Upload du CV
  const cvBtn = document.getElementById('cv-upload-btn');
  const cvInput = document.getElementById('cv-file-input');
  const cvForm = document.getElementById('cv-upload-form');
  if (cvBtn && cvInput && cvForm) {
    cvBtn.addEventListener('click', () => cvInput.click());
    cvInput.addEventListener('change', () => {
      if (cvInput.files.length) cvForm.submit();
    });
  }
});
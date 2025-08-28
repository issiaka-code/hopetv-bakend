function attachDeleteConfirmation(selector) {
  document.querySelectorAll(selector).forEach(function(button) {
      button.addEventListener('click', function(e) {
          e.preventDefault();
          Swal.fire({
              title: 'Êtes-vous sûr ?',
              text: "Cette action est irréversible.",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Oui, supprimer !',
              cancelButtonText: 'Annuler'
          }).then((result) => {
              if (result.isConfirmed) {
                  // si le bouton est dans un formulaire
                  const form = e.target.closest('form');
                  if (form) {
                      form.submit();
                  } else {
                      // sinon, rediriger vers le lien href si présent
                      const link = e.target.getAttribute('href');
                      if (link) window.location.href = link;
                  }
              }
          });
      });
  });
}

function attachUpdateConfirmation(selector) {
  document.querySelectorAll(selector).forEach(function(button) {
      button.addEventListener('click', function(e) {
          e.preventDefault();
          Swal.fire({
              title: 'Êtes-vous sûr ?',
              text: "Voulez-vous vraiment modifier ces données ?",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Oui, Modifier !',
              cancelButtonText: 'Annuler'
          }).then((result) => {
              if (result.isConfirmed) {
                  // si le bouton est dans un formulaire
                  const form = e.target.closest('form');
                  if (form) {
                      form.submit();
                  } else {
                      // sinon, rediriger vers le lien href si présent
                      const link = e.target.getAttribute('href');
                      if (link) window.location.href = link;
                  }
              }
          });
      });
  });
}
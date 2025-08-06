document.addEventListener('DOMContentLoaded', function () {
  const path = window.location.pathname;
  const cleanPath = path.endsWith('/') ? path.slice(0, -1) : path;

  if (cleanPath === '' || cleanPath === '/') {
    document.getElementById('nav-home').classList.add('active');
  } else {
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
      if (
        link.getAttribute('href') === cleanPath ||
        cleanPath.startsWith(link.getAttribute('href') + '/')
      ) {
        link.classList.add('active');
      }
    });
  }
});

$(function () {
  "use strict";

  // Smooth-scroll for in-page anchor links (e.g. footer links to #apply,
  // #how-it-works, FAQ category jumps).
  $('a[href*="#"]').each(function () {
    var href = $(this).attr('href');
    var hashIndex = href.indexOf('#');
    if (hashIndex === -1) return;

    var samePage = href.substring(0, hashIndex) === '' ||
                    href.substring(0, hashIndex) === window.location.pathname.split('/').pop();
    var hash = href.substring(hashIndex);

    if (samePage && hash.length > 1 && $(hash).length) {
      $(this).on('click', function (e) {
        e.preventDefault();
        $('html, body').animate({ scrollTop: $(hash).offset().top - 90 }, 400);
      });
    }
  });

  // Highlight the current nav item if the include didn't already set it
  // (kept as a safety net; header.php sets `.active` server-side via $active).
  var path = window.location.pathname.split('/').pop() || 'index.php';
  $('.itr-navbar .nav-link').each(function () {
    var linkHref = $(this).attr('href');
    if (linkHref === path) $(this).addClass('active');
  });
});

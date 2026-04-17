/* ============================================
   SCREENSOLUTIONS.CH - Main JavaScript
   ============================================ */

document.addEventListener('DOMContentLoaded', function() {

  // === MOBILE NAVIGATION ===
  var hamburger = document.querySelector('.hamburger');
  var nav = document.querySelector('.nav');

  if (hamburger && nav) {
    hamburger.addEventListener('click', function() {
      hamburger.classList.toggle('active');
      nav.classList.toggle('active');
    });

    nav.querySelectorAll('.nav__link').forEach(function(link) {
      link.addEventListener('click', function() {
        hamburger.classList.remove('active');
        nav.classList.remove('active');
      });
    });
  }

  // === SCROLL ANIMATIONS (Fade-In) ===
  // Safe approach: elements are visible by default in CSS.
  // JS hides elements NOT in viewport, then IntersectionObserver reveals them.
  // If JS or observer fails, elements stay visible.
  var fadeElements = document.querySelectorAll('.fade-in');

  function isInViewport(el) {
    var rect = el.getBoundingClientRect();
    var vh = window.innerHeight || document.documentElement.clientHeight;
    return rect.top < vh + 50 && rect.bottom > -50;
  }

  // Step 1: Hide only elements that are NOT currently in viewport
  fadeElements.forEach(function(el) {
    if (!isInViewport(el)) {
      el.classList.add('fade-in--hidden');
    }
  });

  // Step 2: Observe hidden elements and reveal them on scroll
  if ('IntersectionObserver' in window) {
    var fadeObserver = new IntersectionObserver(function(entries) {
      entries.forEach(function(entry) {
        if (entry.isIntersecting) {
          entry.target.classList.remove('fade-in--hidden');
          entry.target.classList.add('visible');
          fadeObserver.unobserve(entry.target);
        }
      });
    }, { threshold: 0.05, rootMargin: '20px 0px 20px 0px' });

    fadeElements.forEach(function(el) {
      if (el.classList.contains('fade-in--hidden')) {
        fadeObserver.observe(el);
      }
    });
  } else {
    // No observer support: show everything
    fadeElements.forEach(function(el) {
      el.classList.remove('fade-in--hidden');
    });
  }

  // Safety net: scroll handler catches anything the observer missed
  var scrollTimer;
  window.addEventListener('scroll', function() {
    if (scrollTimer) return;
    scrollTimer = setTimeout(function() {
      scrollTimer = null;
      document.querySelectorAll('.fade-in.fade-in--hidden').forEach(function(el) {
        if (isInViewport(el)) {
          el.classList.remove('fade-in--hidden');
          el.classList.add('visible');
        }
      });
    }, 80);
  });

  // === FAQ ACCORDION ===
  var faqItems = document.querySelectorAll('.faq__item');

  faqItems.forEach(function(item) {
    var question = item.querySelector('.faq__question');
    if (question) {
      question.addEventListener('click', function() {
        var isActive = item.classList.contains('active');

        faqItems.forEach(function(other) {
          other.classList.remove('active');
        });

        if (!isActive) {
          item.classList.add('active');
        }
      });
    }
  });

  // === HEADER BACKGROUND ON SCROLL ===
  var header = document.querySelector('.header');

  if (header) {
    window.addEventListener('scroll', function() {
      if (window.scrollY > 50) {
        header.classList.add('scrolled');
      } else {
        header.classList.remove('scrolled');
      }
    });
  }

  // Contact form is now handled by js/contact-form.js

  // === FOOTER WAVE ANIMATION ===
  initFooterWaves();

});

// === FOOTER WAVE ANIMATION ===
function initFooterWaves() {
  var canvas = document.getElementById('footer-wave-canvas');
  if (!canvas) return;

  var ctx = canvas.getContext('2d');
  var w = 0, h = 0, time = 0, animId = null, running = false;

  function resize() {
    var rect = canvas.parentElement.getBoundingClientRect();
    if (rect.width > 0 && rect.height > 0) {
      w = canvas.width = Math.round(rect.width);
      h = canvas.height = Math.round(rect.height);
    }
  }

  function drawWave(yBase, amp, freq, speed, color) {
    ctx.beginPath();
    ctx.moveTo(0, h);
    for (var x = 0; x <= w; x += 3) {
      var y = yBase + Math.sin(x * freq + time * speed) * amp + Math.sin(x * freq * 0.5 + time * speed * 0.7) * amp * 0.5;
      ctx.lineTo(x, y);
    }
    ctx.lineTo(w, h);
    ctx.closePath();
    ctx.fillStyle = color;
    ctx.fill();
  }

  function draw() {
    if (w === 0 || h === 0) { resize(); }
    ctx.clearRect(0, 0, w, h);

    var bg = ctx.createLinearGradient(0, 0, 0, h);
    bg.addColorStop(0, '#0d0a1a');
    bg.addColorStop(1, '#1b0f2e');
    ctx.fillStyle = bg;
    ctx.fillRect(0, 0, w, h);

    var glow = ctx.createRadialGradient(w * 0.5, h * 0.4, 0, w * 0.5, h * 0.4, w * 0.5);
    glow.addColorStop(0, 'rgba(120,40,200,0.12)');
    glow.addColorStop(0.5, 'rgba(80,20,160,0.05)');
    glow.addColorStop(1, 'transparent');
    ctx.fillStyle = glow;
    ctx.fillRect(0, 0, w, h);

    drawWave(h * 0.72, 25, 0.003, 0.4, 'rgba(100,30,180,0.15)');
    drawWave(h * 0.68, 30, 0.004, 0.6, 'rgba(140,40,220,0.12)');
    drawWave(h * 0.75, 20, 0.005, 0.8, 'rgba(80,20,160,0.2)');
    drawWave(h * 0.78, 35, 0.003, 0.5, 'rgba(160,60,240,0.1)');
    drawWave(h * 0.82, 22, 0.006, 1.0, 'rgba(120,30,200,0.18)');
    drawWave(h * 0.85, 28, 0.004, 0.7, 'rgba(90,20,170,0.25)');
    drawWave(h * 0.9, 18, 0.007, 0.9, 'rgba(60,15,130,0.3)');

    time += 0.016;
    if (running) animId = requestAnimationFrame(draw);
  }

  function start() { if (!running) { running = true; resize(); draw(); } }
  function stop() { running = false; if (animId) { cancelAnimationFrame(animId); animId = null; } }

  window.addEventListener('resize', resize);

  if ('IntersectionObserver' in window) {
    new IntersectionObserver(function(entries) {
      entries.forEach(function(e) {
        if (e.isIntersecting) { start(); } else { stop(); }
      });
    }, { threshold: 0.05 }).observe(canvas.parentElement);
  } else {
    start();
  }

  setTimeout(resize, 100);
  setTimeout(resize, 500);
}

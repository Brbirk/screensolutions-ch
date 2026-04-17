/* ============================================
   SCREENSOLUTIONS - Shared Contact Form
   Include on any page with:
   <div id="contact-form"></div>
   <script src="js/contact-form.js"></script>
   ============================================ */

(function() {
  var container = document.getElementById("contact-form");
  if (!container) return;

  // Random captcha generieren
  var a = Math.floor(Math.random() * 10) + 1;
  var b = Math.floor(Math.random() * 10) + 1;
  var captchaAnswer = a + b;

  // Optionale Attribute: data-heading, data-text, data-section-class
  var heading = container.getAttribute("data-heading") || "Kontaktanfrage";
  var text = container.getAttribute("data-text") || "Du m\u00f6chtest wissen, wie KI-generierter Content f\u00fcr dein Unternehmen aussehen k\u00f6nnte? Schreib uns \u2013 wir beraten dich unverbindlich und zeigen dir konkrete M\u00f6glichkeiten f\u00fcr deine Marke.";
  var sectionClass = container.getAttribute("data-section-class") || "contact section";

  // Form HTML einsetzen
  container.outerHTML =
    '<section class="' + sectionClass + '" id="kontakt">' +
    '<div class="container">' +
    '<h2 class="fade-in">' + heading + '</h2>' +
    '<p class="fade-in">' + text + '</p>' +
    '<form class="contact__form fade-in" data-captcha-answer="' + captchaAnswer + '">' +
    '<div class="form-row">' +
    '<input type="text" name="name" class="form-input" placeholder="Name" required>' +
    '<input type="email" name="email" class="form-input" placeholder="E-Mail-Adresse" required>' +
    '</div>' +
    '<textarea name="message" class="form-textarea" placeholder="Nachricht" required></textarea>' +
    '<div class="captcha">' +
    '<span>' + a + ' + ' + b + ' =</span>' +
    '<input type="text" name="captcha" required>' +
    '</div>' +
    '<button type="submit" class="btn btn--primary">Senden</button>' +
    '</form>' +
    '</div>' +
    '</section>';

  // Form Handler
  var form = document.querySelector(".contact__form");
  if (!form) return;

  form.addEventListener("submit", function(e) {
    e.preventDefault();
    var name = form.querySelector("[name=name]").value.trim();
    var email = form.querySelector("[name=email]").value.trim();
    var message = form.querySelector("[name=message]").value.trim();
    var captcha = form.querySelector("[name=captcha]").value.trim();
    var answer = form.getAttribute("data-captcha-answer");

    if (!name || !email || !message) {
      showMsg("Bitte f\u00fclle alle Felder aus.", "error");
      return;
    }
    if (captcha !== answer) {
      showMsg("Die Rechenaufgabe ist falsch.", "error");
      return;
    }

    var btn = form.querySelector("button[type=submit]");
    var origText = btn.textContent;
    btn.textContent = "Wird gesendet...";
    btn.disabled = true;

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "send-mail.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
      if (xhr.readyState === 4) {
        btn.textContent = origText;
        btn.disabled = false;
        try {
          var res = JSON.parse(xhr.responseText);
          if (res.success) {
            window.location.href = "danke.html";
            var newA = Math.floor(Math.random() * 10) + 1;
            var newB = Math.floor(Math.random() * 10) + 1;
            form.setAttribute("data-captcha-answer", newA + newB);
            form.querySelector(".captcha span").textContent = newA + " + " + newB + " =";
          } else {
            showMsg(res.message || "Fehler beim Senden.", "error");
          }
        } catch(err) {
          showMsg("Serverfehler. Bitte versuche es sp\u00e4ter.", "error");
        }
      }
    };
    xhr.send("name=" + encodeURIComponent(name) + "&email=" + encodeURIComponent(email) + "&message=" + encodeURIComponent(message) + "&captcha=" + encodeURIComponent(captcha) + "&captcha_answer=" + encodeURIComponent(answer));
  });

  function showMsg(msg, type) {
    var el = document.querySelector(".form-message");
    if (!el) {
      el = document.createElement("div");
      el.className = "form-message";
      form.appendChild(el);
    }
    el.textContent = msg;
    el.className = "form-message form-message--" + type;
    el.style.display = "block";
    setTimeout(function() { el.style.display = "none"; }, 5000);
  }
})();

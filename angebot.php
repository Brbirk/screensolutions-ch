<?php
$pageTitle = 'Website im Abo ab CHF 89.- / Monat | screensolutions';
$pageDescription = 'Website im Abo zum Fixpreis: Handwerk, Business und Premium. Hosting, Wartung, Änderungen und frische Bilder inklusive.';
$pageCanonical = 'https://screensolutions.ch/angebot';
$activePage = 'angebot';
include '_header.php';
?>

<section class="hero hero--sub">
    <div class="hero__content">
        <div class="hero__grid">
            <div>
                <h1 class="hero__title">Deine Website im Abo</h1>
                <p class="hero__subtitle">Ein fixer Monatspreis. Wir bauen deine Website, halten sie aktuell und kümmern uns um alles Technische.</p>
                <a href="#kontakt" class="btn btn--primary">Kostenlose Beratung</a>
            </div>
            <div>
                <p class="hero__text">In jedem Paket enthalten: Hosting, Domain, SSL, Updates, Backups und ein fester Ansprechpartner. Keine Offerte nach Aufwand, keine Überraschungen auf der Rechnung. Mindestlaufzeit 12 Monate, danach monatlich kündbar.</p>
            </div>
        </div>

        <div class="pricing__grid pricing__grid--full">
            <div class="pricing__card">
                <span class="pricing__plan">Handwerk</span>
                <span class="pricing__amount">CHF 89.-</span>
                <span class="pricing__period">/ Monat &middot; einmalig CHF 490.- Einrichtung</span>
                <ul class="pricing__features">
                    <li><span class="pricing__check">✓</span> Für Schreiner, Maler, Gärtner und andere Kleinbetriebe</li>
                    <li><span class="pricing__check">✓</span> Website mit bis zu 5 Seiten aus unserer Branchenvorlage</li>
                    <li><span class="pricing__check">✓</span> Hosting, Domain, SSL, Updates und Backups</li>
                    <li><span class="pricing__check">✓</span> Texte aus deinem Fragebogen, Fotos direkt vom Handy</li>
                    <li><span class="pricing__check">✓</span> Google-Unternehmensprofil eingerichtet</li>
                    <li><span class="pricing__check">✓</span> 1 kleine Änderung pro Monat</li>
                </ul>
                <a href="#kontakt" class="pricing__btn" onclick="selectAngebot(0)">Handwerk wählen</a>
            </div>
            <div class="pricing__card pricing__card--featured">
                <span class="pricing__badge">Empfohlen</span>
                <span class="pricing__plan">Business</span>
                <span class="pricing__amount">CHF 249.-</span>
                <span class="pricing__period">/ Monat &middot; einmalig CHF 990.- Einrichtung</span>
                <ul class="pricing__features">
                    <li><span class="pricing__check">✓</span> Für KMU, die über die Website Kunden gewinnen wollen</li>
                    <li><span class="pricing__check">✓</span> Bis zu 12 Seiten, Design an deine Marke angepasst</li>
                    <li><span class="pricing__check">✓</span> Hosting, Domain, SSL, Updates und Backups</li>
                    <li><span class="pricing__check">✓</span> Lokale Suchmaschinenoptimierung mit Quartalsbericht</li>
                    <li><span class="pricing__check">✓</span> 4 KI-Bilder pro Quartal für Website und Werbung</li>
                    <li><span class="pricing__check">✓</span> Änderungen bis 1 Stunde pro Monat</li>
                </ul>
                <a href="#kontakt" class="pricing__btn" onclick="selectAngebot(1)">Business wählen</a>
            </div>
            <div class="pricing__card">
                <span class="pricing__plan">Premium</span>
                <span class="pricing__amount">CHF 590.-</span>
                <span class="pricing__period">/ Monat &middot; einmalig CHF 1900.- Einrichtung</span>
                <ul class="pricing__features">
                    <li><span class="pricing__check">✓</span> Für Firmen, die den ganzen Auftritt abgeben wollen</li>
                    <li><span class="pricing__check">✓</span> Individuelles Design, Seitenzahl nach Bedarf</li>
                    <li><span class="pricing__check">✓</span> Hosting, Domain, SSL, Updates und Backups</li>
                    <li><span class="pricing__check">✓</span> Laufende Suchmaschinenoptimierung</li>
                    <li><span class="pricing__check">✓</span> 1 Blogbeitrag mit Bild pro Monat</li>
                    <li><span class="pricing__check">✓</span> 8 KI-Bilder pro Monat, auch für Social Media</li>
                    <li><span class="pricing__check">✓</span> Änderungen bis 3 Stunden pro Monat, Antwort innert einem Werktag</li>
                </ul>
                <a href="#kontakt" class="pricing__btn" onclick="selectAngebot(2)">Premium wählen</a>
            </div>
        </div>
    </div>
</section>

<section class="section section--light">
    <div class="container">
        <h2 class="fade-in text-center" style="margin-bottom:2.5rem;">So läuft es ab</h2>
        <div class="grid grid--3">
            <div class="service-card service-card--teal fade-in">
                <h3>1. Kurzes Gespräch</h3>
                <p>Per Videocall klären wir in 20 Minuten, was du brauchst und welches Paket passt. Ein Termin vor Ort ist nicht nötig.</p>
            </div>
            <div class="service-card service-card--teal fade-in">
                <h3>2. Fragebogen und Bilder</h3>
                <p>Du beantwortest ein paar Fragen zu deinem Betrieb und schickst uns Fotos. Daraus schreiben und gestalten wir deine Website.</p>
            </div>
            <div class="service-card service-card--teal fade-in">
                <h3>3. Online und betreut</h3>
                <p>Nach deiner Freigabe geht die Website online. Ab dann kümmern wir uns um Technik, Sicherheit und deine Änderungswünsche.</p>
            </div>
        </div>
    </div>
</section>

<section class="pricing">
    <div class="container">
        <h2 class="fade-in" style="color:#fff;">Zusatz: Social-Media-Content</h2>
        <p class="fade-in" style="color:rgba(255,255,255,0.75);max-width:760px;margin:0 auto 40px;">Zu jedem Website-Paket buchbar: laufend neue Bilder und Texte mit unseren KI-Markenbotschaftern, fertig für deine Kanäle. Monatlich kündbar.</p>
    </div>
    <div class="pricing__grid">
        <div class="pricing__card">
            <span class="pricing__plan">Starter</span>
            <span class="pricing__amount">CHF 390.-</span>
            <span class="pricing__period">/ Monat</span>
            <ul class="pricing__features">
                <li><span class="pricing__check">✓</span> Ein KI Agentur-Model nach Wahl</li>
                <li><span class="pricing__check">✓</span> 6 Bilder und 6 Texte pro Monat</li>
                <li><span class="pricing__check">✓</span> 1 Website Blogpost mit Bild</li>
            </ul>
        </div>
        <div class="pricing__card">
            <span class="pricing__plan">Pro</span>
            <span class="pricing__amount">CHF 790.-</span>
            <span class="pricing__period">/ Monat</span>
            <ul class="pricing__features">
                <li><span class="pricing__check">✓</span> Ein KI Agentur-Model nach Wahl</li>
                <li><span class="pricing__check">✓</span> 12 Bilder und 12 Texte pro Monat</li>
                <li><span class="pricing__check">✓</span> 1 Website Blogpost mit Bild</li>
                <li><span class="pricing__check">✓</span> 2 bewegte Bilder (Animationen)</li>
            </ul>
        </div>
        <div class="pricing__card">
            <span class="pricing__plan">Diamond</span>
            <span class="pricing__amount">CHF 1200.-</span>
            <span class="pricing__period">/ Monat</span>
            <ul class="pricing__features">
                <li><span class="pricing__check">✓</span> Dein eigenes KI-Model für deine Firma</li>
                <li><span class="pricing__check">✓</span> 20 Bilder und 20 Texte pro Monat</li>
                <li><span class="pricing__check">✓</span> 2 Website Blogposts mit Bild</li>
                <li><span class="pricing__check">✓</span> 2 Bilder für Website / Werbung</li>
                <li><span class="pricing__check">✓</span> 4 bewegte Bilder (Animationen)</li>
            </ul>
        </div>
    </div>
    <div class="container" style="margin-top:40px;">
        <a href="#kontakt" class="btn btn--primary" onclick="selectAngebot(3)">Social-Media-Zusatz anfragen</a>
    </div>
</section>

<script>
function selectAngebot(index) {
    const radios = document.querySelectorAll('input[type="radio"][name="angebot"]');
    if (radios[index]) {
        radios[index].checked = true;
        radios[index].dispatchEvent(new Event('change'));
    }
}
</script>

<?php include '_footer.php'; ?>

<?php
$pageTitle = 'Angebot - Unsere Dienstleistungen | screensolutions';
$pageDescription = 'Unsere Angebote: Starter, Pro und Diamond. KI, Webdesign und Fotografie aus einer Hand.';
$pageCanonical = 'https://screensolutions.ch/angebot';
$activePage = 'angebot';
include '_header.php';
?>

<section class="hero hero--sub">
    <div class="hero__content">
        <div class="hero__grid">
            <div>
                <h1 class="hero__title">Unser Angebot</h1>
                <p class="hero__subtitle">Wähle das passende Paket für dein Unternehmen – von der soliden Basis bis zur umfassenden Premium-Lösung.</p>
                <a href="#kontakt" class="btn btn--primary">Kostenlose Beratung</a>
            </div>
            <div>
                <p class="hero__text">Alle Pakete beinhalten KI-generierte Markenbotschafter, professionellen Content für Social Media und persönliche Beratung. Keine versteckten Kosten, monatlich kündbar.</p>
            </div>
        </div>

        <div class="pricing__grid pricing__grid--full">
            <div class="pricing__card">
                <span class="pricing__plan">Starter</span>
                <span class="pricing__amount">CHF 390.-</span>
                <span class="pricing__period">/ Monat</span>
                <ul class="pricing__features">
                    <li><span class="pricing__check">✓</span> Nutzung eines unserer KI Agentur-Models nach Wahl</li>
                    <li><span class="pricing__check">✓</span> 6 Bilder / Monat für Social Media</li>
                    <li><span class="pricing__check">✓</span> 6 Texte / Monat für Social Media</li>
                    <li><span class="pricing__check">✓</span> 1 Website Blogpost mit Bild</li>
                </ul>
                <a href="#kontakt" class="pricing__btn" onclick="selectAngebot(0)">Starter wählen</a>
            </div>
            <div class="pricing__card pricing__card--featured">
                <span class="pricing__badge">Empfohlen</span>
                <span class="pricing__plan">Pro</span>
                <span class="pricing__amount">CHF 790.-</span>
                <span class="pricing__period">/ Monat</span>
                <ul class="pricing__features">
                    <li><span class="pricing__check">✓</span> Nutzung eines unserer KI Agentur-Models nach Wahl</li>
                    <li><span class="pricing__check">✓</span> 12 Bilder / Monat für Social Media</li>
                    <li><span class="pricing__check">✓</span> 12 Texte / Monat für Social Media</li>
                    <li><span class="pricing__check">✓</span> 1 Website Blogpost mit Bild</li>
                    <li><span class="pricing__check">✓</span> 2 bewegte Bilder (Animationen)</li>
                </ul>
                <a href="#kontakt" class="pricing__btn" onclick="selectAngebot(1)">Pro wählen</a>
            </div>
            <div class="pricing__card">
                <span class="pricing__plan">Diamond</span>
                <span class="pricing__amount">CHF 1200.-</span>
                <span class="pricing__period">/ Monat</span>
                <ul class="pricing__features">
                    <li><span class="pricing__check">✓</span> Dein eigenes KI-Model für deine Firma</li>
                    <li><span class="pricing__check">✓</span> 20 Bilder / Monat für Social Media</li>
                    <li><span class="pricing__check">✓</span> 20 Texte / Monat für Social Media</li>
                    <li><span class="pricing__check">✓</span> 2 Website Blogposts mit Bild</li>
                    <li><span class="pricing__check">✓</span> 2 Bilder für Website / Werbung</li>
                    <li><span class="pricing__check">✓</span> 4 bewegte Bilder (Animationen)</li>
                </ul>
                <a href="#kontakt" class="pricing__btn" onclick="selectAngebot(2)">Diamond wählen</a>
            </div>
        </div>
    </div>
</section>

<script>
function selectAngebot(index) {
    const radios = document.querySelectorAll('input[type="radio"][name="angebot"]');
    if (radios[index]) {
        radios[index].checked = true;
    }
}
</script>

<?php include '_footer.php'; ?>
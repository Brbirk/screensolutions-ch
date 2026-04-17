<?php
$pageTitle = 'screensolutions Blog';
$pageDescription = 'Blog-Artikel von screensolutions';
$pageCanonical = 'https://screensolutions.ch/blog';
$activePage = 'blog';
$extraScripts = '<script src="/js/blog.js?v=3"></script>';
include '_header.php';
?>

<section class="hero hero--firma" style="min-height: 20vh; padding: 30px 0 20px;">
    <div class="hero__content">
    </div>
</section>

<section class="section section--dark" style="padding-top: 60px;">
    <div id="blog-article"></div>
</section>

<script src="/js/blog.js?v=3"></script>
<script>loadArticle('blog-article');</script>


<!-- FOOTER -->

<?php include '_footer.php'; ?>
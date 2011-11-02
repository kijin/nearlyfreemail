
<?php if (!isset($BODY_CLASS)): /* Default Page Footer */ ?>

</div><!-- #content -->

<?php elseif ($BODY_CLASS == 'dialog'): ?>

<div class="disclaimer">

    <p><strong>Disclaimer</strong></p>
    <p>NearlyFreeMail is experimental software. Do not rely on it for important correspondence.</p>
    <p>NearlyFreeMail is neither endorsed nor supported by NearlyFreeSpeech.NET.</p>

</div>

<?php endif; ?>

<!-- NearlyFreeMail v<?php e(VERSION); ?> :: Generated at <?php e(gmdate('Y-m-d H:i:s')); ?> UTC -->

</body>
</html>
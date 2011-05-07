
<?php if (!isset($BODY_CLASS)): /* Default Page Footer */ ?>

</div><!-- #content -->

<?php elseif ($BODY_CLASS == 'dialog'): ?>

<div class="disclaimer">

    <p><strong>Disclaimer</strong></p>
    <p>NearlyFreeMail is experimental software. Do not rely on it for important correspondence.</p>
    <p>NearlyFreeMail is neither endorsed nor supported by NearlyFreeSpeech.NET.</p>

</div>

<?php endif; ?>

<!-- NearlyFreeMail v<?php e(VERSION); ?> :: (c) <?php e(date('Y')); ?> Kijin Sung -->
<!-- Generated at <?php e(date('Y-m-d H:i:s e')); ?> -->

</body>
</html>
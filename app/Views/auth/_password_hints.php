<div class="small text-muted mt-1">
    Syarat password:
    <ul class="mb-0 ps-3">
        <?php foreach (\App\Libraries\PasswordPolicy::hints() as $hint): ?>
            <li><?= esc($hint) ?></li>
        <?php endforeach; ?>
    </ul>
</div>

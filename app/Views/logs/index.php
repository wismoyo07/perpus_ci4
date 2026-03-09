<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<h4>Activity Logs</h4>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Waktu</th>
            <th>Actor</th>
            <th>Target</th>
            <th>Aktivitas</th>
            <th>IP</th>
        </tr>
    </thead>

    <tbody>

        <?php foreach($logs as $log): ?>
        <tr>
            <td><?= $log['created_at'] ?></td>
            <td><?= $log['user_id'] ?></td>
            <td><?= $log['target_user_id'] ?></td>
            <td><?= $log['aktivitas'] ?></td>
            <td><?= $log['ip_address'] ?></td>
        </tr>
        <?php endforeach; ?>

    </tbody>
</table>

<?= $this->endSection() ?>
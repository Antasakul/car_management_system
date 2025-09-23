<?php
require_once 'config.php';

$sql = "SELECT * FROM cars ORDER BY created_at DESC";
$result = $mysqli->query($sql);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Учёт автомобилей</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <div class="cyber-container">
        
        <div class="cyber-header cyber-fade-in">
            <div class="header-content">
                <div>
                    <h1 class="cyber-title">Учёт автомобилей</h1>
                </div>
                <a href="add.php" class="btn-cyber">Добавить автомобиль</a>
            </div>
        </div>

        <section class="cyber-fade-in" style="animation-delay: 0.2s;">
            <?php if ($result->num_rows === 0): ?>
                <div class="cyber-table-container empty-table-message">
                    <h3>Автомобилей не обнаружено</h3>
                    <p>Система мониторинга не обнаружила автомобили</p>
                    <a href="add.php" class="btn-cyber">Добавить первый автомобиль</a>
                </div>
            <?php else: ?>
                <div class="cyber-table-container">
                    <table class="cyber-table">
                        <thead>
                            <tr class="cyber-title">
                                <th>ID</th>
                                <th>Гос. номер</th>
                                <th>Название</th>
                                <th>Модель</th>
                                <th>Год</th>
                                <th>Описание</th>
                                <th>Статус</th>
                                <th>Дата добавления</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr class="cyber-fade-in">
                                    <td><?= (int)$row['id'] ?></td>
                                    <td class="license-plate-cell">
                                        <?= !empty($row['license_plate']) ? htmlspecialchars($row['license_plate']) : '—' ?>
                                    </td>
                                    <td class="model-cell"><?= htmlspecialchars($row['title']) ?></td>
                                    <td><?= htmlspecialchars($row['model'] ?: '—') ?></td>
                                    <td class="year-cell"><?= htmlspecialchars($row['year'] ?: '—') ?></td>
                                    <td class="description-cell" title="<?= !empty($row['description']) ? htmlspecialchars($row['description']) : 'Нет описания' ?>">
                                       <?= !empty($row['description']) ? htmlspecialchars($row['description']) : '—' ?></td>
                                    <td class="status-cell">
                                        <span class="status-chip <?= $row['status'] ? 'status-completed' : 'status-pending' ?>">
                                            <?= $row['status'] ? 'В наличии' : 'Не в наличии' ?>
                                        </span>
                                    </td>
                                    <td class="date-cell"><?= date('d.m.Y H:i', strtotime($row['created_at'])) ?></td>
                                    <td class="actions-cell">
                                        <div class="table-actions-vertical">
                                             <button class="btn-table btn-status <?= $row['status'] ? 'status-not-available' : 'status-available' ?>" 
                                               onclick="location.href='update_status.php?id=<?= (int)$row['id'] ?>&status=<?= $row['status'] ? 0 : 1 ?>'"> <?= $row['status'] ? 'Убрать из наличия' : 'Добавить в наличие' ?>
                                            </button>
                                            <button class="btn-table btn-edit" 
                                                    onclick="location.href='edit.php?id=<?= (int)$row['id'] ?>'">
                                                Редактировать
                                            </button>
                                           
                                            <button class="btn-table btn-delete" 
                                                    onclick="if(confirm('Удалить автомобиль &quot;<?= addslashes($row['title']) ?>&quot;?')) location.href='delete.php?id=<?= (int)$row['id'] ?>'">
                                                Удалить
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <div class="stats-container">
                    <h4>Всего автомобилей: <strong><?= $result->num_rows ?></strong></h4>
                </div>
            <?php endif; ?>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('.cyber-table tbody tr');
            
            rows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateX(5px)';
                });
                
                row.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateX(0)';
                });
                
                
            });
        });
    </script>
</body>
</html>
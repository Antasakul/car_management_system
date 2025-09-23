<?php
require_once 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    die("Неверный id.");
}

$errors = [];

$stmt = $mysqli->prepare("SELECT * FROM cars WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$car = $res->fetch_assoc();
if (!$car) {
    die("Запись не найдена.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $model = trim($_POST['model'] ?? '');
    $license_plate = trim($_POST['license_plate'] ?? '');
    $year = trim($_POST['year'] ?? '');

    if ($title === '') $errors[] = "Поле 'Название' обязательно.";
    if (strlen($title) > 255) $errors[] = "Название не должно превышать 255 символов.";
    if ($year !== '' && !ctype_digit($year)) $errors[] = "Год должен быть числом.";

    if (empty($errors)) {
        $stmt = $mysqli->prepare("UPDATE cars SET title=?, description=?, model=?, license_plate=?, year=? WHERE id=?");
        $yearParam = $year === '' ? null : (int)$year;
        $stmt->bind_param("ssssii", $title, $description, $model, $license_plate, $yearParam, $id);
        if ($stmt->execute()) {
            header("Location: index.php");
            exit;
        } else {
            $errors[] = "Ошибка обновления: " . $stmt->error;
        }
    }
} else {

    $title = $car['title'];
    $description = $car['description'];
    $model = $car['model'];
    $license_plate = $car['license_plate'];
    $year = $car['year'];
}
?>

<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= isset($car) ? 'Редактировать' : 'Добавить' ?> автомобиль</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="style.css" rel="stylesheet">
</head>
<body>
<div class="container form-container fade-in">
  <div class="header">
    <h1><?= isset($car) ? 'Редактировать автомобиль' : 'Добавить новый автомобиль' ?></h1>
    <p class="text-muted"><?= isset($car) ? 'Измените информацию об автомобиле' : 'Заполните информацию о новом автомобиле' ?></p>
  </div>

  <?php if ($errors): ?>
    <div class="alert alert-danger">
      <h5>Ошибки при заполнении формы:</h5>
      <ul class="mb-0"><?php foreach($errors as $e): ?><li><?=htmlspecialchars($e)?></li><?php endforeach; ?></ul>
    </div>
  <?php endif; ?>

  <form method="post" novalidate class="row g-3">
    <div class="col-md-6">
      <label class="form-label required-field">Название автомобиля</label>
      <input name="title" class="form-control" value="<?= htmlspecialchars($_POST['title'] ?? ($car['title'] ?? '')) ?>" 
             required maxlength="255" placeholder="Например: Служебный автомобиль CEO">
      <div class="form-text">Краткое описательное название</div>
    </div>

    <div class="col-md-6">
      <label class="form-label">Модель</label>
      <input name="model" class="form-control" value="<?= htmlspecialchars($_POST['model'] ?? ($car['model'] ?? '')) ?>" 
             placeholder="Например: Toyota Camry">
    </div>

    <div class="col-md-4">
      <label class="form-label">Государственный номер</label>
      <input name="license_plate" class="form-control" value="<?= htmlspecialchars($_POST['license_plate'] ?? ($car['license_plate'] ?? '')) ?>" 
             placeholder="Например: А123БВ77">
    </div>

    <div class="col-md-4">
      <label class="form-label">Год выпуска</label>
      <input name="year" type="number" class="form-control" value="<?= htmlspecialchars($_POST['year'] ?? ($car['year'] ?? '')) ?>" 
             min="1900" max="<?= date('Y') + 1 ?>" placeholder="Например: 2022">
    </div>

    <div class="col-12">
      <label class="form-label">Описание</label>
      <textarea name="description" class="form-control" rows="4" 
                placeholder="Дополнительная информация об автомобиле (пробег, особенности, ТО и т.д.)"><?= htmlspecialchars($_POST['description'] ?? ($car['description'] ?? '')) ?></textarea>
    </div>

    <div class="col-12">
      <button class="btn btn-success" type="submit"><?= isset($car) ? 'Сохранить изменения' : 'Добавить автомобиль' ?></button>
      <a href="index.php" class="btn btn-secondary">Назад к списку</a>
    </div>
  </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
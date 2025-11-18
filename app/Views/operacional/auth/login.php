<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <title>Login</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container d-flex align-items-center justify-content-center" style="min-height:100vh">
    <div class="card shadow-sm" style="max-width:380px;width:100%;">
      <div class="card-body p-4">
        <h1 class="h4 mb-3 text-center">Entrar</h1>

        <?php if (session('erro')): ?>
          <div class="alert alert-danger small"><?= esc(session('erro')) ?></div>
        <?php endif; ?>

        <form method="post" action="<?= base_url('login') ?>">
          <?= csrf_field() ?>
          <div class="mb-3">
            <label class="form-label">E-mail</label>
            <input type="email" class="form-control" name="email" placeholder="voce@empresa.com" value="<?= old('email') ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Senha</label>
            <input type="password" class="form-control" name="senha" placeholder="Sua senha" required>
          </div>
          <button class="btn btn-primary w-100" type="submit">Acessar</button>
        </form>
      </div>
    </div>
  </div>
</body>
</html>

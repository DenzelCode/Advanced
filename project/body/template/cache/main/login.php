<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{@title} - {@name}</title>
</head>
<body>
    <form action="/auth/login" method="POST">
        <input type="text" name="username" placeholder="<?=  $language->get("page.login.username") ?>">
        <input type="password" name="password" placeholder="<?=  $language->get("page.login.password") ?>">
        <input type="checkbox" name="remember">
        <label for="remember"><?=  $language->get("page.login.remember_me") ?></label>
        <button type="submit" name="go"><?=  $language->get("page.login.login") ?></button>
    </form>
    <a href="/"><?=  $language->get("page.general.back") ?></a>
</body>
</html>